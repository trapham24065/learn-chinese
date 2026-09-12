<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class CourseRegistration extends Model
{
    use HasFactory;

    protected $fillable = [
        'registration_code',
        'course_id',
        'course_class_id',
        'user_id',
        'full_name',
        'phone',
        'phone_normalized',
        'email',
        'zalo',
        'preferred_schedule',
        'current_level',
        'learning_goal',
        'status',
        'payment_status',
        'payment_method',
        'payment_amount',
        'paid_at',
        'payment_reference',
        'ip_address',
        'user_agent',
        'admin_notes',
    ];

    protected $casts = [
        'payment_amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (CourseRegistration $registration) {
            if (empty($registration->registration_code)) {
                $registration->registration_code = self::generateUniqueCode();
            }

            if (empty($registration->phone_normalized) && ! empty($registration->phone)) {
                $registration->phone_normalized = self::normalizePhone($registration->phone);
            }

            if (empty($registration->payment_reference)) {
                $registration->payment_reference = 'LCH ' . $registration->registration_code;
            }
        });
    }

    public static function generateUniqueCode(): string
    {
        $prefix = 'REG' . date('ym');
        do {
            $code = $prefix . strtoupper(Str::random(4));
        } while (self::where('registration_code', $code)->exists());

        return $code;
    }

    public static function normalizePhone(string $phone): string
    {
        $cleaned = preg_replace('/[^\d]/', '', $phone);
        if (str_starts_with($cleaned, '84')) {
            $cleaned = '0' . substr($cleaned, 2);
        }

        return $cleaned;
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function courseClass(): BelongsTo
    {
        return $this->belongsTo(CourseClass::class, 'course_class_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(CourseRegistrationActivity::class)->orderByDesc('created_at');
    }

    public function recordActivity(string $type, string $description, ?array $metadata = null, ?int $userId = null): CourseRegistrationActivity
    {
        return $this->activities()->create([
            'user_id' => $userId ?? auth()->id(),
            'type' => $type,
            'description' => $description,
            'metadata' => $metadata,
            'created_at' => now(),
        ]);
    }

    public function getFormattedPaymentAmountAttribute(): string
    {
        return number_format((float) $this->payment_amount, 0, ',', '.') . ' ₫';
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Mới đăng ký (Chờ liên hệ)',
            'contacted' => 'Đã liên hệ',
            'consulted' => 'Đã tư vấn',
            'deposit_paid' => 'Đã đặt cọc',
            'paid' => 'Đã thanh toán đủ',
            'enrolled' => 'Đã vào lớp',
            'cancelled' => 'Đã hủy',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'warning',
            'contacted' => 'info',
            'consulted' => 'primary',
            'deposit_paid' => 'indigo',
            'paid' => 'success',
            'enrolled' => 'emerald',
            'cancelled' => 'danger',
            default => 'gray',
        };
    }

    public function getPaymentStatusLabelAttribute(): string
    {
        return match ($this->payment_status) {
            'unpaid' => 'Chưa thanh toán',
            'deposit' => 'Đã cọc',
            'paid' => 'Đã thanh toán',
            'refunded' => 'Đã hoàn tiền',
            default => $this->payment_status,
        };
    }

    public function getCurrentLevelLabelAttribute(): string
    {
        return match ($this->current_level) {
            'chua_biet_gi' => 'Chưa biết gì',
            'co_ban_phat_am' => 'Biết phát âm cơ bản (Pinyin)',
            'hsk1_2' => 'Đã học qua HSK 1 - 2',
            'hsk3_4' => 'Đã học qua HSK 3 - 4',
            'giao_tiep' => 'Muốn tập trung giao tiếp',
            default => $this->current_level,
        };
    }

    public function getVietQrUrl(float $amount = null): string
    {
        $bankId = setting('bank_id', 'MB');
        $bankAccount = setting('bank_account', '0988888888');
        $accountName = urlencode(setting('bank_account_name', 'TIENG TRUNG CO GIAO'));
        $payAmount = $amount ?? ($this->payment_amount > 0 ? $this->payment_amount : ($this->course->price ?? 0));
        $memo = urlencode($this->registration_code);

        return "https://img.vietqr.io/image/{$bankId}-{$bankAccount}-compact2.png?amount={$payAmount}&addInfo={$memo}&accountName={$accountName}";
    }
}
