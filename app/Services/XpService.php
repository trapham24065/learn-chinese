<?php

namespace App\Services;

use App\Models\LearningActivity;
use App\Models\User;
use App\Models\UserLearningStat;
use Carbon\Carbon;

class XpService
{
    /**
     * Standard XP amounts by activity type.
     */
    public const XP_RULES = [
        'flashcard_review' => 1,
        'flashcard_known' => 2,
        'quiz_correct' => 5,
        'quiz_streak_5' => 10,
        'quiz_completed' => 15,
        'lesson_completed' => 30,
        'reading_completed' => 25,
        'pinyin_practice' => 2,
        'daily_goal_bonus' => 20,
        'fast_match' => 15,
        'fast_match_completed' => 15,
        'audio_quiz' => 15,
        'audio_quiz_completed' => 15,
        'pet_mini_quiz' => 2,
    ];

    /**
     * Check if activity with this idempotency key was already recorded for the user.
     */
    public function isDuplicate(User $user, ?string $idempotencyKey): bool
    {
        if (empty($idempotencyKey)) {
            return false;
        }

        return LearningActivity::where('user_id', $user->id)
            ->where('idempotency_key', $idempotencyKey)
            ->exists();
    }

    /**
     * Determine XP to award and update user's total XP stat.
     *
     * @return array{xp_earned: int, is_duplicate: bool, total_xp: int}
     */
    public function calculateAndAward(User $user, string $activityType, ?string $idempotencyKey = null, array $meta = []): array
    {
        if ($this->isDuplicate($user, $idempotencyKey)) {
            $stats = UserLearningStat::firstOrCreate(
                ['user_id' => $user->id],
                ['total_xp' => 0, 'current_streak' => 0, 'longest_streak' => 0]
            );

            return [
                'xp_earned' => 0,
                'is_duplicate' => true,
                'total_xp' => $stats->total_xp,
            ];
        }

        // Determine XP amount
        if (isset($meta['xp']) && is_numeric($meta['xp'])) {
            $xpEarned = min(100, max(0, (int) $meta['xp']));
        } elseif (in_array($activityType, ['fast_match', 'fast_match_completed', 'audio_quiz', 'audio_quiz_completed'])) {
            // Anti-farming diminishing returns for mini-games
            $today = Carbon::now(config('app.timezone', 'Asia/Ho_Chi_Minh'))->toDateString();
            $todayCount = LearningActivity::where('user_id', $user->id)
                ->whereIn('activity_type', ['fast_match', 'fast_match_completed', 'audio_quiz', 'audio_quiz_completed'])
                ->whereDate('created_at', $today)
                ->count();

            if ($todayCount === 0) {
                $xpEarned = 15; // 1st meaningful play of day
            } elseif ($todayCount < 3) {
                $xpEarned = 5;  // 2nd and 3rd replay
            } else {
                $xpEarned = 0;  // 4th+ replay (anti-farming)
            }
        } else {
            $xpEarned = self::XP_RULES[$activityType] ?? 1;
        }

        $stats = UserLearningStat::firstOrCreate(
            ['user_id' => $user->id],
            ['total_xp' => 0, 'current_streak' => 0, 'longest_streak' => 0]
        );

        if ($xpEarned > 0) {
            $stats->increment('total_xp', $xpEarned);
        }
        $stats->update(['last_activity_at' => Carbon::now()]);
        $stats->refresh();

        return [
            'xp_earned' => $xpEarned,
            'is_duplicate' => false,
            'total_xp' => $stats->total_xp,
        ];
    }
}
