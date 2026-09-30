<?php

namespace App\Services\Pet;

use App\Models\User;
use App\Models\UserPet;
use Carbon\Carbon;

class PetDialogueService
{
    public function __construct(
        protected VocabularyMasteryService $masteryService,
    ) {}

    /**
     * Generate a list of contextual dialogues for the pet.
     * Returns 3-5 dialogue strings.
     */
    public function getDialogues(User $user, UserPet $userPet): array
    {
        $dialogues = [];

        // 1. Greeting based on time of day
        $dialogues[] = $this->getTimeGreeting($userPet);

        // 2. Vocabulary-based dialogue (if user has mastered words)
        $words = $this->masteryService->getRandomMastered($user, 3);
        if ($words->isNotEmpty()) {
            $word = $words->first();
            $dialogues[] = "Tớ nhớ bạn đã học từ 「{$word->hanzi}」({$word->pinyin}) nghĩa là '{$word->meaning}'. Giỏi lắm! 📖";

            if ($words->count() >= 2) {
                $word2 = $words->get(1);
                $dialogues[] = "Bạn còn biết cả 「{$word2->hanzi}」({$word2->pinyin}) nữa đấy! Tiếp tục nhé 🌟";
            }
        }

        // 3. Hunger-based dialogue
        $dialogues[] = $this->getHungerDialogue($userPet);

        // 4. Motivational based on stage
        $dialogues[] = $this->getStageDialogue($userPet);

        // 5. Mastery count dialogue
        $masteredCount = $this->masteryService->getMasteredCount($user);
        if ($masteredCount > 0) {
            $dialogues[] = "Bạn đã thành thạo {$masteredCount} từ tiếng Trung rồi đó! 🎉";
        } else {
            $dialogues[] = "Hãy học flashcard để tớ được nghe bạn nói tiếng Trung nhé! 🃏";
        }

        return array_values(array_filter($dialogues));
    }

    /**
     * Get a single random or most relevant dialogue for quick display (Floating widget / Hero card).
     */
    public function getRandomDialogue(User $user, UserPet $userPet): string
    {
        $dialogues = $this->getDialogues($user, $userPet);
        if (empty($dialogues)) {
            return "Cùng học tiếng Trung chăm chỉ nhé! 🇨🇳";
        }
        return $dialogues[array_rand($dialogues)];
    }


    private function getTimeGreeting(UserPet $userPet): string
    {
        $hour  = (int) Carbon::now(config('app.timezone', 'Asia/Ho_Chi_Minh'))->format('H');
        $emoji = optional($userPet->pet->stages->where('stage', $userPet->stage)->first())->emoji ?? '🥚';

        if ($hour < 6)  return "{$emoji} Khuya rồi đấy! Ngủ đủ giấc để học tốt hơn nhé!";
        if ($hour < 12) return "{$emoji} Chào buổi sáng! Hôm nay chúng ta học gì nhé?";
        if ($hour < 14) return "{$emoji} Buổi trưa rồi! Ăn no chưa? Học thêm một chút nhé!";
        if ($hour < 18) return "{$emoji} Buổi chiều mát mẻ, thích hợp để học tiếng Trung! 📚";
        if ($hour < 22) return "{$emoji} Buổi tối ôn lại những gì đã học hôm nay nhé!";

        return "{$emoji} Sắp nửa đêm rồi! Học thêm một chút trước khi ngủ nhé!";
    }

    private function getHungerDialogue(UserPet $userPet): string
    {
        return match ($userPet->getHungerState()) {
            'happy'       => 'Tớ đang rất vui và no! Hãy tiếp tục học để tớ lớn lên nhé! 😊',
            'hungry'      => 'Tớ hơi đói rồi... Học xong thì cho tớ ăn nhé! 🍖',
            'very_hungry' => 'Tớ đói lắm rồi! Mau mau cho tớ ăn đi! 😟',
            'weak'        => 'Tớ sắp kiệt sức mất... Đừng bỏ tớ nhé! 😢',
            'dormant'     => '💤 Tớ đang ngủ đông... Cho ăn để đánh thức tớ dậy!',
            default       => 'Tớ đang chờ bạn học bài! 📚',
        };
    }

    private function getStageDialogue(UserPet $userPet): string
    {
        return match ($userPet->stage) {
            0 => 'Tớ vẫn còn là trứng... Hãy cho tớ ăn để nở ra nhé! 🥚',
            1 => 'Tớ vừa nở! Còn nhỏ xíu, cần bạn chăm sóc nhiều lắm 🐣',
            2 => 'Tớ đang lớn dần lên nhờ bạn học chăm chỉ! 🐥',
            3 => 'Tớ đã lớn hơn rồi! Hãy học thêm từ vựng để tớ tiến hóa tiếp 🦊',
            4 => 'Tớ đã trưởng thành! Chỉ cần bạn đọc và nghe thêm là tớ tiến hóa cấp cuối 🐺',
            5 => 'Tớ đã đạt cấp độ tối đa! Cảm ơn bạn đã học tiếng Trung chăm chỉ! 🐉',
            default => 'Hãy tiếp tục học để tớ lớn lên nhé!',
        };
    }
}
