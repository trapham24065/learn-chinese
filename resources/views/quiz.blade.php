@extends('layouts.app')

@section('title', 'Khoa Cử Trường Thi · 科举殿试 | Chinese Deck')

@section('content')
<div x-data="quizApp()" x-init="initQuiz()" class="space-y-8 pb-16 font-serif-custom">

    {{-- Top Ambience Banner & Toolbar --}}
    <section class="relative overflow-hidden rounded-[2.25rem] border border-amber-900/20 bg-gradient-to-br from-[#faf7f0] via-[#f7f2e7] to-[#f4ebe1] p-6 sm:p-8 shadow-xl shadow-amber-950/5">
        {{-- Subtle Chinese Watermark --}}
        <div class="pointer-events-none absolute -right-6 -bottom-8 select-none text-[9rem] font-serif font-black leading-none text-amber-900/[0.04]">
            科举
        </div>

        <div class="relative z-10 flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
            <div class="max-w-2xl">
                {{-- Breadcrumb / Topic Tag --}}
                <div class="flex flex-wrap items-center gap-2 mb-3">
                    <span class="seal-zhusha-round text-xs font-bold tracking-widest">
                        科 举 · 殿 试
                    </span>
                    @if ($selectedLesson)
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-100/80 border border-amber-300/60 px-3 py-1 text-xs font-semibold text-amber-900">
                            <i data-lucide="book-open" class="h-3 w-3 text-amber-800"></i>
                            <span>{{ $selectedLesson->title }}</span>
                        </span>
                    @elseif ($selectedLevel)
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-100/80 border border-amber-300/60 px-3 py-1 text-xs font-semibold text-amber-900">
                            <span>HSK {{ $selectedLevel }} · Quyển Khảo Thí</span>
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-100/80 border border-amber-300/60 px-3 py-1 text-xs font-semibold text-amber-900">
                            <span>Toàn Thư Quán · Tổng hợp</span>
                        </span>
                    @endif

                    <span class="inline-flex items-center gap-1.5 rounded-full bg-white/80 border border-amber-900/10 px-3 py-1 text-xs font-medium text-slate-700 shadow-2xs">
                        <i data-lucide="scroll" class="h-3 w-3 text-amber-800"></i>
                        <span>{{ count($questions) }} đạo thi đề / {{ $totalPoolCount }} câu</span>
                    </span>
                </div>

                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight text-slate-900 font-serif">
                    Khoa Cử Trường Thi · 科举殿试
                </h1>
                <p class="mt-2.5 text-sm sm:text-base leading-relaxed text-slate-700">
                    <span class="italic text-amber-950 font-medium">"Tĩnh tâm · Hạ bút · Giải đề · Đề danh"</span> — Rời xa xô bồ, ngồi trước thư án tĩnh lặng, lần lượt hoá giải từng đạo đề thi để thấu tỏ Hán tự và văn nghĩa.
                </p>

                {{-- Action Quick-bar --}}
                <div class="mt-4 flex flex-wrap items-center gap-2.5">
                    <a href="{{ request()->fullUrl() }}"
                       class="inline-flex items-center gap-2 rounded-full border border-amber-900/20 bg-white/90 px-4 py-2 text-xs sm:text-sm font-semibold text-slate-700 shadow-2xs transition hover:border-[#991b1b] hover:text-[#991b1b] hover:bg-white active:scale-95">
                        <i data-lucide="refresh-cw" class="h-3.5 w-3.5"></i>
                        <span>Đổi bộ đề thi mới</span>
                    </a>
                    
                    <div x-data="{ sfx: window.soundEngine?.isSfxEnabled() ?? true }">
                        <button type="button" @click="sfx = window.soundEngine?.toggleSfx()" 
                                class="inline-flex items-center gap-1.5 rounded-full border px-3.5 py-2 text-xs font-bold transition shadow-2xs"
                                :class="sfx ? 'bg-amber-100/90 text-amber-900 border-amber-300' : 'bg-white text-slate-400 border-slate-200'"
                                title="Bật/Tắt chuông khánh & âm thanh bút mực">
                            <i data-lucide="volume-2" class="h-3.5 w-3.5"></i>
                            <span x-text="sfx ? 'Âm thanh: Bật' : 'Âm thanh: Tắt'"></span>
                        </button>
                    </div>

                    <a href="{{ route('flashcards', $selectedLesson ? ['lesson' => $selectedLesson->slug] : []) }}"
                       class="inline-flex items-center gap-1.5 rounded-full border border-amber-900/15 bg-amber-50/60 px-3.5 py-2 text-xs font-semibold text-amber-950 hover:bg-amber-100 transition">
                        <i data-lucide="bookmark" class="h-3.5 w-3.5 text-amber-800"></i>
                        <span>Xem Thư Thiêm trước</span>
                    </a>
                </div>
            </div>

            {{-- Thư Án Tiến Độ (Cuộn Đề Tổng Thể) --}}
            <div class="w-full lg:w-80 shrink-0 rounded-3xl border border-amber-900/20 bg-[#faf6ed] p-5 shadow-lg shadow-amber-950/5">
                <div class="flex items-center justify-between border-b border-amber-900/10 pb-3">
                    <span class="text-xs font-bold uppercase tracking-widest text-amber-900 flex items-center gap-1.5 font-serif">
                        <i data-lucide="scroll" class="h-3.5 w-3.5 text-[#991b1b]"></i>
                        <span>Tiến trình giải đề</span>
                    </span>
                    <span x-show="!isSubmitted" class="text-xs font-semibold text-slate-600">
                        <span x-text="answeredCount" class="font-bold text-[#991b1b]">0</span> / {{ count($questions) }} đề
                    </span>
                    <span x-show="isSubmitted" class="seal-zhusha-round text-[10px]">
                        Đã Khảo Thí
                    </span>
                </div>

                {{-- Stats Grid --}}
                <div class="mt-3.5 grid grid-cols-3 gap-2 text-center">
                    <div class="rounded-2xl border border-amber-900/10 bg-white/70 p-2.5">
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Tổng Đề</p>
                        <p class="mt-0.5 text-xl font-black text-slate-900 font-serif">{{ count($questions) }}</p>
                    </div>
                    <div class="rounded-2xl border border-amber-900/10 bg-white/70 p-2.5">
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider" x-text="isSubmitted ? 'Đắc Chuẩn' : 'Đã Hạ Bút'"></p>
                        <p class="mt-0.5 text-xl font-black text-[#991b1b] font-serif">
                            <span x-text="isSubmitted ? (results?.correct_count ?? 0) : answeredCount">0</span>
                        </p>
                    </div>
                    <div class="rounded-2xl border border-amber-900/10 bg-white/70 p-2.5">
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Điểm Số</p>
                        <p class="mt-0.5 text-xl font-black text-emerald-700 font-serif">
                            <span x-text="isSubmitted ? (results?.score + '%') : '--'">--</span>
                        </p>
                    </div>
                </div>

                {{-- Scroll Progress Bar --}}
                <div class="mt-4">
                    <div class="flex items-center justify-between text-xs text-slate-600 mb-1.5 font-medium">
                        <span>Cuộn đề mở ra</span>
                        <span x-text="progressPercentage + '%'" class="font-bold text-amber-950">0%</span>
                    </div>
                    <div class="cuon-de-scroll h-2.5 w-full overflow-hidden p-0.5">
                        <div class="cuon-de-bar" :style="'width: ' + progressPercentage + '%'"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Filter Toolbar: HSK Level Tabs + Lesson Selector + Count Picker --}}
    <section class="space-y-3 rounded-2xl border border-amber-900/15 bg-white/70 p-4 shadow-sm backdrop-blur">
        {{-- Row 1: HSK Level quick-filter tabs --}}
        <div class="flex flex-wrap items-center gap-2">
            <span class="mr-1 text-xs font-bold uppercase tracking-wider text-amber-950 font-serif">Khoa Mục:</span>

            {{-- All --}}
            <a href="{{ route('quiz') }}"
               class="inline-flex items-center gap-1.5 rounded-full px-3.5 py-1.5 text-xs sm:text-sm font-semibold transition
                      {{ !$selectedLevel && $selectedLessonSlug === 'all' ? 'bg-[#991b1b] text-white shadow-md shadow-red-950/20' : 'bg-white border border-amber-900/20 text-slate-700 hover:border-[#991b1b] hover:text-[#991b1b]' }}">
                <span>Tất cả</span>
                <span class="rounded-full px-1.5 py-0.2 text-xs {{ !$selectedLevel && $selectedLessonSlug === 'all' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600' }}">
                    {{ $totalActiveQuestions }}
                </span>
            </a>

            {{-- HSK 1-6 tabs --}}
            @foreach([1,2,3,4,5,6] as $lvl)
                @php
                    $isActive = $selectedLevel === $lvl && $selectedLessonSlug === 'all';
                    $lvlCount = $questionCountsByLevel[$lvl] ?? 0;
                @endphp
                <a href="{{ route('quiz', ['level' => $lvl]) }}"
                   class="inline-flex items-center gap-1.5 rounded-full px-3.5 py-1.5 text-xs sm:text-sm font-semibold transition
                          {{ $isActive ? 'bg-[#991b1b] text-white shadow-md shadow-red-950/20' : 'bg-white border border-amber-900/20 text-slate-700 hover:border-[#991b1b] hover:text-[#991b1b]' }}">
                    <span>HSK {{ $lvl }}</span>
                    <span class="rounded-full px-1.5 py-0.2 text-xs {{ $isActive ? 'bg-white/20 text-white' : 'bg-amber-100/70 text-amber-900' }}">
                        {{ $lvlCount }}
                    </span>
                </a>
            @endforeach
        </div>

        {{-- Row 2: Lesson dropdown + count selector --}}
        <div class="flex flex-wrap items-center gap-3 pt-1 border-t border-amber-900/10">
            {{-- Lesson dropdown --}}
            <div class="relative inline-flex items-center">
                <select onchange="if(this.value){ window.location.href = this.value; }"
                    class="appearance-none rounded-full border border-amber-900/20 bg-white py-2 pl-4 pr-9 text-xs sm:text-sm font-semibold text-slate-800 shadow-2xs outline-none transition hover:border-[#991b1b] focus:border-[#991b1b] focus:ring-1 focus:ring-[#991b1b] cursor-pointer max-w-[280px] sm:max-w-xs truncate">
                    <option value="{{ route('quiz', $selectedLevel ? ['level' => $selectedLevel] : []) }}">
                        📜 Chọn thiên chương bài học...
                    </option>
                    @foreach($lessonsByLevel as $lvl => $lvlLessons)
                        <optgroup label="── HSK {{ $lvl }} ──">
                            @foreach($lvlLessons as $l)
                                <option value="{{ route('quiz', ['lesson' => $l->slug]) }}"
                                        {{ $selectedLessonSlug === $l->slug ? 'selected' : '' }}>
                                    {{ $l->title }} ({{ $l->questions_count }} câu)
                                </option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
                <i data-lucide="chevron-down" class="pointer-events-none absolute right-3 top-2.5 h-4 w-4 text-amber-800"></i>
            </div>

            {{-- Question count picker --}}
            <div class="flex items-center gap-1.5">
                <span class="text-xs font-semibold text-amber-950 font-serif">Số lượng đề:</span>
                @foreach($validCounts as $cnt)
                    @php
                        $cntParams = array_filter([
                            'level'  => $selectedLevel,
                            'lesson' => $selectedLessonSlug !== 'all' ? $selectedLessonSlug : null,
                            'count'  => $cnt,
                        ]);
                    @endphp
                    <a href="{{ route('quiz', $cntParams) }}"
                       class="inline-flex h-7 w-9 items-center justify-center rounded-full text-xs font-bold transition
                              {{ $perSession === $cnt ? 'bg-[#991b1b] text-white shadow-xs' : 'bg-white border border-amber-900/20 text-slate-700 hover:border-[#991b1b]' }}">
                        {{ $cnt }}
                    </a>
                @endforeach
            </div>

            {{-- Active lesson badge + clear button --}}
            @if($selectedLesson)
                <span class="inline-flex items-center gap-1.5 rounded-full bg-red-50 border border-red-200 px-3 py-1 text-xs font-semibold text-[#991b1b]">
                    <i data-lucide="book-open" class="h-3 w-3"></i>
                    <span>{{ $selectedLesson->title }}</span>
                    <a href="{{ route('quiz', $selectedLevel ? ['level' => $selectedLevel] : []) }}"
                       class="ml-1 rounded-full p-0.5 hover:bg-red-200/60 transition text-red-700" title="Bỏ lọc bài học">
                        <i data-lucide="x" class="h-3 w-3"></i>
                    </a>
                </span>
            @endif
        </div>
    </section>

    @if (count($questions) > 0)
        @php
            $petStageObj = $userPet?->pet?->stages?->where('stage', $userPet->stage)?->first();
            $petDefaultEmoji = $petStageObj?->emoji ?? '🐉';
            $petDefaultName = $userPet?->name ?? 'Tiểu Long';
        @endphp

        {{-- 1. EXAM GATE · 展卷 Mở Đề (Gọn Gàng & Thanh Nhã) --}}
        <section x-show="!isUnrolled && !isSubmitted"
                 x-transition:enter="transition ease-out duration-400"
                 x-transition:leave="transition ease-in duration-300"
                 class="relative overflow-hidden rounded-[2.5rem] border-2 border-amber-900/30 bg-[#faf6ed] p-8 sm:p-12 text-center shadow-xl shadow-amber-950/10">
            <div class="pointer-events-none absolute inset-0 opacity-[0.03] select-none text-[16rem] font-serif font-black flex items-center justify-center">
                试
            </div>
            
            <div class="relative z-10 max-w-xl mx-auto space-y-4">
                <div class="inline-flex items-center justify-center">
                    <span class="seal-zhusha-round text-sm font-black tracking-widest px-4 py-1">
                        科 举 · 试 卷
                    </span>
                </div>

                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-black text-slate-900 font-serif">
                    {{ $selectedLesson ? $selectedLesson->title : ($selectedLevel ? 'HSK ' . $selectedLevel . ' · Khảo Thí' : 'Trắc Nghiệm Tổng Hợp') }}
                </h2>

                <p class="text-sm text-slate-600 leading-relaxed font-serif">
                    Đề thi gồm <strong class="text-[#991b1b] font-bold">{{ count($questions) }} đạo thi đề</strong>. 
                    Thời gian làm bài ước tính <strong class="text-amber-950 font-bold">~{{ max(3, round(count($questions) * 0.8)) }} phút</strong>. 
                    Tĩnh tâm hạ bút, mỗi câu hỏi là một bước tiến trên con đường đăng khoa.
                </p>

                <div class="pt-4 flex flex-col sm:flex-row items-center justify-center gap-3">
                    <button type="button"
                            @click="unrollExam()"
                            class="inline-flex items-center justify-center gap-2.5 rounded-full bg-[#991b1b] hover:bg-red-800 text-white px-8 py-3.5 text-base font-bold shadow-lg shadow-red-950/20 hover:scale-105 active:scale-95 transition">
                        <i data-lucide="scroll" class="h-5 w-5"></i>
                        <span>展 卷 · Mở Đề Bắt Đầu</span>
                    </button>
                    <a href="{{ route('flashcards', $selectedLesson ? ['lesson' => $selectedLesson->slug] : []) }}"
                       class="inline-flex items-center justify-center gap-2 rounded-full border border-amber-900/20 bg-white/80 hover:bg-white text-slate-700 px-6 py-3.5 text-sm font-semibold shadow-2xs transition">
                        <i data-lucide="bookmark" class="h-4 w-4 text-amber-800"></i>
                        <span>Ôn Thư Thiêm trước</span>
                    </a>
                </div>
            </div>
        </section>

        {{-- 2. KIM BẢNG (金榜 · Bảng Vàng Vinh Danh Cá Nhân khi đã Submit) --}}
        <section x-show="isSubmitted"
                 x-transition:enter="transition ease-out duration-500"
                 x-transition:enter-start="opacity-0 translate-y-4"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="kimbang-scroll p-6 sm:p-10 mb-8 text-slate-900">
            <div class="relative z-10">
                {{-- Header Seal --}}
                <div class="text-center mb-6">
                    <div class="kimbang-seal-header inline-flex">
                        <span class="text-xs font-black tracking-[0.3em] uppercase text-red-900">HOÀNG TRIỀU KHOA CỬ</span>
                        <span class="text-2xl sm:text-3xl font-black mt-0.5">金 榜 · 题 名</span>
                    </div>
                    <p class="mt-2 text-xs uppercase tracking-widest text-amber-900/80 font-bold font-serif">
                        此 卷 · 已 成 — THỬ QUYỂN DĨ THÀNH
                    </p>
                    <h2 class="mt-2 text-2xl sm:text-4xl font-black text-slate-950 font-serif" x-text="honorTitle.title">
                        Kim Bảng Đề Danh · 金榜题名
                    </h2>
                    <p class="mt-1 text-sm text-amber-950 max-w-lg mx-auto" x-text="honorTitle.desc"></p>
                </div>

                {{-- 4 Thẻ Ngọc Thống Kê Thành Tích --}}
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 my-6">
                    {{-- 1. Tổng đề --}}
                    <div class="rounded-2xl border border-amber-900/20 bg-white/80 p-4 text-center shadow-2xs">
                        <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider font-serif">Tổng Thí Đề</p>
                        <p class="text-2xl sm:text-3xl font-black text-slate-900 mt-1 font-serif">
                            <span x-text="totalCount"></span> <span class="text-xs font-normal text-slate-500">câu</span>
                        </p>
                    </div>

                    {{-- 2. Đắc chuẩn --}}
                    <div class="rounded-2xl border border-amber-900/20 bg-white/80 p-4 text-center shadow-2xs">
                        <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider font-serif">Đắc Chuẩn (Đúng)</p>
                        <p class="text-2xl sm:text-3xl font-black text-[#991b1b] mt-1 font-serif">
                            <span x-text="results?.correct_count ?? 0"></span> <span class="text-xs font-normal text-slate-500">câu</span>
                        </p>
                    </div>

                    {{-- 3. Thời gian --}}
                    <div class="rounded-2xl border border-amber-900/20 bg-white/80 p-4 text-center shadow-2xs">
                        <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider font-serif">Thời Gian Hạ Bút</p>
                        <p class="text-xl sm:text-2xl font-black text-amber-900 mt-1 font-serif" x-text="formattedDuration">
                            --
                        </p>
                    </div>

                    {{-- 4. Điểm số --}}
                    <div class="rounded-2xl border border-amber-900/20 bg-white/80 p-4 text-center shadow-2xs">
                        <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider font-serif">Khảo Điểm</p>
                        <p class="text-2xl sm:text-3xl font-black text-emerald-700 mt-1 font-serif">
                            <span x-text="results?.score + '%'">0%</span>
                        </p>
                    </div>
                </div>

                {{-- Thư Đồng Đồng Viện Vinh Danh (Pet Reward) --}}
                <div x-show="petReward" class="rounded-2xl border border-amber-400/40 bg-gradient-to-r from-amber-100/70 via-orange-50/60 to-red-50/70 p-4 sm:p-5 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="flex items-center gap-3.5">
                        <div class="grid h-14 w-14 shrink-0 place-items-center rounded-2xl bg-white shadow-sm border border-amber-300 text-3xl">
                            <span x-text="petReward?.emoji || '{{ $petDefaultEmoji }}'"></span>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold text-amber-900 uppercase tracking-wide">Thư đồng dâng lễ</span>
                                <span class="seal-zhusha-round text-[10px]">Tặng Thưởng</span>
                            </div>
                            <p class="text-sm font-semibold text-slate-900 mt-0.5" x-text="petSpeech"></p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 shrink-0">
                        <div class="rounded-xl bg-white/90 border border-amber-200 px-3 py-1.5 text-center">
                            <p class="text-[10px] text-slate-500 font-bold uppercase">EXP Pet</p>
                            <p class="text-base font-black text-amber-700" x-text="'+' + (petReward?.exp_bonus || 0) + ' EXP'"></p>
                        </div>
                        <div class="rounded-xl bg-white/90 border border-amber-200 px-3 py-1.5 text-center">
                            <p class="text-[10px] text-slate-500 font-bold uppercase">Vật Phẩm</p>
                            <p class="text-xs font-bold text-rose-700 mt-0.5" x-text="petReward?.gift_item || '🍎 Táo ngọt'"></p>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons on Kim Bảng --}}
                <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
                    <button type="button"
                            @click="resetQuiz()"
                            class="inline-flex items-center gap-2 rounded-full border border-amber-900/20 bg-white hover:bg-amber-50 px-5 py-2.5 text-sm font-bold text-slate-800 shadow-2xs transition active:scale-95">
                        <i data-lucide="rotate-ccw" class="h-4 w-4"></i>
                        <span>Tái Khảo · Làm lại đề này</span>
                    </button>
                    <a href="{{ route('flashcards', $selectedLesson ? ['lesson' => $selectedLesson->slug] : []) }}"
                       class="inline-flex items-center gap-2 rounded-full bg-[#991b1b] hover:bg-red-800 text-white px-6 py-2.5 text-sm font-bold shadow-md shadow-red-950/20 transition hover:scale-105 active:scale-95">
                        <i data-lucide="bookmark" class="h-4 w-4"></i>
                        <span>🎴 Mở Thư Thiêm Flashcard ôn tập</span>
                    </a>
                    <a href="{{ route('dashboard') }}"
                       class="inline-flex items-center gap-2 rounded-full border border-amber-900/15 bg-white/70 px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-white transition">
                        <i data-lucide="home" class="h-4 w-4"></i>
                        <span>Về Nhã Trai (Dashboard)</span>
                    </a>
                </div>
            </div>
        </section>

        {{-- 3. MỰC CHẤM BÀI & THƯ ĐỒNG ĐỒNG VIỆN (Navigation Track & Scholar Companion) --}}
        <section x-show="isUnrolled"
                 class="sticky top-4 z-20 rounded-2xl border border-amber-900/20 bg-[#faf6ed]/95 p-3.5 shadow-lg shadow-amber-950/5 backdrop-blur-md">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                {{-- Left: Companion Voice --}}
                <div class="flex items-center gap-3">
                    <div class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-white border border-amber-300 shadow-2xs text-2xl select-none">
                        <span x-text="petEmoji || '{{ $petDefaultEmoji }}'"></span>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[11px] font-bold uppercase tracking-wider text-amber-900 font-serif">
                            Thư Đồng {{ $petDefaultName }} nhắc nhở:
                        </p>
                        <p class="text-xs sm:text-sm font-medium text-slate-800 truncate max-w-md" x-text="petSpeech">
                            "Tĩnh tâm hạ bút, từng nét chữ đều là tích lũy tri thức."
                        </p>
                    </div>
                </div>

                {{-- Right: Question Ink Track (Dải Mực Chấm Bài) --}}
                <div class="flex items-center gap-1.5 overflow-x-auto pb-1 md:pb-0 max-w-full">
                    <span class="text-[11px] font-bold text-amber-900 shrink-0 mr-1 font-serif">Đề thi:</span>
                    @foreach ($questions as $idx => $q)
                        @php $qIdx = $idx + 1; $qid = $q->id; @endphp
                        <button type="button"
                                @click="scrollToQuestion({{ $qid }})"
                                title="Đến đề {{ $qIdx }}"
                                class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-lg border text-xs font-bold transition font-serif"
                                :class="{
                                    'border-emerald-600 bg-emerald-50 text-emerald-800 font-black ring-1 ring-emerald-500': isSubmitted && results?.details?.[{{ $qid }}]?.is_correct,
                                    'border-slate-400 bg-slate-100 text-slate-600 font-black': isSubmitted && !results?.details?.[{{ $qid }}]?.is_correct,
                                    'border-[#991b1b] bg-red-50 text-[#991b1b] shadow-2xs font-black': !isSubmitted && answers[{{ $qid }}] !== undefined,
                                    'border-amber-900/15 bg-white/70 text-slate-500 hover:border-amber-400': !isSubmitted && answers[{{ $qid }}] === undefined
                                }">
                            <template x-if="isSubmitted && results?.details?.[{{ $qid }}]?.is_correct">
                                <span>准</span>
                            </template>
                            <template x-if="isSubmitted && !results?.details?.[{{ $qid }}]?.is_correct">
                                <span>×</span>
                            </template>
                            <template x-if="!isSubmitted">
                                <span>{{ $qIdx }}</span>
                            </template>
                        </button>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- 4. DANH SÁCH THI ĐỀ (THI ÁN · 考案) --}}
        <div x-show="isUnrolled" class="space-y-6">
            @php
                $chineseNumerals = [1 => '一', 2 => '二', 3 => '三', 4 => '四', 5 => '五', 6 => '六', 7 => '七', 8 => '八', 9 => '九', 10 => '十',
                                    11 => '十一', 12 => '十二', 13 => '十三', 14 => '十四', 15 => '十五', 16 => '十六', 17 => '十七', 18 => '十八', 19 => '十九', 20 => '二十',
                                    21 => '二十一', 22 => '二十二', 23 => '二十三', 24 => '二十四', 25 => '二十五', 26 => '二十六', 27 => '二十七', 28 => '二十八', 29 => '二十九', 30 => '三十'];
            @endphp

            @foreach ($questions as $index => $question)
                @php
                    $qNum = $index + 1;
                    $hanziNum = $chineseNumerals[$qNum] ?? $qNum;
                @endphp

                <article id="question-card-{{ $question->id }}"
                         class="thi-an-card p-6 sm:p-8 transition-all duration-300"
                         :class="{
                             'ring-2 ring-emerald-500/40 bg-emerald-50/20': isSubmitted && results?.details?.[{{ $question->id }}]?.is_correct,
                             'ring-1 ring-slate-400/50 bg-slate-50/40': isSubmitted && !results?.details?.[{{ $question->id }}]?.is_correct
                         }">
                    
                    {{-- Thi Án Header --}}
                    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-amber-900/10 pb-4">
                        <div class="flex items-center gap-3">
                            <span class="inline-flex items-center justify-center rounded-xl bg-amber-950 text-amber-100 px-3 py-1 text-xs font-bold font-serif tracking-wider">
                                第 {{ $hanziNum }} 题 · Đề {{ $qNum }}
                            </span>
                            @if ($question->lesson)
                                <span class="text-xs text-amber-900/70 font-medium font-serif">
                                    • {{ $question->lesson->title }}
                                </span>
                            @endif
                        </div>

                        <div class="flex items-center gap-2">
                            @if ($question->pinyin)
                                <span class="rounded-full bg-white/80 border border-amber-900/10 px-3 py-1 text-xs font-semibold text-slate-800 shadow-2xs">
                                    <x-tone-pinyin :text="$question->pinyin" />
                                </span>
                            @endif

                            {{-- Seal Stamp after Submission --}}
                            <template x-if="isSubmitted">
                                <span>
                                    <template x-if="results?.details?.[{{ $question->id }}]?.is_correct">
                                        <span class="seal-stamp-zhun text-xs">
                                            <span>✓</span>
                                            <span>准 · Đắc Chuẩn</span>
                                        </span>
                                    </template>
                                    <template x-if="!results?.details?.[{{ $question->id }}]?.is_correct">
                                        <span class="seal-stamp-weizhun text-xs">
                                            <span>✗</span>
                                            <span>未准 · Cần Thâm Tu</span>
                                        </span>
                                    </template>
                                </span>
                            </template>
                        </div>
                    </div>

                    {{-- Question Content (Serif Typography & Traditional Balance) --}}
                    <div class="mt-5">
                        <h2 class="text-xl sm:text-2xl font-bold text-slate-900 font-serif leading-relaxed tracking-wide">
                            {{ $question->question }}
                        </h2>
                    </div>

                    {{-- Listening Audio Player --}}
                    @if ($question->skill_type === 'listening' && $question->audio_text)
                        <div class="mt-4 flex items-center gap-4 rounded-2xl border border-amber-900/20 bg-amber-50/80 px-5 py-4">
                            <span class="seal-zhusha text-xs">
                                听音 · Thính Âm
                            </span>

                            <span class="flex-1 font-serif text-lg font-bold text-slate-900 tracking-wider">
                                {{ $question->audio_text }}
                            </span>

                            <button type="button"
                                    data-audio="{{ $question->audio_text }}"
                                    onclick="playQuizAudio(this, this.dataset.audio)"
                                    title="Nghe đoạn âm thanh thi đề"
                                    class="tts-btn inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-[#991b1b] text-white shadow-md shadow-red-950/20 transition hover:bg-red-800 active:scale-95">
                                <i data-lucide="volume-2" class="h-5 w-5 tts-play-icon"></i>
                                <i data-lucide="square" class="h-5 w-5 tts-stop-icon hidden"></i>
                            </button>

                            <span class="hidden text-xs text-amber-900/70 sm:block font-serif">Nhấn để nghe phát âm</span>
                        </div>
                    @endif

                    {{-- Ngọc Bài / Mộc Bài Options (Answer Plaques) --}}
                    <div class="mt-6 grid gap-3 sm:grid-cols-2">
                        @php
                            $optionLetters = ['A', 'B', 'C', 'D', 'E', 'F'];
                        @endphp
                        @foreach ($question->options as $optIdx => $option)
                            @php
                                $optJs = Js::from($option);
                                $qidJs = $question->id;
                            @endphp
                            <button type="button"
                                    @click="selectOption({{ $qidJs }}, {{ $optJs }})"
                                    :disabled="isSubmitted"
                                    class="plaque-option flex items-center justify-between p-4 text-left font-serif transition"
                                    :class="getOptionClass({{ $qidJs }}, {{ $optJs }})">
                                <span class="flex items-center gap-3">
                                    <span class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-lg border text-xs font-black transition font-serif"
                                          :class="getOptionRadioClass({{ $qidJs }}, {{ $optJs }})">
                                        {{ $optionLetters[$optIdx] ?? ($optIdx + 1) }}
                                    </span>
                                    <span class="text-sm sm:text-base font-semibold leading-relaxed" :class="getOptionTextClass({{ $qidJs }}, {{ $optJs }})">
                                        {{ $option }}
                                    </span>
                                </span>

                                {{-- Icon indicator after submit --}}
                                <template x-if="isSubmitted">
                                    <span>
                                        <template x-if="isCorrectOption({{ $qidJs }}, {{ $optJs }})">
                                            <span class="seal-zhusha text-[10px]">准</span>
                                        </template>
                                        <template x-if="isUserWrongOption({{ $qidJs }}, {{ $optJs }})">
                                            <span class="text-xs text-slate-500 font-bold">未准</span>
                                        </template>
                                    </span>
                                </template>
                            </button>
                        @endforeach
                    </div>

                    {{-- Phản Hồi Học Thuật (Lời Phê Giám Khảo & Mẹo Nhớ) --}}
                    <div x-show="isSubmitted"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="mt-6 rounded-2xl border p-4 sm:p-5 font-serif"
                         :class="results?.details?.[{{ $question->id }}]?.is_correct ? 'bg-emerald-50/60 border-emerald-300 text-emerald-950' : 'bg-amber-50/80 border-amber-300 text-amber-950'">
                        <div class="flex items-start gap-3.5">
                            <template x-if="results?.details?.[{{ $question->id }}]?.is_correct">
                                <div class="grid h-8 w-8 shrink-0 place-items-center rounded-xl bg-emerald-100 text-emerald-800">
                                    <i data-lucide="check" class="h-4 w-4"></i>
                                </div>
                            </template>
                            <template x-if="!results?.details?.[{{ $question->id }}]?.is_correct">
                                <div class="grid h-8 w-8 shrink-0 place-items-center rounded-xl bg-amber-200/80 text-amber-900 text-base">
                                    <span>💡</span>
                                </div>
                            </template>

                            <div class="space-y-1.5 flex-1">
                                <template x-if="results?.details?.[{{ $question->id }}]?.is_correct">
                                    <div>
                                        <p class="text-xs font-bold uppercase tracking-wider text-emerald-800 flex items-center gap-1.5">
                                            <span>「 审题精当 · Đắc Chuẩn 」</span>
                                        </p>
                                        <p class="text-sm font-semibold pt-1">
                                            Đáp án chuẩn xác: <span class="text-emerald-800 font-bold">{{ $question->correct_answer }}</span>
                                        </p>
                                        @if ($question->explanation)
                                            <p class="text-xs sm:text-sm text-slate-700 leading-relaxed pt-1">
                                                {{ $question->explanation }}
                                            </p>
                                        @endif
                                    </div>
                                </template>

                                <template x-if="!results?.details?.[{{ $question->id }}]?.is_correct">
                                    <div>
                                        <p class="text-xs font-black uppercase tracking-wider text-amber-900 flex items-center gap-1.5">
                                            <span>「 沉思再悟 · Mẹo Ghi Nhớ 」</span>
                                        </p>
                                        <div class="flex items-baseline gap-2 pt-1 flex-wrap">
                                            <p class="text-sm font-semibold text-slate-900">
                                                Đáp án đúng: <span class="text-[#991b1b] font-black text-base">{{ $question->correct_answer }}</span>
                                                @if ($question->pinyin)
                                                    <span class="text-xs font-mono text-slate-600">[{{ $question->pinyin }}]</span>
                                                @endif
                                            </p>
                                            @if ($question->audio_text)
                                                <button type="button"
                                                        data-audio="{{ $question->audio_text }}"
                                                        onclick="playQuizAudio(this, this.dataset.audio)"
                                                        class="tts-btn inline-flex items-center gap-1 text-[11px] font-bold text-[#991b1b] hover:underline">
                                                    <i data-lucide="volume-2" class="h-3 w-3 tts-play-icon"></i>
                                                    <i data-lucide="square" class="h-3 w-3 tts-stop-icon hidden"></i>
                                                    <span>Nghe lại phát âm</span>
                                                </button>
                                            @endif
                                        </div>
                                        @if ($question->explanation)
                                            <p class="text-xs sm:text-sm text-slate-800 leading-relaxed bg-white/70 p-2.5 rounded-xl border border-amber-200/80 mt-1.5">
                                                {{ $question->explanation }}
                                            </p>
                                        @endif
                                        <p class="text-[11px] text-amber-900/80 italic pt-1">
                                            💬 "Sai là thông tin để thấu suốt sâu hơn, không phải thất bại. Lần sau gặp lại câu này nhất định sẽ đắc chuẩn!"
                                        </p>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        {{-- 5. VÒNG LẶP HỌC TẬP: ÔN LUYỆN LẠI CÁC CÂU CHƯA ĐẮC CHUẨN --}}
        <section x-show="isSubmitted && (results?.score < 100)"
                 x-transition:enter="transition ease-out duration-400"
                 class="rounded-[2.25rem] border border-amber-900/20 bg-[#faf8f5] p-6 sm:p-8 shadow-md">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-amber-900/10 pb-4">
                <div>
                    <span class="seal-zhusha text-xs">考后温习 · ÔN TẬP SAU THI</span>
                    <h3 class="text-xl sm:text-2xl font-black text-slate-900 font-serif mt-1">
                        Củng cố các điểm chưa đắc chuẩn
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-600 font-serif mt-0.5">
                        Thư phòng đã tổng hợp các câu bạn đã bỏ sót hoặc chọn chưa đúng để ôn sâu bằng Thư Thiêm.
                    </p>
                </div>

                <a href="{{ route('flashcards', $selectedLesson ? ['lesson' => $selectedLesson->slug] : []) }}"
                   class="inline-flex items-center justify-center gap-2 rounded-full bg-[#991b1b] hover:bg-red-800 text-white px-6 py-3 text-sm font-bold shadow-md shadow-red-950/20 transition hover:scale-105 active:scale-95 shrink-0">
                    <i data-lucide="bookmark" class="h-4 w-4"></i>
                    <span>🎴 Ôn ngay với Thư Thiêm</span>
                </a>
            </div>

            <div class="mt-4 grid gap-3 sm:grid-cols-2">
                @foreach ($questions as $q)
                    <div x-show="isSubmitted && !results?.details?.[{{ $q->id }}]?.is_correct"
                         class="rounded-2xl border border-amber-900/15 bg-white/80 p-4 shadow-2xs font-serif">
                        <div class="flex items-start justify-between gap-2">
                            <span class="text-xs font-bold text-slate-500">Đề: {{ $loop->iteration }}</span>
                            <span class="text-[11px] font-bold text-amber-900 bg-amber-100/70 px-2 py-0.5 rounded-full">
                                Cần ôn
                            </span>
                        </div>
                        <p class="text-sm font-bold text-slate-900 mt-1 line-clamp-2">
                            {{ $q->question }}
                        </p>
                        <p class="text-xs text-emerald-800 font-bold mt-2">
                            Đáp án chuẩn: <span class="underline">{{ $q->correct_answer }}</span>
                        </p>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- 6. THANH ĐỀ BÚT & NỘP BÀI (STICKY BOTTOM SUBMISSION BAR) --}}
        <section x-show="isUnrolled"
                 class="sticky bottom-6 z-30 rounded-[2rem] border border-amber-900/20 bg-[#faf6ed]/95 p-4 sm:p-5 shadow-2xl shadow-amber-950/15 backdrop-blur-md">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-amber-900 font-serif">Trạng thái bài khảo thí</p>
                    <p class="text-sm text-slate-700 font-serif">
                        <span x-show="!isSubmitted">
                            Đã hạ bút <strong class="font-bold text-[#991b1b]" x-text="answeredCount">0</strong> / {{ count($questions) }} đạo đề.
                            <span x-show="answeredCount < {{ count($questions) }}" class="text-amber-800 font-medium">
                                (Còn <span x-text="{{ count($questions) }} - answeredCount"></span> câu chưa chọn)
                            </span>
                        </span>
                        <span x-show="isSubmitted">
                            Khảo thí hoàn tất. Điểm số: <strong class="font-bold text-emerald-700" x-text="results?.score + '%'"></strong>.
                        </span>
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <template x-if="!isSubmitted">
                        <button type="button"
                                @click="submitQuiz()"
                                :disabled="isSubmitting || answeredCount === 0"
                                class="inline-flex w-full items-center justify-center gap-2 rounded-full bg-[#991b1b] px-8 py-3.5 text-base font-bold text-white shadow-xl shadow-red-950/20 transition hover:bg-red-800 hover:scale-105 active:scale-95 disabled:cursor-not-allowed disabled:opacity-50 sm:w-auto font-serif">
                            <svg x-show="isSubmitting" class="h-5 w-5 animate-spin text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                            <span x-text="isSubmitting ? 'Đang chấm điểm bài thi...' : '落笔 · Nộp Bài Khảo Thí'"></span>
                            <span x-show="!isSubmitting" class="inline-flex">
                                <i data-lucide="check" class="h-5 w-5"></i>
                            </span>
                        </button>
                    </template>

                    <template x-if="isSubmitted">
                        <div class="flex flex-wrap items-center gap-2">
                            <button type="button"
                                    @click="resetQuiz()"
                                    class="inline-flex items-center justify-center gap-2 rounded-full border border-amber-900/20 bg-white px-5 py-3 text-sm font-bold text-slate-800 shadow transition hover:bg-amber-50">
                                <i data-lucide="rotate-ccw" class="h-4 w-4"></i>
                                <span>Làm lại</span>
                            </button>
                            <a href="{{ route('flashcards', $selectedLesson ? ['lesson' => $selectedLesson->slug] : []) }}"
                               class="inline-flex items-center justify-center gap-2 rounded-full bg-[#991b1b] px-5 py-3 text-sm font-bold text-white shadow-md shadow-red-950/20 transition hover:bg-red-800 hover:scale-105 active:scale-95">
                                <i data-lucide="bookmark" class="h-4 w-4"></i>
                                <span>Ôn Thư Thiêm</span>
                            </a>
                            <a href="{{ route('dashboard') }}"
                               class="inline-flex items-center justify-center gap-2 rounded-full border border-amber-900/20 bg-white px-5 py-3 text-sm font-bold text-slate-800 shadow transition hover:bg-amber-50">
                                <i data-lucide="home" class="h-4 w-4"></i>
                                <span class="hidden sm:inline">Về Nhã Trai</span>
                            </a>
                        </div>
                    </template>
                </div>
            </div>
        </section>

        {{-- Footer Refresh Note --}}
        <p class="text-center text-xs text-slate-500 font-serif">
            Bộ {{ count($questions) }} đề được chọn ngẫu nhiên từ kho {{ $totalPoolCount }} câu.
            <a href="{{ request()->fullUrl() }}" class="font-semibold text-[#991b1b] hover:underline inline-flex items-center gap-1">
                <span>Khởi tạo bộ đề khác</span>
                <i data-lucide="arrow-right" class="h-3 w-3 inline"></i>
            </a>
        </p>

    @else
        {{-- Empty State (Nhã Trai Tĩnh Lặng) --}}
        <section class="flex flex-col items-center justify-center rounded-[2.5rem] border border-dashed border-amber-900/25 bg-[#faf8f5] p-12 text-center shadow-2xs sm:p-16 font-serif">
            <div class="grid h-16 w-16 place-items-center rounded-3xl bg-amber-100/80 text-amber-900">
                <i data-lucide="scroll" class="h-8 w-8"></i>
            </div>
            <h2 class="mt-4 text-2xl font-bold text-slate-900">Chưa có đề thi nào trong thiên này</h2>
            <p class="mt-2 max-w-md text-sm text-slate-600">
                Thư phòng hiện chưa biên soạn câu hỏi cho bài học này. Bạn có thể chọn chủ đề khác hoặc chuyển sang ôn luyện từ vựng bằng Thư Thiêm trước.
            </p>
            <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
                <a href="{{ route('quiz') }}" class="inline-flex items-center gap-2 rounded-full bg-[#991b1b] px-6 py-3 text-sm font-bold text-white shadow-md transition hover:bg-red-800">
                    <i data-lucide="layers" class="h-4 w-4"></i>
                    <span>Xem toàn bộ thi đề</span>
                </a>
                <a href="{{ route('flashcards') }}" class="inline-flex items-center gap-2 rounded-full border border-amber-900/20 bg-white px-6 py-3 text-sm font-semibold text-slate-700 shadow-2xs transition hover:bg-amber-50">
                    <i data-lucide="bookmark" class="h-4 w-4"></i>
                    <span>Ôn Thư Thiêm trước</span>
                </a>
            </div>
        </section>
    @endif
