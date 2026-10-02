<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PetMemory extends Model
{
    public $timestamps = true;

    protected $fillable = [
        'user_pet_id',
        'type',
        'memory_key',
        'title',
        'description',
        'importance',
        'related_word_id',
        'metadata',
        'last_recalled_at',
        'recall_count',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'importance'       => 'integer',
        'recall_count'     => 'integer',
        'related_word_id'  => 'integer',
        'metadata'         => 'array',
        'last_recalled_at' => 'datetime',
        'created_at'       => 'datetime',
        'updated_at'       => 'datetime',
    ];

    public function userPet(): BelongsTo
    {
        return $this->belongsTo(UserPet::class);
    }

    public function relatedWord(): BelongsTo
    {
        return $this->belongsTo(Flashcard::class, 'related_word_id');
    }
}
