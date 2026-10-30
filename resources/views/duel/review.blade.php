@extends('layouts.app')

@section('title', 'Ôn Tập Câu Sai · ' . $roomCode . ' | Chinese Duel')

@section('content')
<div x-data="duelReviewApp({{ Js::from($mistakes) }}, '{{ $roomCode }}')" class="max-w-4xl mx-auto space-y-8 pb-16 font-serif-custom select-none">

    {{-- Top Ambience Banner --}}
    <section class="relative overflow-hidden rounded-[2.25rem] border border-amber-900/20 bg-gradient-to-br from-[#faf4ee] via-[#f7ebe0] to-[#f3ded1] p-6 sm:p-8 shadow-xl shadow-amber-950/5">
        <div class="pointer-events-none absolute -right-6 -bottom-8 select-none text-[8rem] sm:text-[10rem] font-serif font-black leading-none text-amber-900/[0.04]">
            温故
        </div>

        <div class="relative z-10 flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
            <div class="max-w-xl">
                <div class="flex flex-wrap items-center gap-2 mb-3">
                    <span class="seal-zhusha-round text-xs font-bold tracking-widest">
                        温 故 · 知 新
                    </span>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-white/80 border border-amber-900/10 px-3 py-1 text-xs font-semibold text-slate-700 shadow-2xs font-sans">
                        <i data-lucide="book-open-check" class="h-3.5 w-3.5 text-amber-700"></i>
                        <span>Ôn Tập & Khắc Sâu Kiến Thức</span>
                    </span>
                </div>

                <h1 class="text-3xl sm:text-4xl font-black tracking-tight text-slate-900 font-serif">
                    Ôn Tập Câu Sai & Hết Giờ
                </h1>
                <p class="mt-2 text-xs sm:text-sm leading-relaxed text-slate-600 font-sans">
                    <span class="italic text-amber-950 font-medium">"Ôn cố nhi tri tân"</span> — Đừng để những câu sai trôi qua lãng phí! Hãy thử trả lời lại và lưu từ vựng vào bộ thẻ Flashcard SRS để ghi nhớ lâu dài.
                </p>
            </div>

            {{-- Summary Pill --}}
            <div class="rounded-2xl bg-white/90 border border-amber-200/80 p-4 text-center shrink-0 font-sans shadow-xs">
                <span class="text-xs text-slate-500 font-semibold uppercase">Cần Ôn Tập</span>
                <p class="text-3xl font-black text-amber-800 mt-0.5" x-text="mistakes.length + ' Câu'"></p>
                <p class="text-[11px] text-slate-400 mt-1">Phòng {{ $roomCode }}</p>
            </div>
        </div>
    </section>

    {{-- Navigation Actions --}}
    <div class="flex items-center justify-between font-sans">
        <a href="{{ route('duel.result', $roomCode) }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-600 hover:text-slate-900 transition">
            <i data-lucide="arrow-left" class="h-4 w-4"></i>
            <span>Quay lại Bảng Kết Quả</span>
        </a>

        <div class="flex items-center gap-2">
            <span class="text-xs text-slate-500 font-medium" x-show="mistakes.length > 0">
                Đã ôn tập: <span class="font-bold text-emerald-700" x-text="getReviewedCount()"></span> / <span x-text="mistakes.length"></span>
            </span>
        </div>
    </div>

    {{-- Empty State: No Mistakes --}}
    <div x-show="mistakes.length === 0" class="rounded-3xl border border-emerald-200 bg-white/90 p-10 text-center font-sans space-y-4 shadow-sm">
        <div class="text-5xl">🎉</div>
        <h3 class="text-2xl font-black text-slate-900 font-serif">Xuất Sắc! Không Có Câu Sai Nào</h3>
        <p class="text-xs sm:text-sm text-slate-600 max-w-md mx-auto">
            Bạn đã trả lời chính xác tất cả các câu hỏi trong trận đấu này. Hãy tiếp tục giữ vững phong độ phản xạ tuyệt vời này!
        </p>
        <div class="pt-2 flex justify-center gap-3">
            <a href="{{ route('duel.lobby') }}" class="py-2.5 px-5 rounded-xl bg-[#991b1b] hover:bg-red-800 text-white font-bold text-xs shadow-md transition">
                Tìm Trận Đấu Mới
            </a>
            <a href="{{ route('duel.result', $roomCode) }}" class="py-2.5 px-5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                Xem Bảng Kết Quả
            </a>
        </div>
    </div>

    {{-- Mistakes Cards List --}}
    <div x-show="mistakes.length > 0" class="space-y-6">
        <template x-for="(m, mIdx) in mistakes" :key="m.round_number">
            <div class="rounded-3xl border border-slate-200/90 bg-white/95 backdrop-blur p-6 sm:p-8 shadow-sm space-y-5 transition"
                 :class="{ 'ring-2 ring-emerald-300 border-emerald-200': m.is_reviewed }">

                {{-- Header: Round & Mistake Reason --}}
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 pb-3 font-sans">
                    <div class="flex items-center gap-2">
                        <span class="rounded-lg bg-slate-900 text-white font-mono font-bold text-xs px-2.5 py-1">
                            Hiệp <span x-text="m.round_number"></span>
                        </span>
                        <template x-if="m.is_timeout">
                            <span class="text-xs font-bold text-amber-800 bg-amber-100 px-2.5 py-0.5 rounded-full inline-flex items-center gap-1">
                                <i data-lucide="clock" class="h-3.5 w-3.5"></i> Hết giờ (Timeout)
                            </span>
                        </template>
                        <template x-if="!m.is_timeout">
                            <span class="text-xs font-bold text-rose-800 bg-rose-100 px-2.5 py-0.5 rounded-full inline-flex items-center gap-1">
                                <i data-lucide="x-circle" class="h-3.5 w-3.5"></i> Đã chọn sai
                            </span>
                        </template>
                    </div>

                    {{-- Flashcard & Review Action Buttons --}}
                    <div class="flex items-center gap-2">
                        {{-- Save to Flashcard Button --}}
                        <button type="button"
                            @click="saveToFlashcard(m, mIdx)"
                            :disabled="m.is_saved_flashcard || isSavingFlashcard"
                            class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 cursor-pointer"
                            :class="m.is_saved_flashcard ? 'bg-amber-100 text-amber-900 cursor-default' : 'bg-slate-100 hover:bg-amber-50 text-slate-700 hover:text-amber-800 border border-slate-200'">
                            <i data-lucide="star" class="h-3.5 w-3.5 text-amber-500" :class="{ 'fill-amber-500': m.is_saved_flashcard }"></i>
                            <span x-text="m.is_saved_flashcard ? 'Đã lưu Flashcard ✓' : 'Lưu Flashcard SRS'"></span>
                        </button>

                        {{-- Mark as Reviewed Pill --}}
                        <button type="button"
                            @click="toggleReviewed(m)"
                            class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1 cursor-pointer"
                            :class="m.is_reviewed ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-500 hover:bg-slate-200'">
                            <i data-lucide="check" class="h-3.5 w-3.5" x-show="m.is_reviewed"></i>
                            <span x-text="m.is_reviewed ? 'Đã hiểu bài' : 'Chưa hiểu'"></span>
                        </button>
                    </div>
                </div>

                {{-- Question Hanzi & Pinyin --}}
                <div class="space-y-1.5">
                    <p class="text-3xl sm:text-4xl font-black font-serif text-slate-900 tracking-wide"
                       x-text="m.question"></p>
                    <p class="text-xs sm:text-sm font-medium text-amber-800 font-sans"
                       x-show="m.pinyin" x-text="m.pinyin"></p>
                </div>

                {{-- Interactive Practice: 4 Options --}}
                <div class="space-y-2 pt-1 font-sans">
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Thử chọn lại đáp án đúng:</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                        <template x-for="(opt, optIdx) in m.options" :key="optIdx">
                            <button type="button"
                                @click="tryAnswer(m, opt)"
                                :class="getPracticeOptionClass(m, opt)"
                                class="p-3.5 rounded-xl border-2 text-left text-xs sm:text-sm font-semibold transition flex items-center justify-between cursor-pointer">
                                <span x-text="opt"></span>
                                <div class="shrink-0">
                                    <template x-if="m.practice_choice && opt === m.correct_answer">
                                        <i data-lucide="check-circle" class="h-4 w-4 text-emerald-600"></i>
                                    </template>
                                    <template x-if="m.practice_choice === opt && opt !== m.correct_answer">
                                        <i data-lucide="x-circle" class="h-4 w-4 text-rose-600"></i>
                                    </template>
                                </div>
                            </button>
                        </template>
                    </div>
                </div>

                {{-- Instant Feedback & Explanation --}}
                <div x-show="m.practice_choice" x-cloak class="rounded-2xl p-4 font-sans text-xs sm:text-sm space-y-2"
                     :class="m.practice_choice === m.correct_answer ? 'bg-emerald-50 border border-emerald-200 text-emerald-950' : 'bg-rose-50 border border-rose-200 text-rose-950'">
                    <p class="font-bold flex items-center gap-1.5">
                        <span x-text="m.practice_choice === m.correct_answer ? '🎉 Chính xác!' : '❌ Chưa chính xác, hãy xem lại đáp án chuẩn:'"></span>
                    </p>
                    <p class="text-slate-800">
                        Đáp án đúng là: <strong class="text-emerald-700" x-text="m.correct_answer"></strong>
                    </p>
                    <template x-if="m.explanation">
                        <div class="mt-2 pt-2 border-t border-slate-200/60 text-slate-600 leading-relaxed">
                            <strong class="text-slate-800">💡 Giải thích:</strong> <span x-text="m.explanation"></span>
                        </div>
                    </template>
                </div>

            </div>
        </template>
    </div>

