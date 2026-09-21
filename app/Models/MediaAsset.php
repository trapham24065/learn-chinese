<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class MediaAsset extends Model
{
    use HasFactory;

    public const CATEGORY_ACTIONS   = 'actions';
    public const CATEGORY_ANIMALS   = 'animals';
    public const CATEGORY_TRANSPORT = 'transport';
    public const CATEGORY_FOOD      = 'food';
    public const CATEGORY_PEOPLE    = 'people';
    public const CATEGORY_PLACES    = 'places';
    public const CATEGORY_OBJECTS   = 'objects';
    public const CATEGORY_NATURE    = 'nature';

    public const CATEGORIES = [
        self::CATEGORY_ACTIONS   => 'Hành động (Actions)',
        self::CATEGORY_ANIMALS   => 'Động vật (Animals)',
        self::CATEGORY_TRANSPORT => 'Giao thông (Transport)',
        self::CATEGORY_FOOD      => 'Ăn uống (Food & Drinks)',
        self::CATEGORY_PEOPLE    => 'Con người & Nghề nghiệp (People & Roles)',
        self::CATEGORY_PLACES    => 'Địa điểm (Places)',
        self::CATEGORY_OBJECTS   => 'Đồ vật (Objects)',
        self::CATEGORY_NATURE    => 'Tự nhiên & Thời tiết (Nature & Weather)',
    ];

    protected $fillable = [
        'slug',
        'name',
        'file_path',
        'category',
        'alt_text',
        'keywords',
        'source',
        'license',
        'attribution',
        'metadata',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'keywords'  => 'array',
            'metadata'  => 'array',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Encode the given value as JSON preserving Unicode characters.
     *
     * @param  mixed  $value
     * @param  int  $flags
     * @return string
     */
    protected function asJson($value, $flags = 0)
    {
        return json_encode($value, $flags | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    /**
     * Linked vocabularies.
     */
    public function vocabularies(): BelongsToMany
    {
        return $this->belongsToMany(Vocabulary::class, 'media_asset_vocabularies')
            ->withPivot('relation_type')
            ->withTimestamps();
    }

    /**
     * Scope query by category.
     */
    public function scopeCategory(Builder $query, string $category): Builder
    {
        return $query->where('category', $category);
    }

    /**
     * Scope query by active status.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Search assets by semantic keyword (Hanzi, Pinyin, Vietnamese, English).
     */
    public function scopeSearchKeyword(Builder $query, string $keyword): Builder
    {
        $keyword = trim($keyword);
        if ($keyword === '') {
            return $query;
        }

        return $query->where(function (Builder $q) use ($keyword) {
            $q->where('name', 'like', "%{$keyword}%")
              ->orWhere('slug', 'like', "%{$keyword}%")
              ->orWhere('alt_text', 'like', "%{$keyword}%")
              ->orWhere('keywords', 'like', "%{$keyword}%")
              ->orWhereHas('vocabularies', function (Builder $vq) use ($keyword) {
                  $vq->where('hanzi', 'like', "%{$keyword}%")
                     ->orWhere('pinyin', 'like', "%{$keyword}%")
                     ->orWhere('meaning', 'like', "%{$keyword}%");
              });
        });
    }

    /**
     * Track where this asset is currently being utilized in the system.
     * Checks:
     *  - Questions (image or image_set matching file_path or mock alias)
     *  - Flashcards (via linked vocabularies)
     */
    public function usageLocations(): array
    {
        $filePath = $this->file_path;
        $baseName = basename($filePath);

        // Find questions using this file path or base file name
        $questionCount = Question::query()
            ->where(function (Builder $q) use ($filePath, $baseName) {
                $q->where('image', $filePath)
                  ->orWhere('image', 'like', "%{$baseName}")
                  ->orWhere('image_set', 'like', "%{$baseName}%");
            })
            ->count();

        // Find flashcards via linked vocabularies
        $vocabIds = $this->vocabularies()->pluck('vocabularies.id')->toArray();
        $flashcardCount = !empty($vocabIds)
            ? Flashcard::whereIn('vocabulary_id', $vocabIds)->count()
            : 0;

        return [
            'questions_count'  => $questionCount,
            'flashcards_count' => $flashcardCount,
            'vocabularies_count' => count($vocabIds),
            'total_references' => $questionCount + $flashcardCount + count($vocabIds),
        ];
    }

    /**
     * Determine if this asset is currently in active use across the system.
     */
    public function isInUse(): bool
    {
        $locations = $this->usageLocations();
        return ($locations['total_references'] ?? 0) > 0;
    }
}
