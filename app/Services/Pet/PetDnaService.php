<?php
namespace App\Services\Pet;

use App\Models\FlashcardProgress;
use App\Models\StudySession;
use App\Models\User;
use App\Models\UserPet;
use Illuminate\Support\Facades\DB;

class PetDnaService
{
    public function compute(User $user, UserPet $userPet): array
    {
        $totalFlashcards = FlashcardProgress::where('user_id', $user->id)->count();
        $reviewedCards   = FlashcardProgress::where('user_id', $user->id)
            ->where('repetition', '>=', 1)->count();
        $flashcardScore  = $totalFlashcards > 0
            ? min(100, (int) round(($reviewedCards / max(1, $totalFlashcards)) * 100))
            : 0;

        $masteredCount   = FlashcardProgress::where('user_id', $user->id)
            ->where('repetition', '>=', 2)->count();
        $vocabScore      = min(100, $masteredCount * 2);

        $quizScore = 0;
        $consistencyScore = 0;

        $totalSessions = StudySession::where('user_id', $user->id)->count();
        $quizSessions  = StudySession::where('user_id', $user->id)
            ->where('session_type', 'quiz')->count();
        $quizScore = $totalSessions > 0
            ? min(100, (int) round(($quizSessions / $totalSessions) * 100))
            : 0;

        $activeDaysLast30 = StudySession::where('user_id', $user->id)
            ->where('started_at', '>=', now()->subDays(30))
            ->selectRaw('DATE(started_at) as day')
            ->groupBy('day')
            ->get()
            ->count();
        $consistencyScore = min(100, (int) round(($activeDaysLast30 / 30) * 100));

        $stat = $user->learningStats ?? null;
        if ($stat && isset($stat->current_streak)) {
            $consistencyScore = max($consistencyScore, min(100, $stat->current_streak * 3));
        }

        $listeningActivities = DB::table('learning_activities')
            ->where('user_id', $user->id)
            ->whereIn('activity_type', ['audio_quiz', 'audio_quiz_completed', 'listening'])
            ->count();
        $listeningScore = min(100, $listeningActivities * 5);

        $struggledButPersisted = FlashcardProgress::where('user_id', $user->id)
            ->where('ease_factor', '<', 2.0)
            ->where('repetition', '>=', 2)
            ->count();
        $resilienceScore = min(100, $struggledButPersisted * 5);

        $scores = [
            'scholar'    => $vocabScore,
            'listener'   => $listeningScore,
            'explorer'   => $quizScore,
            'resilient'  => $resilienceScore,
            'consistent' => $consistencyScore,
        ];
        arsort($scores);
        $dominantTrait = array_key_first($scores);

        $personalityMap = [
            'scholar'    => 'calm',
            'listener'   => 'curious',
            'explorer'   => 'playful',
            'resilient'  => 'cheerful',
            'consistent' => 'shy',
        ];
        $newPersonality = $personalityMap[$dominantTrait] ?? 'playful';

        $dna = [
            'flashcard'      => $flashcardScore,
            'vocabulary'     => $vocabScore,
            'quiz'           => $quizScore,
            'listening'      => $listeningScore,
            'consistency'    => $consistencyScore,
            'resilience'     => $resilienceScore,
            'dominant_trait' => $dominantTrait,
            'computed_at'    => now()->toIso8601String(),
        ];

        $dominantScore = $scores[$dominantTrait];
        $secondScore   = array_values($scores)[1] ?? 0;
        $clearlyDominant = ($dominantScore - $secondScore) >= 15;

        $userPet->update([
            'learning_dna' => $dna,
            'personality'  => $clearlyDominant ? $newPersonality : ($userPet->personality ?? 'playful'),
        ]);

        return $dna;
    }

    public function getDisplayDna(UserPet $userPet): array
    {
        $dna = $userPet->learning_dna ?? [];
        if (empty($dna)) {
            return [];
        }

        return [
            ['key' => 'vocabulary',  'label' => 'Từ vựng',    'score' => $dna['vocabulary']  ?? 0, 'color' => '#6366f1'],
            ['key' => 'flashcard',   'label' => 'Flashcard',  'score' => $dna['flashcard']   ?? 0, 'color' => '#ec4899'],
            ['key' => 'quiz',        'label' => 'Quiz',       'score' => $dna['quiz']        ?? 0, 'color' => '#f59e0b'],
            ['key' => 'listening',   'label' => 'Listening',  'score' => $dna['listening']   ?? 0, 'color' => '#10b981'],
            ['key' => 'consistency', 'label' => 'Kiên trì',   'score' => $dna['consistency'] ?? 0, 'color' => '#3b82f6'],
            ['key' => 'resilience',  'label' => 'Kiên cường', 'score' => $dna['resilience']  ?? 0, 'color' => '#ef4444'],
        ];
    }
}
