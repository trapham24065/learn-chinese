<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserPet extends Model
{
    protected $fillable = [
        'user_id',
        'pet_id',
        'name',
        'stage',
        'exp',
        'total_fed_xp',
        'hunger',
        'status',
        'last_fed_at',
        'last_hunger_calculated_at',
        'dormant_at',
        'reset_count',
        'best_stage',
    ];

    protected $casts = [
        'stage'                     => 'integer',
        'exp'                       => 'integer',
        'total_fed_xp'              => 'integer',
        'hunger'                    => 'integer',
        'last_fed_at'               => 'datetime',
        'last_hunger_calculated_at' => 'datetime',
        'dormant_at'                => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class);
    }

    public function feedingLogs(): HasMany
    {
        return $this->hasMany(PetFeedingLog::class);
    }

    public function memories(): HasMany
    {
        return $this->hasMany(PetMemory::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isDormant(): bool
    {
        return $this->status === 'dormant';
    }

    public function isEgg(): bool
    {
        return $this->status === 'egg';
    }

    public function getHungerState(): string
    {
        return match (true) {
            $this->hunger >= 70 => 'happy',
            $this->hunger >= 40 => 'hungry',
            $this->hunger >= 20 => 'very_hungry',
            $this->hunger >= 1  => 'weak',
            default             => 'dormant',
        };
    }
}
