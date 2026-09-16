<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RadicalCharacter extends Model
{
    use HasFactory;

    protected $fillable = [
        'radical_id',
        'character',
        'pinyin',
        'meaning_vi',
        'hsk_level',
        'flashcard_id',
        'is_featured',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'hsk_level'    => 'integer',
            'is_featured'  => 'boolean',
            'sort_order'   => 'integer',
        ];
    }

    /**
     * Parent radical.
     */
    public function radical(): BelongsTo
    {
        return $this->belongsTo(Radical::class);
    }

    /**
     * Corresponding flashcard in the curriculum (if matched).
     */
    public function flashcard(): BelongsTo
    {
        return $this->belongsTo(Flashcard::class);
    }

    /**
     * Scope: Filter by HSK level.
     */
    public function scopeByHsk(Builder $query, int $level): Builder
    {
        return $query->where('hsk_level', $level);
    }

    /**
     * Scope: Only featured characters.
     */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }
}
