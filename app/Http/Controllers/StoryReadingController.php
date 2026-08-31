<?php

namespace App\Http\Controllers;

use App\Models\Flashcard;
use App\Models\Story;
use App\Models\StoryProgress;
use App\Models\StudySession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class StoryReadingController extends Controller
{
    /**
     * Display the Graded Reader library page.
     */
    public function index(Request $request): View
    {
        $selectedLevel = $request->has('level') && $request->level !== '' ? (int) $request->level : null;
        $selectedCategory = $request->input('category');
        $search = $request->input('search');

        $query = Story::query()->where('is_published', true);

        if ($selectedLevel) {
            $query->where('hsk_level', $selectedLevel);
        }

        if ($selectedCategory && $selectedCategory !== 'all') {
            $query->where('category', $selectedCategory);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('title_vi', 'like', "%{$search}%")
                  ->orWhere('title_pinyin', 'like', "%{$search}%")
                  ->orWhere('summary', 'like', "%{$search}%");
            });
        }

        $stories = $query->orderBy('hsk_level')
            ->orderBy('id')
            ->get();

        $user = Auth::guard('web')->user();
        $completedStoryIds = collect();
        if ($user) {
            $completedStoryIds = StoryProgress::where('user_id', $user->id)
                ->where('is_completed', true)
                ->pluck('story_id');
        }

        $allPublished = Story::where('is_published', true)->get();
        $categories = $allPublished->pluck('category')->unique()->values();

        $completedStories = $allPublished->whereIn('id', $completedStoryIds);
        $stats = [
            'total_stories'     => $allPublished->count(),
            'completed_count'   => $completedStoryIds->count(),
            'total_words_read'  => $completedStories->sum('word_count'),
            'total_read_mins'   => $completedStories->sum('estimated_reading_minutes'),
        ];

        return view('stories.index', compact(
            'stories',
            'stats',
            'selectedLevel',
            'selectedCategory',
            'categories',
            'completedStoryIds'
        ));
    }

    /**
     * Display a specific interactive story reading room.
     */
    public function show(Request $request, string $slug): View
    {
        $story = Story::where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        $user = Auth::guard('web')->user();
        $userProgress = $user ? $story->userProgress($user->id) : null;

        // Fetch user's starred flashcards characters for instant highlight / star state
        $starredCharacters = [];
        if ($user) {
            $starredCharacters = $user->starredFlashcards()->pluck('hanzi')->toArray();
        }

        // Recommended / Related stories in the same level or category
        $relatedStories = Story::where('is_published', true)
            ->where('id', '!=', $story->id)
            ->where(function ($q) use ($story) {
                $q->where('hsk_level', $story->hsk_level)
                  ->orWhere('category', $story->category);
            })
            ->take(3)
            ->get();

        return view('stories.show', compact(
            'story',
            'userProgress',
            'starredCharacters',
            'relatedStories'
        ));
    }

    /**
     * AJAX 1-Click dictionary lookup for Chinese characters.
     */
    public function lookup(Request $request): JsonResponse
    {
        $character = trim((string) $request->input('character', ''));
        if ($character === '') {
            return response()->json(['found' => false]);
        }

        // 1. Direct exact match in flashcards
        $card = Flashcard::where('hanzi', $character)->first();

        // 2. If not found and longer than 1 char, search partial or pinyin
        if (! $card && mb_strlen($character) > 1) {
            $card = Flashcard::where('hanzi', 'like', "%{$character}%")->first();
        }

        $user = Auth::guard('web')->user();
        $isStarred = false;
        if ($card && $user) {
            $isStarred = $user->starredFlashcards()->where('flashcard_id', $card->id)->exists();
        }

        if ($card) {
            return response()->json([
                'found'        => true,
                'id'           => $card->id,
                'character'    => $card->hanzi,
                'pinyin'       => $card->pinyin,
                'meaning'      => $card->meaning,
                'hsk_level'    => $card->hsk_level,
                'example'      => $card->example,
                'example_pinyin' => $card->example_pinyin,
                'example_meaning' => $card->example_meaning,
                'is_starred'   => $isStarred,
            ]);
        }

        return response()->json([
            'found'     => false,
            'character' => $character,
        ]);
    }

    /**
     * Mark story reading as completed & record study session.
     */
    public function complete(Request $request, int $id): JsonResponse
    {
        $story = Story::findOrFail($id);
        $user = Auth::guard('web')->user();

        $quizScore = $request->has('quiz_score') ? (int) $request->input('quiz_score') : 100;

        if ($user) {
            StoryProgress::updateOrCreate(
                [
                    'user_id'  => $user->id,
                    'story_id' => $story->id,
                ],
                [
                    'is_completed' => true,
                    'quiz_score'   => $quizScore,
                    'last_read_at' => now(),
                ]
            );

            // If student, log study session for streaks and analytics
            if ($user->isStudent()) {
                StudySession::create([
                    'user_id'          => $user->id,
                    'session_type'     => 'lesson',
                    'duration_minutes' => max(1, $story->estimated_reading_minutes),
                    'score'            => $quizScore,
                    'started_at'       => now()->subMinutes($story->estimated_reading_minutes),
                    'completed_at'     => now(),
                ]);
            }
        }

        return response()->json([
            'success'  => true,
            'message'  => 'Chúc mừng bạn đã hoàn thành bài đọc!',
            'story_id' => $story->id,
        ]);
    }
}
