@extends('layouts.app')

@section('title', 'Flashcard | Chinese Deck')

@section('content')
<style>
    /* 3D Flip Card */
    .flip-card {
        perspective: 1000px;
    }

    .flip-card-inner {
        position: relative;
        width: 100%;
        height: 100%;
        transform-style: preserve-3d;
        transition: transform 0.55s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .flip-card-inner.flipped {
        transform: rotateY(180deg);
    }

    .flip-card-front,
    .flip-card-back {
        position: absolute;
        inset: 0;
        backface-visibility: hidden;
        -webkit-backface-visibility: hidden;
        border-radius: 2rem;
    }

    .flip-card-back {
        transform: rotateY(180deg);
    }

    /* Progress dots */
    .dot {
        width: 8px;
        height: 8px;
        border-radius: 9999px;
        transition: all .3s;
    }

    .dot.done {
        background: #991b1b;
        transform: scale(1.2);
    }

    .dot.current {
        background: #991b1b;
        opacity: 0.5;
    }

    .dot.future {
        background: #e2e8f0;
    }
</style>

<section class="grid gap-8 py-4 lg:grid-cols-[1fr_0.9fr] lg:py-8">
    <div>
        <p class="text-sm font-semibold uppercase tracking-[0.28em] text-[#991b1b]">Flashcard</p>
        <h1 class="mt-4 text-5xl font-black tracking-tight text-slate-950 sm:text-6xl">Ôn từ vựng bằng thẻ học trực quan</h1>
        <p class="mt-5 max-w-2xl text-lg leading-8 text-slate-700">
            Bấm vào thẻ để lật và xem nghĩa. Lướt qua từng thẻ, nhớ hanzi, pinyin và câu ví dụ thực tế.
        </p>
    </div>

    <div class="rounded-[2rem] bg-slate-950 p-6 text-white shadow-2xl shadow-slate-950/20">
        <p class="text-sm uppercase tracking-[0.28em] text-amber-200/80">Bộ thẻ hiện tại</p>
        <div class="mt-4 grid gap-4 sm:grid-cols-3">
            <div class="rounded-3xl border border-white/10 bg-white/5 p-4">
                <p class="text-sm text-slate-300">Thẻ trong bộ lọc</p>
                <p class="mt-2 text-3xl font-black">{{ $deckTotal }}</p>
            </div>
            <a href="{{ route('flashcards', ['starred' => 1]) }}" class="group rounded-3xl border border-amber-400/20 bg-amber-400/10 p-4 transition hover:bg-amber-400/20">
                <p class="text-sm text-amber-300 flex items-center gap-1.5">
                    <i data-lucide="star" class="h-3.5 w-3.5 fill-current text-amber-400"></i>
                    <span>Sổ từ đã lưu</span>
                </p>
                <p class="mt-2 text-3xl font-black text-white group-hover:text-amber-200 transition">{{ $starredCount }}</p>
            </a>
            <div class="rounded-3xl border border-white/10 bg-white/5 p-4">
                <p class="text-sm text-slate-300">Tổng thẻ hệ thống</p>
                <p class="mt-2 text-3xl font-black">{{ $totalCount }}</p>
            </div>
        </div>
        <div class="mt-5 rounded-3xl border border-amber-300/20 bg-gradient-to-br from-amber-300/15 to-red-500/10 p-4">
            <p class="text-sm uppercase tracking-[0.22em] text-amber-200/80">Tip học hiệu quả</p>
            <p class="mt-2 text-sm leading-6 text-slate-200">Bấm icon ⭐ để lưu từ khó nhớ vào Sổ tay, giúp bạn ôn tập riêng các từ hay quên!</p>
        </div>
    </div>
</section>

{{-- Filters and Search Toolbar --}}
<div class="mb-8 space-y-3">
    {{-- HSK Standard Mode Switcher --}}
    <div class="flex flex-wrap items-center justify-between gap-3 rounded-2xl bg-white/80 border border-slate-200/90 p-2.5 shadow-xs backdrop-blur-sm">
        <div class="flex items-center gap-2 text-xs font-bold text-slate-500 pl-1.5">
            <i data-lucide="layers" class="h-4 w-4 text-[#991b1b]"></i>
            <span>Tiêu chuẩn HSK:</span>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('flashcards', array_filter(['standard' => 'hsk_2_0', 'hsk' => $hskLevel, 'lesson' => $lessonSlug, 'q' => $search, 'starred' => $isStarred ? 1 : null])) }}"
               class="inline-flex items-center gap-1.5 rounded-xl px-3 py-1.5 text-xs font-bold transition
                      {{ ($standardCode ?? 'hsk_2_0') === 'hsk_2_0' ? 'bg-slate-900 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                <span>HSK 2.0 (2010)</span>
            </a>
            <a href="{{ route('flashcards', array_filter(['standard' => 'hsk_3_0_2026', 'hsk' => $hskLevel, 'lesson' => $lessonSlug, 'q' => $search, 'starred' => $isStarred ? 1 : null])) }}"
               class="inline-flex items-center gap-1.5 rounded-xl px-3 py-1.5 text-xs font-bold transition
                      {{ ($standardCode ?? '') === 'hsk_3_0_2026' ? 'bg-[#991b1b] text-white shadow-sm ring-2 ring-red-200' : 'bg-red-50 text-[#991b1b] hover:bg-red-100 border border-red-100' }}">
                <i data-lucide="sparkles" class="h-3 w-3"></i>
                <span>HSK 3.0 (Áp dụng 12/2026)</span>
            </a>
        </div>
    </div>

    <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
        {{-- Primary Filter Tabs, Level Pills & Lesson Dropdown --}}
        <div class="flex flex-wrap items-center gap-2">
            {{-- All Tab --}}
            <a href="{{ route('flashcards', array_filter(['standard' => $standardCode, 'q' => $search])) }}"
                class="rounded-full px-4 py-2 text-sm font-semibold transition shadow-xs
                      {{ (! $lessonSlug && ! $isStarred && ! $hskLevel) ? 'bg-[#991b1b] text-white shadow-md' : 'bg-white/80 text-slate-700 border border-slate-200 hover:border-[#991b1b] hover:text-[#991b1b]' }}">
                Tất cả
            </a>

            {{-- Starred Words Filter Tab --}}
            <a href="{{ route('flashcards', array_filter(['standard' => $standardCode, 'starred' => 1, 'q' => $search])) }}"
                class="inline-flex items-center gap-1.5 rounded-full px-4 py-2 text-sm font-semibold transition shadow-xs
                      {{ $isStarred ? 'bg-amber-500 text-white shadow-md shadow-amber-500/20' : 'bg-amber-50 text-amber-900 border border-amber-200 hover:bg-amber-100 hover:border-amber-300' }}">
                <i data-lucide="star" class="h-3.5 w-3.5 {{ $isStarred ? 'fill-current text-white' : 'fill-current text-amber-500' }}"></i>
                <span>Sổ từ đã lưu</span>
                <span class="rounded-full px-2 py-0.5 text-xs font-bold {{ $isStarred ? 'bg-white/25 text-white' : 'bg-amber-200 text-amber-950' }}">
                    {{ $starredCount }}
                </span>
            </a>

            <div class="hidden sm:block h-5 w-px bg-slate-200 mx-1"></div>

            {{-- HSK Level Pills --}}
            @foreach($availableHskLevels as $level)
            <a href="{{ route('flashcards', array_filter(['standard' => $standardCode, 'hsk' => $level, 'q' => $search])) }}"
                class="rounded-full px-3.5 py-2 text-sm font-semibold transition shadow-xs
                      {{ ($hskLevel == $level && ! $lessonSlug && ! $isStarred) ? (($standardCode === 'hsk_3_0_2026') ? 'bg-[#991b1b] text-white shadow-md' : 'bg-slate-900 text-white shadow-md') : 'bg-white/80 text-slate-700 border border-slate-200 hover:border-slate-400 hover:text-slate-900' }}">
                HSK {{ $level }}
            </a>
            @endforeach

            {{-- Compact Grouped Lesson Selector Dropdown --}}
            <div class="relative inline-flex items-center">
                <select onchange="if(this.value){ window.location.href = this.value; }"
                    class="appearance-none rounded-full border border-slate-200 bg-white/90 py-2 pl-4 pr-9 text-sm font-semibold text-slate-700 shadow-xs outline-none transition hover:border-[#991b1b] focus:border-[#991b1b] focus:ring-1 focus:ring-[#991b1b] cursor-pointer max-w-[240px] sm:max-w-xs truncate">
                    <option value="{{ route('flashcards', array_filter(['standard' => $standardCode, 'hsk' => $hskLevel, 'q' => $search])) }}">
                        📖 Chọn bài học cụ thể...
                    </option>
                    @foreach($lessonsByLevel as $lvl => $lvlLessons)
                        <optgroup label="── {{ $lvl ? 'HSK ' . $lvl : 'Bài học' }} ──">
                            @foreach($lvlLessons as $l)
                                <option value="{{ route('flashcards', array_filter(['standard' => $standardCode, 'lesson' => $l->slug, 'q' => $search])) }}"
                                        {{ $lessonSlug === $l->slug ? 'selected' : '' }}>
                                    {{ $l->title }} ({{ $l->flashcards_count }} từ)
                                </option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
                <i data-lucide="chevron-down" class="pointer-events-none absolute right-3 top-2.5 h-4 w-4 text-slate-400"></i>
            </div>

            {{-- Quick Access: Sổ Từ Vựng Toàn Cảnh --}}
            <button type="button" 
                    onclick="window.openVocabModal?.()"
                    class="inline-flex items-center gap-1.5 rounded-full border border-amber-900/20 bg-amber-50/80 px-3.5 py-2 text-sm font-bold text-amber-950 shadow-xs hover:bg-amber-100 hover:border-amber-900/40 transition active:scale-95 cursor-pointer"
                    title="Mở sổ tra cứu toàn cảnh">
                <i data-lucide="book-marked" class="h-4 w-4 text-[#991b1b]"></i>
                <span class="chinese-seal text-[9px] px-1 py-0.2">词</span>
                <span>Sổ từ vựng</span>
                <span class="rounded-full bg-[#991b1b] text-white px-1.5 py-0.2 text-[10px] font-black">{{ $deckTotal }}</span>
            </button>
        </div>

        {{-- Search Form --}}
        <form action="{{ route('flashcards') }}" method="GET" class="relative w-full sm:w-72 shrink-0">
            @if($lessonSlug) <input type="hidden" name="lesson" value="{{ $lessonSlug }}"> @endif
            @if($hskLevel && ! $lessonSlug) <input type="hidden" name="hsk" value="{{ $hskLevel }}"> @endif
            @if($isStarred) <input type="hidden" name="starred" value="1"> @endif
            <input type="text" name="q" value="{{ $search ?? '' }}" placeholder="Tìm chữ Hán, Pinyin, Nghĩa..." 
                   class="w-full rounded-full border border-slate-200 bg-white/90 py-2 pl-10 pr-9 text-sm text-slate-800 shadow-xs outline-none transition focus:border-[#991b1b] focus:ring-1 focus:ring-[#991b1b]">
            <i data-lucide="search" class="absolute left-3.5 top-2.5 h-4 w-4 text-slate-400"></i>
            @if($search)
                <a href="{{ route('flashcards', array_filter(['lesson' => $lessonSlug, 'hsk' => $hskLevel, 'starred' => $isStarred ? 1 : null])) }}"
                   class="absolute right-3 top-2.5 text-slate-400 hover:text-slate-600 transition"
                   title="Xóa tìm kiếm">
                    <i data-lucide="x" class="h-4 w-4"></i>
                </a>
            @endif
        </form>
    </div>

    {{-- Active Filter Tags Row (Shown only when filtering by lesson or searching) --}}
    @if($activeLesson || $search)
    <div class="flex flex-wrap items-center gap-2 pt-1 text-xs">
        <span class="font-medium text-slate-500">Đang lọc theo:</span>
        
        @if($activeLesson)
        <span class="inline-flex items-center gap-1.5 rounded-full bg-red-50 border border-red-200 px-3 py-1 font-medium text-[#991b1b]">
            <i data-lucide="book-open" class="h-3.5 w-3.5"></i>
            <span>Bài học: {{ $activeLesson->title }}</span>
            <a href="{{ route('flashcards', array_filter(['hsk' => $activeLesson->hsk_level, 'q' => $search])) }}" 
               class="ml-0.5 rounded-full p-0.5 hover:bg-red-200/60 transition text-red-600"
               title="Bỏ lọc bài học">
                <i data-lucide="x" class="h-3 w-3"></i>
            </a>
        </span>
        @endif

        @if($search)
        <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 border border-slate-200 px-3 py-1 font-medium text-slate-700">
            <i data-lucide="search" class="h-3.5 w-3.5 text-slate-500"></i>
            <span>Từ khóa: "{{ $search }}"</span>
            <a href="{{ route('flashcards', array_filter(['lesson' => $lessonSlug, 'hsk' => $hskLevel, 'starred' => $isStarred ? 1 : null])) }}" 
               class="ml-0.5 rounded-full p-0.5 hover:bg-slate-200 transition text-slate-600"
               title="Bỏ tìm kiếm">
                <i data-lucide="x" class="h-3 w-3"></i>
            </a>
        </span>
        @endif

        <a href="{{ route('flashcards') }}" class="text-slate-400 hover:text-[#991b1b] underline font-medium ml-1 transition">
            Xóa tất cả bộ lọc
        </a>
    </div>
    @endif
</div>

@if($flashcards->isEmpty())
<div class="flex flex-col items-center justify-center rounded-[2.5rem] border border-dashed border-slate-300 bg-white/60 py-16 px-6 text-center shadow-sm backdrop-blur">
    @if($isStarred)
        <div class="grid h-16 w-16 place-items-center rounded-3xl bg-amber-50 text-amber-500">
            <i data-lucide="star-off" class="h-8 w-8"></i>
        </div>
        <h3 class="mt-4 text-xl font-bold text-slate-800">Sổ tay từ vựng đang trống</h3>
        <p class="mt-2 max-w-md text-sm text-slate-500">
            Bạn chưa lưu từ vựng nào vào Sổ tay. Hãy bấm biểu tượng ⭐ trên bất kỳ thẻ flashcard nào để lưu lại các từ khó nhớ và ôn tập riêng nhé!
        </p>
        <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
            <a href="{{ route('flashcards') }}" class="inline-flex items-center gap-2 rounded-full bg-[#991b1b] px-5 py-2.5 text-sm font-semibold text-white shadow-md transition hover:bg-red-800">
                <i data-lucide="layers" class="h-4 w-4"></i>
                Khám phá toàn bộ từ vựng
            </a>
        </div>
    @else
        <div class="grid h-16 w-16 place-items-center rounded-3xl bg-red-50 text-[#991b1b]">
            <i data-lucide="search-x" class="h-8 w-8"></i>
        </div>
        <h3 class="mt-4 text-xl font-bold text-slate-800">Không tìm thấy flashcard phù hợp</h3>
        <p class="mt-2 max-w-md text-sm text-slate-500">
            @if($search)
                Không có thẻ nào khớp với từ khóa "<span class="font-semibold text-slate-800">{{ $search }}</span>". Hãy thử tìm từ khác hoặc bỏ bộ lọc.
            @else
                Chưa có thẻ từ vựng nào trong mục này. Vui lòng chọn bài học hoặc cấp độ HSK khác.
            @endif
        </p>
        <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
            @if($search || $lessonSlug || $hskLevel)
                <a href="{{ route('flashcards') }}" class="inline-flex items-center gap-2 rounded-full bg-[#991b1b] px-5 py-2.5 text-sm font-semibold text-white shadow-md transition hover:bg-red-800">
                    <i data-lucide="rotate-ccw" class="h-4 w-4"></i>
                    Xem tất cả thẻ
                </a>
            @endif
            <a href="{{ route('hsk.overview') }}" class="inline-flex items-center gap-2 rounded-full border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
                <i data-lucide="graduation-cap" class="h-4 w-4"></i>
                Xem lộ trình HSK
            </a>
        </div>
    @endif
</div>
@else

@php
    $petStageObj = $userPet?->pet?->stages?->where('stage', $userPet->stage)?->first();
    $petDefaultEmoji = $petStageObj?->emoji ?? '🐉';
    $petDefaultName = $userPet?->name ?? 'Tiểu Long';
@endphp

{{-- Interactive Flashcard Deck with Alpine.js (batch loading) --}}
<div id="flashcard-deck-player"
     @select-card.window="selectCard($event.detail)"
     x-data="{
    ready: false,
    cards: {{ Js::from($deckBatch) }},
    total: {{ $deckTotal }},
    offset: {{ $deckBatch->count() }},
    lessonSlug: '{{ $lessonSlug ?? '' }}',
    hskLevel: '{{ $hskLevel ?? '' }}',
    standardCode: '{{ $standardCode ?? 'hsk_2_0' }}',
    searchQuery: '{{ $search ?? '' }}',
    isStarredFilter: {{ $isStarred ? 'true' : 'false' }},
    starredCount: {{ $starredCount }},
    current: 0,
    flipped: false,
    done: [],
    loading: false,
    justSelected: false,
    sessionLogged: false,
    sessionStartTime: Date.now(),
    streakCount: 0,
    maxComboStreak: 0,
    petEmoji: '{{ $petDefaultEmoji }}',
    petSpeech: 'Cùng tớ lật thẻ ôn từ vựng nhé! Nhớ kỹ nghĩa và câu ví dụ nha 🎴✨',
    comboNotice: '',
    showComboCelebration: false,
    petReward: null,
    floatingXp: null,
    _floatingTimer: null,

    triggerComboNotice(text) {
        this.comboNotice = text;
        this.showComboCelebration = true;
        setTimeout(() => { this.showComboCelebration = false; }, 2400);
    },

    showFloatingXp(xp) {
        this.floatingXp = xp;
        if (this._floatingTimer) clearTimeout(this._floatingTimer);
        this._floatingTimer = setTimeout(() => { this.floatingXp = null; }, 1800);
        setTimeout(() => window.refreshIcons?.(), 30);
    },

    get card() { return this.cards[this.current] ?? null; },
    get progress() {
        return this.cards.length
            ? Math.round(((this.current + 1) / this.cards.length) * 100)
            : 0;
    },
    get hasMore() { return this.offset < this.total; },

    selectCard(cardData) {
        if (!cardData || !cardData.id) return;

        let index = this.cards.findIndex(c => c.id === cardData.id);
        if (index === -1) {
            this.cards.push(cardData);
            index = this.cards.length - 1;
            this.total = Math.max(this.total, this.cards.length);
        }

        this.current = index;
        this.flipped = false;
        this.done = this.done.filter(i => i !== index);
        this.justSelected = true;
        setTimeout(() => { this.justSelected = false; }, 2000);

        const playerEl = document.getElementById('flashcard-deck-player');
        if (playerEl) {
            const topPos = playerEl.getBoundingClientRect().top + window.pageYOffset - 90;
            window.scrollTo({ top: Math.max(0, topPos), behavior: 'smooth' });
        }
        setTimeout(() => window.refreshIcons?.(), 60);
    },

    flip() {
        this.flipped = !this.flipped;
        window.soundEngine?.play('whoosh');
        if (this.flipped) {
            if (this.streakCount >= 3) {
                this.petSpeech = 'Nghĩ ra chưa? Cùng xem đáp án nào, chuỗi x' + this.streakCount + ' đang rực cháy đấy! 🔥';
            } else {
                this.petSpeech = 'Xem đáp án và phát âm chuẩn nhé! Có ví dụ câu hay lắm nè 📖';
            }
        }
    },

    async next() {
        if (!this.done.includes(this.current)) this.done.push(this.current);
        if (this.current < this.cards.length - 1) {
            this.flipped = false;
            setTimeout(() => { this.current++; setTimeout(() => window.refreshIcons?.(), 50); }, 150);
            // Pre-fetch next batch when 3 cards from end
            if (this.current >= this.cards.length - 4 && this.hasMore && !this.loading) {
                await this.loadMore();
            }
        } else if (this.hasMore) {
            // At end but still have more — load and continue
            this.flipped = false;
            await this.loadMore();
            setTimeout(() => { this.current++; setTimeout(() => window.refreshIcons?.(), 50); }, 150);
        } else {
            // Completed deck
            window.soundEngine?.play('fanfare');
            this.logSession();
            this.$nextTick(() => window.refreshIcons?.());
        }
    },

    prev() {
        if (this.current > 0) {
            this.flipped = false;
            setTimeout(() => { this.current--; setTimeout(() => window.refreshIcons?.(), 50); }, 150);
        }
    },

    async toggleStar(cardId) {
        const target = this.cards.find(c => c.id === cardId);
        if (!target) return;
        const prevState = target.is_starred;
        target.is_starred = !prevState;
        if (target.is_starred) this.starredCount++;
        else if (this.starredCount > 0) this.starredCount--;

        try {
            const res = await fetch('{{ route('flashcards.toggleStar') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ flashcard_id: cardId })
            });
            if (!res.ok) {
                target.is_starred = prevState;
                if (res.status === 401) {
                    window.dialog.authRequired({
                        word: target?.hanzi || '',
                        pinyin: target?.pinyin || '',
                        meaning: target?.meaning || '',
                    });
                }
            } else {
                const data = await res.json();
                target.is_starred = data.is_starred;
                this.starredCount = data.starred_count;
                if (data.is_starred) {
                    this.petSpeech = `Đã cất từ 「${target?.hanzi || ''}」vào Sổ tay riêng rồi nhé, để dành ôn lại siêu tiện! ⭐`;
                    window.toast.success('Đã lưu vào Sổ tay từ vựng!', target?.hanzi || '');
                } else {
                    this.petSpeech = `Đã bỏ lưu từ 「${target?.hanzi || ''}」khỏi Sổ tay.`;
                    window.toast.info('Đã bỏ lưu khỏi Sổ tay từ vựng');
                }
            }
        } catch(e) {
            target.is_starred = prevState;
        }
        setTimeout(() => window.refreshIcons?.(), 50);
    },

    async submitReview(quality) {
        if (!this.card) return;

        // Synchronously capture active card state before any async calls or step transitions
        const currentCard = this.card;
        const currentCardId = currentCard.id;
        const currentHanzi = currentCard.hanzi || currentCard.word || '';
        const currentPinyin = currentCard.pinyin || '';
        const currentMeaning = currentCard.meaning || '';

        const isKnown = (quality === 'known' || quality >= 4);
        if (isKnown) {
            this.streakCount = (this.streakCount || 0) + 1;
            if (this.streakCount > this.maxComboStreak) {
                this.maxComboStreak = this.streakCount;
            }

            if (this.streakCount >= 5) {
                this.petSpeech = `⚡ SIÊU PHÀM! Chuỗi x${this.streakCount}! Trí nhớ đỉnh nóc kịch trần luôn! 🔥`;
                this.triggerComboNotice(`🔥 THẦN TỐC x${this.streakCount}! BÙNG NỔ!`);
                window.soundEngine?.play('milestone');
            } else if (this.streakCount >= 3) {
                this.petSpeech = `🔥 Xuất sắc! Chuỗi x${this.streakCount} rồi, tiến bộ nhanh thật đấy!`;
                this.triggerComboNotice(`🔥 COMBO x${this.streakCount}!`);
                window.soundEngine?.play('milestone');
            } else {
                this.petSpeech = `Đỉnh quá! Đã thuộc từ 「${currentHanzi}」rồi nè! 🎉`;
                window.soundEngine?.play('ding');
            }
        } else {
            this.streakCount = 0;
            this.petSpeech = `Không sao cả! Từ 「${currentHanzi}」này hơi nhiều nét, lát nữa mình ôn lại là nhớ ngay! 🌸`;
            window.soundEngine?.play('tap');
            window.PetEventBus?.emit('pet:struggle', { word: currentHanzi });
        }

        try {
            await fetch('{{ route('flashcards.review') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    flashcard_id: currentCardId,
                    quality: quality
                })
            });
        } catch(e) { console.error('submitReview error:', e); }

        @auth
        try {
            const idempotencyKey = `fc_${currentCardId}_${quality}_${Math.floor(Date.now() / 60000)}`;
            const res = await fetch('{{ route('student.activity.log') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    activity_type: isKnown ? 'flashcard_known' : 'flashcard_review',
                    source_type: 'flashcard',
                    source_id: currentCardId,
                    idempotency_key: idempotencyKey,
                    meta: { quality: quality, streak_count: this.streakCount }
                })
            });
            const data = await res.json();
            if (data?.xp?.earned > 0) {
                this.showFloatingXp(data.xp.earned);
            }
            if (data?.daily_goal?.just_completed) {
                window.PetEventBus?.emit('pet:daily-goal-completed', { xp: data?.xp?.earned });
            }
            if (isKnown) {
                window.PetEventBus?.emit('pet:word-mastered', {
                    word: currentHanzi,
                    hanzi: currentHanzi,
                    pinyin: currentPinyin,
                    meaning: currentMeaning,
                    xp: data?.xp?.earned
                });
            }
        } catch(e) {}
        @endauth

        this.next();
    },

    speak(text) {
        if (text) {
            window.playChineseVoice(text);
        }
    },

    async loadMore() {
        if (this.loading || !this.hasMore) return;
        this.loading = true;
        try {
            const params = new URLSearchParams({ offset: this.offset });
            if (this.lessonSlug) params.append('lesson', this.lessonSlug);
            if (this.hskLevel) params.append('hsk', this.hskLevel);
            if (this.standardCode) params.append('standard', this.standardCode);
            if (this.searchQuery) params.append('q', this.searchQuery);
            if (this.isStarredFilter) params.append('starred', '1');
            const res = await fetch(`{{ route('flashcards.cards') }}?${params}`);
            const data = await res.json();
            this.cards.push(...data.cards);
            this.offset += data.cards.length;
            setTimeout(() => window.refreshIcons?.(), 50);
        } catch(e) { console.error(e); }
        this.loading = false;
    },

    restart() {
        this.current = 0;
        this.flipped = false;
        this.done = [];
        this.sessionLogged = false;
        this.streakCount = 0;
        this.maxComboStreak = 0;
        this.petReward = null;
        this.petSpeech = 'Tuyệt vời! Chúng mình cùng ôn luyện thêm một lần nữa nhé! 💪';
        this.sessionStartTime = Date.now();
        setTimeout(() => window.refreshIcons?.(), 50);
    },

    async logSession() {
        if (this.sessionLogged) return;
        this.sessionLogged = true;
        const minutes = Math.max(1, Math.round((Date.now() - this.sessionStartTime) / 60000));
        try {
            const res = await fetch('{{ route('flashcards.session') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                },
                body: JSON.stringify({
                    duration_minutes: minutes,
                    cards_reviewed: this.done.length,
                    lesson_id: this.card?.lesson_id ?? null,
                }),
            });
            const data = await res.json();
            if (data?.pet_reward) {
                this.petReward = data.pet_reward;
                this.petSpeech = `Oa! Cậu đã hoàn thành ${this.cards.length} thẻ rồi! Tớ được thưởng +${data.pet_reward.exp_bonus} EXP và ăn no nê! 💖`;
            }
        } catch(e) {}
    },
}" x-init="ready = true; window.__deckPlayer = this; setTimeout(() => window.refreshIcons?.(), 50);" class="space-y-8">

    {{-- Skeleton loader while initializing --}}
    <div x-show="!ready" class="flex flex-col items-center gap-6">
        <div class="h-2 w-full rounded-full bg-slate-100 skeleton-shimmer"></div>
        <div class="flex justify-center gap-1.5">
            <div class="h-2 w-2 rounded-full bg-slate-200 animate-pulse"></div>
            <div class="h-2 w-2 rounded-full bg-slate-200 animate-pulse"></div>
            <div class="h-2 w-2 rounded-full bg-slate-200 animate-pulse"></div>
        </div>
        <div class="w-full max-w-lg rounded-[2rem] bg-slate-900/90 p-8 shadow-2xl h-[320px] flex flex-col items-center justify-center gap-4 skeleton-shimmer">
            <div class="h-5 w-20 rounded-full bg-white/20"></div>
            <div class="h-20 w-28 rounded-2xl bg-white/20"></div>
            <div class="h-4 w-36 rounded-full bg-white/20"></div>
        </div>
        <div class="flex items-center gap-3">
            <div class="h-10 w-24 rounded-full bg-slate-200 skeleton-shimmer"></div>
            <div class="h-10 w-32 rounded-full bg-slate-300 skeleton-shimmer"></div>
            <div class="h-10 w-24 rounded-full bg-slate-200 skeleton-shimmer"></div>
        </div>
    </div>

    {{-- Real Interactive Deck (shown when ready) --}}
    <div x-show="ready" x-cloak class="space-y-8 relative">
        {{-- Floating XP Notification --}}
        <div x-show="floatingXp" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 -translate-y-2 scale-90"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0 -translate-y-4"
             class="pointer-events-none fixed top-24 right-8 z-50 flex items-center gap-1.5 rounded-full bg-amber-400 px-3.5 py-1.5 text-xs font-black text-slate-950 shadow-lg shadow-amber-400/30">
            <i data-lucide="sparkles" class="h-3.5 w-3.5 fill-current"></i>
            <span x-text="'+' + floatingXp + ' XP'"></span>
        </div>

        {{-- Pet Co-op Companion & Live Streak / Speech Bubble --}}
        <div class="relative overflow-hidden rounded-[2rem] border border-amber-200/90 bg-gradient-to-r from-amber-50 via-orange-50/50 to-red-50 p-4 sm:p-5 shadow-lg shadow-amber-950/5">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                {{-- Left: Pet Avatar & Dynamic Speech --}}
                <div class="flex items-center gap-3.5">
                    <div class="relative grid h-14 w-14 shrink-0 place-items-center rounded-2xl bg-white shadow-md border border-amber-200/80 transition-transform duration-300 hover:scale-105">
                        <span class="text-3xl select-none" x-text="petEmoji || '{{ $petDefaultEmoji }}'"></span>
                        <span class="absolute -top-1 -right-1 flex h-4 w-4">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-4 w-4 bg-amber-500 text-[9px] font-black text-white items-center justify-center">★</span>
                        </span>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-black uppercase tracking-wider text-amber-800">Bạn học {{ $petDefaultName }}</span>
                            <span class="rounded-full bg-amber-200/80 px-2 py-0.2 text-[10px] font-bold text-amber-900">
                                Cấp {{ $userPet?->stage ?? 1 }}: {{ $petStageObj?->name ?? 'Bạn đồng hành' }}
                            </span>
                        </div>
                        <p class="text-sm font-semibold text-slate-800 mt-0.5 leading-snug" x-text="petSpeech">
                            "Cùng tớ lật thẻ ôn từ vựng nhé! Nhớ kỹ nghĩa và câu ví dụ nha 🎴✨"
                        </p>
                    </div>
                </div>

                {{-- Right: Live Streak / Combo Status --}}
                <div class="flex items-center gap-3 self-end sm:self-center">
                    {{-- Dynamic Combo Streak Pill --}}
                    <div x-show="streakCount >= 2"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 scale-90"
                         x-transition:enter-end="opacity-100 scale-100"
                         class="flex items-center gap-2 rounded-2xl bg-gradient-to-r from-red-600 to-amber-500 px-3.5 py-2 text-white shadow-md shadow-red-600/20">
                        <i data-lucide="flame" class="h-4 w-4 text-yellow-200 animate-bounce"></i>
                        <div>
                            <p class="text-[9px] font-bold uppercase tracking-widest text-yellow-100 leading-none">Chuỗi nhớ</p>
                            <p class="text-sm font-black leading-tight">
                                <span x-text="'x' + streakCount"></span>
                                <span x-show="streakCount >= 5" class="text-[10px] text-yellow-200">🔥 Siêu nhớ!</span>
                            </p>
                        </div>
                    </div>

                    <div class="rounded-xl bg-white/80 border border-amber-200/80 px-3 py-1.5 text-xs text-amber-900 font-medium hidden sm:block">
                        <span>Thưởng hoàn thành: <strong class="font-bold text-amber-800">+10~20 EXP cho Pet</strong> 🐉</span>
                    </div>
                </div>
            </div>

            {{-- Big Explosive Combo Celebration Banner --}}
            <div x-show="showComboCelebration"
                 x-transition:enter="transition cubic-bezier(0.34, 1.56, 0.64, 1) duration-500"
                 x-transition:enter-start="opacity-0 scale-50 translate-y-4"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-300"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="mt-3 flex items-center justify-between rounded-2xl bg-gradient-to-r from-amber-500 via-red-600 to-purple-600 p-3 text-white shadow-xl animate-pulse">
                <div class="flex items-center gap-2.5">
                    <span class="text-2xl animate-spin">🔥</span>
                    <div>
                        <p class="text-xs font-black uppercase tracking-wider text-yellow-200" x-text="comboNotice"></p>
                        <p class="text-xs text-white/90">Bạn ghi nhớ liên tiếp cực chuẩn xác, Pet rất tự hào về bạn!</p>
                    </div>
                </div>
                <span class="rounded-full bg-white/20 px-3 py-1 text-xs font-black text-white">
                    +Thưởng Pet EXP!
                </span>
            </div>
        </div>

        {{-- Progress bar --}}
        <div class="flex items-center gap-4">
            <div class="flex-1 h-2 rounded-full bg-slate-100 overflow-hidden">
                <div class="h-2 rounded-full bg-gradient-to-r from-[#991b1b] to-amber-400 transition-all duration-500"
                    :style="`width: ${progress}%`"></div>
            </div>
            <div class="flex items-center gap-2">
                <template x-if="justSelected">
                    <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 text-amber-900 border border-amber-300 px-2.5 py-0.5 text-xs font-bold animate-pulse">
                        <i data-lucide="sparkles" class="h-3 w-3 text-amber-600"></i>
                        <span>Đã mở thẻ: <strong x-text="card?.hanzi"></strong></span>
                    </span>
                </template>
                <span class="shrink-0 text-sm font-semibold text-slate-600">
                    <span x-text="current + 1"></span> / <span x-text="total"></span>
                    <template x-if="loading">
                        <span class="ml-1 inline-block h-3 w-3 animate-spin rounded-full border-2 border-[#991b1b] border-t-transparent"></span>
                    </template>
                </span>
            </div>
        </div>

        {{-- Dot navigation --}}
        <div class="flex flex-wrap justify-center gap-1.5">
            <template x-for="(card, idx) in cards" :key="idx">
                <button @click="current = idx; flipped = false; setTimeout(() => window.refreshIcons?.(), 50);"
                    class="dot"
                    :class="done.includes(idx) ? 'done' : (idx === current ? 'current' : 'future')">
                </button>
            </template>
        </div>

        {{-- Card area --}}
        <template x-if="card && current < cards.length && done.length < cards.length">
            <div class="flex flex-col items-center gap-6">
            {{-- Flip card --}}
            <div class="flip-card w-full max-w-lg cursor-pointer transition-all duration-300 rounded-[2rem]"
                 :class="justSelected ? 'ring-4 ring-amber-400 ring-offset-4 ring-offset-slate-900 shadow-2xl scale-[1.01]' : ''"
                 style="height: 320px;" 
                 @click="flip()">
                <div class="flip-card-inner" :class="{ flipped }">
                    {{-- Front: Hanzi + Lesson label + Star button --}}
                    <div class="flip-card-front relative flex flex-col items-center justify-center gap-3 bg-slate-950 p-8 text-white shadow-2xl shadow-slate-950/20 overflow-hidden chinese-frame-corners">
                        {{-- Subtle Chinese Watermark Motif --}}
                        <span class="chinese-watermark right-6 top-1/2 -translate-y-1/2 text-9xl text-white/5 select-none pointer-events-none">学</span>

                        <div class="flex flex-wrap items-center justify-center gap-1.5 z-10">
                            <span class="chinese-seal px-1.5 py-0.5 text-[10px] font-bold border-red-400/40 text-red-300 bg-red-950/40">【字】</span>
                            <template x-if="card.hsk_level || card.hsk2_level || card.hsk3_level">
                                <span class="rounded-full bg-white/10 px-2.5 py-0.5 text-[11px] font-semibold text-slate-300"
                                      x-text="'HSK ' + (card.hsk_level || card.hsk2_level || card.hsk3_level)"></span>
                            </template>
                            <template x-if="card.lesson">
                                <span class="rounded-full bg-white/10 px-3 py-0.5 text-xs font-semibold uppercase tracking-widest text-slate-300"
                                      x-text="card.lesson"></span>
                            </template>
                        </div>
                        <p class="font-chinese hanzi-ink-glow text-8xl font-black leading-none tracking-tight select-none z-10 transition-transform cursor-pointer" x-text="card.hanzi"></p>
                        <p class="mt-2 text-sm text-slate-400 z-10">Bấm để xem nghĩa 👆</p>

                        {{-- Star / Bookmark Button (Top Right) --}}
                        <button @click.stop="toggleStar(card.id)" 
                                class="absolute top-6 right-6 flex h-11 w-11 items-center justify-center rounded-full transition-all duration-300 hover:scale-110 active:scale-95 shadow-md"
                                :class="card.is_starred ? 'bg-amber-400 text-slate-950 shadow-amber-400/30 ring-2 ring-amber-300' : 'bg-white/10 text-white hover:bg-white/20'"
                                :title="card.is_starred ? 'Bỏ lưu từ này' : 'Lưu vào Sổ từ vựng'">
                            <span :class="{ 'fill-current': card.is_starred }" class="inline-flex">
                                <i data-lucide="star" class="h-5 w-5"></i>
                            </span>
                        </button>

                        {{-- Actions (Write & Speak) --}}
                        <div class="absolute bottom-6 right-6 flex items-center gap-3">
                            <button @click.stop="$dispatch('open-writer', card.hanzi)" class="flex h-12 w-12 items-center justify-center rounded-full bg-white/10 text-white transition hover:bg-white/20 hover:scale-110 active:scale-95" title="Tập viết chữ">
                                <i data-lucide="pen-tool" class="h-5 w-5"></i>
                            </button>
                            <button @click.stop="speak(card.hanzi)" class="flex h-12 w-12 items-center justify-center rounded-full bg-white/10 text-white transition hover:bg-white/20 hover:scale-110 active:scale-95" title="Nghe phát âm">
                                <i data-lucide="volume-2" class="h-5 w-5"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Back: Meaning + Pinyin + Example + Star button --}}
                    <div class="flip-card-back relative flex flex-col justify-between overflow-hidden bg-white shadow-2xl shadow-slate-950/10">
                        <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-[#991b1b] via-amber-400 to-[#111827]"></div>

                        {{-- Star Button on Back Face --}}
                        <button @click.stop="toggleStar(card.id)" 
                                class="absolute top-6 right-6 flex h-10 w-10 items-center justify-center rounded-full transition-all duration-300 hover:scale-110 active:scale-95 shadow-sm"
                                :class="card.is_starred ? 'bg-amber-100 text-amber-600 ring-1 ring-amber-300' : 'bg-slate-100 text-slate-400 hover:text-amber-500 hover:bg-amber-50'"
                                :title="card.is_starred ? 'Bỏ lưu từ này' : 'Lưu vào Sổ từ vựng'">
                            <span :class="{ 'fill-current': card.is_starred }" class="inline-flex">
                                <i data-lucide="star" class="h-5 w-5"></i>
                            </span>
                        </button>

                        <div class="flex flex-1 flex-col justify-center gap-4 p-8 pr-16">
                            <div>
                                <div class="flex flex-wrap items-center gap-1.5 mb-2">
                                    <template x-if="card.hsk2_level">
                                        <span class="rounded-md bg-slate-100 border border-slate-200 px-2 py-0.5 text-[11px] font-bold text-slate-700"
                                              x-text="'HSK 2.0: Cấp ' + card.hsk2_level"></span>
                                    </template>
                                    <template x-if="card.hsk3_level">
                                        <span class="rounded-md bg-red-50 border border-red-200 px-2 py-0.5 text-[11px] font-bold text-[#991b1b]"
                                              x-text="'HSK 3.0: Cấp ' + card.hsk3_level"></span>
                                    </template>
                                    <template x-if="!card.hsk2_level && !card.hsk3_level && card.hsk_level">
                                        <span class="rounded-md bg-red-50 border border-red-200 px-2 py-0.5 text-[11px] font-bold text-[#991b1b]"
                                              x-text="'HSK ' + card.hsk_level"></span>
                                    </template>
                                </div>
                                <p class="text-sm font-semibold tracking-wider" x-html="window.formatTonePinyin ? window.formatTonePinyin(card.pinyin) : card.pinyin"></p>
                                <p class="mt-2 text-4xl font-black tracking-tight text-slate-950" x-text="card.meaning"></p>
                            </div>
                            <template x-if="card.example">
                                <div class="rounded-2xl bg-slate-50 border border-slate-200 p-4">
                                    <p class="text-sm text-slate-500">Ví dụ</p>
                                    <p class="mt-1 text-base font-semibold text-slate-900" x-text="card.example"></p>
                                    <p class="mt-0.5 text-xs text-slate-500" x-html="window.formatTonePinyin ? window.formatTonePinyin(card.example_pinyin) : card.example_pinyin"></p>
                                    <p class="mt-0.5 text-xs text-slate-600 italic" x-text="card.example_meaning"></p>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Navigation buttons --}}
            <div class="flex flex-wrap items-center justify-center gap-3">

                {{-- Previous --}}
                <button
                    @click="prev()"
                    :disabled="current === 0"
                    class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:-translate-y-0.5 hover:border-slate-300 disabled:cursor-not-allowed disabled:opacity-40">
                    <i data-lucide="arrow-left" class="h-4 w-4"></i>
                    <span class="hidden sm:inline">Trước</span>
                </button>

                {{-- Flip card & Quick Catalog --}}
                <div x-show="!flipped" x-cloak class="flex items-center gap-2">
                    <button
                        @click="flip()"
                        class="inline-flex items-center gap-2 rounded-full bg-slate-900 px-8 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-slate-800 active:scale-95">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                        <span>Xem đáp án</span>
                    </button>
                    <button
                        type="button"
                        onclick="window.openVocabModal?.()"
                        class="inline-flex items-center gap-1.5 rounded-full border border-amber-900/20 bg-amber-50 px-4 py-2.5 text-sm font-bold text-amber-950 shadow-sm transition hover:bg-amber-100 active:scale-95 cursor-pointer"
                        title="Mở toàn bộ danh mục từ vựng trong sổ tra cứu">
                        <i data-lucide="book-marked" class="h-4 w-4 text-[#991b1b]"></i>
                        <span class="hidden md:inline">Sổ từ vựng</span>
                        <span class="rounded-full bg-[#991b1b] text-white px-1.5 py-0.2 text-[10px] font-black">{{ $deckTotal }}</span>
                    </button>
                </div>

                {{-- Review options when flipped --}}
                <div x-show="flipped" x-cloak class="flex items-center gap-3">
                    <button
                        @click="submitReview('review')"
                        class="inline-flex items-center gap-2 rounded-full border border-orange-200 bg-orange-50 px-5 py-2.5 text-sm font-bold text-orange-600 shadow-sm transition hover:bg-orange-100 active:scale-95">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/>
                            <path d="M3 3v5h5"/>
                        </svg>
                        <span>Cần ôn lại</span>
                    </button>
                    <button
                        @click="submitReview('known')"
                        class="inline-flex items-center gap-2 rounded-full bg-emerald-500 px-6 py-2.5 text-sm font-bold text-white shadow-md shadow-emerald-500/20 transition hover:-translate-y-0.5 hover:bg-emerald-600 active:scale-95">
                        <span>Đã thuộc</span>
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 6 9 17l-5-5"/>
                        </svg>
                    </button>
                </div>

                {{-- Next / Complete --}}
                <button
                    @click="next()"
                    class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:-translate-y-0.5 hover:border-slate-300 disabled:cursor-not-allowed disabled:opacity-40">
                    <span class="hidden sm:inline" x-text="current === cards.length - 1 ? 'Xong' : 'Tiếp'"></span>
                    <span x-show="current < cards.length - 1" class="inline-flex">
                        <i data-lucide="arrow-right" class="h-4 w-4"></i>
                    </span>
                    <span x-show="current === cards.length - 1" class="inline-flex" style="display: none;">
                        <i data-lucide="circle-check" class="h-4 w-4"></i>
                    </span>
                </button>

            </div>
        </div>
    </template>

    {{-- Completion screen --}}
    <template x-if="done.length >= cards.length">
        <div class="flex flex-col items-center gap-6 rounded-[2.5rem] bg-slate-950 py-12 px-6 text-center text-white shadow-2xl shadow-slate-950/20 border border-white/10 relative overflow-hidden">
            <div class="absolute -right-16 -top-16 h-48 w-48 rounded-full bg-amber-500/10 blur-3xl pointer-events-none"></div>
            <div class="absolute -left-16 -bottom-16 h-48 w-48 rounded-full bg-red-600/10 blur-3xl pointer-events-none"></div>

            <div class="grid h-16 w-16 place-items-center rounded-3xl bg-amber-400/20 text-amber-300">
                <i data-lucide="party-popper" class="h-8 w-8"></i>
            </div>
            <div>
                <p class="text-sm uppercase tracking-[0.28em] text-amber-300/80">Hoàn thành xuất sắc!</p>
                <h2 class="mt-2 text-3xl font-black">Bạn đã ôn hết <span x-text="cards.length"></span> thẻ từ vựng!</h2>
                <p class="mt-2 text-slate-400 text-sm">Phiên học đã được ghi nhận tự động vào tiến trình học tập của bạn.</p>
            </div>

            {{-- Post-Session Pet Reward & Victory Card --}}
            <div class="w-full max-w-xl rounded-3xl bg-white/10 border border-white/15 p-5 text-left text-white shadow-xl">
                <div class="flex flex-col sm:flex-row items-center gap-4">
                    <div class="grid h-16 w-16 shrink-0 place-items-center rounded-2xl bg-white/10 border border-white/10 text-4xl shadow-md animate-bounce">
                        <span x-text="petReward?.emoji || '{{ $petDefaultEmoji }}'"></span>
                    </div>
                    <div class="flex-1 text-center sm:text-left">
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-400/20 px-2.5 py-0.5 text-xs font-bold text-amber-300">
                            <i data-lucide="sparkles" class="h-3 w-3"></i>
                            <span>Phần thưởng bạn đồng hành</span>
                        </span>
                        <h4 class="text-lg font-black mt-1">
                            <span x-text="petReward?.pet_name || '{{ $petDefaultName }}'"></span> rất vui vì được học cùng bạn!
                        </h4>
                        <p class="text-xs text-slate-300 mt-0.5 leading-relaxed" x-text="petSpeech"></p>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-2.5 mt-4">
                    <div class="rounded-xl border border-white/10 bg-white/5 p-2.5 text-center">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">EXP cho Pet</p>
                        <p class="text-base font-black text-amber-300 mt-0.5" x-text="'+' + (petReward?.exp_bonus || 15) + ' EXP'"></p>
                    </div>
                    <div class="rounded-xl border border-white/10 bg-white/5 p-2.5 text-center">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Độ no tăng</p>
                        <p class="text-base font-black text-emerald-400 mt-0.5" x-text="'+' + (petReward?.hunger_gain || 8) + ' No'"></p>
                    </div>
                    <div class="rounded-xl border border-white/10 bg-white/5 p-2.5 text-center">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Vật phẩm</p>
                        <p class="text-xs font-black text-rose-300 mt-1 truncate" x-text="petReward?.gift_item || '🥟 Bánh bao thịt'"></p>
                    </div>
                </div>

                <template x-if="petReward?.stage_up">
                    <div class="mt-3 rounded-xl bg-amber-500/20 border border-amber-400/40 p-2.5 text-center text-amber-200 text-xs font-black animate-pulse">
                        🎉 CHÚC MỪNG! Pet của bạn đã đủ điều kiện TIẾN HÓA lên cấp mới!
                    </div>
                </template>
            </div>

            <div class="flex flex-wrap justify-center gap-3 mt-2">
                <button @click="restart()"
                    class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-6 py-3 text-sm font-bold text-white transition hover:bg-white/20 hover:scale-105 active:scale-95">
                    <i data-lucide="rotate-ccw" class="h-4 w-4"></i>
                    <span>Học lại từ đầu</span>
                </button>
                <a href="{{ route('quiz', $lessonSlug ? ['lesson' => $lessonSlug] : []) }}"
                    class="inline-flex items-center gap-2 rounded-full bg-amber-300 px-6 py-3 text-sm font-bold text-slate-950 transition hover:bg-amber-200 hover:scale-105 active:scale-95">
                    <i data-lucide="target" class="h-4 w-4"></i>
                    <span>Làm Quiz kiểm tra →</span>
                </a>
                <a href="{{ route('pet.index') }}"
                    class="inline-flex items-center gap-2 rounded-full border border-amber-400/40 bg-amber-400/10 px-6 py-3 text-sm font-bold text-amber-300 transition hover:bg-amber-400/20 hover:scale-105 active:scale-95">
                    <i data-lucide="heart" class="h-4 w-4"></i>
                    <span>Vào phòng thăm Pet →</span>
                </a>
            </div>
        </div>
    </template>
    </div>
