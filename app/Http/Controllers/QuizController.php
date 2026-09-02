<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\Question;
use App\Models\StudySession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class QuizController extends Controller
{
    private const QUIZ_PER_SESSION = 10;

    public function index(Request $request): View
    {
        $lessons = Lesson::query()
            ->where('is_published', true)
            ->withCount(['questions' => fn ($q) => $q->where('is_active', true)])
            ->orderBy('sort_order')
            ->get();

        $selectedLessonSlug = $request->query('lesson');
        $selectedLesson = null;

        $query = Question::query()
            ->where('is_active', true)
            ->with('lesson');

        if ($selectedLessonSlug && $selectedLessonSlug !== 'all') {
            $selectedLesson = $lessons->firstWhere('slug', $selectedLessonSlug);
            if ($selectedLesson) {
                $query->where('lesson_id', $selectedLesson->id);
            }
        }

        // Total pool size (before limiting)
        $totalPoolCount = (clone $query)->count();

        // C5: Replace ORDER BY RAND() with PHP shuffle on IDs to avoid MySQL temp table sort
        $allIds = (clone $query)->pluck('id')->toArray();
        shuffle($allIds);
        $selectedIds = array_slice($allIds, 0, self::QUIZ_PER_SESSION);
        $questions = \App\Models\Question::whereIn('id', $selectedIds)->get()
            ->sortBy(fn ($q) => array_search($q->id, $selectedIds))->values();

        $user = Auth::guard('web')->user();
        $recentQuizSessions = $user
            ? $user->studySessions()->where('session_type', 'quiz')->orderByDesc('completed_at')->take(5)->get()
            : collect();

        $userAverageScore = $recentQuizSessions->isNotEmpty()
            ? (int) round((float) $recentQuizSessions->avg('score'))
            : 85;

        return view('quiz', [
            'lessons'             => $lessons,
            'selectedLessonSlug'  => $selectedLessonSlug ?: 'all',
            'selectedLesson'      => $selectedLesson,
            'questions'           => $questions,
            'totalActiveQuestions' => Question::query()->where('is_active', true)->count(),
            'totalPoolCount'      => $totalPoolCount,
            'perSession'          => self::QUIZ_PER_SESSION,
            'userAverageScore'    => $userAverageScore,
            'recentSessionsCount' => $recentQuizSessions->count(),
        ]);
    }

    public function submit(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'answers' => ['required', 'array'],
            'answers.*' => ['nullable', 'string'],
            'lesson_slug' => ['nullable', 'string'],
            'duration_seconds' => ['nullable', 'integer', 'min:1'],
        ]);

        $submittedAnswers = $validated['answers'];
        $questionIds = array_keys($submittedAnswers);

        $questions = Question::query()
            ->whereIn('id', $questionIds)
            ->with('lesson')
            ->get()
            ->keyBy('id');

        $totalQuestions = count($questionIds);
        $correctCount = 0;
        $details = [];

        foreach ($questionIds as $qId) {
            $question = $questions->get($qId);
            if (! $question) {
                continue;
            }

            $userAnswer = isset($submittedAnswers[$qId]) ? trim((string) $submittedAnswers[$qId]) : '';
            $correctAnswer = trim((string) $question->correct_answer);
            $isCorrect = ($userAnswer !== '' && mb_strtolower($userAnswer) === mb_strtolower($correctAnswer));

            if ($isCorrect) {
                $correctCount++;
            }

            $details[$qId] = [
                'id' => $question->id,
                'question' => $question->question,
                'pinyin' => $question->pinyin,
                'user_answer' => $userAnswer,
                'correct_answer' => $correctAnswer,
                'is_correct' => $isCorrect,
                'explanation' => $question->explanation,
                'options' => $question->options,
            ];
        }

        $scorePercent = $totalQuestions > 0 ? (int) round(($correctCount / $totalQuestions) * 100) : 0;
        $durationSeconds = (int) ($validated['duration_seconds'] ?? 60);
        $durationMinutes = max(1, (int) round($durationSeconds / 60));

        $user = Auth::guard('web')->user();
        if ($user && $user->isStudent()) {
            $lessonId = null;
            if (! empty($validated['lesson_slug']) && $validated['lesson_slug'] !== 'all') {
                $lesson = Lesson::query()->where('slug', $validated['lesson_slug'])->first();
                $lessonId = $lesson?->id;
            }

            StudySession::create([
                'user_id' => $user->id,
                'lesson_id' => $lessonId,
                'session_type' => 'quiz',
                'duration_minutes' => $durationMinutes,
                'score' => $scorePercent,
                'started_at' => now()->subSeconds($durationSeconds),
                'completed_at' => now(),
            ]);
        }

        $message = match (true) {
            $scorePercent === 100 => 'Xuất sắc tuyệt đối! Bạn đã trả lời đúng toàn bộ câu hỏi!',
            $scorePercent >= 80 => 'Rất tốt! Bạn đã nắm rất vững kiến thức phần này.',
            $scorePercent >= 50 => 'Khá tốt! Hãy xem lại các câu sai để ghi nhớ lâu hơn nhé.',
            default => 'Cần cố gắng thêm! Hãy xem lại giải thích chi tiết và flashcard nhé.',
        };

        return response()->json([
            'success' => true,
            'score' => $scorePercent,
            'correct_count' => $correctCount,
            'total_questions' => $totalQuestions,
            'message' => $message,
            'details' => $details,
        ]);
    }
}
