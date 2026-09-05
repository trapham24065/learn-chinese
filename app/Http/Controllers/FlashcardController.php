<?php

namespace App\Http\Controllers;

use App\Models\Flashcard;
use App\Models\Lesson;
use App\Models\StudySession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class FlashcardController extends Controller
{
    private const PER_PAGE_GRID = 12;   // cards shown in static grid
    private const PER_PAGE_DECK = 20;   // cards loaded per batch in Alpine deck

    public function index(Request $request): View
    {
        $lessons = Lesson::query()
            ->where('is_published', true)
            ->orderBy('hsk_level')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->withCount('flashcards')
            ->get();

        $lessonsByLevel = $lessons->where('flashcards_count', '>', 0)->groupBy('hsk_level');

        $availableHskLevels = Flashcard::query()
            ->where('is_active', true)
            ->whereNotNull('hsk_level')
            ->distinct()
            ->orderBy('hsk_level')
            ->pluck('hsk_level')
            ->all();

        if (empty($availableHskLevels)) {
            $availableHskLevels = [1, 2, 3];
        }

        $lessonSlug  = $request->query('lesson');
        $hskLevel    = $request->query('hsk');
        $search      = $request->query('q');
        $isStarred   = $request->boolean('starred') || $request->query('starred') === '1';
        $activeLesson = null;

        $user = Auth::guard('web')->user();
        $starredCount = $user ? $user->starredFlashcards()->count() : 0;

        $query = Flashcard::query()
            ->where('flashcards.is_active', true)
            ->with('lesson');

        $now = now()->toDateTimeString();

        if ($user) {
            $query->leftJoin('flashcard_progresses', function ($join) use ($user) {
                $join->on('flashcards.id', '=', 'flashcard_progresses.flashcard_id')
                     ->where('flashcard_progresses.user_id', '=', $user->id);
            })
            ->select('flashcards.*', 'flashcard_progresses.is_starred as is_starred')
            ->orderByRaw('CASE WHEN flashcard_progresses.next_review_at <= ? THEN 0 WHEN flashcard_progresses.next_review_at IS NULL THEN 1 ELSE 2 END', [$now])
            ->orderBy('flashcard_progresses.next_review_at')
            ->orderBy('flashcards.sort_order');

            if ($isStarred) {
                $query->where('flashcard_progresses.is_starred', true);
            }
        } else {
            if ($isStarred) {
                $query->whereRaw('1 = 0');
            }
            $query->orderBy('flashcards.sort_order');
        }

        if ($lessonSlug) {
            $activeLesson = $lessons->firstWhere('slug', $lessonSlug);
            if ($activeLesson) {
                $query->where('flashcards.lesson_id', $activeLesson->id);
            }
        } elseif ($hskLevel) {
            $query->where('flashcards.hsk_level', (int) $hskLevel);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('flashcards.hanzi', 'like', "%{$search}%")
                  ->orWhere('flashcards.pinyin', 'like', "%{$search}%")
                  ->orWhere('flashcards.meaning', 'like', "%{$search}%");
            });
        }

        // First batch for Alpine deck (initial load only)
        $deckBatch = (clone $query)
            ->limit(self::PER_PAGE_DECK)
            ->get()
            ->values()
            ->map(fn ($f) => [
                'id'              => $f->id,
                'hanzi'           => $f->hanzi,
                'pinyin'          => $f->pinyin,
                'meaning'         => $f->meaning,
                'example'         => $f->example,
                'example_pinyin'  => $f->example_pinyin,
                'example_meaning' => $f->example_meaning,
                'lesson'          => $f->lesson?->title ?? 'Chung',
                'lesson_id'       => $f->lesson_id,
                'is_starred'      => (bool) ($f->is_starred ?? false),
            ]);

        // Total for deck so JS knows if there are more batches
        $deckTotal = (clone $query)->count();

        // Paginated grid
        $flashcards = $query->paginate(self::PER_PAGE_GRID)->withQueryString();

        $totalCount = Flashcard::where('is_active', true)->count();

        return view('flashcards', compact(
            'flashcards', 'lessons', 'lessonSlug', 'hskLevel', 'search', 'isStarred', 'starredCount',
            'activeLesson', 'totalCount', 'deckBatch', 'deckTotal', 'lessonsByLevel', 'availableHskLevels'
        ));
    }

    /**
     * JSON endpoint: load next batch of deck cards.
     * GET /flashcards/cards?offset=20&lesson=slug&hsk=1&starred=1
     */
    public function cards(Request $request): JsonResponse
    {
        $offset     = (int) $request->query('offset', 0);
        $lessonSlug = $request->query('lesson');
        $hskLevel   = $request->query('hsk');
        $search     = $request->query('q');
        $isStarred  = $request->boolean('starred') || $request->query('starred') === '1';

        $query = Flashcard::query()
            ->where('flashcards.is_active', true);

        $user = Auth::guard('web')->user();
        $now = now()->toDateTimeString();

        if ($user) {
            $query->leftJoin('flashcard_progresses', function ($join) use ($user) {
                $join->on('flashcards.id', '=', 'flashcard_progresses.flashcard_id')
                     ->where('flashcard_progresses.user_id', '=', $user->id);
            })
            ->select('flashcards.*', 'flashcard_progresses.is_starred as is_starred')
            ->orderByRaw('CASE WHEN flashcard_progresses.next_review_at <= ? THEN 0 WHEN flashcard_progresses.next_review_at IS NULL THEN 1 ELSE 2 END', [$now])
            ->orderBy('flashcard_progresses.next_review_at')
            ->orderBy('flashcards.sort_order');

            if ($isStarred) {
                $query->where('flashcard_progresses.is_starred', true);
            }
        } else {
            if ($isStarred) {
                $query->whereRaw('1 = 0');
            }
            $query->orderBy('flashcards.sort_order');
        }

        if ($lessonSlug) {
            $lesson = Lesson::where('slug', $lessonSlug)->first();
            if ($lesson) $query->where('flashcards.lesson_id', $lesson->id);
        } elseif ($hskLevel) {
            $query->where('flashcards.hsk_level', (int) $hskLevel);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('flashcards.hanzi', 'like', "%{$search}%")
                  ->orWhere('flashcards.pinyin', 'like', "%{$search}%")
                  ->orWhere('flashcards.meaning', 'like', "%{$search}%");
            });
        }

        $cards = $query->skip($offset)->take(self::PER_PAGE_DECK)->get()
            ->map(fn ($f) => [
                'id'              => $f->id,
                'hanzi'           => $f->hanzi,
                'pinyin'          => $f->pinyin,
                'meaning'         => $f->meaning,
                'example'         => $f->example,
                'example_pinyin'  => $f->example_pinyin,
                'example_meaning' => $f->example_meaning,
                'lesson'          => $f->lesson?->title ?? 'Chung',
                'lesson_id'       => $f->lesson_id,
                'is_starred'      => (bool) ($f->is_starred ?? false),
            ]);

        return response()->json([
            'cards'   => $cards,
            'total'   => $query->count(),
            'offset'  => $offset,
        ]);
    }

    public function toggleStar(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'flashcard_id' => ['required', 'exists:flashcards,id'],
        ]);

        $user = Auth::guard('web')->user();
        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đăng nhập để lưu từ vựng vào Sổ tay yêu thích.',
            ], 401);
        }

        $progress = $user->flashcardProgresses()->firstOrCreate(
            ['flashcard_id' => $validated['flashcard_id']],
            ['repetition' => 0, 'ease_factor' => 2.5, 'interval' => 0, 'is_starred' => false]
        );

        $progress->is_starred = ! $progress->is_starred;
        $progress->save();

        $starredCount = $user->starredFlashcards()->count();

        return response()->json([
            'success' => true,
            'is_starred' => (bool) $progress->is_starred,
            'starred_count' => $starredCount,
            'message' => $progress->is_starred 
                ? 'Đã thêm vào Sổ tay từ vựng ⭐' 
                : 'Đã xóa khỏi Sổ tay từ vựng',
        ]);
    }

    public function logSession(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'duration_minutes' => ['required', 'integer', 'min:1', 'max:180'],
            'cards_reviewed'   => ['nullable', 'integer', 'min:0'],
            'lesson_id'        => ['nullable', 'exists:lessons,id'],
        ]);

        $student = Auth::guard('web')->user();
        if (! $student) {
            return response()->json(['success' => false, 'message' => 'Chưa đăng nhập.'], 401);
        }

        StudySession::create([
            'user_id'          => $student->id,
            'lesson_id'        => $validated['lesson_id'] ?? null,
            'session_type'     => 'flashcard',
            'duration_minutes' => $validated['duration_minutes'],
            'score'            => null,
            'started_at'       => now()->subMinutes($validated['duration_minutes']),
            'completed_at'     => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Ghi nhận phiên Flashcard thành công! (+' . $validated['duration_minutes'] . ' phút)',
            'streak'  => $student->calculateStreak(),
        ]);
    }

    public function review(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'flashcard_id' => 'required|exists:flashcards,id',
            'quality'      => 'required|in:known,review',
        ]);

        $user = Auth::guard('web')->user();
        if (!$user) {
            return response()->json(['success' => false], 401);
        }

        $progress = $user->flashcardProgresses()->firstOrCreate(
            ['flashcard_id' => $validated['flashcard_id']],
            ['repetition' => 0, 'ease_factor' => 2.5, 'interval' => 0]
        );

        if ($validated['quality'] === 'known') {
            $progress->repetition++;
            if ($progress->repetition === 1) {
                $progress->interval = 1;
            } elseif ($progress->repetition === 2) {
                $progress->interval = 6;
            } else {
                $progress->interval = (int) round($progress->interval * $progress->ease_factor);
            }
            $progress->next_review_at = now()->addDays($progress->interval);
        } else {
            $progress->repetition = 0;
            $progress->interval = 0;
            $progress->ease_factor = max(1.3, $progress->ease_factor - 0.2);
            $progress->next_review_at = now(); // Ôn lại ngay lập tức
        }

        $progress->save();

        return response()->json(['success' => true, 'next_review_at' => $progress->next_review_at]);
    }
}