</div>

{{-- Vocabulary Catalog Modal Trigger Section --}}
<section x-data class="mt-12 flex flex-col items-center justify-center text-center">
    <div class="relative overflow-hidden rounded-[2.5rem] border border-amber-900/15 bg-white/90 p-8 shadow-sm backdrop-blur max-w-2xl w-full chinese-frame-corners">
        {{-- Decorative traditional seal & title --}}
        <div class="flex items-center justify-center gap-2">
            <span class="chinese-seal px-2 py-0.5 text-xs font-bold">【词汇全表】</span>
            <span class="text-xs font-bold uppercase tracking-[0.2em] text-[#991b1b]">Tra cứu toàn cảnh</span>
        </div>
        <h3 class="mt-3 text-2xl sm:text-3xl font-black text-slate-900">
            Sổ toàn bộ {{ $deckTotal }} từ vựng trong bộ này
        </h3>
        <p class="mt-2 text-sm text-slate-600 max-w-md mx-auto leading-relaxed">
            Mở sổ tra cứu nhanh để xem danh mục từ, nghe phát âm và chọn đưa bất kỳ từ nào lên bảng học 3D mà không làm tải lại trang.
        </p>
        <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
            <button type="button"
                onclick="window.openVocabModal?.()"
                @click="$dispatch('open-vocab-modal')"
                class="inline-flex items-center gap-2.5 rounded-full bg-[#991b1b] px-6 py-3.5 text-sm font-bold text-white shadow-lg shadow-red-950/20 hover:bg-red-800 transition-all hover:scale-105 active:scale-95 cursor-pointer">
                <i data-lucide="book-marked" class="h-4 w-4"></i>
                <span>Mở Sổ Tra Cứu Toàn Bộ Thẻ</span>
                <span class="rounded-full bg-white/20 px-2.5 py-0.5 text-xs font-black">{{ $deckTotal }}</span>
            </button>
        </div>
    </div>
