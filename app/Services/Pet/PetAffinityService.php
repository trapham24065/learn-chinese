<?php

namespace App\Services\Pet;

use App\Models\PetMemory;
use App\Models\UserPet;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class PetAffinityService
{
    /**
     * Daily caps for different affinity earning activities.
     */
    public const DAILY_CAPS = [
        'vocab_mastered'   => 5, // 1 per word mastered, max 5/day
        'study_session'    => 4, // 2 per session, max 2 sessions (4/day)
        'daily_goal'       => 3, // 3 once per day
        'streak_milestone' => 2, // 2 per milestone
        'feeding'          => 2, // 1 per feed, max 2/day
        'evolution'        => 10, // Evolution bonus
    ];

    /**
     * Add affinity to a UserPet, respecting daily caps and detecting tier-ups.
     *
     * @param UserPet $userPet
     * @param int $amount
     * @param string $reason
     * @param array $meta
     * @return array
     */
    public function addAffinity(UserPet $userPet, int $amount, string $reason = 'general', array $meta = []): array
    {
        if ($amount <= 0) {
            return [
                'added'    => 0,
                'affinity' => (int) ($userPet->affinity ?? 0),
                'tier_up'  => false,
                'reason'   => $reason,
                'tier'     => $userPet->getAffinityTier(),
            ];
        }

        $today = Carbon::now(config('app.timezone', 'Asia/Ho_Chi_Minh'))->toDateString();
        $cacheKey = "pet_affinity:daily:{$userPet->id}:{$reason}:{$today}";

        // Calculate allowed amount based on daily cap
        $dailyCap = self::DAILY_CAPS[$reason] ?? null;
        $actualAdded = $amount;

        if ($dailyCap !== null) {
            $alreadyAwarded = (int) Cache::get($cacheKey, 0);
            $remaining = max(0, $dailyCap - $alreadyAwarded);

            if ($remaining <= 0) {
                return [
                    'added'       => 0,
                    'affinity'    => (int) ($userPet->affinity ?? 0),
                    'tier_up'     => false,
                    'daily_capped'=> true,
                    'reason'      => $reason,
                    'tier'        => $userPet->getAffinityTier(),
                ];
            }

            $actualAdded = min($amount, $remaining);
            Cache::put($cacheKey, $alreadyAwarded + $actualAdded, now()->endOfDay());
        }

        $oldTierInfo = $userPet->getAffinityTier();
        $currentAffinity = (int) ($userPet->affinity ?? 0);
        $newAffinity = min(100, $currentAffinity + $actualAdded);

        $userPet->update(['affinity' => $newAffinity]);
        $userPet->refresh();

        $newTierInfo = $userPet->getAffinityTier();
        $tierUp = ($oldTierInfo['tier'] !== $newTierInfo['tier']) && ($newAffinity > $currentAffinity);

        if ($tierUp) {
            PetMemory::create([
                'user_pet_id' => $userPet->id,
                'type'        => 'affinity_tier_up',
                'memory_key'  => "affinity_tier_{$newTierInfo['tier']}",
                'title'       => "Mối quan hệ tiến triển: {$newTierInfo['name']} {$newTierInfo['emoji']}",
                'description' => "Độ thân thiết giữa bạn và Pet đã đạt {$newAffinity}/100! {$newTierInfo['description']}",
                'importance'  => 85,
                'metadata'    => array_merge($meta, [
                    'old_tier'     => $oldTierInfo['tier'],
                    'new_tier'     => $newTierInfo['tier'],
                    'old_affinity' => $currentAffinity,
                    'new_affinity' => $newAffinity,
                ]),
                'created_at'  => now(),
            ]);
        }

        return [
            'added'        => $actualAdded,
            'affinity'     => $newAffinity,
            'tier_up'      => $tierUp,
            'old_tier'     => $oldTierInfo,
            'tier'         => $newTierInfo,
            'reason'       => $reason,
            'daily_capped' => false,
        ];
    }

    /**
     * Get a comprehensive summary of relationship status.
     */
    public function getAffinitySummary(UserPet $userPet): array
    {
        $tier = $userPet->getAffinityTier();
        $val = max(0, min(100, $userPet->affinity ?? 0));

        return [
            'affinity'       => $val,
            'tier'           => $tier['tier'],
            'tier_name'      => $tier['name'],
            'tier_title'     => $tier['title'],
            'tier_emoji'     => $tier['emoji'],
            'tier_color'     => $tier['color'],
            'tier_progress'  => $tier['progress'],
            'tier_desc'      => $tier['description'],
            'personality'    => [
                'type'        => $userPet->personality ?? 'playful',
                'label'       => $userPet->getPersonalityLabel(),
                'emoji'       => $userPet->getPersonalityEmoji(),
                'description' => $userPet->getPersonalityDescription(),
            ],
            'study_sessions' => (int) ($userPet->study_session_count ?? 0),
            'last_studied_at'=> $userPet->last_studied_at?->diffForHumans(),
        ];
    }
}
