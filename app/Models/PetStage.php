<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PetStage extends Model
{
    protected $fillable = [
        'pet_id',
        'stage',
        'name',
        'emoji',
        'required_exp',
        'required_mastered_vocabulary',
        'required_used_vocabulary',
        'required_reading_activities',
        'required_listening_activities',
        'dialogue_level',
        'sort_order',
    ];

    protected $casts = [
        'stage'                         => 'integer',
        'required_exp'                  => 'integer',
        'required_mastered_vocabulary'  => 'integer',
        'required_used_vocabulary'      => 'integer',
        'required_reading_activities'   => 'integer',
        'required_listening_activities' => 'integer',
    ];

    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class);
    }
}
