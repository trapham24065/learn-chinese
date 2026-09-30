<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $guest_uuid
 * @property int $total_xp
 * @property int $activities_count
 * @property string $status
 * @property int|null $claimed_by_user_id
 * @property Carbon|null $claimed_at
 * @property Carbon $expires_at
 * @property Carbon|null $last_activity_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class GuestProgress extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_CLAIMED = 'claimed';
    public const STATUS_EXPIRED = 'expired';

    protected $table = 'guest_progress';

    protected $fillable = [
        'guest_uuid',
        'total_xp',
        'activities_count',
        'status',
        'claimed_by_user_id',
        'claimed_at',
        'expires_at',
        'last_activity_at',
    ];

    protected $casts = [
        'total_xp' => 'integer',
        'activities_count' => 'integer',
        'claimed_at' => 'datetime',
        'expires_at' => 'datetime',
        'last_activity_at' => 'datetime',
    ];

    public function activities(): HasMany
    {
        return $this->hasMany(GuestActivity::class, 'guest_uuid', 'guest_uuid');
    }

    public function claimedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'claimed_by_user_id');
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING && ! $this->isExpired();
    }

    public function isClaimed(): bool
    {
        return $this->status === self::STATUS_CLAIMED;
    }

    public function isExpired(): bool
    {
        return $this->status === self::STATUS_EXPIRED || ($this->expires_at && Carbon::now()->greaterThan($this->expires_at));
    }
}
