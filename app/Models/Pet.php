<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pet extends Model
{
    protected $fillable = ['slug', 'name', 'description', 'image_data', 'is_active'];

    protected $casts = [
        'image_data' => 'array',
        'is_active'  => 'boolean',
    ];

    public function stages(): HasMany
    {
        return $this->hasMany(PetStage::class)->orderBy('stage');
    }

    public function userPets(): HasMany
    {
        return $this->hasMany(UserPet::class);
    }
}
