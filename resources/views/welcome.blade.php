@extends('layouts.app')

@section('title', 'Trang chủ | Learn Chinese')

@section('content')
@php
$stats = [
    ['label' => 'Cấp độ HSK', 'value' => 'HSK 1 - 6', 'note' => 'chuẩn Hanban'],
    ['label' => 'Flashcard', 'value' => number_format($flashcardCount ?? \App\Models\Flashcard::count()), 'note' => 'thuật toán SRS'],
    ['label' => 'Bài học cốt lõi', 'value' => number_format($lessonCount ?? \App\Models\Lesson::count()), 'note' => 'ngắn gọn, dễ nhớ'],
];

$highlights = [
    [
        'icon' => 'book-open',
        'title' => 'Bài học chia nhỏ',
        'desc' => 'Mỗi bài học 5 - 10 phút, tập trung vào Pinyin, ngữ cảnh và mẫu câu giao tiếp tự nhiên.',
    ],
    [
        'icon' => 'layers',
        'title' => 'Thẻ nhớ thông minh (SRS)',
        'desc' => 'Thuật toán lặp lại ngắt quãng nhắc ôn đúng lúc bạn sắp quên, không cần học vẹt.',
    ],
    [
        'icon' => 'sparkles',
        'title' => 'Bạn Pet đồng hành',
        'desc' => 'Bé Pet lớn lên theo từng từ vựng bạn làm chủ, luôn khích lệ và đồng hành mỗi ngày.',
    ],
];
@endphp