</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('duelReviewApp', (initialMistakes, roomCode) => ({
        roomCode: roomCode,
        mistakes: (initialMistakes || []).map(m => ({
            ...m,
            practice_choice: null,
            is_reviewed: false,
            is_saved_flashcard: false,
        })),
        isSavingFlashcard: false,

        init() {
            this.$nextTick(() => {
                if (window.refreshIcons) window.refreshIcons();
            });
        },

        getReviewedCount() {
            return this.mistakes.filter(m => m.is_reviewed).length;
        },

        toggleReviewed(m) {
            m.is_reviewed = !m.is_reviewed;
        },

        tryAnswer(m, opt) {
            m.practice_choice = opt;
            if (opt === m.correct_answer) {
                m.is_reviewed = true;
            }
            this.$nextTick(() => {
                if (window.refreshIcons) window.refreshIcons();
            });
        },

        getPracticeOptionClass(m, opt) {
            if (!m.practice_choice) {
                return 'bg-white hover:bg-slate-50 border-slate-200 text-slate-800';
            }
            if (opt === m.correct_answer) {
                return 'bg-emerald-50 border-emerald-500 text-emerald-950 font-bold';
            }
            if (m.practice_choice === opt) {
                return 'bg-rose-50 border-rose-500 text-rose-950 font-bold';
            }
            return 'bg-slate-50 border-slate-200 text-slate-400 opacity-60';
        },

        async saveToFlashcard(m, idx) {
            if (m.is_saved_flashcard || this.isSavingFlashcard) return;
            this.isSavingFlashcard = true;

            try {
                const res = await fetch(`/duel/room/${this.roomCode}/review/save-flashcard`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: JSON.stringify({ round_number: m.round_number })
                });

                const data = await res.json();

                if (!res.ok) {
                    if (res.status === 401) {
                        alert('Vui lòng đăng nhập để lưu từ vựng vào bộ thẻ Flashcard SRS của bạn.');
                        return;
                    }
                    throw new Error(data.message || 'Lỗi khi lưu thẻ');
                }

                m.is_saved_flashcard = true;
            } catch (err) {
                alert(err.message);
            } finally {
                this.isSavingFlashcard = false;
            }
        }
    }));
});
</script>
@endsection
