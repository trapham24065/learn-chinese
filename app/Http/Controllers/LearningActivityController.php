<?php

namespace App\Http\Controllers;

use App\Services\LearningActivityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LearningActivityController extends Controller
{
    public function __construct(
        protected LearningActivityService $activityService
    ) {}

    /**
     * Log a student learning activity, update XP, streak, and daily goal progress.
     */
    public function log(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated',
            ], 401);
        }

        $validated = $request->validate([
            'activity_type' => ['required', 'string', 'max:50'],
            'source_type' => ['nullable', 'string', 'max:50'],
            'source_id' => ['nullable', 'integer'],
            'idempotency_key' => ['nullable', 'string', 'max:120'],
            'meta' => ['nullable', 'array'],
        ]);

        $result = $this->activityService->logActivity(
            $user,
            $validated['activity_type'],
            $validated
        );

        return response()->json($result);
    }
}
