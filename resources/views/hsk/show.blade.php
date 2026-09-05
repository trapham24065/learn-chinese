@extends('layouts.app')

@section('title', $meta['label'] . ' | Chinese Deck')

@section('content')
<style>
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
</style>

{{-- Breadcrumb --}}
<nav class="mb-6 flex items-center gap-2 text-sm text-slate-500">
    <a href="{{ route('hsk.overview') }}" class="hover:text-[#991b1b] transition">Lộ trình HSK</a>
    <span>/</span>
    <span class="font-semibold text-slate-800">{{ $meta['label'] }}</span>
</nav>

{{-- Header --}}
<section class="relative overflow-hidden rounded-[2rem] p-8 text-white mb-8 shadow-2xl" style="background: {{ $meta['color'] }}">
    <div class="absolute -right-8 -top-8 text-[12rem] font-black leading-none opacity-10 select-none">
        {{ ['一','二','三','四','五','六'][$level - 1] }}
    </div>
    <div class="relative grid gap-6 lg:grid-cols-[1fr_auto] lg:items-center">
        <div>
            <span class="rounded-full bg-white/20 px-3 py-1 text-xs font-bold uppercase tracking-widest">{{ $meta['label'] }}</span>
            <h1 class="mt-4 text-4xl font-black tracking-tight sm:text-5xl">{{ $meta['description'] }}</h1>
            <p class="mt-3 text-white/80">~{{ number_format($meta['vocab_count']) }} từ vựng cần nắm · {{ $lessons->total() }} bài học · {{ $flashcards->total() }} flashcard</p>
        </div>
        <div class="flex flex-wrap gap-3">
            @if($prevLevel)
            <a href="{{ route('hsk.show', $prevLevel) }}"
                class="inline-flex items-center gap-1.5 rounded-full border border-white/30 bg-white/10 px-4 py-2 text-sm font-semibold transition hover:bg-white/20">
                <i data-lucide="arrow-left" class="h-4 w-4"></i> HSK {{ $prevLevel }}
            </a>
            @endif
            @if($nextLevel)
            <a href="{{ route('hsk.show', $nextLevel) }}"
                class="inline-flex items-center gap-1.5 rounded-full bg-white px-4 py-2 text-sm font-bold transition hover:bg-white/90" style="color: {{ $meta['color'] }}">
                HSK {{ $nextLevel }} <i data-lucide="arrow-right" class="h-4 w-4"></i>
            </a>
            @endif
        </div>
    </div>
</section>

