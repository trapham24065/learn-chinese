@extends('layouts.app')

@section('title', 'Lộ trình HSK | Chinese Deck')

@section('content')

<section class="py-4 lg:py-8">
    <p class="text-sm font-semibold uppercase tracking-[0.28em] text-[#991b1b]">Hệ thống chứng chỉ quốc tế</p>
    <h1 class="mt-4 text-5xl font-black tracking-tight text-slate-950 sm:text-6xl">Lộ trình HSK</h1>
    <p class="mt-5 max-w-2xl text-lg leading-8 text-slate-700">
        HSK (汉语水平考试) là bộ tiêu chuẩn đánh giá trình độ tiếng Trung được công nhận toàn cầu. Học theo lộ trình từ HSK 1 đến HSK 6 để tiến từng bước một cách hệ thống.
    </p>
</section>

<section class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
    @foreach($levelData as $level => $data)
    @php
    $isDone = $data['completed_count'] > 0 && $data['progress_pct'] >= 100;
    $hasContent = $data['flashcard_count'] > 0 || $data['lesson_count'] > 0;
    @endphp
    <a href="{{ route('hsk.show', $level) }}"
        class="group relative overflow-hidden rounded-[2rem] border bg-white p-6 sm:p-7 shadow-xl shadow-slate-900/5 transition hover:-translate-y-1 hover:shadow-2xl hover:shadow-slate-900/10 {{ $data['border'] }}"
    >

        {{-- Top accent line --}}
        <div class="absolute inset-x-0 top-0 h-1.5" style="background: {{ $data['color'] }}"></div>

        {{-- HSK badge --}}
        <div class="flex items-center justify-between">
            <span class="rounded-full px-3 py-1 text-xs font-black uppercase tracking-widest {{ $data['badge'] }}">
                {{ $data['label'] }}
            </span>
            @if($isDone)

            {{-- Đã hoàn thành --}}
            <span
                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-emerald-50 text-emerald-600"
                title="Đã hoàn thành">
                <svg
                    class="h-5 w-5"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2.5"
                    stroke-linecap="round"
                    stroke-linejoin="round">
                    <path d="M20 6 9 17l-5-5" />
                </svg>
            </span>

            @elseif($data['progress_pct'] > 0)

            {{-- Đang học --}}
            <span
                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-blue-50 text-blue-600"
                title="Đang học">
                <svg
                    class="h-5 w-5"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" />
                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z" />
                </svg>
            </span>

            @else

            {{-- Chưa bắt đầu --}}
            <span
                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-slate-50 text-slate-400"
                title="Chưa bắt đầu">
                <svg
                    class="h-5 w-5"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round">
                    <rect x="3" y="11" width="18" height="10" rx="2" />
                    <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                </svg>
            </span>

            @endif
        </div>

        {{-- Chinese chars decoration --}}
        <p class="mt-4 text-6xl font-black leading-none tracking-tight text-slate-100 select-none transition group-hover:text-slate-200"
            style="color: {{ $data['color'] }}1A">
            {{ ['一','二','三','四','五','六'][$level - 1] }}
        </p>

        <h2 class="mt-3 text-xl font-black text-slate-900">{{ $data['label'] }}</h2>
        <p class="mt-1 text-sm leading-6 text-slate-600">{{ $data['description'] }}</p>

        <div class="mt-5 grid grid-cols-3 gap-2 text-center text-xs">
            <div class="rounded-2xl bg-slate-50 p-2">
                <p class="font-black text-slate-900">~{{ number_format($data['vocab_count']) }}</p>
                <p class="text-slate-500">từ vựng</p>
            </div>
            <div class="rounded-2xl bg-slate-50 p-2">
                <p class="font-black text-slate-900">{{ $data['lesson_count'] }}</p>
                <p class="text-slate-500">bài học</p>
            </div>
            <div class="rounded-2xl bg-slate-50 p-2">
                <p class="font-black text-slate-900">{{ $data['flashcard_count'] }}</p>
                <p class="text-slate-500">flashcard</p>
            </div>
        </div>

        @if($data['lesson_count'] > 0)
        <div class="mt-5">
            <div class="flex items-center justify-between mb-1.5 text-xs font-semibold text-slate-600">
                <span>Tiến độ</span>
                <span>{{ $data['completed_count'] }}/{{ $data['lesson_count'] }} bài</span>
            </div>
            <div class="h-2 rounded-full bg-slate-100 overflow-hidden">
                <div class="h-2 rounded-full transition-all duration-700"
                    style="width: {{ $data['progress_pct'] }}%; background: {{ $data['color'] }}"></div>
            </div>
        </div>
        @else
        <p class="mt-5 text-xs text-slate-400 italic">Admin chưa thêm nội dung cho cấp này.</p>
        @endif

        <div class="mt-5 flex items-center gap-1.5 text-sm font-bold transition group-hover:gap-2.5" style="color: {{ $data['color'] }}">
            Bắt đầu học <i data-lucide="arrow-right" class="h-4 w-4 transition-transform group-hover:translate-x-1"></i>
        </div>
    </a>
    @endforeach
