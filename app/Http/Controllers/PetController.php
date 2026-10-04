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
        protected \App\Services\Pet\PetDnaService $dnaService,
        protected \App\Services\Pet\PetWorldService $worldService,
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

        // Dialogues for emotional companion (both raw strings and rich objects)
        $dialogues      = $this->dialogueService->getDialogues($user, $userPet);
        $randomDialogue = $this->dialogueService->getRandomDialogue($user, $userPet);
        $richDialogues  = $this->dialogueService->getRichDialogues($user, $userPet);

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

        $dna        = $this->dnaService->getDisplayDna($userPet);
        $worldObjs  = $this->worldService->getWorldObjects($userPet);
        $dreamSent  = $this->worldService->generateDreamSentence($userPet);
        $foodMenu   = PetFeedingService::getFoodMenu();

        // Enriched timeline memories and cozy room environment stats
        $timelineMemories = $this->memoryService->getTimelineMemories($userPet);
        $recentFeedLog    = $userPet->feedingLogs()->latest('fed_at')->first();
        $recentFood       = ($recentFeedLog && isset(PetFeedingService::FOOD_ITEMS[$recentFeedLog->xp_amount]))
            ? PetFeedingService::FOOD_ITEMS[$recentFeedLog->xp_amount]
            : null;

        $roomStats = [
            'days_together'  => max(1, (int) ($userPet->created_at ? $userPet->created_at->diffInDays(now()) : 1)),
            'mastered_words' => $masteredCount,
            'streak'         => (int) ($user->learningStats->current_streak ?? 0),
            'recent_food'    => $recentFood,
            'memories_count' => count($timelineMemories),
        ];

        return view('pet.index', compact(
            'userPet',
            'progress',
            'dailyFed',
            'dailyRemaining',
            'memories',
            'timelineMemories',
            'roomStats',
            'dialogues',
            'randomDialogue',
            'richDialogues',
            'stages',
            'userReqStats',
            'masteredCount',
            'masteredWords',
            'affinitySummary',
            'affinityTier',
            'dna',
            'worldObjs',
            'dreamSent',
            'foodMenu'
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
            $result['rich_dialogues'] = $this->dialogueService->getRichDialogues($user, $userPet);

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
            'rich_dialogues'   => $this->dialogueService->getRichDialogues($user, $userPet),
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
            'interaction_stats'   => $userPet->getInteractionStats(),
            'is_active'           => $userPet->isActive(),
            'is_dormant'          => $userPet->isDormant(),
            'is_egg'              => $userPet->isEgg(),
            'exp'                 => $userPet->exp,
            'daily_fed'           => $this->feedingService->getDailyFedAmount($userPet),
            'daily_remaining'     => $this->feedingService->getRemainingDailyCapacity($userPet),
            'progress'            => $this->growthService->calculateExpProgress($userPet),
            'random_dialogue'     => $this->dialogueService->getRandomDialogue($user, $userPet, $context),
            'dialogues'           => $this->dialogueService->getDialogues($user, $userPet, $context),
            'rich_dialogues'      => $this->dialogueService->getRichDialogues($user, $userPet, $context),
            'food_menu'           => PetFeedingService::getFoodMenu(),
            'random_word'         => $randomWord ? [
                'hanzi'   => $randomWord->hanzi,
                'pinyin'  => $randomWord->pinyin,
                'meaning' => $randomWord->meaning,
            ] : null,
        ]);
    }

    public function dna(Request $request): JsonResponse
    {
        $user   = $request->user();
        $userPet = $this->petService->getActivePet($user);
        if (!$userPet) {
            return response()->json(['dna' => [], 'personality' => null]);
        }

        $dna = $userPet->learning_dna ?? [];
        $age = isset($dna['computed_at'])
            ? now()->diffInMinutes($dna['computed_at'])
            : 999;

        if ($age > 60) {
            $dna = $this->dnaService->compute($user, $userPet);
        }

        return response()->json([
            'dna'         => $this->dnaService->getDisplayDna($userPet),
            'personality' => [
                'type'    => $userPet->personality,
                'label'   => $userPet->getPersonalityLabel(),
                'emoji'   => $userPet->getPersonalityEmoji(),
                'trait'   => $dna['dominant_trait'] ?? 'explorer',
            ],
        ]);
    }

    public function world(Request $request): JsonResponse
    {
        $user   = $request->user();
        $userPet = $this->petService->getActivePet($user);
        if (!$userPet) {
            return response()->json(['objects' => [], 'dream' => null]);
        }

        return response()->json([
            'objects' => $this->worldService->getWorldObjects($userPet),
            'dream'   => $this->worldService->generateDreamSentence($userPet),
        ]);
    }

    /**
     * POST /student/pet/interact — Record interaction (poke, cuddle, wake, dizzy) and return reactive dialogue.
     */
    public function interact(Request $request): JsonResponse
    {
        $request->validate([
            'action'       => ['required', 'string', 'in:poke,cuddle,wake,dizzy'],
            'consecutive'  => ['nullable', 'integer', 'min:1', 'max:50'],
            'page_context' => ['nullable', 'string', 'max:50'],
        ]);

        $user = $request->user();
        $userPet = $this->petService->getActivePet($user);

        if (!$userPet) {
            return response()->json(['error' => 'No pet found.'], 404);
        }

        $action = $request->input('action', 'poke');
        $consecutive = (int) $request->input('consecutive', 1);

        $stats = $userPet->recordInteraction($action, $consecutive);
        $context = $request->all();

        $dialogue = $this->dialogueService->getInteractionDialogue($user, $userPet, $action, $context);

        return response()->json([
            'success'  => true,
            'action'   => $action,
            'dialogue' => $dialogue,
            'stats'    => $stats,
        ]);
    }

    /**
     * GET /student/pet/mini-quiz — Fetch a fresh, non-repeating mini pop quiz question.
     */
    public function miniQuiz(Request $request): JsonResponse
    {
        $user = $request->user();

        // Parse excluded word IDs passed from client history
        $excludeIds = array_filter(array_map('intval', explode(',', (string) $request->query('exclude_ids', ''))));

        // 1. Try to fetch from user's reviewed/mastered flashcards first
        $userCardQuery = \App\Models\FlashcardProgress::where('user_id', $user->id);
        if (!empty($excludeIds)) {
            $userCardQuery->whereNotIn('flashcard_id', $excludeIds);
        }
        $userCardId = (clone $userCardQuery)->inRandomOrder()->value('flashcard_id');

        $card = null;
        if ($userCardId) {
            $card = \App\Models\Flashcard::where('id', $userCardId)
                ->whereNotNull('hanzi')
                ->whereNotNull('meaning')
                ->first();
        }

        // 2. If no user card available or all excluded, fetch from general active flashcards
        if (!$card) {
            $cardQuery = \App\Models\Flashcard::whereNotNull('hanzi')
                ->whereNotNull('meaning')
                ->where('is_active', true);
            if (!empty($excludeIds)) {
                $cardQuery->whereNotIn('id', $excludeIds);
            }
            $card = $cardQuery->inRandomOrder()->first();
        }

        // 3. Fallback if still null (all excluded in small db) -> reset exclusion and pick any
        if (!$card) {
            $card = \App\Models\Flashcard::whereNotNull('hanzi')
                ->whereNotNull('meaning')
                ->where('is_active', true)
                ->inRandomOrder()
                ->first();
        }

        // Fallback default if DB has no flashcards at all
        if (!$card) {
            return response()->json([
                'success'   => true,
                'word_id'   => 0,
                'quiz_type' => 'hanzi_to_meaning',
                'question'  => 'Chữ 「学习」[xuéxí] có nghĩa là gì?',
                'word'      => '学习',
                'pinyin'    => 'xuéxí',
                'meaning'   => 'Học tập',
                'options'   => ['Học tập', 'Ăn cơm', 'Nước uống', 'Đi ngủ'],
                'correct'   => 'Học tập',
            ]);
        }

        // Determine quiz format:
        // Format A (70%): Hanzi -> Meaning
        // Format B (30%): Meaning -> Hanzi
        $quizType = (rand(1, 10) <= 7) ? 'hanzi_to_meaning' : 'meaning_to_hanzi';

        if ($quizType === 'meaning_to_hanzi') {
            $hanziDistractors = \App\Models\Flashcard::where('id', '!=', $card->id)
                ->whereNotNull('hanzi')
                ->where('hanzi', '!=', $card->hanzi)
                ->inRandomOrder()
                ->limit(3)
                ->pluck('hanzi')
                ->toArray();

            $backupHanzi = ['谢谢', '你好', '再见', '老师', '朋友', '苹果', '喝水', '看书'];
            foreach ($backupHanzi as $bh) {
                if (count($hanziDistractors) >= 3) break;
                if ($bh !== $card->hanzi && !in_array($bh, $hanziDistractors, true)) {
                    $hanziDistractors[] = $bh;
                }
            }

            $options = array_merge([$card->hanzi], array_slice($hanziDistractors, 0, 3));
            shuffle($options);

            return response()->json([
                'success'   => true,
                'word_id'   => $card->id,
                'quiz_type' => $quizType,
                'question'  => "Từ có nghĩa là \"{$card->meaning}\" trong tiếng Trung viết thế nào?",
                'word'      => $card->hanzi,
                'pinyin'    => $card->pinyin,
                'meaning'   => $card->meaning,
                'options'   => $options,
                'correct'   => $card->hanzi,
            ]);
        }

        // Default: Hanzi -> Meaning
        $distractors = \App\Models\Flashcard::where('id', '!=', $card->id)
            ->whereNotNull('meaning')
            ->where('meaning', '!=', $card->meaning)
            ->inRandomOrder()
            ->limit(3)
            ->pluck('meaning')
            ->toArray();

        $fillers = ['Học tập', 'Ăn cơm', 'Uống nước', 'Đi ngủ', 'Bạn bè', 'Trường học', 'Gia đình', 'Cảm ơn', 'Tạm biệt'];
        foreach ($fillers as $f) {
            if (count($distractors) >= 3) break;
            if ($f !== $card->meaning && !in_array($f, $distractors, true)) {
                $distractors[] = $f;
            }
        }

        $options = array_merge([$card->meaning], array_slice($distractors, 0, 3));
        shuffle($options);

        return response()->json([
            'success'   => true,
            'word_id'   => $card->id,
            'quiz_type' => 'hanzi_to_meaning',
            'question'  => "Chữ 「{$card->hanzi}」" . ($card->pinyin ? "[{$card->pinyin}]" : '') . " có nghĩa là gì?",
            'word'      => $card->hanzi,
            'pinyin'    => $card->pinyin,
            'meaning'   => $card->meaning,
            'options'   => $options,
            'correct'   => $card->meaning,
        ]);
    }

    /**
     * POST /student/pet/mini-quiz-reward — Server-authoritative XP reward for mascot mini pop quiz.
     */
    public function miniQuizReward(Request $request, \App\Services\LearningActivityService $activityService): JsonResponse
    {
        $request->validate([
            'idempotency_key' => ['nullable', 'string', 'max:191'],
            'correct'         => ['required', 'boolean'],
        ]);

        $user = $request->user();
        if (!$request->boolean('correct')) {
            return response()->json(['success' => true, 'xp_earned' => 0]);
        }

        $idempotencyKey = $request->input('idempotency_key')
            ?? ('pet_quiz_' . $user->id . '_' . now()->format('Ymd_His') . '_' . rand(1000, 9999));

        $result = $activityService->logActivity($user, 'pet_mini_quiz', [
            'idempotency_key' => $idempotencyKey,
            'meta'            => ['xp' => 2, 'source' => 'pet_floating_quiz'],
        ]);

        return response()->json([
            'success'   => true,
            'xp_earned' => $result['xp']['earned'] ?? 2,
            'total_xp'  => $result['xp']['total'] ?? 0,
        ]);
    }
}