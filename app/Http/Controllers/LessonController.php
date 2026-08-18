<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
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
        if ($user) {
            $flashcardSessions = $user->studySessions()->where('lesson_id', $lesson->id)->where('session_type', 'flashcard')->orderByDesc('completed_at')->take(3)->get();
            $quizSessions = $user->studySessions()->where('lesson_id', $lesson->id)->where('session_type', 'quiz')->orderByDesc('completed_at')->take(3)->get();
        }

        return view('lessons.show', compact('lesson', 'flashcardSessions', 'quizSessions'));
    }
}
