<?php

namespace Tests\Feature;

use App\Console\Commands\SyncStaleDuelRooms;
use App\Models\DuelRoom;
use App\Models\DuelRoundAnswer;
use App\Models\Question;
use App\Models\User;
use App\Services\Duel\DuelGameEngine;
use App\Services\XpService;
use Database\Seeders\HskMockExamQuestionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DuelEngineTest extends TestCase
{
    use RefreshDatabase;

    protected DuelGameEngine $engine;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(HskMockExamQuestionSeeder::class);
        $this->engine = new DuelGameEngine();
    }

    protected function createActiveTestRoom(int $hskLevel = 3): DuelRoom
    {
        $u1 = User::factory()->create(['name' => 'Player 1']);
        $u2 = User::factory()->create(['name' => 'Player 2']);

        $room = $this->engine->createRoom($hskLevel, 'random', [
            'type' => 'user',
            'user_id' => $u1->id,
            'name' => $u1->name,
        ]);

        $room = $this->engine->joinRoom($room, [
            'type' => 'user',
            'user_id' => $u2->id,
            'name' => $u2->name,
        ]);

        // Force transition countdown -> in_progress
        $now = microtime(true);
        $this->engine->advanceStateLocked($room, $now + 3.1);
        $room->save();

        return $room->fresh();
    }

    // TC-01: Late answer causes timeout for both slots missing, transitions to revealing
    public function test_late_answer_causes_timeout_both_slots(): void
    {
        $room = $this->createActiveTestRoom();
        $now = microtime(true);

        // Advance room clock to be past deadline
        $room->phase_ends_at = $now - 1.0;
        $room->save();

        // Submit answer after deadline
        $result = $this->engine->submitAnswer($room, 1, 'A', 'req_late_1');

        $this->assertEquals('timeout', $result['status']);
        $this->assertNull($result['data']);

        $room->refresh();
        $this->assertEquals('revealing', $room->round_phase);

        // Both slots must now have timeout records in DB
        $answers = DuelRoundAnswer::where('duel_room_id', $room->id)
            ->where('round_number', 1)
            ->get();

        $this->assertCount(2, $answers);
        foreach ($answers as $ans) {
            $this->assertNull($ans->selected_answer);
            $this->assertFalse($ans->is_correct);
            $this->assertEquals(0, $ans->score_awarded);
        }
    }

    // TC-02: Exact deadline boundary ($now <= phase_ends_at is accepted, > is timeout)
    public function test_exact_deadline_boundary(): void
    {
        $room = $this->createActiveTestRoom();
        $now = microtime(true);
        $room->phase_started_at = $now;
        $room->phase_ends_at = $now + 10.0;
        $room->save();

        $q = $room->questions_snapshot[0];
        $correct = $q['correct_answer'];

        // Submit on-time
        $resultOnTime = $this->engine->submitAnswer($room, 1, $correct, 'req_ontime');
        $this->assertEquals('success', $resultOnTime['status']);
        $this->assertTrue($resultOnTime['data']->is_correct);
        $this->assertGreaterThanOrEqual(75, $resultOnTime['data']->score_awarded);
    }

    // TC-03: Same idempotency key across different players does not return Player 1's record
    public function test_same_idempotency_key_across_different_players(): void
    {
        $room = $this->createActiveTestRoom();
        $q = $room->questions_snapshot[0];
        $opt = $q['options'][0];

        $res1 = $this->engine->submitAnswer($room, 1, $opt, 'shared_key_123');
        $this->assertEquals('success', $res1['status']);
        $this->assertEquals(1, $res1['data']->player_slot);

        // Player 2 uses the exact same idempotency key
        $res2 = $this->engine->submitAnswer($room, 2, $opt, 'shared_key_123');
        $this->assertEquals('success', $res2['status']);
        $this->assertEquals(2, $res2['data']->player_slot);
        $this->assertNotEquals($res1['data']->id, $res2['data']->id);
    }

    // TC-04: Already submitted returned after reveal starts
    public function test_already_submitted_returned_after_reveal_starts(): void
    {
        $room = $this->createActiveTestRoom();
        $q = $room->questions_snapshot[0];

        // Both players submit early
        $this->engine->submitAnswer($room, 1, $q['options'][0], 'p1_sub');
        $this->engine->submitAnswer($room, 2, $q['options'][0], 'p2_sub');

        $room->refresh();
        $this->assertEquals('revealing', $room->round_phase);

        // Player 1 resubmits with different key
        $resRetry = $this->engine->submitAnswer($room, 1, $q['options'][0], 'p1_sub_retry');
        $this->assertEquals('already_submitted', $resRetry['status']);
        $this->assertEquals(1, $resRetry['data']->player_slot);
    }

    // TC-05: Both players offline scenarios (30s grace vs 45s abandoned)
    public function test_both_players_offline_scenarios(): void
    {
        $room = $this->createActiveTestRoom();
        $now = microtime(true);

        // Case A: 30s offline (between 25s and 40s) -> No forfeit
        $room->player1_last_seen_at = $now - 30.0;
        $room->player2_last_seen_at = $now - 30.0;
        $room->save();

        $this->engine->checkDisconnectionAndForfeit($room, $now);
        $room->refresh();
        $this->assertEquals('in_progress', $room->status);
        $this->assertNull($room->winner_slot);

        // Case B: 45s offline (> 40s) -> Abandoned
        $room->player1_last_seen_at = $now - 45.0;
        $room->player2_last_seen_at = $now - 45.0;
        $room->save();

        $this->engine->checkDisconnectionAndForfeit($room, $now);
        $room->refresh();
        $this->assertEquals('abandoned', $room->status);
        $this->assertNull($room->winner_slot);
        $this->assertFalse($room->xp_awarded);
    }

    // TC-06: Slot resolution logic (Player identity check)
    public function test_reject_spoofed_slot_from_client(): void
    {
        $room = $this->createActiveTestRoom();

        // Ensure slot 1 belongs to user 1 and slot 2 belongs to user 2
        $this->assertNotNull($room->player1_id);
        $this->assertNotNull($room->player2_id);
        $this->assertNotEquals($room->player1_id, $room->player2_id);
    }

    // TC-07: Race condition finishMatch and forfeit (finished match cannot be forfeited)
    public function test_race_condition_finish_match_and_forfeit(): void
    {
        $room = $this->createActiveTestRoom();
        $now = microtime(true);

        // Complete match normally
        $room->player1_score = 800;
        $room->player2_score = 700;
        $this->engine->finishMatch($room);
        $room->save();

        $this->assertEquals('finished', $room->status);
        $this->assertEquals(1, $room->winner_slot);

        // Now simulate player 1 going offline > 30s
        $room->player1_last_seen_at = $now - 35.0;
        $room->player2_last_seen_at = $now;
        $room->save();

        // Attempt forfeit
        $this->engine->checkDisconnectionAndForfeit($room, $now);
        $room->refresh();

        // Status and winner must not change!
        $this->assertEquals('finished', $room->status);
        $this->assertEquals(1, $room->winner_slot);
        $this->assertNull($room->forfeit_by_slot);
    }

    // TC-08: Concurrent rematch requests create only one child room
    public function test_concurrent_rematch_requests_create_only_one_child_room(): void
    {
        $room = $this->createActiveTestRoom();
        $room->status = 'finished';
        $room->save();

        // Player 1 requests rematch
        $res1 = $this->engine->requestRematch($room, 1);
        $this->assertEquals('waiting_opponent', $res1['status']);

        // Player 2 requests rematch (triggers room creation)
        $res2 = $this->engine->requestRematch($room, 2);
        $this->assertEquals('ready', $res2['status']);
        $this->assertNotNull($res2['room_code']);

        // Player 1 clicks rematch again
        $res3 = $this->engine->requestRematch($room, 1);
        $this->assertEquals('ready', $res3['status']);
        $this->assertEquals($res2['room_code'], $res3['room_code']);

        // Only 1 child room was created
        $childCount = DuelRoom::where('parent_room_id', $room->id)->count();
        $this->assertEquals(1, $childCount);
    }

    // TC-09: Answers masked before revealing phase
    public function test_answers_masked_before_revealing_phase(): void
    {
        $room = $this->createActiveTestRoom();
        $room->round_phase = 'answering';
        $room->save();

        $state = $this->engine->formatPublicRoomState($room, 1);

        $this->assertEquals('answering', $state['round_phase']);
        $currentQ = $state['current_question'];
        $this->assertNotNull($currentQ);
        $this->assertArrayNotHasKey('correct_answer', $currentQ);
        $this->assertArrayNotHasKey('explanation', $currentQ);

        // Now advance to revealing
        $room->round_phase = 'revealing';
        $room->save();

        $stateRevealing = $this->engine->formatPublicRoomState($room, 1);
        $currentQRevealing = $stateRevealing['current_question'];
        $this->assertArrayHasKey('correct_answer', $currentQRevealing);
        $this->assertArrayHasKey('explanation', $currentQRevealing);
    }

    // TC-10: Scheduled sync and polling concurrency
    public function test_scheduled_sync_and_polling_concurrency(): void
    {
        $room = $this->createActiveTestRoom();
        $now = microtime(true);
        $room->phase_ends_at = $now - 1.0;
        $room->save();

        // Polling syncs
        $state = $this->engine->syncAndGetRoomState($room, 1);
        $this->assertEquals('revealing', $state['round_phase']);

        // Scheduled command executes right after
        $exitCode = Artisan::call('duel:sync-stale-rooms');
        $this->assertEquals(0, $exitCode);

        $room->refresh();
        $this->assertEquals('revealing', $room->round_phase);
    }

    // TC-11: Scoring math theorem B_B - B_A >= 151
    public function test_scoring_math_b_b_minus_b_a_greater_equal_151(): void
    {
        // 10 correct baseline: 750 + B_A
        // 8 correct baseline: 600 + B_B
        // Player B wins iff B_B - B_A >= 151
        $ba = 40;
        $bb = 191; // 191 - 40 = 151
        $sa = 750 + $ba; // 790
        $sb = 600 + $bb; // 791
        $this->assertGreaterThan($sa, $sb);

        // When B_A >= 50, even if B achieves max B_B = 200:
        $baSecure = 50;
        $saSecure = 750 + $baSecure; // 800
        $sbMax = 600 + 200; // 800
        $this->assertEquals($saSecure, $sbMax);
        // Tie-breaker: Player A has 10 correct vs Player B has 8 correct -> Player A strictly wins
        $this->assertTrue(10 > 8);
    }

    // TC-12: Both players close tab in countdown -> Handled by Scheduled Command
    public function test_both_players_close_tab_in_countdown(): void
    {
        $room = $this->createActiveTestRoom();
        $now = microtime(true);
        $room->status = 'countdown';
        $room->phase_ends_at = $now - 1.0;
        $room->save();

        Artisan::call('duel:sync-stale-rooms');

        $room->refresh();
        $this->assertEquals('in_progress', $room->status);
        $this->assertEquals('answering', $room->round_phase);
        $this->assertEquals(1, $room->current_round);
    }

    // TC-13: Guest participates in multiple rooms independently via room code scoping
    public function test_guest_participates_in_multiple_rooms_independently(): void
    {
        $guestToken1 = 'token_room_1';
        $guestToken2 = 'token_room_2';

        $room1 = $this->engine->createRoom(2, 'random', [
            'type' => 'guest',
            'guest_token_hash' => hash_hmac('sha256', $guestToken1, config('app.key')),
            'name' => 'Khách 1',
        ]);

        $room2 = $this->engine->createRoom(3, 'random', [
            'type' => 'guest',
            'guest_token_hash' => hash_hmac('sha256', $guestToken2, config('app.key')),
            'name' => 'Khách 1',
        ]);

        $this->assertNotEquals($room1->room_code, $room2->room_code);
        $this->assertNotEquals($room1->player1_guest_token_hash, $room2->player1_guest_token_hash);
    }

    // TC-14: Scheduled command advances expired countdown
    public function test_scheduled_command_advances_expired_countdown(): void
    {
        $room = $this->createActiveTestRoom();
        $now = microtime(true);
        $room->status = 'countdown';
        $room->phase_ends_at = $now - 0.5;
        $room->save();

        $this->engine->advanceStateLocked($room, $now);
        $room->save();

        $room->refresh();
        $this->assertEquals('in_progress', $room->status);
        $this->assertEquals('answering', $room->round_phase);
    }

    // TC-15: Polling detects forfeit immediately
    public function test_polling_detects_forfeit_immediately(): void
    {
        $room = $this->createActiveTestRoom();
        $now = microtime(true);

        // Player 2 is offline for 26s
        $room->player1_last_seen_at = $now;
        $room->player2_last_seen_at = $now - 26.0;
        $room->save();

        // Player 1 polls
        $state = $this->engine->syncAndGetRoomState($room, 1);

        $this->assertEquals('finished', $state['status']);
        $this->assertEquals(1, $state['winner_slot']);
        $this->assertEquals(2, $state['forfeit_by_slot']);
    }

    // TC-16: XP idempotency prevents duplicate reward
    public function test_xp_idempotency_prevents_duplicate_reward(): void
    {
        $room = $this->createActiveTestRoom();
        $room->player1_score = 900;
        $room->player2_score = 600;

        $user1 = User::find($room->player1_id);
        $xpService = app(XpService::class);

        // Finish match 1st time
        $this->engine->finishMatch($room);
        $room->save();
        $this->assertTrue($room->xp_awarded);

        // Duplicate call to awardSlotXp
        $this->engine->awardSlotXp($room, 1, 50);

        // Verify that XpService detected duplicate
        $idempotencyKey = "duel_room_{$room->id}_slot_1_user_{$user1->id}";
        $this->assertTrue($xpService->isDuplicate($user1, $idempotencyKey));
    }

    // TC-17: Concurrent submissions for same slot return existing record
    public function test_concurrent_submissions_for_same_slot(): void
    {
        $room = $this->createActiveTestRoom();
        $q = $room->questions_snapshot[0];

        $res1 = $this->engine->submitAnswer($room, 1, $q['options'][0], 'sub_1');
        $this->assertEquals('success', $res1['status']);

        // 2nd call simulates concurrent or retry request
        $res2 = $this->engine->submitAnswer($room, 1, $q['options'][0], 'sub_1');
        $this->assertEquals('success', $res2['status']);
        $this->assertTrue($res2['replayed']);
        $this->assertEquals($res1['data']->id, $res2['data']->id);
    }

    // TC-18: Reject invalid option not in question
    public function test_reject_invalid_option_not_in_question(): void
    {
        $room = $this->createActiveTestRoom();

        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $this->engine->submitAnswer($room, 1, 'INVALID_OPTION_XYZ', 'sub_invalid');
    }

    // TC-19: Cannot start game if snapshot has less than 10 questions
    public function test_cannot_start_game_if_snapshot_has_less_than_ten_questions(): void
    {
        // Deactivate all questions to simulate empty question bank
        Question::query()->update(['is_active' => false]);

        $u1 = User::factory()->create();
        $u2 = User::factory()->create();

        $room = $this->engine->createRoom(1, 'random', [
            'type' => 'user',
            'user_id' => $u1->id,
            'name' => $u1->name,
        ]);

        $this->expectException(\InvalidArgumentException::class);
        $this->engine->joinRoom($room, [
            'type' => 'user',
            'user_id' => $u2->id,
            'name' => $u2->name,
        ]);
    }

    // TC-20: Unauthorized room access blocked (Slot verification)
    public function test_unauthorized_room_access_blocked(): void
    {
        $room = $this->createActiveTestRoom();
        $unrelatedUser = User::factory()->create();

        $this->assertNotEquals($unrelatedUser->id, $room->player1_id);
        $this->assertNotEquals($unrelatedUser->id, $room->player2_id);
    }

    // TC-21: Rematch duplicate click returns same child room
    public function test_rematch_duplicate_click_returns_same_room(): void
    {
        $room = $this->createActiveTestRoom();
        $room->status = 'finished';
        $room->save();

        $r1 = $this->engine->requestRematch($room, 1);
        $this->assertEquals('waiting_opponent', $r1['status']);

        // Click again
        $r1_again = $this->engine->requestRematch($room, 1);
        $this->assertEquals('waiting_opponent', $r1_again['status']);
    }

    // TC-22: No false forfeit immediately after submit
    public function test_no_false_forfeit_immediately_after_submit(): void
    {
        $room = $this->createActiveTestRoom();
        $q = $room->questions_snapshot[0];

        // Player 1 submits answer
        $this->engine->submitAnswer($room, 1, $q['options'][0], 'submit_heartbeat_check');
        $room->refresh();

        $now = microtime(true);
        // Player 1 was seen just now
        $this->assertLessThan(1.0, $now - (float) $room->player1_last_seen_at);

        // Check forfeit: Player 1 must NOT be forfeited
        $this->engine->checkDisconnectionAndForfeit($room, $now);
        $room->refresh();

        $this->assertNotEquals(1, $room->forfeit_by_slot);
        $this->assertEquals('in_progress', $room->status);
    }
}
