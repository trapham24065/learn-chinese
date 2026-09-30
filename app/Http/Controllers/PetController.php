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
            'masteredWords'
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
        $randomWord = $this->masteryService->getRandomMastered($user, 1)->first();

        return response()->json([
            'has_pet'         => true,
            'id'              => $userPet->id,
            'name'            => $userPet->name ?? $userPet->pet->name,
            'stage'           => $userPet->stage,
            'stage_name'      => $currentStage?->name ?? 'Trứng',
            'emoji'           => $currentStage?->emoji ?? '🥚',
            'hunger'          => $userPet->hunger,
            'hunger_state'    => $userPet->getHungerState(),
            'status'          => $userPet->status,
            'is_active'       => $userPet->isActive(),
            'is_dormant'      => $userPet->isDormant(),
            'is_egg'          => $userPet->isEgg(),
            'exp'             => $userPet->exp,
            'daily_fed'       => $this->feedingService->getDailyFedAmount($userPet),
            'daily_remaining' => $this->feedingService->getRemainingDailyCapacity($userPet),
            'progress'        => $this->growthService->calculateExpProgress($userPet),
            'random_dialogue' => $this->dialogueService->getRandomDialogue($user, $userPet),
            'dialogues'       => $this->dialogueService->getDialogues($user, $userPet),
            'random_word'     => $randomWord ? [
                'hanzi'   => $randomWord->hanzi,
                'pinyin'  => $randomWord->pinyin,
                'meaning' => $randomWord->meaning,
            ] : null,
        ]);
    }
}
