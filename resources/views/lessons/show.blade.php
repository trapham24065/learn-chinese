@extends('layouts.app')

@section('title', $lesson->title . ' | Chinese Deck')

@section('content')
<div x-data="lessonReader({
        lessonId: {{ $lesson->id }},
        initialPercent: {{ $progress?->progress_percent ?? 20 }},
        initialStatus: '{{ $progress?->status ?? 'in_progress' }}'
    })"
    x-init="init()"
    class="space-y-8">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm font-medium text-slate-500">
        <a href="{{ route('dashboard') }}" class="hover:text-slate-900 transition">Dashboard</a>
        <i data-lucide="chevron-right" class="h-4 w-4"></i>
        @if ($lesson->hsk_level)
            <a href="{{ route('hsk.show', $lesson->hsk_level) }}" class="hover:text-slate-900 transition">HSK {{ $lesson->hsk_level }}</a>
            <i data-lucide="chevron-right" class="h-4 w-4"></i>
        @endif
        <span class="text-slate-900 truncate">{{ $lesson->title }}</span>
    </nav>

    {{-- Hero Section with Live Progress --}}
    <section class="relative overflow-hidden rounded-[2.5rem] bg-slate-950 px-6 py-10 text-white sm:px-12 lg:py-14 shadow-2xl shadow-slate-950/20">
        <div class="absolute inset-x-0 top-0 h-2" style="background: {{ $lesson->accent_color ?? '#991b1b' }}"></div>
        
        <div class="relative z-10 flex flex-col items-start lg:flex-row lg:justify-between lg:gap-12">
            <div class="max-w-2xl flex-1">
                <div class="flex flex-wrap items-center gap-3">
                    <span class="inline-flex items-center rounded-full bg-white/10 px-3 py-1 text-xs font-semibold uppercase tracking-widest text-slate-300">
                        {{ $lesson->hsk_level ? 'HSK ' . $lesson->hsk_level : 'Bài học' }}
                    </span>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-white/10 px-3 py-1 text-xs font-semibold text-slate-300">
                        <i data-lucide="timer" class="h-3.5 w-3.5"></i> {{ $lesson->estimated_minutes }} phút
                    </span>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-white/10 px-3 py-1 text-xs font-semibold text-slate-300">
                        <i data-lucide="bar-chart-2" class="h-3.5 w-3.5"></i> 
                        {{ $lesson->difficulty === 'starter' ? 'Mới bắt đầu' : ($lesson->difficulty === 'intermediate' ? 'Trung bình' : 'Nâng cao') }}
                    </span>

                    {{-- Dynamic status badge --}}
                    <span x-show="status === 'completed'"
                          class="inline-flex items-center gap-1 rounded-full bg-emerald-500/20 px-3 py-1 text-xs font-bold text-emerald-300 border border-emerald-500/30">
                        <i data-lucide="circle-check-big" class="h-3.5 w-3.5"></i> Đã hoàn thành
                    </span>
                    <span x-show="status === 'in_progress'"
                          class="inline-flex items-center gap-1 rounded-full bg-amber-500/20 px-3 py-1 text-xs font-bold text-amber-300 border border-amber-500/30">
                        <i data-lucide="timer" class="h-3.5 w-3.5"></i> Đang học dở (<span x-text="percent + '%'"></span>)
                    </span>
                </div>
                
                <h1 class="mt-5 text-3xl font-black tracking-tight sm:text-4xl lg:text-5xl text-white">
                    {{ $lesson->title }}
                </h1>
                
                <p class="mt-3 text-base sm:text-lg leading-relaxed text-slate-300">
                    {{ $lesson->summary }}
                </p>

                {{-- Reading Progress Bar --}}
                <div class="mt-6 max-w-md rounded-2xl bg-white/5 p-3.5 border border-white/10">
                    <div class="flex items-center justify-between text-xs font-semibold text-slate-300 mb-1.5">
                        <span class="flex items-center gap-1.5">
                            <i data-lucide="bookmark" class="h-3.5 w-3.5 text-amber-400"></i>
                            Tiến độ lý thuyết
                        </span>
                        <span class="font-bold text-white" x-text="percent + '%'"></span>
                    </div>
                    <div class="h-2 w-full overflow-hidden rounded-full bg-white/10">
                        <div class="h-full rounded-full bg-gradient-to-r from-amber-400 to-emerald-400 transition-all duration-300"
                             :style="'width: ' + percent + '%'"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Resume Previous Position Banner --}}
    <div x-show="hasSavedScroll"
         x-transition
         class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 rounded-2xl border border-amber-200 bg-gradient-to-r from-amber-50 to-orange-50 p-4 text-amber-900 shadow-sm">
        <div class="flex items-center gap-3">
            <div class="grid h-9 w-9 place-items-center rounded-xl bg-amber-500 text-white shadow-sm shrink-0">
                <i data-lucide="bookmark" class="h-4 w-4"></i>
            </div>
            <div>
                <p class="text-xs uppercase font-bold tracking-wider text-amber-700">Học tiếp đoạn dang dở</p>
                <p class="text-sm font-semibold text-slate-800">
                    Hệ thống đã ghi nhớ vị trí bạn đang đọc lần trước (Tiến độ: <strong x-text="percent + '%'"></strong>).
                </p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <button type="button"
                    @click="scrollToSavedPosition()"
                    class="inline-flex items-center gap-1.5 rounded-xl bg-amber-600 px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-amber-700 transition active:scale-95">
                <span>Cuộn đến vị trí học trước</span>
                <i data-lucide="arrow-down" class="h-3.5 w-3.5"></i>
            </button>
            <button type="button"
                    @click="dismissSavedPrompt()"
                    class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition">
                Bỏ qua
            </button>
        </div>
    </div>

    {{-- Main Content & Actions --}}
    <div class="grid gap-8 lg:grid-cols-[1fr_320px]">
        
        {{-- Lesson Content --}}
        <div class="space-y-8">
            <section id="lesson-content" class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-xl shadow-slate-900/5 sm:p-10">
                <div class="prose prose-slate prose-lg max-w-none">
                    @if ($lesson->content)
                        {!! $lesson->content !!}
                    @else
                        <div class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-slate-300 py-16 text-center">
                            <i data-lucide="book-open" class="mx-auto h-10 w-10 text-slate-300"></i>
                            <p class="mt-4 text-lg font-bold text-slate-700">Chưa có nội dung chi tiết</p>
                            <p class="mt-2 text-slate-500 max-w-sm">Bài học này hiện chỉ có Flashcard và Quiz. Admin sẽ cập nhật nội dung ngữ pháp sau.</p>
                        </div>
                    @endif
                </div>

                {{-- Completion Checkpoint Card --}}
                <div class="mt-12 rounded-3xl border border-slate-200 bg-gradient-to-b from-slate-50 to-white p-6 sm:p-8 text-center shadow-sm">
                    <div class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-emerald-100 text-emerald-600 shadow-sm">
                        <i data-lucide="circle-check-big" class="h-7 w-7"></i>
                    </div>
                    <h3 class="mt-4 text-xl font-bold text-slate-950">Hoàn thành bài đọc lý thuyết</h3>
                    <p class="mt-2 text-sm text-slate-600 max-w-md mx-auto">
                        Bạn đã nắm được phần lý thuyết và bài khóa của bài học này. Hãy đánh dấu hoàn thành để ghi nhận vào Dashboard và chuyển sang làm Quiz luyện tập!
                    </p>
                    <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
                        <button type="button"
                                @click="markComplete()"
                                :disabled="isSaving"
                                class="inline-flex items-center gap-2 rounded-full px-6 py-3 text-sm font-bold text-white shadow-lg transition active:scale-95 disabled:opacity-50"
                                :class="status === 'completed' ? 'bg-slate-700 hover:bg-slate-800' : 'bg-emerald-600 hover:bg-emerald-700 shadow-emerald-900/20'">
                            <i data-lucide="check" class="h-4 w-4"></i>
                            <span x-text="status === 'completed' ? 'Đã hoàn thành 100% (Bấm để học lại)' : 'Đánh dấu hoàn thành lý thuyết (100%)'"></span>
                        </button>
                        <a href="{{ route('quiz', ['lesson' => $lesson->slug]) }}" 
                           class="inline-flex items-center gap-2 rounded-full bg-[#991b1b] px-6 py-3 text-sm font-bold text-white shadow-lg shadow-red-950/20 hover:bg-red-800 transition active:scale-95">
                            <i data-lucide="target" class="h-4 w-4"></i>
                            <span>Làm Quiz ngay</span>
                        </a>
                    </div>
                </div>
            </section>
        </div>

        {{-- Sidebar Actions --}}
        <div class="space-y-6">
            
            {{-- Quick Progress Card in Sidebar --}}
            <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-xl shadow-slate-900/5">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Tiến độ bài</span>
                    <span class="text-sm font-black text-slate-900" x-text="percent + '%'"></span>
                </div>
                <div class="h-2 w-full overflow-hidden rounded-full bg-slate-100 mb-4">
                    <div class="h-full rounded-full bg-gradient-to-r from-amber-400 to-[#991b1b] transition-all duration-300"
                         :style="'width: ' + percent + '%'"></div>
                </div>
                <button type="button"
                        @click="markComplete()"
                        :disabled="isSaving"
                        class="w-full inline-flex items-center justify-center gap-1.5 rounded-full border border-slate-200 bg-slate-50 py-2.5 text-xs font-bold text-slate-700 transition hover:bg-emerald-50 hover:text-emerald-700 hover:border-emerald-200 disabled:opacity-50">
                    <i data-lucide="check" class="h-3.5 w-3.5"></i>
                    <span x-text="status === 'completed' ? 'Đặt lại tiến độ (Học lại)' : 'Đánh dấu đã xong'"></span>
                </button>
            </div>

            {{-- Flashcard Action --}}
            <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-xl shadow-slate-900/5 transition hover:-translate-y-1 hover:shadow-2xl hover:shadow-slate-900/10">
                <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-100 text-amber-600">
                    <i data-lucide="layers" class="h-6 w-6"></i>
                </div>
                <h3 class="text-xl font-black text-slate-900">Flashcard</h3>
                <p class="mt-2 text-sm text-slate-600">
                    {{ $lesson->flashcards_count }} thẻ vựng
                </p>
                <div class="mt-4 space-y-3">
                    <a href="{{ route('flashcards', ['lesson' => $lesson->slug]) }}" 
                       class="flex w-full items-center justify-center gap-2 rounded-full bg-slate-950 px-4 py-3 text-sm font-bold text-white transition hover:bg-slate-800">
                        Học Flashcard <i data-lucide="arrow-right" class="h-4 w-4"></i>
                    </a>
                </div>
            </div>

            {{-- Quiz Action --}}
            <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-xl shadow-slate-900/5 transition hover:-translate-y-1 hover:shadow-2xl hover:shadow-slate-900/10">
                <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-2xl text-white" style="background: {{ $lesson->accent_color ?? '#991b1b' }}">
                    <i data-lucide="target" class="h-6 w-6"></i>
                </div>
                <h3 class="text-xl font-black text-slate-900">Quiz Test</h3>
                <p class="mt-2 text-sm text-slate-600">
                    {{ $lesson->questions_count }} câu hỏi trắc nghiệm
                </p>
                <div class="mt-4 space-y-3">
                    <a href="{{ route('quiz', ['lesson' => $lesson->slug]) }}" 
                       class="flex w-full items-center justify-center gap-2 rounded-full px-4 py-3 text-sm font-bold text-white transition hover:opacity-90"
                       style="background: {{ $lesson->accent_color ?? '#991b1b' }}">
                        Làm Quiz <i data-lucide="arrow-right" class="h-4 w-4"></i>
                    </a>
                </div>
            </div>
            
        </div>
    </div>
