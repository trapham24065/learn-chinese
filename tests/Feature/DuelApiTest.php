<?php

namespace Tests\Feature;

use App\Models\DuelRoom;
use App\Models\Question;
use App\Models\User;
use App\Services\Duel\DuelAuthService;
use App\Services\Duel\DuelGameEngine;
use Database\Seeders\HskMockExamQuestionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class DuelApiTest extends TestCase
{
    use RefreshDatabase;

    protected DuelGameEngine $engine;
    protected DuelAuthService $authService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(HskMockExamQuestionSeeder::class);
        $this->engine = new DuelGameEngine();
        $this->authService = new DuelAuthService();
        RateLimiter::clear('duel-join-fail:127.0.0.1');
    }

    public function test_authenticated_user_can_create_private_room(): void
    {
        $user = User::factory()->create(['name' => 'Tra Pham']);

        $response = $this->actingAs($user)->postJson(route('duel.room.create'), [
            'hsk_level' => 3,
            'mode' => 'friend',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'status' => 'created',
                'hsk_level' => 3,
                'mode' => 'friend',
            ])
            ->assertJsonStructure(['room_code', 'invite_url']);

        $room = DuelRoom::where('room_code', $response->json('room_code'))->first();
        $this->assertNotNull($room);
        $this->assertEquals($user->id, $room->player1_id);
        $this->assertEquals('user', $room->player1_type);
        $this->assertEquals('waiting', $room->status);
    }

    public function test_guest_cannot_create_private_room_and_receives_401(): void
    {
        $response = $this->postJson(route('duel.room.create'), [
            'hsk_level' => 2,
            'mode' => 'friend',
        ]);

        $response->assertStatus(401);
    }

    public function test_user_can_join_room_via_invite_code(): void
    {
        $host = User::factory()->create(['name' => 'Host User']);
        $joiner = User::factory()->create(['name' => 'Joiner User']);

        $room = $this->engine->createRoom(3, 'friend', [
            'type' => 'user',
            'user_id' => $host->id,
            'name' => $host->name,
        ]);

        $response = $this->actingAs($joiner)->postJson(route('duel.room.join'), [
            'room_code' => $room->room_code,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'joined',
                'room_code' => $room->room_code,
                'role' => 'player2',
            ]);

        $room->refresh();
        $this->assertEquals('countdown', $room->status);
        $this->assertEquals($joiner->id, $room->player2_id);
        $this->assertNotNull($room->questions_snapshot);
        $this->assertCount(10, $room->questions_snapshot);
    }

    public function test_join_room_fails_with_invalid_or_expired_code(): void
    {
        $joiner = User::factory()->create();

        $response = $this->actingAs($joiner)->postJson(route('duel.room.join'), [
            'room_code' => 'INVALID',
        ]);

        $response->assertStatus(404)
            ->assertJsonStructure(['message']);
    }

    public function test_join_room_fails_if_room_already_started(): void
    {
        $u1 = User::factory()->create();
        $u2 = User::factory()->create();
        $u3 = User::factory()->create();

        $room = $this->engine->createRoom(3, 'friend', ['type' => 'user', 'user_id' => $u1->id, 'name' => $u1->name]);
        $this->engine->joinRoom($room, ['type' => 'user', 'user_id' => $u2->id, 'name' => $u2->name]);

        // u3 tries to join an already full countdown room
        $response = $this->actingAs($u3)->postJson(route('duel.room.join'), [
            'room_code' => $room->room_code,
        ]);

        $response->assertStatus(400)
            ->assertJson(['message' => 'Phòng đấu đã đủ người hoặc trận đấu đã bắt đầu.']);
    }

    public function test_public_matchmaking_pairs_two_players_into_one_room(): void
    {
        $p1 = User::factory()->create(['name' => 'Player One']);
        $p2 = User::factory()->create(['name' => 'Player Two']);

        // Player 1 matchmakes -> creates waiting room
        $res1 = $this->actingAs($p1)->postJson(route('duel.matchmake'), [
            'hsk_level' => 4,
        ]);

        $res1->assertStatus(200)
            ->assertJson([
                'status' => 'waiting',
                'role' => 'player1',
            ]);

        $code = $res1->json('room_code');

        // Player 2 matchmakes for same level -> joins Player 1's room
        $res2 = $this->actingAs($p2)->postJson(route('duel.matchmake'), [
            'hsk_level' => 4,
        ]);

        $res2->assertStatus(200)
            ->assertJson([
                'status' => 'matched',
                'room_code' => $code,
                'role' => 'player2',
            ]);

        $room = DuelRoom::where('room_code', $code)->first();
        $this->assertEquals('countdown', $room->status);
        $this->assertEquals($p1->id, $room->player1_id);
        $this->assertEquals($p2->id, $room->player2_id);
    }

    public function test_public_matchmaking_does_not_match_player_with_self(): void
    {
        $player = User::factory()->create(['name' => 'Solo Player']);

        // 1st matchmake
        $res1 = $this->actingAs($player)->postJson(route('duel.matchmake'), [
            'hsk_level' => 3,
        ]);
        $code1 = $res1->json('room_code');

        // Player clicks matchmake again -> should return existing waiting room, NOT match with self
        $res2 = $this->actingAs($player)->postJson(route('duel.matchmake'), [
            'hsk_level' => 3,
        ]);

        $res2->assertStatus(200)
            ->assertJson([
                'status' => 'waiting',
                'room_code' => $code1,
                'role' => 'player1',
            ]);

        $room = DuelRoom::where('room_code', $code1)->first();
        $this->assertEquals('waiting', $room->status);
        $this->assertNull($room->player2_id);
    }

    public function test_state_endpoint_blocks_unauthorized_user(): void
    {
        $u1 = User::factory()->create();
        $u2 = User::factory()->create();
        $stranger = User::factory()->create();

        $room = $this->engine->createRoom(3, 'random', ['type' => 'user', 'user_id' => $u1->id, 'name' => $u1->name]);
        $this->engine->joinRoom($room, ['type' => 'user', 'user_id' => $u2->id, 'name' => $u2->name]);

        $response = $this->actingAs($stranger)->getJson(route('duel.room.state', $room->room_code));
        $response->assertStatus(403);
    }

    public function test_state_endpoint_masks_answer_during_answering_phase(): void
    {
        $u1 = User::factory()->create();
        $u2 = User::factory()->create();

        $room = $this->engine->createRoom(3, 'random', ['type' => 'user', 'user_id' => $u1->id, 'name' => $u1->name]);
        $room = $this->engine->joinRoom($room, ['type' => 'user', 'user_id' => $u2->id, 'name' => $u2->name]);

        // Set countdown to expired so state polling advances to in_progress / answering
        $now = microtime(true);
        $room->phase_ends_at = $now - 1.0;
        $room->save();

        $response = $this->actingAs($u1)->getJson(route('duel.room.state', $room->room_code));

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'in_progress',
                'round_phase' => 'answering',
                'current_round' => 1,
            ]);

        $currentQ = $response->json('current_question');
        $this->assertNotNull($currentQ);
        $this->assertArrayNotHasKey('correct_answer', $currentQ);
        $this->assertArrayNotHasKey('explanation', $currentQ);
    }

    public function test_answer_endpoint_submits_answer_and_handles_idempotency(): void
    {
        $u1 = User::factory()->create();
        $u2 = User::factory()->create();

        $room = $this->engine->createRoom(3, 'random', ['type' => 'user', 'user_id' => $u1->id, 'name' => $u1->name]);
        $room = $this->engine->joinRoom($room, ['type' => 'user', 'user_id' => $u2->id, 'name' => $u2->name]);

        // Advance room into answering phase
        $now = microtime(true);
        $room->status = 'in_progress';
        $room->current_round = 1;
        $room->round_phase = 'answering';
        $room->phase_started_at = $now;
        $room->phase_ends_at = $now + 10.0;
        $room->save();

        $q = $room->questions_snapshot[0];
        $chosen = $q['options'][0];

        // 1. Submit answer
        $res1 = $this->actingAs($u1)->postJson(route('duel.room.answer', $room->room_code), [
            'answer' => $chosen,
            'idempotency_key' => 'idemp_key_1',
        ]);

        $res1->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ]);

        // 2. Retry with same key -> returns replayed true
        $res2 = $this->actingAs($u1)->postJson(route('duel.room.answer', $room->room_code), [
            'answer' => $chosen,
            'idempotency_key' => 'idemp_key_1',
        ]);

        $res2->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'replayed' => true,
            ]);

        // 3. Reject invalid option not in options list
        $resInvalid = $this->actingAs($u2)->postJson(route('duel.room.answer', $room->room_code), [
            'answer' => 'DEFINITELY_NOT_AN_OPTION_XYZ',
            'idempotency_key' => 'idemp_key_2',
        ]);

        $resInvalid->assertStatus(422);
    }

    public function test_rematch_api_coordinates_new_room_for_both_players(): void
    {
        $u1 = User::factory()->create(['name' => 'Player 1']);
        $u2 = User::factory()->create(['name' => 'Player 2']);

        $room = $this->engine->createRoom(3, 'friend', ['type' => 'user', 'user_id' => $u1->id, 'name' => $u1->name]);
        $this->engine->joinRoom($room, ['type' => 'user', 'user_id' => $u2->id, 'name' => $u2->name]);

        // Finish match
        $room->status = 'finished';
        $room->save();

        // Player 1 requests rematch
        $res1 = $this->actingAs($u1)->postJson(route('duel.room.rematch', $room->room_code));
        $res1->assertStatus(200)
            ->assertJson(['status' => 'waiting_opponent']);

        // Player 2 accepts rematch
        $res2 = $this->actingAs($u2)->postJson(route('duel.room.rematch', $room->room_code));
        $res2->assertStatus(200)
            ->assertJson(['status' => 'ready'])
            ->assertJsonStructure(['room_code']);

        $childCode = $res2->json('room_code');
        $this->assertNotEquals($room->room_code, $childCode);

        // Player 1 calls rematch again -> receives the ready child room
        $res3 = $this->actingAs($u1)->postJson(route('duel.room.rematch', $room->room_code));
        $res3->assertStatus(200)
            ->assertJson([
                'status' => 'ready',
                'room_code' => $childCode,
            ]);

        $childRoom = DuelRoom::where('room_code', $childCode)->first();
        $this->assertNotNull($childRoom);
        $this->assertEquals('countdown', $childRoom->status);
        $this->assertEquals($room->id, $childRoom->parent_room_id);
    }
}
