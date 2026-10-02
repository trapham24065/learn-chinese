<?php

namespace App\Services\Pet;

use App\Models\Flashcard;
use App\Models\PetMemory;
use App\Models\UserPet;
use Carbon\Carbon;

class PetMemoryService
{
    /**
     * Remember the first word the user ever learned or reviewed.
     */
    public function rememberFirstWord(UserPet $userPet, Flashcard $flashcard): ?PetMemory
    {
        $hanzi = $flashcard->hanzi ?? $flashcard->word ?? '';
        return $this->createUniqueMemory($userPet, 'first_word', 'first_word', [
            'title'           => "Từ vựng đầu tiên: 「{$hanzi}」",
            'description'     => "Ngày đầu tiên chúng mình cùng học từ 「{$hanzi}」({$flashcard->pinyin}) - '{$flashcard->meaning}'. Kỷ niệm khó quên!",
            'importance'      => 90,
            'related_word_id' => $flashcard->id,
            'metadata'        => [
                'hanzi'   => $hanzi,
                'pinyin'  => $flashcard->pinyin,
                'meaning' => $flashcard->meaning,
            ],
        ]);
    }

    /**
     * Remember the first word the user successfully mastered (repetition >= 2).
     */
    public function rememberFirstMastered(UserPet $userPet, Flashcard $flashcard): ?PetMemory
    {
        $hanzi = $flashcard->hanzi ?? $flashcard->word ?? '';
        return $this->createUniqueMemory($userPet, 'first_mastered', 'first_mastered_word', [
            'title'           => "Làm chủ từ đầu tiên: 「{$hanzi}」",
            'description'     => "Bạn đã thuộc lòng và làm chủ từ 「{$hanzi}」({$flashcard->pinyin})! Pet tự hào về bạn lắm.",
            'importance'      => 95,
            'related_word_id' => $flashcard->id,
            'metadata'        => [
                'hanzi'   => $hanzi,
                'pinyin'  => $flashcard->pinyin,
                'meaning' => $flashcard->meaning,
            ],
        ]);
    }

    /**
     * Remember when user reaches a streak milestone (e.g. 3, 7, 14, 30 days).
     */
    public function rememberStreakMilestone(UserPet $userPet, int $streak): ?PetMemory
    {
        if (!in_array($streak, [3, 7, 14, 21, 30, 60, 100], true)) {
            return null;
        }

        return $this->createUniqueMemory($userPet, 'streak_milestone', "streak_{$streak}", [
            'title'       => "Chuỗi học kiên trì {$streak} ngày!",
            'description' => "Chúng mình đã đồng hành cùng nhau suốt {$streak} ngày liên tục. Thói quen tuyệt vời này sẽ giúp bạn thành công!",
            'importance'  => min(100, 70 + ($streak >= 30 ? 30 : $streak)),
            'metadata'    => ['streak_days' => $streak],
        ]);
    }

    /**
     * Remember when user achieves Daily Goal.
     */
    public function rememberDailyGoal(UserPet $userPet): ?PetMemory
    {
        $today = Carbon::now(config('app.timezone', 'Asia/Ho_Chi_Minh'))->toDateString();
        $isFirstEver = !PetMemory::where('user_pet_id', $userPet->id)
            ->where('type', 'daily_goal')
            ->exists();

        return $this->createUniqueMemory($userPet, 'daily_goal', "daily_goal_{$today}", [
            'title'       => $isFirstEver ? 'Hoàn thành Mục tiêu ngày đầu tiên!' : "Đạt mục tiêu ngày {$today}",
            'description' => $isFirstEver
                ? 'Lần đầu tiên bạn hoàn thành trọn vẹn mục tiêu học trong ngày. Khởi đầu xuất sắc!'
                : 'Mục tiêu học hôm nay đã hoàn thành trọn vẹn cùng Pet.',
            'importance'  => $isFirstEver ? 85 : 50,
            'metadata'    => ['date' => $today, 'is_first' => $isFirstEver],
        ]);
    }

    /**
     * Remember pet evolution.
     */
    public function rememberEvolution(UserPet $userPet, int $newStage, string $stageName, string $emoji): ?PetMemory
    {
        return $this->createUniqueMemory($userPet, 'stage_reached', "evolution_stage_{$newStage}", [
            'title'       => "Tiến hóa lên Giai đoạn {$newStage}: {$stageName} {$emoji}!",
            'description' => "Pet đã trưởng thành và biến đổi hình dạng rực rỡ nhờ công sức học tiếng Trung chăm chỉ của bạn.",
            'importance'  => 100,
            'metadata'    => ['new_stage' => $newStage, 'stage_name' => $stageName, 'emoji' => $emoji],
        ]);
    }

    /**
     * Remember when user returns after being absent for 2 or more days.
     */
    public function rememberAbsenceReturn(UserPet $userPet, int $daysAbsent): ?PetMemory
    {
        $today = Carbon::now(config('app.timezone', 'Asia/Ho_Chi_Minh'))->toDateString();

        return $this->createUniqueMemory($userPet, 'absence_return', "return_{$today}", [
            'title'       => "Chào mừng bạn quay lại học tập!",
            'description' => "Sau {$daysAbsent} ngày vắng mặt, bạn đã quay lại với bàn học. Pet mừng lắm!",
            'importance'  => 65,
            'metadata'    => ['days_absent' => $daysAbsent, 'date' => $today],
        ]);
    }

