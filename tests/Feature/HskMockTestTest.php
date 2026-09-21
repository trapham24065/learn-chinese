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

    public function test_hsk1_exam_room_provides_picture_questions_and_multimedia_metadata(): void
    {
        $response = $this->get(route('hsk.mock.start', 1));
        $response->assertStatus(200);

        $questions = $response->viewData('questions');
        $this->assertCount(20, $questions);

        // Verify presence of picture_true_false and picture_choice question types
        $types = $questions->pluck('question_type')->unique()->toArray();
        $this->assertContains('picture_true_false', $types);
        $this->assertContains('picture_choice', $types);

        // Verify at least some questions contain image or image_set
        $hasImage = $questions->filter(fn ($q) => ! empty($q['image']) || ! empty($q['image_set']));
        $this->assertGreaterThanOrEqual(15, $hasImage->count());
    }

    public function test_submitting_picture_true_false_and_picture_choice_evaluates_accurately(): void
    {
        $student = User::factory()->create(['role' => User::ROLE_STUDENT]);

        // Fetch HSK 1 questions
        $questions = Question::where('is_active', true)->where('hsk_level', 1)->get();
        $this->assertNotEmpty($questions);

        $answers = [];
        foreach ($questions as $q) {
            $answers[$q->id] = $q->correct_answer;
        }

        $response = $this->actingAs($student)->postJson(route('hsk.mock.submit', 1), [
            'answers'          => $answers,
            'duration_seconds' => 450,
            'exam_standard'    => 'hsk_2_0',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success'     => true,
            'passed'      => true,
            'total_score' => 200,
        ]);

        $this->assertDatabaseHas('mock_tests', [
            'user_id'       => $student->id,
            'hsk_level'     => 1,
            'exam_standard' => 'hsk_2_0',
            'passed'        => true,
            'total_score'   => 200,
        ]);
    }

    public function test_hsk1_result_page_renders_multimedia_review(): void
    {
        $student = User::factory()->create(['role' => User::ROLE_STUDENT]);

        $mockTest = MockTest::create([
            'user_id'            => $student->id,
            'hsk_level'          => 1,
            'exam_standard'      => 'hsk_2_0',
            'title'              => 'Bài thi thử mô phỏng HSK 1',
            'total_questions'    => 2,
            'correct_answers'    => 2,
            'total_score'        => 200,
            'max_score'          => 200,
            'listening_score'    => 100,
            'reading_score'      => 100,
            'grammar_score'      => 0,
            'listening_total'    => 1,
            'listening_correct'  => 1,
            'reading_total'      => 1,
            'reading_correct'    => 1,
            'grammar_total'      => 0,
            'grammar_correct'    => 0,
            'duration_seconds'   => 200,
            'time_limit_minutes' => 25,
            'passed'             => true,
            'certificate_code'   => 'LC-HSK1-MEDIA01',
            'details'            => [
                [
                    'id'             => 1,
                    'skill_type'     => 'listening',
                    'question_type'  => 'picture_true_false',
                    'image'          => '/images/hsk/mock/hsk1/he_cha.svg',
                    'image_alt'      => 'Uống trà',
                    'image_set'      => null,
                    'question'       => '听录音判断对错：听到的内容与图片是否一致？',
                    'pinyin'         => 'hē chá',
                    'audio_text'     => '喝茶',
                    'options'        => ['对', '错'],
                    'user_answer'    => '对',
                    'correct_answer' => '对',
                    'is_correct'     => true,
                    'explanation'    => 'Uống trà',
                ],
                [
                    'id'             => 2,
                    'skill_type'     => 'reading',
                    'question_type'  => 'picture_choice',
                    'image'          => null,
                    'image_alt'      => null,
                    'image_set'      => [
                        ['key' => 'A', 'image' => 'images/hsk/mock/hsk1/chuzuche.svg', 'alt' => 'Taxi'],
                        ['key' => 'B', 'image' => 'images/hsk/mock/hsk1/feiji.svg', 'alt' => 'Máy bay'],
                    ],
                    'question'       => '看句子选择相符的图片：',
                    'pinyin'         => null,
                    'audio_text'     => null,
                    'options'        => ['A', 'B'],
                    'user_answer'    => 'A',
                    'correct_answer' => 'A',
                    'is_correct'     => true,
                    'explanation'    => 'Taxi',
                ],
            ],
            'completed_at'       => now(),
        ]);

        $response = $this->actingAs($student)->get(route('hsk.mock.result', $mockTest->id));

        $response->assertStatus(200);
        $response->assertSee('he_cha.svg');
        $response->assertSee('chuzuche.svg');
        $response->assertSee('feiji.svg');
    }

    public function test_hsk2_exam_room_loads_multimedia_picture_questions(): void
    {
        $student = User::factory()->create(['role' => User::ROLE_STUDENT]);

        $response = $this->actingAs($student)->get(route('hsk.mock.start', 2));

        $response->assertStatus(200);
        $response->assertViewHas('questions');

        $questions = $response->viewData('questions');
        $this->assertNotEmpty($questions);

        // Ensure questions in exam room include HSK 2 SVGs and picture question types
        $hasPictureQuestion = false;
        $hasHsk2Svg = false;

        foreach ($questions as $q) {
            if (in_array($q['question_type'] ?? '', ['picture_true_false', 'picture_choice'])) {
                $hasPictureQuestion = true;
            }
            if (str_contains($q['image'] ?? '', 'images/hsk/mock/hsk2/')) {
                $hasHsk2Svg = true;
            }
            if (!empty($q['image_set'])) {
                foreach ($q['image_set'] as $imgItem) {
                    if (str_contains($imgItem['image'] ?? '', 'images/hsk/mock/hsk2/')) {
                        $hasHsk2Svg = true;
                    }
                }
            }
        }

        $this->assertTrue($hasPictureQuestion, 'HSK 2 exam room should contain picture question types.');
        $this->assertTrue($hasHsk2Svg, 'HSK 2 exam room should reference HSK 2 SVGs.');
    }

    public function test_submitting_hsk2_mock_test_scores_on_200_point_scale_with_picture_answers(): void
    {
        $student = User::factory()->create(['role' => User::ROLE_STUDENT]);

        $questions = Question::where('is_active', true)->where('hsk_level', 2)->get();
        $this->assertNotEmpty($questions);

        $answers = [];
        foreach ($questions as $q) {
            $answers[$q->id] = $q->correct_answer;
        }

        $response = $this->actingAs($student)->postJson(route('hsk.mock.submit', 2), [
            'answers'          => $answers,
            'duration_seconds' => 600,
            'exam_standard'    => 'hsk_2_0',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success'     => true,
            'passed'      => true,
            'total_score' => 200,
        ]);

        $this->assertDatabaseHas('mock_tests', [
            'user_id'       => $student->id,
            'hsk_level'     => 2,
            'exam_standard' => 'hsk_2_0',
            'passed'        => true,
            'total_score'   => 200,
            'max_score'     => 200,
        ]);

        $mockTest = MockTest::where('user_id', $student->id)->latest()->first();
        $this->assertNotNull($mockTest->certificate_code);
        $this->assertStringStartsWith('LC-HSK2-', $mockTest->certificate_code);
    }

    public function test_hsk3_exam_room_loads_multimedia_picture_questions_and_all_three_skills(): void
    {
        $student = User::factory()->create(['role' => User::ROLE_STUDENT]);

        $response = $this->actingAs($student)->get(route('hsk.mock.start', 3));

        $response->assertStatus(200);
        $response->assertViewHas('questions');

        $questions = $response->viewData('questions');
        $this->assertNotEmpty($questions);

        // Ensure 3 skills are present
        $skills = $questions->pluck('skill_type')->unique()->toArray();
        $this->assertContains('listening', $skills);
        $this->assertContains('reading', $skills);
        $this->assertContains('grammar', $skills);

        // Ensure questions in exam room include HSK 3 SVGs and picture question types
        $hasPictureQuestion = false;
        $hasHsk3Svg = false;

        foreach ($questions as $q) {
            if (in_array($q['question_type'] ?? '', ['picture_true_false', 'picture_choice'])) {
                $hasPictureQuestion = true;
            }
            if (str_contains($q['image'] ?? '', 'images/hsk/mock/hsk3/')) {
                $hasHsk3Svg = true;
            }
            if (!empty($q['image_set'])) {
                foreach ($q['image_set'] as $imgItem) {
                    if (str_contains($imgItem['image'] ?? '', 'images/hsk/mock/hsk3/')) {
                        $hasHsk3Svg = true;
                    }
                }
            }
        }

        $this->assertTrue($hasPictureQuestion, 'HSK 3 exam room should contain picture question types.');
        $this->assertTrue($hasHsk3Svg, 'HSK 3 exam room should reference HSK 3 SVGs.');
    }

    public function test_submitting_hsk3_mock_test_scores_on_300_point_scale_with_picture_answers(): void
    {
        $student = User::factory()->create(['role' => User::ROLE_STUDENT]);

        $questions = Question::where('is_active', true)->where('hsk_level', 3)->get();
        $this->assertNotEmpty($questions);

        $answers = [];
        foreach ($questions as $q) {
            $answers[$q->id] = $q->correct_answer;
        }

        $response = $this->actingAs($student)->postJson(route('hsk.mock.submit', 3), [
            'answers'          => $answers,
            'duration_seconds' => 900,
            'exam_standard'    => 'hsk_2_0',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success'     => true,
            'passed'      => true,
            'total_score' => 300,
        ]);

        $this->assertDatabaseHas('mock_tests', [
            'user_id'       => $student->id,
            'hsk_level'     => 3,
            'exam_standard' => 'hsk_2_0',
            'passed'        => true,
            'total_score'   => 300,
            'max_score'     => 300,
        ]);

        $mockTest = MockTest::where('user_id', $student->id)->latest()->first();
        $this->assertNotNull($mockTest->certificate_code);
        $this->assertStringStartsWith('LC-HSK3-', $mockTest->certificate_code);
    }
}

