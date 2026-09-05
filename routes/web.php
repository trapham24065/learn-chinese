<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FlashcardController;
use App\Http\Controllers\HskController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\StoryReadingController;
use App\Http\Controllers\TTSController;
use App\Http\Controllers\DictionaryController;
use App\Http\Controllers\HskMockTestController;
use App\Models\Flashcard;
use App\Models\Lesson;
use App\Models\Question;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

// C10: Cache only scalar stats (24h) — never cache Eloquent objects (serialization issues)
Route::get('/', function () {
    if (auth()->guard('web')->check()) {
        return redirect()->route('dashboard');
    }

    $lessonCount    = Cache::remember('home_lesson_count',    86400, fn () => Lesson::count());
    $flashcardCount = Cache::remember('home_flashcard_count', 86400, fn () => Flashcard::count());
    $questionCount  = Cache::remember('home_question_count',  86400, fn () => Question::count());
    // featuredLessons NOT cached — Eloquent Collections don't deserialize safely across deployments
    $featuredLessons = Lesson::where('is_published', true)->orderBy('sort_order')->take(3)->get();

    $student = null;
    $levelData = HskController::getLevelData($student);

    return view('welcome', compact('lessonCount', 'flashcardCount', 'questionCount', 'featuredLessons', 'levelData', 'student'));
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
Route::post('/flashcards/toggle-star', [FlashcardController::class, 'toggleStar'])->name('flashcards.toggleStar');

Route::get('/quiz', [QuizController::class, 'index'])->name('quiz');
Route::post('/quiz/submit', [QuizController::class, 'submit'])->name('quiz.submit');

Route::get('/dictionary', [DictionaryController::class, 'index'])->name('dictionary.index');
Route::get('/dictionary/search', [DictionaryController::class, 'search'])->name('dictionary.search');

Route::prefix('hsk/mock-test')->group(function () {
    Route::get('/', [HskMockTestController::class, 'index'])->name('hsk.mock.index');
    Route::get('/{level}', [HskMockTestController::class, 'start'])->where('level', '[1-6]')->name('hsk.mock.start');
    Route::post('/{level}/submit', [HskMockTestController::class, 'submit'])->where('level', '[1-6]')->name('hsk.mock.submit');
    Route::get('/result/{id}', [HskMockTestController::class, 'result'])->name('hsk.mock.result');
    Route::get('/certificate/{code}', [HskMockTestController::class, 'certificate'])->name('hsk.mock.certificate');
});

Route::post('/tts', [TTSController::class, 'generate'])->name('tts.generate');

Route::get('/hsk', [HskController::class, 'overview'])->name('hsk.overview');
Route::get('/hsk/{level}', [HskController::class, 'show'])->where('level', '[1-6]')->name('hsk.show');

Route::get('/lessons/{slug}', [LessonController::class, 'show'])->name('lesson.show');

// Graded Reader (Phòng Luyện Đọc Hiểu)
Route::get('/reading', [StoryReadingController::class, 'index'])->name('stories.index');
Route::get('/reading/{slug}', [StoryReadingController::class, 'show'])->name('stories.show');
Route::post('/reading/lookup', [StoryReadingController::class, 'lookup'])->name('stories.lookup');
Route::post('/reading/{id}/complete', [StoryReadingController::class, 'complete'])->name('stories.complete');

require __DIR__.'/auth.php';


