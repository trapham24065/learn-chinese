@extends('layouts.app')

@section('title', 'Trang chủ | Learn Chinese')

@section('content')
@php
$stats = [
    ['label' => 'Bài học', 'value' => number_format($lessonCount ?? \App\Models\Lesson::count()), 'note' => 'theo chủ đề'],
    ['label' => 'Flashcard', 'value' => number_format($flashcardCount ?? \App\Models\Flashcard::count()), 'note' => 'từ vựng cốt lõi'],
    ['label' => 'Quiz', 'value' => number_format($questionCount ?? \App\Models\Question::count()), 'note' => 'câu luyện nhanh'],
];

$features = [
    ['title' => 'Bài học ngắn gọn', 'description' => 'Mỗi bài chia thành phần đọc, nghe, từ mới và luyện nhanh.'],
    ['title' => 'Flashcard trực quan', 'description' => 'Ôn tập bằng thẻ từ với nghĩa, pinyin và ví dụ ngắn.'],
    ['title' => 'Theo dõi tiến độ', 'description' => 'Hiển thị streak, điểm quiz và số từ đã học theo ngày.'],
];

if (isset($featuredLessons) && $featuredLessons->isNotEmpty()) {
    $lessonItems = $featuredLessons->map(function ($item) {
        return [
            'title' => $item->title,
            'description' => $item->summary ?? 'Khám phá bài học với từ vựng, ngữ pháp và phát âm chuẩn.',
            'tag' => $item->hsk_level ? 'HSK ' . $item->hsk_level : ucfirst($item->difficulty ?? 'Starter'),
            'url' => route('lesson.show', $item->slug),
        ];
    })->toArray();
} else {
    $lessonItems = [
        ['title' => 'Pinyin cơ bản', 'description' => 'Làm quen với cách đọc, thanh điệu và nhịp phát âm chuẩn ngay từ đầu.', 'tag' => 'Starter', 'url' => route('hsk.overview')],
        ['title' => 'Chào hỏi & giới thiệu', 'description' => 'Nắm những mẫu câu đầu tiên để tự giới thiệu và giao tiếp đơn giản.', 'tag' => 'Conversation', 'url' => route('hsk.overview')],
        ['title' => 'Từ vựng theo chủ đề', 'description' => 'Học từ theo từng nhóm để dễ nhớ hơn: gia đình, đồ ăn, trường học, số đếm.', 'tag' => 'Vocabulary', 'url' => route('hsk.overview')],
    ];
}

$roadmap = [
    ['step' => '01', 'title' => 'Pinyin và phát âm', 'description' => 'Xây nền tảng để đọc đúng và nói tự tin hơn.'],
    ['step' => '02', 'title' => 'Từ vựng nền tảng', 'description' => 'Học từ theo chủ đề để áp dụng ngay vào câu.'],
    ['step' => '03', 'title' => 'Mẫu câu giao tiếp', 'description' => 'Ghép từ vựng thành câu hoàn chỉnh để luyện nói.'],
    ['step' => '04', 'title' => 'Quiz và ôn tập', 'description' => 'Củng cố kiến thức bằng bài kiểm tra ngắn và flashcard.'],
];
@endphp

