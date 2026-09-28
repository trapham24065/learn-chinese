<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserDailyProgress extends Model
{
    protected $table = 'user_daily_progress';

    protected $fillable = [
        'user_id',
        'date',
        'flashcards_count',
        'quiz_count',
        'reading_minutes',
        'pinyin_count',
        'xp_earned',
        'is_goal_completed',
    ];

    protected $casts = [
        'date' => 'string',
        'flashcards_count' => 'integer',
        'quiz_count' => 'integer',
        'reading_minutes' => 'integer',
        'pinyin_count' => 'integer',
        'xp_earned' => 'integer',
        'is_goal_completed' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
