<?php

namespace Tests\Feature;

use App\Models\Pet;
use App\Models\PetFeedingLog;
use App\Models\PetMemory;
use App\Models\PetStage;
use App\Models\User;
use App\Models\UserLearningStat;
use App\Models\UserPet;
use App\Services\Pet\PetFeedingService;
use App\Services\Pet\PetGrowthService;
use App\Services\Pet\PetHungerService;
use App\Services\Pet\PetService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PetSystemTest extends TestCase
{
    use RefreshDatabase;

    protected Pet $pet;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Create the dragon species
        $this->pet = Pet::create([
            'slug'      => 'dragon',
            'name'      => 'Rồng Con',
            'is_active' => true,
        ]);

        // Create all 6 stages
        $stages = [
            ['stage' => 0, 'name' => 'Trứng',         'emoji' => '🥚', 'required_exp' => 0,    'required_mastered_vocabulary' => 0,  'required_used_vocabulary' => 0,  'required_reading_activities' => 0,  'required_listening_activities' => 0],
            ['stage' => 1, 'name' => 'Sơ sinh',        'emoji' => '🐣', 'required_exp' => 100,  'required_mastered_vocabulary' => 0,  'required_used_vocabulary' => 0,  'required_reading_activities' => 0,  'required_listening_activities' => 0],
            ['stage' => 2, 'name' => 'Bé',             'emoji' => '🐥', 'required_exp' => 250,  'required_mastered_vocabulary' => 0,  'required_used_vocabulary' => 0,  'required_reading_activities' => 0,  'required_listening_activities' => 0],
            ['stage' => 3, 'name' => 'Nhỏ',            'emoji' => '🦊', 'required_exp' => 500,  'required_mastered_vocabulary' => 10, 'required_used_vocabulary' => 0,  'required_reading_activities' => 0,  'required_listening_activities' => 0],
            ['stage' => 4, 'name' => 'Trưởng thành',   'emoji' => '🐺', 'required_exp' => 1000, 'required_mastered_vocabulary' => 30, 'required_used_vocabulary' => 10, 'required_reading_activities' => 0,  'required_listening_activities' => 0],
            ['stage' => 5, 'name' => 'Rồng thần',      'emoji' => '🐉', 'required_exp' => 2000, 'required_mastered_vocabulary' => 60, 'required_used_vocabulary' => 0,  'required_reading_activities' => 10, 'required_listening_activities' => 10],
        ];

        foreach ($stages as $stage) {
            PetStage::create(array_merge(['pet_id' => $this->pet->id, 'sort_order' => $stage['stage'], 'dialogue_level' => 0], $stage));
        }

        // Create a test user
        $this->user = User::factory()->create(['role' => 'student']);
    }

    // =========================================================================
    // Test 1: PetService::createInitialPet creates pet correctly
    // =========================================================================

    public function test_user_can_create_initial_pet(): void
    {
        $service = app(PetService::class);
        $userPet = $service->createInitialPet($this->user);

        $this->assertInstanceOf(UserPet::class, $userPet);
        $this->assertEquals($this->user->id, $userPet->user_id);
        $this->assertEquals(0, $userPet->stage);
        $this->assertEquals(100, $userPet->hunger);
        $this->assertEquals('active', $userPet->status);
        $this->assertEquals(0, $userPet->exp);

        // Should create 'hatched' memory
        $memory = PetMemory::where('user_pet_id', $userPet->id)
            ->where('type', 'hatched')
            ->first();
        $this->assertNotNull($memory);
        $this->assertEquals('Trứng nở!', $memory->title);
    }

    // =========================================================================
    // Test 2: createInitialPet is idempotent
    // =========================================================================

    public function test_create_initial_pet_is_idempotent(): void
    {
        $service = app(PetService::class);

        $first  = $service->createInitialPet($this->user);
        $second = $service->createInitialPet($this->user);

        $this->assertEquals($first->id, $second->id);
        $this->assertEquals(1, UserPet::where('user_id', $this->user->id)->count());
        // Only one 'hatched' memory
        $this->assertEquals(1, PetMemory::where('user_pet_id', $first->id)->count());
    }

    // =========================================================================
    // Test 3: Feeding increases hunger and creates log
    // =========================================================================

    public function test_feed_pet_increases_hunger(): void
    {
        $petService  = app(PetService::class);
        $feedService = app(PetFeedingService::class);

        $userPet = $petService->createInitialPet($this->user);
        // Set hunger below 100 so we can verify increase
        $userPet->update(['hunger' => 50]);

        $result = $feedService->feed($this->user, $userPet, 10, 'test-key-001');

        $this->assertTrue($result['success']);
        $this->assertFalse($result['is_duplicate']);
        $this->assertEquals(10, $result['xp_fed']);

        // Verify hunger increased
        $userPet->refresh();
        $this->assertEquals(60, $userPet->hunger);

        // Verify feeding log was created
        $this->assertEquals(1, PetFeedingLog::where('user_pet_id', $userPet->id)->count());
    }

    // =========================================================================
    // Test 4: Daily cap is respected — only partial amount goes through
    // =========================================================================

    public function test_feed_respects_daily_cap(): void
    {
        $petService  = app(PetService::class);
        $feedService = app(PetFeedingService::class);

        $userPet = $petService->createInitialPet($this->user);

        // Manually insert a feeding log to simulate 90 XP already fed today
        PetFeedingLog::create([
            'user_pet_id'      => $userPet->id,
            'user_id'          => $this->user->id,
            'xp_amount'        => 90,
            'daily_fed_before' => 0,
            'idempotency_key'  => 'pre-existing-key',
            'fed_at'           => now(),
        ]);

        // Try to feed 50, but only 10 should go through
        $result = $feedService->feed($this->user, $userPet, 50, 'test-key-cap');

        $this->assertEquals(10, $result['xp_fed']);
        $this->assertEquals(50, $result['xp_requested']);
        $this->assertTrue($result['was_capped']);
        $this->assertEquals(100, $result['daily_fed']);
        $this->assertEquals(0, $result['daily_remaining']);
    }

    // =========================================================================
    // Test 5: Invalid feed amount throws InvalidArgumentException
    // =========================================================================

    public function test_feed_invalid_amount_throws(): void
    {
        $petService  = app(PetService::class);
        $feedService = app(PetFeedingService::class);

        $userPet = $petService->createInitialPet($this->user);

        $this->expectException(\InvalidArgumentException::class);
        $feedService->feed($this->user, $userPet, 7, 'test-key-invalid');
    }

    // =========================================================================
    // Test 6: Same idempotency key returns is_duplicate=true, no second log
    // =========================================================================

    public function test_feed_is_idempotent(): void
    {
        $petService  = app(PetService::class);
        $feedService = app(PetFeedingService::class);

        $userPet = $petService->createInitialPet($this->user);
        $key = 'test-idem-key-001';

        $first  = $feedService->feed($this->user, $userPet, 10, $key);
        $second = $feedService->feed($this->user, $userPet, 10, $key);

        $this->assertFalse($first['is_duplicate']);
        $this->assertTrue($second['is_duplicate']);

        // Only one log entry for this key
        $this->assertEquals(1, PetFeedingLog::where('idempotency_key', $key)->count());
    }

    // =========================================================================
    // Test 7: Hunger decays over days
    // =========================================================================

    public function test_hunger_decays_over_days(): void
    {
        $petService    = app(PetService::class);
        $hungerService = app(PetHungerService::class);

        $userPet = $petService->createInitialPet($this->user);
        // Backdate last calculated to 3 days ago → 3 × 20 = 60 decay
        $userPet->update([
            'hunger'                    => 100,
            'last_hunger_calculated_at' => now()->subDays(3),
        ]);

        $hungerService->calculateAndApplyHunger($userPet);
        $userPet->refresh();

        $this->assertEquals(40, $userPet->hunger);
    }

    // =========================================================================
    // Test 8: Pet goes dormant when hunger hits 0
    // =========================================================================

    public function test_dormant_after_full_decay(): void
    {
        $petService    = app(PetService::class);
        $hungerService = app(PetHungerService::class);

        $userPet = $petService->createInitialPet($this->user);
        // hunger=20, 2 days later → 20 - 40 = 0 → dormant
        $userPet->update([
            'hunger'                    => 20,
            'last_hunger_calculated_at' => now()->subDays(2),
        ]);

        $hungerService->calculateAndApplyHunger($userPet);
        $userPet->refresh();

        $this->assertEquals(0, $userPet->hunger);
        $this->assertEquals('dormant', $userPet->status);
        $this->assertNotNull($userPet->dormant_at);
    }

    // =========================================================================
    // Test 9: Pet resets to egg after grace period
    // =========================================================================

    public function test_egg_reset_after_grace_period(): void
    {
        $petService    = app(PetService::class);
        $hungerService = app(PetHungerService::class);

        $userPet = $petService->createInitialPet($this->user);
        // Already dormant for 4 days — beyond 3-day grace
        $userPet->update([
            'status'                    => 'dormant',
            'hunger'                    => 0,
            'stage'                     => 2,
            'exp'                       => 250,
            'dormant_at'                => now()->subDays(4),
            'last_hunger_calculated_at' => now()->subDays(1),
        ]);

        $hungerService->calculateAndApplyHunger($userPet);
        $userPet->refresh();

        $this->assertEquals('egg', $userPet->status);
        $this->assertEquals(0, $userPet->stage);
        $this->assertEquals(0, $userPet->exp);
        $this->assertGreaterThan(0, $userPet->reset_count);
    }

    // =========================================================================
    // Test 10: Stage up when EXP meets requirement (stage 0 → 1 at 100 EXP)
    // =========================================================================

    public function test_stage_up_when_exp_meets_requirement(): void
    {
        $petService  = app(PetService::class);
        $feedService = app(PetFeedingService::class);

        $userPet = $petService->createInitialPet($this->user);
        // Give 80 EXP already, then feed 20 → total 100 → stage 1
        $userPet->update(['exp' => 80]);

        $result = $feedService->feed($this->user, $userPet, 20, 'stage-up-key');

        $this->assertTrue($result['stage_up']);
        $this->assertEquals(1, $result['new_stage']);

        $userPet->refresh();
        $this->assertEquals(1, $userPet->stage);
        $this->assertEquals(1, $userPet->best_stage);

        // Verify stage_reached memory was created
        $memory = PetMemory::where('user_pet_id', $userPet->id)
            ->where('type', 'stage_reached')
            ->first();
        $this->assertNotNull($memory);
        $this->assertStringContainsString('1', $memory->title);
    }

    // =========================================================================
    // Test 11: Feeding a dormant pet throws RuntimeException
    // =========================================================================

    public function test_feed_dormant_pet_throws(): void
    {
        $petService  = app(PetService::class);
        $feedService = app(PetFeedingService::class);

        $userPet = $petService->createInitialPet($this->user);
        $userPet->update(['status' => 'dormant', 'hunger' => 0]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('not active');
        $feedService->feed($this->user, $userPet, 10, 'dormant-key');
    }

    // =========================================================================
    // Test 12: total_xp on user_learning_stats is NOT affected by feeding
    // =========================================================================

    public function test_total_xp_not_affected_by_feeding(): void
    {
        $petService  = app(PetService::class);
        $feedService = app(PetFeedingService::class);

        // Give user 500 total_xp
        UserLearningStat::create([
            'user_id'    => $this->user->id,
            'total_xp'   => 500,
            'current_streak' => 0,
            'longest_streak' => 0,
        ]);

        $userPet = $petService->createInitialPet($this->user);
        $feedService->feed($this->user, $userPet, 50, 'xp-isolation-key');

        // total_xp must remain unchanged
        $stats = UserLearningStat::where('user_id', $this->user->id)->first();
        $this->assertEquals(500, $stats->total_xp);
    }
}
