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
            <a href="{{ route('home') }}" class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-950 px-3.5 py-3 text-white shadow-md shadow-slate-950/15 hover:bg-slate-900 transition">
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

                {{-- Dashboard --}}
                <a href="{{ route('dashboard') }}"
                    class="group flex items-center justify-between rounded-xl px-3.5 py-2.5 transition {{ request()->routeIs('dashboard') ? 'bg-[#991b1b] text-white shadow-md shadow-red-950/15' : 'text-slate-700 hover:bg-slate-100 hover:text-[#991b1b]' }}">
                    <span class="flex items-center gap-3">
                        <span class="grid h-7 w-7 place-items-center rounded-lg {{ request()->routeIs('dashboard') ? 'bg-white/10' : 'bg-slate-100 group-hover:bg-red-50' }}">
                            <i data-lucide="bar-chart-3" class="h-4 w-4"></i>
                        </span>
                        Tổng quan
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

                {{-- Trang chủ --}}
                <a href="{{ route('home') }}"
                    class="group flex items-center justify-between rounded-xl px-3.5 py-2.5 transition {{ request()->routeIs('home') ? 'bg-[#991b1b] text-white shadow-md shadow-red-950/15' : 'text-slate-700 hover:bg-slate-100 hover:text-[#991b1b]' }}">
                    <span class="flex items-center gap-3">
                        <span class="grid h-7 w-7 place-items-center rounded-lg {{ request()->routeIs('home') ? 'bg-white/10' : 'bg-slate-100 group-hover:bg-red-50' }}">
                            <i data-lucide="house" class="h-4 w-4"></i>
                        </span>
                        Trang chủ
                    </span>
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
            {{-- Mobile Top Bar --}}
            <div class="no-print border-b border-slate-200/80 bg-white/80 p-3 backdrop-blur lg:hidden sticky top-0 z-20">
                <div class="flex items-center gap-2 overflow-x-auto text-xs font-semibold text-slate-700 no-scrollbar pr-4">
                    <a href="{{ route('dashboard') }}" class="shrink-0 whitespace-nowrap rounded-full px-3 py-1.5 {{ request()->routeIs('dashboard') ? 'bg-[#991b1b] text-white shadow-md' : 'bg-slate-100 hover:bg-slate-200' }}">Tổng quan</a>
                    <a href="{{ route('flashcards') }}" class="shrink-0 whitespace-nowrap rounded-full px-3 py-1.5 {{ request()->routeIs('flashcards') ? 'bg-[#991b1b] text-white shadow-md' : 'bg-slate-100 hover:bg-slate-200' }}">Thẻ nhớ</a>
                    <a href="{{ route('stories.index') }}" class="shrink-0 whitespace-nowrap rounded-full px-3 py-1.5 {{ request()->routeIs('stories.*') ? 'bg-[#991b1b] text-white shadow-md' : 'bg-emerald-50 text-emerald-800 border border-emerald-300 font-bold' }}">Đọc hiểu ✨</a>
                    <a href="{{ route('quiz') }}" class="shrink-0 whitespace-nowrap rounded-full px-3 py-1.5 {{ request()->routeIs('quiz') ? 'bg-[#991b1b] text-white shadow-md' : 'bg-slate-100 hover:bg-slate-200' }}">Luyện tập</a>
                    <a href="{{ route('hsk.overview') }}" class="shrink-0 whitespace-nowrap rounded-full px-3 py-1.5 {{ (request()->routeIs('hsk.overview') || request()->routeIs('hsk.show')) ? 'bg-[#991b1b] text-white shadow-md' : 'bg-slate-100 hover:bg-slate-200' }}">HSK</a>
                    <a href="{{ route('hsk.mock.index') }}" class="shrink-0 whitespace-nowrap rounded-full px-3 py-1.5 {{ request()->routeIs('hsk.mock.*') ? 'bg-[#991b1b] text-white shadow-md' : 'bg-amber-100 text-amber-900 border border-amber-300' }}">Thi thử HSK ⭐</a>
                    @if ($authUser)
                    <form method="POST" action="{{ route('logout') }}" class="inline shrink-0">
                        @csrf
                        <button type="submit" class="rounded-full bg-red-50 text-red-700 px-3 py-1.5 text-[10px] sm:text-xs font-bold whitespace-nowrap hover:bg-red-100 transition">Thoát</button>
                    </form>
                    @else
                    <a href="{{ route('register') }}" class="shrink-0 whitespace-nowrap rounded-full bg-[#991b1b] text-white px-3 py-1.5 text-[10px] sm:text-xs font-bold shadow-md">Đăng ký</a>
                    @endif
                    {{-- Spacer to add padding at the end of scroll --}}
                    <div class="w-1 shrink-0"></div>
                </div>
            </div>

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
                        audio.play();
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