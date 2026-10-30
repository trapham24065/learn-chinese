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
        return view('duel.lobby', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Sàn đấu Battle Arena UI (Blade + Alpine.js).
     */
    public function arena(Request $request, string $code): Response|View
    {
        $code = strtoupper(trim($code));
        $room = DuelRoom::where('room_code', $code)->firstOrFail();

        $playerData = $this->authService->resolvePlayerIdentity($request, $code);

        $isPlayer1 = ($playerData['type'] === 'user' && $room->player1_id === $playerData['user_id']) ||
            ($playerData['type'] === 'guest' && $room->player1_guest_token_hash === $playerData['guest_token_hash']);
        $isPlayer2 = ($playerData['type'] === 'user' && $room->player2_id === $playerData['user_id']) ||
            ($playerData['type'] === 'guest' && $room->player2_guest_token_hash === $playerData['guest_token_hash']);

        // Nếu khách hoặc user mới bấm vào link mời phòng riêng còn đang 'waiting' và slot 2 chưa có ai
        if (!$isPlayer1 && !$isPlayer2) {
            if ($room->status === 'waiting' && is_null($room->player2_id) && is_null($room->player2_guest_token_hash)) {
                $room = $this->gameEngine->joinRoom($room, $playerData);
                $isPlayer2 = true;
            } else {
                abort(403, 'Bạn không có quyền tham gia phòng đấu này.');
            }
        }

        $slot = $isPlayer1 ? 1 : 2;
        $initialState = $this->gameEngine->syncAndGetRoomState($room, $slot);

        $response = response()->view('duel.arena', [
            'room' => $room,
            'roomCode' => $code,
            'slot' => $slot,
            'initialState' => $initialState,
        ]);

        if ($playerData['type'] === 'guest' && !empty($playerData['guest_token_plain'])) {
            $response->withCookie(
                $this->authService->makeGuestCookie($room->room_code, $playerData['guest_token_plain'])
            );
        }

        return $response;
    }

    /**
     * Lịch sử thi đấu cá nhân (Duel History).
     */
    public function history(Request $request): View
    {
        $guestHashes = [];

        if (Auth::guard('web')->check()) {
            $userId = Auth::guard('web')->id();
            $baseQuery = DuelRoom::whereIn('status', ['finished', 'abandoned'])
                ->where(function ($q) use ($userId) {
                    $q->where('player1_id', $userId)
                        ->orWhere('player2_id', $userId);
                });
        } else {
            // Guest: Lọc riêng các cookie scoped theo phòng của chính trình duyệt này
            foreach ($request->cookies->all() as $name => $val) {
                if (str_starts_with($name, 'duel_guest_token_') && is_string($val)) {
                    $guestHashes[] = hash_hmac('sha256', $val, config('app.key'));
                }
            }

            if (empty($guestHashes)) {
                $baseQuery = DuelRoom::whereRaw('1 = 0');
            } else {
                $baseQuery = DuelRoom::whereIn('status', ['finished', 'abandoned'])
                    ->where(function ($q) use ($guestHashes) {
                        $q->whereIn('player1_guest_token_hash', $guestHashes)
                            ->orWhereIn('player2_guest_token_hash', $guestHashes);
                    });
            }
        }

        // Tính toán thống kê tổng quát
        $allMatches = (clone $baseQuery)->get();
        $total = $allMatches->count();
        $wins = 0;
        $losses = 0;
        $draws = 0;

        $currentUserId = Auth::guard('web')->id();
        foreach ($allMatches as $m) {
            $slot = null;
            if ($currentUserId) {
                $slot = ($m->player1_id === $currentUserId) ? 1 : (($m->player2_id === $currentUserId) ? 2 : null);
            } else {
                if (in_array($m->player1_guest_token_hash, $guestHashes, true)) {
                    $slot = 1;
                } elseif (in_array($m->player2_guest_token_hash, $guestHashes, true)) {
                    $slot = 2;
                }
            }

            if (!$slot) continue;

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
            'isGuest' => !Auth::guard('web')->check(),
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

        $result = DB::transaction(function () use ($hskLevel, $playerData) {
            // 1. Kiểm tra xem người này đã có phòng ngẫu nhiên đang hoạt động (waiting/countdown/in_progress) chưa
            $existingQuery = DuelRoom::where('hsk_level', $hskLevel)
                ->where('mode', 'random')
                ->whereIn('status', ['waiting', 'countdown', 'in_progress']);

            if ($playerData['type'] === 'user') {
                $existingQuery->where(function ($q) use ($playerData) {
                    $q->where('player1_id', $playerData['user_id'])
                        ->orWhere('player2_id', $playerData['user_id']);
                });
            } else {
                $existingQuery->where(function ($q) use ($playerData) {
                    $q->where('player1_guest_token_hash', $playerData['guest_token_hash'])
                        ->orWhere('player2_guest_token_hash', $playerData['guest_token_hash']);
                });
            }

            $existingRoom = $existingQuery->first();
            if ($existingRoom) {
                $role = ($playerData['type'] === 'user' && $existingRoom->player1_id === $playerData['user_id']) ||
                    ($playerData['type'] === 'guest' && $existingRoom->player1_guest_token_hash === $playerData['guest_token_hash'])
                    ? 'player1' : 'player2';

                return [
                    'status' => ($existingRoom->status === 'waiting') ? 'waiting' : 'matched',
                    'room_code' => $existingRoom->room_code,
                    'role' => $role,
                    'is_existing' => true,
                ];
            }

            // 2. Tìm một phòng chờ công khai của người khác và khóa dòng
            $availableQuery = DuelRoom::where('hsk_level', $hskLevel)
                ->where('mode', 'random')
                ->where('status', 'waiting')
                ->whereNull('player2_id')
                ->whereNull('player2_guest_token_hash');

            if ($playerData['type'] === 'user') {
                $availableQuery->where('player1_id', '!=', $playerData['user_id']);
            } else {
                $availableQuery->where('player1_guest_token_hash', '!=', $playerData['guest_token_hash']);
            }

            $availableRoom = $availableQuery->lockForUpdate()->first();

            if ($availableRoom) {
                $joined = $this->gameEngine->joinRoom($availableRoom, $playerData);
                return [
                    'status' => 'matched',
                    'room_code' => $joined->room_code,
                    'role' => 'player2',
                    'is_existing' => false,
                ];
            }

            // 3. Nếu không có ai đang chờ -> Tạo phòng chờ mới
            $newRoom = $this->gameEngine->createRoom($hskLevel, 'random', $playerData);
            return [
                'status' => 'waiting',
                'room_code' => $newRoom->room_code,
                'role' => 'player1',
                'is_existing' => false,
            ];
        });

        $response = response()->json([
            'status' => $result['status'],
            'room_code' => $result['room_code'],
            'role' => $result['role'],
        ]);

        if ($playerData['type'] === 'guest' && !empty($playerData['guest_token_plain'])) {
            $response->withCookie(
                $this->authService->makeGuestCookie($result['room_code'], $playerData['guest_token_plain'])
            );
        }

        return $response;
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

        $room = $this->gameEngine->createRoom($hskLevel, $mode, $playerData);

        $response = response()->json([
            'status' => 'created',
            'room_code' => $room->room_code,
            'hsk_level' => $room->hsk_level,
            'mode' => $room->mode,
            'invite_url' => url("/duel/room/{$room->room_code}"),
        ], 201);

        if ($playerData['type'] === 'guest' && !empty($playerData['guest_token_plain'])) {
            $response->withCookie(
                $this->authService->makeGuestCookie($room->room_code, $playerData['guest_token_plain'])
            );
        }

        return $response;
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

        try {
            $result = DB::transaction(function () use ($inviteCode, $playerData, $failKey) {
                $room = DuelRoom::where('room_code', $inviteCode)->lockForUpdate()->first();

                if (!$room) {
                    RateLimiter::hit($failKey, 60);
                    abort(404, 'Mã phòng thi đấu không tồn tại.');
                }

                if ($room->expires_at && $room->expires_at->isPast()) {
                    abort(404, 'Phòng thi đấu này đã hết hạn tham gia.');
                }

                $isAlreadyPlayer1 = ($playerData['type'] === 'user' && $room->player1_id === $playerData['user_id']) ||
                    ($playerData['type'] === 'guest' && $room->player1_guest_token_hash === $playerData['guest_token_hash']);

                $isAlreadyPlayer2 = ($playerData['type'] === 'user' && $room->player2_id === $playerData['user_id']) ||
                    ($playerData['type'] === 'guest' && $room->player2_guest_token_hash === $playerData['guest_token_hash']);

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

            $response = response()->json([
                'status' => $result['status'],
                'room_code' => $result['room_code'],
                'role' => $result['role'],
            ]);

            if ($playerData['type'] === 'guest' && !empty($playerData['guest_token_plain'])) {
                $response->withCookie(
                    $this->authService->makeGuestCookie($result['room_code'], $playerData['guest_token_plain'])
                );
            }

            return $response;
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

            $response = response()->json($result);

            // Nếu phòng mới đã được tạo và là Guest, đính kèm cookie cho phòng mới
            if (isset($result['room_code'])) {
                $guestCookie = $request->cookie("duel_guest_token_{$code}");
                if ($guestCookie) {
                    $response->withCookie(
                        $this->authService->makeGuestCookie($result['room_code'], $guestCookie)
                    );
                }
            }

            return $response;
        } catch (InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
