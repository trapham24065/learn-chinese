@extends('layouts.app')

@section('title', '404 - Không tìm thấy trang | Learn Chinese')

@section('content')
<div class="flex min-h-[70vh] flex-col items-center justify-center py-8">
    <div class="w-full max-w-2xl text-center">
        
        {{-- 404 Badge & Chinese theme illustration --}}
        <div class="relative mx-auto mb-6 inline-block">
            <div class="relative overflow-hidden rounded-[2.5rem] bg-slate-950 px-10 py-8 text-white shadow-2xl shadow-slate-950/20">
                <div class="absolute -right-6 -top-6 text-9xl font-black text-white/5 select-none">
                    迷
                </div>
                <div class="relative z-10 flex flex-col items-center">
                    <span class="inline-flex items-center gap-2 rounded-full bg-red-500/20 px-3.5 py-1 text-xs font-bold uppercase tracking-widest text-red-300 border border-red-500/30">
                        Lỗi 404
                    </span>
                    <h1 class="mt-3 text-7xl font-black tracking-tight text-white sm:text-8xl">
                        404
                    </h1>
                    <div class="mt-2 flex items-center gap-2">
                        <span class="text-2xl font-black text-amber-300">迷路</span>
                        <span class="text-sm font-semibold text-slate-400">/ mílù /</span>
                        <span class="text-sm text-slate-300">Lạc đường</span>
                        <button type="button" 
                                onclick="window.playChineseVoice('迷路')" 
                                class="ml-1 text-amber-300/80 transition hover:text-amber-300 focus:outline-none" 
                                title="Nghe phát âm">
                            <i data-lucide="volume-2" class="h-4 w-4"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main Message --}}
        <h2 class="text-2xl font-black tracking-tight text-slate-950 sm:text-3xl">
            Ối! Trang bạn tìm kiếm không tồn tại
        </h2>
        <p class="mt-3 text-base leading-7 text-slate-600 sm:text-lg max-w-lg mx-auto">
            Có vẻ như đường dẫn đã bị thay đổi, bị xóa hoặc bạn đã gõ nhầm địa chỉ. Đừng lo, hãy tiếp tục hành trình học tiếng Trung nhé!
        </p>

        {{-- Action Buttons --}}
        <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
            <a href="{{ route('home') }}"
               class="inline-flex items-center gap-2 rounded-full bg-[#991b1b] px-6 py-3 text-sm font-bold text-white shadow-lg shadow-red-950/20 transition hover:-translate-y-0.5 hover:bg-red-800">
                <i data-lucide="house" class="h-4 w-4"></i>
                Về trang chủ
            </a>
            <a href="{{ route('flashcards') }}"
               class="inline-flex items-center gap-2 rounded-full border border-slate-300 bg-white px-6 py-3 text-sm font-semibold text-slate-700 shadow-sm transition hover:-translate-y-0.5 hover:border-[#991b1b] hover:text-[#991b1b]">
                <i data-lucide="layers" class="h-4 w-4"></i>
                Học Flashcard
            </a>
            <a href="{{ route('quiz') }}"
               class="inline-flex items-center gap-2 rounded-full border border-slate-300 bg-white px-6 py-3 text-sm font-semibold text-slate-700 shadow-sm transition hover:-translate-y-0.5 hover:border-[#991b1b] hover:text-[#991b1b]">
                <i data-lucide="target" class="h-4 w-4"></i>
                Làm Quiz
            </a>
            <a href="{{ route('hsk.overview') }}"
               class="inline-flex items-center gap-2 rounded-full border border-slate-300 bg-white px-6 py-3 text-sm font-semibold text-slate-700 shadow-sm transition hover:-translate-y-0.5 hover:border-[#991b1b] hover:text-[#991b1b]">
                <i data-lucide="graduation-cap" class="h-4 w-4"></i>
                Lộ trình HSK
            </a>
        </div>

        {{-- Mini Chinese Learning Quote --}}
        <div class="mt-10 rounded-3xl border border-white/80 bg-white/75 p-5 shadow-xl shadow-slate-900/5 backdrop-blur max-w-md mx-auto">
            <p class="text-xs font-bold uppercase tracking-wider text-[#991b1b]">💡 Thành ngữ tiếng Trung:</p>
            <p class="mt-1.5 text-base font-bold text-slate-800">千里之行，始于足下</p>
            <p class="text-xs text-slate-500 italic mt-0.5">Qiānlǐ zhī xíng, shǐyú zúxià</p>
            <p class="text-xs text-slate-600 mt-1">"Hành trình vạn dặm bắt đầu từ một bước chân."</p>
        </div>

    </div>
</div>
@endsection
