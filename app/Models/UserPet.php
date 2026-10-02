<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserPet extends Model
{
    protected $fillable = [
        'user_id',
        'pet_id',
        'name',
        'stage',
        'exp',
        'total_fed_xp',
        'hunger',
        'status',
        'personality',
        'affinity',
        'last_studied_at',
        'study_session_count',
        'last_fed_at',
        'last_hunger_calculated_at',
        'dormant_at',
        'reset_count',
        'best_stage',
        'learning_dna',
    ];

    protected $casts = [
        'stage'                     => 'integer',
        'exp'                       => 'integer',
        'total_fed_xp'              => 'integer',
        'hunger'                    => 'integer',
        'affinity'                  => 'integer',
        'study_session_count'       => 'integer',
        'last_studied_at'           => 'datetime',
        'last_fed_at'               => 'datetime',
        'last_hunger_calculated_at' => 'datetime',
        'dormant_at'                => 'datetime',
        'learning_dna'              => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class);
    }

    public function feedingLogs(): HasMany
    {
        return $this->hasMany(PetFeedingLog::class);
    }

    public function memories(): HasMany
    {
        return $this->hasMany(PetMemory::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isDormant(): bool
    {
        return $this->status === 'dormant';
    }

    public function isEgg(): bool
    {
        return $this->status === 'egg';
    }

    public function getHungerState(): string
    {
        return match (true) {
            $this->hunger >= 70 => 'happy',
            $this->hunger >= 40 => 'hungry',
            $this->hunger >= 20 => 'very_hungry',
            $this->hunger >= 1  => 'weak',
            default             => 'dormant',
        };
    }

    /**
     * Get relationship tier details based on affinity (0 - 100).
     */
    public function getAffinityTier(): array
    {
        $val = max(0, min(100, $this->affinity ?? 0));

        return match (true) {
            $val >= 95 => [
                'tier'        => 'partner',
                'name'        => 'Tri kỷ',
                'title'       => 'Tri kỷ học tập',
                'emoji'       => '🐉',
                'color'       => 'rose',
                'min'         => 95,
                'max'         => 100,
                'progress'    => 100,
                'description' => 'Pet xem bạn là tri kỷ trọn đời trên hành trình chinh phục tiếng Trung.',
            ],
            $val >= 80 => [
                'tier'        => 'close_friend',
                'name'        => 'Thân thiết',
                'title'       => 'Bạn thân thiết',
                'emoji'       => '💖',
                'color'       => 'purple',
                'min'         => 80,
                'max'         => 94,
                'progress'    => round((($val - 80) / 15) * 100),
                'description' => 'Pet luôn háo hức mỗi khi thấy bạn mở bài học và chia sẻ tâm sự.',
            ],
            $val >= 60 => [
                'tier'        => 'companion',
                'name'        => 'Đồng hành',
                'title'       => 'Bạn đồng hành',
                'emoji'       => '⭐',
                'color'       => 'amber',
                'min'         => 60,
                'max'         => 79,
                'progress'    => round((($val - 60) / 20) * 100),
                'description' => 'Pet luôn dõi theo từng bước tiến, chủ động cổ vũ khi bạn gặp từ khó.',
            ],
            $val >= 40 => [
                'tier'        => 'familiar',
                'name'        => 'Thân quen',
                'title'       => 'Bạn bè quen thuộc',
                'emoji'       => '🌸',
                'color'       => 'emerald',
                'min'         => 40,
                'max'         => 59,
                'progress'    => round((($val - 40) / 20) * 100),
                'description' => 'Pet đã nhớ các từ vựng bạn đã học và thường nhắc lại kỷ niệm xưa.',
            ],
            $val >= 20 => [
                'tier'        => 'met',
                'name'        => 'Làm quen',
                'title'       => 'Bạn mới quen',
                'emoji'       => '🌿',
                'color'       => 'blue',
                'min'         => 20,
                'max'         => 39,
                'progress'    => round((($val - 20) / 20) * 100),
                'description' => 'Pet đã bắt đầu quen mặt bạn và thích được gọi tên thân mật.',
            ],
            default => [
                'tier'        => 'stranger',
                'name'        => 'Bỡ ngỡ',
                'title'       => 'Người lạ mới gặp',
                'emoji'       => '🌱',
                'color'       => 'slate',
                'min'         => 0,
                'max'         => 19,
                'progress'    => round(($val / 20) * 100),
                'description' => 'Pet còn hơi ngại ngùng, đang làm quen từng ngày qua các bài học.',
            ],
        };
    }

    public function getPersonalityLabel(): string
    {
        return match ($this->personality ?? 'playful') {
            'curious'  => 'Tò mò & Khám phá',
            'shy'      => 'E thẹn & Dịu dàng',
            'cheerful' => 'Lạc quan & Ánh nắng',
            'calm'     => 'Điềm tĩnh & Uyên bác',
            default    => 'Tinh nghịch & Vui nhộn',
        };
    }

    public function getPersonalityEmoji(): string
    {
        return match ($this->personality ?? 'playful') {
            'curious'  => '🔍',
            'shy'      => '🌸',
            'cheerful' => '☀️',
            'calm'     => '🍵',
            default    => '✨',
        };
    }

    public function getPersonalityDescription(): string
    {
        return match ($this->personality ?? 'playful') {
            'curious'  => 'Thích tìm hiểu bộ thủ, tò mò hỏi bạn về những chữ Hán mới lạ.',
            'shy'      => 'Hay bẽn lẽn đỏ mặt, luôn dùng lời động viên nhẹ nhàng ấm áp.',
            'cheerful' => 'Luôn tràn đầy năng lượng, như hoạt náo viên cổ vũ bạn hết mình.',
            'calm'     => 'Điềm đạm, kiên nhẫn, luôn nhắc nhở bạn chậm mà chắc từng ngày.',
            default    => 'Hiếu động, thích nhún nhảy vui vẻ và chúc mừng mỗi khi bạn tiến bộ.',
        };
    }

    public function worldObjects(): HasMany
    {
        return $this->hasMany(PetWorldObject::class);
    }
}