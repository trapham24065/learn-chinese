<?php

namespace App\Services\Pet;

use App\Models\User;
use App\Models\UserPet;
use Carbon\Carbon;

class PetDialogueService
{
    public function __construct(
        protected VocabularyMasteryService $masteryService,
        protected PetMemoryService $memoryService,
        protected PetAffinityService $affinityService,
    ) {}

    /**
     * Generate a list of contextual dialogues for the pet.
     * Incorporates personality, affinity tier, memory recall, and activity context.
     * Returns 4-7 dialogue strings.
     */
    public function getDialogues(User $user, UserPet $userPet, array $context = []): array
    {
        $dialogues = [];
        $personality = $userPet->personality ?? 'playful';
        $affinityTier = $userPet->getAffinityTier()['tier'];
        $userName = $this->getUserDisplayName($user, $affinityTier);

        // 1. Context-specific trigger (e.g. struggle, streak, activity)
        if (!empty($context['struggling'])) {
            $dialogues[] = $this->getStruggleEncouragement($personality, $userName);
        }

        if (!empty($context['streak']) && $context['streak'] >= 2) {
            $dialogues[] = $this->getStreakEncouragement($personality, (int) $context['streak'], $userName);
        }

        if (!empty($context['activity'])) {
            $dialogues[] = $this->getActivityReaction($context['activity'], $personality);
        }

        // 2. Absence return greeting (if user returned after >= 2 days)
        if ($userPet->last_studied_at && $userPet->last_studied_at->diffInDays(now()) >= 2) {
            $daysAbsent = (int) $userPet->last_studied_at->diffInDays(now());
            $dialogues[] = $this->getAbsenceWelcome($personality, $daysAbsent, $userName);
        }

        // 3. Memory recall (remembering first word, milestones, streaks)
        $memory = $this->memoryService->recallRelevantMemory($userPet);
        if ($memory) {
            $dialogues[] = $this->memoryService->formatMemoryDialogue($memory, $personality);
        }

        // 4. Time of day greeting tailored to personality & affinity
        $dialogues[] = $this->getTimeGreeting($userPet, $personality, $userName);

        // 5. Vocabulary recall from recent word or mastered words
        if (!empty($context['recent_word'])) {
            $recentCard = \App\Models\Flashcard::where('hanzi', $context['recent_word'])->first();
            if ($recentCard) {
                $dialogues[] = $this->formatVocabDialogue($recentCard, $personality);
            }
        }

        $words = $this->masteryService->getRandomMastered($user, 3);
        if ($words->isNotEmpty()) {
            foreach ($words as $w) {
                if (empty($context['recent_word']) || $w->hanzi !== $context['recent_word']) {
                    $dialogues[] = $this->formatVocabDialogue($w, $personality);
                    break;
                }
            }
        }

        // 6. Relationship & Affinity intimacy dialogue
        $dialogues[] = $this->getAffinityIntimacyDialogue($personality, $affinityTier, $userName);

        // 7. Hunger-based dialogue
        $dialogues[] = $this->getHungerDialogue($userPet, $personality);

        // 8. Stage-based motivational dialogue
        $dialogues[] = $this->getStageDialogue($userPet, $personality);

        // 9. Mastery count dialogue
        $masteredCount = $this->masteryService->getMasteredCount($user);
        if ($masteredCount > 0) {
            $dialogues[] = $this->getMasteryCountDialogue($masteredCount, $personality);
        } else {
            $dialogues[] = $this->getInitialStudyEncouragement($personality);
        }

        return array_values(array_filter($dialogues));
    }

    /**
     * Get a single random or most relevant dialogue for quick display (Floating widget / Hero card).
     */
    public function getRandomDialogue(User $user, UserPet $userPet, array $context = []): string
    {
        $dialogues = $this->getDialogues($user, $userPet, $context);
        if (empty($dialogues)) {
            return "Cùng học tiếng Trung chăm chỉ nhé! 🇨🇳";
        }

        return $dialogues[array_rand($dialogues)];
    }

