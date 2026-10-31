<?php

namespace App\Services\Duel;

use App\Models\DuelRoom;
use App\Models\DuelRoundAnswer;
use App\Models\Question;
use App\Models\User;
use App\Services\XpService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;

class DuelGameEngine
{
    /**
     * Sinh mã phòng 6 ký tự không trùng lặp, loại trừ ký tự dễ nhầm lẫn.
     */
    public function generateRoomCode(): string
    {
        $alphabet = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
        $maxAttempts = 10;

        for ($i = 0; $i < $maxAttempts; $i++) {
            $code = '';
            for ($c = 0; $c < 6; $c++) {
                $code .= $alphabet[random_int(0, strlen($alphabet) - 1)];
            }

            if (!DuelRoom::where('room_code', $code)->exists()) {
                return $code;
            }
        }

        return strtoupper(Str::random(6));
    }

    /**
     * Tạo phòng đấu mới ở trạng thái waiting.
     */
    public function createRoom(int $hskLevel, string $mode, array $player1Data): DuelRoom
    {
        $now = microtime(true);

        return DuelRoom::create([
            'room_code' => $this->generateRoomCode(),
            'hsk_level' => $hskLevel,
            'mode' => in_array($mode, ['random', 'friend'], true) ? $mode : 'random',
            'status' => 'waiting',
            'player1_type' => 'user',
            'player1_id' => $player1Data['user_id'] ?? null,
            'player1_guest_token_hash' => null,
            'player1_name' => $player1Data['name'] ?? 'Đấu Thủ 1',
            'player1_avatar' => $player1Data['avatar'] ?? null,
            'player1_ip' => $player1Data['ip'] ?? null,
            'player1_ready' => true,
            'player1_last_seen_at' => $now,
            'expires_at' => now()->addMinutes(15),
        ]);
    }

    /**
     * Người chơi thứ 2 tham gia phòng, bốc 10 câu hỏi và chuyển sang countdown.
     */
    public function joinRoom(DuelRoom $room, array $player2Data): DuelRoom
    {
        return DB::transaction(function () use ($room, $player2Data) {
            $locked = DuelRoom::where('id', $room->id)->lockForUpdate()->firstOrFail();

            if ($locked->status !== 'waiting') {
                throw new InvalidArgumentException('Phòng đấu không ở trạng thái chờ người chơi.');
            }

            if ($locked->player2_id !== null || $locked->player2_guest_token_hash !== null) {
                throw new InvalidArgumentException('Phòng đấu đã có người thứ hai tham gia.');
            }

            // Invariant: Tuyệt đối không cho phép tài khoản tự đấu với chính mình
            if ($locked->player1_id !== null && $locked->player1_id === ($player2Data['user_id'] ?? null)) {
                throw new InvalidArgumentException('Bạn không thể tự tham gia thi đấu với chính tài khoản của mình.');
            }

            // Bốc 10 câu hỏi HSK chuẩn CLEC
            $questions = Question::where('hsk_level', $locked->hsk_level)
                ->standard('hsk_2_0')
                ->where('is_active', true)
                ->inRandomOrder()
                ->limit(10)
                ->get();

            if ($questions->count() < 10) {
                // Fallback nếu câu hỏi standard ít hơn 10
                $questions = Question::where('hsk_level', $locked->hsk_level)
                    ->where('is_active', true)
                    ->inRandomOrder()
                    ->limit(10)
                    ->get();
            }

            if ($questions->count() < 10) {
                throw new InvalidArgumentException("Không đủ 10 câu hỏi cho cấp HSK {$locked->hsk_level}.");
            }

            // Chuẩn hóa Snapshot 10 câu hỏi
            $snapshot = [];
            foreach ($questions as $q) {
                $options = is_array($q->options) ? $q->options : [];
                shuffle($options);

                $snapshot[] = [
                    'id' => $q->id,
                    'question' => $q->question,
                    'pinyin' => $q->pinyin,
                    'audio_text' => $q->audio_text,
                    'image' => $q->image,
                    'options' => $options,
                    'correct_answer' => $q->correct_answer,
                    'explanation' => $q->explanation,
                    'skill_type' => $q->skill_type ?? 'reading',
                    'question_type' => $q->question_type ?? 'single_choice',
                ];
            }

            $now = microtime(true);
            $locked->player2_type = 'user';
            $locked->player2_id = $player2Data['user_id'] ?? null;
            $locked->player2_guest_token_hash = null;
            $locked->player2_name = $player2Data['name'] ?? 'Đấu Thủ 2';
            $locked->player2_avatar = $player2Data['avatar'] ?? null;
            $locked->player2_ip = $player2Data['ip'] ?? null;
            $locked->is_same_ip = ($locked->player1_ip && $locked->player1_ip === ($player2Data['ip'] ?? null));
            $locked->player2_ready = true;
            $locked->player2_last_seen_at = $now;

            $locked->questions_snapshot = $snapshot;
            $locked->status = 'countdown';
            $locked->phase_started_at = $now;
            $locked->phase_ends_at = $now + 3.0000;
            $locked->save();

            return $locked;
        });
    }

