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

    {{-- 4 stat tiles – 2x2 balanced Bento Grid matching Avatar Card height --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 h-full">

        {{-- Flashcard --}}
        <a href="{{ route('flashcards') }}"
           class="group flex flex-col justify-between rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-md hover:border-red-200">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Từ vựng</span>
                    <p class="text-3xl font-black text-slate-900 tracking-tight mt-1">
                        {{ number_format($stats['flashcards'] ?? 0) }}
                    </p>
                    <p class="text-xs font-semibold text-slate-700 mt-0.5">Thẻ Flashcard 3D</p>
                </div>
                <div class="grid h-11 w-11 shrink-0 place-items-center rounded-2xl bg-red-50 text-[#991b1b] transition-transform duration-300 group-hover:scale-110">
                    <i data-lucide="layers" class="h-5 w-5"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center justify-between pt-3 border-t border-slate-100 text-xs text-slate-500">
                <span>Toàn bộ HSK 1 &mdash; 6</span>
                <span class="font-bold text-[#991b1b] group-hover:translate-x-0.5 transition-transform inline-flex items-center gap-1">
                    Luyện tập <i data-lucide="arrow-right" class="h-3 w-3"></i>
                </span>
            </div>
        </a>

        {{-- Bài học --}}
        <a href="{{ route('hsk.overview') }}"
           class="group flex flex-col justify-between rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-md hover:border-blue-200">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Giáo trình</span>
                    <p class="text-3xl font-black text-slate-900 tracking-tight mt-1">
                        {{ $stats['lessons'] ?? 0 }}
                    </p>
                    <p class="text-xs font-semibold text-slate-700 mt-0.5">Bài học tiêu chuẩn</p>
                </div>
                <div class="grid h-11 w-11 shrink-0 place-items-center rounded-2xl bg-blue-50 text-blue-600 transition-transform duration-300 group-hover:scale-110">
                    <i data-lucide="book-open" class="h-5 w-5"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center justify-between pt-3 border-t border-slate-100 text-xs text-slate-500">
                <span>Hội thoại &amp; ngữ pháp</span>
                <span class="font-bold text-blue-600 group-hover:translate-x-0.5 transition-transform inline-flex items-center gap-1">
                    Khám phá <i data-lucide="arrow-right" class="h-3 w-3"></i>
                </span>
            </div>
        </a>

        {{-- Hoàn thành --}}
        <div class="flex flex-col justify-between rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-md">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Tiến độ</span>
                    <div class="flex items-baseline gap-1.5 mt-1">
                        <span class="text-3xl font-black text-slate-900 tracking-tight">{{ $stats['completed'] ?? 0 }}</span>
                        <span class="text-xs font-bold text-slate-400">/ {{ $stats['lessons'] ?? 0 }} bài</span>
                    </div>
                    <p class="text-xs font-semibold text-slate-700 mt-0.5">Đã hoàn thành</p>
                </div>
                <div class="grid h-11 w-11 shrink-0 place-items-center rounded-2xl bg-emerald-50 text-emerald-600">
                    <i data-lucide="circle-check-big" class="h-5 w-5"></i>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100">
                <div class="flex items-center justify-between text-[11px] text-slate-500 mb-1.5">
                    <span>Tỷ lệ hoàn thành</span>
                    <span class="font-bold text-emerald-600">{{ $pct }}%</span>
                </div>
                <div class="h-1.5 w-full overflow-hidden rounded-full bg-slate-100">
                    <div class="h-1.5 rounded-full bg-gradient-to-r from-emerald-500 to-teal-400 transition-all duration-500"
                         style="width: {{ $pct }}%;"></div>
                </div>
            </div>
        </div>

        {{-- Streak --}}
        <div class="flex flex-col justify-between rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-md">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Chăm chỉ</span>
                    <div class="flex items-baseline gap-1 mt-1">
                        <span class="text-3xl font-black text-slate-900 tracking-tight">{{ $stats['streak'] ?? 0 }}</span>
                        <span class="text-xs font-bold text-slate-400">ngày</span>
                    </div>
                    <p class="text-xs font-semibold text-slate-700 mt-0.5">Chuỗi ngày liên tiếp</p>
                </div>
                <div class="grid h-11 w-11 shrink-0 place-items-center rounded-2xl {{ ($stats['streak'] ?? 0) > 0 ? 'bg-amber-50 text-amber-500' : 'bg-slate-100 text-slate-400' }}">
                    <i data-lucide="flame" class="h-5 w-5"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center justify-between pt-3 border-t border-slate-100 text-xs">
                <span class="text-slate-500">Hôm nay</span>
                @if($studiedToday)
                    <span class="font-bold text-emerald-600 flex items-center gap-1">
                        <i data-lucide="check" class="h-3.5 w-3.5"></i> Đã học hôm nay
                    </span>
                @else
                    <span class="font-semibold text-amber-600 flex items-center gap-1">
                        <i data-lucide="clock" class="h-3.5 w-3.5"></i> Chưa học bài nào
                    </span>
                @endif
            </div>
        </div>

    </div>
</div>

{{-- ══════════════════════════════════════════════
     ZONE 1.5 · PET COMPANION — Bạn đồng hành Pet
══════════════════════════════════════════════ --}}
<div class="mb-6 overflow-hidden rounded-[2rem] border border-amber-200/80 bg-gradient-to-br from-amber-50/90 via-orange-50/40 to-white p-6 shadow-xl shadow-amber-900/5">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-5">
        <div class="flex items-center gap-4">
            <div class="grid h-16 w-16 shrink-0 place-items-center rounded-2xl bg-white shadow-md shadow-amber-900/10 border-2 border-amber-300 p-1">
                @if(isset($userPet) && $userPet)
                    <x-pet-avatar :stage="$userPet->stage" :mood="$userPet->getHungerState()" size="sm" :interactive="true" />
                @else
                    <span class="text-3xl">🥚</span>
                @endif
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-amber-700 bg-amber-100 border border-amber-300 px-2 py-0.5 rounded-full">
                        Bạn đồng hành
                    </span>
                    @if(isset($userPet) && $userPet)
                    <span class="text-xs font-semibold text-slate-500">
                        Giai đoạn {{ $userPet->stage }}: {{ optional($userPet->pet->stages->where('stage', $userPet->stage)->first())->name ?? 'Trứng' }}
                    </span>
                    @endif
                </div>
                <h3 class="mt-1 text-lg font-black text-slate-900">
                    {{ isset($userPet) && $userPet ? ($userPet->name ?? $userPet->pet->name) : 'Rồng Con' }}
                </h3>
                <p class="text-xs text-slate-600 mt-0.5">
                    @if(isset($userPet) && $userPet)
                        Độ no: <strong class="text-slate-900">{{ $userPet->hunger }}/100</strong> • Tích lũy nuôi: <strong class="text-amber-700">{{ $userPet->total_fed_xp }} XP</strong>
                    @else
                        Pet học tập đồng hành cùng bạn trên mọi chặng đường rèn luyện tiếng Trung.
                    @endif
                </p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('pet.index') }}"
               class="inline-flex items-center gap-2 rounded-2xl bg-gradient-to-r from-amber-500 to-[#991b1b] px-5 py-3 text-xs font-bold text-white shadow-lg shadow-red-950/15 transition hover:opacity-95 hover:shadow-xl active:scale-95">
                <i data-lucide="sparkles" class="h-4 w-4"></i>
                <span>{{ isset($userPet) && $userPet ? 'Vào phòng nuôi Pet' : 'Khám phá Pet ngay' }}</span>
                <i data-lucide="arrow-right" class="h-3.5 w-3.5"></i>
            </a>
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
     ZONE 2.2 · AMBIANCE — Không gian Cổ phong Bốn Mùa (四季时令)
