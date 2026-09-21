<?php

namespace Tests\Feature;

use App\Filament\Resources\MediaAssets\Pages\ListMediaAssets;
use App\Models\Flashcard;
use App\Models\MediaAsset;
use App\Models\Question;
use App\Models\User;
use App\Models\Vocabulary;
use Database\Seeders\HskMediaAssetSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class FilamentMediaAssetTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_media_assets_admin_page(): void
    {
        $response = $this->get('/admin/media-assets');

        $response->assertRedirect('/admin/login');
    }

    public function test_student_cannot_access_media_assets_in_filament(): void
    {
        $student = User::factory()->create([
            'role' => User::ROLE_STUDENT,
        ]);

        $response = $this->actingAs($student, 'web')->get('/admin/media-assets');

        $response->assertRedirect();
    }

    public function test_admin_can_access_media_assets_resource_in_filament(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);

        $response = $this->actingAs($admin, 'admin')->get('/admin/media-assets');

        $response->assertSuccessful();
        $response->assertSee('Kho Tư liệu Đồ họa');
    }

    public function test_admin_can_view_seeded_media_assets(): void
    {
        $this->seed(HskMediaAssetSeeder::class);

        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);

        $response = $this->actingAs($admin, 'admin')->get('/admin/media-assets');
        $response->assertSuccessful();

        $first = MediaAsset::orderBy('id')->first();
        $this->assertNotNull($first);

        Livewire::actingAs($admin, 'admin')
            ->test(ListMediaAssets::class)
            ->assertCanSeeTableRecords([$first]);
    }

    public function test_admin_can_filter_media_assets_by_category(): void
    {
        $this->seed(HskMediaAssetSeeder::class);

        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);

        $transportSample = MediaAsset::where('slug', 'transport-zixingche')->first();
        $actionSample = MediaAsset::where('slug', 'actions-pao-bu')->first();

        Livewire::actingAs($admin, 'admin')
            ->test(ListMediaAssets::class)
            ->filterTable('category', MediaAsset::CATEGORY_TRANSPORT)
            ->assertCanSeeTableRecords([$transportSample])
            ->assertCanNotSeeTableRecords([$actionSample]);
    }

    public function test_safe_deletion_guard_identifies_in_use_assets(): void
    {
        $asset = MediaAsset::create([
            'slug'        => 'test-in-use-asset',
            'name'        => 'Tư liệu kiểm thử đang dùng',
            'file_path'   => 'images/hsk/assets/test/in_use.svg',
            'category'    => MediaAsset::CATEGORY_OBJECTS,
            'alt_text'    => 'Ảnh test',
            'keywords'    => ['test'],
            'source'      => 'Test',
            'license'     => 'CC BY-SA 4.0',
            'attribution' => 'Test',
            'is_active'   => true,
        ]);

        $this->assertFalse($asset->isInUse());
        $this->assertEquals(0, $asset->usageLocations()['total_references']);
        $this->assertEmpty($asset->usageSummaryStrings());

        // Now link it to a question using the 'image' field
        $question = Question::create([
            'question'       => 'Tranh này là gì?',
            'options'        => ['A', 'B'],
            'correct_answer' => 'A',
            'hsk_level'      => 1,
            'image'          => 'images/hsk/assets/test/in_use.svg',
            'is_active'      => true,
        ]);

        // Refresh model
        $asset->refresh();

        $this->assertTrue($asset->isInUse());
        $this->assertEquals(1, $asset->usageLocations()['questions_count']);
        $summaries = $asset->usageSummaryStrings();
        $this->assertNotEmpty($summaries);
        $this->assertStringContainsString('câu hỏi', $summaries[0]);
    }

    public function test_safe_deletion_guard_allows_deleting_unused_asset(): void
    {
        $asset = MediaAsset::create([
            'slug'        => 'test-unused-asset',
            'name'        => 'Tư liệu chưa dùng',
            'file_path'   => 'images/hsk/assets/test/unused.svg',
            'category'    => MediaAsset::CATEGORY_OBJECTS,
            'alt_text'    => 'Ảnh chưa dùng',
            'keywords'    => ['unused'],
            'source'      => 'Test',
            'license'     => 'CC BY-SA 4.0',
            'attribution' => 'Test',
            'is_active'   => true,
        ]);

        $this->assertFalse($asset->isInUse());

        $assetId = $asset->id;
        $asset->delete();

        $this->assertDatabaseMissing('media_assets', [
            'id' => $assetId,
        ]);
    }
}
