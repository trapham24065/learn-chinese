<?php

namespace Tests\Feature;

use App\Models\DuelRoom;
use App\Models\Question;
use App\Models\User;
use App\Services\Duel\DuelAuthService;
use App\Services\Duel\DuelGameEngine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DuelArenaViewTest extends TestCase
{
    use RefreshDatabase;

    protected DuelGameEngine $engine;
    protected DuelAuthService $authService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->engine = app(DuelGameEngine::class);
        $this->authService = app(DuelAuthService::class);

        // Seed 15 câu hỏi HSK 3 cho snapshot
        for ($i = 1; $i <= 15; $i++) {
            Question::create([
                'hsk_level' => 3,
                'question' => "Câu hỏi thi đấu số {$i}",
                'options' => ['Đáp án A', 'Đáp án B', 'Đáp án C', 'Đáp án D'],
                'correct_answer' => 'Đáp án A',
                'explanation' => "Giải thích câu {$i}",
                'is_active' => true,
            ]);
        }
    }

    public function test_lobby_page_can_be_rendered(): void
    {
        $response = $this->get(route('duel.lobby'));

        $response->assertStatus(200);
        $response->assertSee('Đấu Trường Chinese Duel');
        $response->assertSee('Ghép Trận Nhanh');
        $response->assertSee('Tạo Phòng Thách Đấu');
        $response->assertSee('Nhập Mã Phòng');
    }

    public function test_arena_returns_404_if_room_not_found(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/duel/room/NONEXISTENT');

        $response->assertStatus(404);
    }

    public function test_unauthenticated_guest_is_redirected_to_login_when_accessing_arena(): void
    {
        $user1 = User::factory()->create(['name' => 'Người Thách Đấu']);

        $room = $this->engine->createRoom(3, 'custom', [
            'type' => 'user',
            'user_id' => $user1->id,
            'name' => $user1->name,
            'avatar' => null,
        ]);

        $response = $this->get("/duel/room/{$room->room_code}");

        $response->assertStatus(302);
        $response->assertRedirect(route('login'));
    }

    public function test_invited_logged_in_user_auto_joins_waiting_room(): void
    {
        $user1 = User::factory()->create(['name' => 'Người Thách Đấu']);
        $user2 = User::factory()->create(['name' => 'Người Tham Gia']);

        $room = $this->engine->createRoom(3, 'custom', [
            'type' => 'user',
            'user_id' => $user1->id,
            'name' => $user1->name,
            'avatar' => null,
        ]);

        // User 2 đã đăng nhập bấm vào đường link phòng đấu
        $response = $this->actingAs($user2)->get("/duel/room/{$room->room_code}");

        $response->assertStatus(200);

        $room->refresh();
        $this->assertEquals('countdown', $room->status);
        $this->assertEquals($user2->id, $room->player2_id);
        $this->assertEquals('Người Tham Gia', $room->player2_name);
    }

    public function test_unauthorized_third_party_is_blocked_from_full_room(): void
    {
        $user1 = User::factory()->create(['name' => 'Player 1']);
        $user2 = User::factory()->create(['name' => 'Player 2']);
        $intruder = User::factory()->create(['name' => 'Intruder']);

        $room = $this->engine->createRoom(3, 'custom', [
            'type' => 'user',
            'user_id' => $user1->id,
            'name' => $user1->name,
            'avatar' => null,
        ]);

        $this->engine->joinRoom($room, [
            'type' => 'user',
            'user_id' => $user2->id,
            'name' => $user2->name,
            'avatar' => null,
        ]);

        // Kẻ thứ ba cố tình truy cập vào phòng đã đủ 2 người
        $response = $this->actingAs($intruder)->get("/duel/room/{$room->room_code}");

        $response->assertStatus(403);
    }

    public function test_existing_player_can_refresh_and_view_active_arena(): void
    {
        $user1 = User::factory()->create(['name' => 'Player 1']);
        $user2 = User::factory()->create(['name' => 'Player 2']);

        $room = $this->engine->createRoom(3, 'custom', [
            'type' => 'user',
            'user_id' => $user1->id,
            'name' => $user1->name,
            'avatar' => null,
        ]);

        $room = $this->engine->joinRoom($room, [
            'type' => 'user',
            'user_id' => $user2->id,
            'name' => $user2->name,
            'avatar' => null,
        ]);

        // Cho countdown trôi qua để vào in_progress
        $room->phase_ends_at = microtime(true) - 1.0;
        $room->save();

        // Player 2 refresh trang
        $response = $this->actingAs($user2)->get("/duel/room/{$room->room_code}");

        $response->assertStatus(200);
        $response->assertSee($room->room_code);
        $response->assertSee('Tug-of-War Kéo Co');
        $response->assertSee('/ 10');
        $response->assertSee('BẠN'); // Slot 2 của user 2 phải có huy hiệu BẠN
    }
}
