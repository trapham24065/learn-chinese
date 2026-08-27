@extends('layouts.app')

@section('title', 'Phòng thi: ' . $spec['title'] . ' | Chinese Deck')

@section('content')

<div x-data="{
    level: {{ $level }},
    questions: {{ Js::from($questions) }},
    total: {{ $totalQuestions }},
    currentIndex: 0,
    answers: {},
    flagged: [],
    audioPlayCounts: {},
    timeRemaining: {{ $timeLimitSecs }},
    totalTime: {{ $timeLimitSecs }},
    timerInterval: null,
    isPlayingAudio: false,
    showConfirmModal: false,
    isSubmitting: false,
    autoSubmitted: false,
    activeSection: 'all', // 'all', 'listening', 'reading', 'grammar'

    init() {
        this.startTimer();
        this.$watch('currentIndex', () => {
            setTimeout(() => window.refreshIcons?.(), 50);
        });
        setTimeout(() => window.refreshIcons?.(), 100);
    },

    get currentQ() {
        return this.questions[this.currentIndex] ?? null;
    },

    get answeredCount() {
        return Object.keys(this.answers).filter(k => this.answers[k] !== null && this.answers[k] !== '').length;
    },

    get unansweredCount() {
        return this.total - this.answeredCount;
    },

    get formattedTime() {
        const m = Math.floor(this.timeRemaining / 60);
        const s = this.timeRemaining % 60;
        return `${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
    },

    get timeProgressPercent() {
        return Math.round((this.timeRemaining / this.totalTime) * 100);
    },

    get isTimeWarning() {
        return this.timeRemaining <= 180; // under 3 minutes
    },

    startTimer() {
        this.timerInterval = setInterval(() => {
            if (this.timeRemaining > 0) {
                this.timeRemaining--;
            } else {
                clearInterval(this.timerInterval);
                this.handleTimeUp();
            }
        }, 1000);
    },

    selectAnswer(option) {
        if (this.isSubmitting) return;
        this.answers[this.currentQ.id] = option;
    },

    toggleFlag(qId) {
        const idx = this.flagged.indexOf(qId);
        if (idx > -1) {
            this.flagged.splice(idx, 1);
        } else {
            this.flagged.push(qId);
        }
    },

    isFlagged(qId) {
        return this.flagged.includes(qId);
    },

    goTo(index) {
        if (index >= 0 && index < this.total) {
            this.currentIndex = index;
        }
    },

    next() {
        if (this.currentIndex < this.total - 1) {
            this.currentIndex++;
        }
    },

    prev() {
        if (this.currentIndex > 0) {
            this.currentIndex--;
        }
    },

    async playAudio(text, qId) {
        if (!text || this.isPlayingAudio) return;
        
        const count = this.audioPlayCounts[qId] ?? 0;
        if (count >= 2) {
            alert('Mỗi câu hỏi phần Nghe chỉ được phát tối đa 2 lần theo quy chế thi HSK.');
            return;
        }

        this.isPlayingAudio = true;
        this.audioPlayCounts[qId] = count + 1;

        try {
            await window.playChineseVoice(text);
        } catch (e) {
            console.error(e);
        } finally {
            setTimeout(() => {
                this.isPlayingAudio = false;
                window.refreshIcons?.();
            }, 1200);
        }
    },

    handleTimeUp() {
        this.autoSubmitted = true;
        this.submitExam();
    },

    confirmSubmit() {
        this.showConfirmModal = true;
        setTimeout(() => window.refreshIcons?.(), 50);
    },

    async submitExam() {
        if (this.isSubmitting) return;
        this.isSubmitting = true;
        this.showConfirmModal = false;

        if (this.timerInterval) {
            clearInterval(this.timerInterval);
        }

        const durationSeconds = this.totalTime - this.timeRemaining;

        try {
            const response = await fetch('{{ route('hsk.mock.submit', $level) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    answers: this.answers,
                    duration_seconds: Math.max(1, durationSeconds),
                })
            });

            const data = await response.json();
            if (data.success && data.redirect_url) {
                window.location.href = data.redirect_url;
            } else {
                alert('Có lỗi xảy ra khi nộp bài: ' + (data.message || 'Vui lòng thử lại.'));
                this.isSubmitting = false;
            }
        } catch (error) {
            console.error('Lỗi khi nộp bài:', error);
            alert('Không thể kết nối máy chủ để nộp bài. Vui lòng kiểm tra lại mạng.');
            this.isSubmitting = false;
        }
    }
}" x-init="init()" class="space-y-6">

    {{-- Top Sticky Exam Header --}}
    <div class="sticky top-4 z-30 flex flex-col gap-3 rounded-[2rem] border border-white/80 bg-white/90 p-4 sm:p-5 shadow-2xl shadow-slate-900/10 backdrop-blur">
        <div class="flex flex-wrap items-center justify-between gap-4">
            
            {{-- Exam Title & Info --}}
            <div class="flex items-center gap-3">
                <span class="grid h-10 w-10 place-items-center rounded-2xl font-black text-sm text-white shadow-md"
                      style="background: {{ $spec['color'] }}">
                    {{ $spec['label'] }}
                </span>
                <div>
                    <h1 class="text-base sm:text-lg font-black text-slate-900 leading-tight">
                        {{ $spec['title'] }}
                    </h1>
                    <p class="text-xs text-slate-500">
                        Đã làm: <strong class="text-slate-900" x-text="answeredCount"></strong>/<span x-text="total"></span> câu
                        · Còn lại: <strong class="text-[#991b1b]" x-text="unansweredCount"></strong> câu
                    </p>
                </div>
            </div>

            {{-- Countdown Timer & Submit Button --}}
            <div class="flex items-center gap-3">
                {{-- Countdown Badge --}}
                <div class="flex items-center gap-2 rounded-2xl px-4 py-2 text-sm font-black transition-all shadow-inner"
                     :class="isTimeWarning ? 'bg-red-500 text-white animate-pulse shadow-red-500/20' : 'bg-slate-900 text-amber-300'">
                    <i data-lucide="timer" class="h-4 w-4" :class="isTimeWarning ? 'text-white' : 'text-amber-400'"></i>
                    <span x-text="formattedTime" class="tracking-widest font-mono text-base"></span>
                </div>

                {{-- Manual Submit Button --}}
                <button type="button"
                        @click="confirmSubmit()"
                        :disabled="isSubmitting"
                        class="inline-flex items-center gap-2 rounded-2xl bg-gradient-to-r from-[#991b1b] to-red-800 px-5 py-2.5 text-sm font-bold text-white shadow-md shadow-red-950/20 transition hover:opacity-90 active:scale-95 disabled:opacity-50">
                    <i data-lucide="send" class="h-4 w-4"></i>
                    <span class="hidden sm:inline">Nộp bài thi</span>
                    <span class="sm:hidden">Nộp</span>
                </button>
            </div>
        </div>

        {{-- Top Time Progress Bar --}}
        <div class="h-1.5 w-full overflow-hidden rounded-full bg-slate-100">
            <div class="h-full rounded-full transition-all duration-500"
                 :class="isTimeWarning ? 'bg-red-500' : 'bg-amber-400'"
                 :style="`width: ${timeProgressPercent}%`"></div>
        </div>
    </div>

    {{-- Main Workspace (Question Area + Question Palette) --}}
    <div class="grid gap-6 lg:grid-cols-[1fr_320px] items-start">

        {{-- Left: Active Question Card --}}
        <div class="rounded-[2.5rem] border border-white/80 bg-white/90 p-6 sm:p-8 shadow-xl shadow-slate-900/5 backdrop-blur">
            
            {{-- Question Header --}}
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 pb-5">
                <div class="flex items-center gap-2.5">
                    <span class="rounded-xl px-3 py-1 text-xs font-black uppercase tracking-wider text-white"
                          style="background: {{ $spec['color'] }}">
                        Câu <span x-text="currentIndex + 1"></span>/<span x-text="total"></span>
                    </span>

                    <template x-if="currentQ?.skill_type === 'listening'">
                        <span class="inline-flex items-center gap-1 rounded-xl bg-blue-50 border border-blue-200 px-3 py-1 text-xs font-bold text-blue-700">
                            <i data-lucide="headphones" class="h-3.5 w-3.5"></i>
                            <span>Phần 1: Nghe hiểu</span>
                        </span>
                    </template>
                    <template x-if="currentQ?.skill_type === 'reading'">
                        <span class="inline-flex items-center gap-1 rounded-xl bg-emerald-50 border border-emerald-200 px-3 py-1 text-xs font-bold text-emerald-700">
                            <i data-lucide="book-open" class="h-3.5 w-3.5"></i>
                            <span>Phần 2: Đọc hiểu</span>
                        </span>
                    </template>
                    <template x-if="currentQ?.skill_type === 'grammar'">
                        <span class="inline-flex items-center gap-1 rounded-xl bg-purple-50 border border-purple-200 px-3 py-1 text-xs font-bold text-purple-700">
                            <i data-lucide="pen-tool" class="h-3.5 w-3.5"></i>
                            <span>Phần 3: Ngữ pháp</span>
                        </span>
                    </template>
                </div>

                {{-- Flag / Bookmark Button --}}
                <button type="button"
                        @click="toggleFlag(currentQ?.id)"
                        class="inline-flex items-center gap-1.5 rounded-xl border px-3 py-1.5 text-xs font-bold transition"
                        :class="isFlagged(currentQ?.id) ? 'bg-amber-100 border-amber-300 text-amber-900 shadow-sm' : 'border-slate-200 text-slate-500 hover:bg-slate-50'">
                    <i data-lucide="bookmark" class="h-3.5 w-3.5" :class="isFlagged(currentQ?.id) ? 'fill-current text-amber-600' : ''"></i>
                    <span x-text="isFlagged(currentQ?.id) ? 'Đã đánh dấu xem lại' : 'Đánh dấu xem lại'"></span>
                </button>
            </div>

            {{-- Question Content Body --}}
            <div class="py-6 sm:py-8">
                
                {{-- Listening Audio Player Box --}}
                <template x-if="currentQ?.skill_type === 'listening' && currentQ?.audio_text">
                    <div class="mb-6 rounded-3xl border border-blue-100 bg-gradient-to-br from-blue-50 to-indigo-50/40 p-5 sm:p-6 shadow-sm">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <div class="grid h-12 w-12 place-items-center rounded-2xl bg-blue-600 text-white shadow-md shadow-blue-600/20">
                                    <i data-lucide="headphones" class="h-6 w-6"></i>
                                </div>
                                <div>
                                    <p class="text-xs font-bold uppercase tracking-wider text-blue-900">Audio đề thi chuẩn HSK</p>
                                    <p class="text-xs text-blue-600 mt-0.5">
                                        Đã nghe: <span class="font-bold" x-text="audioPlayCounts[currentQ.id] || 0"></span>/2 lần
                                    </p>
                                </div>
                            </div>

                            <button type="button"
                                    @click="playAudio(currentQ.audio_text, currentQ.id)"
                                    :disabled="isPlayingAudio || (audioPlayCounts[currentQ.id] >= 2)"
                                    class="inline-flex items-center justify-center gap-2 rounded-2xl bg-blue-600 px-5 py-3 text-xs font-bold text-white shadow-md transition hover:bg-blue-700 active:scale-95 disabled:cursor-not-allowed disabled:opacity-50">
                                <i data-lucide="volume-2" class="h-4 w-4" :class="isPlayingAudio ? 'animate-bounce' : ''"></i>
                                <span x-text="isPlayingAudio ? 'Đang phát...' : ((audioPlayCounts[currentQ.id] >= 2) ? 'Đã hết lượt nghe' : 'Nghe phát âm')"></span>
                            </button>
                        </div>
                    </div>
                </template>

                {{-- Question Prompt & Text --}}
                <div class="space-y-3">
                    <p class="text-base sm:text-lg font-bold text-slate-900 leading-relaxed" x-text="currentQ?.question"></p>
                    
                    <template x-if="currentQ?.pinyin">
                        <p class="text-xs sm:text-sm font-semibold tracking-wider text-[#991b1b]" x-text="currentQ?.pinyin"></p>
                    </template>
                </div>

                {{-- Multiple Choice Options List --}}
                <div class="mt-8 space-y-3">
                    <template x-for="(option, optIdx) in currentQ?.options" :key="optIdx">
                        <div @click="selectAnswer(option)"
                             class="group flex items-center justify-between gap-4 rounded-2xl border p-4 sm:p-5 cursor-pointer transition-all duration-200 select-none"
                             :class="answers[currentQ?.id] === option ? 'border-[#991b1b] bg-red-50/70 shadow-md ring-1 ring-[#991b1b]' : 'border-slate-200 bg-white hover:border-slate-300 hover:bg-slate-50/80'">
                            
                            <div class="flex items-center gap-3.5">
                                {{-- Option Letter (A, B, C, D) --}}
                                <span class="grid h-8 w-8 shrink-0 place-items-center rounded-xl text-xs font-black transition"
                                      :class="answers[currentQ?.id] === option ? 'bg-[#991b1b] text-white shadow-sm' : 'bg-slate-100 text-slate-700 group-hover:bg-slate-200'"
                                      x-text="['A', 'B', 'C', 'D', 'E', 'F'][optIdx] ?? (optIdx + 1)">
                                </span>

                                <span class="text-sm sm:text-base font-semibold leading-relaxed"
                                      :class="answers[currentQ?.id] === option ? 'text-slate-950 font-bold' : 'text-slate-700'"
                                      x-text="option">
                                </span>
                            </div>

                            {{-- Selected Checkmark --}}
                            <div class="shrink-0">
                                <span class="flex h-6 w-6 items-center justify-center rounded-full border transition"
                                      :class="answers[currentQ?.id] === option ? 'border-[#991b1b] bg-[#991b1b] text-white' : 'border-slate-300 bg-white'">
                                    <template x-if="answers[currentQ?.id] === option">
                                        <i data-lucide="check" class="h-3.5 w-3.5"></i>
                                    </template>
                                </span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Bottom Navigation Controls --}}
            <div class="flex items-center justify-between border-t border-slate-100 pt-6 gap-3">
                <button type="button"
                        @click="prev()"
                        :disabled="currentIndex === 0"
                        class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-5 py-2.5 text-xs sm:text-sm font-bold text-slate-700 shadow-sm transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-40">
                    <i data-lucide="arrow-left" class="h-4 w-4"></i>
                    <span>Câu trước</span>
                </button>

                <div class="text-xs text-slate-400 font-semibold hidden sm:block">
                    Mẹo: Bấm bảng số bên phải để nhảy nhanh câu hỏi
                </div>

                <button type="button"
                        @click="next()"
                        :disabled="currentIndex === total - 1"
                        class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-5 py-2.5 text-xs sm:text-sm font-bold text-slate-700 shadow-sm transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-40">
                    <span>Câu tiếp theo</span>
                    <i data-lucide="arrow-right" class="h-4 w-4"></i>
                </button>
            </div>
        </div>

        {{-- Right: Question Navigator Palette --}}
        <div class="space-y-6">
            
            {{-- Palette Card --}}
            <div class="rounded-[2.5rem] border border-white/80 bg-white/90 p-6 shadow-xl shadow-slate-900/5 backdrop-blur">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <h3 class="text-xs font-bold uppercase tracking-[0.22em] text-[#991b1b]">Bảng câu hỏi</h3>
                    <span class="text-xs font-bold text-slate-500">
                        <span x-text="answeredCount"></span>/<span x-text="total"></span> đã làm
                    </span>
                </div>

                {{-- Status Legend --}}
                <div class="mt-4 grid grid-cols-3 gap-2 text-[11px] font-semibold text-slate-500 pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-1.5">
                        <span class="h-3 w-3 rounded-md bg-slate-900"></span>
                        <span>Đã làm</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="h-3 w-3 rounded-md bg-amber-400"></span>
                        <span>Đánh dấu</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="h-3 w-3 rounded-md border border-slate-300 bg-white"></span>
                        <span>Chưa làm</span>
                    </div>
                </div>

                {{-- Number Grid --}}
                <div class="mt-4 grid grid-cols-5 gap-2 max-h-[360px] overflow-y-auto pr-1" style="scrollbar-width: thin;">
                    <template x-for="(q, idx) in questions" :key="q.id">
                        <button type="button"
                                @click="goTo(idx)"
                                class="relative flex h-10 w-full items-center justify-center rounded-xl text-xs font-black transition-all active:scale-95"
                                :class="{
                                    'ring-2 ring-[#991b1b] ring-offset-2': currentIndex === idx,
                                    'bg-amber-400 text-slate-950 shadow-sm': isFlagged(q.id),
                                    'bg-slate-900 text-white shadow-sm': (!isFlagged(q.id) && answers[q.id]),
                                    'border border-slate-200 bg-slate-50 text-slate-600 hover:bg-slate-100': (!isFlagged(q.id) && !answers[q.id])
                                }"
                                x-text="idx + 1">
                        </button>
                    </template>
                </div>

                {{-- Big Bottom Submit Action in Palette --}}
                <div class="mt-6 pt-4 border-t border-slate-100">
                    <button type="button"
                            @click="confirmSubmit()"
                            :disabled="isSubmitting"
                            class="w-full inline-flex items-center justify-center gap-2 rounded-2xl bg-[#991b1b] py-3.5 px-4 text-xs font-bold text-white shadow-md shadow-red-950/20 transition hover:bg-red-800 active:scale-95 disabled:opacity-50">
                        <i data-lucide="check-circle" class="h-4 w-4"></i>
                        <span>Nộp bài & Xem kết quả</span>
                    </button>
                </div>
            </div>

            {{-- Exam Rule Card --}}
            <div class="rounded-3xl border border-amber-200/60 bg-amber-50/70 p-5 text-xs text-amber-900 leading-relaxed shadow-sm">
                <p class="font-bold flex items-center gap-1.5 text-amber-950 mb-1.5 uppercase tracking-wider">
                    <i data-lucide="info" class="h-4 w-4 text-amber-600"></i>
                    Quy chế phòng thi
                </p>
                <ul class="space-y-1 list-disc pl-4 text-amber-800/90">
                    <li>Hết giờ đồng hồ sẽ tự động nộp bài và khóa đề thi.</li>
                    <li>Đạt từ <strong>180/300 điểm</strong> trở lên để nhận Chứng chỉ Online.</li>
                    <li>Có thể bấm "Đánh dấu xem lại" để kiểm tra trước khi nộp.</li>
                </ul>
            </div>
        </div>
    </div>

    {{-- Confirmation Modal before submit --}}
    <div x-show="showConfirmModal"
         x-cloak
         style="display: none;"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4 backdrop-blur-sm"
         x-transition.opacity>
        
        <div @click.outside="showConfirmModal = false"
             class="relative w-full max-w-md rounded-[2.5rem] bg-white p-6 sm:p-8 shadow-2xl"
             x-transition.scale.95>
            
            <div class="grid h-16 w-16 place-items-center rounded-3xl bg-amber-50 text-amber-600 mx-auto">
                <i data-lucide="help-circle" class="h-8 w-8"></i>
            </div>

            <h3 class="mt-4 text-center text-xl font-black text-slate-900">Xác nhận nộp bài thi?</h3>
            
            <div class="mt-4 rounded-2xl bg-slate-50 p-4 text-xs font-semibold text-slate-700 space-y-2">
                <div class="flex justify-between">
                    <span>Số câu đã làm:</span>
                    <span class="text-emerald-600 font-bold" x-text="`${answeredCount} câu`"></span>
                </div>
                <div class="flex justify-between">
                    <span>Số câu chưa làm:</span>
                    <span class="text-red-600 font-bold" x-text="`${unansweredCount} câu`"></span>
                </div>
                <div class="flex justify-between">
                    <span>Thời gian còn lại:</span>
                    <span class="text-slate-900 font-bold" x-text="formattedTime"></span>
                </div>
            </div>

            <template x-if="unansweredCount > 0">
                <p class="mt-3 text-center text-xs text-amber-700 font-medium">
                    ⚠️ Bạn vẫn còn <span class="font-bold" x-text="unansweredCount"></span> câu chưa chọn đáp án. Bạn có muốn nộp ngay không?
                </p>
            </template>

            <div class="mt-6 flex gap-3">
                <button type="button"
                        @click="showConfirmModal = false"
                        class="flex-1 rounded-2xl border border-slate-200 py-3 text-xs font-bold text-slate-700 transition hover:bg-slate-50">
                    Làm tiếp
                </button>
                <button type="button"
                        @click="submitExam()"
                        :disabled="isSubmitting"
                        class="flex-1 rounded-2xl bg-[#991b1b] py-3 text-xs font-bold text-white shadow-md shadow-red-950/20 transition hover:bg-red-800 disabled:opacity-50">
                    <span x-text="isSubmitting ? 'Đang nộp...' : 'Nộp bài ngay'"></span>
                </button>
            </div>
        </div>
    </div>

    {{-- Auto-submit overlay modal --}}
    <div x-show="autoSubmitted"
         x-cloak
         style="display: none;"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 p-4 backdrop-blur-md">
        <div class="w-full max-w-sm rounded-[2.5rem] bg-white p-8 text-center shadow-2xl">
            <div class="grid h-16 w-16 place-items-center rounded-3xl bg-red-50 text-[#991b1b] mx-auto animate-spin">
                <i data-lucide="loader" class="h-8 w-8"></i>
            </div>
            <h3 class="mt-5 text-xl font-black text-slate-900">Hết giờ làm bài!</h3>
            <p class="mt-2 text-xs text-slate-500 leading-relaxed">
                Hệ thống đang tự động nộp bài và chấm điểm 3 kỹ năng của bạn...
            </p>
        </div>
    </div>

</div>

@endsection