</section>

{{-- HSK Mock Exam Callout Banner --}}
<section class="mt-10 rounded-[2.5rem] bg-gradient-to-r from-[#991b1b] via-[#7f1d1d] to-slate-950 p-8 sm:p-10 text-white shadow-2xl shadow-red-950/20">
    <div class="grid gap-6 lg:grid-cols-[1fr_auto] lg:items-center">
        <div>
            <div class="inline-flex items-center gap-2 rounded-full bg-amber-400/20 px-3.5 py-1 text-xs font-black uppercase tracking-widest text-amber-300 border border-amber-300/30">
                <i data-lucide="award" class="h-4 w-4"></i>
                <span>Tính năng mới</span>
            </div>
            <h2 class="mt-3 text-3xl font-black tracking-tight sm:text-4xl">Thi thử HSK mô phỏng có cấp chứng chỉ</h2>
            <p class="mt-3 max-w-2xl text-white/80 leading-7 text-sm sm:text-base">
                Trải nghiệm phòng thi thật với <strong>30 - 50 câu hỏi</strong>, đồng hồ đếm ngược, phân tích chi tiết 3 kỹ năng <strong>Nghe · Đọc hiểu · Ngữ pháp</strong> và nhận <strong>Chứng chỉ Online</strong> ngay khi đạt điểm đỗ!
            </p>
        </div>
        <a href="{{ route('hsk.mock.index') }}"
            class="shrink-0 inline-flex items-center gap-2.5 justify-center rounded-2xl bg-amber-300 hover:bg-amber-200 px-7 py-4 text-sm font-black text-slate-950 shadow-xl shadow-amber-300/20 transition hover:-translate-y-0.5 active:scale-95">
            <i data-lucide="play" class="h-4 w-4 fill-current"></i>
            <span>Vào phòng thi HSK ngay</span>
        </a>
    </div>
</section>

{{-- Info banner --}}
<section class="mt-8 rounded-[2rem] bg-slate-950 p-8 text-white shadow-2xl shadow-slate-950/20">
    <div class="grid gap-6 lg:grid-cols-[1fr_auto] lg:items-center">
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.28em] text-amber-300/80">Tại sao học theo HSK?</p>
            <h2 class="mt-3 text-3xl font-black tracking-tight sm:text-4xl">Lộ trình học rõ ràng, có đích đến</h2>
            <p class="mt-4 max-w-2xl text-white/70 leading-7">
                Thay vì học lan man, HSK cho bạn biết chính xác cần học bao nhiêu từ và ở mức nào để đạt chứng chỉ quốc tế.
                Hệ thống nội dung trên Chinese Deck được gắn nhãn HSK để bạn luôn biết mình đang ở đâu trên lộ trình.
            </p>
        </div>
        <a href="{{ route('flashcards') }}"
            class="shrink-0 inline-flex items-center gap-2 justify-center rounded-full bg-slate-800 hover:bg-slate-700 px-6 py-3 text-sm font-bold text-white transition hover:-translate-y-0.5 border border-slate-700">
            Bắt đầu với Flashcard
            <i data-lucide="arrow-right" class="h-4 w-4"></i>
        </a>
    </div>
</section>

@endsection