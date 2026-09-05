<?php

namespace App\Http\Controllers;

use App\Models\Flashcard;
use App\Models\Lesson;
use App\Models\LessonProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class HskController extends Controller
{
    /** HSK level metadata */
    public static array $levels = [
        1 => [
            'label'       => 'HSK 1',
            'description' => 'Sơ cấp – Giao tiếp cơ bản hàng ngày',
            'vocab_count' => 150,
            'color'       => '#16a34a',  // green
            'bg'          => 'from-green-50 to-emerald-50',
            'border'      => 'border-green-200',
            'badge'       => 'bg-green-100 text-green-800',
        ],
        2 => [
            'label'       => 'HSK 2',
            'description' => 'Sơ cấp cao – Biểu đạt đơn giản trong cuộc sống',
            'vocab_count' => 300,
            'color'       => '#2563eb',  // blue
            'bg'          => 'from-blue-50 to-sky-50',
            'border'      => 'border-blue-200',
            'badge'       => 'bg-blue-100 text-blue-800',
        ],
        3 => [
            'label'       => 'HSK 3',
            'description' => 'Trung cấp thấp – Giao tiếp trong hầu hết tình huống',
            'vocab_count' => 600,
            'color'       => '#d97706',  // amber
            'bg'          => 'from-amber-50 to-yellow-50',
            'border'      => 'border-amber-200',
            'badge'       => 'bg-amber-100 text-amber-800',
        ],
        4 => [
            'label'       => 'HSK 4',
            'description' => 'Trung cấp – Thảo luận về nhiều chủ đề rộng',
            'vocab_count' => 1200,
            'color'       => '#ea580c',  // orange
            'bg'          => 'from-orange-50 to-red-50',
            'border'      => 'border-orange-200',
            'badge'       => 'bg-orange-100 text-orange-800',
        ],
        5 => [
            'label'       => 'HSK 5',
            'description' => 'Cao cấp – Đọc báo, xem phim không cần phụ đề',
            'vocab_count' => 2500,
            'color'       => '#9333ea',  // purple
            'bg'          => 'from-purple-50 to-violet-50',
            'border'      => 'border-purple-200',
            'badge'       => 'bg-purple-100 text-purple-800',
        ],
        6 => [
            'label'       => 'HSK 6',
            'description' => 'Thành thạo – Hiểu và diễn đạt lưu loát hoàn toàn',
            'vocab_count' => 5000,
            'color'       => '#be123c',  // rose
            'bg'          => 'from-rose-50 to-red-50',
            'border'      => 'border-rose-200',
            'badge'       => 'bg-rose-100 text-rose-800',
        ],
    ];

    public function overview(): View
    {
        $student = Auth::guard('web')->user();

        // C3: Collapse 24 separate queries into 3 aggregate GROUP BY queries
        $flashcardCounts = Flashcard::where('is_active', true)
            ->selectRaw('hsk_level, count(*) as total')
            ->groupBy('hsk_level')
            ->pluck('total', 'hsk_level');

        $lessonCounts = Lesson::where('is_published', true)
            ->selectRaw('hsk_level, count(*) as total')
            ->groupBy('hsk_level')
            ->pluck('total', 'hsk_level');

        $completedCounts = collect();
        if ($student) {
            $completedCounts = LessonProgress::query()
                ->join('lessons', 'lessons.id', '=', 'lesson_progresses.lesson_id')
                ->where('lesson_progresses.user_id', $student->id)
                ->where('lesson_progresses.status', 'completed')
                ->where('lessons.is_published', true)
                ->selectRaw('lessons.hsk_level, count(*) as total')
                ->groupBy('lessons.hsk_level')
                ->pluck('total', 'lessons.hsk_level');
        }

        $levelData = [];
        foreach (self::$levels as $level => $meta) {
            $flashcardCount = $flashcardCounts->get($level, 0);
            $lessonCount    = $lessonCounts->get($level, 0);
            $completedCount = $completedCounts->get($level, 0);

            $levelData[$level] = array_merge($meta, [
                'level'           => $level,
                'flashcard_count' => $flashcardCount,
                'lesson_count'    => $lessonCount,
                'completed_count' => $completedCount,
                'progress_pct'    => $lessonCount > 0 ? round(($completedCount / $lessonCount) * 100) : 0,
            ]);
        }

        return view('hsk.overview', compact('levelData', 'student'));
    }

    public function show(int $level): View
    {
        abort_if($level < 1 || $level > 6, 404);

        $meta     = self::$levels[$level];
        $student  = Auth::guard('web')->user();

        $lessons = Lesson::where('hsk_level', $level)
            ->where('is_published', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->withCount(['questions', 'flashcards'])
            ->get();

        $flashcardsQuery = Flashcard::where('hsk_level', $level)
            ->where('flashcards.is_active', true)
            ->with('lesson');

        if ($student) {
            $flashcardsQuery->leftJoin('flashcard_progresses', function ($join) use ($student) {
                $join->on('flashcards.id', '=', 'flashcard_progresses.flashcard_id')
                     ->where('flashcard_progresses.user_id', '=', $student->id);
            })
            ->select('flashcards.*', 'flashcard_progresses.is_starred as is_starred');
        }

        $flashcards = $flashcardsQuery->orderBy('flashcards.sort_order')->paginate(24);

        // Per-lesson progress for this student
        $progressMap = [];
        if ($student) {
            $progressMap = LessonProgress::where('user_id', $student->id)
                ->whereIn('lesson_id', $lessons->pluck('id'))
                ->pluck('status', 'lesson_id')
                ->toArray();
        }

        $prevLevel = $level > 1 ? $level - 1 : null;
        $nextLevel = $level < 6 ? $level + 1 : null;

        return view('hsk.show', compact(
            'level', 'meta', 'student',
            'lessons', 'flashcards', 'progressMap',
            'prevLevel', 'nextLevel'
        ));
    }
}
