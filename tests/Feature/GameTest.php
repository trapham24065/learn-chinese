<?php

namespace Tests\Feature;

use App\Models\Flashcard;
use App\Models\LearningActivity;
use App\Models\User;
use App\Models\UserDailyProgress;
use App\Models\UserLearningStat;
use App\Services\GameService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Tests\TestCase;

class GameTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed some sample flashcards for games
        Flashcard::factory()->create([
            'hanzi' => '你好',
            'pinyin' => 'nǐ hǎo',
            'meaning' => 'xin chào',
            'hsk_level' => 1,
            'is_active' => true,
        ]);
        Flashcard::factory()->create([
            'hanzi' => '谢谢',
            'pinyin' => 'xièxie',
            'meaning' => 'cảm ơn',
            'hsk_level' => 1,
            'is_active' => true,
        ]);
        Flashcard::factory()->create([
            'hanzi' => '再见',
            'pinyin' => 'zàijiàn',
            'meaning' => 'tạm biệt',
            'hsk_level' => 1,
            'is_active' => true,
        ]);
        Flashcard::factory()->create([
            'hanzi' => '朋友',
            'pinyin' => 'péngyou',
            'meaning' => 'bạn bè',
            'hsk_level' => 1,
            'is_active' => true,
        ]);
        Flashcard::factory()->create([
            'hanzi' => '老师',
            'pinyin' => 'lǎoshī',
            'meaning' => 'thầy cô',
            'hsk_level' => 1,
            'is_active' => true,
        ]);
        Flashcard::factory()->create([
            'hanzi' => '学生',
            'pinyin' => 'xuésheng',
            'meaning' => 'học sinh',
            'hsk_level' => 1,
            'is_active' => true,
        ]);
    }

    public function test_games_hub_page_can_be_rendered(): void
    {
        $response = $this->get(route('games.index'));
        $response->assertStatus(200);
        $response->assertSee('Mini-Game Học Tập Tiếng Trung');
        $response->assertSee('Fast Match');
        $response->assertSee('Audio Pop Quiz');
    }

    public function test_fast_match_session_data_and_card_integrity(): void
    {
        $response = $this->getJson(route('games.fast-match.data', [
            'difficulty' => 'easy',
            'mode' => 'practice',
        ]));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'token',
                'cards',
                'total_pairs',
                'difficulty',
                'mode',
            ],
        ]);

        $data = $response->json('data');
        $this->assertEquals(4, $data['total_pairs']);
        $this->assertCount(8, $data['cards']);

        // Assert client cards do not reveal the private pair ID
        foreach ($data['cards'] as $card) {
            $this->assertArrayNotHasKey('pair_id', $card);
            $this->assertArrayHasKey('id', $card);
            $this->assertArrayHasKey('pair_hash', $card);
            $this->assertArrayHasKey('type', $card);
            $this->assertArrayHasKey('content', $card);
        }
    }

    public function test_fast_match_tampered_token_is_rejected(): void
    {
        $response = $this->postJson(route('games.fast-match.finish'), [
            'token' => 'invalid_forged_token_12345',
            'moves' => [],
            'duration_seconds' => 30,
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
        ]);
    }

    public function test_fast_match_verifies_moves_and_awards_xp_and_tracks_practice(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $gameService = app(GameService::class);
        $session = $gameService->createFastMatchSession('practice', 'easy', null, null, $user);

        // Decrypt token to extract matching card pairs
        $payload = json_decode(Crypt::decryptString($session['token']), true);
        $cardToPair = $payload['card_to_pair'];

        // Group cards by pair
        $grouped = [];
        foreach ($cardToPair as $cardId => $pairId) {
            $grouped[$pairId][] = $cardId;
        }

        // Simulate 4 perfect moves
        $moves = [];
        foreach ($grouped as $pairId => $cards) {
            $moves[] = ['c1' => $cards[0], 'c2' => $cards[1]];
        }

        $response = $this->postJson(route('games.fast-match.finish'), [
            'token' => $session['token'],
            'moves' => $moves,
            'duration_seconds' => 25,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'accuracy' => 100,
            'max_combo' => 4,
            'solved_pairs' => 4,
            'medal' => 'gold',
            'xp_earned' => 15,
        ]);

        // Check database records
        $this->assertDatabaseHas('learning_activities', [
            'user_id' => $user->id,
            'activity_type' => 'fast_match_completed',
            'xp_earned' => 15,
        ]);

        $today = now(config('app.timezone', 'Asia/Ho_Chi_Minh'))->toDateString();
        $this->assertDatabaseHas('user_daily_progress', [
            'user_id' => $user->id,
            'date' => $today,
            'practice_count' => 1,
            'fast_match_count' => 1,
        ]);
    }

    public function test_fast_match_anti_farming_diminishing_returns(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $gameService = app(GameService::class);

        // Pre-complete daily goal so daily goal completion bonus (+20 XP) doesn't confound base XP test
        $today = now(config('app.timezone', 'Asia/Ho_Chi_Minh'))->toDateString();
        UserDailyProgress::create([
            'user_id' => $user->id,
            'date' => $today,
            'is_goal_completed' => true,
        ]);

        // Play 1: 15 XP
        $s1 = $gameService->createFastMatchSession('practice', 'easy', null, null, $user);
        $res1 = $gameService->verifyFastMatchSession($s1['token'], [], 20, $user);
        $this->assertEquals(15, $res1['xp_earned']);

        // Play 2: 5 XP
        $s2 = $gameService->createFastMatchSession('practice', 'easy', null, null, $user);
        $res2 = $gameService->verifyFastMatchSession($s2['token'], [], 20, $user);
        $this->assertEquals(5, $res2['xp_earned']);

        // Play 3: 5 XP
        $s3 = $gameService->createFastMatchSession('practice', 'easy', null, null, $user);
        $res3 = $gameService->verifyFastMatchSession($s3['token'], [], 20, $user);
        $this->assertEquals(5, $res3['xp_earned']);

        // Play 4: 0 XP (anti-farming cap reached)
        $s4 = $gameService->createFastMatchSession('practice', 'easy', null, null, $user);
        $res4 = $gameService->verifyFastMatchSession($s4['token'], [], 20, $user);
        $this->assertEquals(0, $res4['xp_earned']);
    }

    public function test_audio_quiz_session_data_and_modes(): void
    {
        // Minimal pairs mode
        $res1 = $this->getJson(route('games.audio-quiz.data', ['mode' => 'minimal_pairs']));
        $res1->assertStatus(200);
        $res1->assertJsonStructure([
            'success',
            'data' => [
                'token',
                'questions',
                'total_questions',
                'mode',
            ],
        ]);
        $this->assertEquals(5, $res1->json('data.total_questions'));

        // Tone recognition mode
        $res2 = $this->getJson(route('games.audio-quiz.data', ['mode' => 'tone_recognition']));
        $res2->assertStatus(200);
        $this->assertEquals(5, $res2->json('data.total_questions'));
    }

    public function test_audio_quiz_finish_verifies_answers_and_captures_missed_questions(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $gameService = app(GameService::class);
        $session = $gameService->createAudioQuizSession('minimal_pairs', null, $user);

        // Decrypt token to inspect correct answers
        $payload = json_decode(Crypt::decryptString($session['token']), true);
        $answersKey = $payload['answers_key'];

        $qIds = array_keys($answersKey);
        $answers = [];

        // Answer 3 correctly, 2 incorrectly
        foreach ($qIds as $idx => $qId) {
            if ($idx < 3) {
                $answers[$qId] = [
                    'selected' => $answersKey[$qId]['correct'],
                    'response_ms' => 1500, // fast speed
                ];
            } else {
                $answers[$qId] = [
                    'selected' => 'wrong_answer_option',
                    'response_ms' => 4000,
                ];
            }
        }

        $response = $this->postJson(route('games.audio-quiz.finish'), [
            'token' => $session['token'],
            'answers' => $answers,
            'duration_seconds' => 20,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'correct_count' => 3,
            'total_questions' => 5,
            'accuracy' => 60,
        ]);

        $missedWords = $response->json('missed_words');
        $this->assertCount(2, $missedWords);
        $this->assertArrayHasKey('pinyin_link', $missedWords[0]);

        $today = now(config('app.timezone', 'Asia/Ho_Chi_Minh'))->toDateString();
        $this->assertDatabaseHas('user_daily_progress', [
            'user_id' => $user->id,
            'date' => $today,
            'practice_count' => 1,
            'audio_quiz_count' => 1,
        ]);
    }
}
