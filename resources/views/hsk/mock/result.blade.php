@extends('layouts.app')

@section('title', 'Kết quả thi: ' . $test->title . ' | Chinese Deck')

@section('content')

{{-- Breadcrumb --}}
<nav class="mb-6 flex items-center gap-2 text-sm text-slate-500">
    <a href="{{ route('hsk.mock.index') }}" class="hover:text-[#991b1b] transition">Thi thử HSK</a>
    <span>/</span>
    <span class="font-semibold text-slate-800">Kết quả bài thi #{{ $test->id }}</span>
</nav>

{{-- Result Hero Card --}}
<section class="relative overflow-hidden rounded-[2.5rem] p-8 sm:p-12 shadow-2xl transition mb-10 {{ $test->passed ? 'bg-gradient-to-br from-emerald-900 via-slate-950 to-slate-900 text-white shadow-emerald-950/20' : 'bg-gradient-to-br from-slate-900 via-slate-950 to-red-950 text-white shadow-red-950/20' }}">
    
    <div class="absolute -right-10 -bottom-10 text-[14rem] font-black leading-none opacity-5 select-none pointer-events-none">
        {{ $test->passed ? '过' : '试' }}
    </div>

    <div class="relative grid gap-8 lg:grid-cols-[1.4fr_1fr] items-center">
        <div>
            {{-- Status Pill --}}
            <div class="inline-flex items-center gap-2 rounded-full px-4 py-1.5 text-xs font-black uppercase tracking-widest backdrop-blur border {{ $test->passed ? 'bg-emerald-400/20 border-emerald-300/30 text-emerald-300' : 'bg-red-400/20 border-red-300/30 text-red-300' }}">
                <i data-lucide="{{ $test->passed ? 'check-circle' : 'alert-circle' }}" class="h-4 w-4"></i>
                <span>{{ $test->passed ? 'ĐẠT CHỨNG CHỈ (合格)' : 'CHƯA ĐẠT CHUẨN (不合格)' }}</span>
            </div>

            <h1 class="mt-4 text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight">
                {{ $test->title }}
            </h1>
            
            <p class="mt-3 text-base text-white/80 leading-relaxed max-w-xl">
                @if($test->passed)
                    Chúc mừng bạn đã xuất sắc vượt qua bài thi thử HSK với xếp loại <strong>{{ $test->grade_text }}</strong>! Bạn đã đủ điều kiện nhận Chứng chỉ Online chính thức.
                @else
                    Bạn đạt <strong>{{ $test->total_score }}/300 điểm</strong> (cần tối thiểu 180 điểm để đỗ). Hãy xem lại các câu trả lời sai bên dưới để ôn tập củng cố thêm nhé!
                @endif
            </p>

            {{-- Metadata Row --}}
            <div class="mt-6 flex flex-wrap gap-4 text-xs font-semibold text-white/80">
                <div class="flex items-center gap-1.5 rounded-xl bg-white/10 px-3.5 py-2 backdrop-blur">
                    <i data-lucide="calendar" class="h-4 w-4 text-amber-400"></i>
                    <span>Ngày thi: {{ $test->completed_at?->format('d/m/Y H:i') ?? $test->created_at->format('d/m/Y H:i') }}</span>
                </div>
                <div class="flex items-center gap-1.5 rounded-xl bg-white/10 px-3.5 py-2 backdrop-blur">
                    <i data-lucide="timer" class="h-4 w-4 text-blue-400"></i>
                    <span>Thời gian làm bài: {{ $test->formatted_duration }}</span>
                </div>
                <div class="flex items-center gap-1.5 rounded-xl bg-white/10 px-3.5 py-2 backdrop-blur">
                    <i data-lucide="check-square" class="h-4 w-4 text-emerald-400"></i>
                    <span>Số câu đúng: {{ $test->correct_answers }}/{{ $test->total_questions }} câu</span>
                </div>
            </div>

            {{-- CTA Button Group --}}
            <div class="mt-8 flex flex-wrap items-center gap-3">
                @if($test->certificate_code)
                <a href="{{ route('hsk.mock.certificate', $test->certificate_code) }}"
                   class="inline-flex items-center gap-2 rounded-2xl bg-amber-400 hover:bg-amber-300 text-slate-950 px-6 py-3.5 text-sm font-black shadow-lg shadow-amber-400/20 transition active:scale-95">
                    <i data-lucide="award" class="h-5 w-5 fill-current"></i>
                    <span>Xem & In Chứng chỉ Online</span>
                </a>
                @endif

                <a href="{{ route('hsk.mock.start', $test->hsk_level) }}"
                   class="inline-flex items-center gap-2 rounded-2xl bg-white/15 hover:bg-white/25 text-white px-5 py-3.5 text-sm font-bold backdrop-blur border border-white/20 transition active:scale-95">
                    <i data-lucide="rotate-ccw" class="h-4 w-4"></i>
                    <span>Thi lại đề này</span>
                </a>

                <a href="{{ route('hsk.mock.index') }}"
                   class="inline-flex items-center gap-2 rounded-2xl bg-white/10 hover:bg-white/20 text-white px-5 py-3.5 text-sm font-bold backdrop-blur transition">
                    <span>Chọn cấp độ khác</span>
                    <i data-lucide="arrow-right" class="h-4 w-4"></i>
                </a>
            </div>
        </div>

        {{-- Big Score Badge Box --}}
        <div class="rounded-3xl border border-white/15 bg-white/10 p-8 backdrop-blur shadow-2xl text-center">
            <p class="text-xs uppercase tracking-[0.24em] font-bold text-amber-200/90">Tổng điểm bài thi</p>
            <div class="mt-3 flex items-baseline justify-center gap-1">
                <span class="text-6xl sm:text-7xl font-black tracking-tight {{ $test->passed ? 'text-amber-300' : 'text-white' }}">
                    {{ $test->total_score }}
                </span>
                <span class="text-xl font-bold text-white/50">/ 300</span>
            </div>
            
            <div class="mt-3 inline-block rounded-full px-4 py-1 text-xs font-black uppercase tracking-wider {{ $test->passed ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-400/30' : 'bg-red-500/20 text-red-300 border border-red-400/30' }}">
                {{ $test->grade_text }}
            </div>

            @if($test->certificate_code)
            <div class="mt-5 pt-4 border-t border-white/10 text-xs text-slate-300">
                <span>Mã chứng chỉ: </span>
                <strong class="font-mono text-amber-300 select-all">{{ $test->certificate_code }}</strong>
            </div>
            @endif
        </div>
    </div>
