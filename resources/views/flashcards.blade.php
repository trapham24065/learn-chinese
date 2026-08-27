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

{{-- Filters and Search --}}
<div class="mb-8 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
    {{-- Lesson Filter Tabs --}}
    <div class="flex flex-wrap items-center gap-2">
        <a href="{{ route('flashcards', array_filter(['q' => $search])) }}"
            class="rounded-full px-4 py-2 text-sm font-semibold transition
                  {{ (! $lessonSlug && ! $isStarred && ! $hskLevel) ? 'bg-[#991b1b] text-white shadow-md' : 'bg-white/80 text-slate-700 border border-slate-200 hover:border-[#991b1b] hover:text-[#991b1b]' }}">
            Tất cả
        </a>

        {{-- Starred Words Filter Tab --}}
        <a href="{{ route('flashcards', array_filter(['starred' => 1, 'q' => $search])) }}"
            class="inline-flex items-center gap-1.5 rounded-full px-4 py-2 text-sm font-semibold transition
                  {{ $isStarred ? 'bg-amber-500 text-white shadow-md shadow-amber-500/20' : 'bg-amber-50 text-amber-900 border border-amber-200 hover:bg-amber-100 hover:border-amber-300' }}">
            <i data-lucide="star" class="h-3.5 w-3.5 {{ $isStarred ? 'fill-current text-white' : 'fill-current text-amber-500' }}"></i>
            <span>Sổ từ đã lưu</span>
            <span class="rounded-full px-2 py-0.5 text-xs font-bold {{ $isStarred ? 'bg-white/25 text-white' : 'bg-amber-200 text-amber-950' }}">
                {{ $starredCount }}
            </span>
        </a>

        @foreach($lessons as $lesson)
        @if($lesson->flashcards_count > 0)
        <a href="{{ route('flashcards', array_filter(['lesson' => $lesson->slug, 'q' => $search])) }}"
            class="rounded-full px-4 py-2 text-sm font-semibold transition
                      {{ $lessonSlug === $lesson->slug ? 'bg-[#991b1b] text-white shadow-md' : 'bg-white/80 text-slate-700 border border-slate-200 hover:border-[#991b1b] hover:text-[#991b1b]' }}">
            {{ $lesson->title }}
        </a>
        @endif
        @endforeach
    </div>

    {{-- Search Form --}}
    <form action="{{ route('flashcards') }}" method="GET" class="relative w-full lg:max-w-xs">
        @if($lessonSlug) <input type="hidden" name="lesson" value="{{ $lessonSlug }}"> @endif
        @if($hskLevel) <input type="hidden" name="hsk" value="{{ $hskLevel }}"> @endif
        @if($isStarred) <input type="hidden" name="starred" value="1"> @endif
        <input type="text" name="q" value="{{ $search ?? '' }}" placeholder="Tìm chữ Hán, Pinyin, Nghĩa..." 
               class="w-full rounded-full border border-slate-200 bg-white/80 py-2 pl-10 pr-4 text-sm text-slate-800 shadow-sm outline-none transition focus:border-[#991b1b] focus:ring-1 focus:ring-[#991b1b]">
        <i data-lucide="search" class="absolute left-3.5 top-2.5 h-4 w-4 text-slate-400"></i>
    </form>
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

