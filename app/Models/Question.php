<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'lesson_id',
        'hsk_level',
        'exam_standard',
        'question',
        'pinyin',
        'audio_text',
        'image',
        'image_alt',
        'image_set',
        'options',
        'correct_answer',
        'explanation',
        'difficulty',
        'skill_type',
        'question_type',
        'media_type',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'options' => 'array',
            'image_set' => 'array',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
            'hsk_level' => 'integer',
        ];
    }

    public function scopeStandard($query, string $standard = 'hsk_2_0')
    {
        return $query->where(function ($q) use ($standard) {
            $q->where('exam_standard', $standard)
              ->orWhereNull('exam_standard');
        });
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }
}
