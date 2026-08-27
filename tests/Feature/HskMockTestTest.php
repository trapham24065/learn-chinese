<?php

namespace Tests\Feature;

use App\Models\MockTest;
use App\Models\Question;
use App\Models\User;
use Database\Seeders\HskMockExamQuestionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HskMockTestTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(HskMockExamQuestionSeeder::class);
    }

    public function test_mock_test_index_page_is_accessible(): void
    {
        $response = $this->get(route('hsk.mock.index'));

        $response->assertStatus(200);
        $response->assertSee('Thi thử HSK mô phỏng');
        $response->assertSee('HSK 1');
        $response->assertSee('HSK 2');
        $response->assertSee('HSK 3');
    }

    public function test_mock_test_exam_room_loads_with_questions(): void
    {
        $response = $this->get(route('hsk.mock.start', 1));

        $response->assertStatus(200);
        $response->assertSee('Bài thi thử mô phỏng HSK 1');
        $response->assertSee('Bảng câu hỏi');
        $response->assertSee('Nộp bài thi');
    }

    public function test_submitting_mock_test_calculates_scores_and_issues_certificate_when_passed(): void
    {
        $student = User::factory()->create([
            'role' => User::ROLE_STUDENT,
        ]);

        $questions = Question::where('is_active', true)->where('hsk_level', 1)->get();
        $this->assertNotEmpty($questions);

        // Prepare perfect answers
        $answers = [];
        foreach ($questions as $q) {
            $answers[$q->id] = $q->correct_answer;
        }

        $response = $this->actingAs($student)->postJson(route('hsk.mock.submit', 1), [
            'answers'          => $answers,
            'duration_seconds' => 600, // 10 minutes
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success'     => true,
            'passed'      => true,
            'total_score' => 300,
        ]);

        $this->assertDatabaseHas('mock_tests', [
            'user_id'     => $student->id,
            'hsk_level'   => 1,
            'passed'      => true,
            'total_score' => 300,
        ]);

        $mockTest = MockTest::where('user_id', $student->id)->latest()->first();
        $this->assertNotNull($mockTest->certificate_code);
        $this->assertStringStartsWith('LC-HSK1-', $mockTest->certificate_code);
    }

    public function test_submitting_mock_test_with_low_score_does_not_issue_certificate(): void
    {
        $student = User::factory()->create([
            'role' => User::ROLE_STUDENT,
        ]);

        $questions = Question::where('is_active', true)->where('hsk_level', 1)->get();

        // Prepare wrong answers
        $answers = [];
        foreach ($questions as $q) {
            $answers[$q->id] = 'Đáp án hoàn toàn sai';
        }

        $response = $this->actingAs($student)->postJson(route('hsk.mock.submit', 1), [
            'answers'          => $answers,
            'duration_seconds' => 300,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success'     => true,
            'passed'      => false,
            'total_score' => 0,
        ]);

        $mockTest = MockTest::where('user_id', $student->id)->latest()->first();
        $this->assertNull($mockTest->certificate_code);
        $this->assertFalse($mockTest->passed);
    }

    public function test_detailed_result_page_is_viewable(): void
    {
        $student = User::factory()->create([
            'role' => User::ROLE_STUDENT,
        ]);

        $mockTest = MockTest::create([
            'user_id'            => $student->id,
            'hsk_level'          => 1,
            'title'              => 'Bài thi thử mô phỏng HSK 1',
            'total_questions'    => 30,
            'correct_answers'    => 28,
            'total_score'        => 280,
            'max_score'          => 300,
            'listening_score'    => 90,
            'reading_score'      => 100,
            'grammar_score'      => 90,
            'listening_total'    => 10,
            'listening_correct'  => 9,
            'reading_total'      => 10,
            'reading_correct'    => 10,
            'grammar_total'      => 10,
            'grammar_correct'    => 9,
            'duration_seconds'   => 850,
            'time_limit_minutes' => 20,
            'passed'             => true,
            'certificate_code'   => 'LC-HSK1-TEST01',
            'details'            => [],
            'completed_at'       => now(),
        ]);

        $response = $this->actingAs($student)->get(route('hsk.mock.result', $mockTest->id));

        $response->assertStatus(200);
        $response->assertSee('280');
        $response->assertSee('Phần 1: Nghe hiểu');
        $response->assertSee('Phần 2: Đọc hiểu');
        $response->assertSee('Phần 3: Ngữ pháp');
        $response->assertSee('LC-HSK1-TEST01');
    }

    public function test_public_certificate_page_is_accessible_by_code(): void
    {
        $mockTest = MockTest::create([
            'user_id'            => null,
            'hsk_level'          => 1,
            'title'              => 'Bài thi thử mô phỏng HSK 1',
            'total_questions'    => 30,
            'correct_answers'    => 30,
            'total_score'        => 300,
            'max_score'          => 300,
            'listening_score'    => 100,
            'reading_score'      => 100,
            'grammar_score'      => 100,
            'listening_total'    => 10,
            'listening_correct'  => 10,
            'reading_total'      => 10,
            'reading_correct'    => 10,
            'grammar_total'      => 10,
            'grammar_correct'    => 10,
            'duration_seconds'   => 500,
            'time_limit_minutes' => 20,
            'passed'             => true,
            'certificate_code'   => 'LC-HSK1-CERT99',
            'details'            => [],
            'completed_at'       => now(),
        ]);

        $response = $this->get(route('hsk.mock.certificate', 'LC-HSK1-CERT99'));

        $response->assertStatus(200);
        $response->assertSee('汉语水平考试模拟合格证书');
        $response->assertSee('LC-HSK1-CERT99');
        $response->assertSee('300');
    }
}
