<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PetWorldObject extends Model
{
    protected $fillable = [
        'user_pet_id', 'flashcard_id', 'hanzi', 'object_type',
        'emoji', 'object_key', 'pos_x', 'pos_y', 'size',
        'is_visible', 'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_visible' => 'boolean',
        'pos_x' => 'integer',
        'pos_y' => 'integer',
        'size' => 'integer',
    ];

    public function userPet(): BelongsTo
    {
        return $this->belongsTo(UserPet::class);
    }

    public function flashcard(): BelongsTo
    {
        return $this->belongsTo(Flashcard::class);
    }
}