    /**
     * Friendly name addressing based on relationship tier.
     */
    protected function getUserDisplayName(User $user, string $tier): string
    {
        if (in_array($tier, ['stranger'], true)) {
            return 'bạn';
        }

        $name = trim($user->name ?? '');
        if ($name !== '') {
            $parts = explode(' ', $name);
            return end($parts);
        }

        return 'bạn';
    }

    protected function getTimeGreeting(UserPet $userPet, string $personality, string $userName): string
    {
        $hour  = (int) Carbon::now(config('app.timezone', 'Asia/Ho_Chi_Minh'))->format('H');
        $emoji = optional($userPet->pet->stages->where('stage', $userPet->stage)->first())->emoji ?? '🥚';

        if ($hour < 6) {
            return match ($personality) {
                'playful'  => "{$emoji} Oa oa, muộn thế này mà {$userName} còn thức sao? Nhớ đi ngủ sớm giữ gìn mắt nha!",
                'shy'      => "{$emoji} Khuya lắm rồi... {$userName} đừng thức khuya quá kẻo mệt nhé 🌸",
                'curious'  => "{$emoji} Trời chưa sáng mà {$userName} đã chăm chỉ rồi! Nhưng nhớ ngủ đủ giấc nhé 🔍",
                'cheerful' => "{$emoji} Tinh thần học đêm của {$userName} đỉnh quá! Nhưng nhớ nghỉ ngơi để nạp năng lượng nha ☀️",
                default    => "{$emoji} Đêm khuya thanh tịnh, học tập tốt nhưng sức khỏe là vàng. Ngủ sớm nhé {$userName} 🍵",
            };
        }

        if ($hour < 12) {
            return match ($personality) {
                'playful'  => "{$emoji} Chào buổi sáng {$userName}! Hôm nay quẩy bài học nào đây? Nhanh tay nào! ✨",
                'shy'      => "{$emoji} Chào buổi sáng... Chúc {$userName} một ngày học tập thật nhẹ nhàng và vui vẻ nhé 🌸",
                'curious'  => "{$emoji} 早安! Hôm nay chúng mình sẽ khám phá những chữ Hán thú vị nào đây {$userName}? 🔍",
                'cheerful' => "{$emoji} Chào buổi sáng tràn đầy năng lượng! Cùng bắt đầu ngày mới rực rỡ nào {$userName}! ☀️",
                default    => "{$emoji} Chào buổi sáng. Một ngày mới bắt đầu bằng sự tập trung sẽ mang lại kết quả tuyệt vời 🍵",
            };
        }

        if ($hour < 14) {
            return match ($personality) {
                'playful'  => "{$emoji} Buổi trưa no nê chưa nè? Học vài thẻ từ vựng cho tiêu cơm nha {$userName}!",
                'shy'      => "{$emoji} Buổi trưa rồi, {$userName} đã ăn cơm chưa? Đừng để bụng đói học bài nhé 🌸",
                'curious'  => "{$emoji} Tranh thủ giờ nghỉ trưa nạp vài từ mới là trí nhớ tăng vọt đấy {$userName} 🔍",
                'cheerful' => "{$emoji} Buổi trưa vui vẻ! Vừa thư giãn vừa lướt vài chữ Hán nào {$userName} ☀️",
                default    => "{$emoji} Buổi trưa an lành. Thong thả ôn lại bài học trước khi bắt đầu buổi chiều 🍵",
            };
        }

        if ($hour < 18) {
            return match ($personality) {
                'playful'  => "{$emoji} Buổi chiều mát mẻ! Tăng tốc luyện phản xạ thôi {$userName} ơi! 🚀",
                'shy'      => "{$emoji} Chiều rồi... Mình cùng nhau ôn lại bài một chút nhé {$userName} 🌸",
                'curious'  => "{$emoji} Chiều nay học thêm bài đọc mới để hiểu văn hóa Trung Hoa nhé {$userName} 🔍",
                'cheerful' => "{$emoji} Năng lượng buổi chiều vẫn dồi dào! Tiếp tục tiến bước nào {$userName}! ☀️",
                default    => "{$emoji} Ánh chiều êm dịu, rất thích hợp để tĩnh tâm luyện viết và nhớ mặt chữ 🍵",
            };
        }

        if ($hour < 22) {
            return match ($personality) {
                'playful'  => "{$emoji} Buổi tối đến rồi! Ôn tập hoàn thành mục tiêu ngày để tớ ăn mừng nào! 🎉",
                'shy'      => "{$emoji} Buổi tối ấm áp bên bàn học... Tớ luôn thích giờ học tối cùng {$userName} 🌸",
                'curious'  => "{$emoji} Tối nay mình cùng củng cố lại các từ đã học trong ngày hôm nay nhé 🔍",
                'cheerful' => "{$emoji} Hoàn thành bài học tối nay là hoàn hảo luôn! Cố lên {$userName}! ☀️",
                default    => "{$emoji} Buổi tối là lúc củng cố kiến thức tốt nhất. Từng bước vững chắc mỗi ngày 🍵",
            };
        }

        return match ($personality) {
            'playful'  => "{$emoji} Sắp nửa đêm rồi nè! Nốt một bài rồi mình đi ngủ thật ngon nhé!",
            'shy'      => "{$emoji} Sắp sang ngày mới rồi... {$userName} nghỉ ngơi sớm đi nhé 🌸",
            'curious'  => "{$emoji} Đêm đã sâu, ôn lại chút trước khi ngủ sẽ giúp não bộ ghi nhớ lâu hơn đó 🔍",
            'cheerful' => "{$emoji} Bạn đã nỗ lực cả ngày rồi! Chuẩn bị nghỉ ngơi thật ngon nhé {$userName}! ☀️",
            default    => "{$emoji} Ngày sắp khép lại. Nghỉ ngơi tốt để mai lại đón nhận kiến thức mới 🍵",
        };
    }

