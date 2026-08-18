<?php

use App\Models\Lesson;
use App\Models\Question;
use App\Models\StudySession;
use App\Models\User;

test('quiz page can be rendered with dynamic questions', function () {
    $lesson = Lesson::factory()->create([
        'slug' => 'test-lesson',
        'title' => 'Bài học test',
        'is_published' => true,
    ]);

    $question = Question::create([
        'lesson_id' => $lesson->id,
        'question' => '你好 là gì?',
        'pinyin' => 'nǐ hǎo',
        'options' => ['Xin chào', 'Cảm ơn', 'Tạm biệt', 'Xin lỗi'],
        'correct_answer' => 'Xin chào',
        'explanation' => 'Lời chào cơ bản',
        'difficulty' => 'starter',
        'sort_order' => 1,
        'is_active' => true,
    ]);

    $response = $this->get('/quiz');

    $response->assertSuccessful();
    $response->assertSee('你好 là gì?');
    $response->assertSee('Xin chào');
});

test('quiz can be filtered by lesson', function () {
    $lesson1 = Lesson::factory()->create([
        'slug' => 'lesson-1',
        'title' => 'Chủ đề 1',
        'is_published' => true,
    ]);

    $lesson2 = Lesson::factory()->create([
        'slug' => 'lesson-2',
        'title' => 'Chủ đề 2',
        'is_published' => true,
    ]);

    Question::create([
        'lesson_id' => $lesson1->id,
        'question' => 'Câu hỏi chủ đề 1',
        'options' => ['A', 'B'],
        'correct_answer' => 'A',
        'is_active' => true,
    ]);

    Question::create([
        'lesson_id' => $lesson2->id,
        'question' => 'Câu hỏi chủ đề 2',
        'options' => ['C', 'D'],
        'correct_answer' => 'C',
        'is_active' => true,
    ]);

    $response = $this->get('/quiz?lesson=lesson-1');

    $response->assertSuccessful();
    $response->assertSee('Câu hỏi chủ đề 1');
    $response->assertDontSee('Câu hỏi chủ đề 2');
});

test('submitting quiz calculates score and saves study session for student', function () {
    $student = User::factory()->create([
        'role' => User::ROLE_STUDENT,
    ]);

    $lesson = Lesson::factory()->create([
        'slug' => 'lesson-test-submit',
        'is_published' => true,
    ]);

    $q1 = Question::create([
        'lesson_id' => $lesson->id,
        'question' => 'Câu 1?',
        'options' => ['Đáp án 1', 'Đáp án 2'],
        'correct_answer' => 'Đáp án 1',
        'is_active' => true,
    ]);

    $q2 = Question::create([
        'lesson_id' => $lesson->id,
        'question' => 'Câu 2?',
        'options' => ['Đúng', 'Sai'],
        'correct_answer' => 'Đúng',
        'is_active' => true,
    ]);

    $response = $this->actingAs($student, 'web')->postJson('/quiz/submit', [
        'answers' => [
            $q1->id => 'Đáp án 1', // correct
            $q2->id => 'Sai',      // wrong
        ],
        'lesson_slug' => 'lesson-test-submit',
        'duration_seconds' => 45,
    ]);

    $response->assertSuccessful();
    $response->assertJson([
        'success' => true,
        'score' => 50,
        'correct_count' => 1,
        'total_questions' => 2,
    ]);

    $this->assertDatabaseHas('study_sessions', [
        'user_id' => $student->id,
        'session_type' => 'quiz',
        'score' => 50,
    ]);
});

test('admin can access question resource in filament', function () {
    $admin = User::factory()->create([
        'role' => User::ROLE_ADMIN,
    ]);

    $response = $this->actingAs($admin, 'admin')->get('/admin/questions');

    $response->assertSuccessful();
});
