<?php

namespace App\Http\Controllers;

use App\Models\MockTest;
use App\Models\Question;
use App\Models\StudySession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class HskMockTestController extends Controller
{
    /**
     * Level specifications for HSK Mock Tests
     */
    private const LEVEL_SPECS = [
        1 => [
            'label' => 'HSK 1',
            'title' => 'Bài thi thử mô phỏng HSK 1',
            'desc' => 'Đánh giá năng lực tiếng Trung sơ cấp (150 từ vựng cốt lõi)',
            'time_limit' => 20, // minutes
            'question_count' => 30,
            'listening_count' => 10,
            'reading_count' => 10,
            'grammar_count' => 10,
            'max_score' => 300,
            'pass_score' => 180,
            'color' => '#dc2626',
            'badge_bg' => 'bg-red-50 text-red-700 border-red-200',
        ],
        2 => [
            'label' => 'HSK 2',
            'title' => 'Bài thi thử mô phỏng HSK 2',
            'desc' => 'Đánh giá giao tiếp sinh hoạt cơ bản (300 từ vựng)',
            'time_limit' => 25, // minutes
            'question_count' => 35,
            'listening_count' => 10,
            'reading_count' => 15,
            'grammar_count' => 10,
            'max_score' => 300,
            'pass_score' => 180,
            'color' => '#d97706',
            'badge_bg' => 'bg-amber-50 text-amber-700 border-amber-200',
        ],
        3 => [
            'label' => 'HSK 3',
            'title' => 'Bài thi thử mô phỏng HSK 3',
            'desc' => 'Đánh giá khả năng giao tiếp học tập & công việc (600 từ vựng)',
            'time_limit' => 35, // minutes
            'question_count' => 40,
            'listening_count' => 10,
            'reading_count' => 15,
            'grammar_count' => 15,
            'max_score' => 300,
            'pass_score' => 180,
            'color' => '#2563eb',
            'badge_bg' => 'bg-blue-50 text-blue-700 border-blue-200',
        ],
        4 => [
            'label' => 'HSK 4',
            'title' => 'Bài thi thử mô phỏng HSK 4',
            'desc' => 'Đánh giá khả năng thảo luận các chủ đề sâu rộng (1.200 từ vựng)',
            'time_limit' => 40,
            'question_count' => 45,
            'listening_count' => 15,
            'reading_count' => 15,
            'grammar_count' => 15,
            'max_score' => 300,
            'pass_score' => 180,
            'color' => '#7c3aed',
            'badge_bg' => 'bg-purple-50 text-purple-700 border-purple-200',
        ],
        5 => [
            'label' => 'HSK 5',
            'title' => 'Bài thi thử mô phỏng HSK 5',
            'desc' => 'Đánh giá khả năng đọc báo chí, xem phim Trung Quốc (2.500 từ vựng)',
            'time_limit' => 50,
            'question_count' => 50,
            'listening_count' => 15,
            'reading_count' => 20,
            'grammar_count' => 15,
            'max_score' => 300,
            'pass_score' => 180,
            'color' => '#059669',
            'badge_bg' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        ],
        6 => [
            'label' => 'HSK 6',
            'title' => 'Bài thi thử mô phỏng HSK 6',
            'desc' => 'Đánh giá trình độ tiếng Trung cao cấp, diễn đạt như người bản xứ (5.000+ từ)',
            'time_limit' => 50,
            'question_count' => 50,
            'listening_count' => 15,
            'reading_count' => 20,
            'grammar_count' => 15,
            'max_score' => 300,
            'pass_score' => 180,
            'color' => '#0f172a',
            'badge_bg' => 'bg-slate-100 text-slate-800 border-slate-300',
        ],
    ];

    /**
     * Display Mock Tests overview page.
     */
    public function index(Request $request): View
    {
        $specs = self::LEVEL_SPECS;
        $user = Auth::guard('web')->user();

        $history = $user
            ? MockTest::where('user_id', $user->id)
                ->orderByDesc('completed_at')
                ->take(10)
                ->get()
            : collect();

        $stats = [
            'total_taken' => $history->count(),
            'passed_count' => $history->where('passed', true)->count(),
            'highest_score' => $history->max('total_score') ?? 0,
            'certificates_count' => $history->whereNotNull('certificate_code')->count(),
        ];

        return view('hsk.mock.index', compact('specs', 'history', 'stats'));
    }

    /**
     * Start the exam room for a specific HSK level.
     */
    public function start(Request $request, int $level): View|RedirectResponse
    {
        if (! isset(self::LEVEL_SPECS[$level])) {
            return redirect()->route('hsk.mock.index')->with('error', 'Cấp độ HSK không hợp lệ.');
        }

        $spec = self::LEVEL_SPECS[$level];

        // Gather questions: Priority matching hsk_level and skill_type
        $listeningPool = Question::where('is_active', true)
            ->where(function ($q) use ($level) {
                $q->where('hsk_level', $level)->orWhere('hsk_level', '<=', $level);
            })
            ->where('skill_type', 'listening')
            ->inRandomOrder()
            ->take($spec['listening_count'])
            ->get();

        $readingPool = Question::where('is_active', true)
            ->where(function ($q) use ($level) {
                $q->where('hsk_level', $level)->orWhere('hsk_level', '<=', $level);
            })
            ->where('skill_type', 'reading')
            ->inRandomOrder()
            ->take($spec['reading_count'])
            ->get();

        $grammarPool = Question::where('is_active', true)
            ->where(function ($q) use ($level) {
                $q->where('hsk_level', $level)->orWhere('hsk_level', '<=', $level);
            })
            ->where('skill_type', 'grammar')
            ->inRandomOrder()
            ->take($spec['grammar_count'])
            ->get();

        // If any section pool is smaller than required, supplement from general active questions
        if ($listeningPool->count() < $spec['listening_count']) {
            $needed = $spec['listening_count'] - $listeningPool->count();
            $supplement = Question::where('is_active', true)
                ->where('skill_type', 'listening')
                ->whereNotIn('id', $listeningPool->pluck('id'))
                ->inRandomOrder()
                ->take($needed)
                ->get();
            $listeningPool = $listeningPool->merge($supplement);
        }

        if ($readingPool->count() < $spec['reading_count']) {
            $needed = $spec['reading_count'] - $readingPool->count();
            $supplement = Question::where('is_active', true)
                ->where('skill_type', 'reading')
                ->whereNotIn('id', $readingPool->pluck('id'))
                ->inRandomOrder()
                ->take($needed)
                ->get();
            $readingPool = $readingPool->merge($supplement);
        }

        if ($grammarPool->count() < $spec['grammar_count']) {
            $needed = $spec['grammar_count'] - $grammarPool->count();
            $supplement = Question::where('is_active', true)
                ->where('skill_type', 'grammar')
                ->whereNotIn('id', $grammarPool->pluck('id'))
                ->inRandomOrder()
                ->take($needed)
                ->get();
            $grammarPool = $grammarPool->merge($supplement);
        }

        // If general questions still needed, fill with any active questions
        $allQuestions = $listeningPool->merge($readingPool)->merge($grammarPool);
        if ($allQuestions->count() < $spec['question_count']) {
            $needed = $spec['question_count'] - $allQuestions->count();
            $more = Question::where('is_active', true)
                ->whereNotIn('id', $allQuestions->pluck('id'))
                ->inRandomOrder()
                ->take($needed)
                ->get();
            $allQuestions = $allQuestions->merge($more);
        }

        $formattedQuestions = $allQuestions->values()->map(function ($q, $index) {
            return [
                'index'          => $index + 1,
                'id'             => $q->id,
                'skill_type'     => $q->skill_type ?? 'reading',
                'skill_name'     => match ($q->skill_type) {
                    'listening' => 'Nghe hiểu',
                    'grammar'   => 'Ngữ pháp',
                    default     => 'Đọc hiểu',
                },
                'question'       => $q->question,
                'pinyin'         => $q->pinyin,
                'audio_text'     => $q->audio_text,
                'options'        => is_array($q->options) ? $q->options : json_decode($q->options ?? '[]', true),
                'difficulty'     => $q->difficulty,
            ];
        });

        return view('hsk.mock.exam', [
            'level'          => $level,
            'spec'           => $spec,
            'questions'      => $formattedQuestions,
            'totalQuestions' => $formattedQuestions->count(),
            'timeLimitSecs'  => $spec['time_limit'] * 60,
        ]);
    }

    /**
     * Submit and evaluate the Mock Test.
     */
    public function submit(Request $request, int $level): JsonResponse
    {
        $validated = $request->validate([
            'answers'          => ['required', 'array'],
            'duration_seconds' => ['required', 'integer', 'min:1'],
        ]);

        $spec = self::LEVEL_SPECS[$level] ?? self::LEVEL_SPECS[1];
        $submittedAnswers = $validated['answers'];
        $questionIds = array_keys($submittedAnswers);

        $questions = Question::whereIn('id', $questionIds)->get()->keyBy('id');

        $listeningTotal = 0;
        $listeningCorrect = 0;
        $readingTotal = 0;
        $readingCorrect = 0;
        $grammarTotal = 0;
        $grammarCorrect = 0;
        $totalCorrect = 0;
        $details = [];

        foreach ($questionIds as $qId) {
            $question = $questions->get($qId);
            if (! $question) {
                continue;
            }

            $skill = $question->skill_type ?? 'reading';
            $userAns = isset($submittedAnswers[$qId]) ? trim((string) $submittedAnswers[$qId]) : '';
            $correctAns = trim((string) $question->correct_answer);
            $isCorrect = ($userAns !== '' && mb_strtolower($userAns) === mb_strtolower($correctAns));

            if ($skill === 'listening') {
                $listeningTotal++;
                if ($isCorrect) $listeningCorrect++;
            } elseif ($skill === 'grammar') {
                $grammarTotal++;
                if ($isCorrect) $grammarCorrect++;
            } else {
                $readingTotal++;
                if ($isCorrect) $readingCorrect++;
            }

            if ($isCorrect) {
                $totalCorrect++;
            }

            $details[] = [
                'id'             => $question->id,
                'skill_type'     => $skill,
                'question'       => $question->question,
                'pinyin'         => $question->pinyin,
                'audio_text'     => $question->audio_text,
                'options'        => $question->options,
                'user_answer'    => $userAns,
                'correct_answer' => $correctAns,
                'is_correct'     => $isCorrect,
                'explanation'    => $question->explanation,
            ];
        }

        $totalQuestions = count($questionIds);

        // Calculate scores scaled to 100 per skill (Total 300 scale)
        $listeningScore = $listeningTotal > 0 ? (int) round(($listeningCorrect / $listeningTotal) * 100) : 100;
        $readingScore = $readingTotal > 0 ? (int) round(($readingCorrect / $readingTotal) * 100) : 100;
        $grammarScore = $grammarTotal > 0 ? (int) round(($grammarCorrect / $grammarTotal) * 100) : 100;

        // Total score out of 300
        $totalScore = $listeningScore + $readingScore + $grammarScore;
        $passed = $totalScore >= $spec['pass_score']; // >= 180 (60%)

        $user = Auth::guard('web')->user();
        $certificateCode = $passed ? MockTest::generateCertificateCode($level) : null;

        $durationSecs = (int) $validated['duration_seconds'];
        $durationMinutes = max(1, (int) round($durationSecs / 60));

        $mockTest = MockTest::create([
            'user_id'            => $user?->id,
            'hsk_level'          => $level,
            'title'              => $spec['title'],
            'total_questions'    => $totalQuestions,
            'correct_answers'    => $totalCorrect,
            'total_score'        => $totalScore,
            'max_score'          => $spec['max_score'],
            'listening_score'    => $listeningScore,
            'reading_score'      => $readingScore,
            'grammar_score'      => $grammarScore,
            'listening_total'    => $listeningTotal,
            'listening_correct'  => $listeningCorrect,
            'reading_total'      => $readingTotal,
            'reading_correct'    => $readingCorrect,
            'grammar_total'      => $grammarTotal,
            'grammar_correct'    => $grammarCorrect,
            'duration_seconds'   => $durationSecs,
            'time_limit_minutes' => $spec['time_limit'],
            'passed'             => $passed,
            'certificate_code'   => $certificateCode,
            'details'            => $details,
            'completed_at'       => now(),
        ]);

        // If student is logged in, log study session
        if ($user && $user->isStudent()) {
            StudySession::create([
                'user_id'          => $user->id,
                'session_type'     => 'quiz',
                'duration_minutes' => $durationMinutes,
                'score'            => (int) round(($totalScore / 300) * 100),
                'started_at'       => now()->subSeconds($durationSecs),
                'completed_at'     => now(),
            ]);
        }

        return response()->json([
            'success'      => true,
            'redirect_url' => route('hsk.mock.result', $mockTest->id),
            'test_id'      => $mockTest->id,
            'passed'       => $passed,
            'total_score'  => $totalScore,
        ]);
    }

    /**
     * Show detailed result and skill breakdown.
     */
    public function result(Request $request, int $id): View
    {
        $test = MockTest::with('user')->findOrFail($id);
        $spec = self::LEVEL_SPECS[$test->hsk_level] ?? self::LEVEL_SPECS[1];

        return view('hsk.mock.result', compact('test', 'spec'));
    }

    /**
     * Public Certificate Verification & Print View.
     */
    public function certificate(Request $request, string $code): View
    {
        $test = MockTest::with('user')->where('certificate_code', $code)->firstOrFail();
        $spec = self::LEVEL_SPECS[$test->hsk_level] ?? self::LEVEL_SPECS[1];

        return view('hsk.mock.certificate', compact('test', 'spec'));
    }
}