    protected function getStruggleEncouragement(string $personality, string $userName): string
    {
        return match ($personality) {
            'playful'  => "Không sao đâu nè! Sai là chuyện bình thường mà, thử lại lần nữa là nhớ ngay thôi! 🎮",
            'curious'  => "Chữ này hơi lắt léo đúng không? Để ý kỹ bộ thủ một chút là bạn sẽ giải mã được ngay! 🔍",
            'shy'      => "Đừng buồn nhé... Tớ biết chữ này khó, tớ sẽ luôn đồng hành cùng bạn mà 🌸",
            'cheerful' => "Không bỏ cuộc là bạn đã chiến thắng rồi! Lần tới chắc chắn {$userName} sẽ làm đúng! ☀️",
            'calm'     => "Vạn sự khởi đầu nan. Người học giỏi là người kiên nhẫn vượt qua những lần vấp ngã 🍵",
            default    => "Cố lên bạn ơi! Từng bước một, bạn nhất định sẽ nhớ được! 💪",
        };
    }

    protected function getStreakEncouragement(string $personality, int $streak, string $userName): string
    {
        return match ($personality) {
            'playful'  => "Chuỗi {$streak} ngày liên tiếp! {$userName} đỉnh quá xá, giữ lửa thôi nào! 🔥",
            'curious'  => "{$streak} ngày bền bỉ! Sự kiên định này đang tạo nên bước nhảy vọt đó {$userName}! 🔍",
            'shy'      => "Nhìn {$userName} học liên tục {$streak} ngày, tớ ngưỡng mộ bạn thật sự... 🌸",
            'cheerful' => "Chuỗi {$streak} ngày rực lửa! Phong độ của {$userName} không ai cản nổi rồi! ☀️",
            'calm'     => "{$streak} ngày liên tục. Thói quen bền bỉ hơn cả sự thông minh, tiếp tục duy trì nhé 🍵",
            default    => "Chuỗi {$streak} ngày học chăm chỉ! Tuyệt vời lắm! 🔥",
        };
    }

