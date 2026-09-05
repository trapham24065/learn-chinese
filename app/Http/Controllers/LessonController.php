<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\LessonProgress;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class LessonController extends Controller
{
    public function show(Request $request, string $slug): View
    {
        $lesson = Lesson::query()
            ->where('slug', $slug)
            ->where('is_published', true)
            ->withCount(['flashcards' => fn($q) => $q->where('is_active', true)])
            ->withCount(['questions' => fn($q) => $q->where('is_active', true)])
            ->firstOrFail();

        $user = Auth::guard('web')->user();
        
        $flashcardSessions = collect();
        $quizSessions = collect();
        $progress = null;

        if ($user) {
            $flashcardSessions = $user->studySessions()->where('lesson_id', $lesson->id)->where('session_type', 'flashcard')->orderByDesc('completed_at')->take(3)->get();
            $quizSessions = $user->studySessions()->where('lesson_id', $lesson->id)->where('session_type', 'quiz')->orderByDesc('completed_at')->take(3)->get();

            $progress = LessonProgress::firstOrCreate(
                ['user_id' => $user->id, 'lesson_id' => $lesson->id],
                [
                    'status' => 'in_progress',
                    'progress_percent' => 20,
                    'started_at' => now(),
                    'last_accessed_at' => now(),
                ]
            );

            if ($progress->status === 'not_started') {
                $progress->status = 'in_progress';
                $progress->progress_percent = max(20, $progress->progress_percent);
                $progress->started_at = $progress->started_at ?? now();
            }

            $progress->last_accessed_at = now();
            $progress->save();
        }

        return view('lessons.show', compact('lesson', 'flashcardSessions', 'quizSessions', 'progress'));
    }
}
