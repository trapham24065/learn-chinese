<?php

use App\Models\Flashcard;
use App\Models\Story;
use App\Models\User;

test('dictionary page can be rendered', function () {
    Flashcard::create([
        'hanzi'           => '你好',
        'pinyin'          => 'nǐhǎo',
        'meaning'         => 'xin chào',
        'example'         => '你好！很高兴认识你。',
        'example_pinyin'  => 'Nǐhǎo! Hěn gāoxìng rènshi nǐ.',
        'example_meaning' => 'Xin chào! Rất vui được quen bạn.',
        'hsk_level'       => 1,
        'is_active'       => true,
    ]);

    $response = $this->get('/dictionary');

    $response->assertSuccessful();
    $response->assertSee('Tra từ thông minh');
    $response->assertSee('YouGlish');
    $response->assertSee('你好');
});

test('search by hanzi returns correct results and exact match', function () {
    Flashcard::create([
        'hanzi'           => '高兴',
        'pinyin'          => 'gāoxìng',
        'meaning'         => 'vui vẻ, phấn khởi',
        'example'         => '今天我很 高兴 。',
        'example_pinyin'  => 'Jīntiān wǒ hěn gāoxìng.',
        'example_meaning' => 'Hôm nay tôi rất vui vẻ.',
        'hsk_level'       => 1,
        'is_active'       => true,
    ]);

    $response = $this->getJson('/dictionary/search?q=高兴');

    $response->assertSuccessful();
    $response->assertJson([
        'success'       => true,
        'detected_type' => 'hanzi',
    ]);
    $response->assertJsonPath('exact.hanzi', '高兴');
    $response->assertJsonPath('exact.pinyin', 'gāoxìng');
});

test('search by pinyin returns matching words', function () {
    Flashcard::create([
        'hanzi'           => '漂亮',
        'pinyin'          => 'piàoliang',
        'meaning'         => 'xinh đẹp, đẹp đẽ',
        'example'         => '她很 漂亮 。',
        'example_pinyin'  => 'Tā hěn piàoliang.',
        'example_meaning' => 'Cô ấy rất xinh đẹp.',
        'hsk_level'       => 1,
        'is_active'       => true,
    ]);

    $response = $this->getJson('/dictionary/search?q=piaoliang');

    $response->assertSuccessful();
    $response->assertJson([
        'success'       => true,
        'detected_type' => 'pinyin',
    ]);
    $response->assertJsonPath('exact.hanzi', '漂亮');
});

test('search by vietnamese meaning returns correct words', function () {
    Flashcard::create([
        'hanzi'           => '喝',
        'pinyin'          => 'hē',
        'meaning'         => 'uống',
        'example'         => '我 想 喝 水 。',
        'example_pinyin'  => 'Wǒ xiǎng hē shuǐ.',
        'example_meaning' => 'Tôi muốn uống nước.',
        'hsk_level'       => 1,
        'is_active'       => true,
    ]);

    $response = $this->getJson('/dictionary/search?q=uống');

    $response->assertSuccessful();
    $response->assertJson([
        'success'       => true,
        'detected_type' => 'vietnamese',
    ]);
    $response->assertJsonPath('exact.hanzi', '喝');
});

test('dictionary extracts personal learning context from stories', function () {
    Flashcard::create([
        'hanzi'           => '咖啡',
        'pinyin'          => 'kāfēi',
        'meaning'         => 'cà phê',
        'example'         => '我 喜欢 喝 咖啡 。',
        'example_pinyin'  => 'Wǒ xǐhuan hē kāfēi.',
        'example_meaning' => 'Tôi thích uống cà phê.',
        'hsk_level'       => 1,
        'is_active'       => true,
    ]);

    Story::create([
        'title'        => '去咖啡馆喝咖啡',
        'title_pinyin' => 'Qù kāfēiguǎn hē kāfēi',
        'title_vi'     => 'Đi quán cà phê uống cà phê',
        'slug'         => 'di-quan-ca-phe-uong-ca-phe-hsk-1',
        'hsk_level'    => 1,
        'category'     => 'Đời sống',
        'is_published' => true,
        'content_json' => [
            [
                'chinese'    => '今天 下午 我 去 咖啡馆 喝 咖啡 。',
                'pinyin'     => 'Jīntiān xiàwǔ wǒ qù kāfēiguǎn hē kāfēi.',
                'vietnamese' => 'Chiều nay tôi đi quán cà phê uống cà phê.',
            ],
        ],
    ]);

    $response = $this->getJson('/dictionary/search?q=咖啡');

    $response->assertSuccessful();
    $response->assertJsonPath('exact.hanzi', '咖啡');
    $response->assertJsonPath('exact.story_count', 1);
    $response->assertJsonPath('exact.story_matches.0.story_title_vi', 'Đi quán cà phê uống cà phê');
    $response->assertJsonPath('exact.story_matches.0.chinese', '今天 下午 我 去 咖啡馆 喝 咖啡 。');
});

test('search for unknown word returns smart fallback with related words', function () {
    Flashcard::create([
        'hanzi'           => '学习',
        'pinyin'          => 'xuéxí',
        'meaning'         => 'học tập',
        'example'         => '我 学习 汉语 。',
        'example_pinyin'  => 'Wǒ xuéxí hànyǔ.',
        'example_meaning' => 'Tôi học tiếng Hán.',
        'hsk_level'       => 1,
        'is_active'       => true,
    ]);

    // Query for unknown Chinese word (e.g. 计算机)
    $response = $this->getJson('/dictionary/search?q=计算机');

    $response->assertSuccessful();
    $response->assertJson([
        'success'       => true,
        'detected_type' => 'hanzi',
    ]);
    $response->assertJsonPath('exact.is_fallback', true);
    $response->assertJsonPath('exact.hanzi', '计算机');
    $response->assertJsonPath('exact.query', '计算机');
    $this->assertNotEmpty($response->json('exact.related_words'));

    // Query for unknown Vietnamese word
    $responseVi = $this->getJson('/dictionary/search?q=tên-lửa-vũ-trụ-xyz');
    $responseVi->assertSuccessful();
    $responseVi->assertJsonPath('exact.is_fallback', true);
    $responseVi->assertJsonPath('exact.detected_type', 'vietnamese');
    $this->assertNull($responseVi->json('exact.hanzi'));
    $this->assertNotEmpty($responseVi->json('exact.related_words'));
});