    protected function getActivityReaction(string $activity, string $personality): string
    {
        if (str_contains($activity, 'flashcard')) {
            return match ($personality) {
                'playful'  => "Lật thẻ thật nhanh, đoán đúng thật siêu! Xem ai nhớ lâu hơn nào! 🃏",
                'curious'  => "Flashcard là cách tốt nhất để kích hoạt trí nhớ dài hạn đó! 🧠",
                'shy'      => "Tớ sẽ lật thẻ cùng bạn, đừng vội nhé cứ từ từ suy nghĩ 🌸",
                'cheerful' => "Mỗi tấm thẻ lật mở là một bước tiến mới đến HSK! ☀️",
                default    => "Tập trung vào âm thanh và ý nghĩa, bạn sẽ nhớ rất sâu 🍵",
            };
        }

        if (str_contains($activity, 'quiz')) {
            return match ($personality) {
                'playful'  => "Quiz time! Hãy chọn đáp án chính xác và rinh điểm cao về cho tớ nhé! 🎯",
                'curious'  => "Câu hỏi trắc nghiệm sẽ giúp bạn nhận diện các bẫy ngữ pháp thường gặp 🔍",
                'shy'      => "Hít một hơi thật sâu, đọc kỹ đề bài nhé bạn 🌸",
                'cheerful' => "Tự tin lên nào! Bạn đã ôn tập rất kỹ rồi mà! ☀️",
                default    => "Đọc kỹ văn cảnh, đáp án đúng sẽ tự hiện ra 🍵",
            };
        }

        return "Cùng học chăm chỉ nhé! ✨";
    }

    protected function getAbsenceWelcome(string $personality, int $daysAbsent, string $userName): string
    {
        return match ($personality) {
            'playful'  => "Oa {$userName} đã quay lại! Tớ mong bạn muốn xỉu luôn á, mau học cùng tớ đi! 🐲",
            'curious'  => "Chào mừng {$userName} trở lại! Mấy ngày qua bạn có khỏe không? Chúng mình lại tiếp tục khám phá nào! 🔍",
            'shy'      => "Bạn về rồi... Mấy ngày không thấy bạn tớ nhớ lắm. Thật vui vì lại được gặp bạn 🌸",
            'cheerful' => "Hoan hô {$userName} đã trở lại! Chuyến hành trình của chúng mình lại bắt đầu rồi! ☀️",
            default    => "Mừng bạn quay trở lại bàn học. Nghỉ ngơi lấy lại tinh thần rồi cùng bước tiếp nhé 🍵",
        };
    }

    protected function formatVocabDialogue(object $word, string $personality): string
    {
        return match ($personality) {
            'playful'  => "Đố bạn: từ 「{$word->hanzi}」({$word->pinyin}) nghĩa là gì nè? Đúng rồi, là '{$word->meaning}' đó! Siêu ghê! ✨",
            'curious'  => "Bạn có để ý từ 「{$word->hanzi}」({$word->pinyin}) mang nghĩa '{$word->meaning}' không? Chữ này dùng rất nhiều trong thực tế đó! 🔍",
            'shy'      => "Tớ vẫn nhớ hôm bạn học từ 「{$word->hanzi}」('{$word->meaning}'). Lúc đó bạn đọc phát âm rất chuẩn 🌸",
            'cheerful' => "Bạn đã hoàn toàn làm chủ từ 「{$word->hanzi}」('{$word->meaning}') rồi đó! Quá xuất sắc! ☀️",
            default    => "Từ 「{$word->hanzi}」({$word->pinyin}) - '{$word->meaning}' đã nằm trọn trong trí nhớ của bạn 🍵",
        };
    }

