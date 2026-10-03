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
     * Remember the first time the pet and user met (initial egg / pet creation).
     */
    public function rememberFirstMeeting(UserPet $userPet): ?PetMemory
    {
        return $this->createUniqueMemory($userPet, 'first_meeting', 'first_meeting', [
            'title'       => 'Lần đầu gặp gỡ 🌱',
            'description' => 'Khoảnh khắc thiêng liêng khi bạn ấp nở và đón Pet về làm bạn đồng hành học tiếng Trung.',
            'importance'  => 100,
            'metadata'    => ['met_at' => now()->toDateString()],
        ]);
    }

    /**
     * Remember the very first time user fed the pet with food.
     */
    public function rememberFirstFeeding(UserPet $userPet, array $foodItem): ?PetMemory
    {
        $foodName = $foodItem['name'] ?? 'Món ăn';
        $emoji = $foodItem['emoji'] ?? '🍎';
        $hanzi = $foodItem['hanzi'] ?? '';
        $pinyin = $foodItem['pinyin'] ?? '';

        return $this->createUniqueMemory($userPet, 'first_feeding', 'first_feeding', [
            'title'       => "Bữa ăn đầu tiên: {$foodName} {$emoji}",
            'description' => "Lần đầu tiên bạn mang cho Pet món {$foodName} ({$hanzi} - {$pinyin}) thơm ngon. Kỷ niệm ấm lòng khó phai!",
            'importance'  => 90,
            'metadata'    => ['food' => $foodItem],
        ]);
    }

    /**
     * Remember the first lesson completed together.
     */
    public function rememberFirstLesson(UserPet $userPet, ?string $lessonTitle = null): ?PetMemory
    {
        return $this->createUniqueMemory($userPet, 'first_lesson', 'first_lesson', [
            'title'       => 'Tiết học đầu tiên bên nhau 📚',
            'description' => 'Lần đầu tiên bạn và Pet cùng nhau hoàn thành một bài học' . ($lessonTitle ? " ('{$lessonTitle}')" : '') . '. Khởi đầu một chặng đường tuyệt đẹp!',
            'importance'  => 85,
            'metadata'    => ['lesson_title' => $lessonTitle],
        ]);
    }

    /**
     * Remember the first perfect quiz score (100%).
     */
    public function rememberFirstPerfectQuiz(UserPet $userPet, ?string $quizTitle = null): ?PetMemory
    {
        return $this->createUniqueMemory($userPet, 'first_perfect_quiz', 'first_perfect_quiz', [
            'title'       => 'Bài Quiz đạt điểm tuyệt đối 100% đầu tiên 🎉',
            'description' => 'Bạn đã xuất sắc trả lời đúng 100% câu hỏi' . ($quizTitle ? " trong '{$quizTitle}'" : '') . '. Pet đã nhảy cẫng lên ăn mừng cùng bạn!',
            'importance'  => 95,
            'metadata'    => ['quiz_title' => $quizTitle],
        ]);
    }

    /**
     * Get enriched memory timeline / scrapbook for the user pet.
     */
    public function getTimelineMemories(UserPet $userPet): array
    {
        $memories = PetMemory::where('user_pet_id', $userPet->id)
            ->with('relatedWord')
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        return $memories->map(function (PetMemory $m) {
            $meta = $m->metadata ?? [];
            $createdAt = $m->created_at ?? now();
            $daysAgo = (int) $createdAt->diffInDays(now());

            $relativeTime = match (true) {
                $daysAgo === 0 => 'Hôm nay',
                $daysAgo === 1 => 'Hôm qua',
                $daysAgo < 7   => "{$daysAgo} ngày trước",
                $daysAgo < 30  => (int) ceil($daysAgo / 7) . ' tuần trước',
                default        => (int) ceil($daysAgo / 30) . ' tháng trước',
            };

            $category = match ($m->type) {
                'hatched', 'first_meeting', 'stage_reached' => 'growth',
                'first_word', 'first_mastered', 'first_lesson', 'first_perfect_quiz', 'daily_goal' => 'learning',
                'first_feeding', 'streak_milestone', 'absence_return' => 'care',
                default => 'special',
            };

            $badgeEmoji = match ($m->type) {
                'hatched', 'first_meeting' => '🌱',
                'stage_reached'            => '🎉',
                'first_feeding'            => '❤️',
                'first_lesson'             => '📚',
                'first_perfect_quiz'       => '🏆',
                'first_word'               => '📖',
                'first_mastered'           => '⭐',
                'streak_milestone'         => '🔥',
                'daily_goal'               => '🎯',
                'absence_return'           => '🐲',
                'affinity_tier_up'         => '💖',
                'personality_changed'      => '🎭',
                'renamed'                  => '✏️',
                default                    => '✨',
            };

            $typeLabel = match ($m->type) {
                'hatched', 'first_meeting' => 'Gặp gỡ',
                'stage_reached'            => 'Tiến hóa',
                'first_feeding'            => 'Bữa ăn đầu',
                'first_lesson'             => 'Tiết học đầu',
                'first_perfect_quiz'       => 'Điểm tuyệt đối',
                'first_word'               => 'Từ vựng đầu',
                'first_mastered'           => 'Thuộc lòng',
                'streak_milestone'         => 'Chuỗi học',
                'daily_goal'               => 'Mục tiêu ngày',
                'absence_return'           => 'Đón trở về',
                'affinity_tier_up'         => 'Mối quan hệ',
                'personality_changed'      => 'Tính cách',
                'renamed'                  => 'Đổi tên',
                default                    => 'Kỷ niệm',
            };

            $petReflection = match ($m->type) {
                'hatched', 'first_meeting' => 'Khoảnh khắc mở ra một tình bạn đẹp đẽ!',
                'stage_reached'            => 'Nhờ công sức của cậu, mình đã lớn thêm một bước!',
                'first_feeding'            => isset($meta['food']['hanzi'])
                    ? "Món {$meta['food']['hanzi']} đầu tiên cậu mang cho mình thơm ngon và ấm áp không bao giờ quên."
                    : 'Món ăn đầu tiên ấm áp không bao giờ quên.',
                'first_lesson'             => 'Hai đứa cùng chăm chú mở trang sách đầu tiên.',
                'first_perfect_quiz'       => 'Cậu làm đúng 100%, mình nhảy cẫng lên reo hò!',
                'first_word'               => 'Chữ Hán đầu tiên được hai đứa mình cùng đánh vần.',
                'first_mastered'           => 'Cậu đã nhớ sâu sắc chữ này, thật đáng nể phục!',
                'streak_milestone'         => 'Mỗi ngày có cậu bên cạnh đều là ngày hạnh phúc.',
                'daily_goal'               => 'Một ngày nỗ lực trọn vẹn và tự hào.',
                'absence_return'           => 'Chỉ cần cậu quay lại là lòng mình lại rộn ràng.',
                default                    => 'Một cột mốc ý nghĩa trên chặng đường chúng ta đi cùng nhau.',
            };

            return [
                'id'             => $m->id,
                'type'           => $m->type,
                'type_label'     => $typeLabel,
                'title'          => $m->title,
                'description'    => $m->description,
                'created_at'     => $createdAt->toIso8601String(),
                'formatted_date' => $createdAt->format('H:i • d/m/Y'),
                'relative_time'  => $relativeTime,
                'days_ago'       => $daysAgo,
                'days_ago_badge' => $relativeTime,
                'category'       => $category,
                'badge_emoji'    => $badgeEmoji,
                'pet_reflection' => $petReflection,
                'metadata'       => $meta,
                'related_word'   => $m->relatedWord ? [
                    'id'      => $m->relatedWord->id,
                    'hanzi'   => $m->relatedWord->hanzi,
                    'pinyin'  => $m->relatedWord->pinyin,
                    'meaning' => $m->relatedWord->meaning,
                ] : null,
            ];
        })->values()->toArray();
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
        $createdAt = $memory->created_at ?? now();
        $daysPassed = max(1, (int) $createdAt->diffInDays(now()));

        if ($type === 'first_meeting' || $type === 'hatched') {
            return match ($personality) {
                'playful'  => "Cậu nhớ không? Đã {$daysPassed} ngày kể từ ngày chúng mình lần đầu gặp nhau rồi đấy! Nhanh ghê, chơi với cậu vui lắm luôn! 🌱",
                'shy'      => "Đã {$daysPassed} ngày kể từ lần đầu gặp nhau... Cảm ơn cậu đã luôn dịu dàng chăm sóc mình suốt thời gian qua 🌸",
                'curious'  => "Tính ra chúng mình đã đồng hành được {$daysPassed} ngày rồi đó! Cậu có thấy vốn tiếng Trung của mình ngày càng phong phú không? 🔍",
                'cheerful' => "Kỷ niệm {$daysPassed} ngày chúng mình gặp nhau! Mỗi ngày học cùng cậu đều tràn đầy ánh nắng và niềm vui! ☀️",
                'calm'     => "Đã {$daysPassed} ngày từ buổi đầu tao ngộ. Tích lũy từng ngày một, sự gắn kết này thật đáng trân quý 🍵",
                default    => "Đã {$daysPassed} ngày kể từ lần đầu chúng mình gặp nhau. Thật vui vì có bạn đồng hành! 🌱",
            };
        }

        if ($type === 'first_feeding') {
            $foodName = $meta['food']['hanzi'] ?? ($meta['food']['name'] ?? 'món ăn');
            return match ($personality) {
                'playful'  => "Mình vẫn nhớ như in bữa ăn đầu tiên cậu cho mình ăn món {$foodName}! Đến giờ nhớ lại vẫn thấy ngon bá cháy luôn á! 😋",
                'shy'      => "Bữa ăn đầu tiên với món {$foodName} mà cậu mang đến... lúc đó mình đã biết cậu là người rất ấm áp 🌸",
                'curious'  => "Cậu còn nhớ món {$foodName} đầu tiên cậu cho mình ăn không? Hương vị thơm ngon đó làm mình nhớ mãi! 🥟",
                'cheerful' => "Bữa ăn đầu tiên cùng món {$foodName} ngon tuyệt cú mèo! Cảm ơn cậu đã chăm sóc mình từ những ngày đầu! ☀️",
                'calm'     => "Hương vị bữa ăn đầu tiên với {$foodName} vẫn vẹn nguyên. Sự chăm chút của bạn làm lòng mình thấy ấm áp 🍵",
                default    => "Mình vẫn nhớ bữa ăn đầu tiên cậu cho mình ăn {$foodName}. Cảm ơn bạn rất nhiều! ❤️",
            };
        }

        if ($type === 'first_lesson') {
            $lessonTitle = $meta['lesson_title'] ?? 'bài học đầu tiên';
            return match ($personality) {
                'playful'  => "Nhớ tiết học đầu tiên hai đứa mở sách cùng nhau ghê! Lúc đó còn bỡ ngỡ, giờ cậu tiến bộ siêu nhanh rồi! 📚",
                'curious'  => "Tiết học đầu tiên chúng mình cùng hoàn thành là một cột mốc đặc biệt. Từ lúc đó hành trình đã mở ra! 🔍",
                'cheerful' => "Tiết học đầu tiên bên nhau! Cậu nhớ lúc hai đứa cùng hoàn thành nó không? Tuyệt vời lắm luôn! ☀️",
                'calm'     => "Vạn dặm bắt đầu từ một bước chân. Bài học đầu tiên ấy đã mở ra cả một chân trời mới 🍵",
                default    => "Tiết học đầu tiên hai đứa cùng mở sách học là kỷ niệm mình nhớ mãi! 📚",
            };
        }

        if ($type === 'first_perfect_quiz') {
            return match ($personality) {
                'playful'  => "Ủa nhớ bài Quiz được 100% điểm tuyệt đối không ta? Lúc đó mình nhảy cẫng lên ăn mừng mém đụng trần nhà! 🎉",
                'curious'  => "Lần cậu đạt 100% trọn vẹn điểm Quiz làm mình ấn tượng mãi! Độ chính xác đỉnh thật sự! 🔍",
                'cheerful' => "Bài Quiz 100% điểm tròn trĩnh! Cậu làm bài xuất sắc làm mình tự hào muốn khoe với cả thế giới! ☀️",
                'calm'     => "Điểm tuyệt đối của bài Quiz ấy là minh chứng cho sự tập trung và thấu hiểu sâu sắc của bạn 🍵",
                default    => "Lần bạn đạt 100% điểm tuyệt đối trong bài Quiz, mình tự hào về bạn lắm! 🏆",
            };
        }

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

        if ($type === 'stage_reached') {
            $stageName = $meta['stage_name'] ?? 'Giai đoạn mới';
            $stageEmoji = $meta['emoji'] ?? '🐉';
            return "Nhìn lại lúc mình tiến hóa thành {$stageName} {$stageEmoji}, mỗi bước trưởng thành đều có dấu ấn công sức của cậu! 💖";
        }

        if ($type === 'daily_goal') {
            return "Mỗi lần thấy cậu hoàn thành Mục tiêu ngày là mình lại thấy có thêm động lực đồng hành cùng cậu! ⭐";
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
