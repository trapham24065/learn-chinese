<?php

use App\Models\Radical;
use App\Models\RadicalCharacter;
use App\Services\RadicalService;

test('radicals index page can be rendered', function () {
    $radical = Radical::create([
        'radical_number'    => 9,
        'character'         => '人',
        'display_character' => '亻',
        'slug'              => 'nhan-ren',
        'name_vi'           => 'Nhân đứng',
        'pinyin'            => 'rén',
        'meaning_vi'        => 'Người, con người',
        'stroke_count'      => 2,
        'position'          => 'left',
        'position_desc'     => 'Thường đứng bên trái chữ',
        'is_common'         => true,
        'common_rank'       => 3,
        'sort_order'        => 9,
    ]);

    $response = $this->get('/radicals');

    $response->assertSuccessful();
    $response->assertSee('214 Bộ thủ');
    $response->assertSee('Nhân đứng');
    $response->assertSee('亻');
    $response->assertSee('rén');
});

test('radicals can be filtered by stroke count and common flag', function () {
    Radical::create([
        'radical_number'    => 1,
        'character'         => '一',
        'display_character' => '一',
        'slug'              => 'nhat-yi',
        'name_vi'           => 'Nhất',
        'pinyin'            => 'yī',
        'meaning_vi'        => 'Số một',
        'stroke_count'      => 1,
        'position'          => 'standalone',
        'is_common'         => true,
        'common_rank'       => 1,
        'sort_order'        => 1,
    ]);

    Radical::create([
        'radical_number'    => 214,
        'character'         => '龠',
        'display_character' => '龠',
        'slug'              => 'duoc-yue',
        'name_vi'           => 'Dược',
        'pinyin'            => 'yuè',
        'meaning_vi'        => 'Ống sáo sáu lỗ',
        'stroke_count'      => 17,
        'position'          => 'standalone',
        'is_common'         => false,
        'common_rank'       => null,
        'sort_order'        => 214,
    ]);

    // Filter stroke = 1
    $responseStrokes = $this->get('/radicals?strokes=1');
    $responseStrokes->assertSuccessful();
    $responseStrokes->assertSee('Nhất');
    $responseStrokes->assertDontSee('Ống sáo sáu lỗ');

    // Filter common = 1
    $responseCommon = $this->get('/radicals?common=1');
    $responseCommon->assertSuccessful();
    $responseCommon->assertSee('Nhất');
    $responseCommon->assertDontSee('Ống sáo sáu lỗ');
});

test('radicals can be searched by name or pinyin', function () {
    Radical::create([
        'radical_number'    => 85,
        'character'         => '水',
        'display_character' => '氵',
        'slug'              => 'thuy-shui',
        'name_vi'           => 'Ba chấm thủy',
        'pinyin'            => 'shuǐ',
        'meaning_vi'        => 'Nước, chất lỏng',
        'stroke_count'      => 4,
        'position'          => 'left',
        'is_common'         => true,
        'common_rank'       => 4,
        'sort_order'        => 85,
    ]);

    $response = $this->get('/radicals?q=thủy');
    $response->assertSuccessful();
    $response->assertSee('Ba chấm thủy');
    $response->assertSee('shuǐ');
});

test('radical detail page renders correctly with characters and HanziWriter canvas', function () {
    $radical = Radical::create([
        'radical_number'    => 9,
        'character'         => '人',
        'display_character' => '亻',
        'slug'              => 'nhan-ren',
        'name_vi'           => 'Nhân đứng',
        'pinyin'            => 'rén',
        'meaning_vi'        => 'Người, con người',
        'stroke_count'      => 2,
        'position'          => 'left',
        'position_desc'     => 'Thường đứng bên trái chữ',
        'mnemonic'          => 'Dáng người đứng thẳng tựa cành cây',
        'is_common'         => true,
        'common_rank'       => 3,
        'sort_order'        => 9,
    ]);

    RadicalCharacter::create([
        'radical_id'   => $radical->id,
        'character'    => '你',
        'pinyin'       => 'nǐ',
        'meaning_vi'   => 'Bạn, ngôi thứ hai',
        'hsk_level'    => 1,
        'is_featured'  => true,
        'sort_order'   => 1,
    ]);

    $response = $this->get('/radicals/nhan-ren');

    $response->assertSuccessful();
    $response->assertSee('Nhân đứng');
    $response->assertSee('radical-writer-box');
    $response->assertSee('Dáng người đứng thẳng tựa cành cây');
    $response->assertSee('你');
    $response->assertSee('nǐ');
    $response->assertSee('HSK 1');
});

test('radical service can resolve radicals for compound words', function () {
    $radicalRen = Radical::create([
        'radical_number'    => 9,
        'character'         => '人',
        'display_character' => '亻',
        'slug'              => 'nhan-ren',
        'name_vi'           => 'Nhân đứng',
        'pinyin'            => 'rén',
        'meaning_vi'        => 'Người',
        'stroke_count'      => 2,
        'position'          => 'left',
        'sort_order'        => 9,
    ]);

    $radicalNv = Radical::create([
        'radical_number'    => 38,
        'character'         => '女',
        'display_character' => '女',
        'slug'              => 'nu-nv',
        'name_vi'           => 'Nữ',
        'pinyin'            => 'nǚ',
        'meaning_vi'        => 'Phụ nữ',
        'stroke_count'      => 3,
        'position'          => 'left',
        'sort_order'        => 38,
    ]);

    RadicalCharacter::create([
        'radical_id'   => $radicalRen->id,
        'character'    => '你',
        'pinyin'       => 'nǐ',
        'meaning_vi'   => 'Bạn',
        'hsk_level'    => 1,
    ]);

    RadicalCharacter::create([
        'radical_id'   => $radicalNv->id,
        'character'    => '好',
        'pinyin'       => 'hǎo',
        'meaning_vi'   => 'Tốt',
        'hsk_level'    => 1,
    ]);

    $service = new RadicalService();
    $result = $service->getRadicalsForWord('你好');

    expect($result)->toHaveCount(2);
    expect($result->pluck('character')->toArray())->toContain('人', '女');
});