    protected function formatSecondVocabDialogue(object $word, string $personality): string
    {
        return match ($personality) {
            'playful'  => "Chưa hết đâu nha, cả từ 「{$word->hanzi}」({$word->pinyin}) bạn cũng thuộc làu làu luôn rồi nè! 🌟",
            'curious'  => "Chữ 「{$word->hanzi}」('{$word->meaning}') này kết hợp với các từ khác tạo ra nhiều câu hay lắm đấy! 🔍",
            'shy'      => "Từ 「{$word->hanzi}」này cũng rất hay, bạn nhớ giữ gìn phong độ này nhé 🌸",
            'cheerful' => "Lại thêm từ 「{$word->hanzi}」nữa! Vốn từ vựng của bạn ngày càng phong phú rồi! ☀️",
            default    => "Kiến thức về từ 「{$word->hanzi}」rất vững chắc. Hãy tiếp tục bồi đắp mỗi ngày 🍵",
        };
    }

    protected function getAffinityIntimacyDialogue(string $personality, string $tier, string $userName): string
    {
        return match ($tier) {
            'partner' => match ($personality) {
                'playful'  => "Hai đứa mình giờ là cặp đôi tri kỷ vô địch rồi đó {$userName}! Không gì ngăn nổi chúng mình đâu! 🐉",
                'shy'      => "Được làm bạn đồng hành trọn đời cùng {$userName}, tớ thấy mình là chú pet hạnh phúc nhất trần đời 🌸",
                'curious'  => "Chúng mình đã cùng nhau đi một chặng đường thật kỳ diệu. Tớ mong sẽ cùng {$userName} đạt đến HSK cao nhất! 🔍",
                'cheerful' => "Bạn là tri kỷ tuyệt vời nhất của tớ! Tớ sẽ luôn sát cánh bên bạn dù có khó khăn gì! ☀️",
                default    => "Mối lương duyên học tập này thật đáng trân quý. Cảm ơn bạn đã luôn coi tớ là tri kỷ 🍵",
            },
            'close_friend' => match ($personality) {
                'playful'  => "Chúng mình thân nhau quá rồi nè, hôm nào cùng nhau ăn mừng một bữa lớn nha {$userName}! 💖",
                'shy'      => "Mỗi lần nói chuyện cùng {$userName}, tớ cảm thấy thân thuộc như người trong nhà vậy 🌸",
                'curious'  => "Càng học cùng bạn tớ càng thấy hiểu bạn hơn. Bạn là người bạn học tuyệt vời nhất! 🔍",
                'cheerful' => "Tình bạn của chúng mình ngày càng khăng khít rồi! Yêu bạn nhiều lắm {$userName}! ☀️",
                default    => "Tình bạn được xây dựng qua từng ngày kiên trì là tình bạn bền vững nhất 🍵",
            },
            'companion' => match ($personality) {
                'playful'  => "Bây giờ đi đâu tớ cũng muốn đi cùng {$userName} hết á! Đồng đội tốt của tớ! ⭐",
                'shy'      => "Có bạn bên cạnh, tớ không còn thấy sợ những bài học khó nữa 🌸",
                'curious'  => "Chúng mình phối hợp ăn ý lắm đó! Cùng nhau chinh phục tiếp nào! 🔍",
                'cheerful' => "Người bạn đồng hành số 1 của tớ ơi, cùng tỏa sáng nhé! ☀️",
                default    => "Đồng hành cùng người có chí tiến thủ như bạn là vinh hạnh của tớ 🍵",
            },
            'familiar' => match ($personality) {
                'playful'  => "Quen nhau lâu rồi nên tớ nhìn là biết hôm nay bạn có chăm học hay không liền á! 🌸",
                'shy'      => "Tớ bắt đầu cảm thấy thoải mái và tự nhiên hơn nhiều khi ở bên bạn rồi... 🌸",
                'curious'  => "Chúng mình đã quen nhau rồi đó, bạn thích bài học nào nhất trong các bài vừa qua? 🔍",
                'cheerful' => "Quen mặt nhau rồi nha! Mỗi ngày thấy bạn mở app là tớ vui như hội! ☀️",
                default    => "Sự thân quen mỗi ngày giúp việc học trở nên nhẹ nhàng như hơi thở 🍵",
            },
            'met' => match ($personality) {
                'playful'  => "Tớ nhớ tên bạn rồi đó nha {$userName}! Mau mau học để tụi mình thân hơn nữa nè! 🌿",
                'shy'      => "Tớ... tớ đã bắt đầu quen với bạn rồi, gọi tên bạn thấy ấm áp ghê 🌸",
                'curious'  => "Rất vui được làm quen với {$userName}! Bạn bắt đầu học tiếng Trung từ khi nào thế? 🔍",
                'cheerful' => "Chào người bạn mới quen của tớ! Cùng xây dựng tình bạn thật đẹp nhé! ☀️",
                default    => "Vạn dặm khởi đầu từ một bước. Mối duyên làm quen này rất đáng quý 🍵",
            },
            default => match ($personality) {
                'playful'  => "Tớ còn hơi bỡ ngỡ xíu, bạn cho tớ ăn với học cùng tớ nhiều hơn để tụi mình thân nha! 🌱",
                'shy'      => "Tớ... tớ còn hơi ngại một chút... Nhưng tớ rất muốn làm bạn với bạn 🌸",
                'curious'  => "Tớ đang quan sát cách bạn học tập nè, rất thú vị đó! 🌱",
                'cheerful' => "Đừng ngại nhé! Tớ sẽ giúp bạn làm quen với mọi thứ ở đây! ☀️",
                default    => "Mọi mối quan hệ sâu sắc đều khởi đầu bằng sự bỡ ngỡ ban đầu 🌱",
            },
        };
    }

