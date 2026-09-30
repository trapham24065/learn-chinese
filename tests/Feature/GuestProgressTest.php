<?php

namespace Tests\Feature;

use App\Models\GuestActivity;
use App\Models\GuestProgress;
use App\Models\User;
use App\Services\GuestProgressService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuestProgressTest extends TestCase
{
    use RefreshDatabase;

    protected GuestProgressService $guestService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->guestService = app(GuestProgressService::class);
    }

    public function test_guest_progress_record_is_created_with_uuid(): void
    {
        $progress = $this->guestService->resolveOrCreateProgress();

        $this->assertNotNull($progress->guest_uuid);
        $this->assertEquals(0, $progress->total_xp);
        $this->assertEquals(0, $progress->activities_count);
        $this->assertEquals(GuestProgress::STATUS_PENDING, $progress->status);
        $this->assertTrue($progress->isPending());
    }

    public function test_guest_activity_records_xp_and_signed_token(): void
    {
        $progress = $this->guestService->resolveOrCreateProgress();

        $result = $this->guestService->recordActivity(
            guestUuid: $progress->guest_uuid,
            activityType: 'fast_match_completed',
            data: [
                'source_type' => 'game',
                'idempotency_key' => 'fm_test_1',
                'meta' => ['game_type' => 'fast_match'],
            ]
        );

        $this->assertTrue($result['success']);
        $this->assertEquals(15, $result['xp_earned']);
        $this->assertEquals(15, $result['total_xp']);
        $this->assertEquals(1, $result['activities_count']);
        $this->assertNotEmpty($result['claim_token']);

        // Verify token decryption matches
        $payload = $this->guestService->verifySignedToken($result['claim_token']);
        $this->assertNotNull($payload);
        $this->assertEquals($progress->guest_uuid, $payload['guest_uuid']);
    }

    public function test_anti_farming_diminishing_returns_for_guest(): void
    {
        $progress = $this->guestService->resolveOrCreateProgress();

        // 1st play: 15 XP
        $r1 = $this->guestService->recordActivity($progress->guest_uuid, 'fast_match_completed', ['idempotency_key' => 'g1']);
        $this->assertEquals(15, $r1['xp_earned']);

        // 2nd play: 5 XP
        $r2 = $this->guestService->recordActivity($progress->guest_uuid, 'fast_match_completed', ['idempotency_key' => 'g2']);
        $this->assertEquals(5, $r2['xp_earned']);

        // 3rd play: 5 XP
        $r3 = $this->guestService->recordActivity($progress->guest_uuid, 'fast_match_completed', ['idempotency_key' => 'g3']);
        $this->assertEquals(5, $r3['xp_earned']);

        // 4th play: 0 XP (anti-farming limit)
        $r4 = $this->guestService->recordActivity($progress->guest_uuid, 'fast_match_completed', ['idempotency_key' => 'g4']);
        $this->assertEquals(0, $r4['xp_earned']);

        $this->assertEquals(25, $r4['total_xp']);
    }

    public function test_authenticated_user_can_claim_guest_progress(): void
    {
        $user = User::factory()->create([
            'role' => User::ROLE_STUDENT,
        ]);

        $progress = $this->guestService->resolveOrCreateProgress();

        // Guest plays 2 games (15 + 5 = 20 XP)
        $this->guestService->recordActivity($progress->guest_uuid, 'fast_match_completed', ['idempotency_key' => 'claim_1']);
        $rec2 = $this->guestService->recordActivity($progress->guest_uuid, 'audio_quiz_completed', ['idempotency_key' => 'claim_2']);

        $claimToken = $rec2['claim_token'];

        // Claim into user account via API endpoint
        $response = $this->actingAs($user)
            ->postJson(route('student.claim-guest-progress'), [
                'claim_token' => $claimToken,
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'already_claimed' => false,
            'claimed_xp' => 20,
            'activities_count' => 2,
        ]);

        // User stats updated
        $userStats = $user->learningStats;
        $this->assertNotNull($userStats);
        $this->assertEquals(20, $userStats->total_xp);
        $this->assertEquals(1, $userStats->current_streak);

        // GuestProgress marked as claimed
        $freshProgress = GuestProgress::where('guest_uuid', $progress->guest_uuid)->first();
        $this->assertEquals(GuestProgress::STATUS_CLAIMED, $freshProgress->status);
        $this->assertEquals($user->id, $freshProgress->claimed_by_user_id);
    }

    public function test_claim_is_strictly_idempotent(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_STUDENT]);
        $progress = $this->guestService->resolveOrCreateProgress();

        $rec = $this->guestService->recordActivity($progress->guest_uuid, 'fast_match_completed', ['idempotency_key' => 'idem_1']);
        $claimToken = $rec['claim_token'];

        // 1st claim
        $res1 = $this->actingAs($user)->postJson(route('student.claim-guest-progress'), [
            'claim_token' => $claimToken,
        ]);
        $res1->assertStatus(200)->assertJson(['claimed_xp' => 15]);

        // 2nd claim (network duplicate)
        $res2 = $this->actingAs($user)->postJson(route('student.claim-guest-progress'), [
            'claim_token' => $claimToken,
        ]);
        $res2->assertStatus(200)->assertJson([
            'success' => true,
            'already_claimed' => true,
            'claimed_xp' => 15,
        ]);

        // Total user XP is still exactly 15, not 30!
        $this->assertEquals(15, $user->fresh()->learningStats->total_xp);
    }

    public function test_tampered_token_is_rejected(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_STUDENT]);

        $response = $this->actingAs($user)->postJson(route('student.claim-guest-progress'), [
            'claim_token' => 'invalid_tampered_base64_string',
        ]);

        $response->assertStatus(422);
        $response->assertJson(['success' => false]);
    }

    public function test_guest_playing_fast_match_returns_guest_progress_and_token(): void
    {
        // Generate valid game token
        $gameService = app(\App\Services\GameService::class);
        $session = $gameService->createFastMatchSession('starter', 'hanzi_meaning', 1);

        $response = $this->postJson(route('games.fast-match.finish'), [
            'token' => $session['token'],
            'moves' => [],
            'duration_seconds' => 18,
            'guest_uuid' => null,
            'claim_token' => null,
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'score',
            'xp_earned',
            'claim_token',
            'guest_progress' => [
                'guest_uuid',
                'xp_earned',
                'total_xp',
                'activities_count',
                'claim_token',
            ],
        ]);
        $this->assertNotEmpty($response->json('claim_token'));
    }
}
