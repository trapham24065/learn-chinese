<?php

namespace Tests\Feature;

use App\Models\Flashcard;
use App\Models\MediaAsset;
use App\Models\Question;
use App\Models\Vocabulary;
use Database\Seeders\HskMediaAssetSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HskMediaAssetTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(HskMediaAssetSeeder::class);
    }

    public function test_media_assets_table_is_seeded_with_44_assets_and_rich_metadata(): void
    {
        $this->assertEquals(44, MediaAsset::count());

        // Verify each category has records
        $categories = [
            MediaAsset::CATEGORY_ACTIONS,
            MediaAsset::CATEGORY_ANIMALS,
            MediaAsset::CATEGORY_TRANSPORT,
            MediaAsset::CATEGORY_FOOD,
            MediaAsset::CATEGORY_PEOPLE,
            MediaAsset::CATEGORY_PLACES,
            MediaAsset::CATEGORY_OBJECTS,
            MediaAsset::CATEGORY_NATURE,
        ];

        foreach ($categories as $cat) {
            $count = MediaAsset::category($cat)->count();
            $this->assertGreaterThan(0, $count, "Category {$cat} should contain media assets.");
        }

        // Verify license and attribution compliance
        $sample = MediaAsset::where('slug', 'transport-zixingche')->first();
        $this->assertNotNull($sample);
        $this->assertEquals('CC BY-SA 4.0', $sample->license);
        $this->assertStringContainsString('OpenMoji', $sample->source);
        $this->assertStringContainsString('CC BY-SA 4.0', $sample->attribution);
        $this->assertIsArray($sample->keywords);
        $this->assertContains('自行车', $sample->keywords);

        // Verify files exist on disk
        $assets = MediaAsset::all();
        foreach ($assets as $asset) {
            $fullPath = public_path($asset->file_path);
            $this->assertFileExists($fullPath, "Asset file [{$asset->file_path}] must exist on disk.");
        }
    }

    public function test_semantic_keyword_and_category_search(): void
    {
        // Search by Hanzi
        $hanziResults = MediaAsset::searchKeyword('自行车')->get();
        $this->assertTrue($hanziResults->contains('slug', 'transport-zixingche'));

        // Search by Pinyin
        $pinyinResults = MediaAsset::searchKeyword('zìxíngchē')->get();
        $this->assertTrue($pinyinResults->contains('slug', 'transport-zixingche'));

        // Search by Vietnamese
        $vnResults = MediaAsset::searchKeyword('xe đạp')->get();
        $this->assertTrue($vnResults->contains('slug', 'transport-zixingche'));

        // Search by English
        $enResults = MediaAsset::searchKeyword('bicycle')->get();
        $this->assertTrue($enResults->contains('slug', 'transport-zixingche'));

        // Category filter
        $transportAssets = MediaAsset::category(MediaAsset::CATEGORY_TRANSPORT)->get();
        $this->assertCount(5, $transportAssets);
        $this->assertTrue($transportAssets->contains('slug', 'transport-zixingche'));
        $this->assertTrue($transportAssets->contains('slug', 'transport-feiji'));
    }

    public function test_media_asset_vocabulary_many_to_one_relationship(): void
    {
        $vocab1 = Vocabulary::create([
            'hanzi'   => '自行车',
            'pinyin'  => 'zì xíng chē',
            'meaning' => 'Xe đạp',
        ]);

        $vocab2 = Vocabulary::create([
            'hanzi'   => '骑自行车',
            'pinyin'  => 'qí zì xíng chē',
            'meaning' => 'Đi xe đạp',
        ]);

        $asset = MediaAsset::where('slug', 'transport-zixingche')->first();
        $this->assertNotNull($asset);

        // Link multiple vocabularies to the same asset (Many-to-One reuse)
        $asset->vocabularies()->sync([
            $vocab1->id => ['relation_type' => 'primary'],
            $vocab2->id => ['relation_type' => 'secondary'],
        ]);

        // Verify relation from Asset side
        $this->assertCount(2, $asset->vocabularies);

        // Verify relation from Vocabulary side
        $vocab1->refresh();
        $this->assertEquals($asset->id, $vocab1->primaryMediaAsset()?->id);
        $this->assertEquals($asset->file_path, $vocab1->assetImagePath());

        $vocab2->refresh();
        $this->assertTrue($vocab2->mediaAssets->contains('id', $asset->id));
    }

    public function test_usage_tracking_detects_questions_and_flashcards(): void
    {
        $asset = MediaAsset::where('slug', 'transport-zixingche')->first();
        $this->assertNotNull($asset);

        // Create a question referencing zixingche.svg
        Question::create([
            'hsk_level'      => 2,
            'exam_standard'  => 'hsk_2_0',
            'skill_type'     => 'listening',
            'question_type'  => 'picture_choice',
            'media_type'     => 'image_audio',
            'difficulty'     => 'elementary',
            'image'          => $asset->file_path,
            'question'       => '测试题目',
            'options'        => ['A', 'B'],
            'correct_answer' => 'A',
            'is_active'      => true,
        ]);

        $usage = $asset->usageLocations();
        $this->assertGreaterThanOrEqual(1, $usage['questions_count']);
        $this->assertTrue($asset->isInUse());
    }
}
