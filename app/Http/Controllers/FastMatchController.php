<?php

namespace App\Http\Controllers;

use App\Models\Flashcard;
use App\Models\Lesson;
use App\Services\GameService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class FastMatchController extends Controller
{
    public function __construct(
        protected GameService $gameService
    ) {}

    public function index(Request $request): View
    {
        $hskLevels = Flashcard::query()
            ->where('is_active', true)
            ->whereNotNull('hsk_level')
            ->distinct()
            ->orderBy('hsk_level')
            ->pluck('hsk_level')
            ->all();

        if (empty($hskLevels)) {
            $hskLevels = [1, 2, 3];
        }

        $lessons = Lesson::query()
            ->where('is_published', true)
            ->orderBy('sort_order')
            ->take(15)
            ->get(['id', 'title', 'hsk_level']);

        return view('games.fast-match', [
            'hskLevels' => $hskLevels,
            'lessons' => $lessons,
            'user' => Auth::guard('web')->user(),
        ]);
    }

    public function sessionData(Request $request): JsonResponse
    {
        $mode = $request->query('mode', 'practice');
        if (!in_array($mode, ['practice', 'time_challenge', 'hanzi_pinyin'])) {
            $mode = 'practice';
        }

        $difficulty = $request->query('difficulty', 'medium');
        if (!in_array($difficulty, ['easy', 'medium', 'challenge'])) {
            $difficulty = 'medium';
        }

        $hskLevel = $request->has('hsk') && is_numeric($request->query('hsk'))
            ? (int) $request->query('hsk')
            : null;

        $lessonId = $request->has('lesson_id') && is_numeric($request->query('lesson_id'))
            ? (int) $request->query('lesson_id')
            : null;

        $session = $this->gameService->createFastMatchSession(
            mode: $mode,
            difficulty: $difficulty,
            hskLevel: $hskLevel,
            lessonId: $lessonId,
            user: Auth::guard('web')->user()
        );

        return response()->json([
            'success' => true,
            'data' => $session,
        ]);
    }

    public function finish(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'token' => 'required|string',
            'moves' => 'nullable|array',
            'duration_seconds' => 'required|integer|min:1|max:1200',
            'guest_uuid' => 'nullable|string',
            'claim_token' => 'nullable|string',
        ]);

        try {
            $result = $this->gameService->verifyFastMatchSession(
                token: $validated['token'],
                moves: $validated['moves'] ?? [],
                durationSeconds: (int) $validated['duration_seconds'],
                user: Auth::guard('web')->user(),
                guestUuid: $validated['guest_uuid'] ?? null,
                claimToken: $validated['claim_token'] ?? null
            );

            return response()->json($result);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
