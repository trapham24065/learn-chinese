<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserLearningStat;
use Carbon\Carbon;

class StreakService
{
    /**
     * Get the active timezone for streak calculation.
     */
    public function getTimezone(): string
    {
        return config('app.timezone', 'Asia/Ho_Chi_Minh');
    }

    /**
     * Record learning activity and update user's daily streak.
     *
     * @return array{current: int, longest: int, is_new_day: bool, is_new_record: bool, is_milestone: bool}
     */
    public function recordActivity(User $user): array
    {
        $timezone = $this->getTimezone();
        $today = Carbon::now($timezone)->toDateString();
        $yesterday = Carbon::now($timezone)->subDay()->toDateString();

        $stats = UserLearningStat::firstOrCreate(
            ['user_id' => $user->id],
            [
                'total_xp' => 0,
                'current_streak' => 0,
                'longest_streak' => 0,
                'last_activity_date' => null,
            ]
        );

        $lastDate = $stats->last_activity_date
            ? (is_string($stats->last_activity_date) ? substr($stats->last_activity_date, 0, 10) : $stats->last_activity_date->toDateString())
            : null;

        $isNewDay = false;
        $isNewRecord = false;

        if ($lastDate === $today) {
            // Already active today; streak maintained
            $isNewDay = false;
        } elseif ($lastDate === $yesterday) {
            // Consecutive day: increment streak
            $stats->current_streak += 1;
            if ($stats->current_streak > $stats->longest_streak) {
                $stats->longest_streak = $stats->current_streak;
                $isNewRecord = true;
            }
            $stats->last_activity_date = $today;
            $stats->save();
            $isNewDay = true;
        } else {
            // Gap > 1 day or first time: reset streak to 1
            $stats->current_streak = 1;
            if ($stats->longest_streak < 1) {
                $stats->longest_streak = 1;
                $isNewRecord = true;
            }
            $stats->last_activity_date = $today;
            $stats->save();
            $isNewDay = true;
        }

        $isMilestone = $isNewDay && ($stats->current_streak % 5 === 0);

        return [
            'current' => $stats->current_streak,
            'longest' => $stats->longest_streak,
            'is_new_day' => $isNewDay,
            'is_new_record' => $isNewRecord,
            'is_milestone' => $isMilestone,
        ];
    }

    /**
     * Get current effective streak for display.
     */
    public function getCurrentStreak(User $user): int
    {
        $stats = $user->learningStats;
        if (!$stats || !$stats->last_activity_date) {
            return 0;
        }

        $timezone = $this->getTimezone();
        $today = Carbon::now($timezone)->toDateString();
        $yesterday = Carbon::now($timezone)->subDay()->toDateString();
        $lastDate = is_string($stats->last_activity_date)
            ? substr($stats->last_activity_date, 0, 10)
            : $stats->last_activity_date->toDateString();

        if ($lastDate === $today || $lastDate === $yesterday) {
            return (int) $stats->current_streak;
        }

        return 0;
    }
}
