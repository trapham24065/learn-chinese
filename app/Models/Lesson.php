<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lesson extends Model
{
    use HasFactory;

    public const HSK_LEVELS = [
        1 => 'HSK 1 – Sơ cấp (150 từ)',
        2 => 'HSK 2 – Sơ cấp cao (300 từ)',
        3 => 'HSK 3 – Trung cấp thấp (600 từ)',
        4 => 'HSK 4 – Trung cấp (1200 từ)',
        5 => 'HSK 5 – Cao cấp (2500 từ)',
        6 => 'HSK 6 – Thành thạo (5000+ từ)',
    ];

    protected $fillable = [
        'slug',
        'title',
        'summary',
        'content',
        'difficulty',
        'hsk_level',
        'sort_order',
        'estimated_minutes',
        'accent_color',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
        ];
    }

    public function progresses(): HasMany
    {
        return $this->hasMany(LessonProgress::class);
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(StudySession::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    public function flashcards(): HasMany
    {
        return $this->hasMany(Flashcard::class);
    }
}
