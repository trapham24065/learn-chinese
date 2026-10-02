<?php

namespace App\Services;

use App\Models\LearningActivity;
use App\Models\User;
use App\Models\UserLearningStat;

class LearningActivityService
{
    public function __construct(
        protected XpService $xpService,
        protected StreakService $streakService,
        protected DailyGoalService $dailyGoalService
    ) {}

    /**
     * Orchestrate activity logging, XP awards, streak progression, and daily goal tracking.
     *
     * @param array{
     *     source_type?: ?string,
     *     source_id?: ?int,
     *     idempotency_key?: ?string,
     *     meta?: array
     * } $data
     */
    public function logActivity(User $user, string $activityType, array $data = []): array
    {
        $idempotencyKey = $data['idempotency_key'] ?? null;
        $meta = $data['meta'] ?? [];
        $sourceType = $data['source_type'] ?? null;
        $sourceId = isset($data['source_id']) ? (int) $data['source_id'] : null;

        // 1. Idempotency Check
        if ($this->xpService->isDuplicate($user, $idempotencyKey)) {
            $stats = UserLearningStat::firstOrCreate(
                ['user_id' => $user->id],
                ['total_xp' => 0, 'current_streak' => 0, 'longest_streak' => 0]
            );

            $goalSummary = $this->dailyGoalService->getTodaySummary($user);

            return [
                'success' => true,
                'is_duplicate' => true,
                'xp' => [
                    'earned' => 0,
                    'total' => (int) $stats->total_xp,
                ],
                'streak' => [
                    'current' => (int) $stats->current_streak,
                    'is_new_record' => false,
                ],
                'daily_goal' => [
                    'flashcards' => $goalSummary['flashcards'],
                    'quiz' => $goalSummary['quiz'],
                    'progress_percent' => $goalSummary['progress_percent'],
                    'completed' => $goalSummary['completed'],
                ],
                'feedback' => [
                    'level' => 'none',
                    'sound' => null,
                    'message' => null,
                ],
            ];
        }

        // 2. Award Base XP
        $xpResult = $this->xpService->calculateAndAward($user, $activityType, $idempotencyKey, $meta);
        $totalXpEarned = $xpResult['xp_earned'];

        // 3. Update Daily Streak
        $streakResult = $this->streakService->recordActivity($user);

        // 4. Record Daily Goal Progress
        $dailyGoalResult = $this->dailyGoalService->recordProgress($user, $activityType, $totalXpEarned, $meta);

        // 4b. Bonus XP if Daily Goal was just completed
        if ($dailyGoalResult['just_completed']) {
            $bonusXp = XpService::XP_RULES['daily_goal_bonus'] ?? 20;
            $bonusResult = $this->xpService->calculateAndAward($user, 'daily_goal_bonus', null, ['bonus' => true]);
            $totalXpEarned += $bonusXp;

            LearningActivity::create([
                'user_id' => $user->id,
                'activity_type' => 'daily_goal_bonus',
                'source_type' => 'daily_goal',
                'source_id' => null,
                'xp_earned' => $bonusXp,
                'idempotency_key' => $idempotencyKey ? "{$idempotencyKey}:bonus" : null,
                'meta' => ['goal_reached' => true],
            ]);
        }

        // 5. Persist the Primary Activity Log
        LearningActivity::create([
            'user_id' => $user->id,
            'activity_type' => $activityType,
            'source_type' => $sourceType,
            'source_id' => $sourceId,
            'xp_earned' => $xpResult['xp_earned'],
            'idempotency_key' => $idempotencyKey,
            'meta' => $meta,
        ]);

        // 5b. Sync Pet Companion Activity & Milestones if user has an active pet
        try {
            $userPet = \App\Models\UserPet::where('user_id', $user->id)->first();
            if ($userPet && $userPet->isActive()) {
                if ($userPet->last_studied_at && $userPet->last_studied_at->diffInDays(now()) >= 2) {
                    $daysAbsent = (int) $userPet->last_studied_at->diffInDays(now());
                    app(\App\Services\Pet\PetMemoryService::class)->rememberAbsenceReturn($userPet, $daysAbsent);
                }

                $userPet->update(['last_studied_at' => now()]);
                $userPet->increment('study_session_count');

                app(\App\Services\Pet\PetAffinityService::class)->addAffinity($userPet, 2, 'study_session');

                if ($dailyGoalResult['just_completed']) {
                    app(\App\Services\Pet\PetMemoryService::class)->rememberDailyGoal($userPet);
                    app(\App\Services\Pet\PetAffinityService::class)->addAffinity($userPet, 3, 'daily_goal');
                }

                if ($streakResult['is_milestone'] && $streakResult['current'] >= 3) {
                    app(\App\Services\Pet\PetMemoryService::class)->rememberStreakMilestone($userPet, (int) $streakResult['current']);
                    app(\App\Services\Pet\PetAffinityService::class)->addAffinity($userPet, 2, 'streak_milestone');
                }
            }
        } catch (\Throwable $e) {
            report($e);
        }

        // 6. Refresh user stats to get latest total
        $stats = UserLearningStat::firstOrCreate(
            ['user_id' => $user->id],
            ['total_xp' => 0, 'current_streak' => 0, 'longest_streak' => 0]
        );

        // 7. Tiered Feedback Level Determination
        $isCompletion = in_array($activityType, ['quiz_completed', 'lesson_completed', 'reading_completed'])
            || $dailyGoalResult['just_completed'];

        $isMilestone = $streakResult['is_milestone']
            || ($streakResult['is_new_record'] && $streakResult['current'] >= 3)
            || (($meta['streak_count'] ?? 0) >= 5);

        if ($isCompletion) {
            $level = 'completion';
            $sound = 'fanfare';
            $message = $dailyGoalResult['just_completed'] ? 'Mục tiêu ngày đã hoàn thành!' : 'Hoàn thành xuất sắc!';
        } elseif ($isMilestone) {
            $level = 'milestone';
            $sound = 'milestone';
            $message = "Chuỗi tuyệt vời! 🔥 {$streakResult['current']} ngày liên tiếp";
        } else {
            $level = 'micro';
            $sound = str_starts_with($activityType, 'flashcard_review') ? 'tap' : 'ding';
            $message = "+{$totalXpEarned} XP";
        }

        return [
            'success' => true,
            'xp' => [
                'earned' => $totalXpEarned,
                'total' => (int) $stats->total_xp,
            ],
            'streak' => [
                'current' => (int) $streakResult['current'],
                'is_new_record' => (bool) $streakResult['is_new_record'],
            ],
            'daily_goal' => [
                'flashcards' => $dailyGoalResult['flashcards'],
                'quiz' => $dailyGoalResult['quiz'],
                'progress_percent' => (int) $dailyGoalResult['progress_percent'],
                'completed' => (bool) $dailyGoalResult['completed'],
            ],
            'feedback' => [
                'level' => $level,
                'sound' => $sound,
                'message' => $message,
            ],
        ];
    }
}