{{-- ========================================================================= --}}
{{-- 1. HERO SECTION: Light, Warm & Emotionally Resonant                        --}}
{{-- ========================================================================= --}}
<section class="grid items-center gap-8 py-4 lg:grid-cols-[1.1fr_0.9fr] lg:gap-12 lg:py-8" id="hero">
    {{-- Left Column: Brand Message & Direct CTAs --}}
    <div class="max-w-2xl">
        <div class="inline-flex items-center gap-2 rounded-full border border-amber-200/80 bg-white/80 px-4 py-1.5 text-xs font-semibold text-amber-900 shadow-sm backdrop-blur">
            <span class="flex h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
            Không gian học tiếng Trung tinh gọn & nhẹ nhàng
        </div>

        <h1 class="mt-5 text-4xl font-black leading-[1.08] tracking-tight text-slate-950 sm:text-5xl lg:text-6xl">
            Học tiếng Trung
            <span class="text-[#991b1b]">thong thả,</span><br class="hidden sm:inline" />
            đúng nhịp mỗi ngày.
        </h1>

        <p class="mt-5 text-base leading-relaxed text-slate-600 sm:text-lg sm:leading-8">
            Không áp lực dồn dập, không tràn ngập tính năng máy móc. Chỉ tập trung vào bài học cốt lõi, flashcard ghi nhớ sâu và người bạn Pet nhỏ luôn ở bên cạnh bạn.
        </p>

        {{-- Action Buttons --}}
        <div class="mt-8 flex flex-wrap items-center gap-3">
            @guest
            <a href="{{ route('register') }}"
               class="inline-flex items-center justify-center gap-2 rounded-full bg-[#991b1b] px-6 py-3.5 text-sm font-bold text-white shadow-lg shadow-red-950/15 transition hover:-translate-y-0.5 hover:bg-[#7f1717]">
                <i data-lucide="sparkles" class="h-4 w-4 text-amber-300"></i>
                Bắt đầu học thử miễn phí
            </a>
            @else
            <a href="{{ route('dashboard') }}"
               class="inline-flex items-center justify-center gap-2 rounded-full bg-[#991b1b] px-6 py-3.5 text-sm font-bold text-white shadow-lg shadow-red-950/15 transition hover:-translate-y-0.5 hover:bg-[#7f1717]">
                <i data-lucide="layout-dashboard" class="h-4 w-4 text-amber-300"></i>
                Vào Dashboard học tập
            </a>
            @endguest

            <a href="#hsk-roadmap"
               class="inline-flex items-center justify-center gap-2 rounded-full border border-slate-200 bg-white/90 px-5 py-3.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:-translate-y-0.5 hover:border-[#991b1b] hover:text-[#991b1b]">
                <i data-lucide="compass" class="h-4 w-4 text-slate-400"></i>
                Lộ trình HSK 1 - 6
            </a>

            <a href="{{ route('flashcards') }}"
               class="inline-flex items-center justify-center gap-2 rounded-full border border-slate-200 bg-white/90 px-5 py-3.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:-translate-y-0.5 hover:border-[#991b1b] hover:text-[#991b1b]">
                <i data-lucide="layers" class="h-4 w-4 text-slate-400"></i>
                Xem Thẻ ghi nhớ
            </a>
        </div>

        {{-- Gentle Stat Chips --}}
        <div class="mt-10 grid grid-cols-3 gap-3 sm:gap-4">
            @foreach ($stats as $stat)
            <div class="rounded-2xl border border-white/80 bg-white/70 p-4 shadow-sm backdrop-blur transition hover:bg-white/90">
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">{{ $stat['label'] }}</p>
                <p class="mt-1 text-2xl font-black tracking-tight text-slate-900 sm:text-3xl">{{ $stat['value'] }}</p>
                <p class="mt-0.5 text-xs text-slate-500">{{ $stat['note'] }}</p>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Right Column: Gentle Live Preview Card (Matching New Dashboard) --}}
    <div class="relative">
        <div class="absolute -left-6 top-10 h-28 w-28 rounded-full bg-amber-200/40 blur-3xl pointer-events-none"></div>
        <div class="absolute right-0 bottom-6 h-32 w-32 rounded-full bg-red-200/30 blur-3xl pointer-events-none"></div>

        <div class="relative overflow-hidden rounded-[2.25rem] border border-amber-200/60 bg-gradient-to-b from-white via-white to-[#fbf7f2] p-6 shadow-xl shadow-slate-900/5 sm:p-7">
            {{-- Top Mini Status Bar --}}
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div class="flex items-center gap-3">
                    <div class="grid h-10 w-10 place-items-center rounded-2xl bg-amber-50 border border-amber-200 text-xl shadow-sm">
                        🐉
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-900">Bé Long Lân • Đồng hành</p>
                        <p class="text-[11px] text-emerald-600 font-medium flex items-center gap-1">
                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            "Hôm nay mình cùng học 10 phút nhé!"
                        </p>
                    </div>
                </div>
                <span class="rounded-full bg-amber-100/80 px-2.5 py-1 text-[11px] font-bold text-amber-800">
                    Trải nghiệm mẫu
                </span>
            </div>

            {{-- 3 Core Learning Modules Preview --}}
            <div class="mt-5 space-y-3.5">
                {{-- Module 1: Lesson in Progress --}}
                <div class="rounded-2xl border border-slate-100 bg-[#fbf7f2]/80 p-4 transition hover:border-amber-200">
                    <div class="flex items-center justify-between text-xs">
                        <span class="font-bold text-[#991b1b] uppercase tracking-wider flex items-center gap-1.5">
                            <i data-lucide="book-open" class="h-3.5 w-3.5"></i>
                            Bài học hôm nay
                        </span>
                        <span class="rounded-full bg-red-100 px-2 py-0.5 text-[10px] font-bold text-[#991b1b]">HSK 1</span>
                    </div>
                    <h3 class="mt-2 text-base font-black text-slate-900">Bài 1: 你好 - Xin chào</h3>
                    <p class="mt-1 text-xs text-slate-500 leading-relaxed">
                        Làm quen phát âm chuẩn, thanh điệu cơ bản và mẫu câu chào hỏi đầu tiên.
                    </p>
                    <div class="mt-3 flex items-center justify-between text-xs pt-2 border-t border-slate-200/60">
                        <span class="text-slate-400">5 từ vựng • 1 bài tập</span>
                        <a href="{{ route('hsk.show', 1) }}" class="font-bold text-[#991b1b] hover:underline flex items-center gap-1">
                            Học thử ngay <i data-lucide="arrow-right" class="h-3 w-3"></i>
                        </a>
                    </div>
                </div>

                {{-- Module 2: SRS Flashcard Snapshot --}}
                <div class="grid grid-cols-2 gap-3">
                    <a href="{{ route('flashcards') }}" class="rounded-2xl border border-slate-100 bg-white p-3.5 shadow-sm transition hover:border-amber-300 group">
                        <div class="flex items-center justify-between text-[11px] text-slate-400">
                            <span>Thẻ từ cốt lõi</span>
                            <i data-lucide="layers" class="h-3.5 w-3.5 text-amber-500"></i>
                        </div>
                        <p class="mt-2 text-2xl font-black text-slate-900 group-hover:text-[#991b1b] transition">学</p>
                        <p class="text-xs font-semibold text-slate-600">xué • Học tập</p>
                        <p class="mt-1 text-[10px] text-slate-400">Lặp lại ngắt quãng</p>
                    </a>

                    <a href="{{ route('quiz') }}" class="rounded-2xl border border-slate-100 bg-white p-3.5 shadow-sm transition hover:border-amber-300 group">
                        <div class="flex items-center justify-between text-[11px] text-slate-400">
                            <span>Luyện nhanh</span>
                            <i data-lucide="target" class="h-3.5 w-3.5 text-emerald-500"></i>
                        </div>
                        <p class="mt-2 text-2xl font-black text-slate-900 group-hover:text-[#991b1b] transition">Quiz</p>
                        <p class="text-xs font-semibold text-slate-600">3 - 5 câu ngắn</p>
                        <p class="mt-1 text-[10px] text-slate-400">Củng cố sau bài học</p>
                    </a>
                </div>

                {{-- Module 3: Friendly Sign-up Callout --}}
                <div class="rounded-2xl border border-amber-200/80 bg-gradient-to-br from-amber-50 to-orange-50/60 p-4">
                    <p class="text-xs font-bold text-slate-800">
                        🌱 Bạn hoàn toàn có thể học thử ngay mà không cần tạo tài khoản.
                    </p>
                    <p class="mt-1 text-[11px] text-slate-600 leading-relaxed">
                        Khi muốn lưu tiến độ bài học, nhận thức ăn nuôi Pet và ghi nhận cột mốc, hãy đăng ký một tài khoản miễn phí nhé.
                    </p>
                    <div class="mt-3 flex items-center gap-2">
                        @guest
                        <a href="{{ route('register') }}"
                           class="inline-flex items-center gap-1 rounded-xl bg-slate-900 px-3.5 py-2 text-xs font-bold text-white transition hover:bg-slate-800">
                            Đăng ký miễn phí
                            <i data-lucide="arrow-right" class="h-3 w-3 text-amber-300"></i>
                        </a>
                        <a href="{{ route('login') }}"
                           class="inline-flex items-center rounded-xl border border-slate-300 bg-white/80 px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-white">
                            Đã có tài khoản?
                        </a>
                        @else
                        <a href="{{ route('dashboard') }}"
                           class="inline-flex items-center gap-1 rounded-xl bg-[#991b1b] px-3.5 py-2 text-xs font-bold text-white transition hover:bg-[#7f1717]">
                            Vào học tiếp nào
                            <i data-lucide="arrow-right" class="h-3 w-3"></i>
                        </a>
                        @endguest
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ========================================================================= --}}
{{-- 2. CORE VALUES: 3 Simple Highlights (Replaces noisy feature lists)        --}}
{{-- ========================================================================= --}}
<section class="py-8 lg:py-12" id="values">
    <div class="max-w-2xl">
        <p class="text-xs font-bold uppercase tracking-[0.24em] text-[#991b1b]">Trải nghiệm học tập</p>
        <h2 class="mt-2 text-2xl font-black tracking-tight text-slate-950 sm:text-3xl">
            Nhẹ nhàng, bền bỉ và không gây áp lực
        </h2>
        <p class="mt-2 text-sm text-slate-600">
            Học ngoại ngữ là một hành trình dài. Learn Chinese được thiết kế để mỗi ngày bạn đều muốn quay lại học một chút.
        </p>
    </div>

    <div class="mt-6 grid gap-5 md:grid-cols-3">
        @foreach ($highlights as $h)
        <div class="rounded-3xl border border-white/80 bg-white/80 p-6 shadow-sm backdrop-blur transition hover:-translate-y-0.5 hover:shadow-md">
            <div class="grid h-10 w-10 place-items-center rounded-xl bg-amber-50 text-[#991b1b] border border-amber-200/80">
                <i data-lucide="{{ $h['icon'] }}" class="h-5 w-5"></i>
            </div>
            <h3 class="mt-4 text-base font-bold text-slate-900">{{ $h['title'] }}</h3>
            <p class="mt-2 text-xs leading-relaxed text-slate-600">{{ $h['desc'] }}</p>
        </div>
        @endforeach
    </div>
