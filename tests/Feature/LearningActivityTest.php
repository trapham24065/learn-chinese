<?php

namespace Tests\Feature;

use App\Models\LearningActivity;
use App\Models\User;
use App\Models\UserDailyProgress;
use App\Models\UserLearningStat;
use App\Services\DailyGoalService;
use App\Services\LearningActivityService;
use App\Services\StreakService;
use App\Services\XpService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LearningActivityTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_request_is_rejected(): void
    {
        $response = $this->postJson(route('student.activity.log'), [
            'activity_type' => 'flashcard_review',
        ]);

        $response->assertUnauthorized();
    }

    public function test_logging_activity_awards_xp_and_updates_stats(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_STUDENT]);

        $response = $this->actingAs($user)->postJson(route('student.activity.log'), [
            'activity_type' => 'flashcard_known',
            'idempotency_key' => 'flashcard-known-test-1',
            'source_type' => 'flashcard',
            'source_id' => 10,
        ]);

        $response->assertOk();
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('xp.earned', 2);
        $response->assertJsonPath('xp.total', 2);
        $response->assertJsonPath('streak.current', 1);
        $response->assertJsonPath('daily_goal.flashcards.current', 1);

        $this->assertDatabaseHas('learning_activities', [
            'user_id' => $user->id,
            'activity_type' => 'flashcard_known',
            'idempotency_key' => 'flashcard-known-test-1',
            'xp_earned' => 2,
        ]);

        $this->assertDatabaseHas('user_learning_stats', [
            'user_id' => $user->id,
            'total_xp' => 2,
            'current_streak' => 1,
        ]);
    }

    public function test_idempotency_key_prevents_duplicate_xp(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_STUDENT]);
        $key = 'quiz-unique-tx-123';

        // First call
        $first = $this->actingAs($user)->postJson(route('student.activity.log'), [
            'activity_type' => 'quiz_correct',
            'idempotency_key' => $key,
        ]);
        $first->assertOk();
        $first->assertJsonPath('xp.earned', 5);
        $first->assertJsonPath('xp.total', 5);

        // Second call with same key
        $second = $this->actingAs($user)->postJson(route('student.activity.log'), [
            'activity_type' => 'quiz_correct',
            'idempotency_key' => $key,
        ]);
        $second->assertOk();
        $second->assertJsonPath('xp.earned', 0);
        $second->assertJsonPath('xp.total', 5);
        $second->assertJsonPath('is_duplicate', true);

        // Ensure only 1 activity recorded with this key
        $this->assertEquals(1, LearningActivity::where('idempotency_key', $key)->count());
    }

    public function test_streak_service_handles_consecutive_days(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_STUDENT]);
        $streakService = app(StreakService::class);
        $timezone = $streakService->getTimezone();

        // Simulate activity yesterday
        $yesterday = Carbon::now($timezone)->subDay();
        UserLearningStat::create([
            'user_id' => $user->id,
            'total_xp' => 10,
            'current_streak' => 3,
            'longest_streak' => 3,
            'last_activity_date' => $yesterday->toDateString(),
        ]);

        // Record activity today
        $result = $streakService->recordActivity($user);

        $this->assertEquals(4, $result['current']);
        $this->assertEquals(4, $result['longest']);
        $this->assertTrue($result['is_new_day']);

        // Recording again today keeps streak
        $again = $streakService->recordActivity($user);
        $this->assertEquals(4, $again['current']);
        $this->assertFalse($again['is_new_day']);
    }

    public function test_daily_goal_service_progress_calculation(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_STUDENT]);
        $dailyService = app(DailyGoalService::class);

        // Record 5 flashcards
        for ($i = 0; $i < 5; $i++) {
            $dailyService->recordProgress($user, 'flashcard_review', 1);
        }

        $summary = $dailyService->getTodaySummary($user);
        $this->assertEquals(5, $summary['flashcards']['current']);
        // 5 cards * 10 = 50%
        $this->assertEquals(50, $summary['progress_percent']);
        $this->assertFalse($summary['completed']);

        // Record 3 quiz questions -> 50% + (3 * 20 = 60%) = 110% -> capped 100%
        $dailyService->recordProgress($user, 'quiz_correct', 5);
        $dailyService->recordProgress($user, 'quiz_correct', 5);
        $res = $dailyService->recordProgress($user, 'quiz_correct', 5);
        $this->assertEquals(100, $res['progress_percent']);
        $this->assertTrue($res['completed']);
        $this->assertTrue($res['just_completed']);
    }

    public function test_dashboard_displays_engagement_widgets(): void
    {
        $user = User::factory()->create([
            'role' => User::ROLE_STUDENT,
            'email_verified_at' => now(),
        ]);

        UserLearningStat::create([
            'user_id' => $user->id,
            'total_xp' => 150,
            'current_streak' => 5,
            'longest_streak' => 5,
            'last_activity_date' => now()->toDateString(),
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('Mục tiêu hôm nay');
        $response->assertSee('150 XP');
        $response->assertSee('Tích lũy XP');
        $response->assertSee('5 ngày');
    }
}
