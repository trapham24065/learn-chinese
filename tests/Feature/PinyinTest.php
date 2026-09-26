<?php

use App\Services\PinyinService;

test('pinyin index page can be rendered successfully', function () {
    $response = $this->get('/pinyin');

    $response->assertSuccessful();
    $response->assertSee('Bảng Phát Âm');
    $response->assertSee('Luyện Âm Pinyin');
    $response->assertSee('Ma Trận Pinyin');
    $response->assertSee('23 Thanh Mẫu');
    $response->assertSee('36 Vận Mẫu');
    $response->assertSee('Thanh Điệu');
    $response->assertSee('Biến Điệu');
    $response->assertSee('Âm hai môi');
});

test('pinyin syllable API returns accurate tones and details for valid syllable', function () {
    $response = $this->getJson('/pinyin/syllable/ma');

    $response->assertSuccessful()
        ->assertJson([
            'success' => true,
            'syllable' => [
                'syllable' => 'ma',
                'initial' => 'm',
                'final' => 'a',
                'tones' => [
                    1 => [
                        'pinyin' => 'mā',
                        'char' => '妈',
                        'meaning' => 'mẹ',
                    ],
                    3 => [
                        'pinyin' => 'mǎ',
                        'char' => '马',
                        'meaning' => 'con ngựa',
                    ],
                ],
            ],
        ]);
});

test('pinyin syllable API returns 404 for non-existent Mandarin syllables', function () {
    // 'be' and 'fi' do not exist as valid syllables in standard Mandarin
    $response1 = $this->getJson('/pinyin/syllable/be');
    $response1->assertStatus(404);

    $response2 = $this->getJson('/pinyin/syllable/fi');
    $response2->assertStatus(404);
});

test('pinyin search API returns matching syllables by pinyin or meaning', function () {
    // Search by pinyin syllable
    $response1 = $this->getJson('/pinyin/search?q=hao');
    $response1->assertSuccessful()
        ->assertJsonPath('query', 'hao');
    
    $results1 = collect($response1->json('results'))->pluck('syllable')->all();
    expect($results1)->toContain('hao');

    // Search by Vietnamese meaning
    $response2 = $this->getJson('/pinyin/search?q=mẹ');
    $response2->assertSuccessful();
    $results2 = collect($response2->json('results'))->pluck('syllable')->all();
    expect($results2)->toContain('ma');
});

test('pinyin service data structure and completeness', function () {
    $service = app(PinyinService::class);

    // 1. Initials
    $initials = $service->getInitials();
    expect($initials)->toHaveKey('groups');
    expect(count($initials['groups']))->toBe(8);

    // 2. Finals
    $finals = $service->getFinals();
    expect($finals)->toHaveKey('groups');
    expect(count($finals['groups']))->toBe(5);

    // 3. Matrix
    $matrix = $service->getMatrix();
    expect($matrix)->toHaveKeys(['initials', 'finals', 'cells', 'total_syllables']);
    expect($matrix['total_syllables'])->toBeGreaterThanOrEqual(390);

    // 4. Tone Rules
    $toneRules = $service->getToneRules();
    expect($toneRules)->toHaveKeys(['tones', 'sandhi_rules']);
    expect(count($toneRules['tones']))->toBe(5); // 1, 2, 3, 4, 0
    expect(count($toneRules['sandhi_rules']))->toBe(5); // 5 key sandhi rules
});