    protected function getHungerDialogue(UserPet $userPet, string $personality): string
    {
        $state = $userPet->getHungerState();

        if ($state === 'happy') {
            return match ($personality) {
                'playful'  => "Tớ no căng bụng rồi! Giờ tha hồ năng lượng quậy tưng bừng cùng bạn nè! 😊",
                'shy'      => "Tớ đã no bụng rồi, cảm ơn bạn nhiều lắm nhé... Tớ vui lắm 🌸",
                'curious'  => "Đầy đủ năng lượng rồi! Não bộ hoạt động tốt nhất là khi no ấm, học tiếp thôi! 🔍",
                'cheerful' => "Năng lượng 100%! Sẵn sàng cùng bạn chinh phục mọi thử thách hôm nay! ☀️",
                default    => "Thân tâm an lạc, no đủ là điều kiện tốt nhất để tiếp thu tri thức 🍵",
            };
        }

        if ($state === 'hungry') {
            return match ($personality) {
                'playful'  => "Bụng tớ đang đánh trống rột rột nè! Học nhanh rồi cho tớ mẩu bánh nhỏ nha! 🍖",
                'shy'      => "Tớ... tớ hơi đói một chút rồi. Khi nào học xong bạn cho tớ ăn nhé? 🌸",
                'curious'  => "Dấu hiệu cạn năng lượng rồi! Học xong một bài là đủ điều kiện nạp năng lượng đấy! 🔍",
                'cheerful' => "Hơi đói rồi nhưng tinh thần vẫn cao! Học xong mình cùng ăn mừng nha! ☀️",
                default    => "Đói nhẹ giúp tinh thần tỉnh táo, nhưng đừng để bụng rỗng quá lâu bạn nhé 🍵",
            };
        }

        if ($state === 'very_hungry') {
            return match ($personality) {
                'playful'  => "Cứu tớ với! Đói hoa cả mắt rồi nè, mau cho tớ ăn đi mà bạn ơi! 😟",
                'shy'      => "Tớ đói quá rồi... bạn đừng quên tớ nhé, tớ đang chờ bạn nè 🌸",
                'curious'  => "Chỉ số đói đang báo động đỏ! Cần nạp thức ăn gấp để phục hồi thể lực! 🔍",
                'cheerful' => "Đói lắm rồi đó nha! Hãy cho tớ xin chút thức ăn để tiếp tục đồng hành nào! ☀️",
                default    => "Cơ thể cần được bồi bổ kịp thời. Xin bạn hãy chia sẻ chút thức ăn cho tớ 🍵",
            };
        }

        if ($state === 'weak') {
            return match ($personality) {
                'playful'  => "Tớ nằm bẹp một chỗ rồi nè... không nhấc nổi chân nữa, cho tớ ăn đi mà... 😢",
                'shy'      => "Tớ yếu quá rồi... bạn có còn cần tớ nữa không... 😢",
                'curious'  => "Năng lượng cạn kiệt, các chức năng sắp rơi vào trạng thái ngủ đông... 🔍",
                'cheerful' => "Tớ sắp kiệt sức rồi... Mau đánh thức tớ bằng thức ăn đi bạn ơi! ☀️",
                default    => "Sinh lực suy giảm nghiêm trọng. Rất mong bạn nhớ tới người bạn này 🍵",
            };
        }

        if ($state === 'dormant') {
            return "💤 Tớ đang ngủ đông... Hãy cho ăn để đánh thức tớ dậy cùng học nhé!";
        }

        return "Tớ đang chờ bạn cùng học bài nè! 📚";
    }

