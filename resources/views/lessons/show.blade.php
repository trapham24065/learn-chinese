@extends('layouts.app')

@section('title', $lesson->title . ' | Chinese Deck')

@section('content')
<div class="space-y-8">
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

    {{-- Hero Section --}}
    <section class="relative overflow-hidden rounded-[2.5rem] bg-slate-950 px-6 py-12 text-white sm:px-12 lg:py-16 shadow-2xl shadow-slate-950/20">
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
                </div>
                
                <h1 class="mt-6 text-4xl font-black tracking-tight sm:text-5xl lg:text-6xl text-white">
                    {{ $lesson->title }}
                </h1>
                
                <p class="mt-4 text-lg leading-8 text-slate-300">
                    {{ $lesson->summary }}
                </p>
            </div>
        </div>
    </section>

    {{-- Main Content & Actions --}}
    <div class="grid gap-8 lg:grid-cols-[1fr_320px]">
        
        {{-- Lesson Content --}}
        <div class="space-y-8">
            <section class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-xl shadow-slate-900/5 sm:p-10">
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
            </section>
        </div>

        {{-- Sidebar Actions --}}
        <div class="space-y-6">
            
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
@endsection
