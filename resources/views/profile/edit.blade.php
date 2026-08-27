@extends('layouts.app')

@section('title', 'Hồ sơ cá nhân | Learn Chinese')

@section('content')

@php
$streak        = $stats['streak'];
$studiedToday  = $user->hasStudiedToday();
$totalMinutes  = $user->studySessions()->sum('duration_minutes');
$totalSessions = $user->studySessions()->count();
$completedCount= $stats['completed'];
$totalLessons  = $stats['lessons'];
$avgScore      = $user->studySessions()->whereNotNull('score')->avg('score');
$joinDate      = $user->created_at->format('d/m/Y');
$memberSince   = $user->created_at->diffForHumans();
$pct           = $totalLessons > 0 ? min(100, round(($completedCount / $totalLessons) * 100)) : 0;
$initial       = mb_strtoupper(mb_substr($user->name, 0, 1));
@endphp

{{-- ══════════════════════════════════════════════
     ZONE 1 · HERO — Avatar card + 4 stat chips
══════════════════════════════════════════════ --}}
<div class="mb-6 grid gap-4 lg:grid-cols-[auto_1fr]">

    {{-- Avatar card --}}
    <div class="relative overflow-hidden rounded-[2rem] bg-slate-950 px-8 py-10 text-white shadow-2xl shadow-slate-950/25 lg:w-80">

        {{-- Top accent --}}
        <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-amber-400 via-[#991b1b] to-amber-400"></div>

        {{-- Large faded initial bg --}}
        <span class="pointer-events-none absolute -bottom-4 -right-3 select-none text-[9rem] font-black leading-none text-white opacity-[.04]">
            {{ $initial }}
        </span>


        {{-- Profile --}}
        <div class="relative flex items-center gap-4">

            <div class="grid h-16 w-16 shrink-0 place-items-center rounded-2xl bg-gradient-to-br from-[#991b1b] to-amber-500 text-3xl font-black shadow-lg">
                {{ $initial }}
            </div>

            <div class="min-w-0">
                <p class="text-[10px] font-semibold uppercase tracking-[.22em] text-slate-500">
                    Học viên
                </p>

                <p class="mt-1 truncate text-lg font-black tracking-tight">
                    {{ $user->name }}
                </p>

                <p class="mt-0.5 truncate text-xs text-slate-500">
                    {{ $user->email }}
                </p>
            </div>

        </div>


        {{-- Stats --}}
        <div class="relative mt-6 grid grid-cols-2 gap-3">

            {{-- Join date --}}
            <div class="rounded-2xl border border-white/10 bg-white/[.06] p-4">

                <p class="text-[10px] uppercase tracking-[.18em] text-slate-500">
                    Tham gia
                </p>

                <p class="mt-1.5 text-sm font-bold">
                    {{ $joinDate }}
                </p>

                <p class="mt-0.5 text-[10px] text-slate-600">
                    {{ $memberSince }}
                </p>

            </div>


            {{-- Streak --}}
            <div class="rounded-2xl border border-white/10 bg-white/[.06] p-4">

                <p class="text-[10px] uppercase tracking-[.18em] text-slate-500">
                    Streak
                </p>

                <p class="mt-1 text-xl font-black flex items-center gap-1 {{ $studiedToday ? 'text-amber-400' : 'text-slate-400' }}">
                    <i data-lucide="flame" class="h-4 w-4"></i> {{ $streak }}
                </p>

                <p class="mt-0.5 text-[10px] text-slate-600">
                    ngày liên tiếp
                </p>

            </div>

        </div>


        {{-- Progress --}}
        <div class="relative mt-6">

            <div class="mb-2 flex items-center justify-between">

                <p class="text-[10px] font-semibold uppercase tracking-[.18em] text-amber-400/70">
                    Tiến độ
                </p>

                <p class="text-[10px] font-bold text-white/60">
                    {{ $completedCount }} bài ✓
                </p>

            </div>

            <div class="h-2 overflow-hidden rounded-full bg-white/10">
                <div
                    class="h-2 rounded-full bg-gradient-to-r from-amber-400 to-[#991b1b] transition-all duration-700"
                    style="width: {{ $pct }}%;"></div>
            </div>

        </div>

    </div>

    {{-- 4 stat tiles – horizontal bento grid --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">

        {{-- Flashcard --}}
        <div class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-md">
            <div class="flex items-center gap-4">

                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-red-50 text-red-600">
                    <svg
                        class="h-5 w-5"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" />
                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 6.5 2z" />
                    </svg>
                </div>

                <div class="min-w-0">
                    <p class="text-2xl font-black leading-none text-slate-900">
                        {{ $stats['flashcards'] ?? 0 }}
                    </p>

                    <p class="mt-1 text-sm text-slate-500">
                        Flashcard
                    </p>
                </div>

            </div>
        </div>


        {{-- Bài học --}}
        <div class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-md">
            <div class="flex items-center gap-4">

                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                    <svg
                        class="h-5 w-5"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" />
                        <path d="M6.5 2H20v20H6.5" />
                        <path d="M4 6h16" />
                    </svg>
                </div>

                <div class="min-w-0">
                    <p class="text-2xl font-black leading-none text-slate-900">
                        {{ $stats['lessons'] ?? 0 }}
                    </p>

                    <p class="mt-1 text-sm text-slate-500">
                        Bài học
                    </p>
                </div>

            </div>
        </div>


        {{-- Hoàn thành --}}
        <div class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-md">
            <div class="flex items-center gap-4">

                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-600">
                    <svg
                        class="h-5 w-5"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87
                             1.18 6.88L12 17.77
                             5.82 21.02 7 14.14
                             2 9.27l6.91-1.01L12 2z" />
                    </svg>
                </div>

                <div class="min-w-0">
                    <p class="text-2xl font-black leading-none text-slate-900">
                        {{ $stats['completed'] ?? 0 }}
                    </p>

                    <p class="mt-1 text-sm text-slate-500">
                        Đã hoàn thành
                    </p>
                </div>

            </div>
        </div>


        {{-- Streak --}}
        <div class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-md">
            <div class="flex items-center gap-4">

                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                    <svg
                        class="h-5 w-5"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round">
                        <circle cx="12" cy="12" r="9" />
                        <path d="M12 7v5l3 2" />
                    </svg>
                </div>

                <div class="min-w-0">
                    <p class="text-2xl font-black leading-none text-slate-900">
                        {{ $stats['streak'] ?? 0 }}
                    </p>

                    <p class="mt-1 text-sm text-slate-500">
                        Ngày liên tiếp
                    </p>
                </div>

            </div>
        </div>

    </div>
</div>

{{-- ══════════════════════════════════════════════
     ZONE 2 · FORMS — Edit info + Change password
══════════════════════════════════════════════ --}}
<div class="mb-6 grid gap-4 lg:grid-cols-2">

    {{-- Edit profile --}}
    <div class="relative overflow-hidden rounded-[2rem] border border-white/80 bg-white/80 p-8 shadow-xl shadow-slate-900/5 backdrop-blur">
        <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-[#991b1b] via-amber-400 to-[#991b1b]"></div>

        <div class="mb-6 flex items-center gap-3">
            <div class="grid h-10 w-10 shrink-0 place-items-center rounded-2xl bg-red-50 text-red-600">
                <i data-lucide="user" class="h-5 w-5"></i>
            </div>
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-[.22em] text-[#991b1b]">Thông tin cơ bản</p>
                <h2 class="text-lg font-black text-slate-950">Cập nhật hồ sơ</h2>
            </div>
        </div>

        <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
            @csrf @method('PATCH')

            <div>
                <label for="name" class="block text-xs font-bold uppercase tracking-[.14em] text-slate-500 mb-1.5">Họ và tên</label>
                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required autocomplete="name"
                    class="w-full rounded-2xl border border-slate-200 bg-slate-50/80 px-4 py-3 text-sm font-medium text-slate-900 transition focus:border-[#991b1b] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#991b1b]/10">
                @error('name')<p class="mt-1.5 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="email" class="block text-xs font-bold uppercase tracking-[.14em] text-slate-500 mb-1.5">Địa chỉ Email</label>
                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required autocomplete="username"
                    class="w-full rounded-2xl border border-slate-200 bg-slate-50/80 px-4 py-3 text-sm font-medium text-slate-900 transition focus:border-[#991b1b] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#991b1b]/10">
                @error('email')<p class="mt-1.5 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="flex items-center gap-4 pt-2">
                <button type="submit"
                    class="rounded-full bg-[#991b1b] px-6 py-2.5 text-sm font-bold text-white shadow-md transition hover:-translate-y-0.5 hover:bg-[#7f1717]">
                    Lưu thay đổi
                </button>
                @if(session('status') === 'profile-updated')
                <p x-data="{show:true}" x-show="show" x-transition x-init="setTimeout(()=>show=false,2500)"
                    class="text-sm font-semibold text-emerald-600">✓ Đã cập nhật!</p>
                @endif
            </div>
        </form>
    </div>

    {{-- Change password --}}
    <div class="relative overflow-hidden rounded-[2rem] border border-white/80 bg-white/80 p-8 shadow-xl shadow-slate-900/5 backdrop-blur">
        <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-slate-900 via-slate-600 to-slate-900"></div>

        <div class="mb-6 flex items-center gap-3">
            <div class="grid h-10 w-10 shrink-0 place-items-center rounded-2xl bg-slate-100 text-slate-600">
                <i data-lucide="lock" class="h-5 w-5"></i>
            </div>
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-[.22em] text-slate-500">Bảo mật</p>
                <h2 class="text-lg font-black text-slate-950">Đổi mật khẩu</h2>
            </div>
        </div>

        <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
            @csrf @method('PUT')
            @foreach([
            ['update_password_current_password','current_password','Mật khẩu hiện tại','current-password','current_password'],
            ['update_password_password','password','Mật khẩu mới','new-password','password'],
            ['update_password_password_confirmation','password_confirmation','Xác nhận mật khẩu mới','new-password','password_confirmation'],
            ] as [$id,$nm,$lbl,$auto,$errKey])
            <div>
                <label for="{{ $id }}" class="block text-xs font-bold uppercase tracking-[.14em] text-slate-500 mb-1.5">{{ $lbl }}</label>
                <input type="password" id="{{ $id }}" name="{{ $nm }}" autocomplete="{{ $auto }}"
                    class="w-full rounded-2xl border border-slate-200 bg-slate-50/80 px-4 py-3 text-sm font-medium text-slate-900 transition focus:border-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-slate-900/10">
                @error($errKey,'updatePassword')<p class="mt-1.5 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
            </div>
            @endforeach
            <div class="flex items-center gap-4 pt-2">
                <button type="submit"
                    class="rounded-full bg-slate-950 px-6 py-2.5 text-sm font-bold text-white shadow-md transition hover:-translate-y-0.5 hover:bg-slate-800">
                    Đổi mật khẩu
                </button>
                @if(session('status') === 'password-updated')
                <p x-data="{show:true}" x-show="show" x-transition x-init="setTimeout(()=>show=false,2500)"
                    class="text-sm font-semibold text-emerald-600">✓ Đã đổi thành công!</p>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- ══════════════════════════════════════════════
     ZONE 3 · BOTTOM — Quick links + Learning path + Danger
══════════════════════════════════════════════ --}}
<div class="grid gap-4 lg:grid-cols-3">

    {{-- Quick links --}}
    <div class="relative overflow-hidden rounded-[2rem] border border-white/80 bg-white/80 p-6 shadow-xl shadow-slate-900/5 backdrop-blur">

        <div class="absolute inset-x-0 top-0 h-0.5 bg-gradient-to-r from-[#991b1b] to-amber-400"></div>

        <p class="mb-4 text-[10px] font-bold uppercase tracking-[.22em] text-slate-500">
            Truy cập nhanh
        </p>

        <div class="space-y-2">

            {{-- Dashboard --}}
            <a href="{{ route('dashboard') }}"
                class="group flex items-center gap-3 rounded-2xl border border-slate-100 bg-slate-50/60 p-3 transition hover:border-[#991b1b]/20 hover:bg-red-50/50">

                <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-white text-slate-600 shadow-sm transition group-hover:text-[#991b1b] group-hover:shadow">
                    <svg class="h-5 w-5"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="M3 3v18h18" />
                        <path d="m7 16 4-5 3 3 5-7" />
                    </svg>
                </span>

                <div class="min-w-0">
                    <p class="text-sm font-bold text-slate-900">
                        Dashboard
                    </p>
                    <p class="text-xs text-slate-400">
                        Tổng quan tiến độ
                    </p>
                </div>

                <span class="ml-auto shrink-0 text-slate-300 transition group-hover:translate-x-0.5 group-hover:text-[#991b1b]">
                    →
                </span>
            </a>


            {{-- Flashcard --}}
            <a href="{{ route('flashcards') }}"
                class="group flex items-center gap-3 rounded-2xl border border-slate-100 bg-slate-50/60 p-3 transition hover:border-[#991b1b]/20 hover:bg-red-50/50">

                <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-white text-slate-600 shadow-sm transition group-hover:text-[#991b1b] group-hover:shadow">
                    <svg class="h-5 w-5"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round">
                        <rect x="4" y="4" width="12" height="16" rx="2" />
                        <path d="M8 8h4" />
                        <path d="M8 12h4" />
                        <path d="M8 16h2" />
                        <path d="M16 7h2a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-8" />
                    </svg>
                </span>

                <div class="min-w-0">
                    <p class="text-sm font-bold text-slate-900">
                        Flashcard
                    </p>
                    <p class="text-xs text-slate-400">
                        Ôn từ vựng
                    </p>
                </div>

                <span class="ml-auto shrink-0 text-slate-300 transition group-hover:translate-x-0.5 group-hover:text-[#991b1b]">
                    →
                </span>
            </a>


            {{-- Quiz --}}
            <a href="{{ route('quiz') }}"
                class="group flex items-center gap-3 rounded-2xl border border-slate-100 bg-slate-50/60 p-3 transition hover:border-[#991b1b]/20 hover:bg-red-50/50">

                <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-white text-slate-600 shadow-sm transition group-hover:text-[#991b1b] group-hover:shadow">
                    <svg class="h-5 w-5"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round">
                        <rect x="4" y="3" width="16" height="18" rx="2" />
                        <path d="M8 7h8" />
                        <path d="M8 11h1" />
                        <path d="M11 11h5" />
                        <path d="M8 15h1" />
                        <path d="M11 15h5" />
                    </svg>
                </span>

                <div class="min-w-0">
                    <p class="text-sm font-bold text-slate-900">
                        Quiz
                    </p>
                    <p class="text-xs text-slate-400">
                        Kiểm tra kiến thức
                    </p>
                </div>

                <span class="ml-auto shrink-0 text-slate-300 transition group-hover:translate-x-0.5 group-hover:text-[#991b1b]">
                    →
                </span>
            </a>


            {{-- HSK --}}
            <a href="{{ route('hsk.overview') }}"
                class="group flex items-center gap-3 rounded-2xl border border-slate-100 bg-slate-50/60 p-3 transition hover:border-[#991b1b]/20 hover:bg-red-50/50">

                <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-white text-slate-600 shadow-sm transition group-hover:text-[#991b1b] group-hover:shadow">
                    <svg class="h-5 w-5"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="m3 10 9-6 9 6-9 6-9-6Z" />
                        <path d="M7 12v5c3 2 7 2 10 0v-5" />
                        <path d="M21 10v6" />
                    </svg>
                </span>

                <div class="min-w-0">
                    <p class="text-sm font-bold text-slate-900">
                        Lộ trình HSK
                    </p>
                    <p class="text-xs text-slate-400">
                        Học theo cấp HSK 1–6
                    </p>
                </div>

                <span class="ml-auto shrink-0 text-slate-300 transition group-hover:translate-x-0.5 group-hover:text-[#991b1b]">
                    →
                </span>
            </a>

        </div>
    </div>

    {{-- Learning path (HSK progress) --}}
    <div class="relative overflow-hidden rounded-[2rem] border border-white/80 bg-white/80 p-6 shadow-xl shadow-slate-900/5 backdrop-blur">
        <div class="absolute inset-x-0 top-0 h-0.5 bg-gradient-to-r from-blue-500 via-purple-400 to-rose-500"></div>
        <p class="mb-4 text-[10px] font-bold uppercase tracking-[.22em] text-slate-500">Lộ trình HSK</p>
        @php
        $hskColors = ['#16a34a','#2563eb','#d97706','#ea580c','#9333ea','#be123c'];
        $hskLabels = ['HSK 1','HSK 2','HSK 3','HSK 4','HSK 5','HSK 6'];
        $hskDescs = ['150 từ','300 từ','600 từ','1200 từ','2500 từ','5000+ từ'];
        @endphp
        <div class="space-y-3">
            @foreach($hskLabels as $i => $hskLabel)
            @php
            $lvl = $i + 1;
            $lessonIds = \App\Models\Lesson::where('hsk_level', $lvl)->pluck('id');
            $totalLessons = $lessonIds->count();
            $doneLessons = $totalLessons > 0
            ? $user->lessonProgresses()->whereIn('lesson_id',$lessonIds)->where('status','completed')->count()
            : 0;
            $hskPct = $totalLessons > 0 ? round(($doneLessons / $totalLessons) * 100) : 0;
            $color = $hskColors[$i];
            @endphp
            <a href="{{ route('hsk.show', $lvl) }}" class="group flex items-center gap-3">
                <div class="grid h-8 w-12 shrink-0 place-items-center rounded-xl text-white text-[11px] font-black"
                    style="background: {{ $color }}">{{ $hskLabel }}</div>
                <div class="flex-1 min-w-0">
                    <div class="mb-1 flex items-center justify-between gap-2">
                        <span class="text-xs font-semibold text-slate-700">{{ $hskDescs[$i] }}</span>
                        <span class="shrink-0 text-[10px] font-bold" style="color: {{ $color }}">{{ $hskPct }}%</span>
                    </div>
                    <div class="h-1.5 overflow-hidden rounded-full bg-slate-100">
                        <div class="h-1.5 rounded-full transition-all duration-700" style="width:{{ $hskPct }}%; background:{{ $color }}"></div>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </div>

    {{-- Danger zone --}}
    <div class="flex flex-col gap-4">
        {{-- Study today reminder --}}
        <div class="relative overflow-hidden rounded-[2rem] {{ $studiedToday ? 'bg-emerald-50 border-emerald-200' : 'bg-amber-50 border-amber-200' }} border p-5">
            <div class="flex items-center gap-3">
                <span class="text-2xl">{{ $studiedToday ? '🎉' : '⏰' }}</span>
                <div>
                    <p class="text-sm font-black {{ $studiedToday ? 'text-emerald-800' : 'text-amber-800' }}">
                        {{ $studiedToday ? 'Đã học hôm nay!' : 'Chưa học hôm nay' }}
                    </p>
                    <p class="text-xs {{ $studiedToday ? 'text-emerald-600' : 'text-amber-600' }}">
                        {{ $studiedToday ? 'Streak ' . $streak . ' ngày đang duy trì tốt 🔥' : 'Học thêm để duy trì streak ' . $streak . ' ngày!' }}
                    </p>
                </div>
            </div>
            @if(!$studiedToday)
            <a href="{{ route('flashcards') }}"
                class="mt-3 inline-block rounded-full bg-amber-500 px-4 py-1.5 text-xs font-bold text-white transition hover:bg-amber-600">
                Học ngay →
            </a>
            @endif
        </div>

        {{-- Danger zone --}}
        <div class="relative flex-1 overflow-hidden rounded-[2rem] border border-red-100 bg-red-50/60 p-6">
            <p class="text-[10px] font-semibold uppercase tracking-[.22em] text-red-400 mb-1">Vùng nguy hiểm</p>
            <h3 class="text-base font-black text-slate-900">Xóa tài khoản</h3>
            <p class="mt-2 text-xs leading-5 text-slate-500">
                Toàn bộ dữ liệu học tập, {{ $completedCount }} bài đã hoàn thành và streak {{ $streak }} ngày sẽ bị xóa vĩnh viễn.
            </p>
            <button onclick="document.getElementById('confirm-delete-modal').classList.remove('hidden')"
                class="mt-4 rounded-full border border-red-200 bg-white px-5 py-2 text-xs font-bold text-red-600 shadow-sm transition hover:bg-red-600 hover:text-white">
                Xóa tài khoản
            </button>
        </div>
    </div>

</div>

{{-- Delete confirm modal --}}
<div id="confirm-delete-modal"
    class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 backdrop-blur-sm p-4"
    onclick="if(event.target===this)this.classList.add('hidden')">
    <div class="w-full max-w-md rounded-[2rem] bg-white p-8 shadow-2xl">
        <p class="text-2xl font-black text-slate-950">Bạn chắc chắn không?</p>
        <p class="mt-3 text-sm leading-7 text-slate-600">
            Hành động này <strong class="text-red-600">không thể hoàn tác</strong>.
            Toàn bộ dữ liệu và {{ $streak }} ngày streak sẽ bị xóa vĩnh viễn.
        </p>
        <form method="POST" action="{{ route('profile.destroy') }}" class="mt-6 space-y-4">
            @csrf @method('DELETE')
            <div>
                <label for="delete_password" class="block text-xs font-bold uppercase tracking-[.14em] text-slate-500 mb-1.5">Nhập mật khẩu để xác nhận</label>
                <input type="password" id="delete_password" name="password" placeholder="••••••••"
                    class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-500/10">
                @error('password','userDeletion')<p class="mt-1.5 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
            </div>
            <div class="flex gap-3 pt-1">
                <button type="submit" class="flex-1 rounded-full bg-red-600 py-2.5 text-sm font-bold text-white transition hover:bg-red-700">Xóa tài khoản</button>
                <button type="button" onclick="document.getElementById('confirm-delete-modal').classList.add('hidden')"
                    class="flex-1 rounded-full border border-slate-200 py-2.5 text-sm font-bold text-slate-700 transition hover:bg-slate-50">Hủy bỏ</button>
            </div>
        </form>
    </div>
</div>

@endsection