<section class="grid items-center gap-10 py-4 lg:grid-cols-[1.05fr_0.95fr] lg:py-8" id="hero">
    <div class="max-w-2xl">
        <div class="inline-flex items-center gap-2 rounded-full border border-[#d8b07d] bg-white/75 px-4 py-2 text-sm font-medium text-slate-700 shadow-sm backdrop-blur">
            <span class="h-2.5 w-2.5 rounded-full bg-[#c71f1f]"></span>
            Nền tảng học tiếng Trung hiện đại
        </div>

        <h1 class="mt-6 text-5xl font-black leading-[1.03] tracking-tight text-slate-950 sm:text-6xl lg:text-7xl">
            Học tiếng Trung
            <span class="text-[#991b1b]">gọn, đẹp</span>
            và dễ theo dõi.
        </h1>

        <p class="mt-6 text-lg leading-8 text-slate-700 lg:max-w-xl">
            Từ bài học, flashcard đến quiz, mọi thứ đều được sắp xếp rõ ràng để cậu học mỗi ngày mà không bị rối.
        </p>

        <div class="mt-8 flex flex-wrap gap-3">
            @guest
            <a href="{{ route('register') }}" class="inline-flex items-center gap-1.5 justify-center rounded-full bg-[#991b1b] px-6 py-3 text-sm font-bold text-white shadow-lg shadow-red-950/20 transition hover:-translate-y-0.5 hover:bg-[#7f1717]">
                <i data-lucide="sparkles" class="h-4 w-4 text-amber-300"></i>
                Đăng ký học ngay
            </a>
            @endguest
            <a href="{{ route('flashcards') }}" class="inline-flex items-center justify-center rounded-full border border-slate-300 bg-white/80 px-6 py-3 text-sm font-semibold text-slate-800 shadow-sm backdrop-blur transition hover:-translate-y-0.5 hover:border-[#991b1b] hover:text-[#991b1b]">
                Xem flashcard
            </a>
            <a href="{{ route('quiz') }}" class="inline-flex items-center justify-center rounded-full border border-slate-300 bg-white/80 px-6 py-3 text-sm font-semibold text-slate-800 shadow-sm backdrop-blur transition hover:-translate-y-0.5 hover:border-[#991b1b] hover:text-[#991b1b]">
                Vào quiz
            </a>
        </div>

        <div class="mt-10 grid gap-4 sm:grid-cols-3">
            @foreach ($stats as $stat)
            <div class="rounded-[1.5rem] border border-white/80 bg-white/75 p-5 shadow-xl shadow-slate-900/5 backdrop-blur">
                <p class="text-sm font-semibold uppercase tracking-[0.18em] text-[#991b1b]">{{ $stat['label'] }}</p>
                <p class="mt-3 text-4xl font-black tracking-tight text-slate-950">{{ $stat['value'] }}</p>
                <p class="mt-1 text-sm text-slate-600">{{ $stat['note'] }}</p>
            </div>
            @endforeach
        </div>
    </div>

    <div class="relative">
        <div class="absolute -left-6 top-10 h-24 w-24 rounded-full bg-amber-300/40 blur-2xl"></div>
        <div class="absolute right-2 bottom-4 h-28 w-28 rounded-full bg-rose-300/30 blur-2xl"></div>

        <div class="relative overflow-hidden rounded-[2rem] border border-white/80 bg-slate-950 p-6 text-white shadow-2xl shadow-slate-950/20">
            @auth
            @php
                $user = auth()->user();
                $streak = $sidebarStreak ?? ($user ? $user->calculateStreak() : 0);
                $completedLessons = $user ? $user->lessonProgresses()->where('status', 'completed')->count() : 0;
                $totalLessons = max(1, $lessonCount ?? 1);
                $progressPercent = min(100, (int)round(($completedLessons / $totalLessons) * 100));
                $quizAvg = $user ? (int)round($user->studySessions()->where('session_type', 'quiz')->avg('score') ?? 0) : 0;
                $vocabCount = $user ? $user->flashcardProgresses()->count() : 0;
                $starredCount = $user ? $user->starredFlashcards()->count() : 0;
            @endphp
            {{-- Dynamic Authenticated Dashboard Widget --}}
            <div class="flex items-start justify-between gap-4">
                <div>
                    <span class="inline-flex items-center gap-1.5 text-xs uppercase tracking-[0.24em] text-red-300/90 font-bold">
                        <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        Tiến độ của bạn
                    </span>
                    <h2 class="mt-1 text-2xl font-black text-white">Xin chào, {{ $user->name }}</h2>
                </div>
                <div class="rounded-2xl border border-amber-400/30 bg-amber-400/10 px-3.5 py-2 text-right">
                    <p class="text-[10px] uppercase tracking-[0.2em] text-amber-200">Streak</p>
                    <p class="text-xl font-black text-amber-300 flex items-center justify-end gap-1">
                        <i data-lucide="flame" class="h-4 w-4 text-amber-400 fill-amber-400"></i>
                        {{ str_pad($streak, 2, '0', STR_PAD_LEFT) }}
                    </p>
                </div>
            </div>

            <div class="mt-6 space-y-4">
                {{-- Lesson Progress --}}
                <a href="{{ route('hsk.overview') }}" class="block rounded-3xl border border-white/10 bg-white/5 p-4 transition hover:bg-white/10 hover:border-amber-300/30 group">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-slate-300 font-medium group-hover:text-white transition">Tiến độ bài học HSK</span>
                        <span class="font-bold text-amber-300">{{ $completedLessons }}/{{ $totalLessons }} bài ({{ $progressPercent }}%)</span>
                    </div>
                    <div class="mt-3 h-2 rounded-full bg-white/10 overflow-hidden">
                        <div class="h-2 rounded-full bg-gradient-to-r from-amber-400 to-red-500 transition-all duration-500" style="width: {{ max(4, $progressPercent) }}%"></div>
                    </div>
                    <div class="mt-3 flex items-center justify-between text-xs text-slate-400">
                        <span>Lộ trình HSK 1 - 6</span>
                        <span class="text-amber-300/80 font-semibold group-hover:text-amber-300 transition">Học tiếp →</span>
                    </div>
                </a>

                {{-- Stats 2-Grid --}}
                <div class="grid gap-4 sm:grid-cols-2">
                    <a href="{{ route('quiz') }}" class="rounded-3xl border border-white/10 bg-white/5 p-4 transition hover:bg-white/10 hover:border-amber-300/30 group">
                        <div class="flex items-center justify-between">
                            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Điểm Quiz</p>
                            <i data-lucide="target" class="h-4 w-4 text-slate-400 group-hover:text-amber-300 transition"></i>
                        </div>
                        <p class="mt-2 text-2xl sm:text-3xl font-black text-white">
                            {{ $quizAvg > 0 ? $quizAvg . '%' : 'Mới' }}
                        </p>
                        <p class="mt-1 text-xs text-slate-400 group-hover:text-slate-300 transition">Bấm để luyện đề ngay →</p>
                    </a>

                    <a href="{{ route('flashcards') }}" class="rounded-3xl border border-white/10 bg-white/5 p-4 transition hover:bg-white/10 hover:border-amber-300/30 group">
                        <div class="flex items-center justify-between">
                            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Từ vựng đã học</p>
                            <i data-lucide="layers" class="h-4 w-4 text-slate-400 group-hover:text-amber-300 transition"></i>
                        </div>
                        <p class="mt-2 text-2xl sm:text-3xl font-black text-white">
                            {{ $vocabCount }} <span class="text-xs font-normal text-slate-400">từ</span>
                        </p>
                        <p class="mt-1 text-xs text-slate-400 group-hover:text-slate-300 transition">{{ $starredCount }} từ gắn sao ⭐</p>
                    </a>
                </div>

                {{-- Dashboard Action Button --}}
                <a href="{{ route('dashboard') }}" class="flex items-center justify-between rounded-3xl border border-red-500/30 bg-gradient-to-r from-red-600/30 to-amber-600/20 p-4 text-white transition hover:from-red-600/40 hover:to-amber-600/30 group">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-red-600/80 text-white shadow-md">
                            <i data-lucide="layout-dashboard" class="h-5 w-5"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-white">Dashboard học viên đầy đủ</p>
                            <p class="text-xs text-slate-300">Ghi nhận giờ học, SRS Spaced Repetition & Lịch sử</p>
                        </div>
                    </div>
                    <i data-lucide="arrow-right" class="h-5 w-5 text-amber-300 transition group-hover:translate-x-1"></i>
                </a>
            </div>

            @else
            {{-- Interactive Guest Preview Widget --}}
            <div class="flex items-start justify-between gap-4">
                <div>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-red-500/20 px-2.5 py-0.5 text-[11px] font-bold text-red-300 uppercase tracking-wider">
                        Trải nghiệm mẫu
                    </span>
                    <h2 class="mt-2 text-2xl font-black text-white">Dashboard học tập</h2>
                </div>
                <div class="rounded-2xl border border-white/10 bg-white/10 px-3 py-2 text-right">
                    <p class="text-[10px] uppercase tracking-[0.22em] text-slate-300">Streak mẫu</p>
                    <p class="text-xl font-black text-amber-300 flex items-center justify-end gap-1">
                        <i data-lucide="flame" class="h-4 w-4 text-amber-400 fill-amber-400"></i>
                        07
                    </p>
                </div>
            </div>

            <div class="mt-6 space-y-4">
                <a href="{{ route('hsk.overview') }}" class="block rounded-3xl border border-white/10 bg-white/5 p-4 transition hover:bg-white/10 hover:border-amber-300/30 group">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-slate-300 group-hover:text-white transition">Lộ trình HSK 1 - 6</span>
                        <span class="font-semibold text-amber-300">Khám phá ngay</span>
                    </div>
                    <div class="mt-3 h-2 rounded-full bg-white/10">
                        <div class="h-2 w-[68%] rounded-full bg-gradient-to-r from-amber-300 to-red-400"></div>
                    </div>
                    <p class="mt-3 text-xs text-slate-400 flex items-center justify-between">
                        <span>Chữ Hán: 人, 大, 小, 学...</span>
                        <span class="text-amber-300 group-hover:underline">Bắt đầu học →</span>
                    </p>
                </a>

                <div class="grid gap-4 sm:grid-cols-2">
                    <a href="{{ route('quiz') }}" class="rounded-3xl border border-white/10 bg-white/5 p-4 transition hover:bg-white/10 hover:border-amber-300/30 group">
                        <div class="flex items-center justify-between">
                            <p class="text-xs font-semibold text-slate-300 uppercase tracking-wider">Luyện Quiz</p>
                            <i data-lucide="target" class="h-4 w-4 text-slate-400 group-hover:text-amber-300 transition"></i>
                        </div>
                        <p class="mt-2 text-2xl font-black text-white">9.5 <span class="text-xs font-normal text-slate-400">/ 10</span></p>
                        <p class="mt-1 text-xs text-amber-300 group-hover:underline">Làm bài kiểm tra ngắn →</p>
                    </a>
                    <a href="{{ route('flashcards') }}" class="rounded-3xl border border-white/10 bg-white/5 p-4 transition hover:bg-white/10 hover:border-amber-300/30 group">
                        <div class="flex items-center justify-between">
                            <p class="text-xs font-semibold text-slate-300 uppercase tracking-wider">Flashcard 3D</p>
                            <i data-lucide="layers" class="h-4 w-4 text-slate-400 group-hover:text-amber-300 transition"></i>
                        </div>
                        <p class="mt-2 text-2xl font-black text-white">{{ number_format($flashcardCount ?? 150) }} <span class="text-xs font-normal text-slate-400">từ</span></p>
                        <p class="mt-1 text-xs text-amber-300 group-hover:underline">Lật thẻ từ vựng →</p>
                    </a>
                </div>

                <div class="rounded-3xl border border-amber-300/20 bg-gradient-to-br from-amber-300/15 to-red-500/10 p-4">
                    <div class="flex items-center justify-between">
                        <p class="text-xs uppercase tracking-[0.22em] text-amber-200/90 font-bold">Lưu giữ tiến độ</p>
                        <a href="{{ route('register') }}" class="text-xs font-bold text-amber-300 hover:underline">Tạo tài khoản</a>
                    </div>
                    <p class="mt-2 text-sm text-slate-200 leading-relaxed">
                        Đăng ký tài khoản miễn phí để hệ thống tự động ghi nhận chuỗi Streak, lưu điểm số Quiz và ôn từ vựng theo thuật toán Spaced Repetition!
                    </p>
                    <div class="mt-3 flex items-center gap-2">
                        <a href="{{ route('register') }}" class="inline-flex items-center gap-1.5 rounded-full bg-amber-400 px-4 py-2 text-xs font-bold text-slate-950 transition hover:bg-amber-300">
                            Đăng ký miễn phí
                            <i data-lucide="arrow-right" class="h-3.5 w-3.5"></i>
                        </a>
                        <a href="{{ route('login') }}" class="inline-flex items-center rounded-full bg-white/10 px-4 py-2 text-xs font-semibold text-white transition hover:bg-white/20">
                            Đã có tài khoản? Đăng nhập
                        </a>
                    </div>
                </div>
            </div>
            @endauth
        </div>
    </div>
