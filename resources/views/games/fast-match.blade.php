@extends('layouts.app')

@section('title', 'Fast Match - Nối Từ Siêu Tốc | Learn Chinese')

@section('content')
<div x-data="fastMatchGame()" x-init="init()" class="max-w-4xl mx-auto pb-16">

    {{-- Breadcrumb & Audio Controls Bar --}}
    <div class="flex items-center justify-between gap-4 mb-6">
        <a href="{{ route('games.index') }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-600 hover:text-[#991b1b] transition">
            <i data-lucide="arrow-left" class="h-4 w-4"></i>
            <span>Về Game Hub</span>
        </a>

        <div class="flex items-center gap-2">
            <button type="button"
                    @click="toggleSfx()"
                    class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-bold transition border"
                    :class="sfxEnabled ? 'bg-amber-50 text-amber-900 border-amber-300' : 'bg-slate-100 text-slate-400 border-slate-200'">
                <i :data-lucide="sfxEnabled ? 'volume-2' : 'volume-x'" class="h-3.5 w-3.5"></i>
                <span x-text="sfxEnabled ? 'Âm thanh: Bật' : 'Âm thanh: Tắt'"></span>
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
            <span class="inline-flex items-center gap-1.5 rounded-full bg-red-100 px-3.5 py-1 text-xs font-bold uppercase tracking-wider text-[#991b1b]">
                <i data-lucide="zap" class="h-3.5 w-3.5"></i>
                Mini-Game Phản Xạ
            </span>
            <h1 class="mt-3 text-3xl sm:text-4xl font-black text-slate-950">Fast Match</h1>
            <p class="mt-2 text-sm text-slate-600">
                Lật mở và ghép các cặp thẻ Hán tự tương ứng với Nghĩa hoặc Pinyin. Càng chuẩn xác và nhanh, điểm số và combo càng cao!
            </p>
        </div>

        <div class="mt-8 space-y-6 max-w-xl mx-auto">
            {{-- Difficulty Selection --}}
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">
                    1. Chọn độ khó:
                </label>
                <div class="grid grid-cols-3 gap-3">
                    <button type="button"
                            @click="difficulty = 'easy'"
                            class="rounded-2xl border-2 p-3.5 text-center transition"
                            :class="difficulty === 'easy' ? 'border-[#991b1b] bg-red-50 text-[#991b1b] font-black shadow-sm' : 'border-slate-200 hover:border-slate-300 text-slate-700 font-bold'">
                        <p class="text-sm">Dễ</p>
                        <p class="text-[11px] text-slate-500 font-normal mt-0.5">4 cặp (8 thẻ)</p>
                    </button>
                    <button type="button"
                            @click="difficulty = 'medium'"
                            class="rounded-2xl border-2 p-3.5 text-center transition"
                            :class="difficulty === 'medium' ? 'border-[#991b1b] bg-red-50 text-[#991b1b] font-black shadow-sm' : 'border-slate-200 hover:border-slate-300 text-slate-700 font-bold'">
                        <p class="text-sm">Vừa</p>
                        <p class="text-[11px] text-slate-500 font-normal mt-0.5">5 cặp (10 thẻ)</p>
                    </button>
                    <button type="button"
                            @click="difficulty = 'challenge'"
                            class="rounded-2xl border-2 p-3.5 text-center transition"
                            :class="difficulty === 'challenge' ? 'border-[#991b1b] bg-red-50 text-[#991b1b] font-black shadow-sm' : 'border-slate-200 hover:border-slate-300 text-slate-700 font-bold'">
                        <p class="text-sm">Thử thách</p>
                        <p class="text-[11px] text-slate-500 font-normal mt-0.5">6 cặp (12 thẻ)</p>
                    </button>
                </div>
            </div>

            {{-- Mode Selection --}}
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">
                    2. Chế độ chơi:
                </label>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <button type="button"
                            @click="mode = 'practice'"
                            class="rounded-2xl border-2 p-3 text-left transition"
                            :class="mode === 'practice' ? 'border-[#991b1b] bg-red-50 text-[#991b1b] shadow-sm' : 'border-slate-200 hover:border-slate-300 text-slate-700'">
                        <div class="flex items-center gap-2">
                            <i data-lucide="infinity" class="h-4 w-4 shrink-0"></i>
                            <span class="text-xs font-bold">Luyện tập</span>
                        </div>
                        <p class="text-[11px] text-slate-500 mt-1">Không giới hạn giờ</p>
                    </button>
                    <button type="button"
                            @click="mode = 'time_challenge'"
                            class="rounded-2xl border-2 p-3 text-left transition"
                            :class="mode === 'time_challenge' ? 'border-[#991b1b] bg-red-50 text-[#991b1b] shadow-sm' : 'border-slate-200 hover:border-slate-300 text-slate-700'">
                        <div class="flex items-center gap-2">
                            <i data-lucide="timer" class="h-4 w-4 shrink-0"></i>
                            <span class="text-xs font-bold">Thời gian</span>
                        </div>
                        <p class="text-[11px] text-slate-500 mt-1">45s - 75s đếm ngược</p>
                    </button>
                    <button type="button"
                            @click="mode = 'hanzi_pinyin'"
                            class="rounded-2xl border-2 p-3 text-left transition"
                            :class="mode === 'hanzi_pinyin' ? 'border-[#991b1b] bg-red-50 text-[#991b1b] shadow-sm' : 'border-slate-200 hover:border-slate-300 text-slate-700'">
                        <div class="flex items-center gap-2">
                            <i data-lucide="type" class="h-4 w-4 shrink-0"></i>
                            <span class="text-xs font-bold">Hán tự ↔ Pinyin</span>
                        </div>
                        <p class="text-[11px] text-slate-500 mt-1">Rèn thanh điệu màu</p>
                    </button>
                </div>
            </div>

            {{-- HSK Level Filter --}}
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">
                    3. Giới hạn cấp độ từ vựng (tuỳ chọn):
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
                        class="w-full flex items-center justify-center gap-2 rounded-2xl bg-[#991b1b] px-6 py-4 text-base font-bold text-white shadow-lg shadow-red-950/20 transition hover:bg-red-800 active:scale-[0.99] disabled:opacity-50">
                    <i data-lucide="play" class="h-5 w-5 fill-current"></i>
                    <span x-text="isLoading ? 'Đang chuẩn bị thẻ...' : 'Bắt đầu ghép thẻ'"></span>
                </button>
            </div>
        </div>
    </div>

    {{-- ========================================================================= --}}
    {{-- SCREEN 2: PLAYING SCREEN --}}
    {{-- ========================================================================= --}}
    <div x-show="viewState === 'playing'"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         class="space-y-6">

        {{-- Top HUD Status Bar --}}
        <div class="flex items-center justify-between rounded-2xl bg-slate-900 px-5 py-3.5 text-white shadow-md">
            {{-- Pairs Progress --}}
            <div class="flex items-center gap-2">
                <i data-lucide="layers" class="h-4 w-4 text-slate-400"></i>
                <span class="text-xs font-semibold text-slate-300">Đã ghép:</span>
                <span class="text-sm font-black text-amber-400" x-text="`${solvedPairs}/${totalPairs}`"></span>
            </div>

            {{-- Combo Badge --}}
            <div class="flex items-center gap-2">
                <div class="flex items-center gap-1 rounded-full px-3 py-0.5 text-xs font-black transition-transform"
                     :class="combo > 0 ? 'bg-amber-400/20 text-amber-300 border border-amber-400/30 scale-105' : 'bg-white/5 text-slate-400'">
                    <span :class="combo >= 3 ? 'text-amber-400 animate-pulse' : 'text-slate-400'" class="inline-flex">
                        <i data-lucide="flame" class="h-3.5 w-3.5"></i>
                    </span>
                    <span x-text="`Combo x${combo}`"></span>
                </div>
            </div>

            {{-- Timer --}}
            <div class="flex items-center gap-2">
                <i data-lucide="timer" class="h-4 w-4 text-slate-400"></i>
                <template x-if="timeRemaining !== null">
                    <span class="font-mono text-sm font-black"
                          :class="timeRemaining <= 10 ? 'text-red-400 animate-pulse' : 'text-white'"
                          x-text="`${timeRemaining}s`"></span>
                </template>
                <template x-if="timeRemaining === null">
                    <span class="font-mono text-sm font-black text-white" x-text="formatTime(elapsedSeconds)"></span>
                </template>
            </div>
        </div>

        {{-- Combo Message Toast --}}
        <div class="h-6 flex items-center justify-center">
            <span x-show="comboMessage"
                  x-transition:enter="transition ease-out duration-200"
                  x-transition:enter-start="opacity-0 -translate-y-2"
                  x-transition:enter-end="opacity-100 translate-y-0"
                  x-transition:leave="transition ease-in duration-150"
                  x-transition:leave-start="opacity-100"
                  x-transition:leave-end="opacity-0"
                  class="text-xs font-black tracking-wide text-amber-600 bg-amber-50 border border-amber-200 px-3 py-0.5 rounded-full"
                  x-text="comboMessage"></span>
        </div>

        {{-- Interactive Cards Grid --}}
        <div class="grid gap-3 sm:gap-4"
             :class="cards.length <= 8 ? 'grid-cols-2 sm:grid-cols-4' : (cards.length <= 10 ? 'grid-cols-2 sm:grid-cols-3 md:grid-cols-5' : 'grid-cols-2 sm:grid-cols-3 md:grid-cols-4')">

            <template x-for="card in cards" :key="card.id">
                <div @click="onCardClick(card)"
                     class="group relative flex min-h-[105px] sm:min-h-[125px] cursor-pointer select-none flex-col items-center justify-center rounded-2xl border-2 p-3 text-center transition-all duration-200 active:scale-95"
                     :class="{
                         'opacity-20 scale-95 pointer-events-none border-emerald-400 bg-emerald-50 text-emerald-800': card.isMatched,
                         'ring-4 ring-amber-400 border-amber-400 bg-amber-50 scale-[1.02] shadow-lg': card.isSelected && !card.isMatched && !card.isWrong,
                         'ring-4 ring-rose-500 border-rose-500 bg-rose-50 text-rose-800 animate-shake': card.isWrong,
                         'border-slate-200 bg-white hover:border-slate-300 hover:shadow-md text-slate-800': !card.isSelected && !card.isMatched && !card.isWrong
                     }">

                    {{-- Card Content --}}
                    <template x-if="card.type === 'hanzi'">
                        <div class="space-y-1">
                            <span class="text-2xl sm:text-3xl font-black tracking-wide" x-text="card.content"></span>
                            <p class="text-[10px] uppercase font-bold text-slate-400 tracking-widest">Hán tự</p>
                        </div>
                    </template>

                    <template x-if="card.type === 'meaning'">
                        <div class="space-y-1 px-1">
                            <span class="text-xs sm:text-sm font-bold text-slate-800 leading-snug line-clamp-3" x-text="card.content"></span>
                            <p class="text-[10px] uppercase font-bold text-slate-400 tracking-widest">Nghĩa</p>
                        </div>
                    </template>

                    <template x-if="card.type === 'pinyin'">
                        <div class="space-y-1">
                            <span class="text-base sm:text-lg font-black tracking-wide" x-html="formatPinyin(card.content)"></span>
                            <p class="text-[10px] uppercase font-bold text-slate-400 tracking-widest">Pinyin</p>
                        </div>
                    </template>
                </div>
            </template>
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
                <span x-text="result && result.medal === 'gold' ? 'Xuất Sắc! Hoàn Hảo!' : (result && result.medal === 'silver' ? 'Làm Tốt Lắm!' : 'Hoàn Thành Ván Chơi!')"></span>
            </h2>
            <p class="text-xs sm:text-sm text-slate-600">
                Kết quả đã được hệ thống xác thực độc lập và ghi nhận vào tiến độ học tập.
            </p>
        </div>

        {{-- Verified Results Grid --}}
        <div class="mt-6 grid grid-cols-2 sm:grid-cols-4 gap-2.5 text-center">
            <div class="rounded-2xl bg-slate-50 border border-slate-100 p-3">
                <p class="text-[10px] font-bold uppercase text-slate-400">Điểm số</p>
                <p class="text-xl font-black text-[#991b1b] mt-0.5" x-text="result ? result.score : 0"></p>
            </div>
            <div class="rounded-2xl bg-slate-50 border border-slate-100 p-3">
                <p class="text-[10px] font-bold uppercase text-slate-400">Độ chính xác</p>
                <p class="text-xl font-black text-emerald-600 mt-0.5" x-text="result ? `${result.accuracy}%` : '0%'"></p>
            </div>
            <div class="rounded-2xl bg-slate-50 border border-slate-100 p-3">
                <p class="text-[10px] font-bold uppercase text-slate-400">Max Combo</p>
                <p class="text-xl font-black text-amber-600 mt-0.5" x-text="result ? `x${result.max_combo}` : 'x0'"></p>
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

        {{-- GUEST PROGRESS CLAIM REMINDER --}}
        @guest
        <div class="mt-4 rounded-2xl bg-amber-50/90 border border-amber-200/90 p-3.5 text-left flex items-start gap-3">
            <i data-lucide="sparkles" class="h-5 w-5 text-amber-600 shrink-0 mt-0.5 fill-current"></i>
            <div>
                <p class="text-xs font-bold text-amber-950">
                    Bạn đang có <span class="text-sm font-black text-amber-700" x-text="result && result.guest_progress ? result.guest_progress.total_xp : (result ? result.xp_earned : 0)"></span> XP đang chờ lưu
                </p>
                <p class="text-[11px] text-amber-800/90 mt-0.5">
                    <a href="{{ route('register') }}" class="font-bold underline hover:text-amber-950">Đăng ký miễn phí</a> để giữ lại tiến độ học tập.
                </p>
            </div>
        </div>
        @endguest

        {{-- MISTAKE-DRIVEN SRS REVIEW SECTION --}}
        <div class="mt-6 pt-6 border-t border-slate-100">
            <template x-if="result && result.missed_words && result.missed_words.length > 0">
                <div class="space-y-3">
                    <div class="rounded-2xl bg-rose-50 border border-rose-200 p-3.5 text-left flex items-start gap-3">
                        <i data-lucide="alert-triangle" class="h-5 w-5 text-rose-600 shrink-0 mt-0.5"></i>
                        <div>
                            <p class="text-xs font-bold text-rose-950">Phát hiện từ vựng nối nhầm</p>
                            <p class="text-[11px] text-rose-700 mt-0.5">
                                Bạn đã nối nhầm <span class="font-black" x-text="result.missed_words.length"></span> từ. Hãy ôn lại ngay để não bộ ghi nhớ chính xác!
                            </p>
                        </div>
                    </div>

                    {{-- PRIMARY CTA: RECALL MISSED WORDS --}}
                    <button type="button"
                            @click="openReviewModal()"
                            class="w-full flex items-center justify-center gap-2 rounded-2xl bg-rose-600 px-5 py-3.5 text-sm font-bold text-white shadow-lg shadow-rose-950/20 transition hover:bg-rose-700 active:scale-[0.99]">
                        <i data-lucide="rotate-ccw" class="h-4 w-4"></i>
                        <span x-text="`Ôn ngay ${result.missed_words.length} từ vừa nhầm`"></span>
                    </button>
                </div>
            </template>

            <template x-if="result && (!result.missed_words || result.missed_words.length === 0)">
                <div class="rounded-2xl bg-emerald-50 border border-emerald-200 p-3.5 text-center text-xs font-bold text-emerald-900">
                    🎉 Hoàn hảo! Bạn không phạm phải sai sót nào trong ván chơi này.
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
    {{-- MODAL: SRS REVIEW MISSED WORDS --}}
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
                        <i data-lucide="bookmark-check" class="h-4 w-4"></i>
                    </span>
                    <h3 class="text-sm font-black text-slate-900">
                        Ôn tập từ sai (<span x-text="`${currentReviewIdx + 1}/${currentReviewList.length}`"></span>)
                    </h3>
                </div>
                <button type="button" @click="reviewModalOpen = false" class="text-slate-400 hover:text-slate-600">
                    <i data-lucide="x" class="h-5 w-5"></i>
                </button>
            </div>

            <template x-if="currentReviewCard">
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-6 text-center space-y-3">
                    <p class="text-4xl font-black text-slate-900" x-text="currentReviewCard.hanzi"></p>
                    <p class="text-lg font-bold" x-html="formatPinyin(currentReviewCard.pinyin)"></p>
                    <p class="text-sm font-semibold text-slate-700 pt-2 border-t border-slate-200" x-text="currentReviewCard.meaning"></p>

                    <div class="pt-2">
                        <button type="button"
                                @click="speakWord(currentReviewCard.hanzi)"
                                class="inline-flex items-center gap-1.5 rounded-full bg-white border border-slate-200 px-4 py-1.5 text-xs font-bold text-slate-700 shadow-sm hover:bg-slate-100 transition">
                            <i data-lucide="volume-2" class="h-4 w-4 text-rose-600"></i>
                            <span>Nghe phát âm chuẩn</span>
                        </button>
                    </div>
                </div>
            </template>

            <div class="flex items-center justify-between gap-3 pt-2">
                <button type="button"
                        @click="prevReviewCard()"
                        :disabled="currentReviewIdx === 0"
                        class="rounded-xl border border-slate-200 px-4 py-2 text-xs font-bold text-slate-700 disabled:opacity-30">
                    ← Từ trước
                </button>

                <button type="button"
                        @click="nextReviewCard()"
                        class="flex-1 rounded-xl bg-slate-900 px-4 py-2 text-xs font-bold text-white hover:bg-slate-800 transition">
                    <span x-text="currentReviewIdx < currentReviewList.length - 1 ? 'Từ tiếp theo →' : 'Đã ôn xong ✓'"></span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function fastMatchGame() {
    return {
        viewState: 'setup', // 'setup' | 'playing' | 'finished'
        difficulty: 'medium',
        mode: 'practice',
        selectedHsk: null,
        isLoading: false,

        token: null,
        cards: [],
        totalPairs: 0,
        solvedPairs: 0,
        timeRemaining: null,
        elapsedSeconds: 0,
        timerInterval: null,

        firstCard: null,
        lockInput: false,
        moves: [],
        combo: 0,
        maxCombo: 0,
        comboMessage: '',

        result: null,
        sfxEnabled: true,

        reviewModalOpen: false,
        currentReviewIdx: 0,
        currentReviewList: [],

        init() {
            if (window.soundEngine) {
                this.sfxEnabled = window.soundEngine.isSfxEnabled();
            }
        },

        toggleSfx() {
            if (window.soundEngine) {
                this.sfxEnabled = window.soundEngine.toggleSfx();
            }
        },

        async startGame() {
            this.isLoading = true;
            try {
                let url = `/games/fast-match/data?mode=${this.mode}&difficulty=${this.difficulty}`;
                if (this.selectedHsk) {
                    url += `&hsk=${this.selectedHsk}`;
                }

                const res = await fetch(url, {
                    headers: { 'Accept': 'application/json' }
                });
                const json = await res.json();

                if (!json.success || !json.data) {
                    throw new Error('Không thể tải phiên chơi.');
                }

                const data = json.data;
                this.token = data.token;
                this.cards = data.cards.map(c => ({
                    ...c,
                    isSelected: false,
                    isMatched: false,
                    isWrong: false
                }));
                this.totalPairs = data.total_pairs;
                this.solvedPairs = 0;
                this.timeRemaining = data.time_limit;
                this.elapsedSeconds = 0;
                this.firstCard = null;
                this.lockInput = false;
                this.moves = [];
                this.combo = 0;
                this.maxCombo = 0;
                this.comboMessage = '';
                this.result = null;

                this.viewState = 'playing';
                this.startTimer();
                this.$nextTick(() => {
                    if (window.lucide) window.lucide.createIcons();
                });
            } catch (e) {
                alert('Có lỗi khi khởi tạo ván chơi: ' + e.message);
            } finally {
                this.isLoading = false;
            }
        },

        startTimer() {
            clearInterval(this.timerInterval);
            this.timerInterval = setInterval(() => {
                this.elapsedSeconds++;
                if (this.timeRemaining !== null) {
                    this.timeRemaining--;
                    if (this.timeRemaining <= 0) {
                        this.timeRemaining = 0;
                        this.onTimeExpired();
                    }
                }
            }, 1000);
        },

        stopTimer() {
            clearInterval(this.timerInterval);
        },

        onCardClick(card) {
            if (this.lockInput || card.isMatched || card.isSelected) return;

            // First Card Click
            if (!this.firstCard) {
                card.isSelected = true;
                this.firstCard = card;
                if (window.soundEngine) window.soundEngine.play('tap');
                return;
            }

            // Second Card Click
            const card1 = this.firstCard;
            const card2 = card;
            card2.isSelected = true;
            this.lockInput = true;

            // Record move for server replay verification
            this.moves.push({ c1: card1.id, c2: card2.id });

            // Check match via pair_hash
            const isMatch = card1.pair_hash === card2.pair_hash;

            if (isMatch) {
                // Correct Match
                setTimeout(() => {
                    card1.isMatched = true;
                    card2.isMatched = true;
                    card1.isSelected = false;
                    card2.isSelected = false;
                    this.firstCard = null;
                    this.lockInput = false;
                    this.solvedPairs++;

                    this.combo++;
                    this.maxCombo = Math.max(this.maxCombo, this.combo);

                    if (this.combo >= 3) {
                        this.comboMessage = `🔥 ${this.combo} CẶP LIÊN TIẾP!`;
                        if (window.soundEngine) window.soundEngine.play('milestone');
                    } else {
                        this.comboMessage = '';
                        if (window.soundEngine) window.soundEngine.play('ding');
                    }

                    if (this.solvedPairs >= this.totalPairs) {
                        this.finishGame();
                    }
                }, 150);
            } else {
                // Wrong Match
                card1.isWrong = true;
                card2.isWrong = true;
                this.combo = 0;
                this.comboMessage = 'Combo đã reset!';
                if (window.soundEngine) window.soundEngine.play('softError');

                setTimeout(() => {
                    card1.isWrong = false;
                    card2.isWrong = false;
                    card1.isSelected = false;
                    card2.isSelected = false;
                    this.firstCard = null;
                    this.lockInput = false;
                }, 400);
            }
        },

        onTimeExpired() {
            this.stopTimer();
            if (window.soundEngine) window.soundEngine.play('softError');
            this.finishGame();
        },

        async finishGame() {
            this.stopTimer();
            this.lockInput = true;

            // Retrieve existing guest progress from localStorage
            let existingGuest = null;
            try {
                const raw = localStorage.getItem('chinese_guest_progress');
                if (raw) existingGuest = JSON.parse(raw);
            } catch (e) {}

            try {
                const res = await fetch('/games/fast-match/finish', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    },
                    body: JSON.stringify({
                        token: this.token,
                        moves: this.moves,
                        duration_seconds: Math.max(1, this.elapsedSeconds),
                        guest_uuid: existingGuest?.guest_uuid || null,
                        claim_token: existingGuest?.claim_token || null
                    })
                });

                const data = await res.json();
                if (!data.success) {
                    throw new Error(data.message || 'Không thể xác thực kết quả.');
                }

                this.result = data;

                // If guest received signed claim token, securely persist in localStorage
                if (data.guest_progress && data.claim_token) {
                    try {
                        localStorage.setItem('chinese_guest_progress', JSON.stringify({
                            version: 1,
                            guest_uuid: data.guest_progress.guest_uuid,
                            xp: data.guest_progress.total_xp,
                            activities: data.guest_progress.activities_count,
                            last_activity_at: new Date().toISOString(),
                            claim_token: data.claim_token
                        }));
                    } catch (e) {}
                }

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

        get currentReviewCard() {
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

        nextReviewCard() {
            if (this.currentReviewIdx < this.currentReviewList.length - 1) {
                this.currentReviewIdx++;
            } else {
                this.reviewModalOpen = false;
            }
        },

        prevReviewCard() {
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

        formatPinyin(pinyin) {
            if (!pinyin) return '';
            if (typeof window.formatTonePinyin === 'function') {
                return window.formatTonePinyin(pinyin);
            }
            return pinyin;
        },

        formatTime(seconds) {
            const m = Math.floor(seconds / 60);
            const s = seconds % 60;
            return `${m}:${s < 10 ? '0' : ''}${s}`;
        }
    };
}
</script>
@endsection