    protected function getStageDialogue(UserPet $userPet, string $personality): string
    {
        return match ($userPet->stage) {
            0 => match ($personality) {
                'playful'  => "Tớ đang lúc lắc trong quả trứng nè! Mau cho tớ ăn để tớ chui ra chơi cùng bạn! 🥚",
                'shy'      => "Tớ còn đang e ấp trong vỏ trứng... Bạn chăm sóc tớ nhé 🥚",
                'curious'  => "Trong vỏ trứng ấm áp này, tớ đang lắng nghe bạn đọc từng chữ Hán đầu tiên 🥚",
                'cheerful' => "Sắp nở rồi! Sắp được bay ra ngoài ngắm thế giới cùng bạn rồi! 🥚",
                default    => "Mọi sinh mệnh vĩ đại đều ấp ủ từ trong vỏ trứng tĩnh lặng 🥚",
            },
            1 => match ($personality) {
                'playful'  => "Tớ mới nở nè! Chân ngắn lạch bạch nhưng chạy theo bạn nhiệt tình luôn! 🐣",
                'shy'      => "Tớ còn bé xíu à... bạn nhớ che chở cho tớ nhé 🐣",
                'curious'  => "Thế giới bên ngoài nhiều chữ Hán thú vị quá! Chúng mình học từ từ nha! 🐣",
                'cheerful' => "Chào thế giới! Cảm ơn bạn đã ấp nở tớ thành công! 🐣",
                default    => "Giai đoạn sơ sinh non nớt nhưng tràn đầy tiềm năng vô hạn 🐣",
            },
            2 => match ($personality) {
                'playful'  => "Tớ đã biết nhảy loi choi rồi nè! Càng học tớ càng lớn nhanh ghê! 🐥",
                'shy'      => "Nhờ có bạn yêu thương, tớ đang lớn dần từng ngày rồi nè 🐥",
                'curious'  => "Bộ lông tớ đổi màu rồi! Não bộ tớ cũng ghi nhớ được nhiều hơn rồi đó! 🐥",
                'cheerful' => "Lớn thêm một chút rồi! Tinh thần học tập ngày càng cao ngút trời! 🐥",
                default    => "Mầm xanh đang vươn lên từng ngày nhờ sự tưới tắm của tri thức 🐥",
            },
            3 => match ($personality) {
                'playful'  => "Tớ biến thành Cáo con lanh lợi rồi nè! Mau học thêm từ vựng để tớ hóa Sói nhé! 🦊",
                'shy'      => "Tớ thấy mình dũng cảm hơn nhiều rồi khi được ở cạnh bạn 🦊",
                'curious'  => "Cáo con rất thông minh! Tớ có thể hiểu được nhiều ngữ pháp phức tạp hơn rồi! 🦊",
                'cheerful' => "Giai đoạn 3 rồi! Tụi mình sắp đạt tới đỉnh cao rồi đó! 🦊",
                default    => "Trí tuệ đã sắc bén như loài cáo. Hãy giữ vững sự điềm đạm 🦊",
            },
            4 => match ($personality) {
                'playful'  => "Sói dũng mãnh đây! Chỉ cần bạn luyện đọc và nghe thêm là tớ hóa Rồng thần luôn! 🐺",
                'shy'      => "Tớ trưởng thành rồi... Giờ tớ có thể bảo vệ và tiếp sức cho bạn mọi lúc 🐺",
                'curious'  => "Trực giác ngôn ngữ của tớ giờ rất nhạy bén! Chúng mình đọc thêm bài đọc nhé! 🐺",
                'cheerful' => "Một bước nữa thôi là thành Rồng thần rồi! Bứt phá ngoạn mục nào! 🐺",
                default    => "Sự trưởng thành vững chãi của loài sói. Đỉnh cao đang ở ngay trước mắt 🐺",
            },
            5 => match ($personality) {
                'playful'  => "Rồng thần tối thượng xuất hiện! Bay lượn trên bầu trời tiếng Trung cùng bạn nè! 🐉",
                'shy'      => "Tớ đã thành Rồng thần rồi... Tất cả đều là nhờ tấm lòng và công sức của bạn 🐉",
                'curious'  => "Cảnh giới tối cao! Không còn bí ẩn chữ Hán nào có thể làm khó chúng mình nữa! 🐉",
                'cheerful' => "Đỉnh nóc kịch trần! Chúng mình là bộ đôi học tập số một vũ trụ! 🐉",
                default    => "Hóa rồng uy nghiêm. Sự kiên trì của bạn đã tạc nên kỳ tích 🐉",
            },
            default => 'Hãy tiếp tục học để tớ lớn lên nhé!',
        };
    }

