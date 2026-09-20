<?php

namespace Tests\Feature;

use App\Models\Flashcard;
use App\Models\HskStandard;
use App\Models\Vocabulary;
use App\Models\VocabularyHskLevel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HskDualStandardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed standards
        $this->seed(\Database\Seeders\HskStandardSeeder::class);
    }

    public function test_hsk_standards_are_properly_seeded_and_queryable(): void
    {
        $hsk2 = HskStandard::findByCode(HskStandard::CODE_HSK_2_0);
        $hsk3_2021 = HskStandard::findByCode(HskStandard::CODE_HSK_3_0_2021);
        $hsk3_2026 = HskStandard::findByCode(HskStandard::CODE_HSK_3_0_2026);

        $this->assertNotNull($hsk2);
        $this->assertEquals('2010', $hsk2->version);
        $this->assertNotNull($hsk3_2021);
        $this->assertEquals('2021', $hsk3_2021->version);
        $this->assertNotNull($hsk3_2026);
        $this->assertEquals('2026', $hsk3_2026->version);

        $this->assertCount(3, HskStandard::active()->get());
    }

    public function test_vocabulary_can_have_different_levels_across_standards(): void
    {
        $hsk2 = HskStandard::findByCode(HskStandard::CODE_HSK_2_0);
        $hsk3 = HskStandard::findByCode(HskStandard::CODE_HSK_3_0_2026);

        // Word '爱好' (Hobby): HSK 2.0 Level 3, HSK 3.0 Level 1
        $vocab = Vocabulary::create([
            'hanzi'   => '爱好',
            'pinyin'  => 'ài hào',
            'meaning' => 'Sở thích',
        ]);

        VocabularyHskLevel::create([
            'vocabulary_id'   => $vocab->id,
            'hsk_standard_id' => $hsk2->id,
            'level'           => 3,
            'source'          => 'Hanban 2010',
        ]);

        VocabularyHskLevel::create([
            'vocabulary_id'   => $vocab->id,
            'hsk_standard_id' => $hsk3->id,
            'level'           => 1,
            'source'          => 'CTI 2026',
        ]);

        $this->assertEquals(3, $vocab->hskLevelFor(HskStandard::CODE_HSK_2_0));
        $this->assertEquals(1, $vocab->hskLevelFor(HskStandard::CODE_HSK_3_0_2026));
        $this->assertEquals(3, $vocab->hsk2Level());
        $this->assertEquals(1, $vocab->hsk3Level());

        $allLevels = $vocab->allHskLevels();
        $this->assertEquals([
            HskStandard::CODE_HSK_2_0 => 3,
            HskStandard::CODE_HSK_3_0_2026 => 1,
        ], $allLevels);
    }

    public function test_flashcard_links_to_vocabulary_and_exposes_dual_standard_levels(): void
    {
        $hsk2 = HskStandard::findByCode(HskStandard::CODE_HSK_2_0);
        $hsk3 = HskStandard::findByCode(HskStandard::CODE_HSK_3_0_2026);

        $vocab = Vocabulary::create([
            'hanzi'   => '高兴',
            'pinyin'  => 'gāo xìng',
            'meaning' => 'Vui vẻ',
        ]);

        VocabularyHskLevel::create([
            'vocabulary_id'   => $vocab->id,
            'hsk_standard_id' => $hsk2->id,
            'level'           => 3,
        ]);

        VocabularyHskLevel::create([
            'vocabulary_id'   => $vocab->id,
            'hsk_standard_id' => $hsk3->id,
            'level'           => 2,
        ]);

        $flashcard = Flashcard::create([
            'vocabulary_id' => $vocab->id,
            'hanzi'         => '高兴',
            'pinyin'        => 'gāo xìng',
            'meaning'       => 'Vui vẻ',
            'hsk_level'     => 3,
        ]);

        $this->assertEquals($vocab->id, $flashcard->vocabulary->id);
        $this->assertEquals(3, $flashcard->hsk2Level());
        $this->assertEquals(2, $flashcard->hsk3Level());
    }

    public function test_flashcard_without_vocabulary_gracefully_falls_back_to_legacy_hsk_level(): void
    {
        $flashcard = Flashcard::create([
            'hanzi'     => '你好',
            'pinyin'    => 'nǐ hǎo',
            'meaning'   => 'Xin chào',
            'hsk_level' => 1,
        ]);

        $this->assertNull($flashcard->vocabulary_id);
        $this->assertEquals(1, $flashcard->hsk2Level());
        $this->assertNull($flashcard->hsk3Level());
        $this->assertEquals([HskStandard::CODE_HSK_2_0 => 1], $flashcard->allHskLevels());
    }

    public function test_vocabulary_scope_filters_by_standard_and_level(): void
    {
        $hsk2 = HskStandard::findByCode(HskStandard::CODE_HSK_2_0);
        $hsk3 = HskStandard::findByCode(HskStandard::CODE_HSK_3_0_2026);

        $v1 = Vocabulary::create(['hanzi' => '字1', 'pinyin' => 'zì', 'meaning' => 'Nghĩa 1']);
        $v2 = Vocabulary::create(['hanzi' => '字2', 'pinyin' => 'zì', 'meaning' => 'Nghĩa 2']);

        VocabularyHskLevel::create(['vocabulary_id' => $v1->id, 'hsk_standard_id' => $hsk2->id, 'level' => 1]);
        VocabularyHskLevel::create(['vocabulary_id' => $v1->id, 'hsk_standard_id' => $hsk3->id, 'level' => 2]);

        VocabularyHskLevel::create(['vocabulary_id' => $v2->id, 'hsk_standard_id' => $hsk2->id, 'level' => 2]);
        VocabularyHskLevel::create(['vocabulary_id' => $v2->id, 'hsk_standard_id' => $hsk3->id, 'level' => 2]);

        $hsk2Lvl1 = Vocabulary::standardLevel(HskStandard::CODE_HSK_2_0, 1)->get();
        $this->assertCount(1, $hsk2Lvl1);
        $this->assertEquals('字1', $hsk2Lvl1->first()->hanzi);

        $hsk3Lvl2 = Vocabulary::standardLevel(HskStandard::CODE_HSK_3_0_2026, 2)->get();
        $this->assertCount(2, $hsk3Lvl2);
    }

    public function test_validate_hsk_data_artisan_command_runs_successfully(): void
    {
        $this->artisan('hsk:validate')
            ->expectsOutputToContain('KIỂM TRA DỮ LIỆU ĐA CHUẨN HSK 2.0 & HSK 3.0')
            ->assertExitCode(0);
    }
}
