<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Learn Chinese - Nền tảng học tiếng Trung hiện đại, bài bản từ HSK 1 đến HSK 6.">

    <title>@yield('title', 'Tài khoản | Learn Chinese')</title>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-[#f4ede3] text-slate-950 antialiased flex flex-col justify-between selection:bg-[#991b1b] selection:text-white">

    {{-- Top bar --}}
    <header class="p-4 sm:p-6 flex items-center justify-between max-w-5xl mx-auto w-full">
        <a href="{{ route('home') }}" class="flex items-center gap-3 group">
            <div class="grid h-11 w-11 place-items-center rounded-2xl bg-[#991b1b] text-white shadow-md shadow-red-950/20 transition group-hover:scale-105">
                <span class="text-lg font-black">中</span>
            </div>
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.22em] text-[#991b1b]">Learn Chinese</p>
                <p class="text-xs text-slate-500 font-medium">Học tiếng Trung</p>
            </div>
        </a>

        <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 hover:text-[#991b1b] bg-white/80 hover:bg-white px-4 py-2 rounded-full border border-slate-200 shadow-sm transition hover:shadow">
            <i data-lucide="arrow-left" class="h-3.5 w-3.5"></i>
            <span>Về trang chủ</span>
        </a>
    </header>

    {{-- Main Container: Modern Split Bento Card --}}
    <main class="flex-1 flex items-center justify-center p-4 sm:p-6 lg:p-8">
        <div class="w-full max-w-4xl overflow-hidden rounded-[2.5rem] border border-white/80 bg-white/90 shadow-2xl shadow-slate-950/10 backdrop-blur-xl grid lg:grid-cols-[1fr_1.15fr]">
            
            {{-- Left Banner: Brand & Chinese Culture Theme (Visible on lg screens) --}}
            <div class="relative hidden lg:flex flex-col justify-between overflow-hidden bg-gradient-to-br from-slate-950 via-[#1c1212] to-slate-950 p-10 text-white">
                
                {{-- Decorative Watermark Character --}}
                <span class="pointer-events-none absolute -bottom-10 -right-8 select-none text-[13rem] font-black leading-none text-white/[0.03]">
                    学
                </span>

                {{-- Top Accents --}}
                <div class="absolute inset-x-0 top-0 h-1.5 bg-gradient-to-r from-amber-400 via-[#991b1b] to-amber-300"></div>

                {{-- Header within banner --}}
                <div class="relative z-10">
                    <div class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3.5 py-1.5 text-xs font-bold text-amber-300 backdrop-blur-md border border-white/10">
                        <span class="h-2 w-2 rounded-full bg-amber-400 animate-pulse"></span>
                        Hành trình vạn dặm bắt đầu từ đây
                    </div>

                    <h2 class="mt-6 text-3xl font-black leading-tight tracking-tight">
                        Chinh phục tiếng Trung <br />
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-amber-300 via-amber-200 to-white">thông minh & dễ nhớ.</span>
                    </h2>

                    <p class="mt-4 text-xs text-slate-300 leading-relaxed max-w-sm">
                        Kết hợp Flashcard lặp lại ngắt quãng (SM-2), giọng phát âm AI chuẩn Bắc Kinh và bài tập trắc nghiệm theo chuẩn HSK 1 – HSK 6.
                    </p>
                </div>

                {{-- Cultural Quote Box --}}
                <div class="relative z-10 my-8 rounded-2xl bg-white/[0.06] border border-white/10 p-5 backdrop-blur-sm">
                    <p class="text-xs font-bold uppercase tracking-[0.2em] text-amber-400">Khổng Tử • Luận Ngữ</p>
                    <p class="mt-2 text-lg font-black text-white font-serif tracking-wider">“学而时习之，不亦说乎？”</p>
                    <p class="mt-1 text-[11px] text-slate-400 font-mono">Xué ér shí xí zhī, bù yì yuè hū?</p>
                    <p class="mt-1.5 text-xs text-slate-300 italic">“Học mà thường xuyên ôn tập, chẳng vui lắm sao?”</p>
                </div>

                {{-- Feature Badges --}}
                <div class="relative z-10 grid grid-cols-2 gap-2.5 text-[11px] font-semibold text-slate-300">
                    <div class="flex items-center gap-2 rounded-xl bg-white/[0.04] p-2.5 border border-white/5">
                        <i data-lucide="layers" class="h-4 w-4 text-amber-400 shrink-0"></i>
                        <span>5.000+ Flashcard</span>
                    </div>
                    <div class="flex items-center gap-2 rounded-xl bg-white/[0.04] p-2.5 border border-white/5">
                        <i data-lucide="volume-2" class="h-4 w-4 text-emerald-400 shrink-0"></i>
                        <span>Phát âm AI bản xứ</span>
                    </div>
                    <div class="flex items-center gap-2 rounded-xl bg-white/[0.04] p-2.5 border border-white/5">
                        <i data-lucide="pen-tool" class="h-4 w-4 text-sky-400 shrink-0"></i>
                        <span>Tập viết Hán tự</span>
                    </div>
                    <div class="flex items-center gap-2 rounded-xl bg-white/[0.04] p-2.5 border border-white/5">
                        <i data-lucide="target" class="h-4 w-4 text-rose-400 shrink-0"></i>
                        <span>Quiz chuẩn HSK</span>
                    </div>
                </div>

            </div>

            {{-- Right Panel: Form Slot --}}
            <div class="flex flex-col justify-center p-7 sm:p-10 lg:p-12 relative bg-white">
                {{ $slot }}
            </div>

        </div>
    </main>

    {{-- Simple Copyright Footer --}}
    <footer class="py-4 text-center text-xs text-slate-500">
        © {{ date('Y') }} Learn Chinese. Nền tảng học tiếng Trung trực quan.
    </footer>

    <x-toast />
</body>

</html>