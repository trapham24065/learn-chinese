<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Story extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'title_pinyin',
        'title_vi',
        'slug',
        'hsk_level',
        'category',
        'cover_color',
        'summary',
        'content_json',
        'quiz_json',
        'word_count',
        'estimated_reading_minutes',
        'is_published',
    ];

    protected $casts = [
        'hsk_level'                 => 'integer',
        'content_json'              => 'array',
        'quiz_json'                 => 'array',
        'word_count'                => 'integer',
        'estimated_reading_minutes' => 'integer',
        'is_published'              => 'boolean',
    ];

    public function progresses(): HasMany
    {
        return $this->hasMany(StoryProgress::class);
    }

    public function userProgress(?int $userId = null): ?StoryProgress
    {
        $uid = $userId ?? auth()->id();
        if (! $uid) {
            return null;
        }

        return $this->progresses()->where('user_id', $uid)->first();
    }

    public function isCompletedBy(?int $userId = null): bool
    {
        return (bool) $this->userProgress($userId)?->is_completed;
    }

    public function getHskBadgeBgAttribute(): string
    {
        return match ($this->hsk_level) {
            1 => 'bg-red-50 text-red-700 border-red-200',
            2 => 'bg-amber-50 text-amber-700 border-amber-200',
            3 => 'bg-blue-50 text-blue-700 border-blue-200',
            4 => 'bg-purple-50 text-purple-700 border-purple-200',
            5 => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            default => 'bg-slate-100 text-slate-800 border-slate-300',
        };
    }

    public function getHskColorAttribute(): string
    {
        return match ($this->hsk_level) {
            1 => '#dc2626',
            2 => '#d97706',
            3 => '#2563eb',
            4 => '#7c3aed',
            5 => '#059669',
            default => '#0f172a',
        };
    }
}
