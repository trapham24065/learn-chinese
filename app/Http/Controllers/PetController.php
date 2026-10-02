<?php

namespace App\Http\Controllers;

use App\Models\LearningActivity;
use App\Models\PetMemory;
use App\Services\Pet\PetDialogueService;
use App\Services\Pet\PetFeedingService;
use App\Services\Pet\PetGrowthService;
use App\Services\Pet\PetService;
use App\Services\Pet\VocabularyMasteryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PetController extends Controller
{
    public function __construct(
        protected PetService $petService,
        protected PetFeedingService $feedingService,
        protected PetGrowthService $growthService,
        protected PetDialogueService $dialogueService,
        protected VocabularyMasteryService $masteryService,
        protected \App\Services\Pet\PetAffinityService $affinityService,
        protected \App\Services\Pet\PetMemoryService $memoryService,
    ) {}

    /**
     * GET /student/pet — Main pet page.
     * Creates the initial pet automatically on first visit.
     */
    public function index(Request $request): View
    {
        $user    = $request->user();
        $userPet = $this->petService->getActivePet($user)
            ?? $this->petService->createInitialPet($user);

        $progress       = $this->growthService->calculateExpProgress($userPet);
        $dailyFed       = $this->feedingService->getDailyFedAmount($userPet);
        $dailyRemaining = $this->feedingService->getRemainingDailyCapacity($userPet);
        $memories       = $userPet->memories()->latest('created_at')->limit(20)->get();

        // Dialogues for emotional companion
        $dialogues      = $this->dialogueService->getDialogues($user, $userPet);
        $randomDialogue = $this->dialogueService->getRandomDialogue($user, $userPet);

        // Stages with unlock requirements for the Growth tab
        $stages = $userPet->pet->stages()->orderBy('stage')->get();

        // User stats to check against stage requirements
        $masteredCount = $this->masteryService->getMasteredCount($user);
        $usedCount     = $this->masteryService->getUsedCount($user);
        $readingCount  = LearningActivity::where('user_id', $user->id)
            ->whereIn('activity_type', ['reading_completed'])
            ->count();
        $listeningCount = LearningActivity::where('user_id', $user->id)
            ->whereIn('activity_type', ['audio_quiz', 'audio_quiz_completed'])
            ->count();

        $userReqStats = [
            'exp'               => $userPet->exp,
            'mastered_vocab'    => $masteredCount,
            'used_vocab'        => $usedCount,
            'reading_activity'  => $readingCount,
            'listening_activity'=> $listeningCount,
        ];

        // Mastered words for the Pet Vocabulary tab
        $masteredWords = $this->masteryService->getAllMastered($user, 100);

        // Affinity & Personality Summary
        $affinitySummary = $this->affinityService->getAffinitySummary($userPet);
        $affinityTier    = $userPet->getAffinityTier();

        return view('pet.index', compact(
            'userPet',
            'progress',
            'dailyFed',
            'dailyRemaining',
            'memories',
            'dialogues',
            'randomDialogue',
            'stages',
            'userReqStats',
            'masteredCount',
            'masteredWords',
            'affinitySummary',
            'affinityTier'
        ));
    }

    /**
     * POST /student/pet/feed — Feed the pet (JSON).
     */
    public function feed(Request $request): JsonResponse
    {
        $request->validate([
            'amount'          => ['required', 'integer', 'in:5,10,20,50'],
            'idempotency_key' => ['required', 'string', 'max:191'],
        ]);

        $user    = $request->user();
        $userPet = $this->petService->getActivePet($user);

        if (!$userPet) {
            return response()->json(['error' => 'No pet found.'], 404);
        }

        try {
            $result = $this->feedingService->feed(
                $user,
                $userPet,
                (int) $request->input('amount'),
                $request->input('idempotency_key')
            );

            // Fresh dialogue after feeding
            $userPet->refresh();
            $result['dialogue'] = $this->dialogueService->getRandomDialogue($user, $userPet);
            $result['dialogues'] = $this->dialogueService->getDialogues($user, $userPet);

            return response()->json($result);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        } catch (\RuntimeException $e) {
            return response()->json(['error' => $e->getMessage()], 409);
        }
    }

    /**
     * POST /student/pet/rename — Rename pet.
     */
    public function rename(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'min:1', 'max:30'],
        ]);

        $user    = $request->user();
        $userPet = $this->petService->getActivePet($user);

        if (!$userPet) {
            return response()->json(['error' => 'No pet found.'], 404);
        }

        $newName = trim($request->input('name'));
        $oldName = $userPet->name ?? $userPet->pet->name;

        $userPet->update(['name' => $newName]);

        PetMemory::create([
            'user_pet_id' => $userPet->id,
            'type'        => 'renamed',
            'title'       => "Đổi tên thành '{$newName}'",
            'description' => "Từ '{$oldName}' sang '{$newName}'. Một cái tên thật hay!",
            'metadata'    => ['old_name' => $oldName, 'new_name' => $newName],
            'created_at'  => now(),
        ]);

        return response()->json([
            'success' => true,
            'name'    => $newName,
            'message' => "Đã đổi tên pet thành '{$newName}' thành công!",
        ]);
    }

    /**
     * POST /student/pet/personality — Update pet personality archetype.
     */
    public function updatePersonality(Request $request): JsonResponse
    {
        $request->validate([
            'personality' => ['required', 'string', 'in:playful,curious,shy,cheerful,calm'],
        ]);

        $user    = $request->user();
        $userPet = $this->petService->getActivePet($user);

        if (!$userPet) {
            return response()->json(['error' => 'No pet found.'], 404);
        }

        $oldPersonality = $userPet->personality ?? 'playful';
        $newPersonality = $request->input('personality');

        $userPet->update(['personality' => $newPersonality]);
        $userPet->refresh();

        if ($oldPersonality !== $newPersonality) {
            PetMemory::create([
                'user_pet_id' => $userPet->id,
                'type'        => 'personality_changed',
                'title'       => "Tính cách mới: " . $userPet->getPersonalityLabel(),
                'description' => "Pet đã chuyển sang phong cách '{$userPet->getPersonalityLabel()}'. " . $userPet->getPersonalityDescription(),
                'metadata'    => ['old' => $oldPersonality, 'new' => $newPersonality],
                'created_at'  => now(),
            ]);
        }

        return response()->json([
            'success'          => true,
            'personality'      => $newPersonality,
            'label'            => $userPet->getPersonalityLabel(),
            'emoji'            => $userPet->getPersonalityEmoji(),
            'description'      => $userPet->getPersonalityDescription(),
            'random_dialogue'  => $this->dialogueService->getRandomDialogue($user, $userPet),
            'dialogues'        => $this->dialogueService->getDialogues($user, $userPet),
            'message'          => "Đã cập nhật tính cách thành '{$userPet->getPersonalityLabel()}'!",
        ]);
    }

    /**
     * GET /student/pet/status — JSON status for AJAX polling (floating companion / dashboard card).
     */
    public function status(Request $request): JsonResponse
    {
        $user    = $request->user();
        $userPet = $this->petService->getActivePet($user);

        if (!$userPet) {
            return response()->json(['has_pet' => false]);
        }

        $currentStage = $userPet->pet->stages->where('stage', $userPet->stage)->first();
        
        $recentWordParam = $request->query('recent_word');
        $randomWord = null;
        if ($recentWordParam) {
            $matchedCard = \App\Models\Flashcard::where('hanzi', $recentWordParam)->first();
            if ($matchedCard) {
                $randomWord = (object) [
                    'hanzi'   => $matchedCard->hanzi,
                    'pinyin'  => $matchedCard->pinyin,
                    'meaning' => $matchedCard->meaning,
                ];
            }
        }
        if (!$randomWord) {
            $randomWord = $this->masteryService->getRandomMastered($user, 1)->first();
        }

        // Optional activity/struggle/streak/recent_word context from client
        $context = array_filter([
            'activity'    => $request->query('activity'),
            'struggling'  => $request->boolean('struggling'),
            'streak'      => $request->query('streak') ? (int) $request->query('streak') : null,
            'recent_word' => $recentWordParam,
        ]);

        return response()->json([
            'has_pet'             => true,
            'id'                  => $userPet->id,
            'name'                => $userPet->name ?? $userPet->pet->name,
            'stage'               => $userPet->stage,
            'stage_name'          => $currentStage?->name ?? 'Trứng',
            'emoji'               => $currentStage?->emoji ?? '🥚',
            'hunger'              => $userPet->hunger,
            'hunger_state'        => $userPet->getHungerState(),
            'status'              => $userPet->status,
            'personality'         => $userPet->personality ?? 'playful',
            'personality_label'   => $userPet->getPersonalityLabel(),
            'personality_emoji'   => $userPet->getPersonalityEmoji(),
            'personality_desc'    => $userPet->getPersonalityDescription(),
            'affinity'            => (int) ($userPet->affinity ?? 0),
            'affinity_tier'       => $userPet->getAffinityTier(),
            'affinity_summary'    => $this->affinityService->getAffinitySummary($userPet),
            'is_active'           => $userPet->isActive(),
            'is_dormant'          => $userPet->isDormant(),
            'is_egg'              => $userPet->isEgg(),
            'exp'                 => $userPet->exp,
            'daily_fed'           => $this->feedingService->getDailyFedAmount($userPet),
            'daily_remaining'     => $this->feedingService->getRemainingDailyCapacity($userPet),
            'progress'            => $this->growthService->calculateExpProgress($userPet),
            'random_dialogue'     => $this->dialogueService->getRandomDialogue($user, $userPet, $context),
            'dialogues'           => $this->dialogueService->getDialogues($user, $userPet, $context),
            'random_word'         => $randomWord ? [
                'hanzi'   => $randomWord->hanzi,
                'pinyin'  => $randomWord->pinyin,
                'meaning' => $randomWord->meaning,
            ] : null,
        ]);
    }
}
