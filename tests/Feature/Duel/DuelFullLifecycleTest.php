<?php

namespace Tests\Feature\Duel;

use App\Models\DuelRoom;
use App\Models\DuelRoundAnswer;
use App\Models\Flashcard;
use App\Models\FlashcardProgress;
use App\Models\LearningActivity;
use App\Models\Question;
use App\Models\User;
use App\Services\Duel\DuelAuthService;
use App\Services\Duel\DuelGameEngine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class DuelFullLifecycleTest extends TestCase
{
    use RefreshDatabase;

    protected DuelGameEngine $engine;
    protected DuelAuthService $authService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->engine = app(DuelGameEngine::class);
        $this->authService = app(DuelAuthService::class);

        // Nạp 20 câu hỏi HSK 3 chuẩn cho pool đề thi
        for ($i = 1; $i <= 20; $i++) {
            Question::create([
                'hsk_level' => 3,
                'question' => "Câu hỏi thi đấu số {$i}",
                'pinyin' => "kǎo shì dì {$i} tí",
                'options' => ['Lựa chọn A', 'Lựa chọn B', 'Lựa chọn C', 'Lựa chọn D'],
                'correct_answer' => 'Lựa chọn A',
                'explanation' => "Giải thích chi tiết cho câu {$i}",
                'skill_type' => 'reading',
                'question_type' => 'single_choice',
                'is_active' => true,
            ]);
        }
    }

    /**
     * KỊCH BẢN 1: USER VS USER — CHƠI TRỌN VẸN 10 HIỆP ĐẤU
     */
    public function test_user_vs_user_full_match_lifecycle_through_all_10_rounds(): void
    {
        $user1 = User::factory()->create(['name' => 'Nguyễn Cao Thủ']);
        $user2 = User::factory()->create(['name' => 'Trần Tân Binh']);

        // 1. User 1 tạo phòng thách đấu bạn bè
        $createRes = $this->actingAs($user1)->postJson(route('duel.room.create'), [
            'hsk_level' => 3,
            'mode' => 'friend',
        ]);
        $createRes->assertStatus(201);
        $roomCode = $createRes->json('room_code');

        $room = DuelRoom::where('room_code', $roomCode)->firstOrFail();
        $this->assertEquals('waiting', $room->status);
        $this->assertNull($room->player2_id);

        // 2. User 2 nhập mã mời tham gia phòng
        $joinRes = $this->actingAs($user2)->postJson(route('duel.room.join'), [
            'invite_code' => $roomCode,
        ]);
        $joinRes->assertStatus(200);

        $room->refresh();
        $this->assertEquals('countdown', $room->status);
        $this->assertCount(10, $room->questions_snapshot);

        // 3. Giả lập hết 3 giây countdown -> bước vào Hiệp 1 (in_progress, answering)
        $room->phase_ends_at = microtime(true) - 0.1;
        $room->save();

        // Cả hai người chơi polling state
        $state1 = $this->actingAs($user1)->getJson("/duel/room/{$roomCode}/state")->json();
        $this->assertEquals('in_progress', $state1['status']);
        $this->assertEquals('answering', $state1['round_phase']);
        $this->assertEquals(1, $state1['current_round']);
        $this->assertArrayNotHasKey('correct_answer', $state1['current_question']);

        // 4. Lần lượt chơi qua 10 hiệp đấu
        for ($round = 1; $round <= 10; $round++) {
            $room->refresh();
            $questions = $room->questions_snapshot;
            $currentQ = $questions[$round - 1];

            // Giả lập phase answering bắt đầu từ 2 giây trước
            $now = microtime(true);
            $room->phase_started_at = $now - 2.0;
            $room->phase_ends_at = $now + 8.0;
            $room->save();

            // User 1 trả lời đúng
            $res1 = $this->actingAs($user1)->postJson("/duel/room/{$roomCode}/answer", [
                'answer' => $currentQ['correct_answer'],
                'idempotency_key' => "key_{$roomCode}_r{$round}_s1",
            ]);
            $res1->assertStatus(200);
            $this->assertEquals('success', $res1->json('status'));
            $this->assertTrue($res1->json('data.is_correct'));

            // User 2 trả lời (nếu round chẵn thì đúng, round lẻ thì sai)
            $user2Ans = ($round % 2 === 0) ? $currentQ['correct_answer'] : 'Lựa chọn B';
            $res2 = $this->actingAs($user2)->postJson("/duel/room/{$roomCode}/answer", [
                'answer' => $user2Ans,
                'idempotency_key' => "key_{$roomCode}_r{$round}_s2",
            ]);
            $res2->assertStatus(200);

            // Cả hai đã nộp -> server chuyển ngay sang pha Revealing
            $room->refresh();
            $this->assertEquals('revealing', $room->round_phase);

            // Polling trong pha Revealing: cả hai nhìn thấy đáp án và giải thích
            $stateRevealing = $this->actingAs($user1)->getJson("/duel/room/{$roomCode}/state")->json();
            $this->assertArrayHasKey('correct_answer', $stateRevealing['current_question']);
            $this->assertArrayHasKey('explanation', $stateRevealing['current_question']);

            // Hết 2 giây reveal -> chuyển sang round tiếp theo hoặc kết thúc
            $room->phase_ends_at = microtime(true) - 0.1;
            $room->save();

            // Trigger chuyển pha qua state polling
            $this->actingAs($user1)->getJson("/duel/room/{$roomCode}/state");
        }

        // 5. Kết thúc trận đấu: Trạng thái finished, xác định người thắng và trao XP
        $room->refresh();
        $this->assertEquals('finished', $room->status);
        $this->assertEquals(1, $room->winner_slot); // User 1 đúng 10 câu, User 2 đúng 5 câu
        $this->assertFalse($room->is_draw);
        $this->assertTrue($room->xp_awarded);

        // Kiểm tra XP qua LearningActivity: Người thắng +50 XP, Người thua +15 XP
        $u1Activity = LearningActivity::where('user_id', $user1->id)->where('source_type', 'duel_room')->first();
        $u2Activity = LearningActivity::where('user_id', $user2->id)->where('source_type', 'duel_room')->first();
        $this->assertNotNull($u1Activity);
        $this->assertEquals(50, $u1Activity->xp_earned);
        $this->assertNotNull($u2Activity);
        $this->assertEquals(15, $u2Activity->xp_earned);
    }

    /**
     * KỊCH BẢN 2: BẢO VỆ PHÒNG ĐẤU — KHÁCH BỊ CHẶN, USER ĐĂNG NHẬP THAM GIA HỢP LỆ
     */
    public function test_user_vs_guest_identity_and_cookie_scoping(): void
    {
        $user = User::factory()->create(['name' => 'Host User']);
        $user2 = User::factory()->create(['name' => 'Joiner User']);
        $stranger = User::factory()->create(['name' => 'Stranger']);

        // User tạo phòng
        $createRes = $this->actingAs($user)->postJson(route('duel.room.create'), [
            'hsk_level' => 3,
            'mode' => 'friend',
        ]);
        $roomCode = $createRes->json('room_code');

        // Logout khỏi phiên test để đóng vai khách vãng lai
        Auth::guard('web')->logout();

        // 1. Khách vãng lai truy cập thẳng vào URL Sàn đấu -> Bị chặn 302 chuyển sang login
        $guestArenaRes = $this->get("/duel/room/{$roomCode}");
        $guestArenaRes->assertStatus(302);
        $guestArenaRes->assertRedirect(route('login'));

        // 2. User 2 đăng nhập và truy cập vào link phòng -> Tham gia thành công
        $user2ArenaRes = $this->actingAs($user2)->get("/duel/room/{$roomCode}");
        $user2ArenaRes->assertStatus(200);

        $room = DuelRoom::where('room_code', $roomCode)->firstOrFail();
        $this->assertEquals('countdown', $room->status);
        $this->assertEquals('user', $room->player2_type);
        $this->assertEquals($user2->id, $room->player2_id);

        // User 2 polling state thành công với Slot 2
        $user2StateRes = $this->actingAs($user2)->getJson("/duel/room/{$roomCode}/state");
        $user2StateRes->assertStatus(200);
        $this->assertEquals(2, $user2StateRes->json('my_slot'));

        // 3. Người lạ không thuộc phòng bị chặn 403
        $strangerRes = $this->actingAs($stranger)->getJson("/duel/room/{$roomCode}/state");
        $strangerRes->assertStatus(403);
    }

    /**
     * KỊCH BẢN 3: MỘT NGƯỜI TIMEOUT — XỬ LÝ CHÍNH XÁC VÀ TỰ ĐỘNG CHUYỂN PHA REVEAL
     */
    public function test_one_player_timeout_and_reveal_transition(): void
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

        // Cho countdown hết
        $room->phase_ends_at = microtime(true) - 0.1;
        $room->save();

        // Kích hoạt round 1
        $this->engine->syncAndGetRoomState($room, 1);
        $room->refresh();
        $this->assertEquals('answering', $room->round_phase);

        // User 1 trả lời
        $currentQ = $room->questions_snapshot[0];
        $this->engine->submitAnswer($room, 1, $currentQ['correct_answer'], 'idemp_p1_r1');

        // User 2 không trả lời và để hết giờ 10 giây
        $room->refresh();
        $room->phase_ends_at = microtime(true) - 0.1;
        $room->save();

        // Request polling tiếp theo của User 1
        $state = $this->engine->syncAndGetRoomState($room, 1);
        $this->assertEquals('revealing', $state['round_phase']);

        // Kiểm tra User 2 đã được tự động tạo bản ghi Timeout 0 điểm
        $ans2 = DuelRoundAnswer::where('duel_room_id', $room->id)
            ->where('round_number', 1)
            ->where('player_slot', 2)
            ->first();

        $this->assertNotNull($ans2);
        $this->assertNull($ans2->selected_answer); // Timeout
        $this->assertFalse($ans2->is_correct);
        $this->assertEquals(0, $ans2->score_awarded);
    }

    /**
     * KỊCH BẢN 4: CẢ HAI CÙNG TIMEOUT — KHÔNG CỘNG SAI ĐIỂM
     */
    public function test_both_players_timeout_no_false_score(): void
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

        $room->phase_ends_at = microtime(true) - 0.1;
        $room->save();
        $this->engine->syncAndGetRoomState($room, 1);

        // Cả hai không ai nộp bài, hết 10 giây
        $room->refresh();
        $room->phase_ends_at = microtime(true) - 0.1;
        $room->save();

        // Kích hoạt đồng bộ
        $state = $this->engine->syncAndGetRoomState($room, 1);
        $this->assertEquals('revealing', $state['round_phase']);

        $answers = DuelRoundAnswer::where('duel_room_id', $room->id)
            ->where('round_number', 1)
            ->get();

        $this->assertCount(2, $answers);
        foreach ($answers as $ans) {
            $this->assertNull($ans->selected_answer);
            $this->assertFalse($ans->is_correct);
            $this->assertEquals(0, $ans->score_awarded);
        }

        $room->refresh();
        $this->assertEquals(0, $room->player1_score);
        $this->assertEquals(0, $room->player2_score);
    }

    /**
     * KỊCH BẢN 5: XP SAU TRẬN ĐƯỢC TRAO NGUYÊN TỬ VÀ KHÔNG BỊ NHÂN ĐÔI
     */
    public function test_xp_after_match_awarded_only_once_atomic(): void
    {
        $user1 = User::factory()->create(['name' => 'Winner']);
        $user2 = User::factory()->create(['name' => 'Loser']);

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

        $room->player1_score = 1200;
        $room->player2_score = 800;
        $room->save();

        // Kết thúc trận lần 1
        $this->engine->finishMatch($room);
        $room->refresh();
        $this->assertTrue($room->xp_awarded);

        // Gọi kết thúc trận lần 2 (giả lập request lặp hoặc trigger thừa)
        $this->engine->finishMatch($room);

        // Kiểm tra bảng learning_activities: mỗi người chỉ có đúng 1 bản ghi XP
        $p1Activities = LearningActivity::where('user_id', $user1->id)
            ->where('source_type', 'duel_room')
            ->where('source_id', $room->id)
            ->count();
        $p2Activities = LearningActivity::where('user_id', $user2->id)
            ->where('source_type', 'duel_room')
            ->where('source_id', $room->id)
            ->count();

        $this->assertEquals(1, $p1Activities);
        $this->assertEquals(1, $p2Activities);
    }

    /**
     * KỊCH BẢN 6: LƯU CÂU SAI 10 LẦN — KHÔNG SINH THẺ / PROGRESS TRÙNG LẶP VÀ GIỮ REPETITION = 0
     */
    public function test_save_mistake_repeated_and_concurrent_safety(): void
    {
        $user1 = User::factory()->create(['name' => 'Student']);
        $user2 = User::factory()->create(['name' => 'Opponent']);

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
        $room->status = 'finished';
        $room->save();

        // Giả lập người học bấm nút lưu Flashcard 10 lần liên tiếp
        for ($i = 0; $i < 10; $i++) {
            $res = $this->actingAs($user1)->postJson("/duel/room/{$room->room_code}/review/save-flashcard", [
                'round_number' => 1,
            ]);
            $res->assertStatus(200);
            $res->assertJson(['success' => true]);
        }

        $q = $room->questions_snapshot[0];
        $hanzi = trim($q['question']);

        // Chỉ có duy nhất 1 Flashcard cho từ này
        $flashcardCount = Flashcard::where('hanzi', $hanzi)->count();
        $this->assertEquals(1, $flashcardCount);

        $card = Flashcard::where('hanzi', $hanzi)->first();

        // Chỉ có duy nhất 1 bản ghi Progress cho user và card này
        $progressRecords = FlashcardProgress::where('user_id', $user1->id)
            ->where('flashcard_id', $card->id)
            ->get();

        $this->assertCount(1, $progressRecords);

        // Quy tắc SRS: repetition = 0, interval = 0, is_starred = true
        $progress = $progressRecords->first();
        $this->assertEquals(0, $progress->repetition);
        $this->assertEquals(0, $progress->interval);
        $this->assertTrue($progress->is_starred);
    }

    /**
     * KỊCH BẢN 7: TÁI ĐẤU ĐỒNG THỜI — CHỈ TẠO ĐÚNG 1 PHÒNG CON REMATCH DUY NHẤT
     */
    public function test_concurrent_rematch_creates_exactly_one_child_room(): void
    {
        $user1 = User::factory()->create(['name' => 'P1']);
        $user2 = User::factory()->create(['name' => 'P2']);

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
        $room->status = 'finished';
        $room->save();

        // Cả 2 cùng gửi yêu cầu tái đấu
        $res1 = $this->actingAs($user1)->postJson("/duel/room/{$room->room_code}/rematch");
        $res2 = $this->actingAs($user2)->postJson("/duel/room/{$room->room_code}/rematch");

        $res1->assertStatus(200);
        $res2->assertStatus(200);

        $room->refresh();
        $this->assertNotNull($room->rematch_room_id);

        // User 1 kiểm tra lại
        $res1Again = $this->actingAs($user1)->postJson("/duel/room/{$room->room_code}/rematch");
        $res1Again->assertStatus(200);
        $this->assertEquals('ready', $res1Again->json('status'));
        $this->assertEquals('ready', $res2->json('status'));
        $this->assertEquals($res2->json('room_code'), $res1Again->json('room_code'));

        // Chỉ có đúng 1 phòng con được tạo kế thừa từ phòng cha này
        $childRoomsCount = DuelRoom::where('parent_room_id', $room->id)->count();
        $this->assertEquals(1, $childRoomsCount);
    }

    /**
     * KỊCH BẢN 8: RELOAD GIỮA TRẬN — KHÔI PHỤC ĐÚNG TRẠNG THÁI KHÔNG MẤT DỮ LIỆU
     */
    public function test_mid_match_reload_restores_accurate_state_without_data_loss(): void
    {
        $user1 = User::factory()->create(['name' => 'Reload Tester 1']);
        $user2 = User::factory()->create(['name' => 'Reload Tester 2']);

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

        $now = microtime(true);
        // Vào Hiệp 4: còn 8 giây làm bài
        $room->current_round = 4;
        $room->status = 'in_progress';
        $room->round_phase = 'answering';
        $room->phase_started_at = $now - 2.0;
        $room->phase_ends_at = $now + 8.0;
        $room->save();

        // User 1 nộp bài cho Hiệp 4
        $q4 = $room->questions_snapshot[3];
        $this->engine->submitAnswer($room, 1, $q4['correct_answer'], 'idemp_reload_r4');

        // User 1 reload trang web (GET /duel/room/{code} và GET /duel/room/{code}/state)
        $arenaPageRes = $this->actingAs($user1)->get("/duel/room/{$room->room_code}");
        $arenaPageRes->assertStatus(200);

        $stateRes = $this->actingAs($user1)->getJson("/duel/room/{$room->room_code}/state");
        $stateRes->assertStatus(200);

        // Khôi phục đúng slot 1, hiệp 4, my_submitted là true
        $this->assertEquals(1, $stateRes->json('my_slot'));
        $this->assertEquals(4, $stateRes->json('current_round'));
        $this->assertTrue($stateRes->json('my_submitted'));
        $this->assertFalse($stateRes->json('opponent_submitted'));
    }

    /**
     * KỊCH BẢN 9: TÍNH ĐIỂM VÀ PHÂN ĐỊNH TIE-BREAKER 2 CẤP ĐỘ
     */
    public function test_score_calculation_and_two_tier_tie_breakers(): void
    {
        $user1 = User::factory()->create(['name' => 'Player A']);
        $user2 = User::factory()->create(['name' => 'Player B']);

        // Case A: Bằng điểm nhưng Player A có nhiều câu đúng hơn -> Player A thắng
        $roomA = $this->engine->createRoom(3, 'custom', [
            'type' => 'user',
            'user_id' => $user1->id,
            'name' => $user1->name,
            'avatar' => null,
        ]);
        $roomA = $this->engine->joinRoom($roomA, [
            'type' => 'user',
            'user_id' => $user2->id,
            'name' => $user2->name,
            'avatar' => null,
        ]);

        $roomA->player1_score = 1000;
        $roomA->player2_score = 1000;
        $roomA->player1_correct_count = 8;
        $roomA->player2_correct_count = 7;
        $roomA->save();

        $this->engine->finishMatch($roomA);
        $roomA->refresh();
        $this->assertEquals(1, $roomA->winner_slot);
        $this->assertFalse($roomA->is_draw);

        // Case B: Bằng điểm và cùng số câu đúng -> Người có tổng thời gian trả lời câu đúng ít hơn thắng
        $roomB = $this->engine->createRoom(3, 'custom', [
            'type' => 'user',
            'user_id' => $user1->id,
            'name' => $user1->name,
            'avatar' => null,
        ]);
        $roomB = $this->engine->joinRoom($roomB, [
            'type' => 'user',
            'user_id' => $user2->id,
            'name' => $user2->name,
            'avatar' => null,
        ]);

        $roomB->player1_score = 1000;
        $roomB->player2_score = 1000;
        $roomB->player1_correct_count = 8;
        $roomB->player2_correct_count = 8;
        $roomB->player1_total_correct_time = 12.5000; // Nhanh hơn
        $roomB->player2_total_correct_time = 15.2000;
        $roomB->save();

        $this->engine->finishMatch($roomB);
        $roomB->refresh();
        $this->assertEquals(1, $roomB->winner_slot);
        $this->assertFalse($roomB->is_draw);

        // Case C: Bằng điểm, bằng câu đúng, bằng cả thời gian -> Hòa
        $roomC = $this->engine->createRoom(3, 'custom', [
            'type' => 'user',
            'user_id' => $user1->id,
            'name' => $user1->name,
            'avatar' => null,
        ]);
        $roomC = $this->engine->joinRoom($roomC, [
            'type' => 'user',
            'user_id' => $user2->id,
            'name' => $user2->name,
            'avatar' => null,
        ]);

        $roomC->player1_score = 1000;
        $roomC->player2_score = 1000;
        $roomC->player1_correct_count = 8;
        $roomC->player2_correct_count = 8;
        $roomC->player1_total_correct_time = 14.0000;
        $roomC->player2_total_correct_time = 14.0000;
        $roomC->save();

        $this->engine->finishMatch($roomC);
        $roomC->refresh();
        $this->assertNull($roomC->winner_slot);
        $this->assertTrue($roomC->is_draw);
    }
}
