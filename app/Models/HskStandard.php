<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HskStandard extends Model
{
    use HasFactory;

    public const CODE_HSK_2_0 = 'hsk_2_0';
    public const CODE_HSK_3_0_2021 = 'hsk_3_0_2021';
    public const CODE_HSK_3_0_2026 = 'hsk_3_0_2026';

    protected $fillable = [
        'code',
        'name',
        'version',
        'is_active',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function vocabularyLevels(): HasMany
    {
        return $this->hasMany(VocabularyHskLevel::class, 'hsk_standard_id');
    }

    public function vocabularies(): BelongsToMany
    {
        return $this->belongsToMany(Vocabulary::class, 'vocabulary_hsk_levels', 'hsk_standard_id', 'vocabulary_id')
            ->withPivot(['level', 'source', 'topic', 'sort_order'])
            ->withTimestamps();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCode($query, string $code)
    {
        return $query->where('code', $code);
    }

    public static function findByCode(string $code): ?self
    {
        return static::where('code', $code)->first();
    }
}
