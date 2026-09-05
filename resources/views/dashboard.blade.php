@extends('layouts.app')

@section('title', 'Dashboard Học Viên | Chinese Deck')

@section('content')
<div x-data="studentDashboard()" x-init="initDashboard()" class="space-y-8">
    {{-- Toast Notification --}}
    <div x-show="toastMessage"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 -translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-4"
         class="fixed top-6 right-6 z-50 flex items-center gap-3 rounded-2xl bg-slate-950 px-5 py-3.5 text-sm font-semibold text-white shadow-2xl shadow-slate-950/30 border border-white/15">
        <i data-lucide="sparkles" class="h-4 w-4 text-amber-400 shrink-0"></i>
        <span x-text="toastMessage"></span>
    </div>

    {{-- Welcome & Today Focus Header --}}
    <section class="grid gap-6 lg:grid-cols-[1.15fr_0.85fr]">
        <div>
            <div class="inline-flex items-center gap-2 rounded-full bg-red-100 px-3 py-1 text-xs font-bold uppercase tracking-[0.24em] text-[#991b1b]">
                <i data-lucide="layout-dashboard" class="h-3.5 w-3.5"></i>
                Trung tâm học tập
            </div>
            <h1 class="mt-3 text-4xl font-black tracking-tight text-slate-950 sm:text-5xl">
                Xin chào, {{ $student->name }}!
            </h1>
            <p class="mt-3 max-w-2xl text-base leading-7 text-slate-700 sm:text-lg">
                Theo dõi tiến độ lộ trình, làm quiz, ôn thẻ từ vựng và ghi nhận kết quả học tập mỗi ngày.
            </p>

            {{-- Quick Study Session Logger --}}
            <div class="mt-6 rounded-3xl border border-white/80 bg-white/80 p-4 shadow-lg shadow-slate-900/5 backdrop-blur">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-500 flex items-center gap-1.5">
                    <i data-lucide="zap" class="h-3.5 w-3.5 text-amber-500"></i>
                    <span>Ghi nhận buổi tự học hôm nay:</span>
                </p>
                <div class="mt-3 flex flex-wrap gap-2">
                    <button type="button"
                            @click="quickLogSession(10, 'lesson', 'Ôn từ vựng')"
                            :disabled="isLogging"
                            class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-3.5 py-1.5 text-xs font-bold text-slate-800 transition hover:bg-[#991b1b] hover:text-white active:scale-95 disabled:opacity-50">
                        <span>+10 phút</span>
                        <span class="text-slate-400 group-hover:text-white">Từ vựng</span>
                    </button>
                    <button type="button"
                            @click="quickLogSession(15, 'lesson', 'Luyện phát âm')"
                            :disabled="isLogging"
                            class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-3.5 py-1.5 text-xs font-bold text-slate-800 transition hover:bg-[#991b1b] hover:text-white active:scale-95 disabled:opacity-50">
                        <span>+15 phút</span>
                        <span class="text-slate-400 group-hover:text-white">Phát âm</span>
                    </button>
                    <button type="button"
                            @click="quickLogSession(20, 'lesson', 'Học bài mới')"
                            :disabled="isLogging"
                            class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-3.5 py-1.5 text-xs font-bold text-slate-800 transition hover:bg-[#991b1b] hover:text-white active:scale-95 disabled:opacity-50">
                        <span>+20 phút</span>
                        <span class="text-slate-400 group-hover:text-white">Bài học</span>
                    </button>
                    <a href="{{ route('flashcards', ['starred' => 1]) }}"
                       class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 border border-amber-200/80 px-3.5 py-1.5 text-xs font-bold text-amber-900 transition hover:bg-amber-100 active:scale-95">
                        <i data-lucide="star" class="h-4 w-4 fill-current text-amber-500"></i>
                        <span>Sổ từ đã lưu ({{ $starredCount }})</span>
                    </a>
                    <a href="{{ route('stories.index') }}"
                       class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 border border-emerald-200 px-3.5 py-1.5 text-xs font-bold text-emerald-900 transition hover:bg-emerald-200 active:scale-95">
                        <i data-lucide="book-open-check" class="h-4 w-4 text-emerald-700"></i>
                        <span>Luyện đọc hiểu</span>
                    </a>
                    <a href="{{ route('hsk.mock.index') }}"
                       class="inline-flex items-center gap-1.5 rounded-full bg-red-100 border border-red-200 px-3.5 py-1.5 text-xs font-bold text-red-900 transition hover:bg-red-200 active:scale-95">
                        <i data-lucide="award" class="h-4 w-4 text-[#991b1b]"></i>
                        <span>Thi thử HSK</span>
                    </a>
                    <a href="{{ route('quiz') }}"
                       class="inline-flex items-center gap-1.5 rounded-full bg-amber-100 px-3.5 py-1.5 text-xs font-bold text-amber-900 transition hover:bg-amber-200 active:scale-95">
                        <i data-lucide="target" class="h-4 w-4"></i>
                        <span>Luyện tập nhanh</span>
                    </a>
                </div>
            </div>
        </div>

        {{-- Today Goal Card --}}
        <div class="rounded-[2rem] bg-slate-950 p-6 text-white shadow-2xl shadow-slate-950/20">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-amber-200/80">Hôm nay</p>
                <span class="rounded-full bg-white/10 px-3 py-1 text-xs font-bold text-amber-300">
                    Mục tiêu: <span x-text="dailyGoal">20</span> phút
                </span>
            </div>

            <div class="mt-4 flex items-end justify-between gap-4">
                <div>
                    <p class="text-xs text-slate-400">Thời gian đã học</p>
                    <p class="mt-1 text-3xl font-black sm:text-4xl">
                        <span x-text="todayMinutes">{{ $todayMinutes }}</span> phút
                    </p>
                </div>
                <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-2.5 text-right">
                    <p class="text-[10px] uppercase tracking-wider text-slate-400">Đạt chỉ tiêu</p>
                    <p class="mt-0.5 text-xl font-black text-amber-300" x-text="todayGoalPercent + '%'">
                        {{ min(100, (int) round(($todayMinutes / 20) * 100)) }}%
                    </p>
                </div>
            </div>

            <div class="mt-4">
                <div class="h-2.5 w-full overflow-hidden rounded-full bg-white/10">
                    <div class="h-full rounded-full bg-gradient-to-r from-amber-300 to-red-400 transition-all duration-500"
                         :style="'width: ' + todayGoalPercent + '%'"></div>
                </div>
            </div>

            <div class="mt-4 flex items-center justify-between text-xs text-slate-300">
                <span x-show="todayMinutes >= dailyGoal" class="text-emerald-300 font-semibold inline-flex items-center gap-1.5">
                    <i data-lucide="party-popper" class="h-4 w-4 text-amber-400"></i>
                    <span>Hoàn thành xuất sắc mục tiêu ngày!</span>
                </span>
                <span x-show="todayMinutes < dailyGoal" class="text-slate-400">
                    Còn <strong class="text-white" x-text="dailyGoal - todayMinutes"></strong> phút để đạt chỉ tiêu hôm nay.
                </span>
                <span class="inline-flex items-center gap-1 text-amber-200">
                    <i data-lucide="flame" class="h-3.5 w-3.5 text-amber-400"></i>
                    <span>Streak <span x-text="streakDays">{{ $streakDays }}</span> ngày</span>
                </span>
            </div>
        </div>
    </section>

    @if(isset($dueFlashcardsCount) && $dueFlashcardsCount > 0)
    <section class="rounded-[1.75rem] border border-[#991b1b]/20 bg-white p-6 shadow-xl shadow-slate-900/5 transition hover:-translate-y-0.5"
             x-data="{ 
                 cards: {{ Js::from($dueFlashcards) }},
                 submitting: false,
                 async submitReview(cardId, quality, index) {
                     if (this.submitting) return;
                     this.submitting = true;
                     try {
                         await fetch('{{ route('flashcards.review') }}', {
                             method: 'POST',
                             headers: {
                                 'Content-Type': 'application/json',
                                 'X-CSRF-TOKEN': '{{ csrf_token() }}'
                             },
                             body: JSON.stringify({ flashcard_id: cardId, quality: quality })
                         });
                         this.cards.splice(index, 1);
                     } catch(e) { console.error(e); }
                     this.submitting = false;
                 },
                 speak(text) {
                     window.playChineseVoice(text);
                 }
             }"
             x-show="cards.length > 0">
        
        <div class="flex items-center gap-4 mb-6 pb-4 border-b border-slate-100">
            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-red-100 text-[#991b1b]">
                <i data-lucide="layers" class="h-6 w-6"></i>
            </div>
            <div class="flex-1">
                <h3 class="text-xl font-bold text-slate-900">Ôn tập Flashcard</h3>
                <p class="mt-1 text-sm text-slate-500">Bạn có <strong class="text-[#991b1b]" x-text="cards.length"></strong> thẻ cần ôn lại hôm nay.</p>
            </div>
            <a href="{{ route('flashcards') }}" class="hidden sm:inline-flex rounded-full bg-slate-900 px-5 py-2 text-xs font-bold text-white transition hover:bg-slate-800">
                Ôn chế độ 3D
            </a>
        </div>

        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 max-h-[400px] overflow-y-auto pr-2 custom-scrollbar">
            <template x-for="(card, index) in cards" :key="card.id">
                <div x-data="{ expanded: false }" class="rounded-2xl border border-slate-200 bg-slate-50 p-4 transition-all">
                    
                    {{-- Compact View --}}
                    <div class="flex items-center justify-between gap-4">
                        <div class="flex items-center gap-2">
                            <span class="text-3xl font-black text-slate-900" x-text="card.hanzi"></span>
                            <button @click.stop="speak(card.hanzi)" class="rounded-full bg-slate-100 p-2 text-slate-400 hover:bg-slate-200 hover:text-[#991b1b] transition" title="Nghe phát âm">
                                <i data-lucide="volume-2" class="h-4 w-4"></i>
                            </button>
                        </div>
                        <button @click="expanded = !expanded" 
                                class="rounded-full bg-white px-4 py-2 text-xs font-semibold text-slate-700 shadow-sm border border-slate-200 hover:bg-slate-50 transition">
                            <span x-text="expanded ? 'Đóng' : 'Xem'"></span>
                        </button>
                    </div>

                    {{-- Expanded View --}}
                    <div x-show="expanded" x-transition class="mt-4 pt-4 border-t border-slate-200">
                        <p class="text-[10px] font-bold text-[#991b1b] uppercase tracking-widest" x-text="card.pinyin"></p>
                        <p class="mt-1 text-sm font-bold text-slate-900 leading-tight" x-text="card.meaning"></p>
                        
                        <div class="mt-4 flex gap-2">
                            <button @click="submitReview(card.id, 'review', index)" :disabled="submitting"
                                class="flex-1 rounded-xl bg-orange-100 py-2 text-xs font-bold text-orange-700 hover:bg-orange-200 transition disabled:opacity-50">
                                Cần ôn
                            </button>
                            <button @click="submitReview(card.id, 'known', index)" :disabled="submitting"
                                class="flex-1 rounded-xl bg-emerald-500 py-2 text-xs font-bold text-white hover:bg-emerald-600 shadow-sm shadow-emerald-500/20 transition disabled:opacity-50">
                                Đã thuộc
                            </button>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </section>
    @endif

    {{-- Overview 4 Stat Cards --}}
    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <article class="rounded-[1.75rem] border border-white/80 bg-white/80 p-6 shadow-xl shadow-slate-900/5 backdrop-blur transition hover:-translate-y-0.5">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-[#991b1b]">Streak</p>
                <i data-lucide="flame" class="h-5 w-5 text-amber-500"></i>
            </div>
            <p class="mt-3 text-4xl font-black tracking-tight text-slate-950">
                <span x-text="streakDays">{{ $streakDays }}</span> ngày
            </p>
            <p class="mt-2 text-xs text-slate-500">Giữ nhịp học liên tục</p>
        </article>

        <article class="rounded-[1.75rem] border border-white/80 bg-white/80 p-6 shadow-xl shadow-slate-900/5 backdrop-blur transition hover:-translate-y-0.5">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-[#991b1b]">Điểm trung bình</p>
                <i data-lucide="target" class="h-5 w-5 text-[#991b1b]"></i>
            </div>
            <p class="mt-3 text-4xl font-black tracking-tight text-slate-950">
                {{ $averageScore }}%
            </p>
            <p class="mt-2 text-xs text-slate-500">Dựa trên các bài Quiz đã làm</p>
        </article>

        <article class="rounded-[1.75rem] border border-white/80 bg-white/80 p-6 shadow-xl shadow-slate-900/5 backdrop-blur transition hover:-translate-y-0.5">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-[#991b1b]">Bài hoàn thành</p>
                <i data-lucide="book-open" class="h-5 w-5 text-[#991b1b]"></i>
            </div>
            <p class="mt-3 text-4xl font-black tracking-tight text-slate-950">
                <span x-text="completedLessonsCount">{{ $completedLessonsCount }}</span> / <span x-text="totalLessonsCount">{{ $totalLessonsCount }}</span>
            </p>
            <p class="mt-2 text-xs text-slate-500">Theo lộ trình bài học</p>
        </article>

        <article class="rounded-[1.75rem] border border-white/80 bg-white/80 p-6 shadow-xl shadow-slate-900/5 backdrop-blur transition hover:-translate-y-0.5">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-[#991b1b]">Tỉ lệ hoàn thành</p>
                <i data-lucide="trending-up" class="h-5 w-5 text-[#991b1b]"></i>
            </div>
            <p class="mt-3 text-4xl font-black tracking-tight text-slate-950">
                <span x-text="completionRate">{{ $completionRate }}</span>%
            </p>
            <p class="mt-2 text-xs text-slate-500">Tiến độ khóa học tổng thể</p>
        </article>
    </section>

    {{-- Interactive 7-Day Chart & Streak Banner --}}
    <section class="grid gap-6 xl:grid-cols-[1.15fr_0.85fr]">
        <div class="rounded-[2rem] border border-white/80 bg-white/80 p-6 shadow-xl shadow-slate-900/5 backdrop-blur">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-[#991b1b]">Biểu đồ tiến độ</p>
                    <h2 class="mt-2 text-2xl font-black tracking-tight text-slate-950 sm:text-3xl">Hoạt động 7 ngày gần nhất</h2>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('flashcards') }}" class="rounded-full bg-slate-100 px-4 py-2 text-xs font-bold text-slate-800 transition hover:bg-slate-200">
                        Ôn Flashcard
                    </a>
                    <a href="{{ route('quiz') }}" class="rounded-full bg-[#991b1b] px-4 py-2 text-xs font-bold text-white transition hover:bg-red-800">
                        Làm Quiz
                    </a>
                </div>
            </div>

            {{-- SVG Bar Chart with Hover Tooltip --}}
            <div class="mt-6 overflow-hidden rounded-[1.75rem] bg-slate-950 p-5 text-white">
                <svg viewBox="0 0 760 280" class="h-auto w-full" role="img" aria-label="Biểu đồ tiến độ học 7 ngày">
                    <defs>
                        <linearGradient id="normalGradient" x1="0%" x2="0%" y1="0%" y2="100%">
                            <stop offset="0%" stop-color="#fbbf24" />
                            <stop offset="100%" stop-color="#b45309" />
                        </linearGradient>
                        <linearGradient id="todayGradient" x1="0%" x2="0%" y1="0%" y2="100%">
                            <stop offset="0%" stop-color="#f87171" />
                            <stop offset="100%" stop-color="#991b1b" />
                        </linearGradient>
                    </defs>

                    <line x1="40" y1="220" x2="720" y2="220" stroke="rgba(255,255,255,0.15)" stroke-width="2" />

                    @foreach ($weeklyChart as $index => $point)
                        @php
                            $barWidth = 68;
                            $gap = 26;
                            $x = 48 + ($index * ($barWidth + $gap));
                            $barHeight = $point['sessions'] > 0 ? max(25, round(($point['sessions'] / $chartMax) * 135)) : 10;
                            $y = 220 - $barHeight;
                            $isToday = $point['is_today'] ?? false;
                        @endphp

                        <g class="cursor-pointer transition hover:opacity-80">
                            <rect x="{{ $x }}" y="{{ $y }}" width="{{ $barWidth }}" height="{{ $barHeight }}" rx="16"
                                  fill="{{ $isToday ? 'url(#todayGradient)' : 'url(#normalGradient)' }}" />
                            
                            {{-- Value on top of bar --}}
                            <text x="{{ $x + ($barWidth / 2) }}" y="{{ $y - 10 }}" fill="#f8fafc" font-size="16" text-anchor="middle" font-weight="700">
                                {{ $point['sessions'] }}
                            </text>
                            
                            {{-- Day Label --}}
                            <text x="{{ $x + ($barWidth / 2) }}" y="246" fill="{{ $isToday ? '#fbbf24' : 'rgba(255,255,255,0.85)' }}" font-size="14" text-anchor="middle" font-weight="{{ $isToday ? '700' : '400' }}">
                                {{ $point['label'] }}
                            </text>

                            {{-- Date / Score --}}
                            <text x="{{ $x + ($barWidth / 2) }}" y="264" fill="rgba(255,255,255,0.5)" font-size="11" text-anchor="middle">
                                {{ $point['date'] }}
                            </text>
                        </g>
                    @endforeach
                </svg>

                <div class="mt-3 flex items-center justify-between text-xs text-slate-400 px-2">
                    <span class="flex items-center gap-2">
                        <span class="h-2 w-2 rounded-full bg-amber-400"></span> Ngày thường
                        <span class="h-2 w-2 rounded-full bg-red-500 ml-2"></span> Hôm nay
                    </span>
                    <span>Số phiên học / ngày</span>
                </div>
            </div>
        </div>

        {{-- Streak & Quick Motivation --}}
        <div class="space-y-6">
            <div class="rounded-[2rem] bg-gradient-to-br from-[#991b1b] to-red-950 p-6 text-white shadow-2xl shadow-red-950/20">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-red-200/80">Kỷ luật học tập</p>
                    <span class="rounded-full bg-white/15 px-2.5 py-0.5 text-xs font-bold text-amber-300">Streak</span>
                </div>
                <h3 class="mt-3 text-4xl font-black tracking-tight flex items-center gap-2">
                    <span x-text="streakDays">{{ $streakDays }}</span> ngày liên tục <i data-lucide="flame" class="h-8 w-8 text-amber-400"></i>
                </h3>
                <p class="mt-3 text-sm text-white/80 leading-6">
                    Mỗi ngày làm ít nhất 1 bài quiz hoặc ôn thẻ từ vựng để giữ vững chuỗi học tập của bạn.
                </p>
                <div class="mt-5 h-2 rounded-full bg-white/15">
                    <div class="h-2 rounded-full bg-amber-300 transition-all duration-500"
                         :style="'width: ' + Math.min(100, Math.max(15, streakDays * 15)) + '%'"></div>
                </div>
            </div>

            {{-- Daily Breakdown Box --}}
            <div class="rounded-[2rem] border border-white/80 bg-white/80 p-6 shadow-xl shadow-slate-900/5 backdrop-blur">
                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-[#991b1b]">Tiến độ học hôm nay</p>
                <div class="mt-4 space-y-3">
                    <div class="flex items-center justify-between rounded-2xl bg-slate-50 p-3.5">
                        <span class="text-sm font-semibold text-slate-800">Thời gian tự học</span>
                        <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-900">
                            <span x-text="todayMinutes">{{ $todayMinutes }}</span>/20 phút
                        </span>
                    </div>
                    <div class="flex items-center justify-between rounded-2xl bg-slate-50 p-3.5">
                        <span class="text-sm font-semibold text-slate-800">Điểm Quiz trung bình</span>
                        <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-900">
                            {{ $averageScore }}%
                        </span>
                    </div>
                    <div class="flex items-center justify-between rounded-2xl bg-slate-50 p-3.5">
                        <span class="text-sm font-semibold text-slate-800">Bài học hoàn thành</span>
                        <span class="rounded-full bg-slate-950 px-3 py-1 text-xs font-bold text-white">
                            <span x-text="completedLessonsCount">{{ $completedLessonsCount }}</span> / <span x-text="totalLessonsCount">{{ $totalLessonsCount }}</span> bài
                        </span>
                    </div>
                    <a href="{{ route('flashcards', ['starred' => 1]) }}" class="flex items-center justify-between rounded-2xl bg-amber-50/70 border border-amber-200/60 p-3.5 transition hover:bg-amber-100/80 group">
                        <span class="text-sm font-semibold text-slate-800 flex items-center gap-2">
                            <i data-lucide="star" class="h-4 w-4 fill-current text-amber-500"></i>
                            <span>Sổ từ vựng đã lưu</span>
                        </span>
                        <span class="rounded-full bg-amber-200/80 px-3 py-1 text-xs font-bold text-amber-950 group-hover:bg-amber-300 transition">
                            {{ $starredCount }} từ
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- Interactive Curriculum / Lộ trình bài học đầy đủ --}}
    <section class="rounded-[2rem] border border-white/80 bg-white/80 p-6 shadow-xl shadow-slate-900/5 backdrop-blur sm:p-8">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between border-b border-slate-100 pb-6">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-[#991b1b]">Lộ trình đào tạo</p>
                <h2 class="mt-1 text-2xl font-black tracking-tight text-slate-950 sm:text-3xl">Danh sách bài học & Tiến độ</h2>
            </div>

            {{-- Filter Tabs --}}
            <div class="flex flex-wrap items-center gap-2">
                <button type="button"
                        @click="lessonFilter = 'all'"
                        class="rounded-full px-4 py-2 text-xs font-bold transition"
                        :class="lessonFilter === 'all' ? 'bg-[#991b1b] text-white shadow-md' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'">
                    Tất cả (<span x-text="lessons.length"></span>)
                </button>
                <button type="button"
                        @click="lessonFilter = 'in_progress'"
                        class="rounded-full px-4 py-2 text-xs font-bold transition"
                        :class="lessonFilter === 'in_progress' ? 'bg-[#991b1b] text-white shadow-md' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'">
                    Đang học (<span x-text="lessons.filter(l => l.status === 'in_progress').length"></span>)
                </button>
                <button type="button"
                        @click="lessonFilter = 'completed'"
                        class="rounded-full px-4 py-2 text-xs font-bold transition"
                        :class="lessonFilter === 'completed' ? 'bg-[#991b1b] text-white shadow-md' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'">
                    Đã xong (<span x-text="lessons.filter(l => l.status === 'completed').length"></span>)
                </button>
                <button type="button"
                        @click="lessonFilter = 'not_started'"
                        class="rounded-full px-4 py-2 text-xs font-bold transition"
                        :class="lessonFilter === 'not_started' ? 'bg-[#991b1b] text-white shadow-md' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'">
                    Chưa học (<span x-text="lessons.filter(l => l.status === 'not_started').length"></span>)
                </button>
            </div>
        </div>

        {{-- Quick Resume in-progress lesson if any --}}
        @if($inProgressLessons->isNotEmpty())
        @php $latestInProgress = $inProgressLessons->sortByDesc('last_accessed_at')->first(); @endphp
        <div class="mt-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 rounded-2xl border border-amber-200 bg-gradient-to-r from-amber-50 to-orange-50 p-4 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-amber-500 text-white shadow-md shadow-amber-500/20">
                    <i data-lucide="sparkles" class="h-5 w-5"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold uppercase tracking-wider text-amber-700">Đang học dở ({{ $latestInProgress['progress_percent'] }}%)</span>
                        <span class="text-xs text-slate-400">· Tiếp tục phiên học gần nhất</span>
                    </div>
                    <h4 class="font-bold text-slate-900 text-sm sm:text-base">{{ $latestInProgress['title'] }}</h4>
                </div>
            </div>
            <a href="{{ route('lesson.show', ['slug' => $latestInProgress['slug']]) }}"
                class="inline-flex shrink-0 items-center gap-2 rounded-xl bg-amber-600 px-4 py-2 text-xs sm:text-sm font-bold text-white shadow-sm hover:bg-amber-700 transition active:scale-95">
                Học tiếp ngay <i data-lucide="arrow-right" class="h-4 w-4"></i>
            </a>
        </div>
        @endif

        {{-- Lessons Grid --}}
        <div class="mt-6 grid gap-5 md:grid-cols-2">
            <template x-for="lesson in paginatedLessons" :key="lesson.id">
                <article class="relative flex flex-col justify-between rounded-3xl border border-slate-200/80 bg-white p-6 shadow-sm transition hover:shadow-md hover:border-amber-200">
                    <div>
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <span class="grid h-10 w-10 place-items-center rounded-2xl bg-slate-900 text-sm font-black text-white"
                                      :style="'background-color: ' + (lesson.accent_color || '#991b1b')">
                                    <span x-text="lesson.sort_order || '#'"></span>
                                </span>
                                <div>
                                    <h3 class="text-lg font-bold text-slate-950" x-text="lesson.title"></h3>
                                    <p class="text-xs text-slate-500">
                                        <span x-text="lesson.estimated_minutes"></span> phút •
                                        <span class="capitalize" x-text="getDifficultyLabel(lesson.difficulty)"></span> •
                                        <span x-text="lesson.questions_count"></span> câu quiz
                                    </p>
                                </div>
                            </div>

                            {{-- Status Pill --}}
                            <span class="rounded-full px-3 py-1 text-xs font-bold uppercase tracking-wider"
                                  :class="getStatusBadgeClass(lesson.status)">
                                <span x-text="getStatusLabel(lesson.status)"></span>
                            </span>
                        </div>

                        <p class="mt-3 text-sm text-slate-600 line-clamp-2" x-text="lesson.summary"></p>

                        {{-- Progress Bar --}}
                        <div class="mt-4">
                            <div class="flex items-center justify-between text-xs font-semibold text-slate-500 mb-1">
                                <span>Tiến độ bài</span>
                                <span class="font-bold text-slate-900" x-text="lesson.progress_percent + '%'"></span>
                            </div>
                            <div class="h-2 w-full overflow-hidden rounded-full bg-slate-100">
                                <div class="h-full rounded-full bg-gradient-to-r from-amber-400 to-[#991b1b] transition-all duration-500"
                                     :style="'width: ' + lesson.progress_percent + '%'"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Actions Row --}}
                    <div class="mt-6 flex flex-wrap items-center justify-between gap-2 border-t border-slate-100 pt-4">
                        <div class="flex items-center gap-2">
                            <a :href="'/lessons/' + lesson.slug"
                               class="inline-flex items-center gap-1.5 rounded-full border border-slate-200 bg-white px-3.5 py-2 text-xs font-bold text-slate-700 transition hover:bg-slate-50 active:scale-95 shadow-sm">
                                <i data-lucide="book-open" class="h-3.5 w-3.5"></i>
                                <span>Lý thuyết</span>
                            </a>
                            <a :href="'{{ route('quiz') }}?lesson=' + lesson.slug"
                               class="inline-flex items-center gap-1.5 rounded-full bg-[#991b1b] px-3.5 py-2 text-xs font-bold text-white transition hover:bg-red-800 active:scale-95 shadow-sm">
                                <i data-lucide="target" class="h-3.5 w-3.5"></i>
                                <span>Quiz</span>
                            </a>
                            <a :href="'{{ route('flashcards') }}?lesson=' + lesson.slug"
                               class="inline-flex items-center gap-1.5 rounded-full bg-amber-100 px-3 py-2 text-xs font-bold text-amber-900 transition hover:bg-amber-200 active:scale-95">
                                <i data-lucide="layers" class="h-3.5 w-3.5"></i>
                                <span>Thẻ</span>
                            </a>
                        </div>

                        {{-- Quick Progress Update Buttons --}}
                        <div class="flex items-center gap-1">
                            <button type="button"
                                    x-show="lesson.progress_percent < 100"
                                    @click="updateLessonProgress(lesson.id, 100)"
                                    :disabled="isUpdatingProgress === lesson.id"
                                    title="Đánh dấu hoàn thành bài học này"
                                    class="inline-flex items-center gap-1 rounded-full border border-slate-200 bg-slate-50 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-emerald-50 hover:text-emerald-700 hover:border-emerald-200 transition active:scale-95 disabled:opacity-50">
                                <i data-lucide="check" class="h-3.5 w-3.5"></i>
                                <span>Hoàn thành</span>
                            </button>
                            <button type="button"
                                    x-show="lesson.progress_percent === 100"
                                    @click="updateLessonProgress(lesson.id, 0)"
                                    :disabled="isUpdatingProgress === lesson.id"
                                    title="Học lại bài này từ đầu"
                                    class="inline-flex items-center gap-1 rounded-full border border-slate-200 bg-slate-50 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-amber-50 hover:text-amber-700 transition active:scale-95 disabled:opacity-50">
                                <i data-lucide="rotate-ccw" class="h-3.5 w-3.5"></i>
                                <span>Học lại</span>
                            </button>
                        </div>
                    </div>
                </article>
            </template>

            <div x-show="filteredLessons.length === 0" class="col-span-full flex flex-col items-center justify-center rounded-3xl border border-dashed border-slate-300 bg-slate-50/50 py-12 px-4 text-center">
                <div class="grid h-12 w-12 place-items-center rounded-2xl bg-slate-100 text-slate-400">
                    <i data-lucide="book-open" class="h-6 w-6"></i>
                </div>
                <p class="mt-3 text-sm font-bold text-slate-700">Không có bài học nào trong mục này</p>
                <p class="mt-1 text-xs text-slate-400">Hãy chọn tab khác hoặc bắt đầu học bài mới để theo dõi tiến độ.</p>
            </div>
        </div>

        {{-- Pagination for Dashboard Lessons --}}
        <div x-show="totalLessonPages > 1" class="mt-8 flex flex-col items-center justify-between gap-3 border-t border-slate-100 pt-6 sm:flex-row">
            <p class="text-xs text-slate-500">
                Hiển thị <span class="font-bold text-slate-800" x-text="(lessonPage - 1) * lessonsPerPage + 1"></span> –
                <span class="font-bold text-slate-800" x-text="Math.min(lessonPage * lessonsPerPage, filteredLessons.length)"></span>
                trong tổng số <span class="font-bold text-slate-800" x-text="filteredLessons.length"></span> bài học
            </p>

            <div class="flex items-center gap-1.5">
                <button type="button"
                        @click="prevLessonPage()"
                        :disabled="lessonPage <= 1"
                        class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-600 shadow-sm transition hover:bg-slate-50 disabled:opacity-40 disabled:cursor-not-allowed">
                    <i data-lucide="chevron-left" class="h-4 w-4"></i>
                </button>

                <template x-for="p in totalLessonPages" :key="p">
                    <button type="button"
                            @click="goToLessonPage(p)"
                            x-text="p"
                            class="inline-flex h-9 w-9 items-center justify-center rounded-xl text-xs font-bold transition shadow-sm"
                            :class="lessonPage === p ? 'bg-[#991b1b] text-white shadow-red-900/20' : 'border border-slate-200 bg-white text-slate-700 hover:border-[#991b1b] hover:text-[#991b1b]'">
                    </button>
                </template>

                <button type="button"
                        @click="nextLessonPage()"
                        :disabled="lessonPage >= totalLessonPages"
                        class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-600 shadow-sm transition hover:bg-slate-50 disabled:opacity-40 disabled:cursor-not-allowed">
                    <i data-lucide="chevron-right" class="h-4 w-4"></i>
                </button>
            </div>
        </div>
    </section>

    {{-- Recent Activity Feed & Shortcuts --}}
    <section class="grid gap-6 xl:grid-cols-[1.15fr_0.85fr]">
        {{-- Activity Stream --}}
        <div class="rounded-[2rem] border border-white/80 bg-white/80 p-6 shadow-xl shadow-slate-900/5 backdrop-blur sm:p-8">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-[#991b1b]">Nhật ký học tập</p>
                    <h2 class="mt-1 text-2xl font-black tracking-tight text-slate-950">Lịch sử hoạt động gần đây</h2>
                </div>
                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">
                    <span x-text="activities.length"></span> phiên
                </span>
            </div>

            <div class="mt-5 space-y-3">
                <template x-for="act in activities" :key="act.id || act.title">
                    <article class="flex items-center justify-between rounded-2xl bg-slate-50 p-4 transition hover:bg-white hover:shadow-sm border border-transparent hover:border-slate-200">
                        <div class="flex items-center gap-3">
                            <span class="grid h-10 w-10 shrink-0 place-items-center rounded-2xl"
                                  :class="act.type === 'quiz' ? 'bg-amber-100 text-amber-900' : (act.type === 'flashcard' ? 'bg-red-100 text-[#991b1b]' : 'bg-blue-100 text-blue-900')">
                                <i x-show="act.type === 'quiz'" data-lucide="target" class="h-5 w-5 text-amber-600"></i>
                                <i x-show="act.type === 'flashcard'" data-lucide="layers" class="h-5 w-5 text-[#991b1b]"></i>
                                <i x-show="act.type !== 'quiz' && act.type !== 'flashcard'" data-lucide="book-open" class="h-5 w-5 text-blue-600"></i>
                            </span>
                            <div>
                                <h4 class="text-sm font-bold text-slate-950" x-text="act.title"></h4>
                                <p class="text-xs text-slate-500" x-text="act.description"></p>
                            </div>
                        </div>

                        <div class="text-right">
                            <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-bold text-emerald-900" x-text="act.time"></span>
                        </div>
                    </article>
                </template>

                <div x-show="activities.length === 0" class="p-8 text-center text-sm text-slate-500">
                    Chưa có hoạt động nào được ghi nhận. Hãy làm một bài Quiz hoặc bấm ghi nhận buổi học!
                </div>
            </div>
        </div>

        {{-- Learning Tips & Quick Links --}}
        <div class="space-y-6">
            <div class="rounded-[2rem] bg-slate-950 p-6 text-white shadow-2xl shadow-slate-950/20">
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-amber-200/80">Phương pháp học nhanh</p>
                <h3 class="mt-2 text-2xl font-black">Flashcard + Quiz</h3>
                <p class="mt-3 text-sm text-slate-300 leading-relaxed">
                    1. Ôn nghĩa và phiên âm pinyin bằng Flashcard.<br>
                    2. Kiểm tra lại ngay bằng Quiz trắc nghiệm.<br>
                    3. Xem lời giải thích chi tiết cho các câu sai để nhớ sâu hơn.
                </p>
                <div class="mt-6 flex flex-wrap gap-3">
                    <a href="{{ route('flashcards') }}" class="inline-flex items-center justify-center rounded-full bg-amber-300 px-5 py-2.5 text-xs font-bold text-slate-950 transition hover:bg-amber-200">
                        Mở Flashcard
                    </a>
                    <a href="{{ route('quiz') }}" class="inline-flex items-center justify-center rounded-full bg-white/10 border border-white/20 px-5 py-2.5 text-xs font-bold text-white transition hover:bg-white/20">
                        Vào làm Quiz
                    </a>
                </div>
            </div>
        </div>
    </section>
</div>

{{-- Alpine.js Student Dashboard Logic (B7: defined before x-data DOM element) --}}
<script>
function studentDashboard() {
    return {
        todayMinutes: {{ $todayMinutes }},
        streakDays: {{ $streakDays }},
        completionRate: {{ $completionRate }},
        completedLessonsCount: {{ $completedLessonsCount }},
        totalLessonsCount: {{ $totalLessonsCount }},
        dailyGoal: 20,
        lessonFilter: 'all',
        lessonPage: 1,
        lessonsPerPage: 6,
        lessons: @js($lessons),
        activities: @js($activities),
        isLogging: false,
        isUpdatingProgress: null,
        toastMessage: null,
        toastTimeout: null,

        initDashboard() {
            // Refresh icons whenever lessonFilter or lessonPage changes
            this.$watch('lessonFilter', () => {
                this.lessonPage = 1;
                this.$nextTick(() => window.refreshIcons?.());
            });
            this.$watch('lessonPage', () => {
                this.$nextTick(() => window.refreshIcons?.());
            });
        },

        get todayGoalPercent() {
            return Math.min(100, Math.round((this.todayMinutes / this.dailyGoal) * 100));
        },

        get filteredLessons() {
            if (this.lessonFilter === 'all') return this.lessons;
            return this.lessons.filter(l => l.status === this.lessonFilter);
        },

        get totalLessonPages() {
            return Math.max(1, Math.ceil(this.filteredLessons.length / this.lessonsPerPage));
        },

        get paginatedLessons() {
            const start = (this.lessonPage - 1) * this.lessonsPerPage;
            return this.filteredLessons.slice(start, start + this.lessonsPerPage);
        },

        prevLessonPage() {
            if (this.lessonPage > 1) {
                this.lessonPage--;
            }
        },

        nextLessonPage() {
            if (this.lessonPage < this.totalLessonPages) {
                this.lessonPage++;
            }
        },

        goToLessonPage(p) {
            this.lessonPage = p;
        },

        showToast(msg) {
            this.toastMessage = msg;
            if (this.toastTimeout) clearTimeout(this.toastTimeout);
            this.toastTimeout = setTimeout(() => {
                this.toastMessage = null;
            }, 3500);
        },

        getDifficultyLabel(diff) {
            const map = { 'starter': 'Mới bắt đầu', 'intermediate': 'Trung bình', 'advanced': 'Nâng cao' };
            return map[diff] || diff;
        },

        getStatusLabel(status) {
            const map = { 'completed': 'Đã xong', 'in_progress': 'Đang học', 'not_started': 'Chưa học' };
            return map[status] || status;
        },

        getStatusBadgeClass(status) {
            const map = {
                'completed': 'bg-emerald-100 text-emerald-900 border border-emerald-200',
                'in_progress': 'bg-amber-100 text-amber-900 border border-amber-200',
                'not_started': 'bg-slate-100 text-slate-600 border border-slate-200'
            };
            return map[status] || 'bg-slate-100 text-slate-600';
        },

        async quickLogSession(minutes, sessionType, notes) {
            this.isLogging = true;
            try {
                const response = await fetch("{{ route('student.session.log') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({ duration_minutes: minutes, session_type: sessionType })
                });

                if (!response.ok) throw new Error("Ghi nhận thất bại");

                const data = await response.json();
                this.todayMinutes = data.today_minutes;
                this.streakDays = data.streak_days;

                if (data.new_activity) {
                    this.activities.unshift(data.new_activity);
                    setTimeout(() => window.refreshIcons?.(), 50);
                }

                this.showToast(data.message);
            } catch (e) {
                // D1: Use toast instead of alert()
                this.showToast('Đã xảy ra lỗi khi ghi nhận buổi học. Vui lòng thử lại!');
                console.error(e);
            } finally {
                this.isLogging = false;
            }
        },

        async updateLessonProgress(lessonId, newPercent) {
            this.isUpdatingProgress = lessonId;
            try {
                const response = await fetch("{{ route('student.progress.update') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({ lesson_id: lessonId, progress_percent: newPercent })
                });

                if (!response.ok) throw new Error("Cập nhật thất bại");

                const data = await response.json();

                const target = this.lessons.find(l => l.id === lessonId);
                if (target) {
                    target.progress_percent = data.progress_percent;
                    target.status = data.status;
                }

                this.completedLessonsCount = data.completed_count;
                this.completionRate = data.completion_rate;

                this.showToast(data.message);
            } catch (e) {
                // D1: Use toast instead of alert()
                this.showToast('Đã xảy ra lỗi khi cập nhật tiến độ. Vui lòng thử lại!');
                console.error(e);
            } finally {
                this.isUpdatingProgress = null;
            }
        }
    };
}
</script>
@endsection