══════════════════════════════════════════════ --}}
@php
$currentMonth = (int) date('n');
$currentSeasonKey = match(true) {
    $currentMonth >= 2 && $currentMonth <= 4 => 'spring',
    $currentMonth >= 5 && $currentMonth <= 7 => 'summer',
    $currentMonth >= 8 && $currentMonth <= 10 => 'autumn',
    default => 'winter',
};
$allSeasons = [
    'spring' => [
        'name' => 'Mùa Xuân',
        'cn' => '春·桃李春风',
        'icon' => '🌸',
        'period' => 'Tháng 2 – 4',
        'badge' => 'Đào hoa & Mai đỏ',
        'desc' => 'Cánh đào hồng phớt & hoa mai đỏ chu sa bồng bềnh 3D',
        'quote_cn' => '等闲识得东风面，万紫千红总是春',
        'quote_pinyin' => 'Děng xián shí dé dōng fēng miàn, wàn zǐ qiān hóng zǒng shì chūn',
        'quote_vi' => 'Nhận ra mặt gió đông quen thuộc, muôn tía nghìn hồng thảy đều xuân.',
    ],
    'summer' => [
        'name' => 'Mùa Hạ',
        'cn' => '夏·竹林夜萤',
        'icon' => '🍃',
        'period' => 'Tháng 5 – 7',
        'badge' => 'Trúc lâm & Đom đóm',
        'desc' => 'Lá trúc ngọc bích & đom đóm dạ quang lấp lánh',
        'quote_cn' => '竹深树密虫虫绿，蝉噪声中带小凉',
        'quote_pinyin' => 'Zhú shēn shù mì chóng chóng lǜ, chán zào shēng zhōng dài xiǎo liáng',
        'quote_vi' => 'Trúc thẳm cây rậm muôn trùng biếc, tiếng ve râm ran lẫn gió lành.',
    ],
    'autumn' => [
        'name' => 'Mùa Thu',
        'cn' => '秋·金叶知秋',
        'icon' => '🍁',
        'period' => 'Tháng 8 – 10',
        'badge' => 'Ngân hạnh & Lá phong',
        'desc' => 'Lá ngân hạnh vàng óng hình quạt & lá phong đỏ thắm 5 thùy xoay tít',
        'quote_cn' => '停车坐爱枫林晚，霜叶红于二月花',
        'quote_pinyin' => 'Tíng chē zuò ài fēng lín wǎn, shuāng yè hóng yú èr yuè huā',
        'quote_vi' => 'Dừng xe vì mến phong rừng muộn, sương điểm lá phong đỏ hơn hoa.',
    ],
    'winter' => [
        'name' => 'Mùa Đông',
        'cn' => '冬·瑞雪寒梅',
        'icon' => '❄️',
        'period' => 'Tháng 11 – 1',
        'badge' => 'Tuyết trắng & Hàn mai',
        'desc' => 'Tinh thể bông tuyết lục giác pha lê & cánh hàn mai đỏ trong sương',
        'quote_cn' => '忽如一夜春风来，千树万树梨花开',
        'quote_pinyin' => 'Hū rú yí yè chūn fēng lái, qiān shù wàn shù lí huā kāi',
        'quote_vi' => 'Chợt như gió xuân đêm qua tới, nghìn cành muôn nhánh nở hoa tuyết.',
    ],
];
$activeSeasonData = $allSeasons[$currentSeasonKey];
@endphp