</section>

{{-- ========================================================================= --}}
{{-- 3. HSK ROADMAP: Elegant, Clean 6-Level Overview                          --}}
{{-- ========================================================================= --}}
<section class="py-8 lg:py-12" id="hsk-roadmap">
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-[0.22em] text-[#991b1b]">
                <i data-lucide="graduation-cap" class="h-4 w-4"></i>
                Hệ thống chuẩn quốc tế
            </div>
            <h2 class="mt-2 text-2xl font-black tracking-tight text-slate-950 sm:text-3xl">
                Lộ trình HSK (HSK 1 – HSK 6)
            </h2>
            <p class="mt-2 max-w-2xl text-sm text-slate-600">
                Lộ trình từ vựng và ngữ pháp bám sát khung chuẩn Hanban, phân bổ theo độ khó tăng dần để bạn tiến bộ vững chắc.
            </p>
        </div>
        <a href="{{ route('hsk.mock.index') }}"
           class="inline-flex shrink-0 items-center gap-2 rounded-2xl bg-white border border-slate-200 px-4 py-2.5 text-xs font-bold text-slate-800 shadow-sm transition hover:border-[#991b1b] hover:text-[#991b1b]">
            <i data-lucide="award" class="h-4 w-4 text-amber-500"></i>
            <span>Thi thử HSK Online</span>
        </a>
    </div>

    @if(isset($levelData) && count($levelData) > 0)
    <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach($levelData as $level => $data)
        @php
            $chineseNum = ['一','二','三','四','五','六'][$level - 1] ?? $level;
        @endphp
        <a href="{{ route('hsk.show', $level) }}"
           class="group relative flex flex-col justify-between overflow-hidden rounded-3xl border bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-lg {{ $data['border'] }}">
            {{-- Top Accent Line --}}
            <div class="absolute inset-x-0 top-0 h-1" style="background: {{ $data['color'] }}"></div>

            <div>
                <div class="flex items-center justify-between">
                    <span class="rounded-full px-2.5 py-0.5 text-[11px] font-black uppercase tracking-wider {{ $data['badge'] }}">
                        {{ $data['label'] }}
                    </span>
                    <span class="text-3xl font-black select-none text-slate-100 group-hover:text-slate-200 transition" style="color: {{ $data['color'] }}1A">
                        {{ $chineseNum }}
                    </span>
                </div>

                <h3 class="mt-3 text-lg font-black text-slate-900">{{ $data['label'] }}</h3>
                <p class="mt-1 text-xs leading-relaxed text-slate-600 line-clamp-2">{{ $data['description'] }}</p>
            </div>

            <div class="mt-4 pt-3 border-t border-slate-100">
                <div class="flex items-center justify-between text-xs text-slate-500">
                    <span>~{{ number_format($data['vocab_count']) }} từ vựng</span>
                    <span>{{ $data['lesson_count'] }} bài học</span>
                </div>
                <div class="mt-2.5 flex items-center gap-1 text-xs font-bold transition group-hover:gap-2" style="color: {{ $data['color'] }}">
                    Khám phá cấp độ <i data-lucide="arrow-right" class="h-3.5 w-3.5"></i>
                </div>
            </div>
        </a>
        @endforeach
    </div>
    @endif

    {{-- Subtle Mock Test Callout --}}
    <div class="mt-8 rounded-3xl border border-amber-200/80 bg-gradient-to-r from-amber-50/80 via-white to-red-50/50 p-6 sm:p-7 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="max-w-xl">
                <div class="inline-flex items-center gap-1.5 text-xs font-bold text-amber-800 uppercase tracking-wider">
                    <i data-lucide="clock" class="h-3.5 w-3.5 text-amber-600"></i>
                    Định dạng Hanban
                </div>
                <h3 class="mt-1.5 text-lg font-black text-slate-900">
                    Bạn muốn thử thách trình độ với đề thi thử HSK?
                </h3>
                <p class="mt-1 text-xs text-slate-600 leading-relaxed">
                    Làm bài thi thử có tính giờ tự động, chấm điểm tức thì phần Nghe & Đọc kèm giấy chứng nhận hoàn thành online.
                </p>
            </div>
            <a href="{{ route('hsk.mock.index') }}"
               class="inline-flex shrink-0 items-center justify-center gap-2 rounded-2xl bg-slate-900 px-5 py-3 text-xs font-bold text-white shadow-sm transition hover:bg-[#991b1b]">
                <span>Làm bài thi thử</span>
                <i data-lucide="arrow-right" class="h-3.5 w-3.5"></i>
            </a>
        </div>
    </div>
</section>

{{-- ========================================================================= --}}
{{-- 4. WARM INVITATION: Gentle Closing CTA                                   --}}
{{-- ========================================================================= --}}
<section class="py-8 lg:py-10">
    <div class="rounded-[2.25rem] bg-slate-950 p-8 sm:p-10 text-white shadow-xl shadow-slate-950/10 text-center relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 h-40 w-40 rounded-full bg-red-600/20 blur-3xl pointer-events-none"></div>
        <div class="absolute -left-10 -top-10 h-40 w-40 rounded-full bg-amber-400/20 blur-3xl pointer-events-none"></div>

        <div class="relative z-10 max-w-xl mx-auto">
            <div class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3.5 py-1 text-xs font-bold text-amber-300 border border-white/10">
                <i data-lucide="sparkles" class="h-3.5 w-3.5 text-amber-300"></i>
                Đồng hành mỗi ngày
            </div>
            <h2 class="mt-4 text-2xl sm:text-3xl font-black tracking-tight text-white">
                Sẵn sàng bắt đầu hành trình của bạn?
            </h2>
            <p class="mt-3 text-xs sm:text-sm text-slate-300 leading-relaxed">
                Chỉ cần 10 phút mỗi ngày, cùng bé Pet khám phá những con chữ Hán đầu tiên thật nhẹ nhàng và hứng khởi.
            </p>
            <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
                @guest
                <a href="{{ route('register') }}"
                   class="inline-flex items-center gap-2 rounded-full bg-[#991b1b] px-6 py-3 text-xs sm:text-sm font-bold text-white shadow-lg transition hover:bg-[#7f1717] hover:-translate-y-0.5">
                    Tạo tài khoản miễn phí
                    <i data-lucide="arrow-right" class="h-4 w-4"></i>
                </a>
                <a href="{{ route('login') }}"
                   class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-5 py-3 text-xs sm:text-sm font-semibold text-white transition hover:bg-white/20">
                    Đăng nhập
                </a>
                @else
                <a href="{{ route('dashboard') }}"
                   class="inline-flex items-center gap-2 rounded-full bg-[#991b1b] px-6 py-3 text-xs sm:text-sm font-bold text-white shadow-lg transition hover:bg-[#7f1717] hover:-translate-y-0.5">
                    Quay lại Dashboard học tiếp
                    <i data-lucide="arrow-right" class="h-4 w-4"></i>
                </a>
                @endguest
            </div>
        </div>
    </div>
</section>
@endsection