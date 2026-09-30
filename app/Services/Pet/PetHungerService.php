<?php

namespace App\Services\Pet;

use App\Models\UserPet;
use Illuminate\Support\Facades\DB;

class PetHungerService
{
    public const HUNGER_DECAY_PER_DAY = 20;
    public const DORMANT_GRACE_DAYS   = 3;

    /**
     * Calculate how many hunger points have decayed since last check,
     * apply to DB, and handle dormant/egg state transitions.
     * This is idempotent if called multiple times same day.
     */
    public function calculateAndApplyHunger(UserPet $userPet): void
    {
        $lastCalculated = $userPet->last_hunger_calculated_at ?? now();
        $daysElapsed = (int) $lastCalculated->diffInDays(now());

        if ($daysElapsed <= 0) {
            return; // Same day, no decay
        }

        DB::transaction(function () use ($userPet, $daysElapsed) {
            $userPet->lockForUpdate();
            $userPet->refresh();

            $newHunger = max(0, $userPet->hunger - ($daysElapsed * self::HUNGER_DECAY_PER_DAY));
            $updates = [
                'hunger'                    => $newHunger,
                'last_hunger_calculated_at' => now(),
            ];

            // State transitions
            if ($newHunger === 0 && $userPet->status === 'active') {
                $updates['status']     = 'dormant';
                $updates['dormant_at'] = now();
            } elseif ($userPet->status === 'dormant' && $userPet->dormant_at !== null) {
                $dormantDays = (int) $userPet->dormant_at->diffInDays(now());
                if ($dormantDays >= self::DORMANT_GRACE_DAYS) {
                    // Egg reset
                    $updates['status'] = 'egg';
                    $updates['stage']  = 0;
                    $updates['exp']    = 0;
                    $updates['hunger'] = 0;
                    $userPet->increment('reset_count');
                }
            }

            $userPet->update($updates);
        });
    }

    /**
     * Get the hunger state label based on current hunger value.
     */
    public function getHungerState(UserPet $userPet): string
    {
        return $userPet->getHungerState();
    }
}
