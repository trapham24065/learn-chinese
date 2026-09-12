<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseRegistrationActivity extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'course_registration_id',
        'user_id',
        'type',
        'description',
        'metadata',
        'created_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];

    public function registration(): BelongsTo
    {
        return $this->belongsTo(CourseRegistration::class, 'course_registration_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'created' => 'Đăng ký mới',
            'call' => 'Cuộc gọi tư vấn',
            'zalo_sent' => 'Nhắn tin Zalo',
            'status_changed' => 'Đổi trạng thái',
            'payment_updated' => 'Cập nhật thanh toán',
            'meet_sent' => 'Gửi link Google Meet',
            'note_added' => 'Ghi chú nội bộ',
            default => $this->type,
        };
    }

    public function getTypeIconAttribute(): string
    {
        return match ($this->type) {
            'created' => 'heroicon-o-sparkles',
            'call' => 'heroicon-o-phone',
            'zalo_sent' => 'heroicon-o-chat-bubble-left-right',
            'status_changed' => 'heroicon-o-arrow-path',
            'payment_updated' => 'heroicon-o-banknotes',
            'meet_sent' => 'heroicon-o-video-camera',
            'note_added' => 'heroicon-o-pencil-square',
            default => 'heroicon-o-information-circle',
        };
    }
}
