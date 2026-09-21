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

    public function test_hsk1_exam_room_has_no_writing_questions_and_isolates_level(): void
    {
        $response = $this->get(route('hsk.mock.start', 1));

        $response->assertStatus(200);
        $response->assertSee('Bài thi thử mô phỏng HSK 1');
        $response->assertSee('Bảng câu hỏi');
        $response->assertSee('Nộp bài thi');

        $questions = $response->viewData('questions');
        $this->assertCount(20, $questions);

        // International standard for HSK 1: Only Listening and Reading, NO Grammar/Writing
        $skillTypes = $questions->pluck('skill_type')->unique()->toArray();
        $this->assertContains('listening', $skillTypes);
        $this->assertContains('reading', $skillTypes);
        $this->assertNotContains('grammar', $skillTypes);

        // Verify all questions belong strictly to HSK level 1
        $questionIds = $questions->pluck('id')->toArray();
        $nonLevel1Questions = Question::whereIn('id', $questionIds)
            ->where('hsk_level', '!=', 1)
            ->count();
        $this->assertEquals(0, $nonLevel1Questions, 'HSK 1 exam room must only contain level 1 questions.');
    }

    public function test_hsk2_exam_room_has_no_writing_questions_and_isolates_level(): void
    {
        $response = $this->get(route('hsk.mock.start', 2));

        $response->assertStatus(200);
        $response->assertSee('Bài thi thử mô phỏng HSK 2');

        $questions = $response->viewData('questions');
        $this->assertCount(25, $questions);

        // International standard for HSK 2: Only Listening and Reading, NO Grammar/Writing
        $skillTypes = $questions->pluck('skill_type')->unique()->toArray();
        $this->assertContains('listening', $skillTypes);
        $this->assertContains('reading', $skillTypes);
        $this->assertNotContains('grammar', $skillTypes);

        // Verify all questions belong strictly to HSK level 2
        $questionIds = $questions->pluck('id')->toArray();
        $nonLevel2Questions = Question::whereIn('id', $questionIds)
            ->where('hsk_level', '!=', 2)
            ->count();
        $this->assertEquals(0, $nonLevel2Questions, 'HSK 2 exam room must only contain level 2 questions.');
    }

    public function test_hsk3_exam_room_has_all_three_skills_and_isolates_level(): void
    {
        $response = $this->get(route('hsk.mock.start', 3));

        $response->assertStatus(200);
        $response->assertSee('Bài thi thử mô phỏng HSK 3');

        $questions = $response->viewData('questions');
        $this->assertCount(30, $questions);

        // International standard for HSK 3: Listening, Reading, and Writing/Grammar
        $skillTypes = $questions->pluck('skill_type')->unique()->toArray();
        $this->assertContains('listening', $skillTypes);
        $this->assertContains('reading', $skillTypes);
        $this->assertContains('grammar', $skillTypes);

        // Verify all questions belong strictly to HSK level 3
        $questionIds = $questions->pluck('id')->toArray();
        $nonLevel3Questions = Question::whereIn('id', $questionIds)
            ->where('hsk_level', '!=', 3)
            ->count();
        $this->assertEquals(0, $nonLevel3Questions, 'HSK 3 exam room must only contain level 3 questions.');
    }

    public function test_submitting_hsk1_mock_test_scores_on_200_point_scale_and_issues_certificate(): void
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
            'total_score' => 200, // Max 200 for HSK 1
        ]);

        $this->assertDatabaseHas('mock_tests', [
            'user_id'     => $student->id,
            'hsk_level'   => 1,
            'passed'      => true,
            'total_score' => 200,
            'max_score'   => 200,
        ]);

        $mockTest = MockTest::where('user_id', $student->id)->latest()->first();
        $this->assertNotNull($mockTest->certificate_code);
        $this->assertStringStartsWith('LC-HSK1-', $mockTest->certificate_code);
    }

    public function test_submitting_hsk3_mock_test_scores_on_300_point_scale_with_three_skills(): void
    {
        $student = User::factory()->create([
            'role' => User::ROLE_STUDENT,
        ]);

        $questions = Question::where('is_active', true)->where('hsk_level', 3)->get();
        $this->assertNotEmpty($questions);

        // Prepare perfect answers
        $answers = [];
        foreach ($questions as $q) {
            $answers[$q->id] = $q->correct_answer;
        }

        $response = $this->actingAs($student)->postJson(route('hsk.mock.submit', 3), [
            'answers'          => $answers,
            'duration_seconds' => 900,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success'     => true,
            'passed'      => true,
            'total_score' => 300, // Max 300 for HSK 3
        ]);

        $this->assertDatabaseHas('mock_tests', [
            'user_id'     => $student->id,
            'hsk_level'   => 3,
            'passed'      => true,
            'total_score' => 300,
            'max_score'   => 300,
        ]);

        $mockTest = MockTest::where('user_id', $student->id)->latest()->first();
        $this->assertNotNull($mockTest->certificate_code);
        $this->assertStringStartsWith('LC-HSK3-', $mockTest->certificate_code);
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

    public function test_submitting_mock_test_with_only_one_answer_out_of_all_questions_does_not_pass(): void
    {
        $student = User::factory()->create([
            'role' => User::ROLE_STUDENT,
        ]);

        $questions = Question::where('is_active', true)->where('hsk_level', 1)->get();
        $this->assertGreaterThanOrEqual(10, $questions->count());

        $firstQ = $questions->first();

        // User only answers 1 question correctly out of all questions
        $response = $this->actingAs($student)->postJson(route('hsk.mock.submit', 1), [
            'question_ids'     => $questions->pluck('id')->toArray(),
            'answers'          => [
                $firstQ->id => $firstQ->correct_answer,
            ],
            'duration_seconds' => 120,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'passed'  => false,
        ]);

        $mockTest = MockTest::where('user_id', $student->id)->latest()->first();
        $this->assertEquals($questions->count(), $mockTest->total_questions);
        $this->assertEquals(1, $mockTest->correct_answers);
        $this->assertLessThan(120, $mockTest->total_score); // HSK 1 pass score is 120
        $this->assertFalse($mockTest->passed);
        $this->assertNull($mockTest->certificate_code);
    }

    public function test_detailed_result_page_is_viewable(): void
    {
        $student = User::factory()->create([
            'role' => User::ROLE_STUDENT,
        ]);

        // HSK 1 has max_score = 200 and no grammar skill
        $mockTest = MockTest::create([
            'user_id'            => $student->id,
            'hsk_level'          => 1,
            'title'              => 'Bài thi thử mô phỏng HSK 1',
            'total_questions'    => 20,
            'correct_answers'    => 19,
            'total_score'        => 190,
            'max_score'          => 200,
            'listening_score'    => 90,
            'reading_score'      => 100,
            'grammar_score'      => 0,
            'listening_total'    => 10,
            'listening_correct'  => 9,
            'reading_total'      => 10,
            'reading_correct'    => 10,
            'grammar_total'      => 0,
            'grammar_correct'    => 0,
            'duration_seconds'   => 850,
            'time_limit_minutes' => 25,
            'passed'             => true,
            'certificate_code'   => 'LC-HSK1-TEST01',
            'details'            => [],
            'completed_at'       => now(),
        ]);

        $response = $this->actingAs($student)->get(route('hsk.mock.result', $mockTest->id));

        $response->assertStatus(200);
        $response->assertSee('190');
        $response->assertSee('200');
        $response->assertSee('Phần 1: Nghe hiểu');
        $response->assertSee('Phần 2: Đọc hiểu');
        $response->assertDontSee('Phần 3: Viết & Ngữ pháp');
        $response->assertSee('LC-HSK1-TEST01');
    }

    public function test_public_certificate_page_is_accessible_by_code(): void
    {
        $mockTest = MockTest::create([
            'user_id'            => null,
            'hsk_level'          => 1,
            'title'              => 'Bài thi thử mô phỏng HSK 1',
            'total_questions'    => 20,
            'correct_answers'    => 20,
            'total_score'        => 200,
            'max_score'          => 200,
            'listening_score'    => 100,
            'reading_score'      => 100,
            'grammar_score'      => 0,
            'listening_total'    => 10,
            'listening_correct'  => 10,
            'reading_total'      => 10,
            'reading_correct'    => 10,
            'grammar_total'      => 0,
            'grammar_correct'    => 0,
            'duration_seconds'   => 500,
            'time_limit_minutes' => 25,
            'passed'             => true,
            'certificate_code'   => 'LC-HSK1-CERT99',
            'details'            => [],
            'completed_at'       => now(),
        ]);

        $response = $this->get(route('hsk.mock.certificate', 'LC-HSK1-CERT99'));

        $response->assertStatus(200);
        $response->assertSee('汉语水平考试模拟合格证书');
        $response->assertSee('LC-HSK1-CERT99');
        $response->assertSee('200');
    }
}
