<?php

use App\Models\Flashcard;
use App\Models\FlashcardProgress;
use App\Models\Lesson;
use App\Models\User;

test('guest cannot toggle star on flashcard', function () {
    $flashcard = Flashcard::create([
        'hanzi' => '猫',
        'pinyin' => 'māo',
        'meaning' => 'Con mèo',
        'hsk_level' => 1,
        'is_active' => true,
        'sort_order' => 1,
    ]);

    $response = $this->postJson(route('flashcards.toggleStar'), [
        'flashcard_id' => $flashcard->id,
    ]);

    $response->assertStatus(401);
});

test('authenticated user can star and unstar a flashcard', function () {
    $user = User::factory()->create();
    $flashcard = Flashcard::create([
        'hanzi' => '狗',
        'pinyin' => 'gǒu',
        'meaning' => 'Con chó',
        'hsk_level' => 1,
        'is_active' => true,
        'sort_order' => 1,
    ]);

    // Star the card
    $response = $this->actingAs($user)->postJson(route('flashcards.toggleStar'), [
        'flashcard_id' => $flashcard->id,
    ]);

    $response->assertSuccessful();
    $response->assertJson([
        'success' => true,
        'is_starred' => true,
        'starred_count' => 1,
    ]);

    $this->assertDatabaseHas('flashcard_progresses', [
        'user_id' => $user->id,
        'flashcard_id' => $flashcard->id,
        'is_starred' => true,
    ]);

    // Unstar the card
    $response2 = $this->actingAs($user)->postJson(route('flashcards.toggleStar'), [
        'flashcard_id' => $flashcard->id,
    ]);

    $response2->assertSuccessful();
    $response2->assertJson([
        'success' => true,
        'is_starred' => false,
        'starred_count' => 0,
    ]);

    $this->assertDatabaseHas('flashcard_progresses', [
        'user_id' => $user->id,
        'flashcard_id' => $flashcard->id,
        'is_starred' => false,
    ]);
});

test('user can filter flashcards by starred', function () {
    $user = User::factory()->create();
    $lesson = Lesson::factory()->create(['is_published' => true]);

    $card1 = Flashcard::create([
        'lesson_id' => $lesson->id,
        'hanzi' => '苹果',
        'pinyin' => 'píngguǒ',
        'meaning' => 'Quả táo',
        'hsk_level' => 1,
        'is_active' => true,
        'sort_order' => 1,
    ]);

    $card2 = Flashcard::create([
        'lesson_id' => $lesson->id,
        'hanzi' => '香蕉',
        'pinyin' => 'xiāngjiāo',
        'meaning' => 'Quả chuối',
        'hsk_level' => 1,
        'is_active' => true,
        'sort_order' => 2,
    ]);

    // Star card1 only
    FlashcardProgress::create([
        'user_id' => $user->id,
        'flashcard_id' => $card1->id,
        'is_starred' => true,
    ]);

    $response = $this->actingAs($user)->get(route('flashcards', ['starred' => 1]));

    $response->assertSuccessful();
    $response->assertSee('苹果');
    $response->assertDontSee('香蕉');

    // Also test the JSON API cards endpoint
    $apiResponse = $this->actingAs($user)->getJson(route('flashcards.cards', ['starred' => 1]));
    $apiResponse->assertSuccessful();
    $apiResponse->assertJsonCount(1, 'cards');
    $this->assertEquals('苹果', $apiResponse->json('cards.0.hanzi'));
    $this->assertTrue($apiResponse->json('cards.0.is_starred'));
});

test('dashboard displays starred words count', function () {
    $user = User::factory()->create();
    
    for ($i = 1; $i <= 3; $i++) {
        $card = Flashcard::create([
            'hanzi' => '字' . $i,
            'pinyin' => 'zì',
            'meaning' => 'Nghĩa ' . $i,
            'hsk_level' => 1,
            'is_active' => true,
            'sort_order' => $i,
        ]);

        FlashcardProgress::create([
            'user_id' => $user->id,
            'flashcard_id' => $card->id,
            'is_starred' => true,
        ]);
    }

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertSuccessful();
    $response->assertSee('Sổ từ vựng đã lưu');
    $response->assertSee('3 từ');
});

