<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PetFeedingLog extends Model
{
    protected $fillable = [
        'user_pet_id',
        'user_id',
        'xp_amount',
        'daily_fed_before',
        'idempotency_key',
        'fed_at',
    ];

    protected $casts = [
        'xp_amount'        => 'integer',
        'daily_fed_before' => 'integer',
        'fed_at'           => 'datetime',
    ];

    public function userPet(): BelongsTo
    {
        return $this->belongsTo(UserPet::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