</section>

{{-- Skill Breakdown Section (3 Kỹ năng: Nghe, Đọc, Ngữ pháp) --}}
<section class="mb-12">
    <div class="mb-6">
        <p class="text-xs font-bold uppercase tracking-[0.24em] text-[#991b1b]">Phân tích năng lực</p>
        <h2 class="mt-1 text-2xl sm:text-3xl font-black text-slate-900">Bảng điểm chi tiết theo 3 kỹ năng</h2>
    </div>

    <div class="grid gap-6 sm:grid-cols-3">
        
        {{-- Skill 1: Listening --}}
        <div class="rounded-[2rem] border border-blue-100 bg-gradient-to-br from-white to-blue-50/50 p-6 shadow-xl shadow-slate-900/5">
            <div class="flex items-center justify-between">
                <div class="grid h-12 w-12 place-items-center rounded-2xl bg-blue-600 text-white shadow-md shadow-blue-600/20">
                    <i data-lucide="headphones" class="h-6 w-6"></i>
                </div>
                <span class="text-xs font-bold text-blue-700 bg-blue-100 px-3 py-1 rounded-full">
                    Kỹ năng Nghe
                </span>
            </div>

            <h3 class="mt-4 text-lg font-black text-slate-900">Phần 1: Nghe hiểu</h3>
            <p class="text-xs text-slate-500 mt-0.5">Khả năng tiếp nhận âm điệu, từ vựng & hội thoại</p>

            <div class="mt-6 flex items-baseline justify-between">
                <span class="text-4xl font-black text-blue-700">{{ $test->listening_score }}</span>
                <span class="text-xs font-bold text-slate-500">{{ $test->listening_correct }}/{{ $test->listening_total }} câu đúng</span>
            </div>

            {{-- Progress bar --}}
            <div class="mt-3 h-2.5 w-full overflow-hidden rounded-full bg-blue-100">
                <div class="h-full rounded-full bg-blue-600 transition-all duration-700"
                     style="width: {{ $test->listening_percent }}%"></div>
            </div>
            <p class="mt-2 text-right text-[11px] font-bold text-blue-600">{{ $test->listening_percent }}% độ chính xác</p>
        </div>

        {{-- Skill 2: Reading --}}
        <div class="rounded-[2rem] border border-emerald-100 bg-gradient-to-br from-white to-emerald-50/50 p-6 shadow-xl shadow-slate-900/5">
            <div class="flex items-center justify-between">
                <div class="grid h-12 w-12 place-items-center rounded-2xl bg-emerald-600 text-white shadow-md shadow-emerald-600/20">
                    <i data-lucide="book-open" class="h-6 w-6"></i>
                </div>
                <span class="text-xs font-bold text-emerald-700 bg-emerald-100 px-3 py-1 rounded-full">
                    Kỹ năng Đọc
                </span>
            </div>

            <h3 class="mt-4 text-lg font-black text-slate-900">Phần 2: Đọc hiểu</h3>
            <p class="text-xs text-slate-500 mt-0.5">Nhận diện chữ Hán, ngữ cảnh câu & văn bản</p>

            <div class="mt-6 flex items-baseline justify-between">
                <span class="text-4xl font-black text-emerald-700">{{ $test->reading_score }}</span>
                <span class="text-xs font-bold text-slate-500">{{ $test->reading_correct }}/{{ $test->reading_total }} câu đúng</span>
            </div>

            {{-- Progress bar --}}
            <div class="mt-3 h-2.5 w-full overflow-hidden rounded-full bg-emerald-100">
                <div class="h-full rounded-full bg-emerald-600 transition-all duration-700"
                     style="width: {{ $test->reading_percent }}%"></div>
            </div>
            <p class="mt-2 text-right text-[11px] font-bold text-emerald-600">{{ $test->reading_percent }}% độ chính xác</p>
        </div>

        {{-- Skill 3: Grammar --}}
        <div class="rounded-[2rem] border border-purple-100 bg-gradient-to-br from-white to-purple-50/50 p-6 shadow-xl shadow-slate-900/5">
            <div class="flex items-center justify-between">
                <div class="grid h-12 w-12 place-items-center rounded-2xl bg-purple-600 text-white shadow-md shadow-purple-600/20">
                    <i data-lucide="pen-tool" class="h-6 w-6"></i>
                </div>
                <span class="text-xs font-bold text-purple-700 bg-purple-100 px-3 py-1 rounded-full">
                    Ngữ pháp
                </span>
            </div>

            <h3 class="mt-4 text-lg font-black text-slate-900">Phần 3: Ngữ pháp & Cấu trúc</h3>
            <p class="text-xs text-slate-500 mt-0.5">Lượng từ, trợ từ, cấu trúc câu & trật tự từ</p>

            <div class="mt-6 flex items-baseline justify-between">
                <span class="text-4xl font-black text-purple-700">{{ $test->grammar_score }}</span>
                <span class="text-xs font-bold text-slate-500">{{ $test->grammar_correct }}/{{ $test->grammar_total }} câu đúng</span>
            </div>

            {{-- Progress bar --}}
            <div class="mt-3 h-2.5 w-full overflow-hidden rounded-full bg-purple-100">
                <div class="h-full rounded-full bg-purple-600 transition-all duration-700"
                     style="width: {{ $test->grammar_percent }}%"></div>
            </div>
            <p class="mt-2 text-right text-[11px] font-bold text-purple-600">{{ $test->grammar_percent }}% độ chính xác</p>
        </div>

    </div>