    /**
     * Hàm trung tâm chuyển trạng thái phòng đấu có khóa bi quan (Lock đã được giữ bên ngoài).
     * Trả về true nếu trạng thái hoặc pha có sự thay đổi.
     */
    public function advanceStateLocked(DuelRoom $room, float $now): bool
    {
        // 1. Chuyển từ Countdown sang In-Progress (sau đúng 3.0s)
        if ($room->status === 'countdown' && $now >= (float) $room->phase_ends_at) {
            $room->status = 'in_progress';
            $room->current_round = 1;
            $room->round_phase = 'answering';
            $room->phase_started_at = $now;
            $room->phase_ends_at = $now + 10.0000;
            $room->started_at = now();
            return true;
        }

        // 2. Xử lý pha Answering
        if ($room->status === 'in_progress' && $room->round_phase === 'answering') {
            $round = $room->current_round;
            $answersCount = DuelRoundAnswer::where('duel_room_id', $room->id)
                ->where('round_number', $round)
                ->count();

            // TH 2a: Cả 2 slot đã nộp bài sớm
            if ($answersCount >= 2) {
                $room->round_phase = 'revealing';
                $room->phase_started_at = $now;
                $room->phase_ends_at = $now + 2.0000;
                return true;
            }

            // TH 2b: Quá deadline 10.0s (now > phase_ends_at)
            if ($now > (float) $room->phase_ends_at) {
                $existingSlots = DuelRoundAnswer::where('duel_room_id', $room->id)
                    ->where('round_number', $round)
                    ->pluck('player_slot')
                    ->toArray();

                foreach ([1, 2] as $slot) {
                    if (!in_array($slot, $existingSlots, true)) {
                        DuelRoundAnswer::create([
                            'duel_room_id' => $room->id,
                            'round_number' => $round,
                            'player_slot' => $slot,
                            'selected_answer' => null, // Timeout
                            'is_correct' => false,
                            'answer_time_seconds' => 10.0000,
                            'score_awarded' => 0,
                            'idempotency_key' => "timeout_room_{$room->id}_round_{$round}_slot_{$slot}",
                            'answered_at' => $now,
                        ]);
                    }
                }

                $room->round_phase = 'revealing';
                $room->phase_started_at = $now;
                $room->phase_ends_at = $now + 2.0000;
                return true;
            }
        }

        // 3. Xử lý pha Revealing (Hết 2.0s kết quả)
        if ($room->status === 'in_progress' && $room->round_phase === 'revealing') {
            if ($now >= (float) $room->phase_ends_at) {
                if ($room->current_round < 10) {
                    $room->current_round += 1;
                    $room->round_phase = 'answering';
                    $room->phase_started_at = $now;
                    $room->phase_ends_at = $now + 10.0000;
                    return true;
                } else {
                    $this->finishMatch($room);
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Kiểm tra trạng thái ngắt kết nối và xử lý Forfeit thời gian thực.
     */
    public function checkDisconnectionAndForfeit(DuelRoom $room, float $now): void
    {
        if ($room->status !== 'in_progress') {
            return;
        }

        $diff1 = $now - (float) ($room->player1_last_seen_at ?? $now);
        $diff2 = $now - (float) ($room->player2_last_seen_at ?? $now);

        // Nấc 1: Cả hai cùng mất kết nối quá 40 giây -> HỦY TRẬN (Abandoned)
        if ($diff1 > 40.0 && $diff2 > 40.0) {
            $room->status = 'abandoned';
            $room->winner_slot = null;
            $room->is_draw = false;
            $room->round_phase = null;
            $room->finished_at = now();
            $room->save();
            return;
        }

        // Nấc 2: Cả hai cùng mất kết nối trong khoảng 25s - 40s -> KHÔNG XỬ THUA AI
        if ($diff1 > 25.0 && $diff2 > 25.0) {
            return;
        }

        // Nấc 3: Chỉ MỘT người mất kết nối > 25s, người còn lại vẫn online tích cực (diff <= 25s)
        if ($diff1 > 25.0 && $diff2 <= 25.0) {
            $this->executeForfeit($room, 1, 2);
        } elseif ($diff2 > 25.0 && $diff1 <= 25.0) {
            $this->executeForfeit($room, 2, 1);
        }
    }

    /**
     * Xử thua kỹ thuật khi một người bỏ cuộc hoặc mất kết nối.
     */
    public function executeForfeit(DuelRoom $room, int $forfeitSlot, int $winnerSlot): void
    {
        $room->status = 'finished';
        $room->forfeit_by_slot = $forfeitSlot;
        $room->winner_slot = $winnerSlot;
        $room->is_draw = false;
        $room->round_phase = null;
        $room->finished_at = now();

        if (!$room->xp_awarded) {
            // Quy tắc Forfeit XP công bằng & chống Bot farm điểm cấp tốc:
            // Nếu bỏ trận ngay trong Hiệp 1 (dưới 15s từ khi bắt đầu trận): thưởng an ủi +15 XP.
            // Nếu đã thi đấu từ Hiệp 2 trở lên (>15s): thưởng đầy đủ +40 XP cho người thắng.
            $isEarlyForfeit = ($room->current_round === 1) &&
                ($room->started_at === null || now()->diffInSeconds($room->started_at) < 15);
            $xpAward = $isEarlyForfeit ? 15 : 40;

            $this->awardSlotXp($room, $winnerSlot, $xpAward);
            $room->xp_awarded = true;
        }

        $room->save();
    }

    /**
     * Chủ động đầu hàng khi trận đấu đang diễn ra.
     */
    public function surrenderMatch(DuelRoom $room, int $surrenderingSlot): DuelRoom
    {
        return DB::transaction(function () use ($room, $surrenderingSlot) {
            $locked = DuelRoom::where('id', $room->id)->lockForUpdate()->firstOrFail();

            if (!in_array($locked->status, ['countdown', 'in_progress'], true)) {
                throw new InvalidArgumentException('Trận đấu không trong trạng thái có thể đầu hàng.');
            }

            $winnerSlot = ($surrenderingSlot === 1) ? 2 : 1;
            $this->executeForfeit($locked, $surrenderingSlot, $winnerSlot);

            return $locked;
        });
    }

    /**
     * Hủy phòng chờ tìm trận khi chưa có đối thủ tham gia.
     */
    public function cancelWaitingRoom(DuelRoom $room, int $userId): DuelRoom
    {
        return DB::transaction(function () use ($room, $userId) {
            $locked = DuelRoom::where('id', $room->id)->lockForUpdate()->firstOrFail();

            if ($locked->status !== 'waiting') {
                throw new InvalidArgumentException('Không thể hủy vì trận đấu đã bắt đầu hoặc đã kết thúc.');
            }

            if ($locked->player1_id !== $userId) {
                throw new InvalidArgumentException('Bạn không phải chủ phòng.');
            }

            if ($locked->player2_id !== null) {
                throw new InvalidArgumentException('Đã có người tham gia phòng, không thể hủy phòng.');
            }

            $locked->status = 'abandoned';
            $locked->finished_at = now();
            $locked->save();

            return $locked;
        });
    }

    /**
     * Nộp đáp án với khóa bi quan, kiểm tra deadline và chống double-submit.
     */
    public function submitAnswer(DuelRoom $room, int $slot, string $answer, string $idempotencyKey): array
    {
        return DB::transaction(function () use ($room, $slot, $answer, $idempotencyKey) {
            $lockedRoom = DuelRoom::where('id', $room->id)->lockForUpdate()->firstOrFail();
            $now = microtime(true);

            // Cập nhật heartbeat
            if ($slot === 1) {
                $lockedRoom->player1_last_seen_at = $now;
            } elseif ($slot === 2) {
                $lockedRoom->player2_last_seen_at = $now;
            }

            $currentRound = $lockedRoom->current_round;

            // BƯỚC 1: KIỂM TRA ĐÃ NỘP TRƯỚC TIÊN (Không phụ thuộc round_phase)
            $existing = DuelRoundAnswer::where('duel_room_id', $lockedRoom->id)
                ->where('round_number', $currentRound)
                ->where('player_slot', $slot)
                ->first();

            if ($existing) {
                $isSameKey = ($existing->idempotency_key === $idempotencyKey);
                return [
                    'status' => $isSameKey ? 'success' : 'already_submitted',
                    'data' => $existing,
                    'replayed' => $isSameKey,
                ];
            }

            // BƯỚC 2: Đồng bộ pha phòng qua hàm trung tâm
            $this->advanceStateLocked($lockedRoom, $now);

            // BƯỚC 3: Kiểm tra điều kiện nhận bài
            if ($lockedRoom->status !== 'in_progress' || $lockedRoom->round_phase !== 'answering' || $now > (float) $lockedRoom->phase_ends_at) {
                $lockedRoom->save();

                // Phân biệt timeout do quá giờ vs phòng đã kết thúc
                if ($now > (float) $lockedRoom->phase_ends_at || $lockedRoom->round_phase === 'revealing') {
                    return [
                        'status' => 'timeout',
                        'message' => 'Hết thời gian trả lời câu hỏi',
                        'data' => null,
                    ];
                }

                return [
                    'status' => 'rejected',
                    'reason' => 'Trận đấu không trong pha nhận đáp án',
                    'data' => null,
                ];
            }

            // BƯỚC 4: Kiểm tra options hợp lệ & tính điểm từ thời gian thực chưa làm tròn
            $questions = $lockedRoom->questions_snapshot ?? [];
            if (!isset($questions[$currentRound - 1])) {
                throw new InvalidArgumentException("Câu hỏi vòng {$currentRound} không tồn tại.");
            }

            $currentQ = $questions[$currentRound - 1];
            if (!in_array($answer, $currentQ['options'] ?? [], true)) {
                abort(422, 'Đáp án không hợp lệ trong danh sách lựa chọn.');
            }

            $elapsed = max(0.0000, $now - (float) $lockedRoom->phase_started_at);
            $timeSeconds = min(10.0000, round($elapsed, 4));
            $isCorrect = ($answer === $currentQ['correct_answer']);

            $score = 0;
            if ($isCorrect) {
                // Tính điểm từ thời gian thực chưa làm tròn
                $score = 75 + (int) round(25 * max(0.0000, (10.0000 - $elapsed) / 10.0000));
            }

            // BƯỚC 5: Lưu bản ghi câu trả lời
            $record = DuelRoundAnswer::create([
                'duel_room_id' => $lockedRoom->id,
                'round_number' => $currentRound,
                'player_slot' => $slot,
                'question_id' => $currentQ['id'] ?? null,
                'selected_answer' => $answer,
                'is_correct' => $isCorrect,
                'answer_time_seconds' => $timeSeconds,
                'score_awarded' => $score,
                'idempotency_key' => $idempotencyKey,
                'answered_at' => $now,
            ]);

            // BƯỚC 6: Cập nhật điểm và tổng thời gian làm bài câu đúng (Tie-Breaker)
            if ($slot === 1) {
                $lockedRoom->player1_score += $score;
                if ($isCorrect) {
                    $lockedRoom->player1_correct_count += 1;
                    $lockedRoom->player1_total_correct_time += $timeSeconds;
                }
            } else {
                $lockedRoom->player2_score += $score;
                if ($isCorrect) {
                    $lockedRoom->player2_correct_count += 1;
                    $lockedRoom->player2_total_correct_time += $timeSeconds;
                }
            }

            // BƯỚC 7: Kích hoạt Reveal sớm nếu cả 2 đã nộp bài
            $this->advanceStateLocked($lockedRoom, $now);
            $lockedRoom->save();

            return [
                'status' => 'success',
                'data' => $record,
            ];
        });
    }

    /**
     * Đồng bộ trạng thái phòng cho một người chơi qua Smart Polling.
     */
    public function syncAndGetRoomState(DuelRoom $room, int $requestingSlot): array
    {
        return DB::transaction(function () use ($room, $requestingSlot) {
            $locked = DuelRoom::where('id', $room->id)->lockForUpdate()->firstOrFail();
            $now = microtime(true);

            // Cập nhật heartbeat
            if ($requestingSlot === 1) {
                $locked->player1_last_seen_at = $now;
            } elseif ($requestingSlot === 2) {
                $locked->player2_last_seen_at = $now;
            }

            // Kiểm tra Forfeit thời gian thực ngay trong Polling
            $this->checkDisconnectionAndForfeit($locked, $now);

            // Chuyển pha nếu đủ điều kiện
            if (in_array($locked->status, ['countdown', 'in_progress'], true)) {
                $this->advanceStateLocked($locked, $now);
            }

            $locked->save();

            return $this->formatPublicRoomState($locked, $requestingSlot);
        });
    }

    /**
     * Định dạng dữ liệu công khai trả về client, che giấu đáp án tuyệt đối.
     */
    public function formatPublicRoomState(DuelRoom $room, int $requestingSlot): array
    {
        $now = microtime(true);
        $currentRound = $room->current_round;
        $phase = $room->round_phase;
        $questions = $room->questions_snapshot ?? [];

        $sanitizedQuestions = [];
        foreach ($questions as $idx => $q) {
            $roundNum = $idx + 1;

            if ($roundNum > $currentRound && $room->status !== 'finished') {
                continue; // Không gửi câu hỏi tương lai
            }

            $item = [
                'round_number' => $roundNum,
                'question' => $q['question'] ?? '',
                'pinyin' => $q['pinyin'] ?? null,
                'audio_text' => $q['audio_text'] ?? null,
                'image' => $q['image'] ?? null,
                'options' => $q['options'] ?? [],
                'skill_type' => $q['skill_type'] ?? 'reading',
                'question_type' => $q['question_type'] ?? 'single_choice',
            ];

            // Chỉ tiết lộ correct_answer và explanation khi đã sang revealing hoặc finished
            if ($room->status === 'finished' || ($roundNum < $currentRound) || ($roundNum === $currentRound && $phase === 'revealing')) {
                $item['correct_answer'] = $q['correct_answer'] ?? null;
                $item['explanation'] = $q['explanation'] ?? null;
            }

            $sanitizedQuestions[] = $item;
        }

        // Lấy câu trả lời của 2 bên trong vòng hiện tại
        $answers = DuelRoundAnswer::where('duel_room_id', $room->id)
            ->where('round_number', $currentRound)
            ->get();

        $myAnswer = $answers->firstWhere('player_slot', $requestingSlot);
        $otherSlot = ($requestingSlot === 1) ? 2 : 1;
        $opponentAnswer = $answers->firstWhere('player_slot', $otherSlot);

        $opponentOffline = false;
        $opponentLastSeen = ($otherSlot === 1) ? $room->player1_last_seen_at : $room->player2_last_seen_at;
        if ($opponentLastSeen && ($now - (float) $opponentLastSeen > 10.0)) {
            $opponentOffline = true;
        }

        return [
            'room_code' => $room->room_code,
            'hsk_level' => $room->hsk_level,
            'status' => $room->status,
            'current_round' => $room->current_round,
            'round_phase' => $room->round_phase,
            'time_remaining' => max(0.0, round((float) ($room->phase_ends_at ?? $now) - $now, 2)),
            'player1' => [
                'name' => $room->player1_name,
                'avatar' => $room->player1_avatar,
                'score' => $room->player1_score,
                'correct_count' => $room->player1_correct_count,
            ],
            'player2' => [
                'name' => $room->player2_name,
                'avatar' => $room->player2_avatar,
                'score' => $room->player2_score,
                'correct_count' => $room->player2_correct_count,
            ],
            'my_slot' => $requestingSlot,
            'my_submitted' => $myAnswer !== null,
            'opponent_submitted' => $opponentAnswer !== null,
            'opponent_offline' => $opponentOffline,
            'winner_slot' => $room->winner_slot,
            'is_draw' => $room->is_draw,
            'forfeit_by_slot' => $room->forfeit_by_slot,
            'rematch_room_id' => $room->rematch_room_id,
            'rematch_requested_by' => $room->rematch_requested_by,
            'current_question' => end($sanitizedQuestions) ?: null,
            'questions' => $sanitizedQuestions,
        ];
    }

    /**
     * Kết thúc trận đấu, tính điểm, phân định Tie-Breaker và trao XP nguyên tử.
     */
    public function finishMatch(DuelRoom $room): void
    {
        $room->status = 'finished';
        $room->finished_at = now();
        $room->round_phase = null;

        $s1 = $room->player1_score;
        $s2 = $room->player2_score;

        if ($s1 > $s2) {
            $room->winner_slot = 1;
            $room->is_draw = false;
        } elseif ($s2 > $s1) {
            $room->winner_slot = 2;
            $room->is_draw = false;
        } else {
            // TIE-BREAKER 1: Ưu tiên kiến thức (số câu đúng)
            if ($room->player1_correct_count > $room->player2_correct_count) {
                $room->winner_slot = 1;
                $room->is_draw = false;
            } elseif ($room->player2_correct_count > $room->player1_correct_count) {
                $room->winner_slot = 2;
                $room->is_draw = false;
            } else {
                // TIE-BREAKER 2: Ưu tiên phản xạ chuẩn xác (tổng thời gian câu đúng)
                if ($room->player1_total_correct_time < $room->player2_total_correct_time) {
                    $room->winner_slot = 1;
                    $room->is_draw = false;
                } elseif ($room->player2_total_correct_time < $room->player1_total_correct_time) {
                    $room->winner_slot = 2;
                    $room->is_draw = false;
                } else {
                    $room->winner_slot = null;
                    $room->is_draw = true; // Hòa danh dự
                }
            }
        }

        // Trao thưởng XP trong cùng transaction
        if (!$room->xp_awarded) {
            if ($room->winner_slot !== null && !$room->is_draw) {
                $winSlot = $room->winner_slot;
                $loseSlot = ($winSlot === 1) ? 2 : 1;

                $this->awardSlotXp($room, $winSlot, 50); // Người thắng nhận 50 XP
                $this->awardSlotXp($room, $loseSlot, 15); // Người thua nhận 15 XP
            } elseif ($room->is_draw) {
                $this->awardSlotXp($room, 1, 30); // Hòa nhận 30 XP mỗi người
                $this->awardSlotXp($room, 2, 30);
            }

            $room->xp_awarded = true;
        }

        $room->save();
    }

    /**
     * Trao XP cho một slot nếu người chơi là User đã đăng nhập.
     */
    public function awardSlotXp(DuelRoom $room, int $slot, int $amount): void
    {
        $userId = ($slot === 1) ? $room->player1_id : $room->player2_id;
        $userType = ($slot === 1) ? $room->player1_type : $room->player2_type;

        if ($userType === 'user' && $userId !== null) {
            $user = User::find($userId);
            if ($user) {
                $idempotencyKey = "duel_room_{$room->id}_slot_{$slot}_user_{$user->id}";
                app(\App\Services\LearningActivityService::class)->logActivity(
                    $user,
                    'quiz_completed',
                    [
                        'idempotency_key' => $idempotencyKey,
                        'source_type' => 'duel_room',
                        'source_id' => $room->id,
                        'meta' => ['xp' => $amount],
                    ]
                );
            }
        }
    }

    /**
     * Yêu cầu tái đấu (Rematch) và tạo phòng mới độc lập.
     */
    public function requestRematch(DuelRoom $oldRoom, int $requestingSlot): array
    {
        return DB::transaction(function () use ($oldRoom, $requestingSlot) {
            $locked = DuelRoom::where('id', $oldRoom->id)->lockForUpdate()->firstOrFail();

            if ($locked->status !== 'finished') {
                throw new InvalidArgumentException('Trận đấu chưa kết thúc.');
            }

            // Nếu phòng mới đã được tạo bởi request trước
            if ($locked->rematch_room_id !== null) {
                $newRoom = DuelRoom::findOrFail($locked->rematch_room_id);
                return [
                    'status' => 'ready',
                    'room_code' => $newRoom->room_code,
                ];
            }

            // Người đầu tiên bấm Rematch -> Chờ đối thủ
            if ($locked->rematch_requested_by === null) {
                $locked->rematch_requested_by = $requestingSlot;
                $locked->save();

                return ['status' => 'waiting_opponent'];
            }

            // Nếu chính người đó gửi lại request
            if ($locked->rematch_requested_by === $requestingSlot) {
                return ['status' => 'waiting_opponent'];
            }

            // Người thứ hai bấm -> TẠO DUY NHẤT 1 PHÒNG MỚI
            $newRoom = $this->createRematchChildRoom($locked);
            $locked->rematch_room_id = $newRoom->id;
            $locked->save();

            return [
                'status' => 'ready',
                'room_code' => $newRoom->room_code,
            ];
        });
    }

    /**
     * Tạo bản ghi phòng con cho ván tái đấu.
     */
    protected function createRematchChildRoom(DuelRoom $parent): DuelRoom
    {
        $now = microtime(true);

        // Bốc 10 câu hỏi ngẫu nhiên mới
        $questions = Question::where('hsk_level', $parent->hsk_level)
            ->standard('hsk_2_0')
            ->where('is_active', true)
            ->inRandomOrder()
            ->limit(10)
            ->get();

        if ($questions->count() < 10) {
            $questions = Question::where('hsk_level', $parent->hsk_level)
                ->where('is_active', true)
                ->inRandomOrder()
                ->limit(10)
                ->get();
        }

        $snapshot = [];
        foreach ($questions as $q) {
            $options = is_array($q->options) ? $q->options : [];
            shuffle($options);

            $snapshot[] = [
                'id' => $q->id,
                'question' => $q->question,
                'pinyin' => $q->pinyin,
                'audio_text' => $q->audio_text,
                'image' => $q->image,
                'options' => $options,
                'correct_answer' => $q->correct_answer,
                'explanation' => $q->explanation,
                'skill_type' => $q->skill_type ?? 'reading',
                'question_type' => $q->question_type ?? 'single_choice',
            ];
        }

        return DuelRoom::create([
            'room_code' => $this->generateRoomCode(),
            'hsk_level' => $parent->hsk_level,
            'mode' => 'friend',
            'status' => 'countdown',
            'parent_room_id' => $parent->id,
            'player1_type' => $parent->player1_type,
            'player1_id' => $parent->player1_id,
            'player1_guest_token_hash' => $parent->player1_guest_token_hash,
            'player1_name' => $parent->player1_name,
            'player1_avatar' => $parent->player1_avatar,
            'player1_ready' => true,
            'player1_last_seen_at' => $now,
            'player2_type' => $parent->player2_type,
            'player2_id' => $parent->player2_id,
            'player2_guest_token_hash' => $parent->player2_guest_token_hash,
            'player2_name' => $parent->player2_name,
            'player2_avatar' => $parent->player2_avatar,
            'player2_ready' => true,
            'player2_last_seen_at' => $now,
            'questions_snapshot' => $snapshot,
            'phase_started_at' => $now,
            'phase_ends_at' => $now + 3.0000,
        ]);
    }
}
