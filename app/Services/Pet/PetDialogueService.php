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
     * Each dialogue has Chinese characters, Pinyin, and friendly Vietnamese translation.
     * Returns 12-18 dialogue strings with rich variety to prevent repetition.
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

        // 2. Absence return greeting (warm, gentle, no guilt-trip)
        $daysAbsent = $userPet->getDaysAbsent();
        if ($daysAbsent >= 1) {
            array_unshift($dialogues, $this->getAbsenceWelcome($personality, $daysAbsent, $userName));
        }

        // 3. Memory recall (remembering first word, milestones, streaks)
        $memory = $this->memoryService->recallRelevantMemory($userPet);
        if ($memory) {
            $dialogues[] = $this->memoryService->formatMemoryDialogue($memory, $personality);
        }

        // 4. Time of day greeting tailored to personality & affinity
        $dialogues[] = $this->getTimeGreeting($userPet, $personality, $userName);

        // 5. Vocabulary recall from recent word or mastered words (up to 3 words)
        if (!empty($context['recent_word'])) {
            $recentCard = \App\Models\Flashcard::where('hanzi', $context['recent_word'])->first();
            if ($recentCard) {
                $dialogues[] = $this->formatVocabDialogue($recentCard, $personality);
            }
        }

        $words = $this->masteryService->getRandomMastered($user, 3);
        if ($words->isNotEmpty()) {
            foreach ($words as $idx => $w) {
                if (empty($context['recent_word']) || $w->hanzi !== $context['recent_word']) {
                    if ($idx === 0) {
                        $dialogues[] = $this->formatVocabDialogue($w, $personality);
                    } else {
                        $dialogues[] = $this->formatSecondVocabDialogue($w, $personality);
                    }
                }
            }
        }

        // 6. Relationship & Affinity intimacy dialogue
        $dialogues[] = $this->getAffinityIntimacyDialogue($personality, $affinityTier, $userName);

        // 7. Hunger-based dialogue
        $dialogues[] = $this->getHungerDialogue($userPet, $personality);

        // 8. Stage-based motivational dialogue
        $dialogues[] = $this->getStageDialogue($userPet, $personality);

        // 9. Inspiring Chinese idioms & proverbs tailored for language learning
        $dialogues[] = $this->getProverbDialogue($personality);

        // 10. Mastery count dialogue or initial study encouragement
        $masteredCount = $this->masteryService->getMasteredCount($user);
        if ($masteredCount > 0) {
            $dialogues[] = $this->getMasteryCountDialogue($masteredCount, $personality);
        } else {
            $dialogues[] = $this->getInitialStudyEncouragement($personality);
        }

        // 11. Companion bonding & cheer lines (adds variety)
        $dialogues[] = $this->getBondingCheerDialogue($personality, $userName);

        return array_values(array_filter($dialogues));
    }

    /**
     * Get dialogues structured as associative arrays:
     * [
     *   'chinese'    => '早上好，今天一起加油！',
     *   'pinyin'     => 'Zǎoshang hǎo, jīntiān yīqǐ jiāyóu!',
     *   'vietnamese' => 'Chào buổi sáng bạn, hôm nay chúng mình cùng cố gắng nhé! ☀️',
     *   'audio_text' => '早上好，今天一起加油！',
     *   'full_text'  => '...'
     * ]
     */
    public function getRichDialogues(User $user, UserPet $userPet, array $context = []): array
    {
        $rawList = $this->getDialogues($user, $userPet, $context);
        return array_map(fn($d) => $this->parseDialogue($d), $rawList);
    }

    /**
     * Parse any raw dialogue string into structured Chinese, Pinyin, Vietnamese, and AudioText.
     */
    public function parseDialogue(string $d): array
    {
        $d = trim($d);

        // Pattern: "Chinese (Pinyin) Vietnamese" or "Emoji Chinese (Pinyin) Vietnamese"
        if (preg_match('/^([^\(\)（）]+?)\s*[\(（]([^\(\)（）]+?)[\)）]\s*(.*)$/us', $d, $matches)) {
            $chineseRaw = trim($matches[1]);
            $pinyin     = trim($matches[2]);
            $vietnamese = trim($matches[3]);

            // Strip emojis from chinese for clean speech synthesis
            $audioText = preg_replace('/[\x{1F600}-\x{1F64F}\x{1F300}-\x{1F5FF}\x{1F680}-\x{1F6FF}\x{1F1E0}-\x{1F1FF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}\x{FE00}-\x{FE0F}\x{1F900}-\x{1F9FF}\x{1F018}-\x{1F270}]/u', '', $chineseRaw);
            $audioText = trim($audioText);
            if (empty($audioText)) {
                $audioText = $chineseRaw;
            }

            return [
                'chinese'    => $chineseRaw,
                'pinyin'     => $pinyin,
                'vietnamese' => $vietnamese,
                'audio_text' => $audioText,
                'full_text'  => $d,
            ];
        }

        // Fallback: extract Chinese characters if any exist
        if (preg_match('/[\x{4e00}-\x{9fa5}]+/u', $d)) {
            preg_match_all('/[\x{4e00}-\x{9fa5}，。？！、\s]+/u', $d, $cnMatches);
            $chineseSegment = trim(implode('', $cnMatches[0] ?? []));
            return [
                'chinese'    => $chineseSegment ?: '你好！',
                'pinyin'     => '',
                'vietnamese' => $d,
                'audio_text' => $chineseSegment ?: '你好！',
                'full_text'  => $d,
            ];
        }

        return [
            'chinese'    => '你好呀！',
            'pinyin'     => 'Nǐ hǎo ya!',
            'vietnamese' => $d,
            'audio_text' => '你好呀！',
            'full_text'  => $d,
        ];
    }

    /**
     * Get a single random or most relevant dialogue for quick display (Floating widget / Hero card).
     */
    public function getRandomDialogue(User $user, UserPet $userPet, array $context = []): string
    {
        $dialogues = $this->getDialogues($user, $userPet, $context);
        if (empty($dialogues)) {
            return "早上好，今天一起学中文吧！(Zǎoshang hǎo, jīntiān yīqǐ xué zhōngwén ba!) Cùng học tiếng Trung chăm chỉ nhé! 🇨🇳";
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

        if ($hour < 6) {
            return match ($personality) {
                'playful'  => "夜深了，早点休息哦！(Yè shēn le, zǎodiǎn xiūxi ó!) Oa oa, muộn thế này mà {$userName} còn thức sao? Nhớ đi ngủ sớm giữ gìn mắt nha! 🌙",
                'shy'      => "夜深了，别熬夜太晚哦。(Yè shēn le, bié áoyè tài wǎn ó.) Khuya lắm rồi... {$userName} đừng thức khuya quá kẻo mệt nhé 🌸",
                'curious'  => "夜深人静，注意劳逸结合。(Yè shēn rén jìng, zhùyì láoyì jiéhé.) Trời chưa sáng mà {$userName} đã chăm chỉ rồi! Nhưng nhớ ngủ đủ giấc nhé 🔍",
                'cheerful' => "辛苦啦，好好休息充满电！(Xīnkǔ la, hǎohǎo xiūxi chōngmǎn diàn!) Tinh thần học đêm của {$userName} đỉnh quá! Nhưng nhớ nghỉ ngơi để nạp năng lượng nha ☀️",
                default    => "夜阑人静，身体最重要。(Yè lán rén jìng, shēntǐ zuì zhòngyào.) Đêm khuya thanh tịnh, học tập tốt nhưng sức khỏe là vàng. Ngủ sớm nhé {$userName} 🍵",
            };
        }

        if ($hour < 12) {
            return match ($personality) {
                'playful'  => "早上好！今天也要元气满满哦！(Zǎoshang hǎo! Jīntiān yě yào yuánqì mǎnmǎn ó!) Chào buổi sáng {$userName}! Hôm nay quẩy bài học nào đây? Nhanh tay nào! ✨",
                'shy'      => "早上好，祝你今天学习顺利。(Zǎoshang hǎo, zhù nǐ jīntiān xuéxí shùnlì.) Chào buổi sáng... Chúc {$userName} một ngày học tập thật nhẹ nhàng và vui vẻ nhé 🌸",
                'curious'  => "早安！今天我们一起发现新汉字。(Zǎo'ān! Jīntiān wǒmen yīqǐ fāxiàn xīn hànzì.) 早安! Hôm nay chúng mình sẽ khám phá những chữ Hán thú vị nào đây {$userName}? 🔍",
                'cheerful' => "早上好！新的一天冲呀！(Zǎoshang hǎo! Xīn de yī tiān chōng ya!) Chào buổi sáng tràn đầy năng lượng! Cùng bắt đầu ngày mới rực rỡ nào {$userName}! ☀️",
                default    => "一日之计在于晨，我们开始吧。(Yī rì zhī jì zàiyú chén, wǒmen kāishǐ ba.) Chào buổi sáng. Một ngày mới bắt đầu bằng sự tập trung sẽ mang lại kết quả tuyệt vời 🍵",
            };
        }

        if ($hour < 14) {
            return match ($personality) {
                'playful'  => "中午好，吃饱饭才有力气背单词！(Zhōngwǔ hǎo, chī bǎo fàn cái yǒu lìqi bèi dāncí!) Buổi trưa no nê chưa nè? Học vài thẻ từ vựng cho tiêu cơm nha {$userName}! 🍖",
                'shy'      => "中午好，你吃午饭了吗？(Zhōngwǔ hǎo, nǐ chī wǔfàn le ma?) Buổi trưa rồi, {$userName} đã ăn cơm chưa? Đừng để bụng đói học bài nhé 🌸",
                'curious'  => "午休时间，来复习几个生词吧。(Wǔxiū shíjiān, lái fùxí jǐ gè shēngcí ba.) Tranh thủ giờ nghỉ trưa nạp vài từ mới là trí nhớ tăng vọt đấy {$userName} 🔍",
                'cheerful' => "午安！轻松一下继续学！(Wǔ'ān! Qīngsōng yīxià jìxù xué!) Buổi trưa vui vẻ! Vừa thư giãn vừa lướt vài chữ Hán nào {$userName} ☀️",
                default    => "午间小憩，温故而知新。(Wǔjiān xiǎo qì, wēngù ér zhī xīn.) Buổi trưa an lành. Thong thả ôn lại bài học trước khi bắt đầu buổi chiều 🍵",
            };
        }

        if ($hour < 18) {
            return match ($personality) {
                'playful'  => "下午好，精神抖擞继续冲！(Xiàwǔ hǎo, jīngshén dǒusǒu jìxù chōng!) Buổi chiều mát mẻ! Tăng tốc luyện phản xạ thôi {$userName} ơi! 🚀",
                'shy'      => "下午好，我们慢慢复习吧。(Xiàwǔ hǎo, wǒmen mànman fùxí ba.) Chiều rồi... Mình cùng nhau ôn lại bài một chút nhé {$userName} 🌸",
                'curious'  => "下午时间，读篇短文开阔视野。(Xiàwǔ shíjiān, dú piān duǎnwén kāikuò shìyě.) Chiều nay học thêm bài đọc mới để hiểu văn hóa Trung Hoa nhé {$userName} 🔍",
                'cheerful' => "下午状态大好，胜利就在眼前！(Xiàwǔ zhuàngtài dà hǎo, shènglì jiù zài yǎnqián!) Năng lượng buổi chiều vẫn dồi dào! Tiếp tục tiến bước nào {$userName}! ☀️",
                default    => "夕阳西下，正是静心好时候。(Xīyáng xī xià, zhèng shì jìngxīn hǎo shíhou.) Ánh chiều êm dịu, rất thích hợp để tĩnh tâm luyện viết và nhớ mặt chữ 🍵",
            };
        }

        if ($hour < 22) {
            return match ($personality) {
                'playful'  => "晚上好，今天目标搞定了吗？(Wǎnshang hǎo, jīntiān mùbiāo gǎodìng le ma?) Buổi tối đến rồi! Ôn tập hoàn thành mục tiêu ngày để tớ ăn mừng nào! 🎉",
                'shy'      => "晚上好，喜欢陪你学中文的时光。(Wǎnshang hǎo, xǐhuan péi nǐ xué zhōngwén de shíguāng.) Buổi tối ấm áp bên bàn học... Tớ luôn thích giờ học tối cùng {$userName} 🌸",
                'curious'  => "晚上好，回顾一下今天的收获吧。(Wǎnshang hǎo, huígù yīxià jīntiān de shōuhuò ba.) Tối nay mình cùng củng cố lại các từ đã học trong ngày hôm nay nhé 🔍",
                'cheerful' => "晚上好！今天也是棒棒的一天！(Wǎnshang hǎo! Jīntiān yě shì bàngbàng de yī tiān!) Hoàn thành bài học tối nay là hoàn hảo luôn! Cố lên {$userName}! ☀️",
                default    => "晚风习习，温习乃进步之本。(Wǎnfēng xíxí, wēnxí nǎi jìnbù zhī běn.) Buổi tối là lúc củng cố kiến thức tốt nhất. Từng bước vững chắc mỗi ngày 🍵",
            };
        }

        return match ($personality) {
            'playful'  => "快半夜啦，学完这课就呼呼大睡！(Kuài bànyè la, xué wán zhè kè jiù hūhū dà shuì!) Sắp nửa đêm rồi nè! Nốt một bài rồi mình đi ngủ thật ngon nhé!",
            'shy'      => "明天见，今晚做个甜甜的梦。(Míngtiān jiàn, jīnwǎn zuò gè tiántián de mèng.) Sắp sang ngày mới rồi... {$userName} nghỉ ngơi sớm đi nhé 🌸",
            'curious'  => "睡前加深记忆，明天更牢固。(Shuì qián jiāshēn jìyì, míngtiān gèng láogù.) Đêm đã sâu, ôn lại chút trước khi ngủ sẽ giúp não bộ ghi nhớ lâu hơn đó 🔍",
            'cheerful' => "今天辛苦啦，明天我们继续冲！(Jīntiān xīnkǔ la, míngtiān wǒmen jìxù chōng!) Bạn đã nỗ lực cả ngày rồi! Chuẩn bị nghỉ ngơi thật ngon nhé {$userName}! ☀️",
            default    => "养精蓄锐，明日方能更进一竿。(Yǎng jīng xù ruì, míngrì fāng néng gèng jìn yī gān.) Ngày sắp khép lại. Nghỉ ngơi tốt để mai lại đón nhận kiến thức mới 🍵",
        };
    }

    protected function getStruggleEncouragement(string $personality, string $userName): string
    {
        return match ($personality) {
            'playful'  => "没关系，再试一次就记住啦！(Méiguānxi, zài shì yīcì jiù jìzhù la!) Không sao đâu nè! Sai là chuyện bình thường mà, thử lại lần nữa là nhớ ngay thôi! 🎮",
            'curious'  => "仔细看偏旁部首，答案就藏在里面。(Zǐxì kàn piānpáng bùshǒu, dá'àn jiù cáng zài lǐmiàn.) Chữ này hơi lắt léo đúng không? Để ý kỹ bộ thủ một chút là bạn sẽ giải mã được ngay! 🔍",
            'shy'      => "别难过，我会一直陪着你的。(Bié nánguò, wǒ huì yīzhí péizhe nǐ de.) Đừng buồn nhé... Tớ biết chữ này khó, tớ sẽ luôn đồng hành cùng bạn mà 🌸",
            'cheerful' => "失败是成功之母，下一次一定行！(Shībài shì chénggōng zhī mǔ, xià yīcì yīdìng xíng!) Không bỏ cuộc là bạn đã chiến thắng rồi! Lần tới chắc chắn {$userName} sẽ làm đúng! ☀️",
            'calm'     => "万事开头难，静下心来多读几遍。(Wànshì kāitóu nán, jìng xià xīn lái duō dú jǐ biàn.) Vạn sự khởi đầu nan. Người học giỏi là người kiên nhẫn vượt qua những lần vấp ngã 🍵",
            default    => "加油，一步一步来，你一定能掌握！(Jiāyóu, yībù yībù lái, nǐ yīdìng néng zhǎngwò!) Cố lên bạn ơi! Từng bước một, bạn nhất định sẽ nhớ được! 💪",
        };
    }

    protected function getStreakEncouragement(string $personality, int $streak, string $userName): string
    {
        return match ($personality) {
            'playful'  => "连续{$streak}天打卡！太神仙了吧！(Liánxù {$streak} tiān dǎkǎ! Tài shénxiān le ba!) Chuỗi {$streak} ngày liên tiếp! {$userName} đỉnh quá xá, giữ lửa thôi nào! 🔥",
            'curious'  => "坚持{$streak}天，量变正在引起质变！(Jiānchí {$streak} tiān, liàngbiàn zhèngzài yǐnqǐ zhìbiàn!) {$streak} ngày bền bỉ! Sự kiên định này đang tạo nên bước nhảy vọt đó {$userName}! 🔍",
            'shy'      => "连续学了{$streak}天，你真的太了不起了。(Liánxù xué le {$streak} tiān, nǐ zhēnde tài liǎobùqǐ le.) Nhìn {$userName} học liên tục {$streak} ngày, tớ ngưỡng mộ bạn thật sự... 🌸",
            'cheerful' => "太棒了！{$streak}天连胜势不可挡！(Tài bàng le! {$streak} tiān liánshèng shì bù kě dǎng!) Chuỗi {$streak} ngày rực lửa! Phong độ của {$userName} không ai cản nổi rồi! ☀️",
            'calm'     => "持之以恒，{$streak}天的积累功不唐捐。(Chízhīyǐhéng, {$streak} tiān de jīlěi gōng bù táng juān.) {$streak} ngày liên tục. Thói quen bền bỉ hơn cả sự thông minh, tiếp tục duy trì nhé 🍵",
            default    => "连续{$streak}天打卡，太厉害啦！(Liánxù {$streak} tiān dǎkǎ, tài lìhai la!) Chuỗi {$streak} ngày học chăm chỉ! Tuyệt vời lắm! 🔥",
        };
    }

    protected function getActivityReaction(string $activity, string $personality): string
    {
        if (str_contains($activity, 'flashcard')) {
            return match ($personality) {
                'playful'  => "翻卡片就像寻宝，看谁找得准！(Fān kǎpiàn jiù xiàng xúnbǎo, kàn shéi zhǎo de zhǔn!) Lật thẻ thật nhanh, đoán đúng thật siêu! Xem ai nhớ lâu hơn nào! 🃏",
                'curious'  => "卡片艾宾浩斯记忆法，科学高效！(Kǎpiàn Àibīnhàosī jìyì fǎ, kēxué gāoxiào!) Flashcard là cách tốt nhất để kích hoạt trí nhớ dài hạn đó! 🧠",
                'shy'      => "一张一张慢慢翻，不着急哦。(Yī zhāng yī zhāng mànman fān, bù zhāojí ó.) Tớ sẽ lật thẻ cùng bạn, đừng vội nhé cứ từ từ suy nghĩ 🌸",
                'cheerful' => "每翻一张卡片，离HSK就更近一步！(Měi fān yī zhāng kǎpiàn, lí HSK jiù gèng jìn yībù!) Mỗi tấm thẻ lật mở là một bước tiến mới đến HSK! ☀️",
                default    => "一字一句，铭记于心。(Yī zì yī jù, míngjì yú xīn.) Tập trung vào âm thanh và ý nghĩa, bạn sẽ nhớ rất sâu 🍵",
            };
        }

        if (str_contains($activity, 'quiz')) {
            return match ($personality) {
                'playful'  => "答题时间到！全部选对有惊喜哦！(Dátí shíjiān dào! Quánbù xuǎn duì yǒu jīngxǐ ó!) Quiz time! Hãy chọn đáp án chính xác và rinh điểm cao về cho tớ nhé! 🎯",
                'curious'  => "选择题能检验知识盲区，认真作答。(Xuǎnzétí néng jiǎnyàn zhīshi mángqū, rènzhēn zuòdá.) Câu hỏi trắc nghiệm sẽ giúp bạn nhận diện các bẫy ngữ pháp thường gặp 🔍",
                'shy'      => "深呼吸，认真读题你一定懂的。(Shēn hūxī, rènzhēn dú tí nǐ yīdìng dǒng de.) Hít một hơi thật sâu, đọc kỹ đề bài nhé bạn 🌸",
                'cheerful' => "自信出击！你的实力超强的！(Zìxìn chūjī! Nǐ de shílì chāo qiáng de!) Tự tin lên nào! Bạn đã ôn tập rất kỹ rồi mà! ☀️",
                default    => "审慎思考，择优而答。(Shěnshèn sīkǎo, zé yōu ér dá.) Đọc kỹ văn cảnh, đáp án đúng sẽ tự hiện ra 🍵",
            };
        }

        return "我们一起好好学习吧！(Wǒmen yīqǐ hǎohǎo xuéxí ba!) Cùng học chăm chỉ nhé! ✨";
    }

    protected function getAbsenceWelcome(string $personality, int $daysAbsent, string $userName): string
    {
        if ($daysAbsent >= 7) {
            return match ($personality) {
                'playful'  => "❤️ 你回来了！(Nǐ huílái le!) ({$userName} về rồi!) Mình chờ cậu mãi đấy, chúng mình lại cùng chơi và học nhé! 🐉✨",
                'shy'      => "❤️ 你回来了！(Nǐ huílái le!) ({$userName} đã về rồi...) Mấy ngày qua mình luôn ngóng ra cửa. Thật may vì cậu đã quay lại 🌸",
                'curious'  => "❤️ 你回来了！(Nǐ huílái le!) ({$userName} về rồi!) Cậu có khỏe không? Mình đã giữ gìn các trang sách thật cẩn thận chờ cậu đấy! 🔍",
                'cheerful' => "❤️ 你回来了！(Nǐ huílái le!) Hoan hô! {$userName} đã trở về rồi! Cuộc phiêu lưu của hai chúng ta lại tiếp tục rồi! ☀️🐉",
                'calm'     => "❤️ 你回来了。(Nǐ huílái le.) (Bạn đã về rồi.) Chặng đường dài luôn có những khoảng lặng, chỉ cần quay lại là đáng quý 🍵",
                default    => "❤️ 你回来了！(Nǐ huílái le!) (Cậu về rồi!) Thật vui vì lại được đồng hành cùng bạn bên bàn học! ✨",
            };
        }

        if ($daysAbsent >= 2) {
            return match ($personality) {
                'playful'  => "好久不见……(Hǎojiǔ bùjiàn……) (Lâu rồi mới gặp lại cậu……) Cậu có nhớ chú rồng nhỏ này không nè? 🐲",
                'shy'      => "好久不见……(Hǎojiǔ bùjiàn……) (Lâu rồi mới gặp lại cậu……) Cậu vắng bóng mấy hôm, mình thấy hơi trống trải... 🌸",
                'curious'  => "好久不见……(Hǎojiǔ bùjiàn……) (Lâu rồi mới gặp lại cậu……) Mình đã chuẩn bị sẵn vài chữ Hán thú vị chờ cậu rồi đấy! 🔍",
                'cheerful' => "好久不见……(Hǎojiǔ bùjiàn……) (Lâu rồi mới gặp lại cậu……) Chào mừng cậu đã trở lại với nguồn năng lượng mới! ☀️",
                'calm'     => "好久不见。(Hǎojiǔ bùjiàn.) (Lâu rồi mới gặp lại bạn.) Tĩnh tâm lại một chút rồi chúng mình cùng tiếp tục nhé 🍵",
                default    => "好久不见……(Hǎojiǔ bùjiàn……) (Lâu rồi mới gặp lại cậu……) Mình vẫn luôn ở đây đồng hành cùng bạn 📚",
            };
        }

        // Vắng 1 ngày
        return match ($personality) {
            'playful'  => "今天你很忙吗？(Jīntiān nǐ hěn máng ma?) (Hôm nay cậu bận rộn nhiều à?) Dành vài phút cùng mình thư giãn nhé! 🎮",
            'shy'      => "今天你很忙吗？(Jīntiān nǐ hěn máng ma?) (Hôm nay cậu bận lắm phải không?) Nhớ giữ gìn sức khỏe và nghỉ ngơi đủ nhé 🌸",
            'curious'  => "今天你很忙吗？(Jīntiān nǐ hěn máng ma?) (Hôm nay cậu bận à?) Dù bận nhưng cậu vẫn ghé qua thăm mình, vui quá! 🔍",
            'cheerful' => "今天你很忙吗？(Jīntiān nǐ hěn máng ma?) (Hôm nay cậu bận à?) Một nụ cười xua tan mệt mỏi, chúng mình lại bên nhau rồi! ☀️",
            'calm'     => "今天你很忙吗？(Jīntiān nǐ hěn máng ma?) (Hôm nay cậu bận à?) Ngày bận rộn cũng cần những phút giây tĩnh lặng 🍵",
            default    => "今天你很忙吗？(Jīntiān nǐ hěn máng ma?) (Hôm nay cậu bận à?) Dành chút thời gian lật vài thẻ bài cùng tớ nhé! 📚",
        };
    }

    protected function formatVocabDialogue(object $word, string $personality): string
    {
        return match ($personality) {
            'playful'  => "「{$word->hanzi}」这个词你记得很牢呢！(「{$word->hanzi}」zhège cí nǐ jì de hěn láo ne!) Đố bạn: từ 「{$word->hanzi}」({$word->pinyin}) nghĩa là gì nè? Đúng rồi, là '{$word->meaning}' đó! Siêu ghê! ✨",
            'curious'  => "「{$word->hanzi}」这个词很有意思！(「{$word->hanzi}」zhège cí hěn yǒu yìsi!) Bạn có để ý từ 「{$word->hanzi}」({$word->pinyin}) mang nghĩa '{$word->meaning}' không? Chữ này dùng rất nhiều trong thực tế đó! 🔍",
            'shy'      => "「{$word->hanzi}」你的发音真标准。(「{$word->hanzi}」nǐ de fāyīn zhēn biāozhǔn.) Tớ vẫn nhớ hôm bạn học từ 「{$word->hanzi}」('{$word->meaning}'). Lúc đó bạn đọc phát âm rất chuẩn 🌸",
            'cheerful' => "你已经完全掌握「{$word->hanzi}」了！(Nǐ yǐjīng wánquán zhǎngwò 「{$word->hanzi}」le!) Bạn đã hoàn toàn làm chủ từ 「{$word->hanzi}」('{$word->meaning}') rồi đó! Quá xuất sắc! ☀️",
            default    => "词汇「{$word->hanzi}」，温故而知新。(Cíhuì 「{$word->hanzi}」, wēngù ér zhī xīn.) Từ 「{$word->hanzi}」({$word->pinyin}) - '{$word->meaning}' đã nằm trọn trong trí nhớ của bạn 🍵",
        };
    }

    protected function formatSecondVocabDialogue(object $word, string $personality): string
    {
        return match ($personality) {
            'playful'  => "「{$word->hanzi}」你也烂熟于心啦！(「{$word->hanzi}」nǐ yě làn shú yú xīn la!) Chưa hết đâu nha, cả từ 「{$word->hanzi}」({$word->pinyin}) bạn cũng thuộc làu làu luôn rồi nè! 🌟",
            'curious'  => "「{$word->hanzi}」能组成很多漂亮的句子呢。(「{$word->hanzi}」néng zǔchéng hěn duō piàoliang de jùzi ne.) Chữ 「{$word->hanzi}」('{$word->meaning}') này kết hợp với các từ khác tạo ra nhiều câu hay lắm đấy! 🔍",
            'shy'      => "继续保持，「{$word->hanzi}」学得很好！(Jìxù bǎochí, 「{$word->hanzi}」xué de hěn hǎo!) Từ 「{$word->hanzi}」này cũng rất hay, bạn nhớ giữ gìn phong độ này nhé 🌸",
            'cheerful' => "词汇量又增加了，太棒了！(Cíhuì liàng yòu zēngjiā le, tài bàng le!) Lại thêm từ 「{$word->hanzi}」nữa! Vốn từ vựng của bạn ngày càng phong phú rồi! ☀️",
            default    => "不积小流，无以成江海。(Bù jī xiǎo liú, wú yǐ chéng jiāng hǎi.) Kiến thức về từ 「{$word->hanzi}」rất vững chắc. Hãy tiếp tục bồi đắp mỗi ngày 🍵",
        };
    }

    protected function getAffinityIntimacyDialogue(string $personality, string $tier, string $userName): string
    {
        return match ($tier) {
            'partner' => match ($personality) {
                'playful'  => "我们是无敌的默契搭档！(Wǒmen shì wúdí de mòqì dādàng!) Hai đứa mình giờ là cặp đôi tri kỷ vô địch rồi đó {$userName}! Không gì ngăn nổi chúng mình đâu! 🐉",
                'shy'      => "能成为你的知己，我好幸福。(Néng chéngwéi nǐ de zhījǐ, wǒ hǎo xìngfú.) Được làm bạn đồng hành trọn đời cùng {$userName}, tớ thấy mình là chú pet hạnh phúc nhất trần đời 🌸",
                'curious'  => "我们一起走过了奇妙的旅程！(Wǒmen yīqǐ zǒu guò le qímiào de lǚchéng!) Chúng mình đã cùng nhau đi một chặng đường thật kỳ diệu. Tớ mong sẽ cùng {$userName} đạt đến HSK cao nhất! 🔍",
                'cheerful' => "你是我最好的知己伙伴！(Nǐ shì wǒ zuì hǎo de zhījǐ huǒbàn!) Bạn là tri kỷ tuyệt vời nhất của tớ! Tớ sẽ luôn sát cánh bên bạn dù có khó khăn gì! ☀️",
                default    => "得一知己，如沐春风。(Dé yī zhījǐ, rú mù chūnfēng.) Mối lương duyên học tập này thật đáng trân quý. Cảm ơn bạn đã luôn coi tớ là tri kỷ 🍵",
            },
            'close_friend' => match ($personality) {
                'playful'  => "我们的关系越来越好啦！(Wǒmen de guānxì yuè lái yuè hǎo la!) Chúng mình thân nhau quá rồi nè, hôm nào cùng nhau ăn mừng một bữa lớn nha {$userName}! 💖",
                'shy'      => "和你说话感觉像家人一样亲近。(Hé nǐ shuōhuà gǎnjué xiàng jiārén yīyàng qīnjìn.) Mỗi lần nói chuyện cùng {$userName}, tớ cảm thấy thân thuộc như người trong nhà vậy 🌸",
                'curious'  => "越来越了解你了，最佳拍档！(Yuè lái yuè liǎojiě nǐ le, zuì jiā pāidàng!) Càng học cùng bạn tớ càng thấy hiểu bạn hơn. Bạn là người bạn học tuyệt vời nhất! 🔍",
                'cheerful' => "友谊第一，越来越喜欢你了！(Yǒuyì dì yī, yuè lái yuè xǐhuan nǐ le!) Tình bạn của chúng mình ngày càng khăng khít rồi! Yêu bạn nhiều lắm {$userName}! ☀️",
                default    => "相知相伴，岁岁年年。(Xiāngzhī xiāngbàn, suìsuì niánnián.) Tình bạn được xây dựng qua từng ngày kiên trì là tình bạn bền vững nhất 🍵",
            },
            'companion' => match ($personality) {
                'playful'  => "想随时都跟着你一起学！(Xiǎng suíshí dōu gēnzhe nǐ yīqǐ xué!) Bây giờ đi đâu tớ cũng muốn đi cùng {$userName} hết á! Đồng đội tốt của tớ! ⭐",
                'shy'      => "有你在，什么难题都不怕了。(Yǒu nǐ zài, shénme nántí dōu bù pà le.) Có bạn bên cạnh, tớ không còn thấy sợ những bài học khó nữa 🌸",
                'curious'  => "我们配合得天衣无缝！(Wǒmen pèihé de tiānyīwúfèng!) Chúng mình phối hợp ăn ý lắm đó! Cùng nhau chinh phục tiếp nào! 🔍",
                'cheerful' => "第一号学伴就是你啦！(Dì yī hào xuébàn jiù shì nǐ la!) Người bạn đồng hành số 1 của tớ ơi, cùng tỏa sáng nhé! ☀️",
                default    => "志同道合，携手共进。(Zhìtóngdàohé, xiéshǒu gòngjìn.) Đồng hành cùng người có chí tiến thủ như bạn là vinh hạnh của tớ 🍵",
            },
            'familiar' => match ($personality) {
                'playful'  => "一眼就能看出你今天开不开心！(Yī yǎn jiù néng kàn chū nǐ jīntiān kāi bù kāixīn!) Quen nhau lâu rồi nên tớ nhìn là biết hôm nay bạn có chăm học hay không liền á! 🌸",
                'shy'      => "在你身边越来越自在轻松了。(Zài nǐ shēnbiān yuè lái yuè zìzài qīngsōng le.) Tớ bắt đầu cảm thấy thoải mái và tự nhiên hơn nhiều khi ở bên bạn rồi... 🌸",
                'curious'  => "我们已经很熟络了呢。(Wǒmen yǐjīng hěn shúluò le ne.) Chúng mình đã quen nhau rồi đó, bạn thích bài học nào nhất trong các bài vừa qua? 🔍",
                'cheerful' => "天天见，心情天天好！(Tiāntiān jiàn, xīnqíng tiāntiān hǎo!) Quen mặt nhau rồi nha! Mỗi ngày thấy bạn mở app là tớ vui như hội! ☀️",
                default    => "日久生温，相处渐笃。(Rì jiǔ shēng wēn, xiāngchǔ jiàn dǔ.) Sự thân quen mỗi ngày giúp việc học trở nên nhẹ nhàng như hơi thở 🍵",
            },
            'met' => match ($personality) {
                'playful'  => "我记住你的名字啦！(Wǒ jìzhù nǐ de míngzi la!) Tớ nhớ tên bạn rồi đó nha {$userName}! Mau mau học để tụi mình thân hơn nữa nè! 🌿",
                'shy'      => "叫你的名字觉得心里暖暖的。(Jiào nǐ de míngzi juéde xīn lǐ nuǎn nuǎn de.) Tớ... tớ đã bắt đầu quen với bạn rồi, gọi tên bạn thấy ấm áp ghê 🌸",
                'curious'  => "很高兴认识你！(Hěn gāoxìng rènshi nǐ!) Rất vui được làm quen với {$userName}! Bạn bắt đầu học tiếng Trung từ khi nào thế? 🔍",
                'cheerful' => "新朋友，新起点！(Xīn péngyou, xīn qǐdiǎn!) Chào người bạn mới quen của tớ! Cùng xây dựng tình bạn thật đẹp nhé! ☀️",
                default    => "初次相识，幸甚至哉。(Chūcì xiāngshí, xìng shènzhì zāi.) Vạn dặm khởi đầu từ một bước. Mối duyên làm quen này rất đáng quý 🍵",
            },
            default => match ($personality) {
                'playful'  => "多来看看我，我们快快变熟吧！(Duō lái kànkan wǒ, wǒmen kuàikuài biàn shú ba!) Tớ còn hơi bỡ ngỡ xíu, bạn cho tớ ăn với học cùng tớ nhiều hơn để tụi mình thân nha! 🌱",
                'shy'      => "有点害羞，但很想和你做朋友。(Yǒudiǎn hàixiū, dàn hěn xiǎng hé nǐ zuò péngyou.) Tớ... tớ còn hơi ngại một chút... Nhưng tớ rất muốn làm bạn với bạn 🌸",
                'curious'  => "在仔细观察你学习呢，真有趣！(Zài zǐxì guānchá nǐ xuéxí ne, zhēn yǒuqù!) Tớ đang quan sát cách bạn học tập nè, rất thú vị đó! 🌱",
                'cheerful' => "别害羞，我来做你的向导！(Bié hàixiū, wǒ lái zuò nǐ de xiàngdǎo!) Đừng ngại nhé! Tớ sẽ giúp bạn làm quen với mọi thứ ở đây! ☀️",
                default    => "初始相遇，心怀期待。(Chūshǐ xiāngyù, xīnhuái qīdài.) Mọi mối quan hệ sâu sắc đều khởi đầu bằng sự bỡ ngỡ ban đầu 🌱",
            },
        };
    }

    protected function getHungerDialogue(UserPet $userPet, string $personality): string
    {
        $state = $userPet->getHungerState();

        if ($state === 'happy') {
            return match ($personality) {
                'playful'  => "我吃得饱饱的，充满活力！(Wǒ chī de bǎobǎo de, chōngmǎn huólì!) Tớ no căng bụng rồi! Giờ tha hồ năng lượng quậy tưng bừng cùng bạn nè! 😊",
                'shy'      => "肚子饱饱的，谢谢你照顾我。(Dùzi bǎobǎo de, xièxie nǐ zhàogù wǒ.) Tớ đã no bụng rồi, cảm ơn bạn nhiều lắm nhé... Tớ vui lắm 🌸",
                'curious'  => "能量满格，大脑运转最快！(Néngliàng mǎngé, dànǎo yùnzhuǎn zuì kuài!) Đầy đủ năng lượng rồi! Não bộ hoạt động tốt nhất là khi no ấm, học tiếp thôi! 🔍",
                'cheerful' => "能量满分！无敌状态迎接挑战！(Néngliàng mǎnfēn! Wúdí zhuàngtài yíngjiē tiǎozhàn!) Năng lượng 100%! Sẵn sàng cùng bạn chinh phục mọi thử thách hôm nay! ☀️",
                default    => "衣食足而知礼节，身心俱安。(Yī shí zú ér zhī lǐjié, shēnxīn jù ān.) Thân tâm an lạc, no đủ là điều kiện tốt nhất để tiếp thu tri thức 🍵",
            };
        }

        if ($state === 'hungry') {
            return match ($personality) {
                'playful'  => "肚子咕咕叫了，快给我好吃的！(Dùzi gūgū jiào le, kuài gěi wǒ hǎochī de!) Bụng tớ đang đánh trống rột rột nè! Học nhanh rồi cho tớ mẩu bánh nhỏ nha! 🍖",
                'shy'      => "稍微有点饿了，学完喂喂我好吗？(Shāowēi yǒudiǎn è le, xué wán wèi wèi wǒ hǎo ma?) Tớ... tớ hơi đói một chút rồi. Khi nào học xong bạn cho tớ ăn nhé? 🌸",
                'curious'  => "能量提示：需要适时补充能量。(Néngliàng tíshì: xūyào shìshí bǔchōng néngliàng.) Dấu hiệu cạn năng lượng rồi! Học xong một bài là đủ điều kiện nạp năng lượng đấy! 🔍",
                'cheerful' => "虽然有点饿，但精神依然饱满！(Suīrán yǒudiǎn è, dàn jīngshén yīrán bǎomǎn!) Hơi đói rồi nhưng tinh thần vẫn cao! Học xong mình cùng ăn mừng nha! ☀️",
                default    => "略有饥意，适时进食宜于养生。(Lüè yǒu jī yì, shìshí jìnshí yí yú yǎngshēng.) Đói nhẹ giúp tinh thần tỉnh táo, nhưng đừng để bụng rỗng quá lâu bạn nhé 🍵",
            };
        }

        if ($state === 'very_hungry') {
            return match ($personality) {
                'playful'  => "救命呀！饿得前胸贴后背啦！(Jiùmìng ya! È de qiánxiōng tiē hòubèi la!) Cứu tớ với! Đói hoa cả mắt rồi nè, mau cho tớ ăn đi mà bạn ơi! 😟",
                'shy'      => "我好饿呀，不要忘记我哦……(Wǒ hǎo è ya, bùyào wàngjì wǒ ó……) Tớ đói quá rồi... bạn đừng quên tớ nhé, tớ đang chờ bạn nè 🌸",
                'curious'  => "警告：饥饿值超标，急需能量补给！(Jǐnggào: jī'è zhí chāobiāo, jíxū néngliàng bǔjǐ!) Chỉ số đói đang báo động đỏ! Cần nạp thức ăn gấp để phục hồi thể lực! 🔍",
                'cheerful' => "真的饿啦！快带好吃的来拯救我！(Zhēnde è la! Kuài dài hǎochī de lái zhěngjiù wǒ!) Đói lắm rồi đó nha! Hãy cho tớ xin chút thức ăn để tiếp tục đồng hành nào! ☀️",
                default    => "虚劳之象，亟需米粮以充之。(Xū láo zhī xiàng, jí xū mǐ liáng yǐ chōng zhī.) Cơ thể cần được bồi bổ kịp thời. Xin bạn hãy chia sẻ chút thức ăn cho tớ 🍵",
            };
        }

        if ($state === 'weak') {
            return match ($personality) {
                'playful'  => "走不动路啦，给我点吃的吧……(Zǒu bù dòng lù la, gěi wǒ diǎn chī de ba……) Tớ nằm bẹp một chỗ rồi nè... không nhấc nổi chân nữa, cho tớ ăn đi mà... 😢",
                'shy'      => "浑身没力气了，你还记得我吗……(Húnshēn méi lìqi le, nǐ hái jìde wǒ ma……) Tớ yếu quá rồi... bạn có còn cần tớ nữa không... 😢",
                'curious'  => "电量濒临休眠，需要唤醒救援。(Diànliàng bīnlín xiūmián, xūyào huànxǐng jiùyuán.) Năng lượng cạn kiệt, các chức năng sắp rơi vào trạng thái ngủ đông... 🔍",
                'cheerful' => "快来救救我，我需要你的温暖！(Kuài lái jiùjiù wǒ, wǒ xūyào nǐ de wēnnuǎn!) Tớ sắp kiệt sức rồi... Mau đánh thức tớ bằng thức ăn đi bạn ơi! ☀️",
                default    => "气力已竭，望乞怜悯照料。(Qìlì yǐ jié, wàng qǐ liánmǐn zhàoliào.) Sinh lực suy giảm nghiêm trọng. Rất mong bạn nhớ tới người bạn này 🍵",
            };
        }

        if ($state === 'dormant') {
            return "💤 我在冬眠呢……喂我吃东西唤醒我吧！(Wǒ zài dōngmián ne…… wèi wǒ chī dōngxi huànxǐng wǒ ba!) Tớ đang ngủ đông... Hãy cho ăn để đánh thức tớ dậy cùng học nhé!";
        }

        return "我在这里等你一起学习哦！(Wǒ zài zhèlǐ děng nǐ yīqǐ xuéxí ó!) Tớ đang chờ bạn cùng học bài nè! 📚";
    }

    protected function getStageDialogue(UserPet $userPet, string $personality): string
    {
        return match ($userPet->stage) {
            0 => match ($personality) {
                'playful'  => "在蛋壳里晃晃悠悠，快喂我长大！(Zài dànké lǐ huànghuàng yōuyōu, kuài wèi wǒ zhǎngdà!) Tớ đang lúc lắc trong quả trứng nè! Mau cho tớ ăn để tớ chui ra chơi cùng bạn! 🥚",
                'shy'      => "在温暖的蛋壳里悄悄听你念书。(Zài wēnnuǎn de dànké lǐ qiāoqiāo tīng nǐ niànshū.) Tớ còn đang e ấp trong vỏ trứng... Bạn chăm sóc tớ nhé 🥚",
                'curious'  => "隔着蛋壳，感知到神奇的中国汉字。(Gézhe dànké, gǎnzhī dào shénqí de zhōngguó hànzì.) Trong vỏ trứng ấm áp này, tớ đang lắng nghe bạn đọc từng chữ Hán đầu tiên 🥚",
                'cheerful' => "快破壳了！迫不及待想看看大世界！(Kuài pò ké le! Pòbùjídài xiǎng kànkan dà shìjiè!) Sắp nở rồi! Sắp được bay ra ngoài ngắm thế giới cùng bạn rồi! 🥚",
                default    => "潜龙勿用，静待破茧之时。(Qián lóng wù yòng, jìng dài pò jiǎn zhī shí.) Mọi sinh mệnh vĩ đại đều ấp ủ từ trong vỏ trứng tĩnh lặng 🥚",
            },
            1 => match ($personality) {
                'playful'  => "小短腿快步跑，跟着你学中文！(Xiǎo duǎntuǐ kuàibù pǎo, gēnzhe nǐ xué zhōngwén!) Tớ mới nở nè! Chân ngắn lạch bạch nhưng chạy theo bạn nhiệt tình luôn! 🐣",
                'shy'      => "我刚出生不久，要多多关照我哦。(Wǒ gāng chūshēng bùjiǔ, yào duōduō guānzhào wǒ ó.) Tớ còn bé xíu à... bạn nhớ che chở cho tớ nhé 🐣",
                'curious'  => "外面的汉字世界真丰富多彩！(Wàimiàn de hànzì shìjiè zhēn fēngfù duōcǎi!) Thế giới bên ngoài nhiều chữ Hán thú vị quá! Chúng mình học từ từ nha! 🐣",
                'cheerful' => "你好世界！谢谢你把我孵化出来！(Nǐ hǎo shìjiè! Xièxie nǐ bǎ wǒ fūhuà chūlái!) Chào thế giới! Cảm ơn bạn đã ấp nở tớ thành công! 🐣",
                default    => "初生之犊，万物复苏生机勃发。(Chūshēng zhī dú, wànwù fùsū shēngjī bófā.) Giai đoạn sơ sinh non nớt nhưng tràn đầy tiềm năng vô hạn 🐣",
            },
            2 => match ($personality) {
                'playful'  => "蹦蹦跳跳，我长得越来越快啦！(Bèngbèng tiàotiào, wǒ zhǎng de yuè lái yuè kuài la!) Tớ đã biết nhảy loi choi rồi nè! Càng học tớ càng lớn nhanh ghê! 🐥",
                'shy'      => "在你的爱护下，我一天天长大。(Zài nǐ de àihù xià, wǒ yī tiāntiān zhǎngdà.) Nhờ có bạn yêu thương, tớ đang lớn dần từng ngày rồi nè 🐥",
                'curious'  => "脑海里能记下的汉字越来越多啦！(Nǎohǎi lǐ néng jì xià de hànzì yuè lái yuè duō la!) Não bộ tớ ghi nhớ được nhiều từ vựng hơn rồi đó! 🐥",
                'cheerful' => "又长大了一点，我们热情高涨！(Yòu zhǎngdà le yīdiǎn, wǒmen rèqíng gāozhǎng!) Lớn thêm một chút rồi! Tinh thần học tập ngày càng cao ngút trời! 🐥",
                default    => "春苗拔节，日有长进。(Chūnmiáo bájié, rì yǒu zhǎngjìn.) Mầm xanh đang vươn lên từng ngày nhờ sự tưới tắm của tri thức 🐥",
            },
            3 => match ($personality) {
                'playful'  => "机智的小狐狸来啦，快带我升级！(Jīzhì de xiǎo húli lái la, kuài dài wǒ shēngjí!) Tớ biến thành Cáo con lanh lợi rồi nè! Mau học thêm từ vựng để tớ hóa Sói nhé! 🦊",
                'shy'      => "在你身边，我变得越来越勇敢了。(Zài nǐ shēnbiān, wǒ biàn de yuè lái yuè yǒnggǎn le.) Tớ thấy mình dũng cảm hơn nhiều rồi khi được ở cạnh bạn 🦊",
                'curious'  => "能够理解更复杂的汉语语法结构了！(Nénggòu lǐjiě gèng fùzá de hànyǔ yǔfǎ jiégòu le!) Cáo con rất thông minh! Tớ có thể hiểu được nhiều ngữ pháp phức tạp hơn rồi! 🦊",
                'cheerful' => "第三阶段！顶峰近在眼前！(Dì sān jiēduàn! Dǐngfēng jìn zài yǎnqián!) Giai đoạn 3 rồi! Tụi mình sắp đạt tới đỉnh cao rồi đó! 🦊",
                default    => "灵敏智谋，行稳致远。(Língmǐn zhìmóu, xíng wěn zhì yuǎn.) Trí tuệ đã sắc bén như loài cáo. Hãy giữ vững sự điềm đạm 🦊",
            },
            4 => match ($personality) {
                'playful'  => "威武的狼王！再练练听力就成神龙啦！(Wēiwǔ de lángwáng! Zài liànlian tīnglì jiù chéng shénlóng la!) Sói dũng mãnh đây! Chỉ cần bạn luyện đọc và nghe thêm là tớ hóa Rồng thần luôn! 🐺",
                'shy'      => "我长大了，换我来守护你学习吧。(Wǒ zhǎngdà le, huàn wǒ lái shǒuhù nǐ xuéxí ba.) Tớ trưởng thành rồi... Giờ tớ có thể bảo vệ và tiếp sức cho bạn mọi lúc 🐺",
                'curious'  => "语感越来越好，我们多做做阅读题！(Yǔgǎn yuè lái yuè hǎo, wǒmen duō zuòzuò yuèdú tí!) Trực giác ngôn ngữ của tớ giờ rất nhạy bén! Chúng mình đọc thêm bài đọc nhé! 🐺",
                'cheerful' => "临门一脚！化龙飞天就在今天！(Lín mén yī jiǎo! Huà lóng fēitiān jiù zài jīntiān!) Một bước nữa thôi là thành Rồng thần rồi! Bứt phá ngoạn mục nào! 🐺",
                default    => "羽翼渐丰，傲啸山林之姿。(Yǔyì jiàn fēng, àoxiào shānlín zhī zī.) Sự trưởng thành vững chãi của loài sói. Đỉnh cao đang ở ngay trước mắt 🐺",
            },
            5 => match ($personality) {
                'playful'  => "神龙降临！遨游汉语世界无所不能！(Shénlóng jiànglín! Áoyóu hànyǔ shìjiè wú suǒ bù néng!) Rồng thần tối thượng xuất hiện! Bay lượn trên bầu trời tiếng Trung cùng bạn nè! 🐉",
                'shy'      => "蜕变为龙，全靠你一路的关怀照料。(Tuìbiàn wéi lóng, quán kào nǐ yīlù de guānhuái zhàoliào.) Tớ đã thành Rồng thần rồi... Tất cả đều là nhờ tấm lòng và công sức của bạn 🐉",
                'curious'  => "登峰造极，汉语言的奥秘尽收眼底！(Dēngfēngzàojí, hànyǔyán de àomì jìn shōu yǎndǐ!) Cảnh giới tối cao! Không còn bí ẩn chữ Hán nào có thể làm khó chúng mình nữa! 🐉",
                'cheerful' => "最强神龙与最佳学员，天下无双！(Zuì qiáng shénlóng yǔ zuì jiā xuéyuán, tiānxià wúshuāng!) Đỉnh nóc kịch trần! Chúng mình là bộ đôi học tập số một vũ trụ! 🐉",
                default    => "龙行天下，厚积薄发终见奇迹。(Lóng xíng tiānxià, hòujī bófā zhōng jiàn qíjì.) Hóa rồng uy nghiêm. Sự kiên trì của bạn đã tạc nên kỳ tích 🐉",
            },
            default => '请继续努力学习，我会一直陪着你。(Qǐng jìxù nǔlì xuéxí, wǒ huì yīzhí péizhe nǐ.) Hãy tiếp tục học để tớ lớn lên nhé!',
        };
    }

    protected function getProverbDialogue(string $personality): string
    {
        $proverbs = [
            [
                'cn' => '学而时习之，不亦说乎！',
                'py' => 'Xué ér shí xí zhī, bù yì yuè hū!',
                'vn' => 'Học rồi thường xuyên ôn tập, chẳng vui lắm sao! Khổng Tử dạy chúng mình đó 📚',
            ],
            [
                'cn' => '千里之行，始于足下。',
                'py' => 'Qiānlǐ zhī xíng, shǐ yú zú xià.',
                'vn' => 'Hành trình vạn dặm khởi đầu từ bước chân đầu tiên. Từng chữ Hán mỗi ngày nhé! 👣',
            ],
            [
                'cn' => '熟能生巧，勤能补拙。',
                'py' => 'Shóu néng shēng qiǎo, qín néng bǔ zhuō.',
                'vn' => 'Trăm hay không bằng tay quen, cần cù bù thông minh, cứ chăm đọc là giỏi ngay! ✨',
            ],
            [
                'cn' => '温故而知新，可以为师矣。',
                'py' => 'Wēngù ér zhī xīn, kěyǐ wéi shī yǐ.',
                'vn' => 'Ôn cũ biết mới, hôm nay chúng mình cùng lật lại bài học cũ một chút nhé! 📖',
            ],
            [
                'cn' => '读书百遍，其义自见。',
                'py' => 'Dúshū bǎi biàn, qí yì zì xiàn.',
                'vn' => 'Sách đọc trăm lần, nghĩa sẽ tự sáng tỏ. Đừng ngại đọc to câu chữ Hán lên nha! 🗣️',
            ],
        ];

        $p = $proverbs[array_rand($proverbs)];
        return "{$p['cn']}({$p['py']}) {$p['vn']}";
    }

    protected function getBondingCheerDialogue(string $personality, string $userName): string
    {
        return match ($personality) {
            'playful'  => "今天也是超喜欢你的一天！(Jīntiān yě shì chāo xǐhuan nǐ de yī tiān!) Hôm nay tớ cũng siêu yêu quý người bạn học là cậu đấy {$userName}! 🥰✨",
            'curious'  => "多问几个为什么，世界会更大哦！(Duō wèn jǐ gè wèishénme, shìjiè huì gèng dà ó!) Hãy luôn tò mò và đặt câu hỏi, chân trời chữ Hán sẽ rộng mở hơn rất nhiều! 🔍",
            'shy'      => "能默默陪在你身边，我很高兴。(Néng mòmò péi zài nǐ shēnbiān, wǒ hěn gāoxìng.) Được lặng lẽ đồng hành bên cạnh cậu, trong lòng tớ thấy vui và hạnh phúc lắm... 🌸",
            'cheerful' => "微笑面对每一个新汉字！(Wēixiào miànduì měi yī gè xīn hànzì!) Nở một nụ cười rạng rỡ và chinh phục từng chữ Hán mới nào! ☀️🎈",
            default    => "宁静致远，淡泊明志。(Níngjìng zhì yuǎn, dànbó míng zhì.) Tĩnh tâm để vươn xa, mộc mạc để sáng tỏ chí hướng. Bình thản học tập mỗi ngày 🍵",
        };
    }

    protected function getMasteryCountDialogue(int $count, string $personality): string
    {
        return match ($personality) {
            'playful'  => "掌握了{$count}个生词，太厉害啦！(Zhǎngwò le {$count} gè shēngcí, tài lìhai la!) Bạn đã bỏ túi {$count} từ tiếng Trung rồi đó! Bộ sưu tập chữ Hán xịn xò quá chừng! 🎉",
            'curious'  => "{$count}个生词的积累，词汇网络初具规模。( {$count} gè shēngcí de jīlěi, cíhuì wǎngluò chū jù guīmó.) {$count} từ vựng đã được làm chủ! Trí nhớ ngôn ngữ của bạn phát triển rất ấn tượng! 🔍",
            'shy'      => "不知不觉学了{$count}个词，你好棒。(Bùzhī bùjué xué le {$count} gè cí, nǐ hǎo bàng.) {$count} từ rồi... Nhìn bạn giỏi giang tớ tự hào và vui lây luôn 🌸",
            'cheerful' => "{$count}个汉字成就达成！掌声响起来！( {$count} gè hànzì chéngjiù dáchéng! Zhǎngshēng xiǎng qǐlái!) {$count} từ vựng đã được chinh phục! Một con số đáng kinh ngạc, vỗ tay nào! ☀️",
            default    => "积跬步以至千里，{$count}词奠定根基。(Jī kuǐbù yǐ zhì qiānlǐ, {$count} cí diàndìng gēnjī.) {$count} viên gạch tri thức đã được đặt nền móng vững chắc. Rất đáng tán dương 🍵",
        };
    }

    protected function getInitialStudyEncouragement(string $personality): string
    {
        return match ($personality) {
            'playful'  => "翻两张卡片，听你念好听的中文！(Fān liǎng zhāng kǎpiàn, tīng nǐ niàn hǎotīng de zhōngwén!) Lật ngay vài thẻ Flashcard để tớ được nghe bạn đọc tiếng Trung líu lo nào! 🃏",
            'curious'  => "汉字就像画画，快来学第一个字！(Hànzì jiù xiàng huàhuà, kuài lái xué dì yī gè zì!) Mỗi chữ Hán là một câu chuyện bằng tranh, bạn thử học từ đầu tiên xem sao nhé! 🔍",
            'shy'      => "从最简单的词开始，我陪着你呢。(Cóng zuì jiǎndān de cí kāishǐ, wǒ péizhe nǐ ne.) Bạn đừng ngại nhé, cứ bắt đầu từ những từ đơn giản nhất, tớ chờ bạn nè 🌸",
            'cheerful' => "千里之行始于足下，开启第一课！(Qiānlǐ zhī xíng shǐ yú zú xià, kāiqǐ dì yī kè!) Hành trình vạn dặm bắt đầu từ bước đầu tiên! Cùng mở bài học thôi nào! ☀️",
            default    => "不积细流无以成江海，从容起步。(Bù jī xì liú wú yǐ chéng jiāng hǎi, cóngróng qǐbù.) Vạn sự khởi đầu nan. Hãy thong thả mở bài học đầu tiên khi bạn đã sẵn sàng 🍵",
        };
    }

    /**
     * Get interaction-specific dialogue with state, context, and memory level awareness.
     */
    public function getInteractionDialogue(User $user, UserPet $userPet, string $action = 'poke', array $context = []): array
    {
        $consecutive = (int) ($context['consecutive'] ?? 1);
        $pageContext = $context['page_context'] ?? 'other';
        $stats       = $userPet->getInteractionStats();
        $pokeCount   = (int) ($stats['poke_count'] ?? 0);
        $level       = $stats['interaction_level'] ?? 'newcomer';
        $personality = $userPet->personality ?? 'playful';

        // 1. Dizzy condition: rapid consecutive pokes (>= 6)
        if ($consecutive >= 6) {
            $raw = "哎呀，头好晕呀！(Āiyā, tóu hǎo yūn ya!) Oái, hoa hết cả mắt rồi nè! Chóng mặt quá, tha cho tớ đi mà~ 😵💫";
            return array_merge($this->parseDialogue($raw), ['reaction_type' => 'dizzy']);
        }

        // 2. Annoyed / Playful consecutive pokes (>= 3)
        if ($consecutive >= 3) {
            $pool = [
                "别闹啦，快去学习！(Bié nào la, kuài qù xuéxí!) Haha đừng trêu nữa, mau tập trung học bài đi nào! 📚",
                "哈哈，好痒好痒！(Hāha, hǎo yǎng hǎo yǎng!) Haha nhột quá đi thôi! Cậu chọc lét tớ à? 😆",
                "真拿你没办法~ (Zhēn ná nǐ méi bànfǎ~) Thật là hết cách với cậu luôn á~ Chọc hoài à! 🎈",
            ];
            $raw = $pool[array_rand($pool)];
            return array_merge($this->parseDialogue($raw), ['reaction_type' => 'consecutive_annoyed']);
        }

        // 3. Repeated poke reaction (consecutive == 2)
        if ($consecutive === 2) {
            $pool = [
                "你又戳我啦……(Nǐ yòu chuō wǒ la...) Cậu lại chọc tớ nữa rồi nè... Có chuyện gì thế? 😳",
                "哎呀，怎么又来啦！(Āiyā, zěnme yòu lái la!) Oái, sao lại bấm tớ tiếp thế!",
                "别急别急，慢慢戳！(Bié jí bié jí, mànman chuō!) Từ từ thôi nào, chọc gì mà vội vàng thế!",
            ];
            $raw = $pool[array_rand($pool)];
            return array_merge($this->parseDialogue($raw), ['reaction_type' => 'consecutive_poked']);
        }

        // 4. Cuddle / Hug
        if ($action === 'cuddle') {
            $pool = [
                "抱抱！感觉好温暖呀。(Bàobào! Gǎnjué hǎo wēnnuǎn ya!) Được cậu ôm tớ thấy ấm áp và hạnh phúc lắm! 🥰",
                "好舒服，谢谢你的拥抱！(Hǎo shūfu, xièxie nǐ de yōngbào!) Thật là dễ chịu, cảm ơn cái ôm ấm lòng của bạn học nhé! ❤️",
                "有你陪着，心里超踏实。(Yǒu nǐ péizhe, xīnlǐ chāo tàshi!) Có cậu ở bên, trong lòng tớ luôn thấy bình yên và hạnh phúc 🌸",
            ];
            $raw = $pool[array_rand($pool)];
            return array_merge($this->parseDialogue($raw), ['reaction_type' => 'cuddle']);
        }

        // 5. Wake up
        if ($action === 'wake') {
            $pool = [
                "早安！我充满电啦！(Zǎo'ān! Wǒ chōngmǎn diàn la!) Oáp~ Tớ tỉnh ngủ rồi nè! Sạc đầy năng lượng để cùng cậu học bài rồi! ☀️",
                "睡醒啦，今天也一起努力！(Shuì xǐng la, jīntiān yě yīqǐ nǔlì!) Tớ đã dậy rồi, hôm nay chúng mình lại cùng nhau cố gắng nhé! 🚀",
                "揉揉眼睛，看到你真好。(Róurou yǎnjīng, kàndào nǐ zhēn hǎo.) Dụi dụi mắt một cái, mở mắt ra thấy bạn học là vui nhất trần đời! ✨",
            ];
            $raw = $pool[array_rand($pool)];
            return array_merge($this->parseDialogue($raw), ['reaction_type' => 'wake']);
        }

        // 6. Starving condition
        if ($userPet->hunger <= 20 && $userPet->isActive()) {
            $pool = [
                "肚子咕咕叫了，好饿呀……(Dùzi gūgū jiào le, hǎo è ya...) Bụng tớ đang réo ùng ục rồi nè, vào phòng cho tớ ăn chút đi mà~ 🥣",
                "没力气啦，想吃好吃的！(Méi lìqi la, xiǎng chī hǎochī de!) Hết sạch năng lượng rồi, thèm một món ngon do cậu thưởng quá! 🥺",
            ];
            $raw = $pool[array_rand($pool)];
            return array_merge($this->parseDialogue($raw), ['reaction_type' => 'hungry']);
        }

        // 7. Late night (hour >= 23 or hour < 5)
        $hour = (int) now()->format('H');
        if ($hour >= 23 || $hour < 5) {
            $pool = [
                "夜深了，注意休息哦。(Yè shēn le, zhùyì xiūxi ó.) Khuya lắm rồi... học bài xong nhớ ngủ sớm giữ gìn sức khỏe nhé, mai gặp lại! 🌙",
                "快去睡觉吧，明天见！(Kuài qù shuìjiào ba, míngtiān jiàn!) Đi ngủ thật ngon thôi nào, chúc bạn học của tớ có giấc mơ đẹp! 💤",
            ];
            $raw = $pool[array_rand($pool)];
            return array_merge($this->parseDialogue($raw), ['reaction_type' => 'night']);
        }

        // 8. Page context (35% chance if specific page)
        if (rand(1, 100) <= 35 && in_array($pageContext, ['flashcard', 'quiz', 'lesson'], true)) {
            if ($pageContext === 'flashcard') {
                $raw = "一张一张翻，把生词都记住！(Yī zhāng yī zhāng fān, bǎ shēngcí dōu jìzhù!) Lật từng tấm thẻ thật tập trung, cùng làm chủ toàn bộ chữ Hán nào! 🃏";
                return array_merge($this->parseDialogue($raw), ['reaction_type' => 'context_flashcard']);
            }
            if ($pageContext === 'quiz') {
                $raw = "仔细读题，你可以拿满分的！(Zǐxì dú tí, nǐ kěyǐ ná mǎnfēn de!) Đọc đề thật cẩn thận nha, tớ tin cậu chắc chắn sẽ đạt điểm tuyệt đối! 🎯";
                return array_merge($this->parseDialogue($raw), ['reaction_type' => 'context_quiz']);
            }
            if ($pageContext === 'lesson') {
                $raw = "循序渐进，一课一课通关！(Xúnxù jiànjìn, yī kè yī kè tōngguān!) Từng bước vững chắc, chinh phục từng bài học một cách tự tin nhé! 📖";
                return array_merge($this->parseDialogue($raw), ['reaction_type' => 'context_lesson']);
            }
        }

        // 9. Standard Poke Dialogue conditioned on Interaction Memory Tier!
        if ($level === 'soulmate') {
            $pool = [
                "你真的很喜欢戳我呀！(Nǐ zhēn de hěn xǐhuan chuō wǒ ya!) Cậu thực sự thích chọc tớ ghê á! Thôi cho cậu chọc đó, có cậu ở cạnh vui lắm~ ❤️",
                "无论什么时候，我都在你身边。(Wúlùn shénme shíhou, wǒ dōu zài nǐ shēnbiān.) Bất kể lúc nào, tớ cũng luôn ở đây đồng hành học tiếng Trung cùng cậu! 🥰",
                "我们是最好的学习搭档！(Wǒmen shì zuì hǎo de xuéxí dādàng!) Đôi bạn học tuyệt vời nhất quả đất chính là chúng mình! 🐉✨",
            ];
        } elseif ($level === 'familiar') {
            $pool = [
                "又来了…… 找我有事吗？(Yòu lái le... Zhǎo wǒ yǒu shì ma?) Lại trêu tớ rồi... Có chữ nào khó hiểu cần tớ giúp không nào? 🔍",
                "嗨！今天状态看起来不错！(Hāi! Jīntiān zhuàngtài kàn qǐlai bùcuò!) Chào cậu! Trông tinh thần học tập hôm nay của cậu đỉnh quá nè! ☀️",
                "我们已经越来越有默契了！(Wǒmen yǐjīng yuè lái yuè yǒu mòqì le!) Chúng mình ngày càng hiểu ý nhau hơn rồi đó, học tiếp thôi! 🍵",
            ];
        } else {
            // Newcomer
            $pool = [
                "哎呀，你碰我啦！(Āiyā, nǐ pèng wǒ la!) Oái, cậu vừa chạm vào tớ kìa! Chào bạn học mới nhé! ✨",
                "你好呀！今天我们学什么？(Nǐ hǎo ya! Jīntiān wǒmen xué shénme?) Xin chào! Hôm nay chúng mình sẽ cùng học nội dung gì nào? 🎈",
                "初次见面，请多关照哦！(Chūcì jiànmiàn, qǐng duō guānzhào ó!) Lần đầu đồng hành, hãy chiếu cố và giúp đỡ tớ nhiều nha! 🌸",
            ];
        }

        $raw = $pool[array_rand($pool)];
        return array_merge($this->parseDialogue($raw), ['reaction_type' => 'memory_' . $level]);
    }
}
