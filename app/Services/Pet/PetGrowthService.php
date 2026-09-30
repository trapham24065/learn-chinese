<?php

namespace App\Services\Pet;

use App\Models\FlashcardProgress;
use App\Models\LearningActivity;
use App\Models\PetMemory;
use App\Models\PetStage;
use App\Models\User;
use App\Models\UserPet;

class PetGrowthService
{
    /**
     * Add EXP to pet from feeding and check for stage-up.
     * NOTE: total_xp on user_learning_stats is NOT modified here.
     */
    public function feedExp(User $user, UserPet $userPet, int $expAmount): array
    {
        $userPet->increment('exp', $expAmount);
        $userPet->refresh();

        return $this->checkAndApplyStageUp($user, $userPet);
    }

    /**
     * Check if the pet meets requirements for next stage and apply if so.
     */
    public function checkAndApplyStageUp(User $user, UserPet $userPet): array
    {
        $nextStageNumber = $userPet->stage + 1;
        $nextStage = PetStage::where('pet_id', $userPet->pet_id)
            ->where('stage', $nextStageNumber)
            ->first();

        if (!$nextStage) {
            return ['stage_up' => false, 'exp' => $userPet->exp];
        }

        if (!$this->meetsStageRequirements($user, $userPet, $nextStage)) {
            return ['stage_up' => false, 'exp' => $userPet->exp];
        }

        // Apply stage up
        $oldStage = $userPet->stage;
        $userPet->update([
            'stage'      => $nextStageNumber,
            'best_stage' => max($userPet->best_stage, $nextStageNumber),
        ]);

        // Record memory milestone
        PetMemory::create([
            'user_pet_id' => $userPet->id,
            'type'        => 'stage_reached',
            'title'       => "Tiến hóa lên giai đoạn {$nextStageNumber}!",
            'description' => "Pet của bạn đã tiến hóa thành {$nextStage->name} {$nextStage->emoji}",
            'metadata'    => ['from_stage' => $oldStage, 'to_stage' => $nextStageNumber, 'exp' => $userPet->exp],
            'created_at'  => now(),
        ]);

        return [
            'stage_up'   => true,
            'new_stage'  => $nextStageNumber,
            'exp'        => $userPet->exp,
            'stage_name' => $nextStage->name,
            'emoji'      => $nextStage->emoji,
        ];
    }

    /**
     * Check if a UserPet meets all requirements to reach $targetStage.
     */
    public function meetsStageRequirements(User $user, UserPet $userPet, PetStage $targetStage): bool
    {
        // EXP requirement
        if ($userPet->exp < $targetStage->required_exp) {
            return false;
        }

        // Mastered vocabulary (repetition >= 2)
        if ($targetStage->required_mastered_vocabulary > 0) {
            $masteredCount = FlashcardProgress::where('user_id', $user->id)
                ->where('repetition', '>=', 2)
                ->count();
            if ($masteredCount < $targetStage->required_mastered_vocabulary) {
                return false;
            }
        }

        // Used vocabulary (repetition >= 3 as proxy for "actively used")
        if ($targetStage->required_used_vocabulary > 0) {
            $usedCount = FlashcardProgress::where('user_id', $user->id)
                ->where('repetition', '>=', 3)
                ->count();
            if ($usedCount < $targetStage->required_used_vocabulary) {
                return false;
            }
        }

        // Reading activities
        if ($targetStage->required_reading_activities > 0) {
            $readingCount = LearningActivity::where('user_id', $user->id)
                ->whereIn('activity_type', ['reading_completed'])
                ->count();
            if ($readingCount < $targetStage->required_reading_activities) {
                return false;
            }
        }

        // Listening activities
        if ($targetStage->required_listening_activities > 0) {
            $listeningCount = LearningActivity::where('user_id', $user->id)
                ->whereIn('activity_type', ['audio_quiz', 'audio_quiz_completed'])
                ->count();
            if ($listeningCount < $targetStage->required_listening_activities) {
                return false;
            }
        }

        return true;
    }

    /**
     * Calculate EXP progress toward the next stage.
     */
    public function calculateExpProgress(UserPet $userPet): array
    {
        $currentStage = PetStage::where('pet_id', $userPet->pet_id)
            ->where('stage', $userPet->stage)
            ->first();

        $nextStage = PetStage::where('pet_id', $userPet->pet_id)
            ->where('stage', $userPet->stage + 1)
            ->first();

        if (!$nextStage) {
            return [
                'current_exp'  => $userPet->exp,
                'required_exp' => null,
                'percent'      => 100,
                'is_max'       => true,
            ];
        }

        $stageBaseExp = $currentStage?->required_exp ?? 0;
        $expInStage   = $userPet->exp - $stageBaseExp;
        $expNeeded    = $nextStage->required_exp - $stageBaseExp;
        $percent      = $expNeeded > 0 ? min(100, round(($expInStage / $expNeeded) * 100)) : 100;

        return [
            'current_exp'  => $userPet->exp,
            'stage_exp'    => max(0, $expInStage),
            'required_exp' => $nextStage->required_exp,
            'exp_in_stage' => max(0, $expInStage),
            'exp_needed'   => max(0, $expNeeded - $expInStage),
            'percent'      => $percent,
            'is_max'       => false,
        ];
    }
}
