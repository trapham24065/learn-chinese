<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Radical extends Model
{
    use HasFactory;

    protected $fillable = [
        'radical_number',
        'character',
        'variants',
        'display_character',
        'slug',
        'name_vi',
        'pinyin',
        'meaning_vi',
        'stroke_count',
        'position',
        'position_desc',
        'description',
        'mnemonic',
        'is_common',
        'common_rank',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'variants'       => 'array',
            'is_common'      => 'boolean',
            'stroke_count'   => 'integer',
            'radical_number' => 'integer',
            'common_rank'    => 'integer',
            'sort_order'     => 'integer',
        ];
    }

    /**
     * Characters belonging to this radical.
     */
    public function characters(): HasMany
    {
        return $this->hasMany(RadicalCharacter::class)->orderBy('is_featured', 'desc')->orderBy('sort_order')->orderBy('id');
    }

    /**
     * Featured characters for preview.
     */
    public function featuredCharacters(): HasMany
    {
        return $this->characters()->where('is_featured', true);
    }

    /**
     * Scope: Filter by common / featured radicals.
     */
    public function scopeCommon(Builder $query): Builder
    {
        return $query->where('is_common', true)->orderBy('common_rank');
    }

    /**
     * Scope: Filter by stroke count (1..17).
     */
    public function scopeByStrokes(Builder $query, int $strokes): Builder
    {
        return $query->where('stroke_count', $strokes);
    }

    /**
     * Scope: Filter by position.
     */
    public function scopeByPosition(Builder $query, string $position): Builder
    {
        return $query->where('position', $position);
    }

    /**
     * Scope: Search radicals by Hanzi, Sino-Vietnamese name, pinyin, or meaning.
     */
    public function scopeSearch(Builder $query, string $search): Builder
    {
        $search = trim($search);
        if ($search === '') {
            return $query;
        }

        return $query->where(function (Builder $q) use ($search) {
            $q->where('character', $search)
              ->orWhere('display_character', $search)
              ->orWhere('name_vi', 'like', "%{$search}%")
              ->orWhere('pinyin', 'like', "%{$search}%")
              ->orWhere('meaning_vi', 'like', "%{$search}%");
        });
    }

    /**
     * Human-readable Vietnamese label for position.
     */
    public function getPositionBadgeLabelAttribute(): string
    {
        return match ($this->position) {
            'left'       => 'Bên trái (左)',
            'right'      => 'Bên phải (右)',
            'top'        => 'Ở trên (上)',
            'bottom'     => 'Ở dưới (下)',
            'surround'   => 'Bao quanh (包)',
            'inside'     => 'Bên trong (中)',
            'standalone' => 'Độc thể (独)',
            default      => 'Khác (变)',
        };
    }
}