{{-- Lessons in this HSK level --}}
@if($lessons->isNotEmpty())
<section id="lessons" class="mb-10">
    <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
        <h2 class="flex items-center gap-2 text-lg font-black text-slate-900">
            <span class="grid h-8 w-8 place-items-center rounded-xl bg-red-50 text-[#991b1b]">
                <i data-lucide="book-open" class="h-4 w-4"></i>
            </span>
            Bài học thuộc {{ $meta['label'] }} ({{ $lessons->total() }} bài)
        </h2>
        @if($lessons->hasPages())
        <span class="text-xs font-semibold text-slate-500">
            Trang {{ $lessons->currentPage() }} / {{ $lessons->lastPage() }}
        </span>
        @endif
    </div>

    {{-- Banner học tiếp nếu có bài đang học dở --}}
    @if(isset($inProgressLesson) && $inProgressLesson)
    <div class="mb-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 rounded-2xl border border-amber-200 bg-gradient-to-r from-amber-50 to-orange-50 p-4 shadow-sm">
        <div class="flex items-center gap-3">
            <div class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-amber-500 text-white shadow-md shadow-amber-500/20">
                <i data-lucide="sparkles" class="h-5 w-5"></i>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-amber-700">Đang học dở ({{ $inProgressLesson->progress_percent ?? 0 }}%)</span>
                    <span class="text-xs text-slate-400">· Vừa học gần đây</span>
                </div>
                <h4 class="font-bold text-slate-900 text-sm sm:text-base">{{ $inProgressLesson->title }}</h4>
            </div>
        </div>
        <a href="{{ route('lesson.show', ['slug' => $inProgressLesson->slug]) }}"
            class="inline-flex shrink-0 items-center gap-2 rounded-xl bg-amber-600 px-4 py-2 text-xs sm:text-sm font-bold text-white shadow-sm hover:bg-amber-700 transition">
            Học tiếp ngay <i data-lucide="arrow-right" class="h-4 w-4"></i>
        </a>
    </div>
    @endif

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach($lessons as $lesson)
        @php
            $prog = $progressMap[$lesson->id] ?? null;
            $status = is_array($prog) ? ($prog['status'] ?? 'not_started') : ($prog ?? 'not_started');
            $percent = is_array($prog) ? ($prog['percent'] ?? 0) : 0;
            $isInProgress = $status === 'in_progress';
        @endphp
        <div class="group relative overflow-hidden rounded-[1.75rem] border {{ $isInProgress ? 'border-amber-300 ring-2 ring-amber-400/20' : 'border-white/80' }} bg-white/80 p-6 shadow-xl shadow-slate-900/5 backdrop-blur transition hover:-translate-y-0.5">
            <div class="absolute inset-x-0 top-0 h-1" style="background: {{ $isInProgress ? '#f59e0b' : ($lesson->accent_color ?? $meta['color']) }}"></div>
            <div class="flex items-start justify-between gap-3">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-semibold uppercase tracking-widest"
                            style="color: {{ $lesson->accent_color ?? $meta['color'] }}">
                            {{ $meta['label'] }}
                        </span>
                        @if($isInProgress)
                        <span class="inline-flex items-center gap-1 rounded-md bg-amber-100 px-1.5 py-0.5 text-[10px] font-bold text-amber-800">
                            <i data-lucide="timer" class="h-3 w-3"></i> {{ $percent }}%
                        </span>
                        @endif
                    </div>
                    <h3 class="mt-2 text-lg font-black text-slate-900">{{ $lesson->title }}</h3>
                    <p class="mt-1 text-sm text-slate-600">{{ $lesson->summary }}</p>
                </div>
                <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl
    @if($status === 'completed')
        bg-emerald-50 text-emerald-600
    @elseif($status === 'in_progress')
        bg-amber-50 text-amber-600
    @else
        bg-slate-100 text-slate-400
    @endif">
                    @if($status === 'completed')
                    <i data-lucide="circle-check-big" class="h-5 w-5"></i>
                    @elseif($status === 'in_progress')
                    <i data-lucide="book-open" class="h-5 w-5"></i>
                    @else
                    <i data-lucide="circle" class="h-5 w-5"></i>
                    @endif
                </span>
            </div>
            <div class="mt-4 flex items-center gap-3 text-xs text-slate-500">
                <span class="inline-flex items-center gap-1"><i data-lucide="timer" class="h-3.5 w-3.5"></i> {{ $lesson->estimated_minutes }} phút</span>
                <span>·</span>
                <span class="inline-flex items-center gap-1"><i data-lucide="circle-help" class="h-3.5 w-3.5"></i> {{ $lesson->questions_count }} câu hỏi</span>
                <span>·</span>
                <span class="inline-flex items-center gap-1"><i data-lucide="layers" class="h-3.5 w-3.5"></i> {{ $lesson->flashcards_count }} thẻ</span>
            </div>
            <div class="mt-4 flex gap-2">
                <a href="{{ route('lesson.show', ['slug' => $lesson->slug]) }}"
                    class="flex-1 inline-flex items-center justify-center gap-1.5 rounded-full border {{ $isInProgress ? 'border-amber-400 bg-amber-50 text-amber-950 font-bold' : 'border-slate-200 bg-white text-slate-700 font-bold hover:bg-slate-50' }} py-2 text-center text-xs transition">
                    <i data-lucide="book-open" class="h-3.5 w-3.5"></i> {{ $isInProgress ? 'Học tiếp (' . $percent . '%)' : 'Lý thuyết' }}
                </a>
                <a href="{{ route('flashcards', ['lesson' => $lesson->slug]) }}"
                    class="flex-1 inline-flex items-center justify-center gap-1.5 rounded-full bg-slate-100 py-2 text-center text-xs font-bold text-slate-700 hover:bg-slate-200 transition">
                    <i data-lucide="layers" class="h-3.5 w-3.5"></i> Flashcard
                </a>
                <a href="{{ route('quiz', ['lesson' => $lesson->slug]) }}"
                    class="flex-1 inline-flex items-center justify-center gap-1.5 rounded-full py-2 text-center text-xs font-bold text-white transition hover:opacity-90"
                    style="background: {{ $meta['color'] }}">
                    <i data-lucide="target" class="h-3.5 w-3.5"></i> Quiz
                </a>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Custom Pagination links for Lessons --}}
    @if($lessons->hasPages())
    <div class="mt-8 flex items-center justify-center gap-2">
        {{-- Prev --}}
        @if($lessons->onFirstPage())
        <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl border border-slate-200 bg-slate-50 text-slate-300 cursor-not-allowed">
            <i data-lucide="chevron-left" class="h-4 w-4"></i>
        </span>
        @else
        <a href="{{ $lessons->fragment('lessons')->previousPageUrl() }}"
           class="inline-flex h-10 w-10 items-center justify-center rounded-2xl border border-slate-200 bg-white text-slate-700 shadow-sm transition hover:border-[#991b1b] hover:text-[#991b1b]">
            <i data-lucide="chevron-left" class="h-4 w-4"></i>
        </a>
        @endif

        {{-- Page numbers --}}
        @foreach($lessons->fragment('lessons')->getUrlRange(max(1, $lessons->currentPage() - 2), min($lessons->lastPage(), $lessons->currentPage() + 2)) as $page => $url)
        <a href="{{ $url }}"
           class="inline-flex h-10 w-10 items-center justify-center rounded-2xl text-sm font-bold transition
                  {{ $page == $lessons->currentPage()
                      ? 'bg-[#991b1b] text-white shadow-md shadow-red-900/20'
                      : 'border border-slate-200 bg-white text-slate-700 hover:border-[#991b1b] hover:text-[#991b1b]' }}">
            {{ $page }}
        </a>
        @endforeach

        {{-- Next --}}
        @if($lessons->hasMorePages())
        <a href="{{ $lessons->fragment('lessons')->nextPageUrl() }}"
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
        Hiển thị {{ $lessons->firstItem() }}–{{ $lessons->lastItem() }} trong tổng số {{ $lessons->total() }} bài học
    </p>
    @endif
