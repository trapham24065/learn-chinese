<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $guest_uuid
 * @property string $activity_type
 * @property string|null $source_type
 * @property int|null $source_id
 * @property int $xp_earned
 * @property string|null $idempotency_key
 * @property array|null $meta
 * @property Carbon|null $occurred_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class GuestActivity extends Model
{
    protected $table = 'guest_activities';

    protected $fillable = [
        'guest_uuid',
        'activity_type',
        'source_type',
        'source_id',
        'xp_earned',
        'idempotency_key',
        'meta',
        'occurred_at',
    ];

    protected $casts = [
        'xp_earned' => 'integer',
        'meta' => 'array',
        'occurred_at' => 'datetime',
    ];

    public function progress(): BelongsTo
    {
        return $this->belongsTo(GuestProgress::class, 'guest_uuid', 'guest_uuid');
    }
}
