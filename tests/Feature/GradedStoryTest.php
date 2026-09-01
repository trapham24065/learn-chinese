<?php

use App\Models\Flashcard;
use App\Models\Story;
use App\Models\StoryProgress;
use App\Models\StudySession;
use App\Models\User;

test('stories index page can be rendered with list of published stories', function () {
    Story::factory()->create([
        'title'        => '去咖啡馆',
        'title_vi'     => 'Đi quán cà phê',
        'slug'         => 'di-quan-ca-phe',
        'hsk_level'    => 1,
        'category'     => 'Đời sống',
        'is_published' => true,
        'content_json' => [
            [
                'chinese'    => '我 想 喝 咖啡 。',
                'pinyin'     => 'Wǒ xiǎng hē kāfēi.',
                'vietnamese' => 'Tôi muốn uống cà phê.',
                'words'      => [
                    ['hanzi' => '我', 'pinyin' => 'wǒ', 'meaning' => 'tôi'],
                    ['hanzi' => '咖啡', 'pinyin' => 'kāfēi', 'meaning' => 'cà phê'],
                ],
            ],
        ],
    ]);

    $response = $this->get('/reading');

    $response->assertSuccessful();
    $response->assertSee('Luyện Đọc Tiếng Trung Thực Chiến');
    $response->assertSee('去咖啡馆');
    $response->assertSee('Đi quán cà phê');
});

test('stories can be filtered by hsk level and category', function () {
    Story::create([
        'title'        => '买苹果',
        'title_pinyin' => 'Mǎi píngguǒ',
        'title_vi'     => 'Mua táo',
        'slug'         => 'mua-tao-hsk-1',
        'hsk_level'    => 1,
        'category'     => 'Mua sắm',
        'is_published' => true,
        'content_json' => [['chinese' => '买 苹果', 'pinyin' => 'mǎi píngguǒ', 'vietnamese' => 'mua táo']],
    ]);

    Story::create([
        'title'        => '租公寓',
        'title_pinyin' => 'Zū gōngyù',
        'title_vi'     => 'Thuê căn hộ',
        'slug'         => 'thue-can-ho-hsk-3',
        'hsk_level'    => 3,
        'category'     => 'Đời sống',
        'is_published' => true,
        'content_json' => [['chinese' => '租 公寓', 'pinyin' => 'zū gōngyù', 'vietnamese' => 'thuê căn hộ']],
    ]);

    // Filter Level 1
    $respLevel1 = $this->get('/reading?level=1');
    $respLevel1->assertSuccessful();
    $respLevel1->assertSee('买苹果');
    $respLevel1->assertDontSee('租公寓');

    // Filter Level 3
    $respLevel3 = $this->get('/reading?level=3');
    $respLevel3->assertSuccessful();
    $respLevel3->assertSee('租公寓');
    $respLevel3->assertDontSee('买苹果');
});

test('interactive story reading room loads with tokenized words, pinyin, and translation', function () {
    $story = Story::create([
        'title'        => '去咖啡馆喝咖啡',
        'title_pinyin' => 'Qù kāfēiguǎn hē kāfēi',
        'title_vi'     => 'Đi quán cà phê uống cà phê',
        'slug'         => 'di-quan-ca-phe-hsk-1-test',
        'hsk_level'    => 1,
        'category'     => 'Đời sống',
        'is_published' => true,
        'content_json' => [
            [
                'chinese'    => '今天 是 星期六 ， 天气 很 好 。',
                'pinyin'     => 'Jīntiān shì xīngqīliù, tiānqì hěn hǎo.',
                'vietnamese' => 'Hôm nay là thứ Bảy, thời tiết rất đẹp.',
                'words'      => [
                    ['hanzi' => '今天', 'pinyin' => 'jīntiān', 'meaning' => 'hôm nay'],
                    ['hanzi' => '星期六', 'pinyin' => 'xīngqīliù', 'meaning' => 'thứ Bảy'],
                ],
            ],
        ],
        'quiz_json'    => [
            [
                'question'       => '今天 是 星期 几 ？',
                'options'        => ['星期五', '星期六', '星期日', '星期一'],
                'correct_answer' => '星期六',
            ],
        ],
    ]);

    $response = $this->get("/reading/{$story->slug}");

    $response->assertSuccessful();
    $response->assertSee('去咖啡馆喝咖啡');
    $response->assertSee('Qù kāfēiguǎn hē kāfēi');
    $response->assertSee('Đi quán cà phê uống cà phê');
    $response->assertSee('Kiểm tra độ hiểu bài');
    $response->assertSee('星期六');
});

test('ajax dictionary lookup finds word metadata and starred status', function () {
    $student = User::factory()->create(['role' => User::ROLE_STUDENT]);
    $flashcard = Flashcard::factory()->create([
        'hanzi'     => '咖啡',
        'pinyin'    => 'kāfēi',
        'meaning'   => 'Cà phê',
        'hsk_level' => 1,
    ]);

    // Guest lookup
    $response = $this->postJson('/reading/lookup', ['character' => '咖啡']);
    $response->assertSuccessful();
    $response->assertJson([
        'found'      => true,
        'character'  => '咖啡',
        'pinyin'     => 'kāfēi',
        'meaning'    => 'Cà phê',
        'is_starred' => false,
    ]);

    // Authenticated student with starred word
    $this->actingAs($student, 'web');
    $student->starredFlashcards()->attach($flashcard->id, ['is_starred' => true]);

    $responseAuth = $this->postJson('/reading/lookup', ['character' => '咖啡']);
    $responseAuth->assertSuccessful();
    $responseAuth->assertJson([
        'found'      => true,
        'character'  => '咖啡',
        'is_starred' => true,
    ]);
});

test('student completing a story records progress and logs study session', function () {
    $student = User::factory()->create(['role' => User::ROLE_STUDENT]);
    $story = Story::create([
        'title'                     => '短篇故事',
        'title_vi'                  => 'Truyện ngắn',
        'slug'                      => 'truyen-ngan-test',
        'hsk_level'                 => 1,
        'estimated_reading_minutes' => 3,
        'is_published'              => true,
        'content_json'              => [['chinese' => '你好', 'vietnamese' => 'Xin chào']],
    ]);

    $this->actingAs($student, 'web');

    $response = $this->postJson("/reading/{$story->id}/complete", [
        'quiz_score' => 100,
    ]);

    $response->assertSuccessful();
    $response->assertJson(['success' => true]);

    // Verify StoryProgress created
    expect(StoryProgress::where('user_id', $student->id)->where('story_id', $story->id)->exists())->toBeTrue();
    $progress = StoryProgress::where('user_id', $student->id)->where('story_id', $story->id)->first();
    expect($progress->is_completed)->toBeTrue();
    expect($progress->quiz_score)->toBe(100);

    // Verify StudySession logged
    expect(StudySession::where('user_id', $student->id)->exists())->toBeTrue();
    $session = StudySession::where('user_id', $student->id)->latest()->first();
    expect($session->duration_minutes)->toBe(3);
});

test('admin can view stories resource in filament', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

    $this->actingAs($admin, 'admin');

    $response = $this->get('/admin/stories');
    $response->assertSuccessful();
});

test('stories library renders pagination when stories exceed per page limit', function () {
    Story::factory()->count(15)->create(['is_published' => true]);

    $response = $this->get('/reading');
    $response->assertSuccessful();
    $response->assertSee('Hiển thị 1–12 trong tổng số 15 bài đọc hiểu');
    $response->assertSee('?page=2');
});

