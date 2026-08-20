<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Chinese Deck - web học tiếng Trung với bài học ngắn, flashcard, quiz và tiến độ học tập.">

    <title>Learn Chinese</title>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-[#f4ede3] text-slate-950 antialiased lg:pl-72">
    <aside class="fixed inset-y-0 left-0 z-40 hidden w-72 border-r border-white/70 bg-white/80 px-6 py-6 shadow-2xl shadow-slate-950/10 backdrop-blur lg:flex lg:flex-col">
        <a href="{{ route('home') }}" class="flex items-center gap-3 rounded-3xl border border-slate-200 bg-slate-950 px-4 py-4 text-white shadow-lg shadow-slate-950/15">
            <div class="grid h-12 w-12 place-items-center rounded-2xl bg-[#991b1b]">
                <span class="text-xl font-black">中</span>
            </div>
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.24em] text-amber-200/80">Learn Chinese</p>
                <p class="text-sm text-slate-300">Học tiếng Trung</p>
            </div>
        </a>

        <nav class="mt-6 space-y-2 text-sm font-medium">

            {{-- Dashboard --}}
            <a href="{{ route('dashboard') }}"
                class="group flex items-center justify-between rounded-2xl px-4 py-3 transition {{ request()->routeIs('dashboard') ? 'bg-[#991b1b] text-white shadow-lg shadow-red-950/15' : 'text-slate-700 hover:bg-slate-100 hover:text-[#991b1b]' }}">
                <span class="flex items-center gap-3">
                    <span class="grid h-8 w-8 place-items-center rounded-xl {{ request()->routeIs('dashboard') ? 'bg-white/10' : 'bg-slate-100' }}">
                        <i data-lucide="bar-chart-3" class="h-4 w-4"></i>
                    </span>
                    Tổng quan
                </span>
            </a>

            {{-- Trang chủ --}}
            <a href="{{ route('home') }}"
                class="group flex items-center justify-between rounded-2xl px-4 py-3 transition {{ request()->routeIs('home') ? 'bg-[#991b1b] text-white shadow-lg shadow-red-950/15' : 'text-slate-700 hover:bg-slate-100 hover:text-[#991b1b]' }}">
                <span class="flex items-center gap-3">
                    <span class="grid h-8 w-8 place-items-center rounded-xl {{ request()->routeIs('home') ? 'bg-white/10' : 'bg-slate-100' }}">
                        <i data-lucide="house" class="h-4 w-4"></i>
                    </span>
                    Trang chủ
                </span>
            </a>

            {{-- Flashcard --}}
            <a href="{{ route('flashcards') }}"
                class="group flex items-center justify-between rounded-2xl px-4 py-3 transition {{ request()->routeIs('flashcards') ? 'bg-[#991b1b] text-white shadow-lg shadow-red-950/15' : 'text-slate-700 hover:bg-slate-100 hover:text-[#991b1b]' }}">
                <span class="flex items-center gap-3">
                    <span class="grid h-8 w-8 place-items-center rounded-xl {{ request()->routeIs('flashcards') ? 'bg-white/10' : 'bg-slate-100' }}">
                        <i data-lucide="layers" class="h-4 w-4"></i>
                    </span>
                    Thẻ ghi nhớ
                </span>
            </a>

            {{-- Quiz --}}
            <a href="{{ route('quiz') }}"
                class="group flex items-center justify-between rounded-2xl px-4 py-3 transition {{ request()->routeIs('quiz') ? 'bg-[#991b1b] text-white shadow-lg shadow-red-950/15' : 'text-slate-700 hover:bg-slate-100 hover:text-[#991b1b]' }}">
                <span class="flex items-center gap-3">
                    <span class="grid h-8 w-8 place-items-center rounded-xl {{ request()->routeIs('quiz') ? 'bg-white/10' : 'bg-slate-100' }}">
                        <i data-lucide="file-text" class="h-4 w-4"></i>
                    </span>
                    Câu hỏi
                </span>
            </a>

            {{-- HSK --}}
            <a href="{{ route('hsk.overview') }}"
                class="group flex items-center justify-between rounded-2xl px-4 py-3 transition {{ request()->routeIs('hsk.*') ? 'bg-[#991b1b] text-white shadow-lg shadow-red-950/15' : 'text-slate-700 hover:bg-slate-100 hover:text-[#991b1b]' }}">
                <span class="flex items-center gap-3">
                    <span class="grid h-8 w-8 place-items-center rounded-xl {{ request()->routeIs('hsk.*') ? 'bg-white/10' : 'bg-slate-100' }}">
                        <i data-lucide="graduation-cap" class="h-4 w-4"></i>
                    </span>
                    HSK
                </span>
            </a>

        </nav>

        {{-- Dynamic Streak Box --}}
        <div class="mt-4 rounded-[1.75rem] bg-[linear-gradient(135deg,_#111827_0%,_#1f2937_45%,_#991b1b_100%)] p-4 text-white shadow-xl shadow-slate-950/15">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-amber-200/80">Streak học tập</p>
                <i data-lucide="flame" class="h-4 w-4 text-amber-400"></i>
            </div>
            <p class="mt-2 text-2xl font-black">{{ str_pad($sidebarStreak ?? 0, 2, '0', STR_PAD_LEFT) }}</p>
            <p class="text-xs text-slate-300">ngày học liên tiếp</p>
            <div class="mt-3 h-1.5 rounded-full bg-white/10">
                <div class="h-1.5 rounded-full bg-gradient-to-r from-amber-300 to-red-400" style="width: {{ min(100, max(15, ($sidebarStreak ?? 0) * 15)) }}%"></div>
            </div>
        </div>

        {{-- User Profile & Logout Bottom Bar --}}
        <div class="mt-auto pt-4 border-t border-slate-200/60">
            @if ($authUser)
            <div class="flex items-center justify-between gap-2 rounded-2xl bg-slate-50 p-2.5">
                <a href="{{ route('profile.edit') }}" class="flex items-center gap-2.5 overflow-hidden hover:opacity-80 transition">
                    <div class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-slate-900 text-xs font-bold text-white">
                        {{ strtoupper(substr($authUser->name ?? 'U', 0, 1)) }}
                    </div>
                    <div class="truncate text-left">
                        <p class="truncate text-xs font-bold text-slate-900">{{ $authUser->name }}</p>
                        <p class="truncate text-[10px] text-slate-500">{{ $authUser->email }}</p>
                    </div>
                </a>
                <form method="POST" action="{{ route('logout') }}" class="shrink-0">
                    @csrf
                    <button type="submit" title="Đăng xuất"
                        class="grid h-8 w-8 place-items-center rounded-xl bg-white text-slate-600 hover:bg-red-50 hover:text-red-600 border border-slate-200 transition">
                        <i data-lucide="log-out" class="h-4 w-4"></i>
                    </button>
                </form>
            </div>
            @else
            <div class="flex items-center gap-2">
                <a href="{{ route('login') }}" class="flex-1 text-center rounded-xl bg-slate-100 py-2 text-xs font-bold text-slate-800 hover:bg-slate-200 transition">Đăng nhập</a>
                <a href="{{ route('register') }}" class="flex-1 text-center rounded-xl bg-[#991b1b] py-2 text-xs font-bold text-white hover:bg-red-800 transition shadow-sm">Đăng ký</a>
            </div>
            @endif
        </div>
    </aside>

    <main class="min-h-screen">
        {{-- Mobile Top Bar --}}
        <div class="border-b border-white/70 bg-white/70 px-4 py-3 backdrop-blur lg:hidden">
            <div class="flex items-center justify-between gap-3">
                <a href="{{ route('home') }}" class="flex shrink-0 items-center gap-2.5">
                    <div class="grid h-8 w-8 place-items-center rounded-xl bg-[#991b1b] text-white">
                        <span class="text-sm font-black">中</span>
                    </div>
                </a>
                <div class="flex flex-1 min-w-0 items-center gap-1.5 overflow-x-auto text-[10px] sm:text-xs font-semibold uppercase tracking-wider text-slate-600 pb-1" style="scrollbar-width: none;">
                    <a href="{{ route('dashboard') }}" class="shrink-0 whitespace-nowrap rounded-full px-3 py-1.5 {{ request()->routeIs('dashboard') ? 'bg-[#991b1b] text-white shadow-md' : 'bg-slate-100 hover:bg-slate-200' }}">Tổng quan</a>
                    <a href="{{ route('flashcards') }}" class="shrink-0 whitespace-nowrap rounded-full px-3 py-1.5 {{ request()->routeIs('flashcards') ? 'bg-[#991b1b] text-white shadow-md' : 'bg-slate-100 hover:bg-slate-200' }}">Thẻ nhớ</a>
                    <a href="{{ route('quiz') }}" class="shrink-0 whitespace-nowrap rounded-full px-3 py-1.5 {{ request()->routeIs('quiz') ? 'bg-[#991b1b] text-white shadow-md' : 'bg-slate-100 hover:bg-slate-200' }}">Kiểm tra</a>
                    <a href="{{ route('hsk.overview') }}" class="shrink-0 whitespace-nowrap rounded-full px-3 py-1.5 {{ request()->routeIs('hsk.*') ? 'bg-[#991b1b] text-white shadow-md' : 'bg-slate-100 hover:bg-slate-200' }}">HSK</a>
                    @if ($authUser)
                    <form method="POST" action="{{ route('logout') }}" class="inline shrink-0">
                        @csrf
                        <button type="submit" class="rounded-full bg-red-50 text-red-700 px-3 py-1.5 text-[10px] sm:text-xs font-bold hover:bg-red-100 transition">Thoát</button>
                    </form>
                    @else
                    <a href="{{ route('register') }}" class="shrink-0 rounded-full bg-[#991b1b] text-white px-3 py-1.5 text-[10px] sm:text-xs font-bold shadow-md">Đăng ký</a>
                    @endif
                </div>
            </div>
        </div>

        <div class="px-4 py-6 sm:px-6 lg:px-10 lg:py-8">
            @yield('content')
        </div>
    </main>
</body>

</html>