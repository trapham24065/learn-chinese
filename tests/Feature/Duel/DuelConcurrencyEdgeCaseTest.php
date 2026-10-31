<?php

namespace Tests\Feature\Duel;

use App\Console\Commands\SyncStaleDuelRooms;
use App\Models\DuelRoom;
use App\Models\DuelRoundAnswer;
use App\Models\User;
use App\Services\Duel\DuelAuthService;
use App\Services\Duel\DuelGameEngine;
use Database\Seeders\HskMockExamQuestionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DuelConcurrencyEdgeCaseTest extends TestCase
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
    }

    /**
     * Tạo một phòng đấu đang ở hiệp 1 (in_progress, answering) giữa 2 user.
     */
    protected function createActiveRoom(): array
    {
        $u1 = User::factory()->create(['name' => 'Gamer 1']);
        $u2 = User::factory()->create(['name' => 'Gamer 2']);

        $room = $this->engine->createRoom(3, 'custom', [
            'type' => 'user',
            'user_id' => $u1->id,
            'name' => $u1->name,
        ]);

        $room = $this->engine->joinRoom($room, [
            'type' => 'user',
            'user_id' => $u2->id,
            'name' => $u2->name,
        ]);

        // Kích hoạt từ countdown sang in_progress (Round 1 answering)
        $now = microtime(true);
        $this->engine->advanceStateLocked($room, $now + 3.1);
        $room->phase_started_at = $now;
        $room->phase_ends_at = $now + 10.0;
        $room->save();

        return [$room->fresh(), $u1, $u2];
    }

    /**
     * EDGE CASE 1: NỘP ĐÁP ÁN SÁT DEADLINE (9.95s HỢP LỆ VS 10.05s TIMEOUT)
     */
    public function test_sub_second_deadline_boundary_api_behavior(): void
    {
        [$room, $u1, $u2] = $this->createActiveRoom();
        $code = $room->room_code;
        $q = $room->questions_snapshot[0];
        $correct = $q['correct_answer'];

        // 1. Giả lập Người 1 nộp câu trả lời tại 9.95s (trước deadline 10s: now <= phase_ends_at)
        $now = microtime(true);
        $room->phase_started_at = $now - 9.95;
        $room->phase_ends_at = $now + 0.05; // Còn 0.05s
        $room->save();

        $res1 = $this->actingAs($u1)->postJson("/duel/room/{$code}/answer", [
            'answer' => $correct,
            'idempotency_key' => 'edge_key_p1_r1',
        ]);

        $res1->assertStatus(200);
        $this->assertEquals('success', $res1->json('status'));
        $this->assertTrue($res1->json('data.is_correct'));
        $this->assertGreaterThanOrEqual(75, $res1->json('data.score_awarded'));

        // Phòng vẫn ở answering vì Người 2 chưa nộp
        $room->refresh();
        $this->assertEquals('answering', $room->round_phase);

        // 2. Giả lập Người 2 nộp câu trả lời trễ (10.05s, now > phase_ends_at)
        $nowLate = microtime(true);
        $room->phase_ends_at = $nowLate - 0.05; // Đã quá hạn 0.05s
        $room->save();

        $res2 = $this->actingAs($u2)->postJson("/duel/room/{$code}/answer", [
            'answer' => $correct,
            'idempotency_key' => 'edge_key_p2_r1',
        ]);

        // API trả về 200 với trạng thái timeout
        $res2->assertStatus(200);
        $this->assertEquals('timeout', $res2->json('status'));
        $this->assertNull($res2->json('data'));

        // Cả hai slot đều đã có bản ghi (Người 2 được auto-fill timeout), phòng tự động chuyển sang revealing
        $room->refresh();
        $this->assertEquals('revealing', $room->round_phase);

        $ans2 = DuelRoundAnswer::where('duel_room_id', $room->id)
            ->where('round_number', 1)
            ->where('player_slot', 2)
            ->first();
        $this->assertNotNull($ans2);
        $this->assertNull($ans2->selected_answer);
        $this->assertFalse($ans2->is_correct);
        $this->assertEquals(0, $ans2->score_awarded);
    }

    /**
     * EDGE CASE 2: HAI NGƯỜI CHƠI NỘP ĐÁP ÁN ĐỒNG THỜI TRONG CÙNG MỘT GIÂY (SUB-SECOND CONCURRENCY)
     */
    public function test_two_player_sub_second_concurrent_submissions(): void
    {
        [$room, $u1, $u2] = $this->createActiveRoom();
        $code = $room->room_code;
        $q = $room->questions_snapshot[0];
        $correct = $q['correct_answer'];

        // Chọn một đáp án sai hợp lệ nằm trong danh sách options của câu hỏi
        $options = $q['options'];
        $wrong = null;
        foreach ($options as $opt) {
            if ($opt !== $correct) {
                $wrong = $opt;
                break;
            }
        }
        $this->assertNotNull($wrong);

        // Người 1 nộp trước ở T = 1.2s
        $resP1 = $this->actingAs($u1)->postJson("/duel/room/{$code}/answer", [
            'answer' => $correct,
            'idempotency_key' => 'sub_p1_key',
        ]);
        $resP1->assertStatus(200);

        // State check từ Người 2 trước khi Người 2 nộp:
        // Đáp án và điểm của Người 1 PHẢI BỊ CHE GIẤU (opponent_submitted = true, opp_answer = null)
        $stateP2Before = $this->actingAs($u2)->getJson("/duel/room/{$code}/state");
        $stateP2Before->assertStatus(200);
        $this->assertTrue($stateP2Before->json('opponent_submitted'));
        $this->assertNull($stateP2Before->json('opponent_selected'));
        $this->assertEquals('answering', $stateP2Before->json('round_phase'));

        // Người 2 nộp ở T = 1.5s
        $resP2 = $this->actingAs($u2)->postJson("/duel/room/{$code}/answer", [
            'answer' => $wrong,
            'idempotency_key' => 'sub_p2_key',
        ]);
        $resP2->assertStatus(200);
        $this->assertEquals('success', $resP2->json('status'));
        $this->assertFalse($resP2->json('data.is_correct'));
        $this->assertEquals(0, $resP2->json('data.score_awarded'));

        // Sau khi Người 2 nộp, phòng lập tức chuyển sang revealing
        $room->refresh();
        $this->assertEquals('revealing', $room->round_phase);

        // State check sau khi chuyển revealing: Cả hai bên đều xem được đáp án và giải thích
        $stateP2After = $this->actingAs($u2)->getJson("/duel/room/{$code}/state");
        $stateP2After->assertStatus(200);
        $this->assertEquals('revealing', $stateP2After->json('round_phase'));
        $this->assertEquals($correct, $stateP2After->json('questions.0.correct_answer'));
        $this->assertNotNull($stateP2After->json('questions.0.explanation'));
    }

    /**
     * EDGE CASE 3: CÔ LẬP PHÒNG ĐẤU VÀ CHỐNG TRUY CẬP TRÁI PHÉP (CROSS-ROOM ISOLATION)
     */
    public function test_cookie_tampering_and_cross_room_isolation(): void
    {
        $u1 = User::factory()->create();
        $u2 = User::factory()->create();
        $stranger = User::factory()->create();

        $createRes1 = $this->actingAs($u1)->postJson('/duel/room/create', ['hsk_level' => 1, 'mode' => 'friend']);
        $code1 = $createRes1->json('room_code');

        $createRes2 = $this->actingAs($u2)->postJson('/duel/room/create', ['hsk_level' => 2, 'mode' => 'friend']);
        $code2 = $createRes2->json('room_code');

        // 1. User chưa đăng nhập cố gọi endpoint thi đấu -> 401 Unauthorized
        Auth::guard('web')->logout();
        $guestRes = $this->getJson("/duel/room/{$code1}/state");
        $guestRes->assertStatus(401);

        // 2. User 1 (thuộc phòng 1) cố gọi state của Phòng 2 -> 403 Forbidden
        $crossRes = $this->actingAs($u1)->getJson("/duel/room/{$code2}/state");
        $crossRes->assertStatus(403);

        // 3. User khác (người ngoài) cố tình gọi state của Phòng 1 -> 403 Forbidden
        $strangerRes = $this->actingAs($stranger)->getJson("/duel/room/{$code1}/state");
        $strangerRes->assertStatus(403);
    }

    /**
     * EDGE CASE 4: CONCURRENT POLLING KHI HẾT PHA REVEAL — TĂNG VÒNG CHÍNH XÁC ĐÚNG 1 LẦN
     */
    public function test_concurrent_polling_during_phase_transition_advances_round_exactly_once(): void
    {
        [$room, $u1, $u2] = $this->createActiveRoom();
        $code = $room->room_code;
        $now = microtime(true);

        // Cả hai đều đã nộp đáp án hiệp 1 -> Chuyển sang revealing
        DuelRoundAnswer::create([
            'duel_room_id' => $room->id,
            'round_number' => 1,
            'player_slot' => 1,
            'selected_answer' => 'A',
            'is_correct' => true,
            'answer_time_seconds' => 1.5,
            'score_awarded' => 85,
            'idempotency_key' => 'p1_r1',
            'answered_at' => $now - 2.0,
        ]);

        DuelRoundAnswer::create([
            'duel_room_id' => $room->id,
            'round_number' => 1,
            'player_slot' => 2,
            'selected_answer' => 'B',
            'is_correct' => true,
            'answer_time_seconds' => 2.0,
            'score_awarded' => 80,
            'idempotency_key' => 'p2_r1',
            'answered_at' => $now - 1.5,
        ]);

        $room->round_phase = 'revealing';
        $room->current_round = 1;
        $room->phase_started_at = $now - 3.5;
        $room->phase_ends_at = $now - 0.5; // Pha revealing 3.0s đã kết thúc 0.5s trước
        $room->save();

        // Giả lập 2 request polling từ Người 1 và Người 2 gửi đồng thời
        $resP1 = $this->actingAs($u1)->getJson("/duel/room/{$code}/state");
        $resP2 = $this->actingAs($u2)->getJson("/duel/room/{$code}/state");

        $resP1->assertStatus(200);
        $resP2->assertStatus(200);

        // Cả hai request đều phải nhận đúng Hiệp 2, không thể bị nhảy thành Hiệp 3
        $this->assertEquals(2, $resP1->json('current_round'));
        $this->assertEquals('answering', $resP1->json('round_phase'));

        $this->assertEquals(2, $resP2->json('current_round'));
        $this->assertEquals('answering', $resP2->json('round_phase'));

        $room->refresh();
        $this->assertEquals(2, $room->current_round);
        $this->assertEquals('answering', $room->round_phase);
    }

    /**
     * EDGE CASE 5: HAI NGƯỜI CHƠI CÙNG GỬI YÊU CẦU TÁI ĐẤU (CONCURRENT REMATCH API)
     */
    public function test_simultaneous_rematch_api_requests_from_both_players(): void
    {
        [$room, $u1, $u2] = $this->createActiveRoom();
        $code = $room->room_code;

        // Cho trận đấu kết thúc
        $this->engine->finishMatch($room);
        $this->assertEquals('finished', $room->status);

        // Người 1 gửi yêu cầu tái đấu -> Hệ thống ghi nhận và chuyển trạng thái chờ đối thủ
        $resRematch1 = $this->actingAs($u1)->postJson("/duel/room/{$code}/rematch");
        $resRematch1->assertStatus(200);
        $this->assertEquals('waiting_opponent', $resRematch1->json('status'));

        // Người 2 gửi yêu cầu tái đấu ngay sau đó -> Sinh phòng tái đấu mới
        $resRematch2 = $this->actingAs($u2)->postJson("/duel/room/{$code}/rematch");
        $resRematch2->assertStatus(200);
        $this->assertEquals('ready', $resRematch2->json('status'));

        // Người 2 nhận được mã phòng tái đấu
        $newCode = $resRematch2->json('room_code');
        $this->assertNotEmpty($newCode);
        $this->assertNotEquals($code, $newCode);

        // Người 1 gọi lại rematch hoặc polling state cũng nhận đúng trạng thái ready và cùng mã phòng đó
        $resRematch1Again = $this->actingAs($u1)->postJson("/duel/room/{$code}/rematch");
        $resRematch1Again->assertStatus(200);
        $this->assertEquals('ready', $resRematch1Again->json('status'));
        $this->assertEquals($newCode, $resRematch1Again->json('room_code'));

        // Phòng cũ chỉ trỏ duy nhất đến 1 phòng tái đấu
        $room->refresh();
        $this->assertNotNull($room->rematch_room_id);

        $newRoom = DuelRoom::find($room->rematch_room_id);
        $this->assertEquals($newCode, $newRoom->room_code);
        $this->assertEquals($u1->id, $newRoom->player1_id);
        $this->assertEquals($u2->id, $newRoom->player2_id);
    }

    /**
     * EDGE CASE 6: MẤT KẾT NỐI, TAB TRÌNH DUYỆT BỊ THROTTLE VÀ XỬ FORFEIT CHÍNH XÁC
     */
    public function test_heartbeat_throttling_resilience_and_forfeit_trigger(): void
    {
        [$room, $u1, $u2] = $this->createActiveRoom();
        $code = $room->room_code;
        $now = microtime(true);

        // Tình huống A: Trình duyệt Người 2 bị background throttle trong 15s (dưới ngưỡng 25s)
        $room->player1_last_seen_at = $now;
        $room->player2_last_seen_at = $now - 15.0; // Mất tín hiệu 15s
        $room->save();

        // Người 1 polling trạng thái -> Trận đấu KHÔNG ĐƯỢC XỬ THUA NHẦM
        $resP1 = $this->actingAs($u1)->getJson("/duel/room/{$code}/state");
        $resP1->assertStatus(200);
        $room->refresh();
        $this->assertEquals('in_progress', $room->status);
        $this->assertNull($room->forfeit_by_slot);

        // Tình huống B: Người 2 mất kết nối quá 26s (vượt ngưỡng 25s, người 1 vẫn online)
        $room->player1_last_seen_at = $now;
        $room->player2_last_seen_at = $now - 26.0; // Quá 25s
        $room->save();

        // Người 1 polling -> Lập tức phát hiện và xử thua Người 2, Người 1 thắng trận
        $resP1Forfeit = $this->actingAs($u1)->getJson("/duel/room/{$code}/state");
        $resP1Forfeit->assertStatus(200);

        $room->refresh();
        $this->assertEquals('finished', $room->status);
        $this->assertEquals(2, $room->forfeit_by_slot); // Người 2 bỏ cuộc
        $this->assertEquals(1, $room->winner_slot); // Người 1 chiến thắng
        $this->assertFalse($room->is_draw);

        // Tình huống C: Cả hai người cùng ngắt kết nối quá 40s
        [$roomDead, $userA, $userB] = $this->createActiveRoom();
        $roomDead->player1_last_seen_at = $now - 45.0;
        $roomDead->player2_last_seen_at = $now - 50.0;
        $roomDead->phase_ends_at = $now - 40.0;
        $roomDead->save();

        // Scheduled command chạy dọn dẹp các phòng stale
        Artisan::call('duel:sync-stale-rooms');

        $roomDead->refresh();
        $this->assertEquals('abandoned', $roomDead->status);
        $this->assertNull($roomDead->forfeit_by_slot); // Cả hai cùng mất tín hiệu
        $this->assertNull($roomDead->winner_slot);
    }

    /**
     * EDGE CASE 7: BẢO TOÀN BẤT BIẾN CÔNG THỨC ĐIỂM DƯỚI CÁC THỜI GIAN PHẢN ỨNG CỰC ĐOAN
     */
    public function test_extreme_reaction_times_score_formula_invariants(): void
    {
        [$room, $u1, $u2] = $this->createActiveRoom();
        $code = $room->room_code;
        $q = $room->questions_snapshot[0];
        $correct = $q['correct_answer'];
        $now = microtime(true);

        // 1. Phản xạ cực nhanh như bot (0.05 giây): Điểm phải tiệm cận 100
        $room->phase_started_at = $now - 0.05;
        $room->phase_ends_at = $now + 9.95;
        $room->save();

        $resFast = $this->actingAs($u1)->postJson("/duel/room/{$code}/answer", [
            'answer' => $correct,
            'idempotency_key' => 'fast_key_005',
        ]);
        $resFast->assertStatus(200);
        $this->assertEquals('success', $resFast->json('status'));
        $this->assertGreaterThanOrEqual(99, $resFast->json('data.score_awarded'));
        $this->assertLessThanOrEqual(100, $resFast->json('data.score_awarded'));

        // Reset hiệp để test thời gian cực chậm
        $room->refresh();
        DuelRoundAnswer::where('duel_room_id', $room->id)->delete();
        $room->round_phase = 'answering';
        $room->save();

        // 2. Phản xạ cực chậm sát deadline (9.90 giây): Điểm phải tiệm cận 75
        $nowSlow = microtime(true);
        $room->phase_started_at = $nowSlow - 9.90;
        $room->phase_ends_at = $nowSlow + 0.10;
        $room->save();

        $resSlow = $this->actingAs($u1)->postJson("/duel/room/{$code}/answer", [
            'answer' => $correct,
            'idempotency_key' => 'slow_key_990',
        ]);
        $resSlow->assertStatus(200);
        $this->assertEquals('success', $resSlow->json('status'));
        $this->assertGreaterThanOrEqual(75, $resSlow->json('data.score_awarded'));
        $this->assertLessThanOrEqual(76, $resSlow->json('data.score_awarded'));
    }

    /**
     * EDGE CASE 8: BẬN RỘN POLLING (BURST REQUESTS) KHÔNG LÀM SAI LỆCH TRẠNG THÁI
     */
    public function test_burst_polling_under_load_does_not_mutate_state_prematurely(): void
    {
        [$room, $u1, $u2] = $this->createActiveRoom();
        $code = $room->room_code;

        // Giả lập 10 request state polling dồn dập trong cùng 1 giây
        for ($i = 0; $i < 10; $i++) {
            $res = $this->actingAs($u1)->getJson("/duel/room/{$code}/state");
            $res->assertStatus(200);
            $this->assertEquals(1, $res->json('current_round'));
            $this->assertEquals('answering', $res->json('round_phase'));
        }

        $room->refresh();
        $this->assertEquals(1, $room->current_round);
        $this->assertEquals('answering', $room->round_phase);
        $this->assertEquals('in_progress', $room->status);
    }
}
