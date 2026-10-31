<?php

namespace App\Http\Controllers;

use App\Models\DuelRoom;
use App\Models\DuelRoundAnswer;
use App\Models\Flashcard;
use App\Models\FlashcardProgress;
use App\Services\Duel\DuelAuthService;
use App\Services\Duel\DuelGameEngine;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;
use InvalidArgumentException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class DuelController extends Controller
{
    public function __construct(
        protected DuelGameEngine $gameEngine,
        protected DuelAuthService $authService
    ) {}

    /**
     * Sảnh chờ Đấu trường 1v1 (Lobby & Matchmaking Hub).
     */
    public function lobby(Request $request): View
    {
        $user = $request->user();

        // Đếm số người đang chờ ghép trận theo từng cấp HSK
        $waitingCountsRaw = DuelRoom::where('status', 'waiting')
            ->where('mode', 'random')
            ->groupBy('hsk_level')
            ->selectRaw('hsk_level, count(*) as total')
            ->pluck('total', 'hsk_level')
            ->toArray();

        $waitingCounts = [];
        for ($lvl = 1; $lvl <= 6; $lvl++) {
            $waitingCounts[$lvl] = $waitingCountsRaw[$lvl] ?? 0;
        }

        // Danh sách phòng chờ công khai (Room Browser - tối đa 15 phòng gần nhất)
        $publicWaitingRooms = DuelRoom::where('status', 'waiting')
            ->where('mode', 'random')
            ->whereNull('player2_id')
            ->latest('created_at')
            ->take(15)
            ->get();

        // Kiểm tra xem User hiện tại có phòng nào đang hoạt động không
        $myActiveRoom = null;
        if ($user) {
            $myActiveRoom = DuelRoom::whereIn('status', ['waiting', 'countdown', 'in_progress'])
                ->where(function ($q) use ($user) {
                    $q->where('player1_id', $user->id)
                        ->orWhere('player2_id', $user->id);
                })
                ->latest('updated_at')
                ->first();
        }

        return view('duel.lobby', [
            'user' => $user,
            'waitingCounts' => $waitingCounts,
            'publicWaitingRooms' => $publicWaitingRooms,
            'myActiveRoom' => $myActiveRoom,
        ]);
    }

    /**
     * Sàn đấu Battle Arena UI (Blade + Alpine.js).
     */
    public function arena(Request $request, string $code): Response|View|RedirectResponse
    {
        if (!Auth::guard('web')->check()) {
            return redirect()->guest(route('login'))->with('error', 'Vui lòng đăng nhập để tham gia đấu trường 1v1.');
        }

        $code = strtoupper(trim($code));
        $room = DuelRoom::where('room_code', $code)->firstOrFail();

        $playerData = $this->authService->resolvePlayerIdentity($request, $code);

        $isPlayer1 = ($room->player1_id === $playerData['user_id']);
        $isPlayer2 = ($room->player2_id === $playerData['user_id']);

        // Nếu user mới bấm vào link mời phòng còn đang 'waiting' và slot 2 chưa có ai
        if (!$isPlayer1 && !$isPlayer2) {
            if ($room->status === 'waiting' && is_null($room->player2_id)) {
                if ($room->player1_id === $playerData['user_id']) {
                    abort(400, 'Bạn không thể tự thi đấu với chính mình.');
                }
                $room = $this->gameEngine->joinRoom($room, $playerData);
                $isPlayer2 = true;
            } else {
                abort(403, 'Bạn không có quyền tham gia phòng đấu này.');
            }
        }

        $slot = $isPlayer1 ? 1 : 2;
        $initialState = $this->gameEngine->syncAndGetRoomState($room, $slot);

        return response()->view('duel.arena', [
            'room' => $room,
            'roomCode' => $code,
            'slot' => $slot,
            'initialState' => $initialState,
        ]);
    }

    /**
     * Lịch sử thi đấu cá nhân (Duel History).
     */
    public function history(Request $request): View
    {
        $userId = Auth::guard('web')->id();

        $baseQuery = DuelRoom::whereIn('status', ['finished', 'abandoned'])
            ->where(function ($q) use ($userId) {
                $q->where('player1_id', $userId)
                    ->orWhere('player2_id', $userId);
            });

        // Tính toán thống kê tổng quát
        $allMatches = (clone $baseQuery)->get();
        $total = $allMatches->count();
        $wins = 0;
        $losses = 0;
        $draws = 0;

        foreach ($allMatches as $m) {
            $slot = ($m->player1_id === $userId) ? 1 : (($m->player2_id === $userId) ? 2 : null);
            if (!$slot) {
                continue;
            }

            if ($m->is_draw) {
                $draws++;
            } elseif ($m->status === 'abandoned') {
                if ($m->forfeit_by_slot) {
                    if ($m->forfeit_by_slot === $slot) {
                        $losses++;
                    } else {
                        $wins++;
                    }
                } else {
                    $draws++;
                }
            } elseif ($m->winner_slot === $slot) {
                $wins++;
            } else {
                $losses++;
            }
        }

        $winRate = $total > 0 ? round(($wins / $total) * 100, 1) : 0;
        $matches = (clone $baseQuery)->latest('finished_at')->paginate(10);

        return view('duel.history', [
            'user' => $request->user(),
            'stats' => [
                'total' => $total,
                'wins' => $wins,
                'losses' => $losses,
                'draws' => $draws,
                'win_rate' => $winRate,
            ],
            'matches' => $matches,
            'isGuest' => false,
        ]);
    }

    /**
     * Màn hình kết quả trận đấu chi tiết (Match Result).
     */
    public function result(Request $request, string $code): View|RedirectResponse
    {
        $code = strtoupper(trim($code));
        $room = DuelRoom::where('room_code', $code)->firstOrFail();

        // Xác thực quyền truy cập slot
        $slot = $this->authService->resolveAuthenticatedSlot($room, $request);

        if ($room->status !== 'finished' && $room->status !== 'abandoned') {
            return redirect()->route('duel.arena', $code);
        }

        $answers = DuelRoundAnswer::where('duel_room_id', $room->id)->get();
        $myAnswers = $answers->where('player_slot', $slot)->keyBy('round_number');
        $opponentSlot = ($slot === 1) ? 2 : 1;
        $opponentAnswers = $answers->where('player_slot', $opponentSlot)->keyBy('round_number');

        $questions = $room->questions_snapshot ?? [];

        $myCorrect = 0;
        $myIncorrect = 0;
        $myTimeout = 0;
        $totalTime = 0.0;
        $answeredCount = 0;

        $roundDetails = [];
        foreach ($questions as $idx => $q) {
            $rNum = $idx + 1;
            $myAns = $myAnswers->get($rNum);
            $oppAns = $opponentAnswers->get($rNum);

            $isTimeout = ($myAns === null || $myAns->selected_answer === null);
            $isCorrect = ($myAns !== null && $myAns->is_correct);

            if ($isCorrect) {
                $myCorrect++;
            } elseif ($isTimeout) {
                $myTimeout++;
            } else {
                $myIncorrect++;
            }

            if ($myAns && !$isTimeout) {
                $totalTime += (float) $myAns->answer_time_seconds;
                $answeredCount++;
            }

            $roundDetails[] = [
                'round_number' => $rNum,
                'question' => $q['question'] ?? '',
                'pinyin' => $q['pinyin'] ?? null,
                'options' => $q['options'] ?? [],
                'correct_answer' => $q['correct_answer'] ?? '',
                'explanation' => $q['explanation'] ?? '',
                'my_selected' => $myAns?->selected_answer,
                'my_is_correct' => $isCorrect,
                'my_is_timeout' => $isTimeout,
                'my_time' => $myAns ? (float) $myAns->answer_time_seconds : null,
                'my_score' => $myAns ? (int) $myAns->score_awarded : 0,
                'opp_selected' => $oppAns?->selected_answer,
                'opp_is_correct' => $oppAns?->is_correct ?? false,
                'opp_is_timeout' => ($oppAns === null || $oppAns->selected_answer === null),
                'opp_score' => $oppAns ? (int) $oppAns->score_awarded : 0,
            ];
        }

        $avgTime = $answeredCount > 0 ? round($totalTime / $answeredCount, 2) : 0.0;

        $outcome = 'draw';
        if ($room->status === 'abandoned') {
            if ($room->forfeit_by_slot) {
                $outcome = ($room->forfeit_by_slot === $slot) ? 'loss' : 'win';
            } else {
                $outcome = 'abandoned';
            }
        } elseif ($room->is_draw) {
            $outcome = 'draw';
        } elseif ($room->winner_slot === $slot) {
            $outcome = 'win';
        } else {
            $outcome = 'loss';
        }

        return view('duel.result', [
            'room' => $room,
            'roomCode' => $code,
            'slot' => $slot,
            'outcome' => $outcome,
            'stats' => [
                'my_score' => ($slot === 1) ? $room->player1_score : $room->player2_score,
                'opp_score' => ($slot === 1) ? $room->player2_score : $room->player1_score,
                'my_correct' => $myCorrect,
                'my_incorrect' => $myIncorrect,
                'my_timeout' => $myTimeout,
                'avg_time' => $avgTime,
                'my_name' => ($slot === 1) ? $room->player1_name : $room->player2_name,
                'opp_name' => ($slot === 1) ? $room->player2_name : $room->player1_name,
                'my_avatar' => ($slot === 1) ? $room->player1_avatar : $room->player2_avatar,
                'opp_avatar' => ($slot === 1) ? $room->player2_avatar : $room->player1_avatar,
            ],
            'roundDetails' => $roundDetails,
            'hasMistakes' => ($myIncorrect + $myTimeout) > 0,
        ]);
    }

    /**
     * Màn hình ôn tập các câu trả lời sai hoặc timeout trong trận (Review Mistakes).
     */
    public function review(Request $request, string $code): View|RedirectResponse
    {
        $code = strtoupper(trim($code));
        $room = DuelRoom::where('room_code', $code)->firstOrFail();

        $slot = $this->authService->resolveAuthenticatedSlot($room, $request);

        if ($room->status !== 'finished' && $room->status !== 'abandoned') {
            return redirect()->route('duel.arena', $code);
        }

        $myAnswers = DuelRoundAnswer::where('duel_room_id', $room->id)
            ->where('player_slot', $slot)
            ->get()
            ->keyBy('round_number');

        $questions = $room->questions_snapshot ?? [];
        $mistakes = [];

        foreach ($questions as $idx => $q) {
            $rNum = $idx + 1;
            $myAns = $myAnswers->get($rNum);

            // Chỉ đưa vào danh sách nếu trả lời sai hoặc timeout
            if (!$myAns || !$myAns->is_correct) {
                $mistakes[] = [
                    'round_number' => $rNum,
                    'question_id' => $q['id'] ?? null,
                    'question' => $q['question'] ?? '',
                    'pinyin' => $q['pinyin'] ?? null,
                    'audio_text' => $q['audio_text'] ?? null,
                    'image' => $q['image'] ?? null,
                    'options' => $q['options'] ?? [],
                    'correct_answer' => $q['correct_answer'] ?? '',
                    'explanation' => $q['explanation'] ?? '',
                    'my_selected' => $myAns?->selected_answer,
                    'is_timeout' => ($myAns === null || $myAns->selected_answer === null),
                ];
            }
        }

        return view('duel.review', [
            'room' => $room,
            'roomCode' => $code,
            'slot' => $slot,
            'mistakes' => $mistakes,
        ]);
    }

    /**
     * Lưu câu hỏi sai vào bộ Flashcard SRS của người học (Idempotent).
     */
    public function saveMistakeToFlashcard(Request $request, string $code): JsonResponse
    {
        $code = strtoupper(trim($code));
        $room = DuelRoom::where('room_code', $code)->firstOrFail();

        $slot = $this->authService->resolveAuthenticatedSlot($room, $request);

        $user = Auth::guard('web')->user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đăng nhập để lưu câu hỏi này vào bộ Flashcard SRS của bạn.',
            ], 401);
        }

        $validated = $request->validate([
            'round_number' => 'required|integer|min:1|max:10',
        ]);

        $questions = $room->questions_snapshot ?? [];
        $q = $questions[$validated['round_number'] - 1] ?? null;

        if (!$q) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy câu hỏi trong trận đấu.',
            ], 404);
        }

        $hanzi = trim($q['question'] ?? '');
        $pinyin = trim($q['pinyin'] ?? '');
        $meaning = trim($q['correct_answer'] ?? '');

        // Tìm hoặc tạo Flashcard vĩnh viễn
        $flashcard = Flashcard::firstOrCreate(
            ['hanzi' => $hanzi],
            [
                'pinyin' => $pinyin,
                'meaning' => $meaning,
                'hsk_level' => $room->hsk_level,
                'is_active' => true,
                'example' => $q['explanation'] ?? null,
            ]
        );

        // Đưa vào hàng đợi SRS của học viên (repetition = 0, next_review_at = now())
        try {
            $progress = FlashcardProgress::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'flashcard_id' => $flashcard->id,
                ],
                [
                    'repetition' => 0,
                    'ease_factor' => 2.5,
                    'interval' => 0,
                    'next_review_at' => now(),
                    'is_starred' => true,
                ]
            );
        } catch (\Illuminate\Database\QueryException $e) {
            // Xử lý race condition khi 2 request chạy đồng thời vi phạm composite unique ['user_id', 'flashcard_id']
            $progress = FlashcardProgress::where('user_id', $user->id)
                ->where('flashcard_id', $flashcard->id)
                ->firstOrFail();
        }

        if (!$progress->wasRecentlyCreated) {
            $progress->is_starred = true;
            $progress->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Đã thêm từ vựng vào bộ Flashcard thành công!',
            'flashcard_id' => $flashcard->id,
        ]);
    }

    /**
     * Ghép trận tự động công khai (Public Matchmaking).
     */
    public function matchmake(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'hsk_level' => 'required|integer|min:1|max:6',
        ]);

        $hskLevel = (int) $validated['hsk_level'];
        $throttleKey = 'duel-matchmake:' . ($request->user()?->id ?? $request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 20)) {
            return response()->json([
                'message' => 'Bạn thao tác quá nhanh. Vui lòng thử lại sau ít giây.',
            ], 429);
        }
        RateLimiter::hit($throttleKey, 60);

        $playerData = $this->authService->resolvePlayerIdentity($request);
        $userId = $playerData['user_id'];

        $result = DB::transaction(function () use ($hskLevel, $playerData, $userId) {
            // 1. Khóa User để chống race condition khi mở nhiều tab/thiết bị
            \App\Models\User::where('id', $userId)->lockForUpdate()->first();

            // 2. Kiểm tra xem người này đã có trận đấu hoạt động (waiting/countdown/in_progress) trên TOÀN BỘ cấp HSK không
            $existingRoom = DuelRoom::whereIn('status', ['waiting', 'countdown', 'in_progress'])
                ->where(function ($q) use ($userId) {
                    $q->where('player1_id', $userId)
                        ->orWhere('player2_id', $userId);
                })
                ->first();

            if ($existingRoom) {
                $role = ($existingRoom->player1_id === $userId) ? 'player1' : 'player2';

                // Nếu đang ở phòng chờ đúng cấp HSK và random -> tiếp tục chờ
                if ($existingRoom->status === 'waiting' && $existingRoom->hsk_level === $hskLevel && $existingRoom->mode === 'random') {
                    return [
                        'status' => 'waiting',
                        'room_code' => $existingRoom->room_code,
                        'role' => $role,
                        'is_existing' => true,
                    ];
                }

                // Nếu đã vào trận hoặc ở phòng chờ khác -> báo trận đang hoạt động
                return [
                    'status' => 'active_match_exists',
                    'room_code' => $existingRoom->room_code,
                    'role' => $role,
                    'is_existing' => true,
                ];
            }

            // 3. Tìm một phòng chờ công khai của người khác và khóa dòng
            $availableRoom = DuelRoom::where('hsk_level', $hskLevel)
                ->where('mode', 'random')
                ->where('status', 'waiting')
                ->whereNull('player2_id')
                ->where('player1_id', '!=', $userId)
                ->lockForUpdate()
                ->first();

            if ($availableRoom) {
                $joined = $this->gameEngine->joinRoom($availableRoom, $playerData);
                return [
                    'status' => 'matched',
                    'room_code' => $joined->room_code,
                    'role' => 'player2',
                    'is_existing' => false,
                ];
            }

            // 4. Nếu không có ai đang chờ -> Tạo phòng chờ mới
            $newRoom = $this->gameEngine->createRoom($hskLevel, 'random', $playerData);
            return [
                'status' => 'waiting',
                'room_code' => $newRoom->room_code,
                'role' => 'player1',
                'is_existing' => false,
            ];
        });

        $statusCode = ($result['status'] === 'active_match_exists') ? 409 : 200;

        return response()->json([
            'status' => $result['status'],
            'room_code' => $result['room_code'],
            'role' => $result['role'],
        ], $statusCode);
    }

    /**
     * Hủy phòng chờ tìm trận công khai (Cancel Matchmaking).
     */
    public function cancelMatchmake(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'room_code' => 'required|string|max:20',
        ]);

        $code = strtoupper(trim($validated['room_code']));
        $room = DuelRoom::where('room_code', $code)->first();

        if (!$room) {
            return response()->json(['message' => 'Phòng thi đấu không tồn tại.'], 404);
        }

        $userId = Auth::guard('web')->id();
        if (!$userId) {
            return response()->json(['message' => 'Vui lòng đăng nhập.'], 401);
        }

        try {
            $this->gameEngine->cancelWaitingRoom($room, $userId);
            return response()->json([
                'success' => true,
                'message' => 'Đã hủy tìm kiếm trận đấu.',
            ]);
        } catch (InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * Đầu hàng chủ động trong trận đấu (Surrender Match).
     */
    public function surrender(Request $request, string $code): JsonResponse
    {
        $code = strtoupper(trim($code));
        $room = DuelRoom::where('room_code', $code)->first();

        if (!$room) {
            return response()->json(['message' => 'Phòng thi đấu không tồn tại.'], 404);
        }

        $slot = $this->authService->resolveAuthenticatedSlot($room, $request);

        try {
            $room = $this->gameEngine->surrenderMatch($room, $slot);
            return response()->json([
                'success' => true,
                'message' => 'Bạn đã nhận thua trận đấu.',
                'room_code' => $room->room_code,
                'status' => $room->status,
                'forfeit_by_slot' => $room->forfeit_by_slot,
            ]);
        } catch (InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * Tạo phòng thách đấu bạn bè (Private Lobby).
     */
    public function createRoom(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'hsk_level' => 'required|integer|min:1|max:6',
            'mode' => 'nullable|string|in:friend,custom,random',
        ]);

        $hskLevel = (int) $validated['hsk_level'];
        $mode = $validated['mode'] ?? 'friend';
        $playerData = $this->authService->resolvePlayerIdentity($request);
        $userId = $playerData['user_id'];

        // Kiểm tra xem User có đang tham gia trận đấu nào khác đang diễn ra không
        $activeRoom = DuelRoom::whereIn('status', ['waiting', 'countdown', 'in_progress'])
            ->where(function ($q) use ($userId) {
                $q->where('player1_id', $userId)
                    ->orWhere('player2_id', $userId);
            })
            ->first();

        if ($activeRoom) {
            return response()->json([
                'message' => 'Bạn đang có một phòng hoặc trận đấu đang diễn ra.',
                'room_code' => $activeRoom->room_code,
            ], 409);
        }

        $room = $this->gameEngine->createRoom($hskLevel, $mode, $playerData);

        return response()->json([
            'status' => 'created',
            'room_code' => $room->room_code,
            'hsk_level' => $room->hsk_level,
            'mode' => $room->mode,
            'invite_url' => url("/duel/room/{$room->room_code}"),
        ], 201);
    }

    /**
     * Tham gia phòng thách đấu bằng mã mời (Join Private Lobby).
     */
    public function joinRoom(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'invite_code' => 'nullable|string|max:20',
            'room_code' => 'nullable|string|max:20',
        ]);

        $code = strtoupper(trim($validated['invite_code'] ?? $validated['room_code'] ?? ''));
        if (empty($code)) {
            return response()->json([
                'message' => 'Trường invite code hoặc room code không được bỏ trống.',
                'errors' => ['invite_code' => ['Trường invite code không được bỏ trống.']]
            ], 422);
        }

        $inviteCode = $code;
        $failKey = 'duel-join-fail:' . ($request->user()?->id ?? $request->ip());

        if (RateLimiter::tooManyAttempts($failKey, 10)) {
            return response()->json([
                'message' => 'Bạn đã nhập sai mã phòng quá nhiều lần. Vui lòng thử lại sau 1 phút.',
            ], 429);
        }

        $playerData = $this->authService->resolvePlayerIdentity($request);
        $userId = $playerData['user_id'];

        try {
            $result = DB::transaction(function () use ($inviteCode, $playerData, $userId, $failKey) {
                // Khóa User trước để chống race condition
                \App\Models\User::where('id', $userId)->lockForUpdate()->first();

                // Kiểm tra xem User có đang tham gia trận đấu khác đang diễn ra không
                $otherActive = DuelRoom::whereIn('status', ['waiting', 'countdown', 'in_progress'])
                    ->where('room_code', '!=', $inviteCode)
                    ->where(function ($q) use ($userId) {
                        $q->where('player1_id', $userId)
                            ->orWhere('player2_id', $userId);
                    })
                    ->first();

                if ($otherActive) {
                    abort(409, 'Bạn đang có một phòng hoặc trận đấu khác đang diễn ra.');
                }

                $room = DuelRoom::where('room_code', $inviteCode)->lockForUpdate()->first();

                if (!$room) {
                    RateLimiter::hit($failKey, 60);
                    abort(404, 'Mã phòng thi đấu không tồn tại.');
                }

                if ($room->expires_at && $room->expires_at->isPast()) {
                    abort(404, 'Phòng thi đấu này đã hết hạn tham gia.');
                }

                $isAlreadyPlayer1 = ($room->player1_id === $userId);
                $isAlreadyPlayer2 = ($room->player2_id === $userId);

                if ($isAlreadyPlayer1 || $isAlreadyPlayer2) {
                    return [
                        'status' => 'already_joined',
                        'room_code' => $room->room_code,
                        'role' => $isAlreadyPlayer1 ? 'player1' : 'player2',
                    ];
                }

                if ($room->status !== 'waiting') {
                    abort(400, 'Phòng đấu đã đủ người hoặc trận đấu đã bắt đầu.');
                }

                $joined = $this->gameEngine->joinRoom($room, $playerData);
                RateLimiter::clear($failKey);

                return [
                    'status' => 'joined',
                    'room_code' => $joined->room_code,
                    'role' => 'player2',
                ];
            });

            return response()->json([
                'status' => $result['status'],
                'room_code' => $result['room_code'],
                'role' => $result['role'],
            ]);
        } catch (HttpException $e) {
            return response()->json(['message' => $e->getMessage()], $e->getStatusCode());
        } catch (InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * Lấy trạng thái đồng bộ thời gian thực của phòng đấu (State Polling).
     */
    public function state(Request $request, string $code): JsonResponse
    {
        $code = strtoupper(trim($code));
        $room = DuelRoom::where('room_code', $code)->first();

        if (!$room) {
            return response()->json(['message' => 'Phòng đấu không tồn tại.'], 404);
        }

        // Xác thực slot an toàn phía server
        $slot = $this->authService->resolveAuthenticatedSlot($room, $request);

        // Đồng bộ và lấy state công khai (đã che giấu đáp án câu chưa reveal)
        $state = $this->gameEngine->syncAndGetRoomState($room, $slot);

        return response()->json($state);
    }

    /**
     * Nộp câu trả lời cho vòng đấu hiện tại.
     */
    public function answer(Request $request, string $code): JsonResponse
    {
        $validated = $request->validate([
            'answer' => 'required|string|max:255',
            'idempotency_key' => 'required|string|max:64',
        ]);

        $code = strtoupper(trim($code));
        $room = DuelRoom::where('room_code', $code)->first();

        if (!$room) {
            return response()->json(['message' => 'Phòng đấu không tồn tại.'], 404);
        }

        $slot = $this->authService->resolveAuthenticatedSlot($room, $request);

        $result = $this->gameEngine->submitAnswer(
            $room,
            $slot,
            $validated['answer'],
            $validated['idempotency_key']
        );

        if ($result['status'] === 'rejected') {
            return response()->json($result, 400);
        }

        return response()->json($result);
    }

    /**
     * Gửi yêu cầu tái đấu (Rematch).
     */
    public function rematch(Request $request, string $code): JsonResponse
    {
        $code = strtoupper(trim($code));
        $room = DuelRoom::where('room_code', $code)->first();

        if (!$room) {
            return response()->json(['message' => 'Phòng đấu không tồn tại.'], 404);
        }

        $slot = $this->authService->resolveAuthenticatedSlot($room, $request);

        try {
            $result = $this->gameEngine->requestRematch($room, $slot);

            return response()->json($result);
        } catch (InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
