@extends('layouts.app')

@section('title', 'Lịch Sử Thi Đấu · Chinese Duel | Learn Chinese')

@section('content')
<div class="max-w-5xl mx-auto space-y-8 pb-16 font-serif-custom select-none">

    {{-- Top Ambience Banner --}}
    <section class="relative overflow-hidden rounded-[2.25rem] border border-amber-900/20 bg-gradient-to-br from-[#faf4ee] via-[#f7ebe0] to-[#f3dfd2] p-6 sm:p-8 shadow-xl shadow-amber-950/5">
        <div class="pointer-events-none absolute -right-6 -bottom-8 select-none text-[8rem] sm:text-[10rem] font-serif font-black leading-none text-amber-900/[0.04]">
            战绩
        </div>

        <div class="relative z-10 flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
            <div class="max-w-xl">
                <div class="flex flex-wrap items-center gap-2 mb-3">
                    <span class="seal-zhusha-round text-xs font-bold tracking-widest">
                        战 绩 · 历 史
                    </span>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-white/80 border border-amber-900/10 px-3 py-1 text-xs font-semibold text-slate-700 shadow-2xs font-sans">
                        <i data-lucide="trophy" class="h-3.5 w-3.5 text-amber-600"></i>
                        <span>Chinese Duel Record</span>
                    </span>
                </div>

                <h1 class="text-3xl sm:text-4xl font-black tracking-tight text-slate-900 font-serif">
                    Lịch Sử Thi Đấu
                </h1>
                <p class="mt-2 text-xs sm:text-sm leading-relaxed text-slate-600 font-sans">
                    Tổng kết thành tích giao phong đối kháng 1v1 trên đấu trường HSK. Xem lại từng trận đấu và theo dõi sự tiến bộ phản xạ từ vựng của bạn.
                </p>
            </div>

            {{-- 4 Stat Badges --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 shrink-0 font-sans">
                <div class="rounded-2xl bg-white/90 border border-slate-200/80 p-3.5 text-center shadow-xs">
                    <span class="text-xs text-slate-400 font-semibold uppercase">Tổng Trận</span>
                    <p class="text-2xl font-black text-slate-900 mt-0.5">{{ $stats['total'] }}</p>
                </div>
                <div class="rounded-2xl bg-white/90 border border-slate-200/80 p-3.5 text-center shadow-xs">
                    <span class="text-xs text-emerald-600 font-semibold uppercase">Thắng</span>
                    <p class="text-2xl font-black text-emerald-700 mt-0.5">{{ $stats['wins'] }}</p>
                </div>
                <div class="rounded-2xl bg-white/90 border border-slate-200/80 p-3.5 text-center shadow-xs">
                    <span class="text-xs text-rose-600 font-semibold uppercase">Thua</span>
                    <p class="text-2xl font-black text-rose-700 mt-0.5">{{ $stats['losses'] }}</p>
                </div>
                <div class="rounded-2xl bg-white/90 border border-slate-200/80 p-3.5 text-center shadow-xs">
                    <span class="text-xs text-amber-600 font-semibold uppercase">Tỷ Lệ Thắng</span>
                    <p class="text-2xl font-black text-amber-700 mt-0.5">{{ $stats['win_rate'] }}%</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Guest Alert if applicable --}}
    @if ($isGuest)
        <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-xs sm:text-sm font-sans flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <i data-lucide="info" class="h-4 w-4 shrink-0 text-amber-700"></i>
                <span>Bạn đang xem lịch sử với tư cách <strong>Khách vãng lai</strong>. Dữ liệu chỉ lưu tạm trên trình duyệt hiện tại. Hãy <a href="{{ route('register') }}" class="font-bold underline hover:text-amber-950">Đăng ký tài khoản</a> để đồng bộ lịch sử vĩnh viễn!</span>
            </div>
        </div>
    @endif

    {{-- Matches List --}}
    <section class="bg-white/90 backdrop-blur rounded-3xl border border-slate-200/80 p-6 sm:p-8 shadow-sm space-y-5">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <h2 class="text-xl sm:text-2xl font-bold text-slate-900 font-serif">Các Trận Gần Đây</h2>
            <a href="{{ route('duel.lobby') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-[#991b1b] hover:text-red-900 font-sans transition">
                <i data-lucide="plus-circle" class="h-4 w-4"></i>
                <span>Vào Đấu Trận Mới</span>
            </a>
        </div>

        @if ($matches->isEmpty())
            <div class="py-12 text-center font-sans space-y-4">
                <div class="grid h-16 w-16 place-items-center rounded-full bg-slate-100 text-slate-400 mx-auto">
                    <i data-lucide="swords" class="h-8 w-8"></i>
                </div>
                <div>
                    <p class="text-base font-bold text-slate-800">Chưa có trận đấu nào được ghi nhận</p>
                    <p class="text-xs text-slate-500 mt-1">Hãy tham gia đấu trường Chinese Duel để so tài phản xạ tiếng Trung ngay!</p>
                </div>
                <a href="{{ route('duel.lobby') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[#991b1b] hover:bg-red-800 text-white font-bold text-xs shadow-md transition">
                    <i data-lucide="zap" class="h-4 w-4"></i>
                    <span>Bắt Đầu Ghép Trận Ngay</span>
                </a>
            </div>
        @else
            <div class="space-y-3 font-sans">
                @foreach ($matches as $m)
                    @php
                        $currentUserId = auth()->id();
                        $mySlot = 1;
                        if ($currentUserId) {
                            $mySlot = ($m->player1_id === $currentUserId) ? 1 : 2;
                        } else {
                            $guestToken = request()->cookie("duel_guest_token_{$m->room_code}");
                            $tokenHash = $guestToken ? hash_hmac('sha256', $guestToken, config('app.key')) : null;
                            if ($tokenHash && $m->player2_guest_token_hash === $tokenHash) {
                                $mySlot = 2;
                            }
                        }

                        $myScore = ($mySlot === 1) ? $m->player1_score : $m->player2_score;
                        $oppScore = ($mySlot === 1) ? $m->player2_score : $m->player1_score;
                        $oppName = ($mySlot === 1) ? $m->player2_name : $m->player1_name;
                        $oppAvatar = ($mySlot === 1) ? $m->player2_avatar : $m->player1_avatar;

                        $isWin = false;
                        $isLoss = false;
                        $isDraw = false;
                        $isForfeit = ($m->status === 'abandoned');

                        if ($m->is_draw) {
                            $isDraw = true;
                        } elseif ($isForfeit) {
                            if ($m->forfeit_by_slot) {
                                $isWin = ($m->forfeit_by_slot !== $mySlot);
                                $isLoss = ($m->forfeit_by_slot === $mySlot);
                            } else {
                                $isDraw = true;
                            }
                        } elseif ($m->winner_slot === $mySlot) {
                            $isWin = true;
                        } else {
                            $isLoss = true;
                        }
                    @endphp

                    <div class="flex flex-wrap items-center justify-between gap-4 p-4 rounded-2xl border border-slate-200/80 bg-white hover:border-slate-300 hover:shadow-xs transition">
                        {{-- Left: Opponent Info & Room Details --}}
                        <div class="flex items-center gap-3.5 min-w-[200px]">
                            <div class="grid h-11 w-11 place-items-center rounded-xl bg-slate-100 ring-2 ring-slate-200 overflow-hidden shrink-0">
                                @if ($oppAvatar)
                                    <img src="{{ $oppAvatar }}" class="h-full w-full object-cover">
                                @else
                                    <span class="font-black text-slate-700 text-sm">{{ strtoupper(substr($oppName ?? 'U', 0, 1)) }}</span>
                                @endif
                            </div>
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <p class="font-bold text-slate-900 text-sm truncate max-w-[140px] sm:max-w-[180px]">{{ $oppName }}</p>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.2 text-[10px] font-bold text-slate-600">
                                        HSK {{ $m->hsk_level }}
                                    </span>
                                </div>
                                <p class="text-[11px] text-slate-400 mt-0.5">
                                    Phòng: <span class="font-mono text-slate-600 font-semibold">{{ $m->room_code }}</span> · 
                                    {{ $m->finished_at ? $m->finished_at->format('d/m/Y H:i') : $m->created_at->format('d/m/Y H:i') }}
                                </p>
                            </div>
                        </div>

                        {{-- Middle: Outcome Badge --}}
                        <div class="flex items-center gap-3">
                            @if ($isWin)
                                <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 text-emerald-800 font-bold text-xs px-3 py-1">
                                    <i data-lucide="trophy" class="h-3.5 w-3.5"></i>
                                    <span>Thắng</span>
                                </span>
                            @elseif ($isLoss)
                                <span class="inline-flex items-center gap-1 rounded-full bg-rose-100 text-rose-800 font-bold text-xs px-3 py-1">
                                    <i data-lucide="x-circle" class="h-3.5 w-3.5"></i>
                                    <span>Thua</span>
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 text-amber-800 font-bold text-xs px-3 py-1">
                                    <i data-lucide="minus-circle" class="h-3.5 w-3.5"></i>
                                    <span>Hòa</span>
                                </span>
                            @endif

                            {{-- Scores --}}
                            <div class="text-right font-mono font-black text-sm sm:text-base">
                                <span class="{{ $isWin ? 'text-emerald-700' : 'text-slate-800' }}">{{ $myScore }}</span>
                                <span class="text-slate-300 font-normal"> - </span>
                                <span class="{{ $isLoss ? 'text-rose-700' : 'text-slate-500' }}">{{ $oppScore }}</span>
                            </div>
                        </div>

                        {{-- Right: Detail Button --}}
                        <div class="shrink-0">
                            <a href="{{ route('duel.result', $m->room_code) }}"
                                class="inline-flex items-center gap-1 py-1.5 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                                <span>Chi tiết</span>
                                <i data-lucide="chevron-right" class="h-3.5 w-3.5"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination Links --}}
            <div class="pt-4 border-t border-slate-100">
                {{ $matches->links() }}
            </div>
        @endif
    </section>

</div>
@endsection
