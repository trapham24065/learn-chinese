<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DuelRoundAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'duel_room_id',
        'round_number',
        'player_slot',
        'question_id',
        'selected_answer',
        'is_correct',
        'answer_time_seconds',
        'score_awarded',
        'idempotency_key',
        'answered_at',
    ];

    protected function casts(): array
    {
        return [
            'round_number' => 'integer',
            'player_slot' => 'integer',
            'question_id' => 'integer',
            'is_correct' => 'boolean',
            'answer_time_seconds' => 'float',
            'score_awarded' => 'integer',
            'answered_at' => 'float',
        ];
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(DuelRoom::class, 'duel_room_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class, 'question_id');
    }
}
