@extends('layouts.app')

@section('title', 'Luyện tập nhanh theo bài học | Chinese Deck')

@section('content')
<div x-data="quizApp()" x-init="initQuiz()" class="space-y-8">
    {{-- Header & Stats Summary --}}
    <section class="grid gap-6 py-2 lg:grid-cols-[1fr_0.9fr] lg:py-4">
        <div>
            <div class="flex flex-wrap items-center gap-2">
                <span class="inline-flex items-center rounded-full bg-red-100 px-3 py-1 text-xs font-bold uppercase tracking-[0.24em] text-[#991b1b]">
                    Luyện tập nhanh
                </span>
                @if ($selectedLesson)
                    <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-900">
                        {{ $selectedLesson->title }}
                    </span>
                @endif
                <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                    <i data-lucide="shuffle" class="h-3 w-3"></i>
                    {{ count($questions) }} câu ngẫu nhiên / {{ $totalPoolCount }} câu
                </span>
            </div>

            <h1 class="mt-3 text-4xl font-black tracking-tight text-slate-950 sm:text-5xl">
                Luyện tập trắc nghiệm bài học
            </h1>
            <p class="mt-3 max-w-2xl text-base leading-7 text-slate-700 sm:text-lg">
                Làm nhanh 5–10 câu hỏi ngắn để củng cố ngay từ vựng, pinyin, chữ Hán và ngữ nghĩa vừa học theo từng chủ đề bài học.
            </p>
            <a href="{{ request()->fullUrl() }}"
               class="mt-4 inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-[#991b1b] hover:text-[#991b1b]">
                <i data-lucide="refresh-cw" class="h-3.5 w-3.5"></i>
                Đổi bộ câu hỏi mới
            </a>
        </div>

        {{-- Dynamic Live Stats Panel --}}
        <div class="rounded-[2rem] bg-slate-950 p-6 text-white shadow-2xl shadow-slate-950/20">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-amber-200/80">Tiến độ bài làm</p>
                <span x-show="!isSubmitted" class="text-xs text-slate-400">
                    <span x-text="answeredCount">0</span> / {{ count($questions) }} câu đã chọn
                </span>
                <span x-show="isSubmitted" class="rounded-full bg-emerald-500/20 px-3 py-0.5 text-xs font-bold text-emerald-300">
                    Đã nộp bài
                </span>
            </div>

            <div class="mt-4 grid grid-cols-3 gap-3">
                <div class="rounded-2xl border border-white/10 bg-white/5 p-3 text-center sm:p-4">
                    <p class="text-xs text-slate-400">Tổng câu</p>
                    <p class="mt-1 text-2xl font-black sm:text-3xl">{{ count($questions) }}</p>
                </div>
                <div class="rounded-2xl border border-white/10 bg-white/5 p-3 text-center sm:p-4">
                    <p class="text-xs text-slate-400" x-text="isSubmitted ? 'Đúng' : 'Đã làm'"></p>
                    <p class="mt-1 text-2xl font-black sm:text-3xl text-amber-300">
                        <span x-text="isSubmitted ? (results?.correct_count ?? 0) : answeredCount">0</span>
                    </p>
                </div>
                <div class="rounded-2xl border border-white/10 bg-white/5 p-3 text-center sm:p-4">
                    <p class="text-xs text-slate-400">Điểm số</p>
                    <p class="mt-1 text-2xl font-black sm:text-3xl text-emerald-400">
                        <span x-text="isSubmitted ? (results?.score + '%') : '--'">--</span>
                    </p>
                </div>
            </div>

            <div class="mt-4">
                <div class="flex items-center justify-between text-xs text-slate-400 mb-1.5">
                    <span>Mức độ hoàn thành</span>
                    <span x-text="progressPercentage + '%'">0%</span>
                </div>
                <div class="h-2 w-full overflow-hidden rounded-full bg-white/10">
                    <div class="h-full bg-gradient-to-r from-amber-400 to-[#991b1b] transition-all duration-300"
                         :style="'width: ' + progressPercentage + '%'"></div>
                </div>
            </div>
        </div>
    </section>

    {{-- Lesson Filter Pills --}}
    <section class="flex flex-wrap items-center gap-2 border-b border-slate-200/80 pb-4">
        <span class="mr-2 text-xs font-bold uppercase tracking-wider text-slate-500">Chủ đề:</span>
        <a href="{{ route('quiz') }}"
           class="inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-semibold transition {{ $selectedLessonSlug === 'all' ? 'bg-[#991b1b] text-white shadow-md shadow-red-950/15' : 'bg-white/80 text-slate-700 hover:bg-white hover:text-[#991b1b] border border-slate-200' }}">
            <span>Tất cả</span>
            <span class="rounded-full px-2 py-0.5 text-xs {{ $selectedLessonSlug === 'all' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600' }}">
                {{ $totalActiveQuestions }}
            </span>
        </a>

        @foreach ($lessons as $lesson)
            <a href="{{ route('quiz', ['lesson' => $lesson->slug]) }}"
               class="inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-semibold transition {{ $selectedLessonSlug === $lesson->slug ? 'bg-[#991b1b] text-white shadow-md shadow-red-950/15' : 'bg-white/80 text-slate-700 hover:bg-white hover:text-[#991b1b] border border-slate-200' }}">
                <span>{{ $lesson->title }}</span>
                <span class="rounded-full px-2 py-0.5 text-xs {{ $selectedLessonSlug === $lesson->slug ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600' }}">
                    {{ $lesson->questions_count }}
                </span>
            </a>
        @endforeach
    </section>

    {{-- Result Banner when Submitted --}}
    <section x-show="isSubmitted" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="rounded-[2rem] bg-gradient-to-br from-slate-950 via-slate-900 to-[#991b1b] p-6 text-white shadow-2xl shadow-red-950/20 sm:p-8">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 rounded-full bg-amber-400/20 px-3 py-1 text-xs font-bold uppercase tracking-wider text-amber-200">
                    <span>Kết quả bài làm</span>
                </div>
                <h2 class="text-3xl font-black tracking-tight sm:text-4xl" x-text="results?.message">
                    Hoàn thành bài kiểm tra!
                </h2>
                <p class="text-sm text-slate-300 sm:text-base">
                    Bạn đã trả lời đúng <strong class="text-amber-300 font-bold" x-text="results?.correct_count">0</strong> /
                    <span x-text="results?.total_questions">0</span> câu hỏi
                    (Đạt <strong class="text-emerald-400 font-bold" x-text="results?.score + '%'">0%</strong>).
                    @auth
                        Kết quả đã được cập nhật vào tiến độ học tập của bạn!
                    @endauth
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <button type="button" @click="resetQuiz()" class="inline-flex items-center gap-2 rounded-full bg-white px-5 py-3 text-sm font-bold text-slate-950 shadow-lg transition hover:bg-amber-100 hover:scale-105 active:scale-95">
                    <i data-lucide="rotate-ccw" class="h-4 w-4"></i>
                    <span>Làm lại bài này</span>
                </button>
                <a href="{{ route('flashcards') }}" class="inline-flex items-center gap-2 rounded-full bg-amber-300 px-5 py-3 text-sm font-bold text-slate-950 shadow-lg transition hover:bg-amber-200 hover:scale-105 active:scale-95">
                    <i data-lucide="layers" class="h-4 w-4"></i>
                    <span>Ôn Flashcard</span>
                </a>
            </div>
        </div>
    </section>

    @if (count($questions) > 0)
        {{-- Questions List --}}
        <div class="space-y-6">
            @foreach ($questions as $index => $question)
                <article id="question-card-{{ $question->id }}"
                         class="group relative overflow-hidden rounded-[2rem] border bg-white p-6 shadow-xl shadow-slate-900/5 transition sm:p-8"
                         :class="{
                             'border-emerald-300 ring-2 ring-emerald-400/30': isSubmitted && results?.details?.[{{ $question->id }}]?.is_correct,
                             'border-rose-300 ring-2 ring-rose-400/30': isSubmitted && !results?.details?.[{{ $question->id }}]?.is_correct,
                             'border-white/80 hover:border-amber-200': !isSubmitted
                         }">
                    
                    {{-- Card Header --}}
                    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 pb-4">
                        <div class="flex items-center gap-3">
                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-slate-900 text-xs font-black text-white">
                                {{ $index + 1 }}
                            </span>
                            <div>
                                <span class="text-xs font-bold uppercase tracking-wider text-[#991b1b]">Câu {{ $index + 1 }}</span>
                                @if ($question->lesson)
                                    <span class="ml-2 text-xs text-slate-400">• {{ $question->lesson->title }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            @if ($question->pinyin)
                                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600">
                                    {{ $question->pinyin }}
                                </span>
                            @endif

                            {{-- Status Badge on Submit --}}
                            <template x-if="isSubmitted">
                                <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-bold"
                                      :class="results?.details?.[{{ $question->id }}]?.is_correct ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800'">
                                    <span x-text="results?.details?.[{{ $question->id }}]?.is_correct ? '✓ Chính xác' : '✗ Chưa đúng'"></span>
                                </span>
                            </template>
                        </div>
                    </div>

                    {{-- Question Content --}}
                    <div class="mt-5">
                        <h2 class="text-xl font-bold text-slate-950 sm:text-2xl">
                            {{ $question->question }}
                        </h2>
                    </div>

                    {{-- Options List --}}
                    <div class="mt-6 grid gap-3 sm:grid-cols-2">
                        @foreach ($question->options as $option)
                            <button type="button"
                                    @click="selectOption({{ $question->id }}, @js($option))"
                                    :disabled="isSubmitted"
                                    class="relative flex items-center justify-between rounded-2xl border p-4 text-left font-medium transition"
                                    :class="getOptionClass({{ $question->id }}, @js($option))">
                                <span class="flex items-center gap-3">
                                    <span class="inline-flex h-6 w-6 shrink-0 items-center justify-center rounded-full border text-xs font-bold transition"
                                          :class="getOptionRadioClass({{ $question->id }}, @js($option))">
                                        <span x-show="isOptionSelected({{ $question->id }}, @js($option))" class="h-2 w-2 rounded-full bg-current"></span>
                                    </span>
                                    <span class="text-sm sm:text-base" :class="getOptionTextClass({{ $question->id }}, @js($option))">
                                        {{ $option }}
                                    </span>
                                </span>

                                {{-- Icon indicator after submit --}}
                                <template x-if="isSubmitted">
                                    <span>
                                        <template x-if="isCorrectOption({{ $question->id }}, @js($option))">
                                            <i data-lucide="circle-check-big" class="h-5 w-5 text-emerald-600"></i>
                                        </template>
                                        <template x-if="isUserWrongOption({{ $question->id }}, @js($option))">
                                            <i data-lucide="circle-x" class="h-5 w-5 text-rose-600"></i>
                                        </template>
                                    </span>
                                </template>
                            </button>
                        @endforeach
                    </div>

                    {{-- Explanation Box (Revealed after Submit) --}}
                    <div x-show="isSubmitted"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="mt-6 rounded-2xl border p-4 sm:p-5"
                         :class="results?.details?.[{{ $question->id }}]?.is_correct ? 'bg-emerald-50/70 border-emerald-200 text-emerald-950' : 'bg-amber-50/70 border-amber-200 text-amber-950'">
                        <div class="flex items-start gap-3">
                            <div class="grid h-8 w-8 shrink-0 place-items-center rounded-xl bg-amber-100 text-amber-800">
                                <i data-lucide="lightbulb" class="h-4 w-4"></i>
                            </div>
                            <div class="space-y-1">
                                <p class="text-xs font-bold uppercase tracking-wider text-slate-600">
                                    Giải thích chi tiết:
                                </p>
                                <p class="text-sm font-semibold">
                                    Đáp án đúng: <span class="text-emerald-700 font-bold">{{ $question->correct_answer }}</span>
                                </p>
                                @if ($question->explanation)
                                    <p class="text-sm text-slate-700 leading-relaxed pt-1">
                                        {{ $question->explanation }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        {{-- Bottom Submission Bar --}}
        <section class="sticky bottom-6 z-30 rounded-[2rem] border border-white/60 bg-white/90 p-4 shadow-2xl shadow-slate-900/10 backdrop-blur-lg sm:p-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-[#991b1b]">Trạng thái bài làm</p>
                    <p class="text-sm text-slate-700">
                        <span x-show="!isSubmitted">
                            Đã chọn <strong class="font-bold text-slate-950" x-text="answeredCount">0</strong> / {{ count($questions) }} câu hỏi.
                            <span x-show="answeredCount < {{ count($questions) }}" class="text-amber-700 font-medium">
                                (Còn <span x-text="{{ count($questions) }} - answeredCount"></span> câu chưa chọn)
                            </span>
                        </span>
                        <span x-show="isSubmitted">
                            Hoàn thành với điểm số <strong class="font-bold text-emerald-600" x-text="results?.score + '%'"></strong>.
                        </span>
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <template x-if="!isSubmitted">
                        <button type="button"
                                @click="submitQuiz()"
                                :disabled="isSubmitting || answeredCount === 0"
                                class="inline-flex w-full items-center justify-center gap-2 rounded-full bg-[#991b1b] px-8 py-3.5 text-base font-bold text-white shadow-xl shadow-red-950/20 transition hover:bg-red-800 hover:scale-105 active:scale-95 disabled:cursor-not-allowed disabled:opacity-50 sm:w-auto">
                            <svg x-show="isSubmitting" class="h-5 w-5 animate-spin text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                            <span x-text="isSubmitting ? 'Đang chấm điểm...' : 'Nộp bài & Chấm điểm'"></span>
                            <i x-show="!isSubmitting" data-lucide="target" class="h-5 w-5"></i>
                        </button>
                    </template>

                    <template x-if="isSubmitted">
                        <div class="flex items-center gap-2">
                            <button type="button"
                                    @click="resetQuiz()"
                                    class="inline-flex items-center justify-center gap-2 rounded-full border border-slate-300 bg-white px-6 py-3 text-sm font-bold text-slate-800 shadow transition hover:bg-slate-50">
                                <i data-lucide="rotate-ccw" class="h-4 w-4"></i>
                                <span>Làm lại</span>
                            </button>
                            <a href="{{ route('dashboard') }}"
                               class="inline-flex items-center justify-center gap-2 rounded-full bg-slate-950 px-6 py-3 text-sm font-bold text-white shadow transition hover:bg-slate-800">
                                <i data-lucide="layout-dashboard" class="h-4 w-4"></i>
                                <span>Về Dashboard</span>
                            </a>
                        </div>
                    </template>
                </div>
            </div>
        </section>

        {{-- New set hint --}}
        <p class="text-center text-xs text-slate-400">
            Bộ {{ count($questions) }} câu được chọn ngẫu nhiên từ {{ $totalPoolCount }} câu.
            <a href="{{ request()->fullUrl() }}" class="font-semibold text-[#991b1b] hover:underline inline-flex items-center gap-1">
                <span>Tải bộ câu mới</span>
                <i data-lucide="arrow-right" class="h-3 w-3 inline"></i>
            </a>
        </p>
    @else
        {{-- Empty State --}}
        <section class="flex flex-col items-center justify-center rounded-[2.5rem] border border-dashed border-slate-300 bg-white/60 p-12 text-center shadow-sm backdrop-blur sm:p-16">
            <div class="grid h-16 w-16 place-items-center rounded-3xl bg-amber-50 text-amber-600">
                <i data-lucide="circle-help" class="h-8 w-8"></i>
            </div>
            <h2 class="mt-4 text-2xl font-bold text-slate-900">Chưa có câu hỏi nào cho chủ đề này</h2>
            <p class="mt-2 max-w-md text-sm text-slate-500">
                Hiện tại chưa có câu hỏi quiz nào được tạo cho bài học này. Bạn có thể chọn chủ đề khác hoặc luyện tập từ vựng bằng flashcard trước.
            </p>
            <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
                <a href="{{ route('quiz') }}" class="inline-flex items-center gap-2 rounded-full bg-[#991b1b] px-6 py-3 text-sm font-bold text-white shadow-md transition hover:bg-red-800">
                    <i data-lucide="layers" class="h-4 w-4"></i>
                    <span>Xem tất cả câu hỏi</span>
                </a>
                <a href="{{ route('flashcards') }}" class="inline-flex items-center gap-2 rounded-full border border-slate-300 bg-white px-6 py-3 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
                    <i data-lucide="book-open" class="h-4 w-4"></i>
                    <span>Ôn Flashcard trước</span>
                </a>
            </div>
        </section>
    @endif
</div>

{{-- Alpine.js Quiz Logic --}}
<script>
function quizApp() {
    return {
        answers: {},
        isSubmitted: false,
        isSubmitting: false,
        results: null,
        startTime: null,
        totalCount: {{ count($questions) }},

        initQuiz() {
            this.startTime = Date.now();
        },

        get answeredCount() {
            return Object.keys(this.answers).filter(k => this.answers[k] !== undefined && this.answers[k] !== '').length;
        },

        get progressPercentage() {
            if (this.totalCount === 0) return 0;
            return Math.round((this.answeredCount / this.totalCount) * 100);
        },

        selectOption(questionId, option) {
            if (this.isSubmitted) return;
            this.answers[questionId] = option;
        },

        isOptionSelected(questionId, option) {
            return this.answers[questionId] === option;
        },

        isCorrectOption(questionId, option) {
            if (!this.isSubmitted || !this.results?.details?.[questionId]) return false;
            return this.results.details[questionId].correct_answer === option;
        },

        isUserWrongOption(questionId, option) {
            if (!this.isSubmitted || !this.results?.details?.[questionId]) return false;
            const detail = this.results.details[questionId];
            return detail.user_answer === option && !detail.is_correct;
        },

        getOptionClass(questionId, option) {
            if (!this.isSubmitted) {
                if (this.isOptionSelected(questionId, option)) {
                    return 'border-[#991b1b] bg-red-50/60 text-[#991b1b] shadow-md';
                }
                return 'border-slate-200 bg-slate-50 hover:border-amber-300 hover:bg-white text-slate-700';
            }

            if (this.isCorrectOption(questionId, option)) {
                return 'border-emerald-500 bg-emerald-50 text-emerald-950 font-bold ring-1 ring-emerald-500';
            }

            if (this.isUserWrongOption(questionId, option)) {
                return 'border-rose-500 bg-rose-50 text-rose-950 ring-1 ring-rose-500 line-through opacity-90';
            }

            return 'border-slate-200 bg-slate-50/50 text-slate-400 opacity-60';
        },

        getOptionRadioClass(questionId, option) {
            if (!this.isSubmitted) {
                if (this.isOptionSelected(questionId, option)) {
                    return 'border-[#991b1b] bg-[#991b1b] text-white';
                }
                return 'border-slate-300 bg-white text-transparent';
            }

            if (this.isCorrectOption(questionId, option)) {
                return 'border-emerald-600 bg-emerald-600 text-white';
            }

            if (this.isUserWrongOption(questionId, option)) {
                return 'border-rose-600 bg-rose-600 text-white';
            }

            return 'border-slate-300 bg-slate-100 text-transparent';
        },

        getOptionTextClass(questionId, option) {
            if (!this.isSubmitted) {
                return this.isOptionSelected(questionId, option) ? 'font-bold text-[#991b1b]' : 'text-slate-700';
            }

            if (this.isCorrectOption(questionId, option)) {
                return 'font-bold text-emerald-900';
            }

            if (this.isUserWrongOption(questionId, option)) {
                return 'text-rose-900';
            }

            return 'text-slate-400';
        },

        async submitQuiz() {
            if (this.answeredCount === 0) {
                alert('Vui lòng chọn ít nhất một đáp án trước khi nộp bài.');
                return;
            }

            if (this.answeredCount < this.totalCount) {
                const proceed = confirm(`Bạn còn ${this.totalCount - this.answeredCount} câu chưa làm. Bạn có chắc chắn muốn nộp bài luôn không?`);
                if (!proceed) return;
            }

            this.isSubmitting = true;
            const durationSeconds = Math.max(1, Math.round((Date.now() - this.startTime) / 1000));

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
                    throw new Error("Không thể gửi bài kiểm tra.");
                }

                const data = await response.json();
                this.results = data;
                this.isSubmitted = true;
                setTimeout(() => window.refreshIcons?.(), 50);

                // Smooth scroll to top to see score
                window.scrollTo({ top: 0, behavior: 'smooth' });
            } catch (err) {
                console.error(err);
                alert("Đã xảy ra lỗi khi chấm điểm. Vui lòng thử lại!");
            } finally {
                this.isSubmitting = false;
            }
        },

        resetQuiz() {
            this.answers = {};
            this.isSubmitted = false;
            this.results = null;
            this.startTime = Date.now();
            setTimeout(() => window.refreshIcons?.(), 50);
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    };
}
</script>
@endsection