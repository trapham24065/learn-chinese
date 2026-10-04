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

    // =========================================================================
    // Test 13: User can rename their pet
    // =========================================================================

    public function test_user_can_rename_pet(): void
    {
        $petService = app(PetService::class);
        $userPet = $petService->createInitialPet($this->user);

        $response = $this->actingAs($this->user)
            ->postJson(route('pet.rename'), [
                'name' => 'Tiểu Long',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'name'    => 'Tiểu Long',
            ]);

        $this->assertEquals('Tiểu Long', $userPet->fresh()->name);

        $memory = PetMemory::where('user_pet_id', $userPet->id)
            ->where('type', 'renamed')
            ->first();
        $this->assertNotNull($memory);
        $this->assertStringContainsString('Tiểu Long', $memory->title);
    }

    // =========================================================================
    // Test 14: Pet status returns companion data for floating widget
    // =========================================================================

    public function test_pet_status_returns_companion_data(): void
    {
        $petService = app(PetService::class);
        $petService->createInitialPet($this->user);

        $response = $this->actingAs($this->user)
            ->getJson(route('pet.status'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'has_pet',
                'id',
                'name',
                'stage',
                'stage_name',
                'emoji',
                'hunger',
                'hunger_state',
                'status',
                'exp',
                'daily_fed',
                'daily_remaining',
                'progress',
                'random_dialogue',
                'dialogues',
            ]);

        $this->assertTrue($response->json('has_pet'));
        $this->assertEquals('Trứng', $response->json('stage_name'));
        $this->assertEquals('🥚', $response->json('emoji'));
    }

    // =========================================================================
    // Test 15: Pet dialogue includes mastered vocabulary
    // =========================================================================

    public function test_pet_dialogue_includes_mastered_vocabulary(): void
    {
        $petService = app(PetService::class);
        $userPet = $petService->createInitialPet($this->user);

        // Create flashcard and mastered progress (repetition >= 2)
        $flashcard = \App\Models\Flashcard::create([
            'hanzi'   => '谢谢',
            'pinyin'  => 'xièxie',
            'meaning' => 'Cảm ơn',
        ]);

        \App\Models\FlashcardProgress::create([
            'user_id'      => $this->user->id,
            'flashcard_id' => $flashcard->id,
            'repetition'   => 2,
            'ease_factor'  => 2.5,
            'interval'     => 1,
            'next_review_at' => now()->addDay(),
        ]);

        $dialogueService = app(\App\Services\Pet\PetDialogueService::class);
        $dialogues = $dialogueService->getDialogues($this->user, $userPet);

        $foundVocab = false;
        foreach ($dialogues as $d) {
            if (str_contains($d, '谢谢') && str_contains($d, 'Cảm ơn')) {
                $foundVocab = true;
                break;
            }
        }

        $this->assertTrue($foundVocab, 'Pet dialogue should include mastered word 谢谢.');
    }

    // =========================================================================
    // Test 16: Dashboard displays pet hero card when pet exists
    // =========================================================================

    public function test_dashboard_displays_pet_companion(): void
    {
        $petService = app(PetService::class);
        $petService->createInitialPet($this->user);

        $response = $this->actingAs($this->user)
            ->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Giai đoạn 0: Trứng');
        $response->assertSee('Cho pet ăn nhanh');
    }

    // =========================================================================
    // Test 17: User can update pet personality
    // =========================================================================

    public function test_user_can_update_pet_personality(): void
    {
        $petService = app(PetService::class);
        $userPet = $petService->createInitialPet($this->user);

        $response = $this->actingAs($this->user)
            ->postJson(route('pet.personality'), [
                'personality' => 'curious',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success'     => true,
                'personality' => 'curious',
                'label'       => 'Tò mò & Khám phá',
                'emoji'       => '🔍',
            ]);

        $userPet->refresh();
        $this->assertEquals('curious', $userPet->personality);

        $memory = PetMemory::where('user_pet_id', $userPet->id)
            ->where('type', 'personality_changed')
            ->first();
        $this->assertNotNull($memory);
        $this->assertStringContainsString('Tò mò', $memory->title);
    }

    // =========================================================================
    // Test 18: Pet status returns affinity, personality, and contextual dialogues
    // =========================================================================

    public function test_pet_status_returns_affinity_and_personality(): void
    {
        $petService = app(PetService::class);
        $userPet = $petService->createInitialPet($this->user);
        $userPet->update(['personality' => 'shy', 'affinity' => 45]);

        $response = $this->actingAs($this->user)
            ->getJson(route('pet.status'));

        $response->assertStatus(200)
            ->assertJson([
                'has_pet'           => true,
                'personality'       => 'shy',
                'personality_label' => 'E thẹn & Dịu dàng',
                'personality_emoji' => '🌸',
                'affinity'          => 45,
            ]);

        $this->assertEquals('familiar', $response->json('affinity_tier.tier'));
        $this->assertEquals('Thân quen', $response->json('affinity_tier.name'));
    }

    // =========================================================================
    // Test 19: Pet affinity progression and tier-up memory
    // =========================================================================

    public function test_pet_affinity_progression_and_tier_up(): void
    {
        $petService = app(PetService::class);
        $userPet = $petService->createInitialPet($this->user);
        $userPet->update(['affinity' => 19]); // Just below 'met' tier (20)

        $affinityService = app(\App\Services\Pet\PetAffinityService::class);
        $result = $affinityService->addAffinity($userPet, 5, 'evolution'); // Evolution gives uncapped

        $this->assertTrue($result['tier_up']);
        $this->assertEquals('met', $result['tier']['tier']);
        $this->assertEquals(24, $result['affinity']);

        $memory = PetMemory::where('user_pet_id', $userPet->id)
            ->where('type', 'affinity_tier_up')
            ->first();
        $this->assertNotNull($memory);
        $this->assertStringContainsString('Làm quen', $memory->title);
    }

    // =========================================================================
    // Test 20: Pet affinity respects daily caps
    // =========================================================================

    public function test_pet_affinity_respects_daily_caps(): void
    {
        $petService = app(PetService::class);
        $userPet = $petService->createInitialPet($this->user);
        $affinityService = app(\App\Services\Pet\PetAffinityService::class);

        // Daily cap for study_session is 4
        $res1 = $affinityService->addAffinity($userPet, 3, 'study_session');
        $this->assertEquals(3, $res1['added']);

        $res2 = $affinityService->addAffinity($userPet, 3, 'study_session');
        $this->assertEquals(1, $res2['added']); // Only 1 remaining of cap 4

        $res3 = $affinityService->addAffinity($userPet, 2, 'study_session');
        $this->assertEquals(0, $res3['added']);
        $this->assertTrue($res3['daily_capped']);
    }

    // =========================================================================
    // Test 21: Pet memory recall and dialogue formatting tailored by personality
    // =========================================================================

    public function test_pet_memory_recall_and_dialogue_formatting(): void
    {
        $petService = app(PetService::class);
        $userPet = $petService->createInitialPet($this->user);
        $userPet->update(['personality' => 'playful']);

        $flashcard = \App\Models\Flashcard::create([
            'hanzi'   => '朋友',
            'pinyin'  => 'péngyou',
            'meaning' => 'Bạn bè',
        ]);

        $memoryService = app(\App\Services\Pet\PetMemoryService::class);
        $memory = $memoryService->rememberFirstMastered($userPet, $flashcard);

        $this->assertNotNull($memory);
        $this->assertEquals('first_mastered', $memory->type);

        $dialogue = $memoryService->formatMemoryDialogue($memory, 'playful');
        $this->assertStringContainsString('朋友', $dialogue);
        $this->assertStringContainsString('đố bạn đọc lại được nè', $dialogue);

        $shyDialogue = $memoryService->formatMemoryDialogue($memory, 'shy');
        $this->assertStringContainsString('ấm áp ghê', $shyDialogue);
    }

    // =========================================================================
    // Test 22: Pet DNA computation and dominant trait detection
    // =========================================================================

    public function test_pet_dna_computation_and_display(): void
    {
        $petService = app(PetService::class);
        $userPet = $petService->createInitialPet($this->user);

        // Create mastered flashcard
        $flashcard = \App\Models\Flashcard::create([
            'hanzi'   => '猫',
            'pinyin'  => 'māo',
            'meaning' => 'Con mèo',
        ]);

        \App\Models\FlashcardProgress::create([
            'user_id'      => $this->user->id,
            'flashcard_id' => $flashcard->id,
            'repetition'   => 2,
            'ease_factor'  => 2.5,
            'interval'     => 1,
            'next_review_at' => now()->addDay(),
        ]);

        $dnaService = app(\App\Services\Pet\PetDnaService::class);
        $dna = $dnaService->compute($this->user, $userPet);

        $this->assertIsArray($dna);
        $this->assertArrayHasKey('vocabulary', $dna);
        $this->assertArrayHasKey('dominant_trait', $dna);
        $this->assertGreaterThan(0, $dna['vocabulary']);

        $displayDna = $dnaService->getDisplayDna($userPet->fresh());
        $this->assertNotEmpty($displayDna);
        $this->assertEquals('Từ vựng', $displayDna[0]['label']);
    }

    // =========================================================================
    // Test 23: Mastered flashcard review adds object to Pet Memory World
    // =========================================================================

    public function test_mastering_word_adds_object_to_pet_world(): void
    {
        $petService = app(PetService::class);
        $userPet = $petService->createInitialPet($this->user);

        $flashcard = \App\Models\Flashcard::create([
            'hanzi'   => '山',
            'pinyin'  => 'shān',
            'meaning' => 'Ngọn núi',
        ]);

        // First review (repetition 0 -> 1)
        $this->actingAs($this->user)->postJson(route('flashcards.review'), [
            'flashcard_id' => $flashcard->id,
            'quality'      => 'known',
        ])->assertStatus(200);

        $this->assertEquals(0, \App\Models\PetWorldObject::where('user_pet_id', $userPet->id)->count());

        // Second review (repetition 1 -> 2: Mastered)
        $this->actingAs($this->user)->postJson(route('flashcards.review'), [
            'flashcard_id' => $flashcard->id,
            'quality'      => 'known',
        ])->assertStatus(200);

        $worldObject = \App\Models\PetWorldObject::where('user_pet_id', $userPet->id)->first();
        $this->assertNotNull($worldObject);
        $this->assertEquals('山', $worldObject->hanzi);
        $this->assertEquals('⛰️', $worldObject->emoji);
        $this->assertEquals('nature', $worldObject->object_type);
    }

    // =========================================================================
    // Test 24: Dream sentence generation from world objects
    // =========================================================================

    public function test_dream_sentence_generation_and_api_endpoints(): void
    {
        $petService = app(PetService::class);
        $userPet = $petService->createInitialPet($this->user);
        $worldService = app(\App\Services\Pet\PetWorldService::class);

        $words = [
            ['hanzi' => '猫', 'pinyin' => 'māo', 'meaning' => 'Mèo'],
            ['hanzi' => '鱼', 'pinyin' => 'yú', 'meaning' => 'Cá'],
            ['hanzi' => '月', 'pinyin' => 'yuè', 'meaning' => 'Mặt trăng'],
        ];

        foreach ($words as $w) {
            $fc = \App\Models\Flashcard::create($w);
            $worldService->addWordToWorld($userPet, $fc);
        }

        $dream = $worldService->generateDreamSentence($userPet);
        $this->assertNotNull($dream);
        $this->assertStringContainsString('梦', $dream);

        // Test DNA API
        $responseDna = $this->actingAs($this->user)->getJson(route('pet.dna'));
        $responseDna->assertStatus(200)
            ->assertJsonStructure(['dna', 'personality']);

        // Test World API
        $responseWorld = $this->actingAs($this->user)->getJson(route('pet.world'));
        $responseWorld->assertStatus(200)
            ->assertJsonStructure(['objects', 'dream']);
        $this->assertCount(3, $responseWorld->json('objects'));
    }

    // =========================================================================
    // Step 2 Tests: Sensory Feeding Ritual & Absence Recall
    // =========================================================================

    public function test_sensory_feeding_returns_food_payload_and_reaction(): void
    {
        $petService  = app(PetService::class);
        $feedService = app(PetFeedingService::class);

        $userPet = $petService->createInitialPet($this->user);
        $userPet->update(['hunger' => 50]);

        // Direct service test
        $result = $feedService->feed($this->user, $userPet, 10, 'key-sensory-10');
        $this->assertTrue($result['success']);
        $this->assertNotNull($result['food']);
        $this->assertEquals('饺子', $result['food']['hanzi']);
        $this->assertEquals('🥟', $result['food']['emoji']);
        $this->assertStringContainsString('好吃', $result['food_reaction']);
        $this->assertStringContainsString('好吃', $result['chinese_say']);

        // Controller API test with apple (amount 5)
        $response = $this->actingAs($this->user)->postJson(route('pet.feed'), [
            'amount'          => 5,
            'idempotency_key' => 'key-sensory-api-5',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('food.hanzi', '苹果')
            ->assertJsonPath('food.emoji', '🍎')
            ->assertJsonStructure([
                'success',
                'food',
                'food_reaction',
                'chinese_say',
                'hunger',
                'daily_fed',
            ]);
    }

    public function test_absence_recall_greetings_scale_with_days_absent(): void
    {
        $petService      = app(PetService::class);
        $dialogueService = app(\App\Services\Pet\PetDialogueService::class);

        $userPet = $petService->createInitialPet($this->user);

        // Case A: Absent 1 day -> "今天你很忙吗？"
        $userPet->update(['last_studied_at' => now()->subDays(1)->subHours(1)]);
        $dialogues1 = $dialogueService->getDialogues($this->user, $userPet);
        $this->assertStringContainsString('今天你很忙吗？', $dialogues1[0]);

        // Case B: Absent 3 days -> "好久不见……"
        $userPet->update(['last_studied_at' => now()->subDays(3)->subHours(2)]);
        $dialogues3 = $dialogueService->getDialogues($this->user, $userPet);
        $this->assertStringContainsString('好久不见……', $dialogues3[0]);

        // Case C: Absent 8 days -> "❤️ 你回来了！"
        $userPet->update(['last_studied_at' => now()->subDays(8)]);
        $dialogues8 = $dialogueService->getDialogues($this->user, $userPet);
        $this->assertStringContainsString('❤️ 你回来了！', $dialogues8[0]);
    }

    // =========================================================================
    // Step 3 Tests: Cozy Pet Room & Mutual Memory Timeline
    // =========================================================================

    public function test_pet_memory_timeline_and_new_milestones(): void
    {
        $petService    = app(PetService::class);
        $memoryService = app(\App\Services\Pet\PetMemoryService::class);

        $userPet = $petService->createInitialPet($this->user);

        // 1. Test rememberFirstMeeting
        $memoryService->rememberFirstMeeting($userPet);
        $this->assertEquals(1, PetMemory::where('user_pet_id', $userPet->id)->where('type', 'first_meeting')->count());
        // Idempotent
        $memoryService->rememberFirstMeeting($userPet);
        $this->assertEquals(1, PetMemory::where('user_pet_id', $userPet->id)->where('type', 'first_meeting')->count());

        // 2. Test rememberFirstFeeding
        $memoryService->rememberFirstFeeding($userPet, PetFeedingService::FOOD_ITEMS[5]);
        $this->assertEquals(1, PetMemory::where('user_pet_id', $userPet->id)->where('type', 'first_feeding')->count());

        // 3. Test rememberFirstLesson
        $memoryService->rememberFirstLesson($userPet, 'Bài 1: Chào hỏi');
        $this->assertEquals(1, PetMemory::where('user_pet_id', $userPet->id)->where('type', 'first_lesson')->count());

        // 4. Test rememberFirstPerfectQuiz
        $memoryService->rememberFirstPerfectQuiz($userPet, 'Quiz HSK 1 - Điểm tuyệt đối');
        $this->assertEquals(1, PetMemory::where('user_pet_id', $userPet->id)->where('type', 'first_perfect_quiz')->count());

        // 5. Test getTimelineMemories
        $timeline = $memoryService->getTimelineMemories($userPet);
        $this->assertNotEmpty($timeline);

        $firstMeetingMem = collect($timeline)->firstWhere('type', 'first_meeting');
        $this->assertNotNull($firstMeetingMem);
        $this->assertEquals('growth', $firstMeetingMem['category']);
        $this->assertNotEmpty($firstMeetingMem['pet_reflection']);
        $this->assertArrayHasKey('days_ago_badge', $firstMeetingMem);

        $firstFeedMem = collect($timeline)->firstWhere('type', 'first_feeding');
        $this->assertNotNull($firstFeedMem);
        $this->assertEquals('care', $firstFeedMem['category']);
        $this->assertStringContainsString('苹果', $firstFeedMem['pet_reflection']);

        $firstQuizMem = collect($timeline)->firstWhere('type', 'first_perfect_quiz');
        $this->assertNotNull($firstQuizMem);
        $this->assertEquals('learning', $firstQuizMem['category']);

        // 6. Test GET /student/pet route returns timelineMemories and roomStats
        $response = $this->actingAs($this->user)->get(route('pet.index'));
        $response->assertStatus(200);
        $response->assertViewHas('timelineMemories');
        $response->assertViewHas('roomStats');
        $roomStats = $response->viewData('roomStats');
        $this->assertArrayHasKey('days_together', $roomStats);
        $this->assertArrayHasKey('mastered_words', $roomStats);
        $this->assertArrayHasKey('streak', $roomStats);
    }

    // =========================================================================
    // Test 28: Interaction API records poke & cuddle and responds with memory tier
    // =========================================================================

    public function test_interaction_api_records_and_returns_dialogue(): void
    {
        $petService = app(PetService::class);
        $userPet = $petService->createInitialPet($this->user);

        // 1. Initial poke (Newcomer)
        $res = $this->actingAs($this->user)->postJson(route('pet.interact'), [
            'action'      => 'poke',
            'consecutive' => 1,
        ]);

        $res->assertStatus(200);
        $res->assertJsonPath('success', true);
        $res->assertJsonPath('action', 'poke');
        $this->assertEquals(1, $res->json('stats.poke_count'));
        $this->assertEquals('newcomer', $res->json('stats.interaction_level'));
        $this->assertNotEmpty($res->json('dialogue.chinese'));
        $this->assertNotEmpty($res->json('dialogue.vietnamese'));

        // 2. Consecutive poke 2 -> returns "你又戳我啦……"
        $res2 = $this->actingAs($this->user)->postJson(route('pet.interact'), [
            'action'      => 'poke',
            'consecutive' => 2,
        ]);
        $res2->assertStatus(200);
        $this->assertEquals('consecutive_poked', $res2->json('dialogue.reaction_type'));

        // 3. Consecutive poke >= 6 -> Dizzy reaction
        $res3 = $this->actingAs($this->user)->postJson(route('pet.interact'), [
            'action'      => 'poke',
            'consecutive' => 6,
        ]);
        $res3->assertStatus(200);
        $this->assertEquals('dizzy', $res3->json('dialogue.reaction_type'));
        $this->assertStringContainsString('头好晕呀', $res3->json('dialogue.chinese'));

        // 4. Cuddle action
        $resCuddle = $this->actingAs($this->user)->postJson(route('pet.interact'), [
            'action' => 'cuddle',
        ]);
        $resCuddle->assertStatus(200);
        $this->assertEquals('cuddle', $resCuddle->json('dialogue.reaction_type'));
        $this->assertEquals(1, $resCuddle->json('stats.cuddle_count'));

        // 5. Memory Tier: simulate 35 pokes -> Soulmate level
        $userPet->update([
            'interaction_stats' => array_merge($userPet->getInteractionStats(), [
                'poke_count' => 35,
            ]),
        ]);

        $resSoulmate = $this->actingAs($this->user)->postJson(route('pet.interact'), [
            'action'      => 'poke',
            'consecutive' => 1,
        ]);
        $resSoulmate->assertStatus(200);
        $this->assertEquals('soulmate', $resSoulmate->json('stats.interaction_level'));
        $this->assertEquals('memory_soulmate', $resSoulmate->json('dialogue.reaction_type'));
    }

    // =========================================================================
    // Test 29: Mini Quiz Reward awards XP via LearningActivityService
    // =========================================================================

    public function test_mini_quiz_reward_awards_xp_server_authoritative(): void
    {
        $petService = app(PetService::class);
        $petService->createInitialPet($this->user);

        $key = 'test_quiz_key_' . uniqid();
        $res = $this->actingAs($this->user)->postJson(route('pet.mini-quiz-reward'), [
            'correct'         => true,
            'idempotency_key' => $key,
        ]);

        $res->assertStatus(200);
        $res->assertJsonPath('success', true);
        $this->assertEquals(2, $res->json('xp_earned'));
        $this->assertGreaterThanOrEqual(2, $res->json('total_xp'));

        // Duplicate idempotency_key returns 0 earned
        $resDup = $this->actingAs($this->user)->postJson(route('pet.mini-quiz-reward'), [
            'correct'         => true,
            'idempotency_key' => $key,
        ]);
        $resDup->assertStatus(200);
        $this->assertEquals(0, $resDup->json('xp_earned'));
    }

    // =========================================================================
    // Test 30: Mini Quiz Endpoint returns rotating questions and respects exclude_ids
    // =========================================================================

    public function test_mini_quiz_endpoint_returns_questions_and_respects_exclude_ids(): void
    {
        $card1 = \App\Models\Flashcard::create([
            'hanzi'   => '学习',
            'pinyin'  => 'xuéxí',
            'meaning' => 'Học tập',
            'hsk_level' => 1,
            'is_active' => true,
        ]);

        $card2 = \App\Models\Flashcard::create([
            'hanzi'   => '朋友',
            'pinyin'  => 'péngyou',
            'meaning' => 'Bạn bè',
            'hsk_level' => 1,
            'is_active' => true,
        ]);

        $card3 = \App\Models\Flashcard::create([
            'hanzi'   => '高兴',
            'pinyin'  => 'gāoxìng',
            'meaning' => 'Vui mừng',
            'hsk_level' => 1,
            'is_active' => true,
        ]);

        $card4 = \App\Models\Flashcard::create([
            'hanzi'   => '苹果',
            'pinyin'  => 'píngguǒ',
            'meaning' => 'Quả táo',
            'hsk_level' => 1,
            'is_active' => true,
        ]);

        $res1 = $this->actingAs($this->user)->getJson(route('pet.mini-quiz'));
        $res1->assertStatus(200);
        $res1->assertJsonPath('success', true);
        $this->assertNotEmpty($res1->json('question'));
        $this->assertCount(4, $res1->json('options'));

        $firstWordId = $res1->json('word_id');
        $this->assertNotNull($firstWordId);

        // Exclude first card
        $res2 = $this->actingAs($this->user)->getJson(route('pet.mini-quiz', ['exclude_ids' => $firstWordId]));
        $res2->assertStatus(200);
        $this->assertNotEquals($firstWordId, $res2->json('word_id'));
    }
}


