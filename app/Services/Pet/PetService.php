<?php

namespace App\Services\Pet;

use App\Models\Pet;
use App\Models\PetMemory;
use App\Models\User;
use App\Models\UserPet;

class PetService
{
    public function __construct(
        protected PetHungerService $hungerService,
    ) {}

    /**
     * Get user's active pet, refreshing hunger lazily. Returns null if no pet.
     */
    public function getActivePet(User $user): ?UserPet
    {
        $userPet = UserPet::where('user_id', $user->id)
            ->with(['pet', 'pet.stages'])
            ->first();

        if (!$userPet) {
            return null;
        }

        // Lazily apply hunger decay
        $this->hungerService->calculateAndApplyHunger($userPet);
        $userPet->refresh();

        return $userPet;
    }

    /**
     * Create the initial pet for a user (called on first visit to /pet).
     * Does NOT create if user already has a pet.
     */
    public function createInitialPet(User $user, string $petSlug = 'dragon'): UserPet
    {
        $existing = UserPet::where('user_id', $user->id)->first();
        if ($existing) {
            return $existing;
        }

        $pet = Pet::where('slug', $petSlug)->where('is_active', true)->firstOrFail();

        $userPet = UserPet::create([
            'user_id'                   => $user->id,
            'pet_id'                    => $pet->id,
            'name'                      => null,
            'stage'                     => 0,
            'exp'                       => 0,
            'total_fed_xp'              => 0,
            'hunger'                    => 100,
            'status'                    => 'active',
            'last_fed_at'               => null,
            'last_hunger_calculated_at' => now(),
            'dormant_at'                => null,
            'reset_count'               => 0,
            'best_stage'                => 0,
        ]);

        // Record first memory
        PetMemory::create([
            'user_pet_id' => $userPet->id,
            'type'        => 'hatched',
            'memory_key'  => 'hatched',
            'title'       => 'Trứng nở!',
            'description' => 'Pet của bạn vừa được tạo ra. Hãy bắt đầu học để nuôi lớn nhé!',
            'metadata'    => ['met_at' => now()->toDateString()],
            'created_at'  => now(),
        ]);

        return $userPet;
    }
}