</section>

{{-- Question Review & Explanation Section --}}
<section x-data="{
    filter: 'all',
    playingId: null,
    async playAudio(text, id) {
        if (!text || this.playingId) return;
        this.playingId = id;
        try {
            await window.playChineseVoice(text);
        } catch(e) {
            console.error(e);
        } finally {
            setTimeout(() => {
                this.playingId = null;
                window.refreshIcons?.();
            }, 1000);
        }
    }
}" class="rounded-[2.5rem] border border-white/80 bg-white/80 p-6 sm:p-8 shadow-xl shadow-slate-900/5 backdrop-blur mb-12">
    
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-100 pb-6 mb-6">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.24em] text-[#991b1b]">Xem lại chi tiết bài làm</p>
            <h2 class="mt-1 text-2xl font-black text-slate-900">Đáp án & Giải thích chi tiết từng câu</h2>
        </div>

        {{-- Filter Buttons --}}
        <div class="flex flex-wrap items-center gap-2 text-xs font-bold">
            <button type="button"
                    @click="filter = 'all'"
                    class="rounded-xl px-3.5 py-2 transition"
                    :class="filter === 'all' ? 'bg-slate-900 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'">
                Tất cả ({{ count($test->details ?? []) }})
            </button>
            <button type="button"
                    @click="filter = 'correct'"
                    class="rounded-xl px-3.5 py-2 transition"
                    :class="filter === 'correct' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100'">
                Câu đúng ({{ $test->correct_answers }})
            </button>
            <button type="button"
                    @click="filter = 'incorrect'"
                    class="rounded-xl px-3.5 py-2 transition"
                    :class="filter === 'incorrect' ? 'bg-red-600 text-white shadow-sm' : 'bg-red-50 text-red-700 hover:bg-red-100'">
                Câu sai ({{ $test->total_questions - $test->correct_answers }})
            </button>
        </div>
    </div>

    {{-- Questions List --}}
    <div class="space-y-4">
        @foreach($test->details ?? [] as $index => $item)
        @php
            $isCorrect = $item['is_correct'] ?? false;
            $skill = $item['skill_type'] ?? 'reading';
        @endphp
        <div x-show="filter === 'all' || (filter === 'correct' && {{ $isCorrect ? 'true' : 'false' }}) || (filter === 'incorrect' && {{ $isCorrect ? 'false' : 'true' }})"
             class="rounded-3xl border p-5 sm:p-6 transition {{ $isCorrect ? 'border-emerald-200/80 bg-emerald-50/20' : 'border-red-200/80 bg-red-50/20' }}">
            
            {{-- Item Header --}}
            <div class="flex flex-wrap items-center justify-between gap-2 mb-3">
                <div class="flex items-center gap-2">
                    <span class="grid h-7 w-7 place-items-center rounded-xl text-xs font-black text-white {{ $isCorrect ? 'bg-emerald-600' : 'bg-red-600' }}">
                        {{ $index + 1 }}
                    </span>
                    <span class="text-xs font-bold uppercase tracking-wider px-2.5 py-0.5 rounded-lg {{ $skill === 'listening' ? 'bg-blue-100 text-blue-800' : ($skill === 'grammar' ? 'bg-purple-100 text-purple-800' : 'bg-emerald-100 text-emerald-800') }}">
                        {{ $skill === 'listening' ? '🎧 Nghe hiểu' : ($skill === 'grammar' ? '✍️ Ngữ pháp' : '📖 Đọc hiểu') }}
                    </span>
                </div>

                <div>
                    @if($isCorrect)
                    <span class="inline-flex items-center gap-1 text-xs font-bold text-emerald-700 bg-emerald-100 px-3 py-1 rounded-full">
                        <i data-lucide="check" class="h-3.5 w-3.5"></i> Chính xác
                    </span>
                    @else
                    <span class="inline-flex items-center gap-1 text-xs font-bold text-red-700 bg-red-100 px-3 py-1 rounded-full">
                        <i data-lucide="x" class="h-3.5 w-3.5"></i> Chưa đúng
                    </span>
                    @endif
                </div>
            </div>

            {{-- Listening Audio Replay Button --}}
            @if(!empty($item['audio_text']))
            <div class="mb-3 flex items-center gap-3">
                <button type="button"
                        @click="playAudio('{{ addslashes($item['audio_text']) }}', {{ $index }})"
                        class="inline-flex items-center gap-1.5 rounded-xl bg-blue-600 text-white px-3 py-1.5 text-xs font-bold shadow-sm hover:bg-blue-700 transition active:scale-95">
                    <i data-lucide="volume-2" class="h-3.5 w-3.5"></i>
                    <span>Nghe lại audio</span>
                </button>
                <span class="text-xs text-slate-500 font-mono italic">"{{ $item['audio_text'] }}"</span>
            </div>
            @endif

            {{-- Question text & pinyin --}}
            <p class="text-base font-bold text-slate-900">{{ $item['question'] }}</p>
            @if(!empty($item['pinyin']))
            <p class="text-xs font-semibold text-[#991b1b] mt-0.5">{{ $item['pinyin'] }}</p>
            @endif

            {{-- User Answer vs Correct Answer Box --}}
            <div class="mt-4 grid gap-3 sm:grid-cols-2 text-xs">
                <div class="rounded-2xl p-3 border {{ $isCorrect ? 'bg-emerald-50 border-emerald-200 text-emerald-950' : 'bg-red-50 border-red-200 text-red-950' }}">
                    <p class="font-bold text-slate-500">Câu trả lời của bạn:</p>
                    <p class="mt-1 font-semibold text-sm">{{ $item['user_answer'] ?: '(Bỏ trống)' }}</p>
                </div>
                <div class="rounded-2xl p-3 border bg-emerald-50 border-emerald-200 text-emerald-950">
                    <p class="font-bold text-emerald-700">Đáp án chuẩn xác:</p>
                    <p class="mt-1 font-semibold text-sm">{{ $item['correct_answer'] }}</p>
                </div>
            </div>

            {{-- Explanation --}}
            @if(!empty($item['explanation']))
            <div class="mt-4 rounded-2xl bg-slate-50 p-4 border border-slate-200 text-xs text-slate-700">
                <p class="font-bold text-slate-900 flex items-center gap-1.5 mb-1">
                    <i data-lucide="lightbulb" class="h-3.5 w-3.5 text-amber-500"></i>
                    Giải thích chi tiết:
                </p>
                <p class="leading-relaxed">{{ $item['explanation'] }}</p>
            </div>
            @endif

        </div>
        @endforeach
    </div>
</section>

@endsection
