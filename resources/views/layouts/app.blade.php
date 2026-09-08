<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Chinese Deck - web học tiếng Trung với bài học ngắn, flashcard, quiz và tiến độ học tập.">

    <title>@yield('title', 'Learn Chinese | Học Tiếng Trung')</title>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-[#f4ede3] text-slate-950 antialiased lg:pl-72">
    <aside class="fixed inset-y-0 left-0 z-40 hidden w-72 border-r border-slate-200/80 bg-white/95 px-5 py-5 shadow-2xl shadow-slate-950/10 backdrop-blur lg:flex lg:flex-col justify-between overflow-hidden">
        
        {{-- 1. Pinned Top: Brand Logo --}}
        <div class="shrink-0 mb-3">
            <a href="{{ auth()->check() ? route('dashboard') : route('home') }}" class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-950 px-3.5 py-3 text-white shadow-md shadow-slate-950/15 hover:bg-slate-900 transition">
                <div class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-[#991b1b]">
                    <span class="text-lg font-black">中</span>
                </div>
                <div class="truncate">
                    <p class="text-xs font-bold uppercase tracking-[0.2em] text-amber-200/80">Learn Chinese</p>
                    <p class="text-xs text-slate-300">Học tiếng Trung</p>
                </div>
            </a>
        </div>

        {{-- 2. Scrollable Middle Area: Nav Links + Streak Box --}}
        <div class="flex-1 overflow-y-auto overflow-x-hidden pr-1 space-y-3.5 custom-scrollbar">
            
            <nav class="space-y-1.5 text-sm font-medium">

                {{-- Group: Học tập & Luyện tập --}}
                <p class="px-3 pt-1 text-[11px] font-bold uppercase tracking-wider text-slate-400">Học tập & Thực hành</p>

                {{-- Trang chủ (Dashboard) --}}
                <a href="{{ auth()->check() ? route('dashboard') : route('home') }}"
                    class="group flex items-center justify-between rounded-xl px-3.5 py-2.5 transition {{ (request()->routeIs('dashboard') || request()->routeIs('home')) ? 'bg-[#991b1b] text-white shadow-md shadow-red-950/15' : 'text-slate-700 hover:bg-slate-100 hover:text-[#991b1b]' }}">
                    <span class="flex items-center gap-3">
                        <span class="grid h-7 w-7 place-items-center rounded-lg {{ (request()->routeIs('dashboard') || request()->routeIs('home')) ? 'bg-white/10' : 'bg-slate-100 group-hover:bg-red-50' }}">
                            <i data-lucide="house" class="h-4 w-4"></i>
                        </span>
                        Trang chủ
                    </span>
                </a>

                {{-- Flashcard --}}
                <a href="{{ route('flashcards') }}"
                    class="group flex items-center justify-between rounded-xl px-3.5 py-2.5 transition {{ request()->routeIs('flashcards') ? 'bg-[#991b1b] text-white shadow-md shadow-red-950/15' : 'text-slate-700 hover:bg-slate-100 hover:text-[#991b1b]' }}">
                    <span class="flex items-center gap-3">
                        <span class="grid h-7 w-7 place-items-center rounded-lg {{ request()->routeIs('flashcards') ? 'bg-white/10' : 'bg-slate-100 group-hover:bg-red-50' }}">
                            <i data-lucide="layers" class="h-4 w-4"></i>
                        </span>
                        Thẻ ghi nhớ
                    </span>
                </a>

                {{-- Luyện đọc hiểu Graded Reader --}}
                <a href="{{ route('stories.index') }}"
                    class="group flex items-center justify-between rounded-xl px-3.5 py-2.5 transition {{ request()->routeIs('stories.*') ? 'bg-[#991b1b] text-white shadow-md shadow-red-950/15' : 'text-slate-700 hover:bg-slate-100 hover:text-[#991b1b]' }}">
                    <span class="flex items-center gap-3">
                        <span class="grid h-7 w-7 place-items-center rounded-lg {{ request()->routeIs('stories.*') ? 'bg-white/10' : 'bg-slate-100 group-hover:bg-red-50' }}">
                            <i data-lucide="book-open-check" class="h-4 w-4"></i>
                        </span>
                        Luyện đọc hiểu
                    </span>
                    <span class="rounded-full bg-emerald-100 border border-emerald-300 px-1.5 py-0.2 text-[9px] font-black uppercase text-emerald-800 {{ request()->routeIs('stories.*') ? 'bg-white/20 text-white border-transparent' : '' }}">Mới</span>
                </a>

                {{-- Tra từ điển & Video YouGlish --}}
                <a href="{{ route('dictionary.index') }}"
                    class="group flex items-center justify-between rounded-xl px-3.5 py-2.5 transition {{ request()->routeIs('dictionary.*') ? 'bg-[#991b1b] text-white shadow-md shadow-red-950/15' : 'text-slate-700 hover:bg-slate-100 hover:text-[#991b1b]' }}">
                    <span class="flex items-center gap-3">
                        <span class="grid h-7 w-7 place-items-center rounded-lg {{ request()->routeIs('dictionary.*') ? 'bg-white/10' : 'bg-slate-100 group-hover:bg-red-50' }}">
                            <i data-lucide="video" class="h-4 w-4"></i>
                        </span>
                        Từ điển & Video
                    </span>
                    <span class="rounded-full bg-red-100 border border-red-300 px-1.5 py-0.2 text-[9px] font-black uppercase text-red-800 {{ request()->routeIs('dictionary.*') ? 'bg-white/20 text-white border-transparent' : '' }}">Hot</span>
                </a>

                {{-- Quiz / Luyện tập nhanh --}}
                <a href="{{ route('quiz') }}"
                    class="group flex items-center justify-between rounded-xl px-3.5 py-2.5 transition {{ request()->routeIs('quiz') ? 'bg-[#991b1b] text-white shadow-md shadow-red-950/15' : 'text-slate-700 hover:bg-slate-100 hover:text-[#991b1b]' }}">
                    <span class="flex items-center gap-3">
                        <span class="grid h-7 w-7 place-items-center rounded-lg {{ request()->routeIs('quiz') ? 'bg-white/10' : 'bg-slate-100 group-hover:bg-red-50' }}">
                            <i data-lucide="target" class="h-4 w-4"></i>
                        </span>
                        Luyện tập nhanh
                    </span>
                </a>

                {{-- Group: Khung HSK --}}
                <p class="px-3 pt-2 text-[11px] font-bold uppercase tracking-wider text-slate-400">Khung Chứng chỉ HSK</p>

                {{-- HSK Overview --}}
                <a href="{{ route('hsk.overview') }}"
                    class="group flex items-center justify-between rounded-xl px-3.5 py-2.5 transition {{ (request()->routeIs('hsk.overview') || request()->routeIs('hsk.show')) ? 'bg-[#991b1b] text-white shadow-md shadow-red-950/15' : 'text-slate-700 hover:bg-slate-100 hover:text-[#991b1b]' }}">
                    <span class="flex items-center gap-3">
                        <span class="grid h-7 w-7 place-items-center rounded-lg {{ (request()->routeIs('hsk.overview') || request()->routeIs('hsk.show')) ? 'bg-white/10' : 'bg-slate-100 group-hover:bg-red-50' }}">
                            <i data-lucide="graduation-cap" class="h-4 w-4"></i>
                        </span>
                        Lộ trình HSK
                    </span>
                </a>

                {{-- Thi thử HSK Mô phỏng --}}
                <a href="{{ route('hsk.mock.index') }}"
                    class="group flex items-center justify-between rounded-xl px-3.5 py-2.5 transition {{ request()->routeIs('hsk.mock.*') ? 'bg-[#991b1b] text-white shadow-md shadow-red-950/15' : 'text-slate-700 hover:bg-slate-100 hover:text-[#991b1b]' }}">
                    <span class="flex items-center gap-3">
                        <span class="grid h-7 w-7 place-items-center rounded-lg {{ request()->routeIs('hsk.mock.*') ? 'bg-white/10' : 'bg-slate-100 group-hover:bg-red-50' }}">
                            <i data-lucide="award" class="h-4 w-4"></i>
                        </span>
                        Thi thử HSK
                    </span>
                    <span class="rounded-full bg-amber-100 border border-amber-300 px-1.5 py-0.2 text-[9px] font-black uppercase text-amber-800 {{ request()->routeIs('hsk.mock.*') ? 'bg-white/20 text-white border-transparent' : '' }}">Thi</span>
                </a>
            </nav>

            {{-- Compact Streak Box --}}
            <div class="rounded-2xl bg-[linear-gradient(135deg,_#111827_0%,_#1f2937_45%,_#991b1b_100%)] p-3.5 text-white shadow-lg shadow-slate-950/15">
                <div class="flex items-center justify-between">
                    <p class="text-[10px] font-semibold uppercase tracking-[0.2em] text-amber-200/80">Streak học tập</p>
                    <i data-lucide="flame" class="h-3.5 w-3.5 text-amber-400"></i>
                </div>
                <div class="mt-1.5 flex items-baseline justify-between">
                    <p class="text-xl font-black">{{ str_pad($sidebarStreak ?? 0, 2, '0', STR_PAD_LEFT) }}</p>
                    <p class="text-[11px] text-slate-300">ngày liên tiếp</p>
                </div>
                <div class="mt-2 h-1.5 rounded-full bg-white/10">
                    <div class="h-1.5 rounded-full bg-gradient-to-r from-amber-300 to-red-400" style="width: {{ min(100, max(15, ($sidebarStreak ?? 0) * 15)) }}%"></div>
                </div>
            </div>

        </div>

        {{-- 3. Pinned Bottom: User Profile / Login Action --}}
        <div class="shrink-0 pt-3 mt-2 border-t border-slate-200/80">
            @if ($authUser)
            <div class="flex items-center justify-between gap-2 rounded-2xl bg-slate-50 p-2 border border-slate-100">
                <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 overflow-hidden hover:opacity-80 transition min-w-0">
                    <div class="grid h-8 w-8 shrink-0 place-items-center rounded-xl bg-slate-900 text-xs font-bold text-white">
                        {{ strtoupper(substr($authUser->name ?? 'U', 0, 1)) }}
                    </div>
                    <div class="truncate text-left">
                        <p class="truncate text-xs font-bold text-slate-900">{{ $authUser->name }}</p>
                        <p class="truncate text-[10px] text-slate-500">{{ $authUser->isAdmin() ? 'Quản trị viên' : $authUser->email }}</p>
                    </div>
                </a>
                <form method="POST" action="{{ route('logout') }}" class="shrink-0">
                    @csrf
                    <button type="submit" title="Đăng xuất"
                        class="grid h-8 w-8 place-items-center rounded-xl bg-white text-slate-600 hover:bg-red-50 hover:text-red-600 border border-slate-200 transition">
                        <i data-lucide="log-out" class="h-3.5 w-3.5"></i>
                    </button>
                </form>
            </div>
            @else
            <div class="space-y-1.5">
                <a href="{{ route('login') }}" class="flex w-full items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white py-2 text-xs font-bold text-slate-700 hover:bg-slate-50 shadow-sm transition">
                    <i data-lucide="log-in" class="h-3.5 w-3.5"></i>
                    <span>Đăng nhập</span>
                </a>
                <a href="{{ route('register') }}" class="flex w-full items-center justify-center gap-2 rounded-xl bg-[#991b1b] py-2 text-xs font-bold text-white hover:bg-red-800 shadow-md shadow-red-950/15 transition">
                    <i data-lucide="user-plus" class="h-3.5 w-3.5"></i>
                    <span>Đăng ký học viên</span>
                </a>
            </div>
            @endif
        </div>
    </aside>

    <main class="min-h-screen flex flex-col justify-between">
        <div>
            {{-- Mobile Navigation (hidden on lg desktop) --}}
            <div class="no-print lg:hidden" x-data="{ mobileMenuOpen: false }" @keydown.escape.window="mobileMenuOpen = false">

                {{-- Sticky Top Header Bar --}}
                <header class="sticky top-0 z-30 flex items-center justify-between gap-3 border-b border-slate-200/80 bg-white/95 px-4 py-3 backdrop-blur shadow-sm shadow-slate-900/5">

                    {{-- Left: Brand Logo --}}
                    <a href="{{ auth()->check() ? route('dashboard') : route('home') }}" class="flex items-center gap-2.5 min-w-0">
                        <div class="grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-[#991b1b] text-white shadow-sm">
                            <span class="text-sm font-black">中</span>
                        </div>
                        <div class="min-w-0 truncate">
                            <p class="text-sm font-black tracking-tight text-slate-950">Learn Chinese</p>
                            <p class="text-[10px] text-slate-500 leading-none">Học tiếng Trung</p>
                        </div>
                    </a>

                    {{-- Right: User avatar + Hamburger --}}
                    <div class="flex shrink-0 items-center gap-2">
                        @if ($authUser)
                            <a href="{{ route('profile.edit') }}" title="{{ $authUser->name }}"
                               class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-slate-900 text-xs font-bold text-white shadow-sm ring-2 ring-white hover:opacity-90 transition">
                                {{ strtoupper(substr($authUser->name ?? 'U', 0, 1)) }}
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="rounded-full border border-slate-200 bg-white px-3 py-1.5 text-xs font-bold text-slate-700 hover:bg-slate-50 shadow-sm transition">
                                Đăng nhập
                            </a>
                        @endif

                        {{-- Hamburger Button --}}
                        <button @click="mobileMenuOpen = true"
                                aria-label="Mở menu"
                                class="flex h-9 w-9 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-700 shadow-sm transition hover:bg-slate-50 active:scale-95">
                            <i data-lucide="menu" class="h-5 w-5"></i>
                        </button>
                    </div>
                </header>

                {{-- Dark Backdrop Overlay --}}
                <div x-show="mobileMenuOpen"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     @click="mobileMenuOpen = false"
                     class="fixed inset-0 z-40 bg-slate-950/60 backdrop-blur-sm"
                     style="display:none;"></div>

                {{-- Slide-in Drawer Panel --}}
                <div x-show="mobileMenuOpen"
                     x-transition:enter="transition ease-out duration-300 transform"
                     x-transition:enter-start="-translate-x-full"
                     x-transition:enter-end="translate-x-0"
                     x-transition:leave="transition ease-in duration-200 transform"
                     x-transition:leave-start="translate-x-0"
                     x-transition:leave-end="-translate-x-full"
                     class="fixed inset-y-0 left-0 z-50 flex w-[280px] flex-col bg-white shadow-2xl shadow-slate-950/20"
                     style="display:none;">

                    {{-- Drawer Header --}}
                    <div class="flex shrink-0 items-center justify-between border-b border-slate-100 px-5 py-4">
                        <a href="{{ auth()->check() ? route('dashboard') : route('home') }}"
                           @click="mobileMenuOpen = false"
                           class="flex items-center gap-3">
                            <div class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-[#991b1b] shadow-md shadow-red-950/20">
                                <span class="text-base font-black text-white">中</span>
                            </div>
                            <div>
                                <p class="text-xs font-black uppercase tracking-[0.15em] text-slate-900">Learn Chinese</p>
                                <p class="text-[10px] text-slate-500">Học tiếng Trung</p>
                            </div>
                        </a>
                        <button @click="mobileMenuOpen = false"
                                aria-label="Đóng menu"
                                class="flex h-8 w-8 items-center justify-center rounded-xl text-slate-400 transition hover:bg-slate-100 hover:text-slate-600 active:scale-95">
                            <i data-lucide="x" class="h-5 w-5"></i>
                        </button>
                    </div>

                    {{-- Drawer Navigation Links --}}
                    <nav class="flex-1 overflow-y-auto px-4 py-4 space-y-0.5">
                        <p class="px-2 pb-2 pt-1 text-[10px] font-bold uppercase tracking-widest text-slate-400">Học tập &amp; Thực hành</p>

                        <a href="{{ auth()->check() ? route('dashboard') : route('home') }}"
                           @click="mobileMenuOpen = false"
                           class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition {{ (request()->routeIs('dashboard') || request()->routeIs('home')) ? 'bg-[#991b1b] text-white shadow-md shadow-red-950/10' : 'text-slate-700 hover:bg-slate-100 hover:text-[#991b1b]' }}">
                            <i data-lucide="house" class="h-4 w-4 shrink-0"></i>
                            Trang chủ
                        </a>

                        <a href="{{ route('flashcards') }}"
                           @click="mobileMenuOpen = false"
                           class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition {{ request()->routeIs('flashcards') ? 'bg-[#991b1b] text-white shadow-md shadow-red-950/10' : 'text-slate-700 hover:bg-slate-100 hover:text-[#991b1b]' }}">
                            <i data-lucide="layers" class="h-4 w-4 shrink-0"></i>
                            Thẻ ghi nhớ
                        </a>

                        <a href="{{ route('stories.index') }}"
                           @click="mobileMenuOpen = false"
                           class="flex items-center justify-between gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition {{ request()->routeIs('stories.*') ? 'bg-[#991b1b] text-white shadow-md shadow-red-950/10' : 'text-slate-700 hover:bg-slate-100 hover:text-[#991b1b]' }}">
                            <span class="flex items-center gap-3">
                                <i data-lucide="book-open-check" class="h-4 w-4 shrink-0"></i>
                                Luyện đọc hiểu
                            </span>
                            <span class="shrink-0 rounded-full px-2 py-0.5 text-[9px] font-black {{ request()->routeIs('stories.*') ? 'bg-white/20 text-white' : 'bg-emerald-100 text-emerald-800' }}">Mới</span>
                        </a>

                        <a href="{{ route('dictionary.index') }}"
                           @click="mobileMenuOpen = false"
                           class="flex items-center justify-between gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition {{ request()->routeIs('dictionary.*') ? 'bg-[#991b1b] text-white shadow-md shadow-red-950/10' : 'text-slate-700 hover:bg-slate-100 hover:text-[#991b1b]' }}">
                            <span class="flex items-center gap-3">
                                <i data-lucide="video" class="h-4 w-4 shrink-0"></i>
                                Từ điển &amp; Video
                            </span>
                            <span class="shrink-0 rounded-full px-2 py-0.5 text-[9px] font-black {{ request()->routeIs('dictionary.*') ? 'bg-white/20 text-white' : 'bg-red-100 text-red-800' }}">Hot</span>
                        </a>

                        <a href="{{ route('quiz') }}"
                           @click="mobileMenuOpen = false"
                           class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition {{ request()->routeIs('quiz') ? 'bg-[#991b1b] text-white shadow-md shadow-red-950/10' : 'text-slate-700 hover:bg-slate-100 hover:text-[#991b1b]' }}">
                            <i data-lucide="target" class="h-4 w-4 shrink-0"></i>
                            Luyện tập nhanh
                        </a>

                        <p class="px-2 pb-2 pt-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Khung Chứng chỉ HSK</p>

                        <a href="{{ route('hsk.overview') }}"
                           @click="mobileMenuOpen = false"
                           class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition {{ (request()->routeIs('hsk.overview') || request()->routeIs('hsk.show')) ? 'bg-[#991b1b] text-white shadow-md shadow-red-950/10' : 'text-slate-700 hover:bg-slate-100 hover:text-[#991b1b]' }}">
                            <i data-lucide="graduation-cap" class="h-4 w-4 shrink-0"></i>
                            Lộ trình HSK
                        </a>

                        <a href="{{ route('hsk.mock.index') }}"
                           @click="mobileMenuOpen = false"
                           class="flex items-center justify-between gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition {{ request()->routeIs('hsk.mock.*') ? 'bg-[#991b1b] text-white shadow-md shadow-red-950/10' : 'text-slate-700 hover:bg-slate-100 hover:text-[#991b1b]' }}">
                            <span class="flex items-center gap-3">
                                <i data-lucide="award" class="h-4 w-4 shrink-0"></i>
                                Thi thử HSK
                            </span>
                            <span class="shrink-0 rounded-full px-2 py-0.5 text-[9px] font-black {{ request()->routeIs('hsk.mock.*') ? 'bg-white/20 text-white' : 'bg-amber-100 text-amber-900' }}">Thi</span>
                        </a>
                    </nav>

                    {{-- Drawer Footer: Streak + User Info --}}
                    <div class="shrink-0 space-y-3 border-t border-slate-100 px-4 py-4">

                        {{-- Streak Box --}}
                        <div class="flex items-center justify-between rounded-xl bg-slate-950 px-4 py-3 text-white">
                            <div class="flex items-center gap-2">
                                <i data-lucide="flame" class="h-4 w-4 text-amber-400"></i>
                                <p class="text-xs font-semibold text-slate-400">Streak học tập</p>
                            </div>
                            <p class="text-sm font-black text-amber-300">
                                {{ str_pad($sidebarStreak ?? 0, 2, '0', STR_PAD_LEFT) }} ngày
                            </p>
                        </div>

                        {{-- User Profile / Auth Actions --}}
                        @if ($authUser)
                            <div class="flex items-center justify-between gap-2 rounded-xl border border-slate-100 bg-slate-50 p-2">
                                <a href="{{ route('profile.edit') }}" @click="mobileMenuOpen = false" class="flex min-w-0 items-center gap-2 hover:opacity-80 transition">
                                    <div class="grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-slate-900 text-xs font-bold text-white">
                                        {{ strtoupper(substr($authUser->name ?? 'U', 0, 1)) }}
                                    </div>
                                    <div class="min-w-0 truncate">
                                        <p class="truncate text-xs font-bold text-slate-900">{{ $authUser->name }}</p>
                                        <p class="truncate text-[10px] text-slate-500">{{ $authUser->isAdmin() ? 'Quản trị viên' : $authUser->email }}</p>
                                    </div>
                                </a>
                                <form method="POST" action="{{ route('logout') }}" class="shrink-0">
                                    @csrf
                                    <button type="submit" title="Đăng xuất"
                                            class="grid h-8 w-8 place-items-center rounded-lg border border-slate-200 bg-white text-slate-500 transition hover:bg-red-50 hover:text-red-600">
                                        <i data-lucide="log-out" class="h-3.5 w-3.5"></i>
                                    </button>
                                </form>
                            </div>
                        @else
                            <div class="grid grid-cols-2 gap-2">
                                <a href="{{ route('login') }}" @click="mobileMenuOpen = false"
                                   class="flex items-center justify-center gap-1.5 rounded-xl border border-slate-200 bg-white py-2.5 text-xs font-bold text-slate-700 transition hover:bg-slate-50">
                                    <i data-lucide="log-in" class="h-3.5 w-3.5"></i>
                                    Đăng nhập
                                </a>
                                <a href="{{ route('register') }}" @click="mobileMenuOpen = false"
                                   class="flex items-center justify-center gap-1.5 rounded-xl bg-[#991b1b] py-2.5 text-xs font-bold text-white shadow-md shadow-red-950/15 transition hover:bg-red-800">
                                    <i data-lucide="user-plus" class="h-3.5 w-3.5"></i>
                                    Đăng ký
                                </a>
                            </div>
                        @endif
                    </div>

                </div>{{-- End Drawer --}}
            </div>{{-- End Mobile Navigation --}}

            <div class="px-4 py-6 sm:px-6 lg:px-10 lg:py-8">
                @yield('content')
            </div>
        </div>

        <x-footer />
    </main>
    
    <script>
        window.playChineseVoice = async function(text) {
            if (!text) return;
            
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const response = await fetch('/tts', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ text: text })
                });

                if (response.ok) {
                    const data = await response.json();
                    if (data.audio) {
                        const audio = new Audio(data.audio);
                        // B3: await so autoplay rejection falls through to catch → WebSpeech fallback
                        await audio.play();
                        return;
                    }
                }
                throw new Error('Azure TTS API failed');
            } catch (e) {
                console.warn('Sử dụng giọng đọc dự phòng của trình duyệt...', e);
                if ('speechSynthesis' in window) {
                    window.speechSynthesis.cancel();
                    const utterance = new SpeechSynthesisUtterance(text);
                    utterance.lang = 'zh-CN';
                    window.speechSynthesis.speak(utterance);
                }
            }
        };
    </script>
    <x-toast />
</body>

</html>