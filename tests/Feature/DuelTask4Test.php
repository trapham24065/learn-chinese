<?php

namespace Tests\Feature;

use App\Models\DuelRoom;
use App\Models\DuelRoundAnswer;
use App\Models\Flashcard;
use App\Models\FlashcardProgress;
use App\Models\Question;
use App\Models\User;
use App\Services\Duel\DuelAuthService;
use App\Services\Duel\DuelGameEngine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DuelTask4Test extends TestCase
{
    use RefreshDatabase;

    protected DuelGameEngine $engine;
    protected DuelAuthService $authService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->engine = app(DuelGameEngine::class);
        $this->authService = app(DuelAuthService::class);

        // Seed 12 câu hỏi HSK 2
        for ($i = 1; $i <= 12; $i++) {
            Question::create([
                'hsk_level' => 2,
                'question' => "Câu hỏi Task 4 số {$i}",
                'pinyin' => "cè shì {$i}",
                'options' => ['Đáp án A', 'Đáp án B', 'Đáp án C', 'Đáp án D'],
                'correct_answer' => 'Đáp án A',
                'explanation' => "Giải thích chi tiết câu {$i}",
                'is_active' => true,
            ]);
        }
    }

    /**
     * Helper tạo phòng đã hoàn thành và lưu các câu trả lời mẫu
     */
    protected function createFinishedMatch(User $user1, User $user2, int $winnerSlot = 1): DuelRoom
    {
        $room = $this->engine->createRoom(2, 'custom', [
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
        $room->status = 'finished';
        $room->winner_slot = $winnerSlot;
        $room->is_draw = ($winnerSlot === 0);
        $room->player1_score = ($winnerSlot === 1) ? 1500 : (($winnerSlot === 0) ? 1000 : 800);
        $room->player2_score = ($winnerSlot === 2) ? 1500 : (($winnerSlot === 0) ? 1000 : 800);
        $room->player1_correct_count = 8;
        $room->player2_correct_count = 6;
        $room->finished_at = now();
        $room->save();

        // Tạo câu trả lời cho vòng 1: Slot 1 đúng, Slot 2 sai
        DuelRoundAnswer::create([
            'duel_room_id' => $room->id,
            'round_number' => 1,
            'player_slot' => 1,
            'selected_answer' => 'Đáp án A',
            'is_correct' => true,
            'answer_time_seconds' => 1.5,
            'score_awarded' => 190,
            'answered_at' => $now,
        ]);

        DuelRoundAnswer::create([
            'duel_room_id' => $room->id,
            'round_number' => 1,
            'player_slot' => 2,
            'selected_answer' => 'Đáp án B',
            'is_correct' => false,
            'answer_time_seconds' => 3.2,
            'score_awarded' => 0,
            'answered_at' => $now,
        ]);

        // Vòng 2: Cả 2 đều timeout (selected_answer = null)
        DuelRoundAnswer::create([
            'duel_room_id' => $room->id,
            'round_number' => 2,
            'player_slot' => 1,
            'selected_answer' => null,
            'is_correct' => false,
            'answer_time_seconds' => 10.0,
            'score_awarded' => 0,
            'answered_at' => $now,
        ]);

        DuelRoundAnswer::create([
            'duel_room_id' => $room->id,
            'round_number' => 2,
            'player_slot' => 2,
            'selected_answer' => null,
            'is_correct' => false,
            'answer_time_seconds' => 10.0,
            'score_awarded' => 0,
            'answered_at' => $now,
        ]);

        return $room;
    }

    public function test_match_result_displays_correctly_for_winner_and_loser(): void
    {
        $user1 = User::factory()->create(['name' => 'Chiến Binh Thắng']);
        $user2 = User::factory()->create(['name' => 'Chiến Binh Thua']);

        $room = $this->createFinishedMatch($user1, $user2, 1);

        // Winner xem kết quả
        $resWinner = $this->actingAs($user1)->get("/duel/room/{$room->room_code}/result");
        $resWinner->assertStatus(200);
        $resWinner->assertSee('Chiến Thắng Tuyệt Đối!');
        $resWinner->assertSee('1500');
        $resWinner->assertSee('Chi Tiết 10 Hiệp Đấu');

        // Loser xem kết quả
        $resLoser = $this->actingAs($user2)->get("/duel/room/{$room->room_code}/result");
        $resLoser->assertStatus(200);
        $resLoser->assertSee('Tiếc Nuối... Thất Bại');
        $resLoser->assertSee('800');
    }

    public function test_match_result_displays_draw_and_abandoned_correctly(): void
    {
        $user1 = User::factory()->create(['name' => 'Người Chơi A']);
        $user2 = User::factory()->create(['name' => 'Người Chơi B']);

        // Test trận hòa
        $drawRoom = $this->createFinishedMatch($user1, $user2, 0);
        $drawRes = $this->actingAs($user1)->get("/duel/room/{$drawRoom->room_code}/result");
        $drawRes->assertStatus(200);
        $drawRes->assertSee('Bất Phân Thắng Bại!');

        // Test trận abandoned / forfeit
        $abandonedRoom = $this->createFinishedMatch($user1, $user2, 1);
        $abandonedRoom->status = 'abandoned';
        $abandonedRoom->forfeit_by_slot = 2; // User 2 bỏ cuộc
        $abandonedRoom->save();

        $res1 = $this->actingAs($user1)->get("/duel/room/{$abandonedRoom->room_code}/result");
        $res1->assertStatus(200);
        $res1->assertSee('Chiến Thắng Tuyệt Đối!');

        $res2 = $this->actingAs($user2)->get("/duel/room/{$abandonedRoom->room_code}/result");
        $res2->assertStatus(200);
        $res2->assertSee('Tiếc Nuối... Thất Bại');
    }

    public function test_unauthorized_user_cannot_view_match_result(): void
    {
        $user1 = User::factory()->create(['name' => 'P1']);
        $user2 = User::factory()->create(['name' => 'P2']);
        $stranger = User::factory()->create(['name' => 'Stranger']);

        $room = $this->createFinishedMatch($user1, $user2, 1);

        $response = $this->actingAs($stranger)->get("/duel/room/{$room->room_code}/result");
        $response->assertStatus(403);
    }

    public function test_duel_history_shows_only_user_own_matches_and_correct_stats(): void
    {
        $user1 = User::factory()->create(['name' => 'Main Player']);
        $user2 = User::factory()->create(['name' => 'Rival']);
        $user3 = User::factory()->create(['name' => 'Other Player']);

        // User 1 chơi 2 trận (1 thắng, 1 thua)
        $this->createFinishedMatch($user1, $user2, 1); // User 1 win
        $this->createFinishedMatch($user2, $user1, 1); // User 2 win (User 1 loss)

        // User 3 chơi 1 trận hoàn toàn riêng biệt
        $this->createFinishedMatch($user3, $user2, 1);

        $response = $this->actingAs($user1)->get(route('duel.history'));

        $response->assertStatus(200);
        $response->assertSee('Lịch Sử Thi Đấu');
        $response->assertSee('Rival');
        $response->assertDontSee('Other Player'); // Không thấy trận của người khác

        // Kiểm tra thống kê: 2 trận, 1 thắng, 1 thua, tỷ lệ 50%
        $response->assertSee('2'); // Tổng trận
        $response->assertSee('50%'); // Tỷ lệ thắng
    }

    public function test_guest_history_isolation(): void
    {
        // Khách 1 tạo và hoàn thành trận đấu
        $room1 = $this->engine->createRoom(2, 'custom', [
            'type' => 'guest',
            'user_id' => null,
            'name' => 'Khách 1',
            'avatar' => null,
            'guest_token_plain' => 'guest_token_plain_11111111111111111111111111111111',
            'guest_token_hash' => hash_hmac('sha256', 'guest_token_plain_11111111111111111111111111111111', config('app.key')),
        ]);
        $room1->status = 'finished';
        $room1->winner_slot = 1;
        $room1->finished_at = now();
        $room1->save();

        // Khách 2 tạo trận khác
        $room2 = $this->engine->createRoom(2, 'custom', [
            'type' => 'guest',
            'user_id' => null,
            'name' => 'Khách 2',
            'avatar' => null,
            'guest_token_plain' => 'guest_token_plain_22222222222222222222222222222222',
            'guest_token_hash' => hash_hmac('sha256', 'guest_token_plain_22222222222222222222222222222222', config('app.key')),
        ]);
        $room2->status = 'finished';
        $room2->winner_slot = 1;
        $room2->finished_at = now();
        $room2->save();

        // Khách 1 xem lịch sử với cookie phòng 1
        $res = $this->withCookie("duel_guest_token_{$room1->room_code}", 'guest_token_plain_11111111111111111111111111111111')
            ->get(route('duel.history'));

        $res->assertStatus(200);
        $res->assertSee($room1->room_code);
        $res->assertDontSee($room2->room_code); // Tuyệt đối không thấy phòng của khách 2
    }

    public function test_review_mistakes_includes_wrong_and_timeout_questions_and_excludes_correct(): void
    {
        $user1 = User::factory()->create(['name' => 'Player 1']);
        $user2 = User::factory()->create(['name' => 'Player 2']);

        $room = $this->createFinishedMatch($user1, $user2, 1);

        // Đối với user 1:
        // Round 1: Đúng -> KHÔNG được xuất hiện trong Review
        // Round 2: Timeout -> PHẢI xuất hiện trong Review
        // Round 3..10: Chưa trả lời -> PHẢI xuất hiện trong Review

        $response = $this->actingAs($user1)->get("/duel/room/{$room->room_code}/review");

        $response->assertStatus(200);
        $response->assertSee('Ôn Tập Câu Sai');

        // Round 1 (đúng) không được có trong danh sách mistakes
        $mistakes = collect($response->viewData('mistakes'));
        $mistakeRounds = $mistakes->pluck('round_number')->all();

        $this->assertNotContains(1, $mistakeRounds);
        $this->assertContains(2, $mistakeRounds);
    }

    public function test_save_mistake_to_flashcard_is_idempotent_and_respects_srs_rules(): void
    {
        $user1 = User::factory()->create(['name' => 'Học Viên SRS']);
        $user2 = User::factory()->create(['name' => 'Đối Thủ']);

        $room = $this->createFinishedMatch($user1, $user2, 1);

        // Lưu câu sai của Round 2 vào Flashcard
        $response1 = $this->actingAs($user1)->postJson("/duel/room/{$room->room_code}/review/save-flashcard", [
            'round_number' => 2,
        ]);

        $response1->assertStatus(200);
        $response1->assertJson(['success' => true]);

        $flashcardId = $response1->json('flashcard_id');
        $this->assertNotNull($flashcardId);

        // Kiểm tra Flashcard và Progress được tạo đúng quy tắc SRS
        $progress = FlashcardProgress::where('user_id', $user1->id)
            ->where('flashcard_id', $flashcardId)
            ->first();

        $this->assertNotNull($progress);
        $this->assertTrue($progress->is_starred);
        $this->assertEquals(0, $progress->repetition); // Không được tự ý đánh dấu đã thuộc!
        $this->assertEquals(0, $progress->interval);

        // Gửi lại request lần 2 (Idempotency test)
        $response2 = $this->actingAs($user1)->postJson("/duel/room/{$room->room_code}/review/save-flashcard", [
            'round_number' => 2,
        ]);

        $response2->assertStatus(200);
        $response2->assertJson(['success' => true]);

        // Đảm bảo không nhân đôi bản ghi FlashcardProgress
        $count = FlashcardProgress::where('user_id', $user1->id)
            ->where('flashcard_id', $flashcardId)
            ->count();
        $this->assertEquals(1, $count);
    }
}
