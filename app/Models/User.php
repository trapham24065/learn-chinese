<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    public const ROLE_STUDENT = 'student';

    public const ROLE_ADMIN = 'admin';

    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function lessonProgresses(): HasMany
    {
        return $this->hasMany(LessonProgress::class);
    }

    public function flashcardProgresses(): HasMany
    {
        return $this->hasMany(FlashcardProgress::class);
    }

    public function starredFlashcards(): BelongsToMany
    {
        return $this->belongsToMany(Flashcard::class, 'flashcard_progresses')
            ->wherePivot('is_starred', true)
            ->withTimestamps();
    }

    public function studySessions(): HasMany
    {
        return $this->hasMany(StudySession::class);
    }

    public function mockTests(): HasMany
    {
        return $this->hasMany(MockTest::class);
    }

    public function storyProgresses(): HasMany
    {
        return $this->hasMany(StoryProgress::class);
    }

    public function courseRegistrations(): HasMany
    {
        return $this->hasMany(CourseRegistration::class);
    }

    public function learningActivities(): HasMany
    {
        return $this->hasMany(LearningActivity::class);
    }

    public function learningStats(): HasOne
    {
        return $this->hasOne(UserLearningStat::class);
    }

    public function dailyProgresses(): HasMany
    {
        return $this->hasMany(UserDailyProgress::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isStudent(): bool
    {
        return $this->role === self::ROLE_STUDENT;
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->isAdmin();
    }

    public function calculateStreak(): int
    {
        $activeDates = $this->studySessions()
            ->get()
            ->map(fn (StudySession $session) => ($session->completed_at ?? $session->started_at ?? $session->created_at)->toDateString())
            ->unique()
            ->flip();

        if ($activeDates->isEmpty()) {
            return 0;
        }

        $todayStr = now()->toDateString();
        $yesterdayStr = now()->subDay()->toDateString();

        // If studied today, start from today.
        // If haven't studied today yet, but studied yesterday, streak is kept from yesterday.
        if (isset($activeDates[$todayStr])) {
            $cursor = now()->startOfDay();
        } elseif (isset($activeDates[$yesterdayStr])) {
            $cursor = now()->subDay()->startOfDay();
        } else {
            return 0;
        }

        $streak = 0;
        while (isset($activeDates[$cursor->toDateString()])) {
            $streak++;
            $cursor->subDay();
        }

        return $streak;
    }

    public function hasStudiedToday(): bool
    {
        $timezone = config('app.timezone', 'Asia/Ho_Chi_Minh');
        $today = now($timezone)->toDateString();

        $stats = $this->learningStats;
        if ($stats && $stats->last_activity_date) {
            $lastDate = is_string($stats->last_activity_date)
                ? substr($stats->last_activity_date, 0, 10)
                : $stats->last_activity_date->toDateString();
            if ($lastDate === $today) {
                return true;
            }
        }

        return $this->studySessions()
            ->get()
            ->contains(fn (StudySession $s) => ($s->completed_at ?? $s->started_at ?? $s->created_at)->isToday());
    }
}
