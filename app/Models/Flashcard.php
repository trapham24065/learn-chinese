<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Flashcard extends Model
{
    use HasFactory;

    protected $fillable = [
        'vocabulary_id',
        'lesson_id',
        'hanzi',
        'pinyin',
        'meaning',
        'example',
        'example_pinyin',
        'example_meaning',
        'tags',
        'sort_order',
        'is_active',
        'hsk_level',
    ];

    protected function casts(): array
    {
        return [
            'tags'      => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function vocabulary(): BelongsTo
    {
        return $this->belongsTo(Vocabulary::class);
    }

    public function hskLevel(string $standardCode = HskStandard::CODE_HSK_2_0): ?int
    {
        if ($this->vocabulary) {
            $level = $this->vocabulary->hskLevelFor($standardCode);
            if ($level !== null) {
                return $level;
            }
        }

        return $standardCode === HskStandard::CODE_HSK_2_0 ? $this->hsk_level : null;
    }

    public function hsk2Level(): ?int
    {
        return $this->hskLevel(HskStandard::CODE_HSK_2_0);
    }

    public function hsk3Level(): ?int
    {
        return $this->vocabulary?->hsk3Level();
    }

    public function allHskLevels(): array
    {
        if ($this->vocabulary) {
            $levels = $this->vocabulary->allHskLevels();
            if (!empty($levels)) {
                return $levels;
            }
        }

        return $this->hsk_level ? [HskStandard::CODE_HSK_2_0 => $this->hsk_level] : [];
    }
}
