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
    ];

    protected $casts = [
        'next_review_at' => 'datetime',
        'ease_factor' => 'float',
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