</section>

<section class="py-8 lg:py-10" id="features">
    <div class="flex items-end justify-between gap-6">
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.28em] text-[#991b1b]">Tính năng</p>
            <h2 class="mt-3 text-3xl font-black tracking-tight text-slate-950 sm:text-4xl">Những phần cốt lõi cho web học tiếng Trung</h2>
        </div>
    </div>

    <div class="mt-8 grid gap-5 lg:grid-cols-3">
        @foreach ($features as $feature)
        <article class="rounded-[1.75rem] border border-white/80 bg-white/75 p-6 shadow-xl shadow-slate-900/5 backdrop-blur">
            <h3 class="text-2xl font-bold text-slate-950">{{ $feature['title'] }}</h3>
            <p class="mt-3 leading-7 text-slate-600">{{ $feature['description'] }}</p>
        </article>
        @endforeach
    </div>
</section>

<section class="py-8 lg:py-10" id="lessons">
    <div class="grid gap-6 lg:grid-cols-[0.95fr_1.05fr]">
        <div class="rounded-[2rem] bg-[#991b1b] p-8 text-white shadow-2xl shadow-red-950/15">
            <p class="text-sm font-semibold uppercase tracking-[0.28em] text-red-100/80">Khóa học</p>
            <h2 class="mt-4 text-3xl font-black tracking-tight">Mở đầu bằng nội dung dễ vào</h2>
            <p class="mt-4 text-white/80 leading-7">
                Mỗi bài học được thiết kế ngắn, tập trung vào điều quan trọng nhất để người học không bị ngợp.
            </p>
        </div>

        <div class="space-y-4">
            @foreach ($lessonItems as $lesson)
            <a href="{{ $lesson['url'] ?? route('hsk.overview') }}" class="group flex gap-4 rounded-[1.5rem] border border-white/80 bg-white/75 p-5 shadow-xl shadow-slate-900/5 backdrop-blur transition hover:-translate-y-0.5 hover:border-[#991b1b]/40 hover:shadow-2xl">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-slate-950 text-white transition group-hover:bg-[#991b1b]">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m6-6H6" />
                    </svg>
                </div>
                <div class="flex-1">
                    <div class="flex flex-wrap items-center gap-3">
                        <h3 class="text-xl font-bold text-slate-950 transition group-hover:text-[#991b1b]">{{ $lesson['title'] }}</h3>
                        <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-amber-900">{{ $lesson['tag'] }}</span>
                    </div>
                    <p class="mt-2 leading-7 text-slate-600">{{ $lesson['description'] }}</p>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</section>

<section class="py-8 lg:py-10" id="roadmap">
    <div class="flex items-end justify-between gap-6">
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.28em] text-[#991b1b]">Lộ trình</p>
            <h2 class="mt-3 text-3xl font-black tracking-tight text-slate-950 sm:text-4xl">4 chặng học dễ theo dõi</h2>
        </div>
    </div>

    <div class="mt-8 grid gap-4 lg:grid-cols-2">
        @foreach ($roadmap as $item)
        <div class="flex gap-4 rounded-[1.5rem] border border-white/80 bg-white/75 p-5 shadow-xl shadow-slate-900/5 backdrop-blur">
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-slate-950 text-sm font-black text-white">
                {{ $item['step'] }}
            </div>
            <div>
                <h3 class="text-xl font-bold text-slate-950">{{ $item['title'] }}</h3>
                <p class="mt-2 leading-7 text-slate-600">{{ $item['description'] }}</p>
            </div>
        </div>
        @endforeach
    </div>
</section>
@endsection