</div>

{{-- Alpine.js Quiz Logic --}}
<script>
// ─── TTS Audio Player for Listening Questions ────────────────────────────────
let _activeTtsBtn = null;

function playQuizAudio(btn, text) {
    if (!('speechSynthesis' in window)) {
        alert('Trình duyệt của bạn không hỗ trợ đọc âm thanh. Vui lòng dùng Chrome hoặc Edge.');
        return;
    }

    const playIcon = btn.querySelector('.tts-play-icon');
    const stopIcon = btn.querySelector('.tts-stop-icon');

    // If this button is already playing → stop it
    if (_activeTtsBtn === btn) {
        window.speechSynthesis.cancel();
        _activeTtsBtn = null;
        playIcon?.classList.remove('hidden');
        stopIcon?.classList.add('hidden');
        return;
    }

    // If another button is playing → cancel it first
    if (_activeTtsBtn) {
        window.speechSynthesis.cancel();
        const prevPlay = _activeTtsBtn.querySelector('.tts-play-icon');
        const prevStop = _activeTtsBtn.querySelector('.tts-stop-icon');
        if (prevPlay) prevPlay.classList.remove('hidden');
        if (prevStop) prevStop.classList.add('hidden');
    }

    // Start new speech
    const utterance = new SpeechSynthesisUtterance(text);
    utterance.lang = 'zh-CN';
    utterance.rate = 0.85;
    utterance.pitch = 1;

    // Try to pick a Chinese voice
    const voices = window.speechSynthesis.getVoices();
    const zhVoice = voices.find(v => v.lang.startsWith('zh'));
    if (zhVoice) utterance.voice = zhVoice;

    _activeTtsBtn = btn;
    playIcon?.classList.add('hidden');
    stopIcon?.classList.remove('hidden');

    const resetBtn = () => {
        if (_activeTtsBtn === btn) {
            _activeTtsBtn = null;
            playIcon?.classList.remove('hidden');
            stopIcon?.classList.add('hidden');
        }
    };

    utterance.onend = resetBtn;
    utterance.onerror = resetBtn;

    window.speechSynthesis.speak(utterance);
}

