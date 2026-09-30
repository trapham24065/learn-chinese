<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PetMemory extends Model
{
    public $timestamps = false;

    public $fillable = [
        'user_pet_id',
        'type',
        'title',
        'description',
        'metadata',
        'created_at',
    ];

    protected $casts = [
        'metadata'   => 'array',
        'created_at' => 'datetime',
    ];

    public function userPet(): BelongsTo
    {
        return $this->belongsTo(UserPet::class);
    }
}
