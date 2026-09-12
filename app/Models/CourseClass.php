<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseClass extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'name',
        'code',
        'start_date',
        'end_date',
        'schedule_days',
        'schedule_time',
        'max_students',
        'status',
        'meet_url',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'max_students' => 'integer',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(CourseRegistration::class);
    }

    public function confirmedRegistrations(): HasMany
    {
        return $this->hasMany(CourseRegistration::class)
            ->whereIn('status', ['deposit_paid', 'paid', 'enrolled']);
    }

    public function getEnrolledCountAttribute(): int
    {
        return $this->confirmedRegistrations()->count();
    }

    public function getRemainingSlotsAttribute(): int
    {
        return max(0, $this->max_students - $this->enrolled_count);
    }

    public function getIsFullAttribute(): bool
    {
        return $this->status === 'full' || $this->remaining_slots <= 0;
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'Bản nháp',
            'open' => 'Đang mở đăng ký',
            'full' => 'Đã đủ học viên',
            'ongoing' => 'Đang học',
            'completed' => 'Đã kết thúc',
            'cancelled' => 'Đã hủy',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'open' => 'success',
            'full' => 'warning',
            'ongoing' => 'info',
            'completed' => 'gray',
            'cancelled' => 'danger',
            default => 'gray',
        };
    }
}
