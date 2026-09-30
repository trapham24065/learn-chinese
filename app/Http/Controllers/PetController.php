<?php

namespace App\Http\Controllers;

use App\Services\Pet\PetFeedingService;
use App\Services\Pet\PetGrowthService;
use App\Services\Pet\PetService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PetController extends Controller
{
    public function __construct(
        protected PetService $petService,
        protected PetFeedingService $feedingService,
        protected PetGrowthService $growthService,
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
        $memories       = $userPet->memories()->latest('created_at')->limit(10)->get();

        return view('pet.index', compact('userPet', 'progress', 'dailyFed', 'dailyRemaining', 'memories'));
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

            return response()->json($result);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        } catch (\RuntimeException $e) {
            return response()->json(['error' => $e->getMessage()], 409);
        }
    }

    /**
     * GET /student/pet/status — JSON status for AJAX polling (pet card on dashboard).
     */
    public function status(Request $request): JsonResponse
    {
        $user    = $request->user();
        $userPet = $this->petService->getActivePet($user);

        if (!$userPet) {
            return response()->json(['has_pet' => false]);
        }

        return response()->json([
            'has_pet'         => true,
            'name'            => $userPet->name ?? $userPet->pet->name,
            'stage'           => $userPet->stage,
            'emoji'           => optional($userPet->pet->stages->where('stage', $userPet->stage)->first())->emoji,
            'hunger'          => $userPet->hunger,
            'hunger_state'    => $userPet->getHungerState(),
            'status'          => $userPet->status,
            'exp'             => $userPet->exp,
            'daily_remaining' => $this->feedingService->getRemainingDailyCapacity($userPet),
            'progress'        => $this->growthService->calculateExpProgress($userPet),
        ]);
    }
}
