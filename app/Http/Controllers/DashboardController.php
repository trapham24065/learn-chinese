<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\StudySession;
use App\Models\User;
use Carbon\CarbonPeriod;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(): View
    {
        $student = $this->resolveStudent();
        $studySessions = $student->studySessions()->with('lesson')->orderByDesc('completed_at')->orderByDesc('started_at')->get();
        $lessonProgresses = $student->lessonProgresses()->with('lesson')->get()->keyBy('lesson_id');

        // Fetch all published lessons with real question count and attach student's progress
        $lessons = Lesson::query()
            ->where('is_published', true)
            ->withCount(['questions' => fn ($q) => $q->where('is_active', true)])
            ->orderBy('sort_order')
            ->get()
            ->map(function (Lesson $lesson) use ($lessonProgresses) {
                $progress = $lessonProgresses->get($lesson->id);

                return [
                    'id' => $lesson->id,
                    'slug' => $lesson->slug,
                    'title' => $lesson->title,
                    'summary' => $lesson->summary,
                    'difficulty' => $lesson->difficulty,
                    'sort_order' => $lesson->sort_order,
                    'estimated_minutes' => $lesson->estimated_minutes,
                    'accent_color' => $lesson->accent_color ?? '#991b1b',
                    'questions_count' => $lesson->questions_count,
                    'status' => $progress?->status ?? 'not_started',
                    'progress_percent' => $progress?->progress_percent ?? 0,
                    'last_accessed_at' => $progress?->last_accessed_at,
                    'completed_at' => $progress?->completed_at,
                ];
            });

        $streakDays = $student->calculateStreak();
        $totalLessonsCount = $lessons->count();
        $completedLessonsCount = $lessons->where('status', 'completed')->count();
        $inProgressLessons = $lessons->where('status', 'in_progress')->values();
        $notStartedLessons = $lessons->where('status', 'not_started')->values();

        $todayMinutes = $studySessions
            ->filter(fn (StudySession $session) => $this->sessionDate($session)->isToday())
            ->sum('duration_minutes');

        $averageScore = (int) round((float) ($studySessions->whereNotNull('score')->avg('score') ?? 0));
        $completionRate = $totalLessonsCount > 0
            ? (int) round(($completedLessonsCount / $totalLessonsCount) * 100)
            : 0;

        $dueFlashcards = $student->flashcardProgresses()
            ->where('next_review_at', '<=', now())
            ->with('flashcard')
            ->get()
            ->pluck('flashcard')
            ->filter();
            
        $dueFlashcardsCount = $dueFlashcards->count();

        $weeklyChart = $this->buildWeeklyChart($studySessions);
        $chartMax = max(1, $weeklyChart->max('sessions'));

        $overview = [
            ['label' => 'Streak', 'value' => $streakDays . ' ngày', 'note' => 'Học liên tiếp'],
            ['label' => 'Điểm trung bình', 'value' => $averageScore . '%', 'note' => 'Các bài quiz đã làm'],
            ['label' => 'Bài hoàn thành', 'value' => $completedLessonsCount . '/' . $totalLessonsCount, 'note' => 'Lộ trình bài học'],
            ['label' => 'Tỉ lệ hoàn thành', 'value' => $completionRate . '%', 'note' => 'Tiến độ tổng thể'],
        ];

        $activities = $studySessions->take(6)->map(function (StudySession $session): array {
            $lessonTitle = $session->lesson?->title ?? 'Bài học chung';
            $timestamp = $this->sessionDate($session);
            $scoreText = $session->score !== null ? ' • ' . $session->score . ' điểm' : '';

            return [
                'id' => $session->id,
                'time' => $timestamp->diffForHumans(),
                'date_formatted' => $timestamp->format('d/m H:i'),
                'type' => $session->session_type,
                'title' => ucfirst($session->session_type) . ' - ' . $lessonTitle,
                'description' => $session->duration_minutes . ' phút' . $scoreText,
                'score' => $session->score,
            ];
        });

        $starredCount = $student->starredFlashcards()->count();

        return view('dashboard', [
            'student' => $student,
            'lessons' => $lessons,
            'totalLessonsCount' => $totalLessonsCount,
            'completedLessonsCount' => $completedLessonsCount,
            'inProgressLessons' => $inProgressLessons,
            'overview' => $overview,
            'weeklyChart' => $weeklyChart,
            'chartMax' => $chartMax,
            'streakDays' => $streakDays,
            'todayMinutes' => $todayMinutes,
            'averageScore' => $averageScore,
            'completionRate' => $completionRate,
            'activities' => $activities,
            'dueFlashcardsCount' => $dueFlashcardsCount,
            'dueFlashcards' => $dueFlashcards,
            'starredCount' => $starredCount,
        ]);
    }

    public function updateProgress(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'lesson_id' => ['required', 'exists:lessons,id'],
            'progress_percent' => ['required', 'integer', 'min:0', 'max:100'],
        ]);

        $student = $this->resolveStudent();
        $percent = (int) $validated['progress_percent'];

        $status = match (true) {
            $percent >= 100 => 'completed',
            $percent > 0 => 'in_progress',
            default => 'not_started',
        };

        $progress = LessonProgress::query()->updateOrCreate(
            [
                'user_id' => $student->id,
                'lesson_id' => $validated['lesson_id'],
            ],
            [
                'progress_percent' => $percent,
                'status' => $status,
                'last_accessed_at' => now(),
                'started_at' => now(),
                'completed_at' => $percent >= 100 ? now() : null,
            ]
        );

        $totalLessons = Lesson::query()->where('is_published', true)->count();
        $completedLessons = LessonProgress::query()
            ->where('user_id', $student->id)
            ->where('status', 'completed')
            ->count();

        $completionRate = $totalLessons > 0 ? (int) round(($completedLessons / $totalLessons) * 100) : 0;

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật tiến độ bài học thành công!',
            'progress_percent' => $progress->progress_percent,
            'status' => $progress->status,
            'completed_count' => $completedLessons,
            'total_lessons' => $totalLessons,
            'completion_rate' => $completionRate,
        ]);
    }

    public function logSession(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'duration_minutes' => ['required', 'integer', 'min:1', 'max:180'],
            'session_type' => ['required', 'string', 'in:lesson,flashcard,quiz'],
            'lesson_id' => ['nullable', 'exists:lessons,id'],
            'score' => ['nullable', 'integer', 'min:0', 'max:100'],
        ]);

        $student = $this->resolveStudent();

        $session = StudySession::create([
            'user_id' => $student->id,
            'lesson_id' => $validated['lesson_id'] ?? null,
            'session_type' => $validated['session_type'],
            'duration_minutes' => $validated['duration_minutes'],
            'score' => $validated['score'] ?? null,
            'started_at' => now()->subMinutes($validated['duration_minutes']),
            'completed_at' => now(),
        ]);

        $studySessions = $student->studySessions()->get();
        $todayMinutes = $studySessions
            ->filter(fn (StudySession $s) => $this->sessionDate($s)->isToday())
            ->sum('duration_minutes');
        $streakDays = $student->calculateStreak();

        return response()->json([
            'success' => true,
            'message' => 'Ghi nhận buổi học thành công! Đã cộng +' . $session->duration_minutes . ' phút.',
            'today_minutes' => $todayMinutes,
            'streak_days' => $streakDays,
            'new_activity' => [
                'id' => $session->id,
                'time' => 'Vừa xong',
                'date_formatted' => now()->format('d/m H:i'),
                'type' => $session->session_type,
                'title' => ucfirst($session->session_type) . ' - ' . ($session->lesson?->title ?? 'Tự học'),
                'description' => $session->duration_minutes . ' phút' . ($session->score ? ' • ' . $session->score . ' điểm' : ''),
            ],
        ]);
    }

    protected function resolveStudent(): User
    {
        return Auth::guard('web')->user()
            ?? User::query()->where('role', User::ROLE_STUDENT)->first()
            ?? User::query()->whereHas('studySessions')->orderBy('id')->first()
            ?? User::query()->orderBy('id')->firstOrFail();
    }

    protected function calculateStreak(Collection $studySessions): int
    {
        $activeDates = $studySessions
            ->map(fn (StudySession $session) => $this->sessionDate($session)->toDateString())
            ->unique()
            ->flip();

        $streak = 0;
        $cursor = now()->startOfDay();

        while (isset($activeDates[$cursor->toDateString()])) {
            $streak++;
            $cursor->subDay();
        }

        return $streak;
    }

    protected function buildWeeklyChart(Collection $studySessions): Collection
    {
        $period = CarbonPeriod::create(now()->subDays(6)->startOfDay(), now()->startOfDay());

        return collect($period)->map(function ($date) use ($studySessions): array {
            $daySessions = $studySessions->filter(fn (StudySession $session) => $this->sessionDate($session)->isSameDay($date));
            $scoreAverage = (int) round((float) ($daySessions->whereNotNull('score')->avg('score') ?? 0));
            $totalMinutes = $daySessions->sum('duration_minutes');

            return [
                'label' => $date->format('D'),
                'date' => $date->format('d/m'),
                'is_today' => $date->isToday(),
                'sessions' => $daySessions->count(),
                'minutes' => $totalMinutes,
                'score' => $scoreAverage,
            ];
        });
    }

    protected function sessionDate(StudySession $session)
    {
        return $session->completed_at ?? $session->started_at ?? $session->created_at;
    }
}
