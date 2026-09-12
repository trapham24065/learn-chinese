<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'category',
        'summary',
        'description',
        'original_price',
        'price',
        'duration_weeks',
        'total_sessions',
        'highlights',
        'curriculum',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'original_price' => 'decimal:2',
        'price' => 'decimal:2',
        'duration_weeks' => 'integer',
        'total_sessions' => 'integer',
        'highlights' => 'array',
        'curriculum' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function classes(): HasMany
    {
        return $this->hasMany(CourseClass::class);
    }

    public function openClasses(): HasMany
    {
        return $this->hasMany(CourseClass::class)
            ->whereIn('status', ['open', 'full'])
            ->orderBy('start_date');
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(CourseRegistration::class);
    }

    public function getFormattedPriceAttribute(): string
    {
        return number_format((float) $this->price, 0, ',', '.') . ' ₫';
    }

    public function getFormattedOriginalPriceAttribute(): ?string
    {
        if (! $this->original_price || $this->original_price <= $this->price) {
            return null;
        }

        return number_format((float) $this->original_price, 0, ',', '.') . ' ₫';
    }

    public function getDiscountPercentageAttribute(): ?int
    {
        if (! $this->original_price || $this->original_price <= $this->price) {
            return null;
        }

        return (int) round((1 - ($this->price / $this->original_price)) * 100);
    }

    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'hsk_starter' => 'HSK Sơ cấp (1-2)',
            'hsk_intermediate' => 'HSK Trung cấp (3-4)',
            'hsk_advanced' => 'HSK Cao cấp (5-6)',
            'conversation' => 'Giao tiếp thực chiến',
            'business' => 'Tiếng Trung Thương mại',
            'kids' => 'Tiếng Trung Trẻ em',
            default => 'Khóa học',
        };
    }
}