<div class="mb-6 scroll-mt-6" id="chinese-ambiance-settings" x-data="ambianceSettings()">
    <div class="relative overflow-hidden rounded-[2rem] border border-amber-900/15 bg-white/90 p-6 sm:p-8 shadow-xl shadow-amber-950/5 backdrop-blur">
        {{-- Top decorative gradient --}}
        <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-amber-500 via-[#991b1b] to-amber-500"></div>

        {{-- Header --}}
        <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
            <div class="flex items-center gap-3.5">
                <div class="grid h-11 w-11 shrink-0 place-items-center rounded-2xl bg-amber-50 text-amber-900 border border-amber-200 shadow-sm">
                    <span class="text-xl">{{ $activeSeasonData['icon'] }}</span>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <p class="text-[10px] font-semibold uppercase tracking-[.22em] text-[#991b1b]">Tân Quốc Phong · 四季时令</p>
                        <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 border border-amber-300 px-2.5 py-0.5 text-[10px] font-bold text-amber-900">
                            <i data-lucide="lock" class="h-2.5 w-2.5 text-amber-800"></i> Tự động theo mùa
                        </span>
                    </div>
                    <h2 class="text-xl font-black text-slate-950 tracking-tight">Không Gian Học Tập Bốn Mùa</h2>
                </div>
            </div>

            {{-- Main Toggle Switch --}}
            <div class="flex items-center gap-3 bg-amber-50/70 border border-amber-200/80 rounded-2xl px-4 py-2">
                <div class="text-right">
                    <p class="text-xs font-bold text-slate-800" x-text="enabled ? 'Hiệu ứng: Đang Bật' : 'Hiệu ứng: Đã Tắt'"></p>
                    <p class="text-[10px] text-slate-500" x-text="enabled ? 'Hoa lá bay lượn nhẹ nhàng' : 'Tạm dừng toàn bộ hiệu ứng rơi'"></p>
                </div>
                <button type="button" @click="toggleAmbiance()"
                    class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
                    :class="enabled ? 'bg-emerald-600' : 'bg-slate-300'">
                    <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                        :class="enabled ? 'translate-x-5' : 'translate-x-0'"></span>
                </button>
            </div>
        </div>

        {{-- Active Season Highlight + Description --}}
        <div class="grid gap-6 lg:grid-cols-[1.3fr_0.7fr] mb-6">
            {{-- Active Season Highlight Card --}}
            <div class="rounded-2xl border border-amber-900/15 bg-gradient-to-br from-amber-50/90 via-white to-red-50/40 p-5 shadow-xs">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-amber-800 bg-amber-100 px-2 py-0.5 rounded-md border border-amber-300">
                                Tiết khí hiện tại
                            </span>
                            <span class="text-xs font-bold text-[#991b1b]">
                                {{ $activeSeasonData['period'] }}
                            </span>
                        </div>
                        <h3 class="text-lg font-black text-slate-900 mt-2 flex items-center gap-2">
                            <span class="text-2xl">{{ $activeSeasonData['icon'] }}</span>
                            <span>{{ $activeSeasonData['name'] }} · {{ $activeSeasonData['cn'] }}</span>
                        </h3>
                        <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                            {{ $activeSeasonData['desc'] }}
                        </p>
                    </div>
                    <div class="shrink-0 text-right">
                        <span class="inline-block text-[11px] font-mono font-bold text-amber-900 bg-amber-100/60 border border-amber-300/60 rounded-lg px-2.5 py-1 shadow-2xs">
                            Tháng {{ $currentMonth }} Dương lịch
                        </span>
                    </div>
                </div>

                {{-- Classical Couplet Box --}}
                <div class="mt-4 rounded-xl border border-amber-900/10 bg-white/90 p-3.5 text-center shadow-2xs">
                    <p class="text-xs font-serif font-bold text-amber-950 tracking-wide">{{ $activeSeasonData['quote_cn'] }}</p>
                    <p class="text-[10px] font-sans text-amber-800/80 italic mt-0.5">{{ $activeSeasonData['quote_pinyin'] }}</p>
                    <p class="text-[11px] text-slate-600 mt-1.5 border-t border-amber-900/10 pt-1.5 leading-snug">“{{ $activeSeasonData['quote_vi'] }}”</p>
                </div>
            </div>

            {{-- Mechanism Note & Breeze switch --}}
            <div class="flex flex-col justify-between rounded-2xl border border-slate-200 bg-slate-50/60 p-5">
                <div>
                    <p class="text-xs font-bold text-slate-800 flex items-center gap-1.5 mb-2">
                        <i data-lucide="info" class="h-4 w-4 text-[#991b1b]"></i>
                        <span>Cơ chế luân chuyển bốn mùa</span>
                    </p>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Phong cách không gian được <strong>khóa tự động theo tiết khí và mùa trong năm</strong> để giữ trọn vẹn nét tao nhã của dòng chảy thời gian. Tính năng tự do chọn mùa thủ công tạm thời được bảo lưu.
                    </p>
                </div>

                {{-- Mouse Breeze Option --}}
                <div class="mt-4 pt-3 border-t border-slate-200/80 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-slate-800 flex items-center gap-1">
                            <span>💨</span> <span>Làn gió theo trỏ chuột</span>
                        </p>
                        <p class="text-[10px] text-slate-500">Hoa lá tản nhẹ khi di chuyển chuột</p>
                    </div>
                    <button type="button" @click="toggleBreeze()" :disabled="!enabled"
                        class="relative inline-flex h-5 w-9 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
                        :class="(!enabled) ? 'opacity-40 cursor-not-allowed bg-slate-300' : (breeze ? 'bg-amber-600' : 'bg-slate-300')">
                        <span class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                            :class="breeze ? 'translate-x-4' : 'translate-x-0'"></span>
                    </button>
                </div>
            </div>
        </div>

        {{-- 4 Seasons Locked Cycle Grid --}}
        <div>
            <div class="flex items-center justify-between mb-3">
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Chu kỳ cảnh sắc 4 mùa trong năm</p>
                <span class="text-[11px] text-slate-500 flex items-center gap-1">
                    <i data-lucide="calendar" class="h-3 w-3 text-slate-400"></i> Tự động đổi theo ngày hệ thống
                </span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                @foreach($allSeasons as $sKey => $s)
                @php $isCurrent = ($sKey === $currentSeasonKey); @endphp
                <div class="relative rounded-2xl border p-4 transition {{ $isCurrent ? 'border-amber-500 bg-amber-50/70 shadow-sm ring-1 ring-amber-400/50' : 'border-slate-200 bg-slate-50/40 opacity-75' }}">
                    <div class="flex items-start justify-between mb-2">
                        <span class="text-2xl">{{ $s['icon'] }}</span>
                        @if($isCurrent)
                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 border border-emerald-300 px-2 py-0.5 text-[9px] font-black text-emerald-800">
                            ✓ Đang áp dụng
                        </span>
                        @else
                        <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 border border-slate-200 px-2 py-0.5 text-[9px] font-semibold text-slate-500">
                            <i data-lucide="lock" class="h-2.5 w-2.5"></i> Khóa
                        </span>
                        @endif
                    </div>
                    <p class="text-xs font-black text-slate-900">{{ $s['name'] }}</p>
                    <p class="text-[10px] font-bold text-amber-800 mt-0.5">{{ $s['period'] }}</p>
                    <p class="text-[11px] text-slate-500 mt-1 line-clamp-2">{{ $s['badge'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════
     ZONE 2.5 · COURSES — Khóa học của tôi
══════════════════════════════════════════════ --}}
@if(setting_bool('feature_courses', true))
<div id="my-courses" class="mb-6 scroll-mt-6">
    <div class="relative overflow-hidden rounded-[2rem] border border-white/80 bg-white/90 p-6 sm:p-8 shadow-xl shadow-slate-900/5 backdrop-blur">
        <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-[#991b1b] via-amber-400 to-[#991b1b]"></div>

        {{-- Header --}}
        <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
            <div class="flex items-center gap-3.5">
                <div class="grid h-11 w-11 shrink-0 place-items-center rounded-2xl bg-red-50 text-[#991b1b]">
                    <i data-lucide="graduation-cap" class="h-6 w-6"></i>
                </div>
                <div>
                    <p class="text-[10px] font-semibold uppercase tracking-[.22em] text-[#991b1b]">Đào tạo trực tuyến</p>
                    <h2 class="text-xl font-black text-slate-950 tracking-tight">Khóa Học Của Tôi</h2>
                </div>
            </div>

            <a href="{{ route('courses.index') }}"
               class="inline-flex items-center gap-1.5 rounded-xl bg-[#991b1b] px-4 py-2 text-xs font-bold text-white hover:bg-red-800 shadow-md shadow-red-950/15 transition">
                <i data-lucide="plus" class="h-3.5 w-3.5"></i>
                <span>Đăng ký khóa học mới</span>
            </a>
        </div>

        @if(session('success'))
        <div class="rounded-2xl bg-emerald-50 border border-emerald-200 px-4 py-3 mb-4 flex items-center gap-2.5">
            <i data-lucide="check-circle" class="h-4 w-4 text-emerald-600 shrink-0"></i>
            <p class="text-xs font-bold text-emerald-800">{{ session('success') }}</p>
        </div>
        @endif
        @if(session('error'))
        <div class="rounded-2xl bg-rose-50 border border-rose-200 px-4 py-3 mb-4 flex items-center gap-2.5">
            <i data-lucide="alert-circle" class="h-4 w-4 text-rose-600 shrink-0"></i>
            <p class="text-xs font-bold text-rose-800">{{ session('error') }}</p>
        </div>
        @endif

        @if(!isset($courseRegistrations) || $courseRegistrations->isEmpty())
            {{-- Empty state --}}
            <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50/50 p-8 text-center space-y-3">
                <div class="inline-grid h-12 w-12 place-items-center rounded-2xl bg-white border border-slate-200 text-slate-400 shadow-sm">
                    <i data-lucide="book-open" class="h-6 w-6"></i>
                </div>
                <div>
                    <p class="text-sm font-bold text-slate-800">Bạn chưa đăng ký khóa học nào</p>
                    <p class="text-xs text-slate-500 mt-0.5">Khám phá các khóa học trực tiếp qua Google Meet với giáo viên để nâng cao trình độ.</p>
                </div>

                {{-- Claim registration box --}}
                <div class="mt-4 pt-4 border-t border-slate-200/80 max-w-md mx-auto text-left">
                    <p class="text-xs font-bold text-slate-700 mb-1.5 flex items-center gap-1.5">
                        <i data-lucide="link" class="h-3.5 w-3.5 text-slate-400"></i>
                        <span>Đã đăng ký trước đó nhưng chưa thấy ở đây?</span>
                    </p>
                    <p class="text-[11px] text-slate-500 mb-3">
                        Nhập mã đơn đăng ký (VD: REG2609XXXX) được gửi đến email <strong class="text-slate-700">{{ $user->email }}</strong> để liên kết vào tài khoản.
                    </p>
                    <form method="POST" action="#" id="claim-form" class="flex gap-2">
                        @csrf
                        <input type="text" name="code" placeholder="Mã đơn: REG..." maxlength="12" required
                               class="flex-1 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-mono font-bold text-slate-900 uppercase placeholder:normal-case placeholder:font-normal placeholder:text-slate-400 focus:border-[#991b1b] focus:outline-none focus:ring-2 focus:ring-[#991b1b]/10">
                        <button type="submit" class="rounded-xl bg-slate-950 px-4 py-2 text-xs font-bold text-white hover:bg-slate-800 transition shrink-0">
                            Liên kết ngay
                        </button>
                    </form>
                    <script>
                        document.getElementById('claim-form')?.addEventListener('submit', function(e) {
                            e.preventDefault();
                            var c = this.querySelector('[name=code]').value.trim().toUpperCase();
                            if (c.length < 6) return;
                            this.action = '/courses/claim/' + c;
                            this.submit();
                        });
                    </script>
                </div>
            </div>
        @else
            {{-- List of courses --}}
            <div class="space-y-3.5">
                @foreach($courseRegistrations as $reg)
                @php
                    $statusColors = [
                        'pending'       => 'bg-amber-50 border-amber-200 text-amber-800',
                        'contacted'     => 'bg-blue-50 border-blue-200 text-blue-800',
                        'consulted'     => 'bg-indigo-50 border-indigo-200 text-indigo-800',
                        'deposit_paid'  => 'bg-purple-50 border-purple-200 text-purple-800',
                        'paid'          => 'bg-emerald-50 border-emerald-200 text-emerald-800',
                        'enrolled'      => 'bg-green-50 border-green-200 text-green-800',
                        'cancelled'     => 'bg-rose-50 border-rose-200 text-rose-800',
                    ];
                    $badgeClass = $statusColors[$reg->status] ?? 'bg-slate-50 border-slate-200 text-slate-700';
                @endphp
                <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-5 hover:bg-white hover:shadow-md transition-all space-y-3">
                    <div class="flex flex-wrap items-start justify-between gap-2">
                        <div class="min-w-0 flex-1">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Khóa học</span>
                            <h3 class="text-base font-black text-slate-900 leading-tight mt-0.5">{{ $reg->course->title }}</h3>
                            <p class="text-xs text-slate-600 mt-1">Học viên: <strong class="text-slate-900">{{ $reg->full_name }}</strong></p>
                            @if($reg->courseClass)
                            <p class="text-xs text-slate-500 mt-0.5 flex items-center gap-1">
                                <i data-lucide="calendar" class="h-3 w-3 text-slate-400"></i>
                                <span>{{ $reg->courseClass->name }} &bull; {{ $reg->courseClass->schedule_days }} ({{ $reg->courseClass->schedule_time }})</span>
                            </p>
                            @elseif($reg->preferred_schedule)
                            <p class="text-xs text-slate-500 mt-0.5">Lịch học mong muốn: {{ $reg->preferred_schedule }}</p>
                            @endif
                        </div>
                        <span class="shrink-0 rounded-full border px-2.5 py-1 text-[11px] font-bold {{ $badgeClass }}">
                            {{ $reg->status_label }}
                        </span>
                    </div>

                    {{-- Details grid --}}
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 pt-2 border-t border-slate-200/60 text-xs">
                        <div>
                            <span class="text-[10px] uppercase font-bold text-slate-400 block">Mã đơn</span>
                            <span class="font-black font-mono text-[#991b1b] mt-0.5 block">{{ $reg->registration_code }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] uppercase font-bold text-slate-400 block">Ngày đăng ký</span>
                            <span class="font-semibold text-slate-700 mt-0.5 block">{{ $reg->created_at->format('d/m/Y') }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] uppercase font-bold text-slate-400 block">Trạng thái thanh toán</span>
                            <span class="font-bold mt-0.5 block {{ $reg->payment_status === 'paid' ? 'text-emerald-700' : 'text-amber-700' }}">
                                {{ $reg->payment_status_label }}
                            </span>
                        </div>
                        <div>
                            <span class="text-[10px] uppercase font-bold text-slate-400 block">Học phí</span>
                            <span class="font-black text-slate-900 mt-0.5 block">{{ $reg->formatted_payment_amount }}</span>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex flex-wrap items-center justify-between gap-2 pt-2 border-t border-slate-200/60">
                        <div class="text-[11px] text-slate-500">
                            Nội dung chuyển khoản: <strong class="font-mono text-slate-800">{{ $reg->payment_reference }}</strong>
                        </div>
                        <a href="{{ route('courses.success', $reg->registration_code) }}"
                           class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-xs font-bold text-slate-700 hover:bg-slate-100 hover:text-slate-900 transition shadow-sm">
                            <i data-lucide="qr-code" class="h-3.5 w-3.5 text-emerald-600"></i>
                            <span>Xem mã QR chuyển khoản</span>
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endif

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

@push('scripts')
<script>
function ambianceSettings() {
    return {
        enabled: localStorage.getItem('chinese_ambiance_enabled') !== 'false',
        breeze: localStorage.getItem('chinese_ambiance_breeze') !== 'false',
        toggleAmbiance() {
            this.enabled = !this.enabled;
            localStorage.setItem('chinese_ambiance_enabled', this.enabled ? 'true' : 'false');
            window.chineseAmbiance?.setEnabled(this.enabled);
            if (this.enabled) {
                window.toast?.success('Đã bật hiệu ứng không gian bốn mùa cổ phong 🌸');
            } else {
                window.toast?.info('Đã tắt hiệu ứng không gian cổ phong');
            }
        },
        toggleBreeze() {
            this.breeze = !this.breeze;
            localStorage.setItem('chinese_ambiance_breeze', this.breeze ? 'true' : 'false');
            window.chineseAmbiance?.toggleMouseBreeze();
            if (this.breeze) {
                window.toast?.success('Đã bật hiệu ứng gió theo con trỏ chuột 💨');
            } else {
                window.toast?.info('Đã tắt hiệu ứng gió theo chuột');
            }
        }
    };
}
</script>
@endpush

@endsection