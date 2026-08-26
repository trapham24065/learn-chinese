<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FlashcardController;
use App\Http\Controllers\HskController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\TTSController;
use App\Models\Flashcard;
use App\Models\Lesson;
use App\Models\Question;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $lessonCount = Lesson::count();
    $flashcardCount = Flashcard::count();
    $questionCount = Question::count();
    $featuredLessons = Lesson::where('is_published', true)
        ->orderBy('sort_order')
        ->take(3)
        ->get();

    return view('welcome', compact('lessonCount', 'flashcardCount', 'questionCount', 'featuredLessons'));
})->name('home');

Route::redirect('/dashboard', '/student/dashboard');

Route::prefix('student')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware(['auth', 'verified', 'student'])
        ->name('dashboard');

    Route::middleware(['auth', 'student'])->group(function () {
        Route::post('/progress/update', [DashboardController::class, 'updateProgress'])->name('student.progress.update');
        Route::post('/session/log', [DashboardController::class, 'logSession'])->name('student.session.log');
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });
});

Route::redirect('/profile', '/student/profile');

Route::get('/flashcards', [FlashcardController::class, 'index'])->name('flashcards');
Route::get('/flashcards/cards', [FlashcardController::class, 'cards'])->name('flashcards.cards');
Route::post('/flashcards/session', [FlashcardController::class, 'logSession'])->name('flashcards.session');
Route::post('/flashcards/review', [FlashcardController::class, 'review'])->name('flashcards.review');

Route::get('/quiz', [QuizController::class, 'index'])->name('quiz');
Route::post('/quiz/submit', [QuizController::class, 'submit'])->name('quiz.submit');

Route::post('/tts', [TTSController::class, 'generate'])->name('tts.generate');

Route::get('/hsk', [HskController::class, 'overview'])->name('hsk.overview');
Route::get('/hsk/{level}', [HskController::class, 'show'])->where('level', '[1-6]')->name('hsk.show');

Route::get('/lessons/{slug}', [LessonController::class, 'show'])->name('lesson.show');

require __DIR__.'/auth.php';