    /**
     * Recall a relevant memory to bring up in conversation.
     * Prioritizes high importance and memories not recalled recently.
     */
    public function recallRelevantMemory(UserPet $userPet): ?PetMemory
    {
        $memory = PetMemory::where('user_pet_id', $userPet->id)
            ->where('importance', '>=', 50)
            ->where(function ($q) {
                $q->whereNull('last_recalled_at')
                  ->orWhere('last_recalled_at', '<', now()->subHours(6));
            })
            ->orderByRaw('last_recalled_at IS NULL DESC')
            ->orderByDesc('importance')
            ->orderBy('last_recalled_at', 'ASC')
            ->first();

        if ($memory) {
            $memory->update([
                'last_recalled_at' => now(),
                'recall_count'     => ($memory->recall_count ?? 0) + 1,
            ]);
        }

        return $memory;
    }

    /**
     * Format a recalled memory into a conversational dialogue line tailored to personality.
     */
    public function formatMemoryDialogue(PetMemory $memory, string $personality): string
    {
        $type = $memory->type;
        $meta = $memory->metadata ?? [];

        if ($type === 'first_mastered' || $type === 'first_word') {
            $hanzi = $meta['hanzi'] ?? 'từ đầu tiên';
            $meaning = $meta['meaning'] ?? '';
            $meaningText = $meaning ? " ('{$meaning}')" : '';

            return match ($personality) {
                'playful'  => "Nè nè! Bạn còn nhớ từ 「{$hanzi}」{$meaningText} không? Hôm trước chúng mình đã cùng nhau học đó, đố bạn đọc lại được nè~ ✨",
                'curious'  => "Tớ vừa nhớ lại lúc chúng mình cùng học từ 「{$hanzi}」{$meaningText}... Bạn có muốn cùng tớ ôn lại chữ này một chút không? 🔍",
                'shy'      => "Mỗi lần nhìn lại từ 「{$hanzi}」, tớ lại thấy ấm áp ghê... Đó là kỷ niệm đầu tiên của hai đứa mình đó! 🌸",
                'cheerful' => "Nhìn thấy từ 「{$hanzi}」{$meaningText} là tớ lại nhớ bạn đã cố gắng thế nào! Bạn thật sự rất tuyệt vời đó! ☀️",
                'calm'     => "Kỷ niệm khi bạn làm chủ từ 「{$hanzi}」vẫn còn rất rõ. Từng chữ Hán được tích lũy sẽ đưa bạn đi rất xa. 🍵",
                default    => "Bạn còn nhớ từ 「{$hanzi}」{$meaningText} không? Chúng mình đã học từ này cùng nhau đó! 📖",
            };
        }

        if ($type === 'streak_milestone') {
            $streak = $meta['streak_days'] ?? 3;
            return match ($personality) {
                'playful'  => "Chuỗi {$streak} ngày học cùng nhau của chúng mình đỉnh thật đấy! Hôm nay quẩy tiếp thôi nào! 🚀",
                'curious'  => "Chúng mình đã bên nhau được {$streak} ngày rồi đó. Hôm nay bạn muốn khám phá thêm bài học nào nè? 🔍",
                'shy'      => "Cảm ơn bạn đã luôn chăm chỉ... {$streak} ngày bên bạn là khoảng thời gian rất vui với tớ. 🌸",
                'cheerful' => "{$streak} ngày liên tiếp rồi đó! Phong độ của bạn đỉnh chóp luôn, hôm nay tiếp tục tỏa sáng nhé! ☀️",
                'calm'     => "{$streak} ngày kiên trì bền bỉ. Sự kỷ luật của bạn chính là chìa khóa để làm chủ tiếng Trung. 🍵",
                default    => "Chuỗi {$streak} ngày học tập kiên trì! Cùng giữ vững phong độ nhé! 🔥",
            };
        }

        if ($type === 'absence_return') {
            return match ($personality) {
                'playful'  => "A! Cậu quay lại rồi! Tớ đợi cậu muốn rụng cả râu rồng luôn nè, mau cùng học thôi nào! 🐲",
                'curious'  => "Hoan nghênh bạn trở lại! Mấy hôm nay bạn có bận gì không? Mình cùng tiếp tục học chữ mới nhé! 🔍",
                'shy'      => "Cậu về rồi... Tớ nhớ cậu lắm! Hôm nay chúng mình lại cùng học nhẹ nhàng nhé? 🌸",
                'cheerful' => "Chào mừng bạn trở lại! Thật vui vì lại được thấy bạn mở sách học tiếng Trung hôm nay! ☀️",
                'calm'     => "Mừng bạn quay lại. Học tập là chặng đường dài, nghỉ ngơi lấy lại sức rồi tiếp tục tiến bước nhé. 🍵",
                default    => "Chào mừng bạn đã trở lại! Hôm nay chúng mình cùng học tiếng Trung thật vui nhé! ✨",
            };
        }

        return "Tớ luôn ghi nhớ từng bước trưởng thành của bạn: {$memory->title} 💖";
    }

    /**
     * Helper to create idempotent memory by unique memory_key.
     */
    protected function createUniqueMemory(UserPet $userPet, string $type, string $key, array $data): ?PetMemory
    {
        $existing = PetMemory::where('user_pet_id', $userPet->id)
            ->where('memory_key', $key)
            ->first();

        if ($existing) {
            return $existing;
        }

        return PetMemory::create(array_merge([
            'user_pet_id' => $userPet->id,
            'type'        => $type,
            'memory_key'  => $key,
            'created_at'  => now(),
        ], $data));
    }
}
