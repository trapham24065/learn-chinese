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
            'label'          => 'HSK 1',
            'mountain_cn'    => '初入江山',
            'mountain_vi'    => 'Sơ Nhập Giang Sơn',
            'mountain_sub'   => 'Khởi đầu bước vào thế giới tiếng Trung',
            'mountain_quote' => '千里之行，始于足下',
            'description'    => 'Sơ cấp – Giao tiếp cơ bản hàng ngày',
            'vocab_count'    => 150,
            'color'          => '#16a34a',
            'bg'             => 'from-emerald-50 to-teal-50',
            'border'         => 'border-emerald-200',
            'badge'          => 'bg-emerald-100 text-emerald-800',
        ],
        2 => [
            'label'          => 'HSK 2',
            'mountain_cn'    => '踏雪寻径',
            'mountain_vi'    => 'Đạp Tuyết Tầm Kính',
            'mountain_sub'   => 'Tự mình tìm đường · Cầu nối vạn dặm',
            'mountain_quote' => '读书破万卷，下笔如有神',
            'description'    => 'Sơ cấp cao – Biểu đạt đơn giản trong cuộc sống',
            'vocab_count'    => 300,
            'color'          => '#2563eb',
            'bg'             => 'from-blue-50 to-sky-50',
            'border'         => 'border-blue-200',
            'badge'          => 'bg-blue-100 text-blue-800',
        ],
        3 => [
            'label'          => 'HSK 3',
            'mountain_cn'    => '行舟入远',
            'mountain_vi'    => 'Hành Chu Nhập Viễn',
            'mountain_sub'   => 'Thuận buồm xuôi gió · Đi xa hơn bằng ngôn ngữ',
            'mountain_quote' => '长风破浪会有时，直挂云帆济沧海',
            'description'    => 'Trung cấp thấp – Giao tiếp trong hầu hết tình huống',
            'vocab_count'    => 600,
            'color'          => '#d97706',
            'bg'             => 'from-amber-50 to-yellow-50',
            'border'         => 'border-amber-200',
            'badge'          => 'bg-amber-100 text-amber-800',
        ],
        4 => [
            'label'          => 'HSK 4',
            'mountain_cn'    => '登高望远',
            'mountain_vi'    => 'Đăng Cao Vọng Viễn',
            'mountain_sub'   => 'Tầm nhìn rộng mở · Tự tin luận đàm',
            'mountain_quote' => '欲穷千里目，更上一层楼',
            'description'    => 'Trung cấp – Thảo luận về nhiều chủ đề rộng',
            'vocab_count'    => 1200,
            'color'          => '#ea580c',
            'bg'             => 'from-orange-50 to-red-50',
            'border'         => 'border-orange-200',
            'badge'          => 'bg-orange-100 text-orange-800',
        ],
        5 => [
            'label'          => 'HSK 5',
            'mountain_cn'    => '游历山河',
            'mountain_vi'    => 'Du Lịch Sơn Hà',
            'mountain_sub'   => 'Ngôn ngữ là công cụ khám phá văn hóa & tư duy',
            'mountain_quote' => '读万卷书，行万里路',
            'description'    => 'Cao cấp – Đọc báo, xem phim không cần phụ đề',
            'vocab_count'    => 2500,
            'color'          => '#9333ea',
            'bg'             => 'from-purple-50 to-violet-50',
            'border'         => 'border-purple-200',
            'badge'          => 'bg-purple-100 text-purple-800',
        ],
        6 => [
            'label'          => 'HSK 6',
            'mountain_cn'    => '观天地',
            'mountain_vi'    => 'Quan Thiên Địa',
            'mountain_sub'   => 'Cảnh giới tự do lưu loát · Thấu suốt càn khôn',
            'mountain_quote' => '会当凌绝顶，一览众山小',
            'description'    => 'Thành thạo – Hiểu và diễn đạt lưu loát hoàn toàn',
            'vocab_count'    => 5000,
            'color'          => '#be123c',
            'bg'             => 'from-rose-50 to-red-50',
            'border'         => 'border-rose-200',
            'badge'          => 'bg-rose-100 text-rose-800',
        ],
    ];

    public static function getLevelData($student = null): array
    {
        // Collapse queries into 3 aggregate GROUP BY queries
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

        return $levelData;
    }

    public function overview(): View
    {
        $student = Auth::guard('web')->user();
        $levelData = self::getLevelData($student);

        $totalCurriculumVocab   = array_sum(array_column($levelData, 'vocab_count'));
        $totalCurriculumLessons = array_sum(array_column($levelData, 'lesson_count'));
        $totalCompletedLessons  = array_sum(array_column($levelData, 'completed_count'));
        $overallProgressPct     = $totalCurriculumLessons > 0 ? round(($totalCompletedLessons / $totalCurriculumLessons) * 100) : 0;

        return view('hsk.overview', compact(
            'levelData', 'student',
            'totalCurriculumVocab', 'totalCurriculumLessons', 'totalCompletedLessons', 'overallProgressPct'
        ));
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
            ->with(['flashcards' => function ($q) {
                $q->where('is_active', true)->select('id', 'lesson_id', 'hanzi', 'pinyin', 'meaning');
            }])
            ->paginate(6, ['*'], 'lessons_page')
            ->withQueryString();

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

        $flashcards = $flashcardsQuery->orderBy('flashcards.sort_order')
            ->paginate(24, ['*'], 'cards_page')
            ->withQueryString();

        // Level-wide completion stats
        $totalLessonsCount = Lesson::where('hsk_level', $level)->where('is_published', true)->count();
        $completedLessonsCount = 0;
        if ($student) {
            $completedLessonsCount = LessonProgress::query()
                ->join('lessons', 'lessons.id', '=', 'lesson_progresses.lesson_id')
                ->where('lesson_progresses.user_id', $student->id)
                ->where('lesson_progresses.status', 'completed')
                ->where('lessons.is_published', true)
                ->where('lessons.hsk_level', $level)
                ->count();
        }
        $levelProgressPct = $totalLessonsCount > 0 ? round(($completedLessonsCount / $totalLessonsCount) * 100) : 0;
        $isLevelCompleted = ($student && $totalLessonsCount > 0 && $completedLessonsCount >= $totalLessonsCount);

        // Per-lesson progress for this student
        $progressMap = [];
        $inProgressLesson = null;
        if ($student) {
            $progressRecords = LessonProgress::where('user_id', $student->id)
                ->whereIn('lesson_id', $lessons->pluck('id'))
                ->get();

            foreach ($progressRecords as $rec) {
                $progressMap[$rec->lesson_id] = [
                    'status'           => $rec->status,
                    'percent'          => $rec->progress_percent,
                    'last_accessed_at' => $rec->last_accessed_at,
                ];
            }

            // Find most recently accessed in-progress lesson in this HSK level
            $inProgressLesson = Lesson::where('hsk_level', $level)
                ->where('is_published', true)
                ->join('lesson_progresses', 'lessons.id', '=', 'lesson_progresses.lesson_id')
                ->where('lesson_progresses.user_id', $student->id)
                ->where('lesson_progresses.status', 'in_progress')
                ->select('lessons.*', 'lesson_progresses.progress_percent', 'lesson_progresses.last_accessed_at')
                ->orderByDesc('lesson_progresses.last_accessed_at')
                ->first();
        }

        $prevLevel = $level > 1 ? $level - 1 : null;
        $nextLevel = $level < 6 ? $level + 1 : null;

        return view('hsk.show', compact(
            'level', 'meta', 'student',
            'lessons', 'flashcards', 'progressMap', 'inProgressLesson',
            'prevLevel', 'nextLevel',
            'totalLessonsCount', 'completedLessonsCount', 'levelProgressPct', 'isLevelCompleted'
        ));
    }
}
