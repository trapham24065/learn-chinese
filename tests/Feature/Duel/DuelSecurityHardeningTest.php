<?php

namespace Tests\Feature\Duel;

use App\Models\DuelRoom;
use App\Models\Question;
use App\Models\User;
use App\Services\Duel\DuelGameEngine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DuelSecurityHardeningTest extends TestCase
{
    use RefreshDatabase;

    protected DuelGameEngine $engine;

    protected function setUp(): void
    {
        parent::setUp();
        $this->engine = app(DuelGameEngine::class);

        // Seed 15 câu hỏi HSK 1, 2, 3
        foreach ([1, 2, 3] as $lvl) {
            for ($i = 1; $i <= 12; $i++) {
                Question::create([
                    'hsk_level' => $lvl,
                    'question' => "Câu hỏi HSK {$lvl} số {$i}",
                    'options' => ['Đáp án A', 'Đáp án B', 'Đáp án C', 'Đáp án D'],
                    'correct_answer' => 'Đáp án A',
                    'explanation' => "Giải thích câu {$i}",
                    'is_active' => true,
                ]);
            }
        }
    }

    /**
     * 1. Lobby công khai cho khách xem, hiển thị Live Counters và Room Browser
     */
    public function test_public_lobby_is_accessible_without_auth_with_live_counters_and_room_browser(): void
    {
        $host = User::factory()->create(['name' => 'Host User']);

        // Tạo 1 phòng chờ HSK 2 và 2 phòng chờ HSK 3
        $this->engine->createRoom(2, 'random', [
            'type' => 'user',
            'user_id' => $host->id,
            'name' => $host->name,
            'ip' => '127.0.0.1',
        ]);

        $res = $this->get(route('duel.lobby'));

        $res->assertStatus(200);
        $res->assertSee('Đấu Trường Chinese Duel');
        $res->assertSee('Phòng Chờ Đang Tìm Đối Thủ (Room Browser)');
        $res->assertSee('Host User');
        $res->assertSee('HSK 2');
    }

    /**
     * 2. Khách vãng lai gọi các API thi đấu bị chặn 401 Unauthorized ở server-side
     */
    public function test_guest_cannot_matchmake_or_create_room_server_side_enforced(): void
    {
        // matchmake
        $res1 = $this->postJson(route('duel.matchmake'), ['hsk_level' => 1]);
        $res1->assertStatus(401);

        // create room
        $res2 = $this->postJson(route('duel.room.create'), ['hsk_level' => 1, 'mode' => 'friend']);
        $res2->assertStatus(401);

        // join room
        $res3 = $this->postJson(route('duel.room.join'), ['room_code' => 'TESTCODE']);
        $res3->assertStatus(401);
    }

    /**
     * 3. User không thể tự ghép trận hoặc tự đấu với chính mình
     */
    public function test_user_cannot_self_match_or_join_own_room_as_player2(): void
    {
        $user = User::factory()->create(['name' => 'Single User']);

        $room = $this->engine->createRoom(1, 'friend', [
            'type' => 'user',
            'user_id' => $user->id,
            'name' => $user->name,
            'ip' => '127.0.0.1',
        ]);

        // Cố tình gọi joinRoom trực tiếp với cùng user_id
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Bạn không thể tự tham gia thi đấu với chính tài khoản của mình.');

        $this->engine->joinRoom($room, [
            'type' => 'user',
            'user_id' => $user->id,
            'name' => $user->name,
            'ip' => '127.0.0.1',
        ]);
    }

    /**
     * 4. User đang có trận đấu hoạt động không thể tạo hoặc ghép phòng khác trên TOÀN BỘ cấp HSK
     */
    public function test_user_with_active_match_cannot_create_or_matchmake_another_room_cross_hsk(): void
    {
        $u1 = User::factory()->create(['name' => 'Active Player']);
        $u2 = User::factory()->create(['name' => 'Opponent']);

        // U1 đang trong trận HSK 1
        $room = $this->engine->createRoom(1, 'random', [
            'type' => 'user',
            'user_id' => $u1->id,
            'name' => $u1->name,
            'ip' => '127.0.0.1',
        ]);
        $this->engine->joinRoom($room, [
            'type' => 'user',
            'user_id' => $u2->id,
            'name' => $u2->name,
            'ip' => '127.0.0.2',
        ]);

        $room->refresh();
        $this->assertEquals('countdown', $room->status);

        // U1 cố tình gọi matchmake HSK 2 -> Trả về 409 Conflict báo đã có active match
        $resMatchmake = $this->actingAs($u1)->postJson(route('duel.matchmake'), [
            'hsk_level' => 2,
        ]);
        $resMatchmake->assertStatus(409)
            ->assertJson([
                'status' => 'active_match_exists',
                'room_code' => $room->room_code,
            ]);

        // U1 cố tình gọi createRoom HSK 3 -> Trả về 409
        $resCreate = $this->actingAs($u1)->postJson(route('duel.room.create'), [
            'hsk_level' => 3,
            'mode' => 'friend',
        ]);
        $resCreate->assertStatus(409);
    }

    /**
     * 5. Chủ phòng có thể hủy phòng chờ tìm trận cleanly, không bị trừ điểm/phạt
     */
    public function test_user_can_cancel_their_waiting_room_cleanly_without_penalty(): void
    {
        $u1 = User::factory()->create(['name' => 'Host Cancel']);

        $resCreate = $this->actingAs($u1)->postJson(route('duel.room.create'), [
            'hsk_level' => 1,
            'mode' => 'random',
        ]);
        $code = $resCreate->json('room_code');

        $resCancel = $this->actingAs($u1)->postJson(route('duel.matchmake.cancel'), [
            'room_code' => $code,
        ]);

        $resCancel->assertStatus(200)
            ->assertJson(['success' => true]);

        $room = DuelRoom::where('room_code', $code)->first();
        $this->assertEquals('abandoned', $room->status);
        $this->assertNull($room->forfeit_by_slot); // Hủy phòng chờ không gán forfeit slot phạt
    }

    /**
     * 6. Người ngoài không thể hủy phòng chờ của người khác
     */
    public function test_non_owner_cannot_cancel_waiting_room(): void
    {
        $u1 = User::factory()->create(['name' => 'Host Real']);
        $stranger = User::factory()->create(['name' => 'Stranger']);

        $resCreate = $this->actingAs($u1)->postJson(route('duel.room.create'), [
            'hsk_level' => 1,
            'mode' => 'random',
        ]);
        $code = $resCreate->json('room_code');

        $resCancel = $this->actingAs($stranger)->postJson(route('duel.matchmake.cancel'), [
            'room_code' => $code,
        ]);

        $resCancel->assertStatus(400); // InvalidArgumentException: Bạn không phải chủ phòng chờ này.
    }

    /**
     * 7. Người chơi trong trận có thể chủ động đầu hàng, đối thủ lập tức giành chiến thắng
     */
    public function test_user_can_surrender_in_match_opponent_wins_instantly(): void
    {
        $u1 = User::factory()->create(['name' => 'Player 1']);
        $u2 = User::factory()->create(['name' => 'Player 2']);

        $room = $this->engine->createRoom(2, 'friend', [
            'type' => 'user',
            'user_id' => $u1->id,
            'name' => $u1->name,
            'ip' => '127.0.0.1',
        ]);
        $room = $this->engine->joinRoom($room, [
            'type' => 'user',
            'user_id' => $u2->id,
            'name' => $u2->name,
            'ip' => '127.0.0.2',
        ]);

        $room->status = 'in_progress';
        $room->current_round = 3;
        $room->save();

        // U1 bấm đầu hàng
        $res = $this->actingAs($u1)->postJson("/duel/room/{$room->room_code}/surrender");
        $res->assertStatus(200)
            ->assertJson([
                'success' => true,
                'status' => 'finished',
                'forfeit_by_slot' => 1,
            ]);

        $room->refresh();
        $this->assertEquals('finished', $room->status);
        $this->assertEquals(1, $room->forfeit_by_slot);
        $this->assertEquals(2, $room->winner_slot); // Slot 2 thắng
    }

    /**
     * 8. Thoát trận sớm ở Hiệp 1 (< 15s): Người thắng nhận +15 XP an ủi, người đầu hàng nhận 0 XP
     */
    public function test_early_forfeit_in_round_one_awards_consolation_fifteen_xp(): void
    {
        $u1 = User::factory()->create();
        $u2 = User::factory()->create();

        $room = $this->engine->createRoom(2, 'friend', [
            'type' => 'user',
            'user_id' => $u1->id,
            'name' => $u1->name,
            'ip' => '127.0.0.1',
        ]);
        $room = $this->engine->joinRoom($room, [
            'type' => 'user',
            'user_id' => $u2->id,
            'name' => $u2->name,
            'ip' => '127.0.0.2',
        ]);

        $room->status = 'in_progress';
        $room->current_round = 1;
        $room->started_at = now(); // Vừa mới bắt đầu
        $room->save();

        // U1 đầu hàng
        $this->actingAs($u1)->postJson("/duel/room/{$room->room_code}/surrender");

        $u1Xp = \App\Models\UserLearningStat::where('user_id', $u1->id)->value('total_xp') ?? 0;
        $u2Xp = \App\Models\UserLearningStat::where('user_id', $u2->id)->value('total_xp') ?? 0;

        $this->assertEquals(0, $u1Xp); // U1 nhận 0 XP
        $this->assertEquals(15, $u2Xp); // U2 nhận +15 XP an ủi (vì < 15s hiệp 1)
    }

    /**
     * 9. Thoát trận ở Hiệp 2 trở lên: Người thắng nhận trọn vẹn +40 XP
     */
    public function test_later_forfeit_in_round_two_awards_full_forty_xp_to_winner(): void
    {
        $u1 = User::factory()->create();
        $u2 = User::factory()->create();

        $room = $this->engine->createRoom(2, 'friend', [
            'type' => 'user',
            'user_id' => $u1->id,
            'name' => $u1->name,
            'ip' => '127.0.0.1',
        ]);
        $room = $this->engine->joinRoom($room, [
            'type' => 'user',
            'user_id' => $u2->id,
            'name' => $u2->name,
            'ip' => '127.0.0.2',
        ]);

        $room->status = 'in_progress';
        $room->current_round = 2; // Hiệp 2
        $room->started_at = now()->subSeconds(30);
        $room->save();

        // U1 đầu hàng
        $this->actingAs($u1)->postJson("/duel/room/{$room->room_code}/surrender");

        $u1Xp = \App\Models\UserLearningStat::where('user_id', $u1->id)->value('total_xp') ?? 0;
        $u2Xp = \App\Models\UserLearningStat::where('user_id', $u2->id)->value('total_xp') ?? 0;

        $this->assertEquals(0, $u1Xp); // 0 XP
        $this->assertEquals(40, $u2Xp); // +40 XP đầy đủ
    }

    /**
     * 10. Audit IP và cờ is_same_ip được lưu trữ chính xác
     */
    public function test_ip_audit_fields_recorded_and_is_same_ip_flagged_correctly(): void
    {
        $u1 = User::factory()->create();
        $u2 = User::factory()->create();

        // Trường hợp 1: Hai người khác IP
        $roomDiff = $this->engine->createRoom(1, 'random', [
            'type' => 'user',
            'user_id' => $u1->id,
            'name' => $u1->name,
            'ip' => '113.190.23.45',
        ]);
        $roomDiff = $this->engine->joinRoom($roomDiff, [
            'type' => 'user',
            'user_id' => $u2->id,
            'name' => $u2->name,
            'ip' => '113.190.23.99',
        ]);

        $this->assertEquals('113.190.23.45', $roomDiff->player1_ip);
        $this->assertEquals('113.190.23.99', $roomDiff->player2_ip);
        $this->assertFalse((bool) $roomDiff->is_same_ip);

        // Trường hợp 2: Hai người cùng IP (ví dụ Wi-Fi gia đình/ký túc xá)
        $u3 = User::factory()->create();
        $u4 = User::factory()->create();

        $roomSame = $this->engine->createRoom(1, 'random', [
            'type' => 'user',
            'user_id' => $u3->id,
            'name' => $u3->name,
            'ip' => '42.112.88.10',
        ]);
        $roomSame = $this->engine->joinRoom($roomSame, [
            'type' => 'user',
            'user_id' => $u4->id,
            'name' => $u4->name,
            'ip' => '42.112.88.10',
        ]);

        $this->assertEquals('42.112.88.10', $roomSame->player1_ip);
        $this->assertEquals('42.112.88.10', $roomSame->player2_ip);
        $this->assertTrue((bool) $roomSame->is_same_ip);
    }
}