</section>

{{-- Interactive Vocabulary Deck Modal (词汇总表) --}}
<div x-data="vocabularyModal()"
     x-init="init()"
     @open-vocab-modal.window="open()"
     @keydown.escape.window="close()"
     x-show="isOpen"
     x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-6">

    {{-- Dark Backdrop --}}
    <div x-show="isOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="close()"
         class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm"></div>

    {{-- Modal Panel Container --}}
    <div x-show="isOpen"
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 sm:scale-95"
         class="relative flex flex-col w-full max-w-5xl max-h-[92vh] rounded-[2.5rem] bg-[#fcf8f2] border border-amber-900/15 shadow-2xl overflow-hidden z-10 chinese-frame-corners paper-warm-texture">

        {{-- Header: Chinese Style with Seal Stamp --}}
        <div class="relative shrink-0 border-b border-amber-900/10 bg-white/95 px-6 py-5 sm:px-8">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="chinese-seal px-2 py-0.5 text-xs font-bold">【录】</span>
                        <p class="text-xs font-bold uppercase tracking-[0.2em] text-[#991b1b]">Sổ Từ Vựng Toàn Cầu · 词汇全表</p>
                    </div>
                    <h2 class="mt-1 text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                        Toàn bộ từ vựng (<span x-text="total"></span> từ)
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                        Bấm vào từ bất kỳ để đưa lên bàn học 3D, nghe phát âm chuẩn hoặc lưu vào Sổ tay.
                    </p>
                </div>

                {{-- Close Button --}}
                <button @click="close()" aria-label="Đóng sổ từ vựng"
                    class="grid h-10 w-10 shrink-0 place-items-center rounded-2xl border border-slate-200 bg-white text-slate-500 hover:bg-red-50 hover:text-red-700 hover:border-red-200 transition active:scale-95">
                    <i data-lucide="x" class="h-5 w-5"></i>
                </button>
            </div>

            {{-- Toolbar: Search + Star filter inside Modal --}}
            <div class="mt-4 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 pt-2">
                <div class="relative flex-1 max-w-md">
                    <input type="text"
                           x-model="searchTerm"
                           @input.debounce.350ms="search($event.target.value)"
                           placeholder="Tra nhanh chữ Hán, pinyin hoặc nghĩa..."
                           class="w-full rounded-2xl border border-slate-200 bg-white py-2.5 pl-10 pr-9 text-sm text-slate-800 shadow-xs outline-none transition focus:border-[#991b1b] focus:ring-1 focus:ring-[#991b1b]">
                    <i data-lucide="search" class="absolute left-3.5 top-3 h-4 w-4 text-slate-400"></i>
                    <template x-if="searchTerm">
                        <button @click="searchTerm = ''; search('')" class="absolute right-3 top-3 text-slate-400 hover:text-slate-600">
                            <i data-lucide="x" class="h-4 w-4"></i>
                        </button>
                    </template>
                </div>

                <div class="flex items-center gap-2">
                    <button type="button"
                            @click="filterStarredOnly = !filterStarredOnly; search(searchTerm)"
                            class="inline-flex items-center gap-1.5 rounded-2xl px-4 py-2 text-xs font-bold transition shadow-xs"
                            :class="filterStarredOnly ? 'bg-amber-500 text-white shadow-md shadow-amber-500/20' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-50'">
                        <i data-lucide="star" class="h-3.5 w-3.5" :class="filterStarredOnly ? 'fill-current text-white' : 'text-amber-500 fill-amber-400'"></i>
                        <span>Chỉ xem từ đã lưu</span>
                    </button>

                    <span class="text-xs text-slate-400 pl-2">
                        Trang <strong class="text-slate-700" x-text="currentPage"></strong> / <span x-text="lastPage"></span>
                    </span>
                </div>
            </div>
        </div>

        {{-- Modal Body: Scrollable Card Grid --}}
        <div id="vocab-modal-scroll" class="flex-1 overflow-y-auto p-5 sm:p-8 custom-scrollbar">
            {{-- Loading State --}}
            <div x-show="loading" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <template x-for="i in 6" :key="i">
                    <div class="h-36 rounded-3xl border border-slate-200/60 bg-white/60 p-5 skeleton-shimmer"></div>
                </template>
            </div>

            {{-- Empty State --}}
            <div x-show="!loading && cards.length === 0" class="flex flex-col items-center justify-center py-16 text-center">
                <span class="text-5xl select-none mb-3">🔍</span>
                <h4 class="text-base font-bold text-slate-800">Không tìm thấy từ vựng nào</h4>
                <p class="text-xs text-slate-500 mt-1 max-w-sm">Thử gõ từ khóa khác hoặc tắt bộ lọc để xem toàn bộ danh mục từ vựng nhé.</p>
            </div>

            {{-- Card Grid --}}
            <div x-show="!loading && cards.length > 0" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <template x-for="card in cards" :key="card.id">
                    <article @click="selectAndPlay(card)"
                        class="group relative flex flex-col justify-between overflow-hidden rounded-3xl border border-amber-900/10 bg-white/90 p-5 shadow-xs transition-all duration-200 hover:border-[#991b1b]/60 hover:bg-white hover:shadow-lg hover:-translate-y-0.5 cursor-pointer">
                        
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="truncate text-xs font-semibold tracking-wider text-slate-500"
                                          x-html="window.formatTonePinyin ? window.formatTonePinyin(card.pinyin) : card.pinyin"></span>
                                    <button type="button"
                                        @click.stop="window.playChineseVoice(card.hanzi)"
                                        class="text-slate-300 transition hover:text-[#991b1b] focus:outline-none"
                                        title="Nghe phát âm">
                                        <i data-lucide="volume-2" class="h-3.5 w-3.5"></i>
                                    </button>
                                </div>
                                <div class="mt-1 flex items-baseline gap-2">
                                    <h3 class="font-chinese text-3xl font-black text-slate-900 group-hover:text-[#991b1b] transition-colors" x-text="card.hanzi"></h3>
                                </div>
                                <p class="mt-1 text-sm font-medium leading-relaxed text-slate-600 line-clamp-2" :title="card.meaning" x-text="card.meaning"></p>
                            </div>

                            <div class="flex flex-col items-end gap-2 shrink-0">
                                {{-- Star Toggle inside Modal --}}
                                <button type="button"
                                        @click.stop="toggleStar(card)"
                                        class="flex h-8 w-8 items-center justify-center rounded-full transition hover:scale-110 active:scale-95"
                                        :class="card.is_starred ? 'text-amber-500 bg-amber-50 shadow-sm ring-1 ring-amber-200' : 'text-slate-300 hover:text-amber-400 hover:bg-slate-50'"
                                        :title="card.is_starred ? 'Bỏ lưu' : 'Lưu vào Sổ tay'">
                                    <span :class="{ 'fill-current': card.is_starred }" class="inline-flex">
                                        <i data-lucide="star" class="h-4 w-4"></i>
                                    </span>
                                </button>

                                <template x-if="card.hsk_level || card.hsk2_level || card.hsk3_level">
                                    <span class="rounded-lg bg-red-50 border border-red-200/80 px-2 py-0.5 text-[10px] font-bold text-[#991b1b]"
                                          x-text="'HSK ' + (card.hsk_level || card.hsk2_level || card.hsk3_level)"></span>
                                </template>
                            </div>
                        </div>

                        {{-- Example Sentence (if any) --}}
                        <template x-if="card.example">
                            <div class="mt-3 border-t border-slate-100 pt-2.5 text-xs">
                                <p class="font-medium text-slate-800 truncate" x-text="card.example"></p>
                                <p class="text-slate-500 italic truncate mt-0.5" x-text="card.example_meaning"></p>
                            </div>
                        </template>

                        {{-- Action Footnote --}}
                        <div class="mt-3 flex items-center justify-between border-t border-dashed border-slate-200/80 pt-2.5 text-[11px]">
                            <span class="inline-flex items-center gap-1.5 font-bold text-slate-400 group-hover:text-[#991b1b] transition">
                                <i data-lucide="layers" class="h-3.5 w-3.5"></i>
                                <span>Lật thẻ này trên bảng học</span>
                            </span>
                            <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-600 group-hover:bg-[#991b1b] group-hover:text-white transition">
                                <span>Lên bảng</span>
                                <i data-lucide="arrow-up-right" class="h-3 w-3"></i>
                            </span>
                        </div>
                    </article>
                </template>
            </div>
        </div>

        {{-- Modal Footer: AJAX Pagination without page reload! --}}
        <div class="shrink-0 border-t border-amber-900/10 bg-white/95 px-6 py-4 flex flex-wrap items-center justify-between gap-3">
            <p class="text-xs text-slate-500">
                Hiển thị tối đa <span x-text="perPage"></span> từ/trang · Chuyển trang tức thì không tải lại trang
            </p>

            <div class="flex items-center gap-1.5" x-show="lastPage > 1">
                {{-- Prev Button --}}
                <button type="button"
                    @click="goToPage(currentPage - 1)"
                    :disabled="currentPage <= 1 || loading"
                    class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-700 shadow-xs transition hover:bg-slate-50 disabled:opacity-40 disabled:cursor-not-allowed">
                    <i data-lucide="chevron-left" class="h-4 w-4"></i>
                </button>

                {{-- Page range --}}
                <template x-for="p in Array.from({ length: Math.min(5, lastPage) }, (_, i) => {
                    if (lastPage <= 5) return i + 1;
                    if (currentPage <= 3) return i + 1;
                    if (currentPage >= lastPage - 2) return lastPage - 4 + i;
                    return currentPage - 2 + i;
                })" :key="p">
                    <button type="button"
                        @click="goToPage(p)"
                        class="inline-flex h-9 w-9 items-center justify-center rounded-xl text-xs font-bold transition shadow-xs"
                        :class="currentPage === p ? 'bg-[#991b1b] text-white shadow-md' : 'border border-slate-200 bg-white text-slate-700 hover:bg-slate-50'">
                        <span x-text="p"></span>
                    </button>
                </template>

                {{-- Next Button --}}
                <button type="button"
                    @click="goToPage(currentPage + 1)"
                    :disabled="currentPage >= lastPage || loading"
                    class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-700 shadow-xs transition hover:bg-slate-50 disabled:opacity-40 disabled:cursor-not-allowed">
                    <i data-lucide="chevron-right" class="h-4 w-4"></i>
                </button>
            </div>
        </div>

    </div>
