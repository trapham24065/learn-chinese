@extends('layouts.app')

@section('title', 'Audio Pop Quiz - Đố Vui Phản Xạ Âm Thanh | Learn Chinese')

@section('content')
<div x-data="audioQuizGame()" x-init="init()" class="max-w-3xl mx-auto pb-16">

    {{-- Breadcrumb & Audio Settings Bar --}}
    <div class="flex items-center justify-between gap-4 mb-6">
        <a href="{{ route('games.index') }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-600 hover:text-[#991b1b] transition">
            <i data-lucide="arrow-left" class="h-4 w-4"></i>
            <span>Về Game Hub</span>
        </a>

        <div class="flex items-center gap-2">
            <button type="button"
                    @click="toggleSpeechSpeed()"
                    class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-bold transition border bg-white border-slate-200 text-slate-700 hover:bg-slate-50">
                <i data-lucide="gauge" class="h-3.5 w-3.5 text-amber-600"></i>
                <span x-text="`Tốc độ: ${speechSpeed}x`"></span>
            </button>
            <button type="button"
                    @click="toggleSfx()"
                    class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-bold transition border"
                    :class="sfxEnabled ? 'bg-amber-50 text-amber-900 border-amber-300' : 'bg-slate-100 text-slate-400 border-slate-200'">
                <i :data-lucide="sfxEnabled ? 'volume-2' : 'volume-x'" class="h-3.5 w-3.5"></i>
                <span x-text="sfxEnabled ? 'SFX: Bật' : 'SFX: Tắt'"></span>
            </button>
        </div>
    </div>

    {{-- ========================================================================= --}}
    {{-- SCREEN 1: SETUP SCREEN --}}
    {{-- ========================================================================= --}}
    <div x-show="viewState === 'setup'"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-10 shadow-xl shadow-slate-900/5">

        <div class="text-center max-w-lg mx-auto">
            <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-100 px-3.5 py-1 text-xs font-bold uppercase tracking-wider text-amber-900">
                <i data-lucide="headphones" class="h-3.5 w-3.5"></i>
                Phản Xạ Thính Lực
            </span>
            <h1 class="mt-3 text-3xl sm:text-4xl font-black text-slate-950">Audio Pop Quiz</h1>
            <p class="mt-2 text-sm text-slate-600">
                Luyện đôi tai nhạy bén với âm điệu tiếng Trung chuẩn Bắc Kinh. Chinh phục triệt để các cạm bẫy thanh mẫu và thanh điệu!
            </p>
        </div>

        <div class="mt-8 space-y-6 max-w-xl mx-auto">
            {{-- Mode Selection --}}
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">
                    1. Chọn nội dung luyện nghe:
                </label>
                <div class="space-y-2.5">
                    <button type="button"
                            @click="mode = 'minimal_pairs'"
                            class="w-full rounded-2xl border-2 p-4 text-left transition flex items-start gap-3"
                            :class="mode === 'minimal_pairs' ? 'border-amber-500 bg-amber-50/70 text-slate-950 shadow-sm' : 'border-slate-200 hover:border-slate-300 text-slate-700'">
                        <div class="grid h-9 w-9 shrink-0 place-items-center rounded-xl"
                             :class="mode === 'minimal_pairs' ? 'bg-amber-600 text-white' : 'bg-slate-100 text-slate-600'">
                            <i data-lucide="git-compare" class="h-5 w-5"></i>
                        </div>
                        <div>
                            <p class="text-sm font-black">Cặp âm dễ nhầm (Minimal Pairs) ⭐ Khuyên dùng</p>
                            <p class="text-xs text-slate-500 mt-0.5">Phân biệt các cặp âm hóc búa: q / j, zh / z, ch / c, b / p, in / ing</p>
                        </div>
                    </button>

                    <button type="button"
                            @click="mode = 'tone_recognition'"
                            class="w-full rounded-2xl border-2 p-4 text-left transition flex items-start gap-3"
                            :class="mode === 'tone_recognition' ? 'border-amber-500 bg-amber-50/70 text-slate-950 shadow-sm' : 'border-slate-200 hover:border-slate-300 text-slate-700'">
                        <div class="grid h-9 w-9 shrink-0 place-items-center rounded-xl"
                             :class="mode === 'tone_recognition' ? 'bg-amber-600 text-white' : 'bg-slate-100 text-slate-600'">
                            <i data-lucide="music" class="h-5 w-5"></i>
                        </div>
                        <div>
                            <p class="text-sm font-black">Nhận diện 4 thanh điệu (Tones)</p>
                            <p class="text-xs text-slate-500 mt-0.5">Luyện phân biệt Thanh 1 (55), Thanh 2 (35), Thanh 3 (214), Thanh 4 (51)</p>
                        </div>
                    </button>

                    <button type="button"
                            @click="mode = 'vocabulary'"
                            class="w-full rounded-2xl border-2 p-4 text-left transition flex items-start gap-3"
                            :class="mode === 'vocabulary' ? 'border-amber-500 bg-amber-50/70 text-slate-950 shadow-sm' : 'border-slate-200 hover:border-slate-300 text-slate-700'">
                        <div class="grid h-9 w-9 shrink-0 place-items-center rounded-xl"
                             :class="mode === 'vocabulary' ? 'bg-amber-600 text-white' : 'bg-slate-100 text-slate-600'">
                            <i data-lucide="book-open" class="h-5 w-5"></i>
                        </div>
                        <div>
                            <p class="text-sm font-black">Từ vựng giao tiếp &amp; Flashcards</p>
                            <p class="text-xs text-slate-500 mt-0.5">Nghe phát âm từ vựng và chọn nghĩa tiếng Việt chuẩn xác</p>
                        </div>
                    </button>
                </div>
            </div>

            {{-- HSK Filter (for Vocabulary mode) --}}
            <div x-show="mode === 'vocabulary'">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">
                    2. Cấp độ HSK:
                </label>
                <div class="flex flex-wrap gap-2">
                    <button type="button"
                            @click="selectedHsk = null"
                            class="rounded-xl px-3.5 py-1.5 text-xs font-bold transition border"
                            :class="selectedHsk === null ? 'bg-slate-900 text-white border-slate-900' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100'">
                        Tất cả cấp độ
                    </button>
                    @foreach($hskLevels as $lvl)
                    <button type="button"
                            @click="selectedHsk = {{ $lvl }}"
                            class="rounded-xl px-3.5 py-1.5 text-xs font-bold transition border"
                            :class="selectedHsk === {{ $lvl }} ? 'bg-slate-900 text-white border-slate-900' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100'">
                        HSK {{ $lvl }}
                    </button>
                    @endforeach
                </div>
            </div>

            {{-- Start Button --}}
            <div class="pt-4">
                <button type="button"
                        @click="startGame()"
                        :disabled="isLoading"
                        class="w-full flex items-center justify-center gap-2 rounded-2xl bg-amber-600 px-6 py-4 text-base font-bold text-white shadow-lg shadow-amber-950/20 transition hover:bg-amber-700 active:scale-[0.99] disabled:opacity-50">
                    <i data-lucide="headphones" class="h-5 w-5"></i>
                    <span x-text="isLoading ? 'Đang tải âm thanh...' : 'Bắt đầu nghe & đố vui'"></span>
                </button>
            </div>
        </div>
    </div>

    {{-- ========================================================================= --}}
    {{-- SCREEN 2: PLAYING QUESTION SCREEN --}}
    {{-- ========================================================================= --}}
    <div x-show="viewState === 'playing'"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         class="space-y-6">

        {{-- Progress Bar & HUD --}}
        <div class="flex items-center justify-between text-xs font-bold text-slate-600">
            <span class="flex items-center gap-1.5">
                <i data-lucide="headphones" class="h-4 w-4 text-amber-600"></i>
                <span>Câu <span class="text-slate-950 font-black" x-text="currentIdx + 1"></span> / <span x-text="totalQuestions"></span></span>
            </span>
            <span class="text-amber-700 font-semibold" x-text="currentQuestion ? currentQuestion.group_title : ''"></span>
        </div>

        <div class="h-2 w-full overflow-hidden rounded-full bg-slate-200">
            <div class="h-full rounded-full bg-amber-500 transition-all duration-300"
                 :style="`width: ${((currentIdx + 1) / totalQuestions) * 100}%`"></div>
        </div>

        {{-- Audio Player Center Card --}}
        <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-lg shadow-slate-900/5 text-center space-y-6">
            <p class="text-xs uppercase font-bold tracking-wider text-slate-400"
               x-text="currentQuestion ? currentQuestion.prompt : 'Nghe phát âm và chọn đáp án đúng:'"></p>

            {{-- Big Audio Play Button with Ripple Animation --}}
            <div class="relative flex items-center justify-center py-2">
                <button type="button"
                        @click="playAudio()"
                        class="relative z-10 grid h-24 w-24 place-items-center rounded-3xl bg-amber-600 text-white shadow-xl shadow-amber-600/30 transition hover:scale-105 active:scale-95">
                    <i data-lucide="volume-2" class="h-10 w-10"></i>
                </button>
                <div x-show="isPlayingAudio"
                     class="absolute h-32 w-32 rounded-full border-4 border-amber-400/40 animate-ping pointer-events-none"></div>
            </div>

            <div class="flex items-center justify-center gap-3">
                <button type="button"
                        @click="playAudio()"
                        class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-4 py-1.5 text-xs font-bold text-slate-700 hover:bg-slate-200 transition">
                    <i data-lucide="rotate-cw" class="h-3.5 w-3.5"></i>
                    <span>Nghe lại (Phím Space)</span>
                </button>
                <button type="button"
                        @click="toggleSpeechSpeed()"
                        class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 border border-amber-200 px-3 py-1.5 text-xs font-bold text-amber-900 hover:bg-amber-100 transition">
                    <i data-lucide="gauge" class="h-3.5 w-3.5 text-amber-600"></i>
                    <span x-text="`Tốc độ: ${speechSpeed}x`"></span>
                </button>
            </div>
        </div>

        {{-- 4 Answer Options --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
            <template x-for="(opt, optIdx) in (currentQuestion ? currentQuestion.options : [])" :key="optIdx">
                <button type="button"
                        @click="selectOption(opt)"
                        :disabled="isAnswered"
                        class="relative flex items-center justify-between rounded-2xl border-2 p-5 text-left transition-all active:scale-[0.98]"
                        :class="getOptionClass(opt)">
                    <span class="text-lg font-black" x-html="formatOptionText(opt)"></span>

                    <template x-if="isAnswered && opt === selectedOption">
                        <span class="grid h-7 w-7 place-items-center rounded-full text-white"
                              :class="isCorrect ? 'bg-emerald-500' : 'bg-rose-500'">
                            <i :data-lucide="isCorrect ? 'check' : 'x'" class="h-4 w-4"></i>
                        </span>
                    </template>
                </button>
            </template>
        </div>

        {{-- Feedback Card on Answer --}}
        <div x-show="isAnswered"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="rounded-2xl p-4 border"
             :class="isCorrect ? 'bg-emerald-50 border-emerald-200 text-emerald-950' : 'bg-rose-50 border-rose-200 text-rose-950'">

            <div class="flex items-start justify-between gap-3">
                <div class="space-y-1">
                    <p class="text-sm font-black flex items-center gap-1.5">
                        <i :data-lucide="isCorrect ? 'check-circle' : 'alert-circle'" class="h-4 w-4"
                           :class="isCorrect ? 'text-emerald-600' : 'text-rose-600'"></i>
                        <span x-text="isCorrect ? 'Chính xác! Đôi tai tuyệt vời!' : 'Chưa chính xác!'"></span>
                    </p>
                    <p class="text-xs" x-text="currentQuestion ? currentQuestion.hint : ''"></p>

                    <template x-if="!isCorrect && currentQuestion && currentQuestion.pinyin_link">
                        <div class="pt-1">
                            <a :href="currentQuestion.pinyin_link"
                               target="_blank"
                               class="inline-flex items-center gap-1 text-xs font-bold text-[#991b1b] underline hover:text-red-700">
                                <span x-text="currentQuestion.link_text || 'Xem bài học trên Bảng Pinyin'"></span>
                                <i data-lucide="external-link" class="h-3 w-3"></i>
                            </a>
                        </div>
                    </template>
                </div>

                <button type="button"
                        @click="nextQuestion()"
                        class="shrink-0 rounded-xl px-4 py-2 text-xs font-bold text-white shadow-sm transition"
                        :class="isCorrect ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-rose-600 hover:bg-rose-700'">
                    <span x-text="currentIdx < totalQuestions - 1 ? 'Câu tiếp theo →' : 'Xem kết quả ✓'"></span>
                </button>
            </div>
        </div>
    </div>

    {{-- ========================================================================= --}}
    {{-- SCREEN 3: END-GAME SUMMARY SCREEN --}}
    {{-- ========================================================================= --}}
    <div x-show="viewState === 'finished'"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-10 shadow-2xl shadow-slate-900/10 text-center max-w-xl mx-auto">

        {{-- Medal & Fanfare Header --}}
        <div class="space-y-3">
            <template x-if="result && result.medal === 'gold'">
                <div class="inline-flex h-20 w-20 items-center justify-center rounded-3xl bg-amber-100 border-2 border-amber-300 text-amber-600 shadow-lg shadow-amber-500/10">
                    <span class="text-4xl">🥇</span>
                </div>
            </template>
            <template x-if="result && result.medal === 'silver'">
                <div class="inline-flex h-20 w-20 items-center justify-center rounded-3xl bg-slate-100 border-2 border-slate-300 text-slate-600 shadow-lg">
                    <span class="text-4xl">🥈</span>
                </div>
            </template>
            <template x-if="result && result.medal === 'bronze'">
                <div class="inline-flex h-20 w-20 items-center justify-center rounded-3xl bg-amber-50 border-2 border-amber-700/30 text-amber-800 shadow-md">
                    <span class="text-4xl">🥉</span>
                </div>
            </template>
            <template x-if="!result || result.medal === 'none'">
                <div class="inline-flex h-20 w-20 items-center justify-center rounded-3xl bg-slate-100 text-slate-500">
                    <i data-lucide="check" class="h-10 w-10"></i>
                </div>
            </template>

            <h2 class="text-2xl sm:text-3xl font-black text-slate-950">
                <span x-text="result && result.medal === 'gold' ? 'Tai Thính Tuyệt Đối!' : (result && result.medal === 'silver' ? 'Phản Xạ Rất Tốt!' : 'Hoàn Thành Bài Nghe!')"></span>
            </h2>
            <p class="text-xs sm:text-sm text-slate-600">
                Kết quả kiểm tra thính lực đã được hệ thống xác thực độc lập.
            </p>
        </div>

        {{-- Stats Grid --}}
        <div class="mt-6 grid grid-cols-2 sm:grid-cols-4 gap-2.5 text-center">
            <div class="rounded-2xl bg-slate-50 border border-slate-100 p-3">
                <p class="text-[10px] font-bold uppercase text-slate-400">Điểm phản xạ</p>
                <p class="text-xl font-black text-amber-600 mt-0.5" x-text="result ? result.score : 0"></p>
            </div>
            <div class="rounded-2xl bg-slate-50 border border-slate-100 p-3">
                <p class="text-[10px] font-bold uppercase text-slate-400">Độ chính xác</p>
                <p class="text-xl font-black text-emerald-600 mt-0.5" x-text="result ? `${result.accuracy}%` : '0%'"></p>
            </div>
            <div class="rounded-2xl bg-slate-50 border border-slate-100 p-3">
                <p class="text-[10px] font-bold uppercase text-slate-400">Số câu đúng</p>
                <p class="text-xl font-black text-slate-800 mt-0.5" x-text="result ? `${result.correct_count}/${result.total_questions}` : '0/5'"></p>
            </div>
            <div class="rounded-2xl bg-slate-50 border border-slate-100 p-3">
                <p class="text-[10px] font-bold uppercase text-slate-400">Thời gian</p>
                <p class="text-xl font-black text-slate-800 mt-0.5" x-text="result ? `${result.duration_seconds}s` : '0s'"></p>
            </div>
        </div>

        {{-- XP Awarded & Daily Goal Badge --}}
        <div class="mt-4 flex items-center justify-between rounded-2xl bg-amber-50/80 border border-amber-200/80 px-4 py-2.5 text-xs text-amber-900">
            <span class="font-bold flex items-center gap-1.5">
                <i data-lucide="sparkles" class="h-4 w-4 text-amber-500 fill-current"></i>
                <span x-text="result && result.xp_earned > 0 ? `+${result.xp_earned} XP thưởng thực tế` : 'Đã đạt trần XP mini-game hôm nay'"></span>
            </span>
            <span class="text-[11px] text-amber-700 font-semibold">+34% mục tiêu ngày</span>
        </div>

        {{-- MISTAKE-DRIVEN REVIEW SECTION --}}
        <div class="mt-6 pt-6 border-t border-slate-100">
            <template x-if="result && result.missed_words && result.missed_words.length > 0">
                <div class="space-y-3">
                    <div class="rounded-2xl bg-rose-50 border border-rose-200 p-3.5 text-left flex items-start gap-3">
                        <i data-lucide="alert-triangle" class="h-5 w-5 text-rose-600 shrink-0 mt-0.5"></i>
                        <div>
                            <p class="text-xs font-bold text-rose-950">Phát hiện bẫy âm nghe nhầm</p>
                            <p class="text-[11px] text-rose-700 mt-0.5">
                                Bạn đã nghe nhầm <span class="font-black" x-text="result.missed_words.length"></span> câu. Hãy ôn lại ngay để định hình phản xạ chuẩn!
                            </p>
                        </div>
                    </div>

                    {{-- Primary CTA: Review Missed Audio Items --}}
                    <button type="button"
                            @click="openReviewModal()"
                            class="w-full flex items-center justify-center gap-2 rounded-2xl bg-rose-600 px-5 py-3.5 text-sm font-bold text-white shadow-lg shadow-rose-950/20 transition hover:bg-rose-700 active:scale-[0.99]">
                        <i data-lucide="rotate-ccw" class="h-4 w-4"></i>
                        <span x-text="`Ôn ngay ${result.missed_words.length} câu nghe nhầm`"></span>
                    </button>
                </div>
            </template>

            <template x-if="result && (!result.missed_words || result.missed_words.length === 0)">
                <div class="rounded-2xl bg-emerald-50 border border-emerald-200 p-3.5 text-center text-xs font-bold text-emerald-900">
                    🎉 Hoàn hảo! Bạn trả lời đúng 100% tất cả các câu nghe!
                </div>
            </template>
        </div>

        {{-- Secondary Action Buttons --}}
        <div class="mt-4 flex flex-col sm:flex-row gap-2">
            <button type="button"
                    @click="resetToSetup()"
                    class="flex-1 flex items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-xs font-bold text-slate-800 transition hover:bg-slate-100">
                <i data-lucide="play" class="h-3.5 w-3.5"></i>
                <span>Chơi lại ván mới</span>
            </button>
            <a href="{{ route('games.index') }}"
               class="flex-1 flex items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-3 text-xs font-bold text-slate-700 transition hover:bg-slate-50">
                <i data-lucide="layout-grid" class="h-3.5 w-3.5"></i>
                <span>Về Game Hub</span>
            </a>
        </div>
    </div>

    {{-- ========================================================================= --}}
    {{-- MODAL: SRS REVIEW MISSED AUDIO QUESTIONS --}}
    {{-- ========================================================================= --}}
    <div x-show="reviewModalOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/70 p-4 backdrop-blur-sm"
         style="display:none;">

        <div @click.away="reviewModalOpen = false"
             class="w-full max-w-md rounded-3xl bg-white p-6 shadow-2xl space-y-6">

            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <span class="grid h-7 w-7 place-items-center rounded-xl bg-rose-100 text-rose-700">
                        <i data-lucide="headphones" class="h-4 w-4"></i>
                    </span>
                    <h3 class="text-sm font-black text-slate-900">
                        Ôn tập câu nhầm (<span x-text="`${currentReviewIdx + 1}/${currentReviewList.length}`"></span>)
                    </h3>
                </div>
                <button type="button" @click="reviewModalOpen = false" class="text-slate-400 hover:text-slate-600">
                    <i data-lucide="x" class="h-5 w-5"></i>
                </button>
            </div>

            <template x-if="currentReviewItem">
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-6 text-center space-y-3">
                    <p class="text-3xl font-black text-slate-900" x-text="currentReviewItem.hanzi"></p>
                    <p class="text-lg font-bold" x-html="formatOptionText(currentReviewItem.pinyin)"></p>
                    <p class="text-xs font-semibold text-slate-600 pt-1" x-text="currentReviewItem.meaning"></p>

                    <div class="rounded-xl bg-amber-50 border border-amber-200 p-2.5 text-xs text-amber-900 text-left mt-2"
                         x-text="currentReviewItem.hint"></div>

                    <div class="pt-2 flex flex-col gap-2">
                        <button type="button"
                                @click="speakWord(currentReviewItem.hanzi)"
                                class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-white border border-slate-200 px-4 py-2 text-xs font-bold text-slate-700 shadow-sm hover:bg-slate-100 transition">
                            <i data-lucide="volume-2" class="h-4 w-4 text-amber-600"></i>
                            <span>Nghe lại âm chuẩn</span>
                        </button>

                        <template x-if="currentReviewItem.pinyin_link">
                            <a :href="currentReviewItem.pinyin_link"
                               target="_blank"
                               class="inline-flex items-center justify-center gap-1 text-xs font-bold text-[#991b1b] hover:underline">
                                <span x-text="currentReviewItem.link_text || 'Học chuyên sâu tại Bảng Pinyin'"></span>
                                <i data-lucide="external-link" class="h-3 w-3"></i>
                            </a>
                        </template>
                    </div>
                </div>
            </template>

            <div class="flex items-center justify-between gap-3 pt-2">
                <button type="button"
                        @click="prevReviewItem()"
                        :disabled="currentReviewIdx === 0"
                        class="rounded-xl border border-slate-200 px-4 py-2 text-xs font-bold text-slate-700 disabled:opacity-30">
                    ← Câu trước
                </button>

                <button type="button"
                        @click="nextReviewItem()"
                        class="flex-1 rounded-xl bg-slate-900 px-4 py-2 text-xs font-bold text-white hover:bg-slate-800 transition">
                    <span x-text="currentReviewIdx < currentReviewList.length - 1 ? 'Câu tiếp theo →' : 'Đã ôn xong ✓'"></span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function audioQuizGame() {
    return {
        viewState: 'setup', // 'setup' | 'playing' | 'finished'
        mode: 'minimal_pairs',
        selectedHsk: null,
        speechSpeed: 1.0,
        sfxEnabled: true,
        isLoading: false,

        token: null,
        questions: [],
        totalQuestions: 0,
        currentIdx: 0,
        answers: {},

        // Current question state
        selectedOption: null,
        isAnswered: false,
        isCorrect: false,
        questionStartTime: null,
        isPlayingAudio: false,

        elapsedSeconds: 0,
        timerInterval: null,
        result: null,

        reviewModalOpen: false,
        currentReviewIdx: 0,
        currentReviewList: [],

        init() {
            if (window.soundEngine) {
                this.sfxEnabled = window.soundEngine.isSfxEnabled();
            }

            // Keyboard shortcut: Space to replay audio
            window.addEventListener('keydown', (e) => {
                if (e.code === 'Space' && this.viewState === 'playing' && !this.isAnswered) {
                    e.preventDefault();
                    this.playAudio();
                }
            });
        },

        toggleSfx() {
            if (window.soundEngine) {
                this.sfxEnabled = window.soundEngine.toggleSfx();
            }
        },

        toggleSpeechSpeed() {
            this.speechSpeed = this.speechSpeed === 1.0 ? 0.75 : 1.0;
        },

        get currentQuestion() {
            if (this.questions.length === 0 || this.currentIdx >= this.questions.length) return null;
            return this.questions[this.currentIdx];
        },

        async startGame() {
            this.isLoading = true;
            try {
                let url = `/games/audio-quiz/data?mode=${this.mode}`;
                if (this.selectedHsk && this.mode === 'vocabulary') {
                    url += `&hsk=${this.selectedHsk}`;
                }

                const res = await fetch(url, {
                    headers: { 'Accept': 'application/json' }
                });
                const json = await res.json();

                if (!json.success || !json.data) {
                    throw new Error('Không thể tải câu hỏi audio.');
                }

                const data = json.data;
                this.token = data.token;
                this.questions = data.questions;
                this.totalQuestions = data.total_questions;
                this.currentIdx = 0;
                this.answers = {};
                this.elapsedSeconds = 0;
                this.result = null;

                this.viewState = 'playing';
                this.loadQuestion();
                this.startTimer();

                this.$nextTick(() => {
                    if (window.lucide) window.lucide.createIcons();
                });
            } catch (e) {
                alert('Có lỗi khi khởi tạo Audio Pop Quiz: ' + e.message);
            } finally {
                this.isLoading = false;
            }
        },

        startTimer() {
            clearInterval(this.timerInterval);
            this.timerInterval = setInterval(() => {
                this.elapsedSeconds++;
            }, 1000);
        },

        stopTimer() {
            clearInterval(this.timerInterval);
        },

        loadQuestion() {
            this.selectedOption = null;
            this.isAnswered = false;
            this.isCorrect = false;
            this.questionStartTime = Date.now();

            // Auto-play audio with 200ms delay
            setTimeout(() => {
                this.playAudio();
            }, 250);

            this.$nextTick(() => {
                if (window.lucide) window.lucide.createIcons();
            });
        },

        playAudio() {
            if (!this.currentQuestion) return;
            this.isPlayingAudio = true;

            const text = this.currentQuestion.audio_text || this.currentQuestion.audio_pinyin;

            if ('speechSynthesis' in window) {
                window.speechSynthesis.cancel();
                const utterance = new SpeechSynthesisUtterance(text);
                utterance.lang = 'zh-CN';
                utterance.rate = this.speechSpeed;
                utterance.onend = () => {
                    this.isPlayingAudio = false;
                };
                utterance.onerror = () => {
                    this.isPlayingAudio = false;
                };
                window.speechSynthesis.speak(utterance);
            } else {
                this.isPlayingAudio = false;
            }
        },

        selectOption(opt) {
            if (this.isAnswered || !this.currentQuestion) return;

            const responseMs = Date.now() - (this.questionStartTime || Date.now());
            this.selectedOption = opt;
            this.isAnswered = true;

            // Record response
            this.answers[this.currentQuestion.id] = {
                selected: opt,
                response_ms: responseMs
            };

            // Sound feedback
            // Note: Server has ultimate verification, locally we play tap / ding
            if (window.soundEngine) {
                window.soundEngine.play('tap');
            }

            this.$nextTick(() => {
                if (window.lucide) window.lucide.createIcons();
            });
        },

        nextQuestion() {
            if (this.currentIdx < this.totalQuestions - 1) {
                this.currentIdx++;
                this.loadQuestion();
            } else {
                this.finishGame();
            }
        },

        getOptionClass(opt) {
            if (!this.isAnswered) {
                return 'border-slate-200 bg-white hover:border-amber-300 hover:shadow-md text-slate-800';
            }

            if (opt === this.selectedOption) {
                return 'border-amber-500 bg-amber-50 text-amber-950 font-black ring-2 ring-amber-400';
            }

            return 'border-slate-100 bg-slate-50 text-slate-400 opacity-60';
        },

        async finishGame() {
            this.stopTimer();

            try {
                const res = await fetch('/games/audio-quiz/finish', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    },
                    body: JSON.stringify({
                        token: this.token,
                        answers: this.answers,
                        duration_seconds: Math.max(1, this.elapsedSeconds)
                    })
                });

                const data = await res.json();
                if (!data.success) {
                    throw new Error(data.message || 'Không thể xác thực kết quả.');
                }

                this.result = data;
                this.currentReviewList = data.missed_words || [];
                this.currentReviewIdx = 0;

                this.viewState = 'finished';

                if (window.soundEngine) {
                    window.soundEngine.play('fanfare');
                }

                this.$nextTick(() => {
                    if (window.lucide) window.lucide.createIcons();
                });
            } catch (e) {
                alert('Có lỗi khi lưu kết quả: ' + e.message);
                this.viewState = 'setup';
            }
        },

        get currentReviewItem() {
            if (this.currentReviewList.length === 0) return null;
            return this.currentReviewList[this.currentReviewIdx];
        },

        openReviewModal() {
            this.currentReviewIdx = 0;
            this.reviewModalOpen = true;
            this.$nextTick(() => {
                if (window.lucide) window.lucide.createIcons();
            });
        },

        nextReviewItem() {
            if (this.currentReviewIdx < this.currentReviewList.length - 1) {
                this.currentReviewIdx++;
            } else {
                this.reviewModalOpen = false;
            }
        },

        prevReviewItem() {
            if (this.currentReviewIdx > 0) {
                this.currentReviewIdx--;
            }
        },

        speakWord(text) {
            if ('speechSynthesis' in window) {
                const utterance = new SpeechSynthesisUtterance(text);
                utterance.lang = 'zh-CN';
                utterance.rate = 0.85;
                window.speechSynthesis.speak(utterance);
            }
        },

        resetToSetup() {
            this.viewState = 'setup';
            this.$nextTick(() => {
                if (window.lucide) window.lucide.createIcons();
            });
        },

        formatOptionText(text) {
            if (!text) return '';
            if (typeof window.formatTonePinyin === 'function') {
                return window.formatTonePinyin(text);
            }
            return text;
        }
    };
}
</script>
@endsection
