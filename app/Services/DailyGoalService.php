<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserDailyProgress;
use Carbon\Carbon;

class DailyGoalService
{
    public const TARGET_FLASHCARDS = 10;
    public const TARGET_QUIZ = 5;
    public const TARGET_READING_MINUTES = 5;
    public const TARGET_PINYIN = 10;
    public const TARGET_PRACTICE = 3;

    /**
     * Get the active timezone.
     */
    public function getTimezone(): string
    {
        return config('app.timezone', 'Asia/Ho_Chi_Minh');
    }

    /**
     * Get or create daily progress record for today.
     */
    public function getTodayProgress(User $user): UserDailyProgress
    {
        $timezone = $this->getTimezone();
        $today = Carbon::now($timezone)->toDateString();

        return UserDailyProgress::firstOrCreate(
            [
                'user_id' => $user->id,
                'date' => $today,
            ],
            [
                'flashcards_count' => 0,
                'quiz_count' => 0,
                'reading_minutes' => 0,
                'pinyin_count' => 0,
                'practice_count' => 0,
                'fast_match_count' => 0,
                'audio_quiz_count' => 0,
                'xp_earned' => 0,
                'is_goal_completed' => false,
            ]
        );
    }

    /**
     * Record daily progress for an activity.
     */
    public function recordProgress(User $user, string $activityType, int $xpEarned = 0, array $meta = []): array
    {
        $progress = $this->getTodayProgress($user);

        if (str_starts_with($activityType, 'flashcard_')) {
            $progress->flashcards_count += 1;
        } elseif (str_starts_with($activityType, 'quiz_')) {
            $progress->quiz_count += 1;
        } elseif ($activityType === 'pinyin_practice') {
            $progress->pinyin_count += 1;
        } elseif ($activityType === 'reading_completed') {
            $minutes = isset($meta['minutes']) && is_numeric($meta['minutes'])
                ? max(1, (int) $meta['minutes'])
                : 2;
            $progress->reading_minutes += $minutes;
        } elseif (str_starts_with($activityType, 'fast_match')) {
            $progress->practice_count += 1;
            $progress->fast_match_count += 1;
        } elseif (str_starts_with($activityType, 'audio_quiz')) {
            $progress->practice_count += 1;
            $progress->audio_quiz_count += 1;
        }

        if ($xpEarned > 0) {
            $progress->xp_earned += $xpEarned;
        }

        // Calculate flexible composite progress (capped at 100)
        // 10 flashcards = 100%, 5 quiz questions = 100%, 5 reading mins = 100%, 10 pinyin = 100%, 3 practice sessions = 100%
        $score = ($progress->flashcards_count * 10)
            + ($progress->quiz_count * 20)
            + ($progress->reading_minutes * 20)
            + ($progress->pinyin_count * 10)
            + ($progress->practice_count * 34);

        $percent = min(100, (int) round($score));

        $justCompleted = false;
        if ($percent >= 100 && !$progress->is_goal_completed) {
            $progress->is_goal_completed = true;
            $justCompleted = true;
        }

        $progress->save();

        return [
            'flashcards' => [
                'current' => $progress->flashcards_count,
                'target' => self::TARGET_FLASHCARDS,
            ],
            'quiz' => [
                'current' => $progress->quiz_count,
                'target' => self::TARGET_QUIZ,
            ],
            'reading_minutes' => [
                'current' => $progress->reading_minutes,
                'target' => self::TARGET_READING_MINUTES,
            ],
            'pinyin' => [
                'current' => $progress->pinyin_count,
                'target' => self::TARGET_PINYIN,
            ],
            'practice' => [
                'current' => $progress->practice_count,
                'target' => self::TARGET_PRACTICE,
            ],
            'fast_match_count' => $progress->fast_match_count,
            'audio_quiz_count' => $progress->audio_quiz_count,
            'progress_percent' => $percent,
            'completed' => (bool) $progress->is_goal_completed,
            'just_completed' => $justCompleted,
        ];
    }

    /**
     * Get current day's progress summary for user.
     */
    public function getTodaySummary(User $user): array
    {
        $progress = $this->getTodayProgress($user);

        $score = ($progress->flashcards_count * 10)
            + ($progress->quiz_count * 20)
            + ($progress->reading_minutes * 20)
            + ($progress->pinyin_count * 10)
            + ($progress->practice_count * 34);

        $percent = min(100, (int) round($score));

        return [
            'flashcards' => [
                'current' => $progress->flashcards_count,
                'target' => self::TARGET_FLASHCARDS,
            ],
            'quiz' => [
                'current' => $progress->quiz_count,
                'target' => self::TARGET_QUIZ,
            ],
            'reading_minutes' => [
                'current' => $progress->reading_minutes,
                'target' => self::TARGET_READING_MINUTES,
            ],
            'pinyin' => [
                'current' => $progress->pinyin_count,
                'target' => self::TARGET_PINYIN,
            ],
            'practice' => [
                'current' => $progress->practice_count,
                'target' => self::TARGET_PRACTICE,
            ],
            'fast_match_count' => $progress->fast_match_count,
            'audio_quiz_count' => $progress->audio_quiz_count,
            'progress_percent' => $percent,
            'completed' => (bool) $progress->is_goal_completed,
            'xp_earned_today' => $progress->xp_earned,
        ];
    }
}