</div>
@endif

<section class="mt-8 rounded-[2rem] bg-[#991b1b] p-8 text-white shadow-2xl shadow-red-950/15">
    <div class="grid gap-6 lg:grid-cols-[1fr_auto] lg:items-center">
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.28em] text-red-100/80">Ôn tập nhanh</p>
            <h2 class="mt-3 text-3xl font-black tracking-tight sm:text-4xl">Nhìn một lần, lướt một vòng, nhớ lâu hơn</h2>
            <p class="mt-4 max-w-2xl text-white/80 leading-7">
                Flashcard phù hợp để cậu ôn từ vựng hằng ngày trước khi chuyển sang quiz kiểm tra lại kiến thức.
            </p>
        </div>
        <a href="{{ route('quiz', $lessonSlug ? ['lesson' => $lessonSlug] : []) }}"
            class="inline-flex items-center justify-center rounded-full bg-amber-300 px-6 py-3 text-sm font-semibold text-slate-950 transition hover:-translate-y-0.5 hover:bg-amber-200">
            Đi sang quiz →
        </a>
    </div>
</section>

{{-- Hanzi Writer Modal --}}
<div x-data="{
        isOpen: false,
        word: '',
        chars: [],
        currentChar: '',
        writer: null,
        openModal(word) {
            this.word = word || '';
            // B5: Guard against null/undefined word
            this.chars = Array.from(this.word);
            this.currentChar = this.chars[0] || '';
            this.isOpen = true;
            this.$nextTick(() => {
                this.initWriter();
            });
        },
        closeModal() {
            this.isOpen = false;
            if (this.writer) {
                document.getElementById('hanzi-character-target').innerHTML = '';
                this.writer = null;
            }
        },
        setChar(char) {
            this.currentChar = char;
            this.initWriter();
        },
        initWriter() {
            const container = document.getElementById('hanzi-character-target');
            if(!container || !this.currentChar) return;
            container.innerHTML = '';

            if(window.HanziWriter) {
                const size = window.innerWidth < 400 ? 240 : 300;
                this.writer = window.HanziWriter.create('hanzi-character-target', this.currentChar, {
                    width: size,
                    height: size,
                    padding: 20,
                    strokeAnimationSpeed: 1,
                    delayBetweenStrokes: 1000,
                    showOutline: true,
                    strokeColor: '#991b1b',
                    highlightColor: '#10b981',
                    drawingColor: '#333333',
                    drawingWidth: 15,
                });
                this.writer.quiz();
            }
        },
        quiz() {
            if(this.writer) this.writer.quiz();
        },
        animate() {
            if(this.writer) {
                this.writer.animateCharacter({
                    onComplete: () => {
                        // B5: Guard against null writer if modal was closed during animation
                        setTimeout(() => { if (this.writer) this.writer.quiz(); }, 1000);
                    }
                });
            }
        }
    }"
    @open-writer.window="openModal($event.detail)"
    x-show="isOpen"
    style="display: none;"
    x-transition.opacity
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4 backdrop-blur-sm">
    
    <div @click.outside="closeModal()" x-show="isOpen" x-transition.scale.90 class="relative w-full max-w-sm rounded-[2rem] bg-white p-6 shadow-2xl">
        <button @click="closeModal()" class="absolute right-4 top-4 rounded-full p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600">
            <i data-lucide="x" class="h-5 w-5"></i>
        </button>

        <h3 class="mb-4 text-center text-lg font-bold text-slate-800">Tập viết chữ</h3>

        {{-- Multi-character selection --}}
        <template x-if="chars.length > 1">
            <div class="mb-6 flex justify-center gap-2">
                <template x-for="char in chars" :key="char">
                    <button @click="setChar(char)" 
                            :class="char === currentChar ? 'bg-[#991b1b] text-white shadow-md' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                            class="h-12 w-12 rounded-xl text-2xl font-bold transition">
                        <span x-text="char"></span>
                    </button>
                </template>
            </div>
        </template>

        <div class="flex justify-center">
            {{-- Draw grid background --}}
            <div class="relative overflow-hidden rounded-xl border-2 border-amber-200/50 bg-amber-50 shadow-inner" style="width: 300px; height: 300px;">
                {{-- Grid lines --}}
                <div class="absolute inset-0 top-1/2 border-b border-dashed border-amber-200/60"></div>
                <div class="absolute inset-0 left-1/2 border-r border-dashed border-amber-200/60"></div>
                
                {{-- Writer Target --}}
                <div id="hanzi-character-target" class="absolute inset-0 cursor-crosshair"></div>
            </div>
        </div>

        <div class="mt-6 flex justify-center gap-3">
            <button @click="quiz()" class="flex items-center gap-2 rounded-full bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-200">
                <i data-lucide="refresh-ccw" class="h-4 w-4"></i> Xóa bảng
            </button>
            <button @click="animate()" class="flex items-center gap-2 rounded-full bg-amber-100 px-4 py-2 text-sm font-semibold text-amber-700 transition hover:bg-amber-200">
                <i data-lucide="play" class="h-4 w-4"></i> Gợi ý nét
            </button>
        </div>
    </div>