test('flashcards page renders clean filter toolbar with HSK tabs and lesson select dropdown', function () {
    $lesson = Lesson::factory()->create([
        'title' => 'Bài 1: Xin chào',
        'slug' => 'bai-1-xin-chao',
        'hsk_level' => 1,
        'is_published' => true,
    ]);

    Flashcard::create([
        'lesson_id' => $lesson->id,
        'hanzi' => '你好',
        'pinyin' => 'nǐ hǎo',
        'meaning' => 'Xin chào',
        'hsk_level' => 1,
        'is_active' => true,
        'sort_order' => 1,
    ]);

    $response = $this->get(route('flashcards'));

    $response->assertSuccessful();
    $response->assertSee('Tất cả');
    $response->assertSee('Sổ từ đã lưu');
    $response->assertSee('HSK 1');
    $response->assertSee('Chọn bài học cụ thể...');
    $response->assertSee('Bài 1: Xin chào (1 từ)');
    $response->assertSee('placeholder="Tìm chữ Hán, Pinyin, Nghĩa..."', false);
});

test('filtering flashcards by hsk level shows matching cards', function () {
    $card1 = Flashcard::create([
        'hanzi' => '你好',
        'pinyin' => 'nǐ hǎo',
        'meaning' => 'Xin chào',
        'hsk_level' => 1,
        'is_active' => true,
        'sort_order' => 1,
    ]);

    $card2 = Flashcard::create([
        'hanzi' => '机场',
        'pinyin' => 'jīchǎng',
        'meaning' => 'Sân bay',
        'hsk_level' => 2,
        'is_active' => true,
        'sort_order' => 1,
    ]);

    $response = $this->get(route('flashcards', ['hsk' => 1]));

    $response->assertSuccessful();
    $response->assertSee('你好');
    $response->assertDontSee('机场');
});

test('filtering flashcards by lesson displays active lesson filter tag', function () {
    $lesson1 = Lesson::factory()->create([
        'title' => 'Bài 1: Xin chào',
        'slug' => 'bai-1-xin-chao',
        'hsk_level' => 1,
        'is_published' => true,
    ]);

    $lesson2 = Lesson::factory()->create([
        'title' => 'Bài 2: Cảm ơn',
        'slug' => 'bai-2-cam-on',
        'hsk_level' => 1,
        'is_published' => true,
    ]);

    Flashcard::create([
        'lesson_id' => $lesson1->id,
        'hanzi' => '你好',
        'pinyin' => 'nǐ hǎo',
        'meaning' => 'Xin chào',
        'hsk_level' => 1,
        'is_active' => true,
        'sort_order' => 1,
    ]);

    Flashcard::create([
        'lesson_id' => $lesson2->id,
        'hanzi' => '谢谢',
        'pinyin' => 'xièxie',
        'meaning' => 'Cảm ơn',
        'hsk_level' => 1,
        'is_active' => true,
        'sort_order' => 1,
    ]);

    $response = $this->get(route('flashcards', ['lesson' => 'bai-1-xin-chao']));

    $response->assertSuccessful();
    $response->assertSee('Bài học: Bài 1: Xin chào');
    $response->assertSee('你好');
    $response->assertDontSee('谢谢');
});

test('searching flashcards by keyword filters results and renders active tag', function () {
    Flashcard::create([
        'hanzi' => '苹果',
        'pinyin' => 'píngguǒ',
        'meaning' => 'Quả táo',
        'hsk_level' => 1,
        'is_active' => true,
        'sort_order' => 1,
    ]);

    Flashcard::create([
        'hanzi' => '香蕉',
        'pinyin' => 'xiāngjiāo',
        'meaning' => 'Quả chuối',
        'hsk_level' => 1,
        'is_active' => true,
        'sort_order' => 2,
    ]);

    $response = $this->get(route('flashcards', ['q' => 'táo']));

    $response->assertSuccessful();
    $response->assertSee('苹果');
    $response->assertDontSee('香蕉');
    $response->assertSeeText('Từ khóa: "táo"');
});