    protected function getMasteryCountDialogue(int $count, string $personality): string
    {
        return match ($personality) {
            'playful'  => "Bạn đã bỏ túi {$count} từ tiếng Trung rồi đó! Bộ sưu tập chữ Hán xịn xò quá chừng! 🎉",
            'curious'  => "{$count} từ vựng đã được làm chủ! Trí nhớ ngôn ngữ của bạn phát triển rất ấn tượng! 🔍",
            'shy'      => "{$count} từ rồi... Nhìn bạn giỏi giang tớ tự hào và vui lây luôn 🌸",
            'cheerful' => "{$count} từ vựng đã được chinh phục! Một con số đáng kinh ngạc, vỗ tay nào! ☀️",
            default    => "{$count} viên gạch tri thức đã được đặt nền móng vững chắc. Rất đáng tán dương 🍵",
        };
    }

    protected function getInitialStudyEncouragement(string $personality): string
    {
        return match ($personality) {
            'playful'  => "Lật ngay vài thẻ Flashcard để tớ được nghe bạn đọc tiếng Trung líu lo nào! 🃏",
            'curious'  => "Mỗi chữ Hán là một câu chuyện bằng tranh, bạn thử học từ đầu tiên xem sao nhé! 🔍",
            'shy'      => "Bạn đừng ngại nhé, cứ bắt đầu từ những từ đơn giản nhất, tớ chờ bạn nè 🌸",
            'cheerful' => "Hành trình vạn dặm bắt đầu từ bước đầu tiên! Cùng mở bài học thôi nào! ☀️",
            default    => "Vạn sự khởi đầu nan. Hãy thong thả mở bài học đầu tiên khi bạn đã sẵn sàng 🍵",
        };
    }
}
