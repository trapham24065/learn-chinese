<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vocabulary extends Model
{
    use HasFactory;

    protected $fillable = [
        'hanzi',
        'pinyin',
        'meaning',
        'simplified',
        'traditional',
        'part_of_speech',
        'example',
        'example_pinyin',
        'example_meaning',
        'audio_url',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function hskLevels(): HasMany
    {
        return $this->hasMany(VocabularyHskLevel::class, 'vocabulary_id');
    }

    public function standards(): BelongsToMany
    {
        return $this->belongsToMany(HskStandard::class, 'vocabulary_hsk_levels', 'vocabulary_id', 'hsk_standard_id')
            ->withPivot(['level', 'source', 'topic', 'sort_order'])
            ->withTimestamps();
    }

    public function flashcards(): HasMany
    {
        return $this->hasMany(Flashcard::class, 'vocabulary_id');
    }

    /**
     * Get level for a given standard code (e.g. 'hsk_2_0' or 'hsk_3_0_2026').
     */
    public function hskLevelFor(string $standardCode): ?int
    {
        // If relation is already loaded, avoid query
        if ($this->relationLoaded('hskLevels')) {
            $match = $this->hskLevels->first(function ($hl) use ($standardCode) {
                return $hl->standard?->code === $standardCode || $hl->hsk_standard_id == $standardCode;
            });
            return $match?->level;
        }

        return $this->hskLevels()
            ->whereHas('standard', fn(Builder $q) => $q->where('code', $standardCode))
            ->value('level');
    }

    public function hsk2Level(): ?int
    {
        return $this->hskLevelFor(HskStandard::CODE_HSK_2_0);
    }

    public function hsk3Level(): ?int
    {
        return $this->hskLevelFor(HskStandard::CODE_HSK_3_0_2026)
            ?? $this->hskLevelFor(HskStandard::CODE_HSK_3_0_2021);
    }

    /**
     * Returns an array of standard code => level mappings.
     * e.g. ['hsk_2_0' => 3, 'hsk_3_0_2026' => 2]
     */
    public function allHskLevels(): array
    {
        $levels = $this->relationLoaded('hskLevels')
            ? $this->hskLevels
            : $this->hskLevels()->with('standard')->get();

        $result = [];
        foreach ($levels as $item) {
            $code = $item->standard?->code ?? "standard_{$item->hsk_standard_id}";
            $result[$code] = $item->level;
        }
        return $result;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeStandardLevel($query, string $standardCode, int $level)
    {
        return $query->whereHas('hskLevels', function (Builder $q) use ($standardCode, $level) {
            $q->where('level', $level)
              ->whereHas('standard', fn(Builder $sq) => $sq->where('code', $standardCode));
        });
    }

    public function scopeSearch($query, string $term)
    {
        $term = trim($term);
        if (empty($term)) return $query;

        return $query->where(function (Builder $q) use ($term) {
            $q->where('hanzi', 'like', "%{$term}%")
              ->orWhere('pinyin', 'like', "%{$term}%")
              ->orWhere('meaning', 'like', "%{$term}%");
        });
    }

    /**
     * Linked media assets.
     */
    public function mediaAssets(): BelongsToMany
    {
        return $this->belongsToMany(MediaAsset::class, 'media_asset_vocabularies')
            ->withPivot('relation_type')
            ->withTimestamps();
    }

    /**
     * Get the primary media asset for this vocabulary term.
     */
    public function primaryMediaAsset(): ?MediaAsset
    {
        if ($this->relationLoaded('mediaAssets')) {
            return $this->mediaAssets->firstWhere('pivot.relation_type', 'primary')
                ?? $this->mediaAssets->first();
        }

        return $this->mediaAssets()
            ->wherePivot('relation_type', 'primary')
            ->first()
            ?? $this->mediaAssets()->first();
    }

    /**
     * Get image file path of the primary asset or null.
     */
    public function assetImagePath(): ?string
    {
        return $this->primaryMediaAsset()?->file_path;
    }
}
