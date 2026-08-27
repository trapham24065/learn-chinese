<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FlashcardProgress extends Model
{
    protected $table = 'flashcard_progresses';

    protected $fillable = [
        'user_id',
        'flashcard_id',
        'repetition',
        'ease_factor',
        'interval',
        'next_review_at',
        'is_starred',
    ];

    protected $casts = [
        'next_review_at' => 'datetime',
        'ease_factor' => 'float',
        'is_starred' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function flashcard(): BelongsTo
    {
        return $this->belongsTo(Flashcard::class);
    }
}
