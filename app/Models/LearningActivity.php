<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LearningActivity extends Model
{
    protected $fillable = [
        'user_id',
        'activity_type',
        'source_type',
        'source_id',
        'xp_earned',
        'idempotency_key',
        'meta',
    ];

    protected $casts = [
        'xp_earned' => 'integer',
        'meta' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
