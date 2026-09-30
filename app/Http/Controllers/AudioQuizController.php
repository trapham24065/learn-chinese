<?php

namespace App\Http\Controllers;

use App\Models\Flashcard;
use App\Services\GameService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AudioQuizController extends Controller
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

        return view('games.audio-quiz', [
            'hskLevels' => $hskLevels,
            'user' => Auth::guard('web')->user(),
        ]);
    }

    public function sessionData(Request $request): JsonResponse
    {
        $mode = $request->query('mode', 'vocabulary');
        if (!in_array($mode, ['vocabulary', 'minimal_pairs', 'tone_recognition'])) {
            $mode = 'vocabulary';
        }

        $hskLevel = $request->has('hsk') && is_numeric($request->query('hsk'))
            ? (int) $request->query('hsk')
            : null;

        $session = $this->gameService->createAudioQuizSession(
            mode: $mode,
            hskLevel: $hskLevel,
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
            'answers' => 'required|array',
            'duration_seconds' => 'required|integer|min:1|max:1200',
            'guest_uuid' => 'nullable|string',
            'claim_token' => 'nullable|string',
        ]);

        try {
            $result = $this->gameService->verifyAudioQuizSession(
                token: $validated['token'],
                answers: $validated['answers'],
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
