<?php

namespace App\Http\Controllers;

use App\Models\LearningActivity;
use App\Models\UserLearningStat;
use App\Services\DailyGoalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class GameController extends Controller
{
    public function __construct(
        protected DailyGoalService $dailyGoalService
    ) {}

    public function index(Request $request): View
    {
        $user = Auth::guard('web')->user();
        $stats = null;
        $dailySummary = null;
        $recentGames = collect();

        if ($user) {
            $stats = UserLearningStat::firstOrCreate(
                ['user_id' => $user->id],
                ['total_xp' => 0, 'current_streak' => 0, 'longest_streak' => 0]
            );

            $dailySummary = $this->dailyGoalService->getTodaySummary($user);

            $recentGames = LearningActivity::where('user_id', $user->id)
                ->whereIn('activity_type', ['fast_match_completed', 'audio_quiz_completed'])
                ->latest()
                ->take(5)
                ->get();
        }

        return view('games.index', compact('user', 'stats', 'dailySummary', 'recentGames'));
    }
}
