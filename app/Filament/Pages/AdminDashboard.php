<?php

namespace App\Filament\Pages;

use App\Filament\Pages\ManageHsk;
use App\Filament\Resources\Flashcards\FlashcardResource;
use App\Filament\Resources\Lessons\LessonResource;
use App\Filament\Resources\Questions\QuestionResource;
use App\Filament\Resources\Students\StudentResource;
use App\Models\Flashcard;
use App\Models\Lesson;
use App\Models\MockTest;
use App\Models\Question;
use App\Models\StudySession;
use App\Models\User;
use Carbon\Carbon;
use Filament\Pages\Dashboard as BaseDashboard;

class AdminDashboard extends BaseDashboard
{
    protected static string $routePath = '/dashboard';

    protected static ?string $title = 'Tổng quan quản trị';

    protected string $view = 'filament.pages.admin-dashboard';

    public function getWidgets(): array
    {
        return [];
    }

    public function getViewData(): array
    {
        // 1. Core KPIs
        $totalStudents = User::where('role', User::ROLE_STUDENT)->count();
        $newStudentsThisWeek = User::where('role', User::ROLE_STUDENT)
            ->where('created_at', '>=', now()->subDays(7))
            ->count();

        $activeLearners7d = User::where('role', User::ROLE_STUDENT)
            ->whereHas('studySessions', fn ($q) => $q->where('started_at', '>=', now()->subDays(7)))
            ->count();

        $totalLessons = Lesson::where('is_published', true)->count();
        $totalFlashcards = Flashcard::count();
        $totalQuestions = Question::where('is_active', true)->count();

        $totalStudyMinutes = (int) StudySession::sum('duration_minutes');
        $totalStudyHours = round($totalStudyMinutes / 60, 1);
        $totalSessions7d = StudySession::where('started_at', '>=', now()->subDays(7))->count();
        $avgScore = (int) round((float) (StudySession::whereNotNull('score')->avg('score') ?? 0));

        $totalMockTests = MockTest::count();
        $passedMockTests = MockTest::where('passed', true)->count();
        $mockPassRate = $totalMockTests > 0 ? (int) round(($passedMockTests / $totalMockTests) * 100) : 0;
        $totalCertificates = MockTest::whereNotNull('certificate_code')->count();

        // 2. 14-Day Study Activity (Chart data)
        $chartDates = [];
        $studyMinutesData = [];
        $sessionsCountData = [];
        for ($i = 13; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $dateStr = $date->format('Y-m-d');
            $chartDates[] = $date->format('d/m');

            $daySessions = StudySession::whereDate('started_at', $dateStr)->get();
            $studyMinutesData[] = (int) $daySessions->sum('duration_minutes');
            $sessionsCountData[] = $daySessions->count();
        }

        // 3. HSK Content & Test Matrix (Level 1 - 6)
        $hskColors = [
            1 => '#dc2626',
            2 => '#d97706',
            3 => '#2563eb',
            4 => '#7c3aed',
            5 => '#059669',
            6 => '#0f172a',
        ];

        $hskMatrix = [];
        for ($lvl = 1; $lvl <= 6; $lvl++) {
            $cardsCount = Flashcard::where('hsk_level', $lvl)->count();
            $lessonsCount = Lesson::where('hsk_level', $lvl)->where('is_published', true)->count();
            $questionsCount = Question::where('hsk_level', $lvl)->where('is_active', true)->count();
            $listeningQCount = Question::where('hsk_level', $lvl)->where('skill_type', 'listening')->where('is_active', true)->count();
            $readingQCount = Question::where('hsk_level', $lvl)->where('skill_type', 'reading')->where('is_active', true)->count();
            $grammarQCount = Question::where('hsk_level', $lvl)->where('skill_type', 'grammar')->where('is_active', true)->count();

            $testsCount = MockTest::where('hsk_level', $lvl)->count();
            $testsPassed = MockTest::where('hsk_level', $lvl)->where('passed', true)->count();
            $passRate = $testsCount > 0 ? (int) round(($testsPassed / $testsCount) * 100) : 0;

            $hskMatrix[$lvl] = [
                'level'            => $lvl,
                'color'            => $hskColors[$lvl] ?? '#dc2626',
                'flashcards'       => $cardsCount,
                'lessons'          => $lessonsCount,
                'questions'        => $questionsCount,
                'listening_q'      => $listeningQCount,
                'reading_q'        => $readingQCount,
                'grammar_q'        => $grammarQCount,
                'mock_tests_count' => $testsCount,
                'pass_rate'        => $passRate,
            ];
        }

        // 4. Latest 6 Mock Test Submissions
        $recentMockTests = MockTest::with('user')
            ->orderByDesc('completed_at')
            ->take(6)
            ->get();

        // 5. Top 5 Active / High Study Students
        $topStudents = User::where('role', User::ROLE_STUDENT)
            ->withCount(['lessonProgresses as completed_lessons' => fn ($q) => $q->where('status', 'completed')])
            ->withSum('studySessions as total_minutes', 'duration_minutes')
            ->orderByDesc('total_minutes')
            ->orderByDesc('id')
            ->take(5)
            ->get()
            ->map(function ($student) {
                $student->calculated_streak = $student->calculateStreak();
                return $student;
            });

        return [
            'kpis' => [
                'total_students'     => $totalStudents,
                'new_students_7d'    => $newStudentsThisWeek,
                'active_students_7d' => $activeLearners7d,
                'total_lessons'      => $totalLessons,
                'total_flashcards'   => $totalFlashcards,
                'total_questions'    => $totalQuestions,
                'total_study_hours'  => $totalStudyHours,
                'total_sessions_7d'  => $totalSessions7d,
                'avg_score'          => $avgScore,
                'total_mock_tests'   => $totalMockTests,
                'mock_pass_rate'     => $mockPassRate,
                'total_certificates' => $totalCertificates,
            ],
            'chart' => [
                'labels'   => $chartDates,
                'minutes'  => $studyMinutesData,
                'sessions' => $sessionsCountData,
            ],
            'hskMatrix'       => $hskMatrix,
            'recentMockTests' => $recentMockTests,
            'topStudents'     => $topStudents,
            'urls'            => [
                'create_flashcard' => FlashcardResource::getUrl('create'),
                'create_question'  => QuestionResource::getUrl('create'),
                'create_lesson'    => LessonResource::getUrl('create'),
                'students'         => StudentResource::getUrl('index'),
                'manage_hsk'       => ManageHsk::getUrl(),
            ],
        ];
    }
}
