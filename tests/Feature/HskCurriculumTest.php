<?php

use App\Models\Flashcard;
use App\Models\Lesson;
use App\Models\Question;
use Database\Seeders\HskCurriculumSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('hsk curriculum seeder populates hsk 1, 2, 3 lessons with flashcards and questions', function () {
    $this->seed(HskCurriculumSeeder::class);

    // HSK 1 (2 introductory foundational lessons + 15 standard textbook lessons = 17)
    $hsk1Lessons = Lesson::where('hsk_level', 1)->get();
    expect($hsk1Lessons)->toHaveCount(17);
    expect($hsk1Lessons->pluck('slug'))->toContain('pinyin-co-ban', 'chao-hoi-gioi-thieu', 'hsk1-bai-01-ni-hao');

    // Verify pinyin-co-ban rich lesson
    $pinyinLesson = $hsk1Lessons->firstWhere('slug', 'pinyin-co-ban');
    expect($pinyinLesson)->not->toBeNull();
    expect($pinyinLesson->content)->toContain('Bính âm Hán ngữ');
    expect($pinyinLesson->flashcards()->count())->toBe(10);
    expect($pinyinLesson->questions()->count())->toBe(5);

    // Verify chao-hoi-gioi-thieu rich lesson
    $chaoHoiLesson = $hsk1Lessons->firstWhere('slug', 'chao-hoi-gioi-thieu');
    expect($chaoHoiLesson)->not->toBeNull();
    expect($chaoHoiLesson->content)->toContain('Bài khóa 1');
    expect($chaoHoiLesson->flashcards()->count())->toBe(12);
    expect($chaoHoiLesson->questions()->count())->toBe(5);

    // Verify obsolete rough phonetic flashcards are purged
    expect(Flashcard::whereIn('hanzi', ['ā', 'á', 'ǎ', 'à'])->count())->toBe(0);

    // Verify standard lesson hsk1-bai-01-ni-hao
    $bai1Lesson = $hsk1Lessons->firstWhere('slug', 'hsk1-bai-01-ni-hao');
    expect($bai1Lesson)->not->toBeNull();
    expect($bai1Lesson->content)->toContain('Bài khóa 1');
    expect($bai1Lesson->content)->toContain('Ngữ pháp trọng điểm');
    expect($bai1Lesson->flashcards()->count())->toBeGreaterThanOrEqual(4);
    expect($bai1Lesson->questions()->count())->toBeGreaterThanOrEqual(2);

    // HSK 2
    $hsk2Lessons = Lesson::where('hsk_level', 2)->get();
    expect($hsk2Lessons)->toHaveCount(15);
    expect($hsk2Lessons->first()->slug)->toBe('hsk2-bai-01-jiu-yue-qu-bei-jing-lv-you-zui-hao');

    // HSK 3
    $hsk3Lessons = Lesson::where('hsk_level', 3)->get();
    expect($hsk3Lessons)->toHaveCount(10);
    expect($hsk3Lessons->first()->slug)->toBe('hsk3-bai-01-zhou-mo-ni-you-shen-me-da-suan');
});

test('artisan app:seed-hsk-curriculum command executes successfully with level option', function () {
    $this->artisan('app:seed-hsk-curriculum --level=1')
        ->assertSuccessful();

    expect(Lesson::where('hsk_level', 1)->count())->toBe(17);
    expect(Lesson::where('hsk_level', 2)->count())->toBe(0);
});

test('hsk level show page displays standard lessons', function () {
    $this->seed(HskCurriculumSeeder::class);

    $response = $this->get(route('hsk.show', 1));
    $response->assertSuccessful();
    $response->assertSee('Bài mở đầu: Pinyin cơ bản');
    $response->assertSee('pinyin-co-ban');
    $response->assertSee('Chào hỏi & Tự giới thiệu');
    $response->assertSee('chao-hoi-gioi-thieu');
    $response->assertSee('Bài 1: 你好 - Xin chào');
    $response->assertSee('hsk1-bai-01-ni-hao');
});

test('lesson show page displays rich content, dialogues, and action buttons', function () {
    $this->seed(HskCurriculumSeeder::class);

    // Check standard lesson
    $response = $this->get(route('lesson.show', ['slug' => 'hsk1-bai-01-ni-hao']));
    $response->assertSuccessful();
    $response->assertSee('Bài 1: 你好 - Xin chào');
    $response->assertSee('Bài khóa 1');
    $response->assertSee('Ngữ pháp trọng điểm');
    $response->assertSee(route('flashcards', ['lesson' => 'hsk1-bai-01-ni-hao']));
    $response->assertSee(route('quiz', ['lesson' => 'hsk1-bai-01-ni-hao']));

    // Check pinyin lesson
    $pinyinResponse = $this->get(route('lesson.show', ['slug' => 'pinyin-co-ban']));
    $pinyinResponse->assertSuccessful();
    $pinyinResponse->assertSee('Pinyin cơ bản & Ngữ âm chuẩn');
    $pinyinResponse->assertSee('Bính âm Hán ngữ');

    // Check chao-hoi-gioi-thieu lesson
    $introResponse = $this->get(route('lesson.show', ['slug' => 'chao-hoi-gioi-thieu']));
    $introResponse->assertSuccessful();
    $introResponse->assertSee('Chào hỏi & Tự giới thiệu');
});