// Pre-load voices when page loads
if ('speechSynthesis' in window) {
    window.speechSynthesis.getVoices();
    window.speechSynthesis.addEventListener('voiceschanged', () => {
        window.speechSynthesis.getVoices();
    });
}

function quizApp() {
    return {
        answers: {},
        isSubmitted: false,
        isSubmitting: false,
        isUnrolled: true, // Auto-unroll or toggleable via Exam Gate
        results: null,
        startTime: null,
        endTime: null,
        totalCount: {{ count($questions) }},

        // Scholar Companion Voice
        petName: '{{ $petDefaultName }}',
        petEmoji: '{{ $petDefaultEmoji }}',
        petSpeech: 'Tĩnh tâm hạ bút, từng nét chữ đều là tích lũy tri thức.',
        petReward: null,

        initQuiz() {
            this.startTime = Date.now();
        },

        unrollExam() {
            this.isUnrolled = true;
            this.startTime = Date.now();
            window.soundEngine?.play('open');
            this.$nextTick(() => {
                const firstCard = document.querySelector('.thi-an-card');
                if (firstCard) {
                    firstCard.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        },

        get answeredCount() {
            return Object.keys(this.answers).filter(k => this.answers[k] !== undefined && this.answers[k] !== '').length;
        },

        get progressPercentage() {
            if (this.totalCount === 0) return 0;
            return Math.round((this.answeredCount / this.totalCount) * 100);
        },

        get formattedDuration() {
            const start = this.startTime || Date.now();
            const end = this.endTime || Date.now();
            const sec = Math.max(1, Math.round((end - start) / 1000));
            const m = Math.floor(sec / 60);
            const s = sec % 60;
            if (m === 0) return `${s} giây`;
            return `${m} phút ${s < 10 ? '0' : ''}${s} giây`;
        },

        get honorTitle() {
            const score = this.results?.score ?? 0;
            if (score === 100) {
                return {
                    title: 'Kim Bảng Đề Danh · 金榜题名',
                    desc: 'Thấu suốt toàn văn, bút nở hoa gấm, tài học quán thông muôn người kính phục!'
                };
            }
            if (score >= 80) {
                return {
                    title: 'Đăng Khoa Cập Đệ · 登科及第',
                    desc: 'Nền tảng vững vàng, văn phong chuẩn xác, xứng đáng đề danh bảng vàng!'
                };
            }
            if (score >= 60) {
                return {
                    title: 'Cập Đệ Nhập Hàng · 及第入行',
                    desc: 'Hạ bút mạch lạc, đã nắm được tinh hoa trọng tâm của thiên sách!'
                };
            }
            return {
                title: 'Tích Lũy Thâm Tu · 再试深修',
                desc: 'Học đạo cần kiên trì, từng câu chưa đắc chuẩn đều là hạt mầm cho sự thấu tỏ mai sau.'
            };
        },

        selectOption(qId, option) {
            if (this.isSubmitted) return;
            this.answers[+qId] = option;
            window.soundEngine?.play('tap');

            // Scholar Companion micro-reaction
            const remaining = this.totalCount - this.answeredCount;
            if (remaining === 0) {
                this.petSpeech = 'Đã hạ bút đủ toàn bộ thi đề! Hãy kiểm tra lại hoặc nhấn "Nộp Bài Khảo Thí" bên dưới nhé! 📜✨';
            } else if (remaining === 1) {
                this.petSpeech = 'Chỉ còn đúng 1 đạo đề nữa thôi! Giữ vững tâm trí nào! 💡';
            } else {
                const phrases = [
                    'Ghi nhận nét mực! Quan sát tiếp đề kế bên nhé! 👀',
                    'Bút lực tự tin, tĩnh tâm giải đề! ✍️',
                    'Đạo lý trong câu này rất thâm sâu, hãy kiên trì! 🌿',
                    'Tập trung cao độ, điểm lành trên bảng vàng! 🎯'
                ];
                this.petSpeech = phrases[Math.floor(Math.random() * phrases.length)];
            }
        },

        scrollToQuestion(qId) {
            const el = document.getElementById('question-card-' + qId);
            if (el) {
                el.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        },

        isOptionSelected(qId, option) {
            return this.answers[+qId] === option;
        },

        isCorrectOption(qId, option) {
            if (!this.isSubmitted || !this.results?.details?.[+qId]) return false;
            return this.results.details[+qId].correct_answer === option;
        },

        isUserWrongOption(qId, option) {
            if (!this.isSubmitted || !this.results?.details?.[+qId]) return false;
            const detail = this.results.details[+qId];
            return detail.user_answer === option && !detail.is_correct;
        },

        getOptionClass(qId, option) {
            if (!this.isSubmitted) {
                if (this.isOptionSelected(qId, option)) {
                    return 'selected';
                }
                return '';
            }

            if (this.isCorrectOption(qId, option)) {
                return 'border-emerald-600 bg-emerald-50/70 text-emerald-950 ring-1 ring-emerald-500 font-bold';
            }

            if (this.isUserWrongOption(qId, option)) {
                return 'border-slate-400 bg-slate-100/80 text-slate-600 line-through opacity-85';
            }

            return 'border-amber-900/10 bg-white/50 text-slate-400 opacity-60';
        },

        getOptionRadioClass(qId, option) {
            if (!this.isSubmitted) {
                if (this.isOptionSelected(qId, option)) {
                    return 'border-[#991b1b] bg-[#991b1b] text-white shadow-xs';
                }
                return 'border-amber-900/20 bg-[#faf6ed] text-slate-700 font-bold';
            }

            if (this.isCorrectOption(qId, option)) {
                return 'border-emerald-600 bg-emerald-600 text-white font-black';
            }

            if (this.isUserWrongOption(qId, option)) {
                return 'border-slate-500 bg-slate-500 text-white font-bold';
            }

            return 'border-amber-900/15 bg-white text-slate-400';
        },

        getOptionTextClass(qId, option) {
            if (!this.isSubmitted) {
                return this.isOptionSelected(qId, option) ? 'font-bold text-[#991b1b]' : 'text-slate-800';
            }

            if (this.isCorrectOption(qId, option)) {
                return 'font-bold text-emerald-900';
            }

            if (this.isUserWrongOption(qId, option)) {
                return 'text-slate-500';
            }

            return 'text-slate-400';
        },

        async submitQuiz() {
            if (this.answeredCount === 0) {
                window.toast?.warning('Vui lòng hạ bút chọn ít nhất một đáp án trước khi nộp bài!');
                return;
            }

            if (this.answeredCount < this.totalCount) {
                const remaining = this.totalCount - this.answeredCount;
                const proceed = await window.dialog?.confirm({
                    title: 'Chưa hoàn thành tất cả thi đề',
                    text: `Còn ${remaining} câu chưa hạ bút. Bạn có chắc chắn muốn kết thúc bài thi luôn không?`,
                    confirmText: 'Nộp bài ngay',
                    cancelText: 'Tiếp tục làm bài',
                    icon: 'question',
                });
                if (!proceed) return;
            }

            this.isSubmitting = true;
            this.endTime = Date.now();
            const durationSeconds = Math.max(1, Math.round((this.endTime - this.startTime) / 1000));

            try {
                const response = await fetch("{{ route('quiz.submit') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        answers: this.answers,
                        lesson_slug: "{{ $selectedLessonSlug }}",
                        duration_seconds: durationSeconds
                    })
                });

                if (!response.ok) {
                    throw new Error("Không thể gửi bài thi.");
                }

                const data = await response.json();
                this.results = data;
                this.isSubmitted = true;
                this.petReward = data.pet_reward;

                // Sound & Scholar Speech feedback
                if (data.score === 100) {
                    this.petSpeech = `🎉 TUYỆT ĐỐI 100%! Bút nở hoa gấm, đúng trọn vẹn ${data.total_questions} đạo đề! Kim Bảng Đề Danh! 🏆✨`;
                    window.soundEngine?.play('fanfare');
                } else if (data.score >= 80) {
                    this.petSpeech = `Đăng Khoa Cập Đệ! Đúng ${data.correct_count}/${data.total_questions} câu! Thư đồng đã dâng trà mừng bạn! 🍵❤️`;
                    window.soundEngine?.play('fanfare');
                } else if (data.score >= 60) {
                    this.petSpeech = `Làm tốt lắm! Đúng ${data.correct_count}/${data.total_questions} câu. Hãy xem kỹ những câu chưa đắc chuẩn để thấu hiểu sâu hơn nhé! 💡`;
                    window.soundEngine?.play('milestone');
                } else {
                    this.petSpeech = `Vạn sự khởi đầu nan. Hãy xem phần mách nước chi tiết của thư đồng ở từng câu để ghi nhớ bền lâu nhé! 🌸`;
                    window.soundEngine?.play('ding');
                }

                // Log Learning Activity
                @auth
                try {
                    const idempotencyKey = `quiz_${Date.now()}_${data.score}`;
                    const actRes = await fetch('{{ route('student.activity.log') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            activity_type: 'quiz_completed',
                            source_type: 'quiz',
                            idempotency_key: idempotencyKey,
                            meta: {
                                score: data.score,
                                correct_count: data.correct_count,
                                total: this.totalCount,
                                duration_seconds: durationSeconds
                            }
                        })
                    });
                    const actData = await actRes.json();
                    if (actData?.daily_goal?.just_completed) {
                        window.PetEventBus?.emit('pet:daily-goal-completed', { score: data.score, xp: actData?.xp?.earned });
                    } else {
                        window.PetEventBus?.emit('pet:quiz-completed', { score: data.score, correct: data.correct_count, xp: actData?.xp?.earned });
                    }
                } catch(e) {}
                @endauth

                setTimeout(() => window.refreshIcons?.(), 50);

                // Smooth scroll to Kim Bảng
                window.scrollTo({ top: 0, behavior: 'smooth' });
            } catch (err) {
                console.error(err);
                window.toast?.error('Đã xảy ra lỗi khi chấm điểm', 'Vui lòng kiểm tra lại kết nối và thử lại!');
            } finally {
                this.isSubmitting = false;
            }
        },

        resetQuiz() {
            this.answers = {};
            this.isSubmitted = false;
            this.results = null;
            this.petReward = null;
            this.petSpeech = 'Tĩnh tâm hạ bút, từng nét chữ đều là tích lũy tri thức.';
            this.startTime = Date.now();
            this.endTime = null;
            setTimeout(() => window.refreshIcons?.(), 50);
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    };
}
</script>
@endsection