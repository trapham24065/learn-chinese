<?php

use App\Models\Flashcard;
use App\Models\Lesson;
use App\Models\Question;
use Database\Seeders\HskCurriculumSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('hsk curriculum seeder populates hsk 1, 2, 3 lessons with flashcards and questions', function () {
    $this->seed(HskCurriculumSeeder::class);

    // HSK 1
    $hsk1Lessons = Lesson::where('hsk_level', 1)->get();
    expect($hsk1Lessons)->toHaveCount(15);
    expect($hsk1Lessons->first()->slug)->toBe('hsk1-bai-01-ni-hao');
    expect($hsk1Lessons->first()->content)->toContain('Bài khóa 1');
    expect($hsk1Lessons->first()->content)->toContain('Ngữ pháp trọng điểm');

    // HSK 2
    $hsk2Lessons = Lesson::where('hsk_level', 2)->get();
    expect($hsk2Lessons)->toHaveCount(15);
    expect($hsk2Lessons->first()->slug)->toBe('hsk2-bai-01-jiu-yue-qu-bei-jing-lv-you-zui-hao');

    // HSK 3
    $hsk3Lessons = Lesson::where('hsk_level', 3)->get();
    expect($hsk3Lessons)->toHaveCount(10);
    expect($hsk3Lessons->first()->slug)->toBe('hsk3-bai-01-zhou-mo-ni-you-shen-me-da-suan');

    // Flashcards & Questions linked to lessons
    $firstLesson = $hsk1Lessons->first();
    expect($firstLesson->flashcards()->count())->toBeGreaterThanOrEqual(4);
    expect($firstLesson->questions()->count())->toBeGreaterThanOrEqual(2);
});

test('artisan app:seed-hsk-curriculum command executes successfully with level option', function () {
    $this->artisan('app:seed-hsk-curriculum --level=1')
        ->assertSuccessful();

    expect(Lesson::where('hsk_level', 1)->count())->toBe(15);
    expect(Lesson::where('hsk_level', 2)->count())->toBe(0);
});

test('hsk level show page displays standard lessons', function () {
    $this->seed(HskCurriculumSeeder::class);

    $response = $this->get(route('hsk.show', 1));
    $response->assertSuccessful();
    $response->assertSee('Bài 1: 你好 - Xin chào');
    $response->assertSee('hsk1-bai-01-ni-hao');
});

test('lesson show page displays rich content, dialogues, and action buttons', function () {
    $this->seed(HskCurriculumSeeder::class);

    $response = $this->get(route('lesson.show', ['slug' => 'hsk1-bai-01-ni-hao']));
    $response->assertSuccessful();
    $response->assertSee('Bài 1: 你好 - Xin chào');
    $response->assertSee('Bài khóa 1');
    $response->assertSee('Ngữ pháp trọng điểm');
    $response->assertSee(route('flashcards', ['lesson' => 'hsk1-bai-01-ni-hao']));
    $response->assertSee(route('quiz', ['lesson' => 'hsk1-bai-01-ni-hao']));
});
