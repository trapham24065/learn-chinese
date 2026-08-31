<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StoryProgress extends Model
{
    use HasFactory;

    protected $table = 'story_progresses';

    protected $fillable = [
        'user_id',
        'story_id',
        'is_completed',
        'quiz_score',
        'last_read_at',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'quiz_score'   => 'integer',
        'last_read_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function story(): BelongsTo
    {
        return $this->belongsTo(Story::class);
    }
}
