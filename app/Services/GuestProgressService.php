<?php

namespace App\Services;

use App\Models\GuestActivity;
use App\Models\GuestProgress;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GuestProgressService
{
    public const TOKEN_VERSION = 1;
    public const EXPIRATION_DAYS = 7;

    public function __construct(
        protected LearningActivityService $learningActivityService,
        protected XpService $xpService
    ) {}

    /**
     * Resolve existing pending guest progress or create a new one.
     */
    public function resolveOrCreateProgress(?string $guestUuid = null): GuestProgress
    {
        if ($guestUuid && Str::isUuid($guestUuid)) {
            $progress = GuestProgress::where('guest_uuid', $guestUuid)->first();

            if ($progress) {
                // If this guest record was already claimed or expired, start a fresh guest session
                if ($progress->isClaimed() || $progress->isExpired()) {
                    if ($progress->isExpired() && $progress->status !== GuestProgress::STATUS_EXPIRED) {
                        $progress->update(['status' => GuestProgress::STATUS_EXPIRED]);
                    }

                    return $this->createNewProgress();
                }

                return $progress;
            }
        }

        return $this->createNewProgress($guestUuid);
    }

    /**
     * Create a new guest progress record with a secure UUID.
     */
    protected function createNewProgress(?string $requestedUuid = null): GuestProgress
    {
        $uuid = ($requestedUuid && Str::isUuid($requestedUuid))
            ? $requestedUuid
            : Str::uuid()->toString();

        return GuestProgress::create([
            'guest_uuid' => $uuid,
            'total_xp' => 0,
            'activities_count' => 0,
            'status' => GuestProgress::STATUS_PENDING,
            'expires_at' => Carbon::now()->addDays(self::EXPIRATION_DAYS),
            'last_activity_at' => Carbon::now(),
        ]);
    }

    /**
     * Generate an encrypted signed token for the guest progress.
     */
    public function generateSignedToken(GuestProgress $progress): string
    {
        $payload = [
            'v' => self::TOKEN_VERSION,
            'guest_uuid' => $progress->guest_uuid,
            'issued_at' => Carbon::now()->timestamp,
            'expires_at' => $progress->expires_at->timestamp,
        ];

        return Crypt::encryptString(json_encode($payload));
    }

    /**
     * Verify and decrypt a signed guest token.
     *
     * @return array{v: int, guest_uuid: string, issued_at: int, expires_at: int}|null
     */
    public function verifySignedToken(?string $token): ?array
    {
        if (empty($token)) {
            return null;
        }

        try {
            $decrypted = Crypt::decryptString($token);
            $payload = json_decode($decrypted, true);

            if (! is_array($payload) || empty($payload['guest_uuid'])) {
                return null;
            }

            // Expiration check
            if (isset($payload['expires_at']) && Carbon::now()->timestamp > $payload['expires_at']) {
                return null;
            }

            return $payload;
        } catch (Exception) {
            return null;
        }
    }

    /**
     * Calculate valid XP for guest with daily diminishing returns for mini-games.
     */
    public function calculateGuestXp(string $guestUuid, string $activityType, array $meta = []): int
    {
        if (isset($meta['xp']) && is_numeric($meta['xp'])) {
            return min(100, max(0, (int) $meta['xp']));
        }

        if (in_array($activityType, ['fast_match', 'fast_match_completed', 'audio_quiz', 'audio_quiz_completed'])) {
            $today = Carbon::now(config('app.timezone', 'Asia/Ho_Chi_Minh'))->toDateString();
            $todayCount = GuestActivity::where('guest_uuid', $guestUuid)
                ->whereIn('activity_type', ['fast_match', 'fast_match_completed', 'audio_quiz', 'audio_quiz_completed'])
                ->whereDate('created_at', $today)
                ->count();

            if ($todayCount === 0) {
                return 15; // 1st meaningful play of day
            }
            if ($todayCount < 3) {
                return 5;  // 2nd and 3rd replay
            }

            return 0;      // 4th+ replay (anti-farming)
        }

        return XpService::XP_RULES[$activityType] ?? 1;
    }

    /**
     * Record a verified guest learning activity, update aggregate XP, and return new signed token.
     *
     * @param array{
     *     source_type?: ?string,
     *     source_id?: ?int,
     *     idempotency_key?: ?string,
     *     meta?: array
     * } $data
     */
    public function recordActivity(string $guestUuid, string $activityType, array $data = []): array
    {
        $idempotencyKey = $data['idempotency_key'] ?? null;
        $meta = $data['meta'] ?? [];
        $sourceType = $data['source_type'] ?? null;
        $sourceId = isset($data['source_id']) ? (int) $data['source_id'] : null;

        return DB::transaction(function () use ($guestUuid, $activityType, $idempotencyKey, $meta, $sourceType, $sourceId) {
            $progress = GuestProgress::where('guest_uuid', $guestUuid)
                ->lockForUpdate()
                ->first();

            if (! $progress || $progress->isClaimed() || $progress->isExpired()) {
                $progress = $this->resolveOrCreateProgress($guestUuid);
            }

            // Check idempotency per guest
            if ($idempotencyKey) {
                $existing = GuestActivity::where('guest_uuid', $progress->guest_uuid)
                    ->where('idempotency_key', $idempotencyKey)
                    ->first();

                if ($existing) {
                    return [
                        'success' => true,
                        'is_duplicate' => true,
                        'guest_uuid' => $progress->guest_uuid,
                        'xp_earned' => 0,
                        'total_xp' => (int) $progress->total_xp,
                        'activities_count' => (int) $progress->activities_count,
                        'claim_token' => $this->generateSignedToken($progress),
                        'expires_at' => $progress->expires_at->toIso8601String(),
                    ];
                }
            }

            $xpEarned = $this->calculateGuestXp($progress->guest_uuid, $activityType, $meta);

            // Record the guest activity
            GuestActivity::create([
                'guest_uuid' => $progress->guest_uuid,
                'activity_type' => $activityType,
                'source_type' => $sourceType,
                'source_id' => $sourceId,
                'xp_earned' => $xpEarned,
                'idempotency_key' => $idempotencyKey,
                'meta' => $meta,
                'occurred_at' => Carbon::now(),
            ]);

            // Update aggregate guest progress
            $progress->total_xp += $xpEarned;
            $progress->activities_count += 1;
            $progress->last_activity_at = Carbon::now();
            $progress->expires_at = Carbon::now()->addDays(self::EXPIRATION_DAYS);
            $progress->save();

            return [
                'success' => true,
                'is_duplicate' => false,
                'guest_uuid' => $progress->guest_uuid,
                'xp_earned' => $xpEarned,
                'total_xp' => (int) $progress->total_xp,
                'activities_count' => (int) $progress->activities_count,
                'claim_token' => $this->generateSignedToken($progress),
                'expires_at' => $progress->expires_at->toIso8601String(),
            ];
        });
    }

    /**
     * Claim guest progress into an authenticated user's account with strict idempotency and DB transaction.
     *
     * @return array{
     *     success: bool,
     *     already_claimed?: bool,
     *     claimed_xp?: int,
     *     activities_count?: int,
     *     total_user_xp?: int,
     *     streak?: int,
     *     message: string
     * }
     */
    public function claim(User $user, string $claimToken): array
    {
        $payload = $this->verifySignedToken($claimToken);

        if (! $payload) {
            return [
                'success' => false,
                'message' => 'Mã xác thực tiến độ học thử không hợp lệ hoặc đã hết hạn.',
            ];
        }

        $guestUuid = $payload['guest_uuid'];

        return DB::transaction(function () use ($user, $guestUuid) {
            $progress = GuestProgress::where('guest_uuid', $guestUuid)
                ->lockForUpdate()
                ->first();

            if (! $progress) {
                return [
                    'success' => false,
                    'message' => 'Không tìm thấy dữ liệu tiến độ học thử.',
                ];
            }

            // 1. Idempotency Guard: Check if already claimed
            if ($progress->isClaimed()) {
                if ($progress->claimed_by_user_id === $user->id) {
                    $userStats = $user->learningStats;

                    return [
                        'success' => true,
                        'already_claimed' => true,
                        'claimed_xp' => (int) $progress->total_xp,
                        'activities_count' => (int) $progress->activities_count,
                        'total_user_xp' => (int) ($userStats?->total_xp ?? 0),
                        'streak' => (int) ($userStats?->current_streak ?? 1),
                        'message' => 'Tiến độ học thử này đã được cộng vào tài khoản của bạn trước đó.',
                    ];
                }

                return [
                    'success' => false,
                    'message' => 'Tiến độ học thử này đã được liên kết với một tài khoản khác.',
                ];
            }

            // 2. Expiration Guard
            if ($progress->isExpired()) {
                $progress->update(['status' => GuestProgress::STATUS_EXPIRED]);

                return [
                    'success' => false,
                    'message' => 'Tiến độ học thử đã hết hạn bảo lưu (quá hạn 7 ngày).',
                ];
            }

            // 3. Process and import all guest activities
            $activities = GuestActivity::where('guest_uuid', $guestUuid)
                ->orderBy('id', 'asc')
                ->get();

            $importedCount = 0;
            foreach ($activities as $activity) {
                $this->learningActivityService->logActivity($user, $activity->activity_type, [
                    'source_type' => $activity->source_type ?? 'guest_claim',
                    'source_id' => $activity->source_id,
                    'idempotency_key' => 'claim_' . $guestUuid . '_' . ($activity->idempotency_key ?? $activity->id),
                    'meta' => array_merge($activity->meta ?? [], [
                        'claimed_from_guest_uuid' => $guestUuid,
                        'original_occurred_at' => $activity->occurred_at?->toIso8601String() ?? $activity->created_at?->toIso8601String(),
                        'xp' => $activity->xp_earned, // preserve exact validated XP calculated at game time
                    ]),
                ]);
                $importedCount++;
            }

            // 4. Mark guest progress as claimed
            $progress->update([
                'status' => GuestProgress::STATUS_CLAIMED,
                'claimed_by_user_id' => $user->id,
                'claimed_at' => Carbon::now(),
            ]);

            $userStats = $user->learningStats()->first();

            return [
                'success' => true,
                'already_claimed' => false,
                'claimed_xp' => (int) $progress->total_xp,
                'activities_count' => (int) $progress->activities_count,
                'total_user_xp' => (int) ($userStats?->total_xp ?? 0),
                'streak' => (int) ($userStats?->current_streak ?? 1),
                'message' => 'Chúc mừng! Bạn đã giữ lại thành công ' . $progress->total_xp . ' XP vào tài khoản chính thức.',
            ];
        });
    }
}
