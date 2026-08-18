<?php

namespace App\Filament\Widgets;

use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\StudySession;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class LearningOverview extends StatsOverviewWidget
{
    protected ?string $pollingInterval = '60s';

    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        $lessonCount = Lesson::query()->where('is_published', true)->count();
        $weeklySessionCount = StudySession::query()->whereDate('started_at', '>=', now()->subDays(6))->count();
        $activeLearnerCount = User::query()->whereHas('studySessions')->count();
        $completedLessonCount = LessonProgress::query()->where('status', 'completed')->count();
        $averageScore = (int) round((float) (StudySession::query()->whereNotNull('score')->avg('score') ?? 0));

        return [
            Stat::make('Bài học', $lessonCount)
                ->description('Đã xuất bản')
                ->color('primary'),
            Stat::make('Học viên đang hoạt động', $activeLearnerCount)
                ->description('Có session gần đây')
                ->color('success'),
            Stat::make('Phiên học 7 ngày', $weeklySessionCount)
                ->description('Bài học, flashcard, quiz')
                ->color('warning'),
            Stat::make('Bài đã hoàn thành', $completedLessonCount)
                ->description('Theo trạng thái completed')
                ->color('info'),
            Stat::make('Điểm trung bình', $averageScore . '%')
                ->description('Dựa trên quiz thực tế')
                ->color('danger'),
        ];
    }
}
