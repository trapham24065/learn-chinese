<?php

namespace Tests\Feature;

use App\Models\Lesson;
use App\Models\Question;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuizOptionShuffleTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Lesson $lesson;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->lesson = Lesson::create([
            'title'             => 'Bài 1: Chào hỏi',
            'slug'              => 'bai-1-chao-hoi',
            'summary'           => 'Tổng quan chào hỏi',
            'hsk_level'         => 1,
            'sort_order'        => 1,
            'estimated_minutes' => 20,
            'is_published'      => true,
        ]);
    }

    public function test_quiz_options_are_dynamically_shuffled_and_not_always_in_position_a(): void
    {
        // Tạo 20 câu hỏi có đáp án đúng ban đầu luôn nằm ở vị trí đầu tiên (A)
        for ($i = 1; $i <= 20; $i++) {
            Question::create([
                'lesson_id'      => $this->lesson->id,
                'hsk_level'      => 1,
                'question'       => "Câu hỏi thử nghiệm số {$i}?",
                'options'        => ["Đáp án đúng {$i}", "Sai A {$i}", "Sai B {$i}", "Sai C {$i}"],
                'correct_answer' => "Đáp án đúng {$i}",
                'explanation'    => "Giải thích câu {$i}",
                'difficulty'     => 'starter',
                'skill_type'     => 'vocabulary',
                'is_active'      => true,
            ]);
        }

        $res = $this->actingAs($this->user)->get(route('quiz', ['count' => 20]));
        $res->assertStatus(200);

        $viewQuestions = $res->viewData('questions');
        $this->assertNotEmpty($viewQuestions);

        $correctAtFirstIndexCount = 0;
        foreach ($viewQuestions as $q) {
            $options = $q->options;
            $this->assertCount(4, $options);
            $this->assertContains($q->correct_answer, $options);

            if ($options[0] === $q->correct_answer) {
                $correctAtFirstIndexCount++;
            }
        }

        // Nếu không xáo trộn, 100% (20/20) câu sẽ có đáp án đúng ở vị trí đầu tiên (A).
        // Khi đã xáo trộn, xác suất cả 20 câu đều rơi vào A là (1/4)^20 ≈ 0.
        // Ta khẳng định không phải toàn bộ 20 câu đều là A.
        $this->assertLessThan(20, $correctAtFirstIndexCount, "Đáp án đúng không được luôn luôn nằm ở vị trí đầu tiên A.");
    }

    public function test_quiz_evaluation_is_accurate_regardless_of_option_position(): void
    {
        $q1 = Question::create([
            'lesson_id'      => $this->lesson->id,
            'hsk_level'      => 1,
            'question'       => 'Chữ 你 có nghĩa là gì?',
            'options'        => ['Bạn, anh, chị', 'Tôi', 'Anh ấy', 'Chúng tôi'],
            'correct_answer' => 'Bạn, anh, chị',
            'explanation'    => '你 nghĩa là ngôi thứ 2 số ít.',
            'is_active'      => true,
        ]);

        $q2 = Question::create([
            'lesson_id'      => $this->lesson->id,
            'hsk_level'      => 1,
            'question'       => 'Chữ 好 có nghĩa là gì?',
            'options'        => ['Tốt, đẹp', 'Xấu', 'To', 'Nhỏ'],
            'correct_answer' => 'Tốt, đẹp',
            'explanation'    => '好 nghĩa là tốt.',
            'is_active'      => true,
        ]);

        // Submit câu trả lời đúng cho q1 và sai cho q2
        $res = $this->actingAs($this->user)->postJson(route('quiz.submit'), [
            'answers' => [
                $q1->id => 'Bạn, anh, chị',
                $q2->id => 'Xấu',
            ],
            'lesson_slug' => $this->lesson->slug,
            'duration_seconds' => 45,
        ]);

        $res->assertStatus(200);
        $this->assertEquals(2, $res->json('total_questions'));
        $this->assertEquals(1, $res->json('correct_count'));
        $this->assertEquals(50, $res->json('score'));
        $this->assertTrue($res->json("details.{$q1->id}.is_correct"));
        $this->assertFalse($res->json("details.{$q2->id}.is_correct"));
    }

    public function test_shuffle_quiz_options_artisan_command(): void
    {
        $q = Question::create([
            'lesson_id'      => $this->lesson->id,
            'hsk_level'      => 1,
            'question'       => 'Test artisan command shuffle',
            'options'        => ['Đúng', 'Sai 1', 'Sai 2', 'Sai 3'],
            'correct_answer' => 'Đúng',
            'is_active'      => true,
        ]);

        $trueFalseQ = Question::create([
            'lesson_id'      => $this->lesson->id,
            'hsk_level'      => 1,
            'question'       => 'Test câu Đúng / Sai không bị đổi thứ tự',
            'options'        => ['对', '错'],
            'correct_answer' => '对',
            'is_active'      => true,
        ]);

        $this->artisan('quiz:shuffle-options')
            ->assertExitCode(0);

        $trueFalseQ->refresh();
        $this->assertEquals(['对', '错'], $trueFalseQ->options);

        $q->refresh();
        $this->assertContains('Đúng', $q->options);
        $this->assertCount(4, $q->options);
    }
}