</div>

<script>
function lessonReader(config) {
    return {
        lessonId: config.lessonId,
        percent: config.initialPercent || 20,
        status: config.initialStatus || 'in_progress',
        hasSavedScroll: false,
        savedScrollY: 0,
        isSaving: false,
        scrollTimer: null,

        init() {
            const key = 'learn_chinese_lesson_scroll_' + this.lessonId;
            const saved = localStorage.getItem(key);
            if (saved) {
                const y = parseInt(saved, 10);
                if (y > 350) {
                    this.savedScrollY = y;
                    this.hasSavedScroll = true;
                }
            }

            // Listen to scroll to track reading depth
            window.addEventListener('scroll', () => {
                this.handleScroll();
            }, { passive: true });
        },

        handleScroll() {
            // Save scroll pos locally
            const key = 'learn_chinese_lesson_scroll_' + this.lessonId;
            if (window.scrollY > 150) {
                localStorage.setItem(key, Math.round(window.scrollY));
            }

            if (this.status === 'completed') return;

            // Calculate reading depth in #lesson-content
            const el = document.getElementById('lesson-content');
            if (!el) return;

            const rect = el.getBoundingClientRect();
            const totalHeight = rect.height;
            const scrolledPast = window.innerHeight - rect.top;

            if (totalHeight > 0) {
                const ratio = Math.min(1, Math.max(0, scrolledPast / totalHeight));
                const calcPercent = Math.round(20 + (ratio * 70)); // 20% to 90%
                if (calcPercent > this.percent) {
                    this.percent = calcPercent;

                    // Debounce API sync
                    if (this.scrollTimer) clearTimeout(this.scrollTimer);
                    this.scrollTimer = setTimeout(() => {
                        this.syncProgress(this.percent);
                    }, 2000);
                }
            }
        },

        scrollToSavedPosition() {
            window.scrollTo({
                top: this.savedScrollY,
                behavior: 'smooth'
            });
            this.hasSavedScroll = false;
        },

        dismissSavedPrompt() {
            this.hasSavedScroll = false;
        },

        async markComplete() {
            const nextPercent = this.status === 'completed' ? 20 : 100;
            await this.syncProgress(nextPercent);
        },

        async syncProgress(targetPercent) {
            this.isSaving = true;
            try {
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                const res = await fetch("{{ route('student.progress.update') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json",
                        "X-CSRF-TOKEN": token || ""
                    },
                    body: JSON.stringify({
                        lesson_id: this.lessonId,
                        progress_percent: targetPercent
                    })
                });

                if (res.ok) {
                    const data = await res.json();
                    this.percent = data.progress_percent;
                    this.status = data.status;
                    this.$nextTick?.(() => window.refreshIcons?.());
                }
            } catch (err) {
                console.error("Lỗi cập nhật tiến độ:", err);
            } finally {
                this.isSaving = false;
            }
        }
    };
}
</script>
@endsection

