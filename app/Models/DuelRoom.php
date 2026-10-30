<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DuelRoom extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_code',
        'hsk_level',
        'mode',
        'status',
        'parent_room_id',
        'rematch_room_id',
        'rematch_requested_by',
        'rematch_rejected',
        'player1_type',
        'player1_id',
        'player1_guest_token_hash',
        'player1_name',
        'player1_avatar',
        'player1_ready',
        'player1_score',
        'player1_correct_count',
        'player1_total_correct_time',
        'player1_last_seen_at',
        'player2_type',
        'player2_id',
        'player2_guest_token_hash',
        'player2_name',
        'player2_avatar',
        'player2_ready',
        'player2_score',
        'player2_correct_count',
        'player2_total_correct_time',
        'player2_last_seen_at',
        'current_round',
        'round_phase',
        'phase_started_at',
        'phase_ends_at',
        'questions_snapshot',
        'winner_slot',
        'is_draw',
        'forfeit_by_slot',
        'xp_awarded',
        'started_at',
        'finished_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'hsk_level' => 'integer',
            'player1_ready' => 'boolean',
            'player1_score' => 'integer',
            'player1_correct_count' => 'integer',
            'player1_total_correct_time' => 'float',
            'player1_last_seen_at' => 'float',
            'player2_ready' => 'boolean',
            'player2_score' => 'integer',
            'player2_correct_count' => 'integer',
            'player2_total_correct_time' => 'float',
            'player2_last_seen_at' => 'float',
            'current_round' => 'integer',
            'phase_started_at' => 'float',
            'phase_ends_at' => 'float',
            'questions_snapshot' => 'array',
            'winner_slot' => 'integer',
            'is_draw' => 'boolean',
            'forfeit_by_slot' => 'integer',
            'xp_awarded' => 'boolean',
            'rematch_requested_by' => 'integer',
            'rematch_rejected' => 'boolean',
            'started_at' => 'datetime',
            'finished_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function player1(): BelongsTo
    {
        return $this->belongsTo(User::class, 'player1_id');
    }

    public function player2(): BelongsTo
    {
        return $this->belongsTo(User::class, 'player2_id');
    }

    public function parentRoom(): BelongsTo
    {
        return $this->belongsTo(DuelRoom::class, 'parent_room_id');
    }

    public function rematchRoom(): BelongsTo
    {
        return $this->belongsTo(DuelRoom::class, 'rematch_room_id');
    }

    public function roundAnswers(): HasMany
    {
        return $this->hasMany(DuelRoundAnswer::class, 'duel_room_id');
    }
}
