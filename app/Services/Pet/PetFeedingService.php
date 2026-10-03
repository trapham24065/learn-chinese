<?php

namespace App\Services\Pet;

use App\Models\PetFeedingLog;
use App\Models\User;
use App\Models\UserPet;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PetFeedingService
{
    public const DAILY_FEED_CAPACITY = 100;
    public const ALLOWED_AMOUNTS     = [5, 10, 20, 50];

    public const FOOD_ITEMS = [
        5 => [
            'amount'      => 5,
            'key'         => 'apple',
            'hanzi'       => '苹果',
            'pinyin'      => 'píngguǒ',
            'name'        => 'Quả táo tươi',
            'emoji'       => '🍎',
            'xp'          => 5,
            'description' => 'Táo đỏ giòn ngọt, ăn nhẹ thanh đạm.',
            'reaction'    => '咔嚓！脆脆的，真甜！(Giòn tan, ngọt lịm!)',
            'chinese_say' => '好吃！谢谢你！',
        ],
        10 => [
            'amount'      => 10,
            'key'         => 'dumpling',
            'hanzi'       => '饺子',
            'pinyin'      => 'jiǎozi',
            'name'        => 'Há cảo nóng hổi',
            'emoji'       => '🥟',
            'xp'          => 10,
            'description' => 'Bánh chẻo hấp nhân thịt thơm phức.',
            'reaction'    => '哇！香喷喷的饺子！好吃！(Oa! Há cảo thơm phức, ngon tuyệt!)',
            'chinese_say' => '哇！饺子真香！好吃！',
        ],
        20 => [
            'amount'      => 20,
            'key'         => 'rice',
            'hanzi'       => '米饭',
            'pinyin'      => 'mǐfàn',
            'name'        => 'Bát cơm dẻo',
            'emoji'       => '🍚',
            'xp'          => 20,
            'description' => 'Cơm trắng dẻo thơm no bụng chắc dạ.',
            'reaction'    => '肚子饱饱的，充满力气了！(No bụng rồi, tràn đầy sức lực!)',
            'chinese_say' => '吃饱了，力气满满！',
        ],
        50 => [
            'amount'      => 50,
            'key'         => 'peach',
            'hanzi'       => '桃子',
            'pinyin'      => 'táozi',
            'name'        => 'Đào tiên ngọt lịm',
            'emoji'       => '🍑',
            'xp'          => 50,
            'description' => 'Trái đào tiên mọng nước bồi bổ sinh lực.',
            'reaction'    => '太美味了！谢谢你带来这么棒的仙桃！(Ngon xuất sắc! Cảm ơn cậu đã mang quả đào tiên này!)',
            'chinese_say' => '太美味了！真是极品仙桃！',
        ],
    ];

    public static function getFoodMenu(): array
    {
        return array_values(self::FOOD_ITEMS);
    }

    public function __construct(
        protected PetGrowthService $growthService,
        protected PetAffinityService $affinityService,
    ) {}

    /**
     * Get how much XP has been fed to the pet today.
     */
    public function getDailyFedAmount(UserPet $userPet): int
    {
        $today = Carbon::now(config('app.timezone', 'Asia/Ho_Chi_Minh'))->toDateString();

        return (int) PetFeedingLog::where('user_pet_id', $userPet->id)
            ->whereDate('fed_at', $today)
            ->sum('xp_amount');
    }

    /**
     * Get how much more XP the pet can receive today.
     */
    public function getRemainingDailyCapacity(UserPet $userPet): int
    {
        return max(0, self::DAILY_FEED_CAPACITY - $this->getDailyFedAmount($userPet));
    }

    /**
     * Feed the pet. Returns result array.
     *
     * @throws \InvalidArgumentException on invalid amount
     * @throws \RuntimeException on business rule violation
     */
    public function feed(User $user, UserPet $userPet, int $amount, string $idempotencyKey): array
    {
        // 1. Validate amount is one of the allowed buttons
        if (!in_array($amount, self::ALLOWED_AMOUNTS, true)) {
            throw new \InvalidArgumentException(
                "Invalid feed amount: {$amount}. Allowed: " . implode(', ', self::ALLOWED_AMOUNTS)
            );
        }

        // 2. Idempotency check (before lock)
        if (PetFeedingLog::where('idempotency_key', $idempotencyKey)->exists()) {
            return [
                'success'      => true,
                'is_duplicate' => true,
                'xp_fed'       => 0,
                'hunger'       => $userPet->hunger,
            ];
        }

        return DB::transaction(function () use ($user, $userPet, $amount, $idempotencyKey) {
            $userPet->lockForUpdate();
            $userPet->refresh();

            // 3. Must be active to feed
            if (!$userPet->isActive()) {
                throw new \RuntimeException('Pet is not active. Cannot feed a dormant or egg pet.');
            }

            // 4. Check daily cap
            $dailyFed  = $this->getDailyFedAmount($userPet);
            $remaining = self::DAILY_FEED_CAPACITY - $dailyFed;

            if ($remaining <= 0) {
                throw new \RuntimeException('Daily feeding capacity reached (100 XP/day).');
            }

            // 5. Cap amount at remaining capacity
            $actualAmount = min($amount, $remaining);

            // 6. Apply hunger increase (max 100)
            $newHunger = min(100, $userPet->hunger + $actualAmount);

            // 7. Persist feeding log
            PetFeedingLog::create([
                'user_pet_id'      => $userPet->id,
                'user_id'          => $user->id,
                'xp_amount'        => $actualAmount,
                'daily_fed_before' => $dailyFed,
                'idempotency_key'  => $idempotencyKey,
                'fed_at'           => now(),
            ]);

            // 8. Update pet hunger + totals
            $userPet->update([
                'hunger'       => $newHunger,
                'total_fed_xp' => $userPet->total_fed_xp + $actualAmount,
                'last_fed_at'  => now(),
            ]);

            // 9. Add EXP and check for stage-up
            $growthResult = $this->growthService->feedExp($user, $userPet, $actualAmount);

            // 10. Award affinity for caretaking (capped daily)
            $affinityResult = $this->affinityService->addAffinity($userPet, 1, 'feeding');

            // 11. Record first feeding memory if this is the pet's first meal
            if (isset(self::FOOD_ITEMS[$actualAmount]) && PetFeedingLog::where('user_pet_id', $userPet->id)->count() === 1) {
                app(PetMemoryService::class)->rememberFirstFeeding($userPet, self::FOOD_ITEMS[$actualAmount]);
            }

            return [
                'success'         => true,
                'is_duplicate'    => false,
                'xp_fed'          => $actualAmount,
                'xp_requested'    => $amount,
                'was_capped'      => $actualAmount < $amount,
                'food'            => self::FOOD_ITEMS[$actualAmount] ?? null,
                'food_reaction'   => self::FOOD_ITEMS[$actualAmount]['reaction'] ?? '好吃！',
                'chinese_say'     => self::FOOD_ITEMS[$actualAmount]['chinese_say'] ?? '好吃！',
                'hunger'          => $userPet->fresh()->hunger,
                'hunger_state'    => $userPet->fresh()->getHungerState(),
                'daily_fed'       => $dailyFed + $actualAmount,
                'daily_remaining' => max(0, $remaining - $actualAmount),
                'stage_up'        => $growthResult['stage_up'],
                'new_stage'       => $growthResult['new_stage'] ?? null,
                'pet_exp'         => $growthResult['exp'],
                'affinity'        => $affinityResult['affinity'],
                'tier_up'         => $affinityResult['tier_up'],
                'tier'            => $affinityResult['tier'],
            ];
        });
    }
}