{{-- Interactive Flashcard Deck with Alpine.js (batch loading) --}}
<div x-data="{
    ready: false,
    cards: {{ Js::from($deckBatch) }},
    total: {{ $deckTotal }},
    offset: {{ $deckBatch->count() }},
    lessonSlug: '{{ $lessonSlug ?? '' }}',
    hskLevel: '{{ $hskLevel ?? '' }}',
    searchQuery: '{{ $search ?? '' }}',
    isStarredFilter: {{ $isStarred ? 'true' : 'false' }},
    starredCount: {{ $starredCount }},
    current: 0,
    flipped: false,
    done: [],
    loading: false,
    sessionLogged: false,
    sessionStartTime: Date.now(),

    get card() { return this.cards[this.current] ?? null; },
    get progress() {
        return this.cards.length
            ? Math.round(((this.current + 1) / this.cards.length) * 100)
            : 0;
    },
    get hasMore() { return this.offset < this.total; },

    flip() { this.flipped = !this.flipped; },

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
            this.logSession();
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
                    alert('Vui lòng đăng nhập để lưu từ vựng vào Sổ tay yêu thích!');
                }
            } else {
                const data = await res.json();
                target.is_starred = data.is_starred;
                this.starredCount = data.starred_count;
            }
        } catch(e) {
            target.is_starred = prevState;
        }
        setTimeout(() => window.refreshIcons?.(), 50);
    },

    async submitReview(quality) {
        if (!this.card) return;
        
        try {
            fetch('{{ route('flashcards.review') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    flashcard_id: this.card.id,
                    quality: quality
                })
            });
        } catch(e) { console.error(e); }

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
        this.sessionStartTime = Date.now();
        setTimeout(() => window.refreshIcons?.(), 50);
    },

    async logSession() {
        if (this.sessionLogged) return;
        this.sessionLogged = true;
        const minutes = Math.max(1, Math.round((Date.now() - this.sessionStartTime) / 60000));
        try {
            await fetch('{{ route('flashcards.session') }}', {
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
        } catch(e) {}
    },
}" x-init="ready = true; setTimeout(() => window.refreshIcons?.(), 50);" class="space-y-8">

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
    <div x-show="ready" x-cloak class="space-y-8">
        {{-- Progress bar --}}
        <div class="flex items-center gap-4">
            <div class="flex-1 h-2 rounded-full bg-slate-100 overflow-hidden">
                <div class="h-2 rounded-full bg-gradient-to-r from-[#991b1b] to-amber-400 transition-all duration-500"
                    :style="`width: ${progress}%`"></div>
            </div>
            <span class="shrink-0 text-sm font-semibold text-slate-600">
                <span x-text="current + 1"></span> / <span x-text="total"></span>
                <template x-if="loading">
                    <span class="ml-1 inline-block h-3 w-3 animate-spin rounded-full border-2 border-[#991b1b] border-t-transparent"></span>
                </template>
            </span>
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
            <div class="flip-card w-full max-w-lg cursor-pointer" style="height: 320px;" @click="flip()">
                <div class="flip-card-inner" :class="{ flipped }">
                    {{-- Front: Hanzi + Lesson label + Star button --}}
                    <div class="flip-card-front relative flex flex-col items-center justify-center gap-3 bg-slate-950 p-8 text-white shadow-2xl shadow-slate-950/20">
                        <span class="rounded-full bg-white/10 px-3 py-1 text-xs font-semibold uppercase tracking-widest text-slate-300"
                            x-text="card.lesson"></span>
                        <p class="text-8xl font-black leading-none tracking-tight" x-text="card.hanzi"></p>
                        <p class="mt-2 text-sm text-slate-400">Bấm để xem nghĩa 👆</p>

                        {{-- Star / Bookmark Button (Top Right) --}}
                        <button @click.stop="toggleStar(card.id)" 
                                class="absolute top-6 right-6 flex h-11 w-11 items-center justify-center rounded-full transition-all duration-300 hover:scale-110 active:scale-95 shadow-md"
                                :class="card.is_starred ? 'bg-amber-400 text-slate-950 shadow-amber-400/30 ring-2 ring-amber-300' : 'bg-white/10 text-white hover:bg-white/20'"
                                :title="card.is_starred ? 'Bỏ lưu từ này' : 'Lưu vào Sổ từ vựng'">
                            <i data-lucide="star" class="h-5 w-5" :class="{ 'fill-current': card.is_starred }"></i>
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
                            <i data-lucide="star" class="h-5 w-5" :class="{ 'fill-current': card.is_starred }"></i>
                        </button>

                        <div class="flex flex-1 flex-col justify-center gap-4 p-8 pr-16">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-[#991b1b]" x-text="card.pinyin"></p>
                                <p class="mt-2 text-4xl font-black tracking-tight text-slate-950" x-text="card.meaning"></p>
                            </div>
                            <template x-if="card.example">
                                <div class="rounded-2xl bg-slate-50 border border-slate-200 p-4">
                                    <p class="text-sm text-slate-500">Ví dụ</p>
                                    <p class="mt-1 text-base font-semibold text-slate-900" x-text="card.example"></p>
                                    <p class="mt-0.5 text-xs text-slate-500" x-text="card.example_pinyin"></p>
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

                <template x-if="!flipped">
                    {{-- Flip card --}}
                    <button
                        @click="flip()"
                        class="inline-flex items-center gap-2 rounded-full bg-slate-900 px-8 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-slate-800">
                        <i data-lucide="eye" class="h-4 w-4"></i>
                        Xem đáp án
                    </button>
                </template>

                <template x-if="flipped">
                    <div class="flex items-center gap-3">
                        <button
                            @click="submitReview('review')"
                            class="inline-flex items-center gap-2 rounded-full border border-orange-200 bg-orange-50 px-5 py-2.5 text-sm font-bold text-orange-600 shadow-sm transition hover:bg-orange-100">
                            <i data-lucide="rotate-ccw" class="h-4 w-4"></i> Cần ôn lại
                        </button>
                        <button
                            @click="submitReview('known')"
                            class="inline-flex items-center gap-2 rounded-full bg-emerald-500 px-6 py-2.5 text-sm font-bold text-white shadow-md transition hover:-translate-y-0.5 hover:bg-emerald-600 shadow-emerald-500/20">
                            Đã thuộc <i data-lucide="check" class="h-4 w-4"></i>
                        </button>
                    </div>
                </template>

                {{-- Next / Complete --}}
                <button
                    @click="next()"
                    class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:-translate-y-0.5 hover:border-slate-300 disabled:cursor-not-allowed disabled:opacity-40">
                    <span class="hidden sm:inline" x-text="current === cards.length - 1 ? 'Xong' : 'Tiếp'"></span>
                    <i x-show="current < cards.length - 1" data-lucide="arrow-right" class="h-4 w-4"></i>
                    <i x-show="current === cards.length - 1" data-lucide="circle-check" class="h-4 w-4"></i>
                </button>

            </div>
        </div>
    </template>

    {{-- Completion screen --}}
    <template x-if="done.length >= cards.length">
        <div class="flex flex-col items-center gap-6 rounded-[2rem] bg-slate-950 py-16 text-center text-white shadow-2xl shadow-slate-950/20">
            <div class="grid h-16 w-16 place-items-center rounded-3xl bg-amber-400/20 text-amber-300">
                <i data-lucide="party-popper" class="h-8 w-8"></i>
            </div>
            <div>
                <p class="text-sm uppercase tracking-[0.28em] text-amber-300/80">Xong rồi!</p>
                <h2 class="mt-2 text-3xl font-black">Bạn đã ôn hết <span x-text="cards.length"></span> thẻ!</h2>
                <p class="mt-3 text-slate-400">Phiên học của bạn đã được ghi nhận tự động.</p>
            </div>
            <div class="flex gap-3">
                <button @click="restart()"
                    class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-6 py-3 text-sm font-bold text-white transition hover:bg-white/20">
                    <i data-lucide="rotate-ccw" class="h-4 w-4"></i>
                    <span>Học lại từ đầu</span>
                </button>
                <a href="{{ route('quiz', $lessonSlug ? ['lesson' => $lessonSlug] : []) }}"
                    class="inline-flex items-center gap-2 rounded-full bg-amber-300 px-6 py-3 text-sm font-bold text-slate-950 transition hover:bg-amber-200">
                    <i data-lucide="target" class="h-4 w-4"></i>
                    <span>Làm Quiz kiểm tra →</span>
                </a>
            </div>
        </div>
    </template>
    </div>
</div>

{{-- Static card grid with pagination --}}
<section class="mt-12">
    <div class="mb-5 flex items-center justify-between flex-wrap gap-3">
        <p class="text-sm font-semibold uppercase tracking-[0.22em] text-slate-500">
            Toàn bộ {{ $deckTotal }} thẻ trong bộ này
        </p>
        <p class="text-sm text-slate-400">
            Trang {{ $flashcards->currentPage() }} / {{ $flashcards->lastPage() }}
        </p>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach($flashcards as $card)
        <article class="group relative flex flex-col justify-between overflow-hidden rounded-3xl border border-slate-200/60 bg-white/80 p-5 shadow-sm backdrop-blur transition hover:border-slate-300 hover:shadow-md">
            <div class="flex items-start justify-between gap-3">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                        <p class="truncate text-xs font-bold uppercase tracking-widest text-slate-400">{{ $card->pinyin }}</p>
                        <button type="button" 
                            x-data="{}" 
                            @click.prevent="window.playChineseVoice('{{ addslashes($card->hanzi) }}')" 
                            class="text-slate-300 transition hover:text-blue-500 focus:outline-none"
                            title="Nghe phát âm">
                            <i data-lucide="volume-2" class="h-3.5 w-3.5"></i>
                        </button>
                    </div>
                    <div class="mt-1 flex items-baseline gap-3">
                        <h2 class="text-3xl font-black text-slate-800">{{ $card->hanzi }}</h2>
                    </div>
                    <p class="mt-2 text-sm font-medium leading-relaxed text-slate-600 line-clamp-2" title="{{ $card->meaning }}">{{ $card->meaning }}</p>
                </div>
                
                <div class="flex flex-col items-end gap-2 shrink-0">
                    {{-- Star Button on Grid Card --}}
                    <button type="button"
                            x-data="{ isStarred: {{ ($card->is_starred ?? false) ? 'true' : 'false' }}, loading: false }"
                            @click.prevent="
                                if (loading) return;
                                loading = true;
                                const prev = isStarred;
                                isStarred = !prev;
                                fetch('{{ route('flashcards.toggleStar') }}', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'Accept': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    },
                                    body: JSON.stringify({ flashcard_id: {{ $card->id }} })
                                })
                                .then(r => {
                                    if (!r.ok) {
                                        isStarred = prev;
                                        if (r.status === 401) alert('Vui lòng đăng nhập để lưu từ vựng!');
                                    }
                                    return r.json();
                                })
                                .then(d => { if (d.success) isStarred = d.is_starred; })
                                .catch(() => { isStarred = prev; })
                                .finally(() => { loading = false; setTimeout(() => window.refreshIcons?.(), 50); });
                            "
                            class="flex h-8 w-8 items-center justify-center rounded-full transition hover:scale-110 active:scale-95"
                            :class="isStarred ? 'text-amber-500 bg-amber-50 shadow-sm' : 'text-slate-300 hover:text-amber-400 hover:bg-slate-50'"
                            :title="isStarred ? 'Bỏ lưu khỏi Sổ từ' : 'Lưu vào Sổ từ vựng'">
                        <i data-lucide="star" class="h-4 w-4" :class="{ 'fill-current': isStarred }"></i>
                    </button>

                    @if($card->hsk_level)
                    <span class="shrink-0 rounded-xl bg-red-50/80 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-red-600 border border-red-100">
                        HSK {{ $card->hsk_level }}
                    </span>
                    @elseif($card->lesson)
                    <span class="shrink-0 rounded-xl bg-amber-50 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-amber-700 border border-amber-100">
                        L{{ $card->lesson_id }}
                    </span>
                    @endif
                </div>
            </div>

            @if($card->example)
            <div class="mt-4 border-t border-slate-100 pt-3">
                <p class="text-sm font-medium text-slate-800">{{ $card->example }}</p>
                @if($card->example_pinyin)
                <p class="mt-0.5 text-[11px] text-slate-400">{{ $card->example_pinyin }}</p>
                @endif
                <p class="mt-1 text-xs text-slate-500 line-clamp-2">{{ $card->example_meaning }}</p>
            </div>
            @endif
        </article>
        @endforeach
    </div>

    {{-- Pagination links --}}
    @if($flashcards->hasPages())
    <div class="mt-8 flex items-center justify-center gap-2">
        {{-- Prev --}}
        @if($flashcards->onFirstPage())
        <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl border border-slate-200 bg-slate-50 text-slate-300 cursor-not-allowed">
            <i data-lucide="chevron-left" class="h-4 w-4"></i>
        </span>
        @else
        <a href="{{ $flashcards->previousPageUrl() }}"
           class="inline-flex h-10 w-10 items-center justify-center rounded-2xl border border-slate-200 bg-white text-slate-700 shadow-sm transition hover:border-[#991b1b] hover:text-[#991b1b]">
            <i data-lucide="chevron-left" class="h-4 w-4"></i>
        </a>
        @endif

        {{-- Page numbers --}}
        @foreach($flashcards->getUrlRange(max(1, $flashcards->currentPage() - 2), min($flashcards->lastPage(), $flashcards->currentPage() + 2)) as $page => $url)
        <a href="{{ $url }}"
           class="inline-flex h-10 w-10 items-center justify-center rounded-2xl text-sm font-bold transition
                  {{ $page == $flashcards->currentPage()
                      ? 'bg-[#991b1b] text-white shadow-md shadow-red-900/20'
                      : 'border border-slate-200 bg-white text-slate-700 hover:border-[#991b1b] hover:text-[#991b1b]' }}">
            {{ $page }}
        </a>
        @endforeach

        {{-- Next --}}
        @if($flashcards->hasMorePages())
        <a href="{{ $flashcards->nextPageUrl() }}"
           class="inline-flex h-10 w-10 items-center justify-center rounded-2xl border border-slate-200 bg-white text-slate-700 shadow-sm transition hover:border-[#991b1b] hover:text-[#991b1b]">
            <i data-lucide="chevron-right" class="h-4 w-4"></i>
        </a>
        @else
        <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl border border-slate-200 bg-slate-50 text-slate-300 cursor-not-allowed">
            <i data-lucide="chevron-right" class="h-4 w-4"></i>
        </span>
        @endif
    </div>

    <p class="mt-3 text-center text-xs text-slate-400">
        Hiển thị {{ $flashcards->firstItem() }}–{{ $flashcards->lastItem() }} trong tổng số {{ $deckTotal }} thẻ
    </p>
    @endif
</section>
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
            this.word = word;
            this.chars = Array.from(word);
            this.currentChar = this.chars[0];
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
            if(!container) return;
            container.innerHTML = '';
            
            if(window.HanziWriter) {
                this.writer = window.HanziWriter.create('hanzi-character-target', this.currentChar, {
                    width: 300,
                    height: 300,
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
                        setTimeout(() => this.writer.quiz(), 1000);
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

@endsection