</section>
@endif

{{-- Flashcard 3D Deck & Vocabulary Section --}}
@if($flashcards->isNotEmpty())
<section id="flashcards" class="mb-12">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="flex items-center gap-2 text-xl font-black text-slate-900">
                <i data-lucide="layers" class="h-5 w-5 text-slate-600"></i>
                Ôn luyện Flashcard 3D • {{ $meta['label'] }}
            </h2>
            <p class="text-xs text-slate-500 mt-1">Lật thẻ để kiểm tra trí nhớ, tập viết chữ và luyện nghe phát âm.</p>
        </div>
        <a href="{{ route('flashcards', ['hsk' => $level]) }}" class="inline-flex items-center gap-1 text-xs font-bold hover:underline" style="color: {{ $meta['color'] }}">
            Chế độ chuyên sâu <i data-lucide="arrow-right" class="h-3.5 w-3.5"></i>
        </a>
    </div>

    <div x-data="{
        ready: false,
        cards: {{ Js::from($flashcards->values()->map(fn($f) => [
            'id'              => $f->id,
            'hanzi'           => $f->hanzi,
            'pinyin'          => $f->pinyin,
            'meaning'         => $f->meaning,
            'example'         => $f->example,
            'example_pinyin'  => $f->example_pinyin,
            'example_meaning' => $f->example_meaning,
            'is_starred'      => (bool) ($f->is_starred ?? false),
        ])) }},

        current: 0,
        flipped: false,
        done: [],
        submitting: false,

        get card() {
            return this.cards[this.current] ?? null;
        },

        get progress() {
            return this.cards.length
                ? Math.round(((this.current + 1) / this.cards.length) * 100)
                : 0;
        },

        flip() {
            this.flipped = !this.flipped;
        },

        async toggleStar(cardId) {
            const target = this.cards.find(c => c.id === cardId);
            if (!target) return;
            const prevState = target.is_starred;
            target.is_starred = !prevState;

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
                    if (res.status === 401) alert('Vui lòng đăng nhập để lưu từ vựng vào Sổ tay yêu thích!');
                } else {
                    const data = await res.json();
                    target.is_starred = data.is_starred;
                }
            } catch(e) {
                target.is_starred = prevState;
            }
            setTimeout(() => window.refreshIcons?.(), 50);
        },

        next() {
            if (!this.done.includes(this.current)) {
                this.done.push(this.current);
            }

            if (this.current < this.cards.length - 1) {
                this.flipped = false;
                setTimeout(() => {
                    this.current++;
                    setTimeout(() => window.refreshIcons?.(), 50);
                }, 150);
            }
        },

        prev() {
            if (this.current > 0) {
                this.flipped = false;
                setTimeout(() => {
                    this.current--;
                    setTimeout(() => window.refreshIcons?.(), 50);
                }, 150);
            }
        },

        restart() {
            this.current = 0;
            this.flipped = false;
            this.done = [];
            setTimeout(() => window.refreshIcons?.(), 50);
        },

        async submitReview(quality) {
            if (!this.card) return;
            this.submitting = true;
            try {
                await fetch('{{ route('flashcards.review') }}', {
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
            this.submitting = false;
            this.next();
        },

        speak(text) {
            if (text) {
                window.playChineseVoice(text);
            }
        }
    }" x-init="ready = true; setTimeout(() => window.refreshIcons?.(), 50);" class="space-y-6">

        {{-- Skeleton Loader for HSK card --}}
        <div x-show="!ready" class="flex flex-col items-center gap-6">
            <div class="h-2 w-full max-w-lg rounded-full bg-slate-100 skeleton-shimmer"></div>
            <div class="w-full max-w-lg rounded-[2rem] p-8 shadow-2xl h-[300px] flex flex-col items-center justify-center gap-4 skeleton-shimmer" style="background: {{ $meta['color'] }}20">
                <div class="h-20 w-32 rounded-2xl bg-slate-300/40"></div>
                <div class="h-4 w-28 rounded-full bg-slate-300/40"></div>
            </div>
            <div class="flex justify-center gap-3">
                <div class="h-10 w-24 rounded-full bg-slate-200 skeleton-shimmer"></div>
                <div class="h-10 w-32 rounded-full bg-slate-300 skeleton-shimmer"></div>
                <div class="h-10 w-24 rounded-full bg-slate-200 skeleton-shimmer"></div>
            </div>
        </div>

        {{-- Real Interactive 3D Deck --}}
        <div x-show="ready" x-cloak class="space-y-6">
            {{-- Progress Bar & Counter --}}
            <div class="flex items-center gap-4 max-w-xl mx-auto">
                <div class="flex-1 h-2 rounded-full bg-slate-100 overflow-hidden">
                    <div class="h-2 rounded-full transition-all duration-500"
                        :style="`width: ${progress}%; background: {{ $meta['color'] }}`"></div>
                </div>
                <span class="shrink-0 text-xs font-bold text-slate-500">
                    <span x-text="current + 1"></span> / <span x-text="cards.length"></span> thẻ
                </span>
            </div>

            {{-- Dot Navigation --}}
            <div class="flex flex-wrap justify-center gap-1.5 max-w-lg mx-auto">
                <template x-for="(c, idx) in cards" :key="idx">
                    <button @click="current = idx; flipped = false; setTimeout(() => window.refreshIcons?.(), 50);"
                        class="h-2 rounded-full transition-all duration-300"
                        :class="done.includes(idx) ? 'w-5 bg-emerald-500' : (idx === current ? 'w-5' : 'w-2 bg-slate-200')"
                        :style="idx === current ? 'background: {{ $meta['color'] }}' : ''">
                    </button>
                </template>
            </div>

            {{-- 3D Flip Card Container --}}
            <template x-if="card && current < cards.length && done.length < cards.length">
                <div class="flex flex-col items-center gap-6">
                    <div class="flip-card w-full max-w-lg cursor-pointer" style="height: 300px;" @click="flip()">
                        <div class="flip-card-inner" :class="{ flipped }">
                            
                            {{-- Front Face --}}
                            <div class="flip-card-front relative flex flex-col items-center justify-center gap-3 text-white shadow-2xl shadow-slate-950/20"
                                style="background: {{ $meta['color'] }}">
                                
                                <span class="rounded-full bg-white/20 px-3 py-1 text-xs font-black uppercase tracking-widest text-white/90">
                                    {{ $meta['label'] }}
                                </span>

                                <p class="text-7xl sm:text-8xl font-black leading-none tracking-tight" x-text="card?.hanzi"></p>
                                <p class="mt-1 text-xs text-white/70">Bấm để lật thẻ 👆</p>

                                {{-- Star / Bookmark Button (Top Right) --}}
                                <button @click.stop="toggleStar(card?.id)" 
                                        class="absolute top-5 right-5 flex h-11 w-11 items-center justify-center rounded-full transition-all duration-300 hover:scale-110 active:scale-95 shadow-md"
                                        :class="card?.is_starred ? 'bg-amber-400 text-slate-950 shadow-amber-400/30 ring-2 ring-amber-300' : 'bg-black/25 text-white hover:bg-black/40'"
                                        :title="card?.is_starred ? 'Bỏ lưu từ này' : 'Lưu vào Sổ từ vựng'">
                                    <i data-lucide="star" class="h-5 w-5" :class="{ 'fill-current': card?.is_starred }"></i>
                                </button>

                                {{-- Action Buttons (Writing & Speech) --}}
                                <div class="absolute bottom-5 right-5 flex items-center gap-2">
                                    <button @click.stop="$dispatch('open-writer', card?.hanzi)" 
                                            class="flex h-11 w-11 items-center justify-center rounded-full bg-black/25 text-white transition hover:scale-110 hover:bg-black/40 active:scale-95 shadow-md" 
                                            title="Tập viết nét chữ Hán">
                                        <i data-lucide="pen-tool" class="h-4 w-4"></i>
                                    </button>
                                    <button @click.stop="speak(card?.hanzi)" 
                                            class="flex h-11 w-11 items-center justify-center rounded-full bg-black/25 text-white transition hover:scale-110 hover:bg-black/40 active:scale-95 shadow-md" 
                                            title="Nghe phát âm chuẩn">
                                        <i data-lucide="volume-2" class="h-4 w-4"></i>
                                    </button>
                                </div>
                            </div>

                            {{-- Back Face --}}
                            <div class="flip-card-back relative flex flex-col justify-between overflow-hidden bg-white p-7 shadow-2xl shadow-slate-900/10">
                                <div class="absolute inset-x-0 top-0 h-1.5" style="background: {{ $meta['color'] }}"></div>
                                
                                {{-- Star Button on Back Face --}}
                                <button @click.stop="toggleStar(card?.id)" 
                                        class="absolute top-5 right-5 flex h-10 w-10 items-center justify-center rounded-full transition-all duration-300 hover:scale-110 active:scale-95 shadow-sm"
                                        :class="card?.is_starred ? 'bg-amber-100 text-amber-600 ring-1 ring-amber-300' : 'bg-slate-100 text-slate-400 hover:text-amber-500 hover:bg-amber-50'"
                                        :title="card?.is_starred ? 'Bỏ lưu từ này' : 'Lưu vào Sổ từ vựng'">
                                    <i data-lucide="star" class="h-5 w-5" :class="{ 'fill-current': card?.is_starred }"></i>
                                </button>

                                <div class="flex flex-1 flex-col justify-center gap-3 pr-12">
                                    <div>
                                        <p class="text-xs font-bold uppercase tracking-widest" style="color: {{ $meta['color'] }}" x-text="card?.pinyin"></p>
                                        <p class="mt-1 text-3xl font-black text-slate-950" x-text="card?.meaning"></p>
                                    </div>

                                    <template x-if="card?.example">
                                        <div class="rounded-2xl bg-slate-50 border border-slate-100 p-3.5 text-left">
                                            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Ví dụ câu:</p>
                                            <p class="mt-1 text-sm font-bold text-slate-900" x-text="card?.example"></p>
                                            <p class="text-xs text-slate-500" x-text="card?.example_pinyin"></p>
                                            <p class="mt-0.5 text-xs text-slate-600 italic" x-text="card?.example_meaning"></p>
                                        </div>
                                    </template>
                                </div>
                            </div>

                        </div>
                    </div>

                    {{-- Controls Bar --}}
                    <div class="flex flex-wrap items-center justify-center gap-3">
                        <button @click="prev()" :disabled="current === 0"
                                class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-slate-300 disabled:cursor-not-allowed disabled:opacity-40">
                            <i data-lucide="arrow-left" class="h-4 w-4"></i>
                            <span class="hidden sm:inline">Trước</span>
                        </button>

                        <template x-if="!flipped">
                            <button @click="flip()"
                                    class="inline-flex items-center gap-2 rounded-full bg-slate-950 px-8 py-2.5 text-sm font-bold text-white shadow-md transition hover:bg-slate-800 active:scale-95">
                                <i data-lucide="eye" class="h-4 w-4"></i>
                                Xem đáp án
                            </button>
                        </template>

                        <template x-if="flipped">
                            <div class="flex items-center gap-2.5">
                                <button @click="submitReview('review')" :disabled="submitting"
                                        class="inline-flex items-center gap-2 rounded-full border border-orange-200 bg-orange-50 px-5 py-2.5 text-sm font-bold text-orange-600 transition hover:bg-orange-100 disabled:opacity-50">
                                    <i data-lucide="rotate-ccw" class="h-4 w-4"></i> Ôn lại
                                </button>
                                <button @click="submitReview('known')" :disabled="submitting"
                                        class="inline-flex items-center gap-2 rounded-full bg-emerald-500 px-6 py-2.5 text-sm font-bold text-white shadow-md shadow-emerald-500/20 transition hover:bg-emerald-600 disabled:opacity-50">
                                    Đã thuộc <i data-lucide="check" class="h-4 w-4"></i>
                                </button>
                            </div>
                        </template>

                        <button @click="next()"
                                class="inline-flex items-center gap-2 rounded-full px-5 py-2.5 text-sm font-bold text-white shadow-md transition hover:opacity-90 active:scale-95"
                                style="background: {{ $meta['color'] }}">
                            <span class="hidden sm:inline" x-text="current === cards.length - 1 ? 'Xong' : 'Tiếp'"></span>
                            <i x-show="current < cards.length - 1" data-lucide="arrow-right" class="h-4 w-4"></i>
                            <i x-show="current === cards.length - 1" data-lucide="circle-check" class="h-4 w-4"></i>
                        </button>
                    </div>
                </div>
            </template>

            {{-- Completion Screen --}}
            <template x-if="done.length >= cards.length">
                <div class="flex flex-col items-center gap-5 rounded-[2.5rem] bg-slate-950 py-12 px-6 text-center text-white shadow-2xl max-w-lg mx-auto">
                    <span class="text-6xl">🎉</span>
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.25em] text-amber-300">Hoàn thành xuất sắc!</p>
                        <h3 class="mt-2 text-2xl font-black">Bạn đã ôn xong toàn bộ thẻ {{ $meta['label'] }}!</h3>
                        <p class="mt-2 text-xs text-slate-400">Hãy tiếp tục thử thách bản thân với bài kiểm tra trắc nghiệm nhé.</p>
                    </div>
                    <div class="flex flex-wrap justify-center gap-3 pt-2">
                        <button @click="restart()"
                                class="rounded-full border border-white/20 bg-white/10 px-5 py-2.5 text-xs font-bold text-white transition hover:bg-white/20">
                            🔄 Học lại từ đầu
                        </button>
                        <a href="{{ route('quiz') }}"
                           class="rounded-full bg-amber-300 px-6 py-2.5 text-xs font-bold text-slate-950 transition hover:bg-amber-200 shadow-md">
                            🎯 Làm Quiz trắc nghiệm →
                        </a>
                    </div>
                </div>
            </template>
        </div>

    </div>

    {{-- Static card grid with pagination (Identical to main flashcards page) --}}
    <div class="mt-12">
        <div class="mb-5 flex items-center justify-between flex-wrap gap-3">
            <p class="text-sm font-semibold uppercase tracking-[0.22em] text-slate-500">
                Toàn bộ {{ $flashcards->total() }} thẻ thuộc {{ $meta['label'] }}
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
                                onclick="window.playChineseVoice('{{ addslashes($card->hanzi) }}')" 
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

        {{-- Custom Pagination links --}}
        @if($flashcards->hasPages())
        <div class="mt-8 flex items-center justify-center gap-2">
            {{-- Prev --}}
            @if($flashcards->onFirstPage())
            <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl border border-slate-200 bg-slate-50 text-slate-300 cursor-not-allowed">
                <i data-lucide="chevron-left" class="h-4 w-4"></i>
            </span>
            @else
            <a href="{{ $flashcards->fragment('flashcards')->previousPageUrl() }}"
               class="inline-flex h-10 w-10 items-center justify-center rounded-2xl border border-slate-200 bg-white text-slate-700 shadow-sm transition hover:border-[#991b1b] hover:text-[#991b1b]">
                <i data-lucide="chevron-left" class="h-4 w-4"></i>
            </a>
            @endif

            {{-- Page numbers --}}
            @foreach($flashcards->fragment('flashcards')->getUrlRange(max(1, $flashcards->currentPage() - 2), min($flashcards->lastPage(), $flashcards->currentPage() + 2)) as $page => $url)
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
            <a href="{{ $flashcards->fragment('flashcards')->nextPageUrl() }}"
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
            Hiển thị {{ $flashcards->firstItem() }}–{{ $flashcards->lastItem() }} trong tổng số {{ $flashcards->total() }} thẻ
        </p>
        @endif
    </div>

</section>
@else
<div class="mb-10 flex flex-col items-center justify-center rounded-[2.5rem] border border-dashed border-slate-300 bg-white/60 py-12 px-6 text-center shadow-sm backdrop-blur">
    <div class="grid h-14 w-14 place-items-center rounded-2xl bg-slate-100 text-slate-400">
        <i data-lucide="layers" class="h-7 w-7"></i>
    </div>
    <p class="mt-3 text-base font-bold text-slate-800">Chưa có flashcard cho {{ $meta['label'] }}</p>
    <p class="mt-1 text-xs text-slate-500">Các thẻ từ vựng cho cấp độ này đang được cập nhật thêm.</p>
</div>
@endif

{{-- CTA: Quiz --}}
<section class="rounded-[2rem] p-8 text-white shadow-2xl mb-4" style="background: #111827">
    <div class="grid gap-4 lg:grid-cols-[1fr_auto] lg:items-center">
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.28em] text-amber-300/80">Kiểm tra kiến thức</p>
            <h2 class="mt-2 text-2xl font-black">Bạn đã sẵn sàng làm quiz {{ $meta['label'] }}?</h2>
            <p class="mt-2 text-white/70 text-sm">Ôn xong flashcard rồi làm quiz để củng cố và ghi nhớ lâu hơn.</p>
        </div>
        <a href="{{ route('quiz') }}" class="inline-flex items-center gap-2 justify-center rounded-full bg-amber-300 px-6 py-3 text-sm font-bold text-slate-950 transition hover:bg-amber-200 hover:-translate-y-0.5">
            <i data-lucide="target" class="h-4 w-4"></i>
            Làm Quiz ngay
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