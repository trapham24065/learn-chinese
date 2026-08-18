<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Flashcard extends Model
{
    use HasFactory;

    protected $fillable = [
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
}
