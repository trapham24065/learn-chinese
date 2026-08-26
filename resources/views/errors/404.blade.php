@extends('layouts.app')

@section('title', '404 - Không tìm thấy trang | Learn Chinese')

@section('content')
<div class="py-6 sm:py-10 max-w-4xl mx-auto space-y-8">

    {{-- Main 404 Hero Bento Card --}}
    <div class="relative overflow-hidden rounded-[2.5rem] bg-slate-950 p-8 sm:p-12 lg:p-16 text-white shadow-2xl shadow-slate-950/20">
        {{-- Decorative background gradients & Chinese watermark --}}
        <div class="absolute -right-10 -bottom-10 select-none text-[16rem] font-black leading-none text-white/[0.03] pointer-events-none">
            迷
        </div>
        <div class="absolute -left-12 -top-12 h-48 w-48 rounded-full bg-[#991b1b]/30 blur-3xl pointer-events-none"></div>
        <div class="absolute right-10 top-10 h-36 w-36 rounded-full bg-amber-400/15 blur-2xl pointer-events-none"></div>
        <div class="absolute inset-x-0 top-0 h-1.5 bg-gradient-to-r from-[#991b1b] via-amber-400 to-[#991b1b]"></div>

        <div class="relative z-10 grid gap-8 lg:grid-cols-[1.2fr_0.8fr] lg:items-center">
            
            {{-- Left column: Error Info & Actions --}}
            <div class="space-y-6 text-left">
                <div class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/10 px-4 py-1.5 text-xs font-bold uppercase tracking-widest text-amber-300 backdrop-blur">
                    <span class="h-2 w-2 rounded-full bg-[#991b1b] animate-ping"></span>
                    Mã lỗi 404 • Không tìm thấy trang
                </div>

                <div>
                    <h1 class="text-4xl font-black tracking-tight sm:text-5xl lg:text-6xl text-white">
                        Bạn có vẻ đã <br>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-amber-300 via-orange-300 to-red-400">bị lạc đường?</span>
                    </h1>
                    <p class="mt-4 text-base leading-7 text-slate-300 sm:text-lg max-w-xl">
                        Đường dẫn này không tồn tại hoặc đã được di chuyển. Đừng để việc này làm gián đoạn buổi học, hãy quay lại các phần chính nhé!
                    </p>
                </div>

                {{-- Action Quick Buttons --}}
                <div class="flex flex-wrap gap-3 pt-2">
                    <a href="{{ route('home') }}"
                       class="inline-flex items-center gap-2 rounded-full bg-[#991b1b] px-6 py-3.5 text-sm font-bold text-white shadow-xl shadow-red-950/30 transition hover:-translate-y-0.5 hover:bg-red-800 active:scale-95">
                        <i data-lucide="house" class="h-4 w-4"></i>
                        <span>Về trang chủ</span>
                    </a>
                    <a href="{{ route('flashcards') }}"
                       class="inline-flex items-center gap-2 rounded-full bg-white/10 border border-white/15 px-6 py-3.5 text-sm font-semibold text-white backdrop-blur transition hover:-translate-y-0.5 hover:bg-white/20 active:scale-95">
                        <i data-lucide="layers" class="h-4 w-4"></i>
                        <span>Ôn Flashcard</span>
                    </a>
                    <a href="{{ route('quiz') }}"
                       class="inline-flex items-center gap-2 rounded-full bg-amber-400 text-slate-950 px-6 py-3.5 text-sm font-bold shadow-lg shadow-amber-400/20 transition hover:-translate-y-0.5 hover:bg-amber-300 active:scale-95">
                        <i data-lucide="target" class="h-4 w-4"></i>
                        <span>Làm Quiz</span>
                    </a>
                </div>
            </div>

            {{-- Right column: Big Character & Audio Badge --}}
            <div class="flex flex-col items-center justify-center">
                <div class="w-full max-w-xs rounded-3xl border border-white/15 bg-white/5 p-6 text-center backdrop-blur-md shadow-inner">
                    <p class="text-xs font-bold uppercase tracking-[0.25em] text-slate-400">Từ vựng tình huống</p>
                    
                    {{-- Big Hanzi --}}
                    <div class="my-4">
                        <span class="text-7xl sm:text-8xl font-black text-amber-300 tracking-tight leading-none drop-shadow-md">
                            迷路
                        </span>
                    </div>

                    {{-- Pinyin & Meaning with Audio --}}
                    <div class="flex items-center justify-center gap-2 rounded-2xl bg-white/10 py-2.5 px-4">
                        <span class="text-sm font-bold text-amber-200 uppercase tracking-widest">mílù</span>
                        <span class="text-slate-400">•</span>
                        <span class="text-sm font-medium text-slate-200">Lạc đường</span>
                        <button type="button" 
                                onclick="window.playChineseVoice('迷路')" 
                                class="ml-1 flex h-8 w-8 items-center justify-center rounded-full bg-amber-400 text-slate-950 transition hover:scale-110 active:scale-95 shadow-sm" 
                                title="Phát âm từ này">
                            <i data-lucide="volume-2" class="h-4 w-4"></i>
                        </button>
                    </div>

                    <p class="mt-3 text-xs text-slate-400">
                        Ví dụ: 我迷路了 (Wǒ mílù le - Tôi bị lạc rồi)
                    </p>
                </div>
            </div>

        </div>
    </div>

    {{-- Bottom Secondary Grid --}}
    <div class="grid gap-6 sm:grid-cols-2">
        
        {{-- Chinese Proverb Card --}}
        <div class="rounded-[2rem] border border-white/80 bg-white/80 p-6 shadow-xl shadow-slate-900/5 backdrop-blur flex items-start gap-4">
            <div class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-red-50 text-[#991b1b]">
                <i data-lucide="quote" class="h-5 w-5"></i>
            </div>
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-[#991b1b]">Thành ngữ mỗi ngày</p>
                <h3 class="mt-1 text-lg font-black text-slate-900">千里之行，始于足下</h3>
                <p class="text-xs text-slate-500 font-medium italic mt-0.5">Qiānlǐ zhī xíng, shǐyú zúxià</p>
                <p class="mt-2 text-xs text-slate-600 leading-relaxed">
                    "Hành trình vạn dặm bắt đầu từ một bước chân." Mỗi sai sót đều là một bước tiến trên con đường học tập.
                </p>
            </div>
        </div>

        {{-- Quick Navigation Navigation Guide --}}
        <div class="rounded-[2rem] border border-white/80 bg-white/80 p-6 shadow-xl shadow-slate-900/5 backdrop-blur flex items-start gap-4">
            <div class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-amber-50 text-amber-700">
                <i data-lucide="compass" class="h-5 w-5"></i>
            </div>
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-amber-800">Lộ trình học tập</p>
                <h3 class="mt-1 text-lg font-black text-slate-900">Khám phá các cấp độ HSK</h3>
                <p class="mt-2 text-xs text-slate-600 leading-relaxed">
                    Hệ thống 6 cấp độ từ HSK 1 đến HSK 6 với hơn 5.000 từ vựng và bài tập đang chờ đón bạn.
                </p>
                <a href="{{ route('hsk.overview') }}" class="mt-3 inline-flex items-center gap-1.5 text-xs font-bold text-[#991b1b] hover:underline">
                    Xem lộ trình HSK 1 - 6 <i data-lucide="arrow-right" class="h-3.5 w-3.5"></i>
                </a>
            </div>
        </div>

    </div>

</div>
@endsection

