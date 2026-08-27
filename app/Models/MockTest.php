<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class MockTest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'hsk_level',
        'title',
        'total_questions',
        'correct_answers',
        'total_score',
        'max_score',
        'listening_score',
        'reading_score',
        'grammar_score',
        'listening_total',
        'listening_correct',
        'reading_total',
        'reading_correct',
        'grammar_total',
        'grammar_correct',
        'duration_seconds',
        'time_limit_minutes',
        'passed',
        'certificate_code',
        'details',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'hsk_level' => 'integer',
            'total_questions' => 'integer',
            'correct_answers' => 'integer',
            'total_score' => 'integer',
            'max_score' => 'integer',
            'listening_score' => 'integer',
            'reading_score' => 'integer',
            'grammar_score' => 'integer',
            'listening_total' => 'integer',
            'listening_correct' => 'integer',
            'reading_total' => 'integer',
            'reading_correct' => 'integer',
            'grammar_total' => 'integer',
            'grammar_correct' => 'integer',
            'duration_seconds' => 'integer',
            'time_limit_minutes' => 'integer',
            'passed' => 'boolean',
            'details' => 'array',
            'completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function generateCertificateCode(int $hskLevel): string
    {
        do {
            $code = sprintf('LC-HSK%d-%s', $hskLevel, strtoupper(Str::random(6)));
        } while (static::where('certificate_code', $code)->exists());

        return $code;
    }

    public function getListeningPercentAttribute(): int
    {
        return $this->listening_total > 0
            ? (int) round(($this->listening_correct / $this->listening_total) * 100)
            : 0;
    }

    public function getReadingPercentAttribute(): int
    {
        return $this->reading_total > 0
            ? (int) round(($this->reading_correct / $this->reading_total) * 100)
            : 0;
    }

    public function getGrammarPercentAttribute(): int
    {
        return $this->grammar_total > 0
            ? (int) round(($this->grammar_correct / $this->grammar_total) * 100)
            : 0;
    }

    public function getFormattedDurationAttribute(): string
    {
        $minutes = (int) floor($this->duration_seconds / 60);
        $seconds = $this->duration_seconds % 60;

        return sprintf('%02d:%02d', $minutes, $seconds);
    }

    public function getGradeTextAttribute(): string
    {
        $pct = $this->max_score > 0 ? ($this->total_score / $this->max_score) * 100 : 0;

        return match (true) {
            $pct >= 90 => 'Xuất sắc (优秀)',
            $pct >= 80 => 'Rất tốt (良好)',
            $pct >= 60 => 'Đạt chuẩn (合格)',
            default => 'Chưa đạt (不合格)',
        };
    }
}