</div>

<script>
(function() {
    let lastSelectTime = 0;
    window.selectFlashcard = function(cardData) {
        if (!cardData) return;
        const now = Date.now();
        if (now - lastSelectTime < 300) return; // Prevent double invocation from both @click and onclick
        lastSelectTime = now;

        // 1. Direct call to Alpine player if reference is bound
        if (window.__deckPlayer && typeof window.__deckPlayer.selectCard === 'function') {
            window.__deckPlayer.selectCard(cardData);
            return;
        }

        // 2. Dispatch custom event as fallback
        window.dispatchEvent(new CustomEvent('select-card', { detail: cardData }));
    };

    window.openVocabModal = function() {
        if (window.__vocabModal && typeof window.__vocabModal.open === 'function') {
            window.__vocabModal.open();
        } else {
            window.dispatchEvent(new CustomEvent('open-vocab-modal'));
        }
    };
})();

function vocabularyModal() {
    return {
        isOpen: false,
        loading: false,
        cards: @json($flashcards->items() ?? []),
        currentPage: {{ $flashcards->currentPage() ?? 1 }},
        lastPage: {{ $flashcards->lastPage() ?? 1 }},
        total: {{ $deckTotal ?? 0 }},
        perPage: 12,
        searchTerm: '',
        filterStarredOnly: false,
        lessonSlug: '{{ $lessonSlug ?? '' }}',
        hskLevel: '{{ $hskLevel ?? '' }}',
        standardCode: '{{ $standardCode ?? 'hsk_2_0' }}',

        init() {
            window.__vocabModal = this;
        },
        open() {
            this.isOpen = true;
            this.$nextTick(() => window.refreshIcons?.());
        },
        close() {
            this.isOpen = false;
        },
        async goToPage(page) {
            if (page < 1 || page > this.lastPage || page === this.currentPage || this.loading) return;
            this.loading = true;
            const offset = (page - 1) * this.perPage;
            let url = `/flashcards/cards?offset=${offset}&limit=${this.perPage}&standard=${encodeURIComponent(this.standardCode)}`;
            if (this.lessonSlug) url += `&lesson=${encodeURIComponent(this.lessonSlug)}`;
            if (this.hskLevel) url += `&hsk=${encodeURIComponent(this.hskLevel)}`;
            if (this.searchTerm) url += `&q=${encodeURIComponent(this.searchTerm)}`;
            if (this.filterStarredOnly) url += `&starred=1`;

            try {
                const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
                if (res.ok) {
                    const data = await res.json();
                    this.cards = data.cards || [];
                    this.total = data.total || 0;
                    this.currentPage = page;
                    this.lastPage = Math.max(1, Math.ceil(this.total / this.perPage));
                    const scrollEl = document.getElementById('vocab-modal-scroll');
                    if (scrollEl) scrollEl.scrollTop = 0;
                }
            } catch (e) {
                console.error('Error fetching cards', e);
            } finally {
                this.loading = false;
                this.$nextTick(() => window.refreshIcons?.());
            }
        },
        async search(val) {
            this.searchTerm = (val || '').trim();
            this.loading = true;
            let url = `/flashcards/cards?offset=0&limit=${this.perPage}&standard=${encodeURIComponent(this.standardCode)}`;
            if (this.lessonSlug) url += `&lesson=${encodeURIComponent(this.lessonSlug)}`;
            if (this.hskLevel) url += `&hsk=${encodeURIComponent(this.hskLevel)}`;
            if (this.searchTerm) url += `&q=${encodeURIComponent(this.searchTerm)}`;
            if (this.filterStarredOnly) url += `&starred=1`;

            try {
                const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
                if (res.ok) {
                    const data = await res.json();
                    this.cards = data.cards || [];
                    this.total = data.total || 0;
                    this.currentPage = 1;
                    this.lastPage = Math.max(1, Math.ceil(this.total / this.perPage));
                }
            } catch (e) {
                console.error('Error searching cards', e);
            } finally {
                this.loading = false;
                this.$nextTick(() => window.refreshIcons?.());
            }
        },
        selectAndPlay(card) {
            if (!card) return;
            window.selectFlashcard(card);
            this.close();
            if (window.toast && typeof window.toast.info === 'function') {
                window.toast.info(`Đã đưa từ 「${card.hanzi}」lên bảng học 3D!`);
            }
        },
        async toggleStar(card) {
            if (!card) return;
            const prev = card.is_starred;
            card.is_starred = !prev;
            try {
                const res = await fetch('{{ route('flashcards.toggleStar') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ flashcard_id: card.id })
                });
                if (!res.ok) {
                    card.is_starred = prev;
                } else {
                    const data = await res.json();
                    card.is_starred = data.is_starred;
                }
            } catch (e) {
                card.is_starred = prev;
            }
            this.$nextTick(() => window.refreshIcons?.());
        }
    };
}
</script>
@endsection