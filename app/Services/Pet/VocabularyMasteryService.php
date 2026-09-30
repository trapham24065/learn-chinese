<?php

namespace App\Services\Pet;

use App\Models\Flashcard;
use App\Models\FlashcardProgress;
use App\Models\User;
use Illuminate\Support\Collection;

class VocabularyMasteryService
{
    /**
     * Count words the user has mastered (repetition >= 2).
     */
    public function getMasteredCount(User $user): int
    {
        return FlashcardProgress::where('user_id', $user->id)
            ->where('repetition', '>=', 2)
            ->count();
    }

    /**
     * Count words the user has actively used (repetition >= 3).
     */
    public function getUsedCount(User $user): int
    {
        return FlashcardProgress::where('user_id', $user->id)
            ->where('repetition', '>=', 3)
            ->count();
    }

    /**
     * Get recently mastered flashcards (with hanzi/pinyin/meaning).
     * Returns Collection of Flashcards the user has repetition >= 2.
     */
    public function getRecentlyMastered(User $user, int $limit = 10): Collection
    {
        $progressIds = FlashcardProgress::where('user_id', $user->id)
            ->where('repetition', '>=', 2)
            ->orderByDesc('updated_at')
            ->limit($limit)
            ->pluck('flashcard_id');

        if ($progressIds->isEmpty()) {
            return collect();
        }

        return Flashcard::whereIn('id', $progressIds)
            ->whereNotNull('hanzi')
            ->get(['id', 'hanzi', 'pinyin', 'meaning']);
    }

    /**
     * Get a random sample of mastered words for pet dialogue.
     */
    public function getRandomMastered(User $user, int $count = 5): Collection
    {
        $progressIds = FlashcardProgress::where('user_id', $user->id)
            ->where('repetition', '>=', 2)
            ->inRandomOrder()
            ->limit($count)
            ->pluck('flashcard_id');

        if ($progressIds->isEmpty()) {
            return collect();
        }

        return Flashcard::whereIn('id', $progressIds)
            ->whereNotNull('hanzi')
            ->get(['id', 'hanzi', 'pinyin', 'meaning']);
    }

    /**
     * Get all mastered flashcards for the Pet Vocabulary tab.
     */
    public function getAllMastered(User $user, int $limit = 60): Collection
    {
        $progressRecords = FlashcardProgress::where('user_id', $user->id)
            ->where('repetition', '>=', 2)
            ->orderByDesc('repetition')
            ->orderByDesc('updated_at')
            ->limit($limit)
            ->get(['flashcard_id', 'repetition', 'updated_at'])
            ->keyBy('flashcard_id');

        if ($progressRecords->isEmpty()) {
            return collect();
        }

        $cards = Flashcard::whereIn('id', $progressRecords->keys())
            ->whereNotNull('hanzi')
            ->get(['id', 'hanzi', 'pinyin', 'meaning']);

        return $cards->map(function ($card) use ($progressRecords) {
            $progress = $progressRecords->get($card->id);
            $card->repetition = $progress?->repetition ?? 2;
            $card->mastered_at = $progress?->updated_at;
            return $card;
        });
    }
}
