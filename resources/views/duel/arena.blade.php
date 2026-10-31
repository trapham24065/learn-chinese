@extends('layouts.app')

@section('title', 'Sàn Đấu Chinese Duel 1v1 · ' . $roomCode . ' | Learn Chinese')

@section('content')
<div x-data="duelArenaApp('{{ $roomCode }}', {{ $slot }}, {{ Js::from($initialState) }})"
     x-init="initArena()"
     @keydown.window="handleKeyPress($event)"
     class="relative max-w-5xl mx-auto pb-16 font-serif-custom select-none space-y-6">

    {{-- TOP BAR: Navigation, Match Info & Connection Status --}}
    <header class="flex flex-wrap items-center justify-between gap-3 bg-white/80 backdrop-blur rounded-2xl border border-slate-200/80 px-4 py-3 shadow-xs">
        <div class="flex items-center gap-2 sm:gap-3">
            <a href="{{ route('duel.lobby') }}" class="p-1.5 rounded-xl hover:bg-slate-100 text-slate-500 hover:text-slate-800 transition" title="Rời sàn đấu">
                <i data-lucide="arrow-left" class="h-4 w-4"></i>
            </a>
            <div class="h-4 w-px bg-slate-200"></div>
            <div class="flex items-center gap-2">
                <span class="seal-zhusha-round text-[10px] font-bold tracking-widest px-2 py-0.5">
                    争 锋
                </span>
                <span class="text-xs sm:text-sm font-bold text-slate-900 font-sans">
                    Phòng: <span class="font-mono text-red-800" x-text="roomCode"></span>
                </span>
                <span class="rounded-full bg-red-100/80 border border-red-200 px-2 py-0.5 text-[11px] font-semibold text-red-900 font-sans">
                    HSK <span x-text="room.hsk_level"></span>
                </span>
            </div>
        </div>

        {{-- Connection Indicator & Surrender Button --}}
        <div class="flex items-center gap-3 font-sans text-xs">
            {{-- Opponent Offline Warning --}}
            <template x-if="room.opponent_offline && room.status === 'in_progress'">
                <span class="flex items-center gap-1.5 text-amber-700 bg-amber-50 border border-amber-200 px-2.5 py-1 rounded-full animate-pulse">
                    <i data-lucide="wifi-off" class="h-3.5 w-3.5"></i>
                    <span>Đối thủ mất kết nối</span>
                </span>
            </template>

            {{-- Live Status Dot --}}
            <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-slate-100 text-slate-600">
                <span class="relative flex h-2 w-2">
                    <span x-show="connectionState === 'connected'" class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2"
                        :class="{
                            'bg-emerald-500': connectionState === 'connected',
                            'bg-amber-500': connectionState === 'syncing',
                            'bg-rose-500': connectionState === 'error'
                        }"></span>
                </span>
                <span x-text="getConnectionLabel()" class="font-medium text-[11px]"></span>
            </div>

            {{-- Nút Đầu Hàng Trong Trận --}}
            <template x-if="room.status === 'in_progress' || room.status === 'countdown'">
                <button type="button"
                    @click="confirmSurrender = true"
                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 text-xs font-semibold transition cursor-pointer"
                    title="Nhận thua trận đấu">
                    <i data-lucide="flag" class="h-3 w-3 text-rose-600"></i>
                    <span class="hidden sm:inline">Đầu hàng</span>
                </button>
            </template>
        </div>
    </header>

    {{-- 1. WAITING ROOM SCREEN (When waiting for Player 2) --}}
    <section x-show="room.status === 'waiting'" x-cloak class="rounded-3xl border border-slate-200 bg-white/95 backdrop-blur p-8 sm:p-12 text-center shadow-lg">
        <div class="max-w-md mx-auto space-y-6">
            {{-- Pulsing Radar Swords --}}
            <div class="relative grid h-24 w-24 mx-auto place-items-center">
                <div class="absolute inset-0 rounded-full bg-red-100 animate-ping opacity-75"></div>
                <div class="absolute inset-2 rounded-full bg-red-200 animate-pulse"></div>
                <div class="relative grid h-16 w-16 place-items-center rounded-full bg-[#991b1b] text-white shadow-md">
                    <i data-lucide="swords" class="h-8 w-8"></i>
                </div>
            </div>

            <div>
                <h2 class="text-2xl sm:text-3xl font-black text-slate-900 font-serif">Đang Chờ Đối Thủ...</h2>
                <p class="text-sm text-slate-600 font-sans mt-2">
                    Trận đấu HSK <span x-text="room.hsk_level" class="font-bold"></span> sẽ tự động bắt đầu ngay khi có người thứ 2 tham gia.
                </p>
            </div>

            {{-- Share Link & Code Box (For Private Lobby) --}}
            <div class="rounded-2xl bg-amber-50/70 border border-amber-200/80 p-5 space-y-4 text-left font-sans">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Mã Phòng Thách Đấu</label>
                    <div class="flex items-center gap-2">
                        <div class="flex-1 font-mono font-black text-2xl tracking-[0.2em] text-[#991b1b] bg-white rounded-xl border border-amber-200 px-4 py-2 text-center" x-text="roomCode"></div>
                        <button type="button"
                            @click="copyToClipboard(roomCode, 'code')"
                            class="px-4 py-3 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-xs transition flex items-center gap-1.5 shrink-0 cursor-pointer">
                            <i data-lucide="copy" class="h-4 w-4" x-show="copySuccess !== 'code'"></i>
                            <i data-lucide="check" class="h-4 w-4 text-emerald-400" x-show="copySuccess === 'code'" x-cloak></i>
                            <span x-text="copySuccess === 'code' ? 'Đã sao chép' : 'Sao chép'"></span>
                        </button>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Đường Link Tham Gia Trực Tiếp</label>
                    <div class="flex items-center gap-2">
                        <input type="text" readonly :value="getInviteUrl()"
                            class="flex-1 text-xs text-slate-600 bg-white rounded-xl border border-amber-200 px-3 py-2 truncate">
                        <button type="button"
                            @click="copyToClipboard(getInviteUrl(), 'url')"
                            class="px-3.5 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs shadow-xs transition flex items-center gap-1 shrink-0 cursor-pointer">
                            <i data-lucide="share-2" class="h-3.5 w-3.5" x-show="copySuccess !== 'url'"></i>
                            <i data-lucide="check" class="h-3.5 w-3.5 text-emerald-300" x-show="copySuccess === 'url'" x-cloak></i>
                            <span x-text="copySuccess === 'url' ? 'Đã copy' : 'Copy link'"></span>
                        </button>
                    </div>
                </div>
            </div>

            <div class="pt-2">
                <button type="button"
                    @click="cancelWaitingRoom()"
                    class="inline-flex items-center gap-2 text-xs font-medium text-slate-500 hover:text-rose-700 font-sans transition cursor-pointer">
                    <i data-lucide="x" class="h-3.5 w-3.5"></i>
                    <span>Hủy phòng và quay về sảnh chính</span>
                </button>
            </div>
        </div>
    </section>

    {{-- 2. PRE-MATCH COUNTDOWN OVERLAY (`status === 'countdown'`) --}}
    <div x-show="room.status === 'countdown'" x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 backdrop-blur-md p-4 transition-opacity">
        <div class="max-w-md w-full bg-gradient-to-b from-[#faf5ee] to-[#f4ebe1] rounded-3xl border-2 border-amber-300/60 p-8 shadow-2xl text-center space-y-6">
            <span class="seal-zhusha-round text-xs font-bold tracking-widest px-3 py-1">
                CHUẨN BỊ TRANH PHONG
            </span>

            {{-- Two Avatars Face-off --}}
            <div class="flex items-center justify-center gap-4 sm:gap-6 py-2">
                {{-- Player 1 --}}
                <div class="flex flex-col items-center">
                    <div class="relative grid h-16 w-16 place-items-center rounded-full ring-4 ring-red-600 bg-red-100 overflow-hidden shadow-lg">
                        <template x-if="room.player1?.avatar">
                            <img :src="room.player1.avatar" class="h-full w-full object-cover">
                        </template>
                        <template x-if="!room.player1?.avatar">
                            <span class="text-xl font-black text-red-900" x-text="getInitial(room.player1?.name)"></span>
                        </template>
                    </div>
                    <span class="mt-2 text-xs font-bold text-slate-800 truncate max-w-[100px]" x-text="room.player1?.name"></span>
                </div>

                <div class="grid h-12 w-12 place-items-center rounded-2xl bg-slate-900 text-amber-400 font-black text-lg font-sans shadow-inner">
                    VS
                </div>

                {{-- Player 2 --}}
                <div class="flex flex-col items-center">
                    <div class="relative grid h-16 w-16 place-items-center rounded-full ring-4 ring-indigo-600 bg-indigo-100 overflow-hidden shadow-lg">
                        <template x-if="room.player2?.avatar">
                            <img :src="room.player2.avatar" class="h-full w-full object-cover">
                        </template>
                        <template x-if="!room.player2?.avatar">
                            <span class="text-xl font-black text-indigo-900" x-text="getInitial(room.player2?.name)"></span>
                        </template>
                    </div>
                    <span class="mt-2 text-xs font-bold text-slate-800 truncate max-w-[100px]" x-text="room.player2?.name"></span>
                </div>
            </div>

            {{-- Giant Animated Countdown Number --}}
            <div class="py-2">
                <div class="text-6xl sm:text-7xl font-black font-sans text-red-800 tracking-tight animate-bounce"
                     x-text="Math.ceil(localTimeRemaining) || 3"></div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-widest mt-2 font-sans">
                    Hiệp 1 sắp bắt đầu...
                </p>
            </div>
        </div>
    </div>

    {{-- 3. MAIN BATTLE ARENA (In Progress or Finished) --}}
    <div x-show="room.status === 'in_progress' || room.status === 'finished' || room.status === 'abandoned'" class="space-y-6">

        {{-- A. BATTLE HEADER & TUG-OF-WAR BAR --}}
        <section class="rounded-3xl border border-slate-200/80 bg-white/95 backdrop-blur p-5 sm:p-7 shadow-md space-y-5">
            {{-- Players Face-off Row --}}
            <div class="grid grid-cols-2 gap-4 items-center">

                {{-- Player 1 (Red / Left) --}}
                <div class="flex items-center gap-3 sm:gap-4">
                    <div class="relative shrink-0">
                        <div class="grid h-12 w-12 sm:h-14 sm:w-14 place-items-center rounded-2xl ring-2 sm:ring-4 ring-red-600 bg-red-50 overflow-hidden shadow-sm">
                            <template x-if="room.player1?.avatar">
                                <img :src="room.player1.avatar" class="h-full w-full object-cover">
                            </template>
                            <template x-if="!room.player1?.avatar">
                                <span class="text-base sm:text-lg font-black text-red-900" x-text="getInitial(room.player1?.name)"></span>
                            </template>
                        </div>
                        <template x-if="slot === 1">
                            <span class="absolute -top-1.5 -left-1.5 rounded-full bg-amber-400 text-slate-950 font-black text-[9px] px-1.5 py-0.2 shadow-xs font-sans">
                                BẠN
                            </span>
                        </template>
                    </div>

                    <div class="min-w-0">
                        <div class="flex items-center gap-1.5">
                            <p class="truncate text-sm sm:text-base font-bold text-slate-900 font-sans" x-text="room.player1?.name"></p>
                        </div>
                        <div class="flex items-center gap-2 mt-0.5">
                            <span class="text-xl sm:text-2xl font-black text-red-800 font-sans tracking-tight" x-text="room.player1?.score || 0"></span>
                            <span class="text-[11px] text-slate-400 font-sans">điểm</span>
                        </div>
                        {{-- Submission Indicator --}}
                        <div class="mt-1">
                            <template x-if="slot === 1 ? room.my_submitted : room.opponent_submitted">
                                <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full font-sans">
                                    <i data-lucide="check" class="h-3 w-3"></i> Đã nộp bài
                                </span>
                            </template>
                            <template x-if="!(slot === 1 ? room.my_submitted : room.opponent_submitted)">
                                <span class="text-[11px] text-slate-400 italic font-sans flex items-center gap-1">
                                    <span class="inline-block h-1.5 w-1.5 rounded-full bg-slate-300 animate-pulse"></span>
                                    Đang suy nghĩ...
                                </span>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- Player 2 (Blue / Right) --}}
                <div class="flex items-center justify-end gap-3 sm:gap-4 text-right">
                    <div class="min-w-0">
                        <div class="flex items-center justify-end gap-1.5">
                            <p class="truncate text-sm sm:text-base font-bold text-slate-900 font-sans" x-text="room.player2?.name"></p>
                        </div>
                        <div class="flex items-center justify-end gap-2 mt-0.5">
                            <span class="text-[11px] text-slate-400 font-sans">điểm</span>
                            <span class="text-xl sm:text-2xl font-black text-indigo-800 font-sans tracking-tight" x-text="room.player2?.score || 0"></span>
                        </div>
                        {{-- Submission Indicator --}}
                        <div class="mt-1 flex justify-end">
                            <template x-if="slot === 2 ? room.my_submitted : room.opponent_submitted">
                                <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full font-sans">
                                    <i data-lucide="check" class="h-3 w-3"></i> Đã nộp bài
                                </span>
                            </template>
                            <template x-if="!(slot === 2 ? room.my_submitted : room.opponent_submitted)">
                                <span class="text-[11px] text-slate-400 italic font-sans flex items-center gap-1">
                                    <span class="inline-block h-1.5 w-1.5 rounded-full bg-slate-300 animate-pulse"></span>
                                    Đang suy nghĩ...
                                </span>
                            </template>
                        </div>
                    </div>

                    <div class="relative shrink-0">
                        <div class="grid h-12 w-12 sm:h-14 sm:w-14 place-items-center rounded-2xl ring-2 sm:ring-4 ring-indigo-600 bg-indigo-50 overflow-hidden shadow-sm">
                            <template x-if="room.player2?.avatar">
                                <img :src="room.player2.avatar" class="h-full w-full object-cover">
                            </template>
                            <template x-if="!room.player2?.avatar">
                                <span class="text-base sm:text-lg font-black text-indigo-900" x-text="getInitial(room.player2?.name)"></span>
                            </template>
                        </div>
                        <template x-if="slot === 2">
                            <span class="absolute -top-1.5 -right-1.5 rounded-full bg-amber-400 text-slate-950 font-black text-[9px] px-1.5 py-0.2 shadow-xs font-sans">
                                BẠN
                            </span>
                        </template>
                    </div>
                </div>

            </div>

            {{-- B. TUG-OF-WAR PROGRESS BAR --}}
            <div class="space-y-1.5 pt-2">
                <div class="flex items-center justify-between text-[11px] font-sans font-bold uppercase tracking-wider text-slate-500">
                    <span class="flex items-center gap-1 text-red-800">
                        <span>Chu Sa</span> · <span x-text="getScoreDiffLabel(1)"></span>
                    </span>
                    <span class="text-slate-400 font-normal">Tug-of-War Kéo Co</span>
                    <span class="flex items-center gap-1 text-indigo-800">
                        <span x-text="getScoreDiffLabel(2)"></span> · <span>Lam Thẫm</span>
                    </span>
                </div>

                {{-- The Dual-Colored Bar Container --}}
                <div class="relative h-6 sm:h-7 rounded-full bg-slate-200 border-2 border-slate-300/80 p-0.5 overflow-visible shadow-inner flex items-center">
                    {{-- Left Bar (Player 1 Red Gradient) --}}
                    <div class="h-full rounded-l-full bg-gradient-to-r from-rose-700 via-red-600 to-amber-500 transition-all duration-700 ease-out shadow-xs"
                         :style="'width: ' + tugPercent + '%'"></div>

                    {{-- Right Bar (Player 2 Blue Gradient) --}}
                    <div class="h-full rounded-r-full bg-gradient-to-l from-indigo-800 via-blue-600 to-cyan-500 transition-all duration-700 ease-out shadow-xs"
                         :style="'width: ' + (100 - tugPercent) + '%'"></div>

                    {{-- Center Knot / Swords Marker --}}
                    <div class="absolute top-1/2 -translate-y-1/2 -translate-x-1/2 z-10 transition-all duration-700 ease-out flex items-center justify-center"
                         :style="'left: ' + tugPercent + '%'">
                        <div class="grid h-8 w-8 sm:h-9 sm:w-9 place-items-center rounded-full bg-slate-950 text-amber-300 border-2 border-amber-300 shadow-lg ring-2 ring-slate-950/20 transform hover:scale-110 transition">
                            <span class="text-xs sm:text-sm">⚔️</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- C. ROUND & TIMER HUD --}}
        <section class="flex flex-wrap items-center justify-between gap-3 bg-white/90 backdrop-blur rounded-2xl border border-slate-200 px-5 py-3.5 shadow-xs font-sans">
            {{-- Round Indicator --}}
            <div class="flex items-center gap-3">
                <span class="rounded-xl bg-slate-900 text-white font-bold text-xs px-3 py-1.5 uppercase tracking-wider font-sans">
                    Hiệp <span x-text="room.current_round || 1"></span> / 10
                </span>
                <div class="hidden sm:flex items-center gap-1.5">
                    <template x-for="r in 10" :key="r">
                        <span class="h-2 w-2 rounded-full transition-all"
                            :class="{
                                'bg-[#991b1b] ring-2 ring-red-200': r === room.current_round,
                                'bg-emerald-500': r < room.current_round,
                                'bg-slate-200': r > room.current_round
                            }"></span>
                    </template>
                </div>
            </div>

            {{-- Timer and Phase Status --}}
            <div class="flex items-center gap-3">
                {{-- Revealing Phase Badge --}}
                <template x-if="room.round_phase === 'revealing'">
                    <span class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-800 bg-emerald-100 border border-emerald-300 px-3 py-1 rounded-full animate-pulse">
                        <i data-lucide="eye" class="h-3.5 w-3.5"></i>
                        <span>KẾT QUẢ · <span x-text="Math.max(0, localTimeRemaining).toFixed(1)"></span>s</span>
                    </span>
                </template>

                {{-- Answering Phase Timer --}}
                <template x-if="room.round_phase === 'answering'">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Thời gian:</span>
                        <div class="flex items-center gap-1.5 px-3 py-1 rounded-full font-mono font-black text-sm transition"
                            :class="{
                                'bg-red-100 text-red-900 border border-red-300 animate-pulse': localTimeRemaining <= 3.0,
                                'bg-amber-100 text-amber-900 border border-amber-300': localTimeRemaining > 3.0 && localTimeRemaining <= 6.0,
                                'bg-slate-100 text-slate-800 border border-slate-200': localTimeRemaining > 6.0
                            }">
                            <i data-lucide="timer" class="h-4 w-4"></i>
                            <span x-text="Math.max(0, localTimeRemaining).toFixed(1) + 's'"></span>
                        </div>
                    </div>
                </template>
            </div>
        </section>

        {{-- D. QUESTION ARENA CARD --}}
        <section class="rounded-3xl border border-slate-200 bg-white/95 backdrop-blur p-6 sm:p-10 shadow-lg space-y-6">

            {{-- Chinese Question & Pinyin --}}
            <div class="text-center py-4 space-y-2 border-b border-slate-100">
                {{-- Skill Tag --}}
                <div class="flex items-center justify-center gap-2 mb-2">
                    <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-[11px] font-semibold text-slate-600 font-sans uppercase tracking-wider">
                        Trắc nghiệm tiếng Trung
                    </span>
                </div>

                {{-- Big Chinese Hanzi --}}
                <h2 class="text-4xl sm:text-5xl md:text-6xl font-black text-slate-900 font-serif tracking-wide py-2"
                    x-text="currentQuestion?.question || 'Đang tải câu hỏi...'">
                </h2>

                {{-- Pinyin --}}
                <template x-if="currentQuestion?.pinyin">
                    <p class="text-base sm:text-xl font-medium text-amber-800 font-sans tracking-wide"
                       x-text="currentQuestion.pinyin"></p>
                </template>
            </div>

            {{-- 4 Answer Options Grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 pt-2">
                <template x-for="(opt, idx) in (currentQuestion?.options || [])" :key="idx">
                    <button type="button"
                        @click="submitOption(opt)"
                        :disabled="isOptionDisabled()"
                        :class="getOptionClass(opt, idx)"
                        class="group relative flex items-center gap-3.5 p-4 sm:p-5 rounded-2xl border-2 transition-all duration-200 text-left font-sans cursor-pointer min-h-[64px] active:scale-[0.98]">

                        {{-- Option Key Badge (A, B, C, D) --}}
                        <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl font-mono font-black text-sm transition"
                            :class="getOptionBadgeClass(opt, idx)">
                            <span x-text="getOptionLetter(idx)"></span>
                        </span>

                        {{-- Option Content Text --}}
                        <span class="flex-1 text-sm sm:text-base font-semibold leading-snug"
                            x-text="opt"></span>

                        {{-- Revealing Result Icon --}}
                        <div class="shrink-0">
                            {{-- Correct Answer Checkmark --}}
                            <template x-if="room.round_phase === 'revealing' && opt === currentQuestion?.correct_answer">
                                <span class="grid h-7 w-7 place-items-center rounded-full bg-emerald-500 text-white shadow-sm">
                                    <i data-lucide="check" class="h-4 w-4"></i>
                                </span>
                            </template>

                            {{-- Selected Wrong X --}}
                            <template x-if="room.round_phase === 'revealing' && selectedOption === opt && opt !== currentQuestion?.correct_answer">
                                <span class="grid h-7 w-7 place-items-center rounded-full bg-rose-500 text-white shadow-sm">
                                    <i data-lucide="x" class="h-4 w-4"></i>
                                </span>
                            </template>
                        </div>
                    </button>
                </template>
            </div>

            {{-- Submission Lock Status Bar --}}
            <div x-show="selectedOption !== null && room.round_phase === 'answering'" x-cloak
                class="rounded-xl bg-amber-50 border border-amber-200/80 px-4 py-2.5 flex items-center justify-between text-xs font-sans text-amber-900">
                <span class="flex items-center gap-2">
                    <i data-lucide="lock" class="h-3.5 w-3.5 text-amber-700"></i>
                    <span>Đã ghi nhận lựa chọn. Đang đợi đối thủ hoặc hết giờ để chốt kết quả...</span>
                </span>
                <span class="font-bold text-amber-800" x-text="'Lựa chọn: ' + selectedOption"></span>
            </div>

            {{-- Explanation Box (Revealing Phase) --}}
            <div x-show="room.round_phase === 'revealing' && currentQuestion?.explanation" x-cloak
                class="rounded-2xl bg-slate-50 border border-slate-200 p-4 sm:p-5 font-sans text-xs sm:text-sm text-slate-700 space-y-1">
                <p class="font-bold text-slate-900 flex items-center gap-1.5 text-amber-900">
                    <i data-lucide="lightbulb" class="h-4 w-4 text-amber-600"></i>
                    <span>Giải thích đáp án:</span>
                </p>
                <p class="leading-relaxed" x-text="currentQuestion?.explanation"></p>
            </div>

        </section>

        {{-- E. MATCH FINISHED / ABANDONED BANNER & REMATCH BUTTON --}}
        <section x-show="room.status === 'finished' || room.status === 'abandoned'" x-cloak
            class="rounded-3xl border-2 border-amber-300/80 bg-gradient-to-b from-[#faf5ee] to-[#f4ebe1] p-6 sm:p-10 shadow-xl text-center space-y-6">

            <div class="space-y-2">
                {{-- Result Icon & Title --}}
                <div class="text-5xl mb-2" x-text="getMatchResultEmoji()"></div>
                <h3 class="text-2xl sm:text-4xl font-black font-serif text-slate-900" x-text="getMatchResultTitle()"></h3>
                <p class="text-xs sm:text-sm text-slate-600 font-sans max-w-md mx-auto" x-text="getMatchResultSubtitle()"></p>
            </div>

            {{-- Final Scores Comparison --}}
            <div class="max-w-md mx-auto grid grid-cols-2 gap-4 rounded-2xl bg-white/80 border border-amber-200 p-4 font-sans">
                <div class="border-r border-slate-200 pr-2">
                    <p class="text-xs text-slate-500 font-semibold" x-text="room.player1?.name"></p>
                    <p class="text-2xl font-black text-red-800 mt-1" x-text="room.player1?.score + ' Đ'"></p>
                    <p class="text-[11px] text-slate-400" x-text="room.player1?.correct_count + '/10 câu đúng'"></p>
                </div>
                <div class="pl-2">
                    <p class="text-xs text-slate-500 font-semibold" x-text="room.player2?.name"></p>
                    <p class="text-2xl font-black text-indigo-800 mt-1" x-text="room.player2?.score + ' Đ'"></p>
                    <p class="text-[11px] text-slate-400" x-text="room.player2?.correct_count + '/10 câu đúng'"></p>
                </div>
            </div>

            {{-- Rematch & Result Actions --}}
            <div class="flex flex-wrap items-center justify-center gap-3 pt-2 font-sans">
                <a :href="'/duel/room/' + roomCode + '/result'"
                    class="py-3 px-5 rounded-xl bg-amber-600 hover:bg-amber-700 active:scale-[0.98] text-white font-bold text-sm shadow-md shadow-amber-950/20 transition flex items-center gap-2">
                    <i data-lucide="bar-chart-2" class="h-4 w-4"></i>
                    <span>Xem Kết Quả Chi Tiết</span>
                </a>

                <a :href="'/duel/room/' + roomCode + '/review'"
                    class="py-3 px-5 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:scale-[0.98] text-white font-bold text-sm shadow-md shadow-emerald-950/20 transition flex items-center gap-2">
                    <i data-lucide="book-open" class="h-4 w-4"></i>
                    <span>Ôn Tập Câu Sai</span>
                </a>

                <button type="button"
                    @click="requestRematch()"
                    :disabled="isRematching"
                    class="py-3 px-5 rounded-xl bg-[#991b1b] hover:bg-red-800 active:scale-[0.98] text-white font-bold text-sm shadow-md shadow-red-950/20 transition flex items-center gap-2 cursor-pointer disabled:opacity-50">
                    <i data-lucide="rotate-ccw" class="h-4 w-4" x-show="!isRematching"></i>
                    <i data-lucide="loader-2" class="h-4 w-4 animate-spin" x-show="isRematching" x-cloak></i>
                    <span x-text="rematchButtonLabel"></span>
                </button>

                <a href="{{ route('duel.lobby') }}"
                    class="py-3 px-5 rounded-xl bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 font-bold text-sm shadow-xs transition flex items-center gap-2">
                    <i data-lucide="home" class="h-4 w-4"></i>
                    <span>Về sảnh chính</span>
                </a>
            </div>
        </section>

    </div>

    {{-- MODAL XÁC NHẬN ĐẦU HÀNG --}}
    <div x-show="confirmSurrender" x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/70 backdrop-blur-xs p-4">
        <div @click.away="confirmSurrender = false" class="max-w-md w-full bg-white rounded-3xl border border-rose-200 p-6 shadow-2xl space-y-4 font-sans text-center">
            <div class="grid h-12 w-12 place-items-center rounded-2xl bg-rose-100 text-rose-700 mx-auto">
                <i data-lucide="flag" class="h-6 w-6"></i>
            </div>
            <h3 class="text-lg font-black text-slate-900 font-serif">Xác Nhận Nhận Thua?</h3>
            <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                Bạn có chắc chắn muốn đầu hàng trận đấu này? Điểm số sẽ được bảo lưu, bạn nhận 0 XP và đối thủ sẽ giành chiến thắng ngay lập tức.
            </p>
            <div class="flex items-center justify-center gap-3 pt-2">
                <button type="button" @click="confirmSurrender = false" class="flex-1 py-2.5 px-4 rounded-xl border border-slate-200 text-slate-700 font-bold text-xs hover:bg-slate-50 transition cursor-pointer">
                    Tiếp tục thi đấu
                </button>
                <button type="button" @click="executeSurrender()" :disabled="isSurrendering" class="flex-1 py-2.5 px-4 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-md shadow-rose-900/20 transition disabled:opacity-50 cursor-pointer">
                    <span x-show="!isSurrendering">Xác nhận đầu hàng</span>
                    <span x-show="isSurrendering" x-cloak>Đang xử lý...</span>
                </button>
            </div>
        </div>
    </div>

</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('duelArenaApp', (roomCode, mySlot, initialState) => ({
        roomCode: roomCode,
        slot: mySlot,
        room: initialState || {},
        currentQuestion: initialState?.current_question || null,

        // User Input & Locks
        selectedOption: null,
        isSubmittingAnswer: false,
        answerResult: null,

        // Interpolated Visual Timer
        localTimeRemaining: initialState?.time_remaining || 0,
        targetEndTimestamp: Date.now() + ((initialState?.time_remaining || 0) * 1000),

        // Network & Polling
        connectionState: 'connected', // 'connected' | 'syncing' | 'error'
        isPolling: false,
        pollIntervalId: null,
        tickIntervalId: null,
        copySuccess: '',

        // Surrender & Cancel
        confirmSurrender: false,
        isSurrendering: false,

        // Rematch
        isRematching: false,
        rematchButtonLabel: 'Tái Đấu Ngay 🔄',

        initArena() {
            // Khởi tạo icons Lucide
            if (window.refreshIcons) window.refreshIcons();

            // Khởi động đồng hồ nội bộ 100ms để đếm ngược mượt mà
            this.startLocalClock();

            // Bắt đầu Smart Polling
            this.startSmartPolling();

            // Lắng nghe sự kiện rời trang để dừng polling
            window.addEventListener('beforeunload', () => this.stopAllTimers());
            document.addEventListener('visibilitychange', () => {
                if (document.hidden) {
                    // Khi ẩn tab, vẫn giữ polling cơ bản
                } else {
                    // Khi quay lại tab, kích hoạt fetch đồng bộ ngay lập tức
                    this.fetchRoomState();
                }
            });
        },

        startLocalClock() {
            if (this.tickIntervalId) clearInterval(this.tickIntervalId);

            this.tickIntervalId = setInterval(() => {
                if (this.room.status === 'in_progress' || this.room.status === 'countdown') {
                    const remaining = Math.max(0, (this.targetEndTimestamp - Date.now()) / 1000);
                    this.localTimeRemaining = remaining;

                    // Nếu đồng hồ địa phương đếm hết trong lúc answering mà chưa chuyển phase, kéo state mới
                    if (remaining <= 0 && this.room.round_phase === 'answering') {
                        this.fetchRoomState();
                    }
                }
            }, 100);
        },

        startSmartPolling() {
            if (this.pollIntervalId) clearInterval(this.pollIntervalId);

            const scheduleNext = () => {
                if (this.room.status === 'finished' || this.room.status === 'abandoned') {
                    // Nếu đã kết thúc, kiểm tra xem có phòng tái đấu mới chưa
                    if (this.room.rematch_room_id && this.room.rematch_requested_by) {
                        // Tiếp tục poll nhẹ để cùng vào phòng tái đấu
                        this.pollIntervalId = setTimeout(async () => {
                            await this.fetchRoomState();
                            scheduleNext();
                        }, 2000);
                    }
                    return;
                }

                let delay = 1000;
                if (this.room.status === 'waiting') {
                    delay = 2000;
                } else if (this.room.status === 'countdown') {
                    delay = 1000;
                } else if (this.room.round_phase === 'revealing') {
                    delay = 800; // Poll nhanh hơn để đón câu tiếp theo ngay sau 2s
                } else if (this.room.my_submitted && !this.room.opponent_submitted) {
                    delay = 800; // Đã nộp xong, poll nhanh chờ đối thủ
                }

                this.pollIntervalId = setTimeout(async () => {
                    await this.fetchRoomState();
                    scheduleNext();
                }, delay);
            };

            scheduleNext();
        },

        stopAllTimers() {
            if (this.pollIntervalId) clearTimeout(this.pollIntervalId);
            if (this.tickIntervalId) clearInterval(this.tickIntervalId);
        },

        async fetchRoomState() {
            if (this.isPolling) return;
            this.isPolling = true;

            try {
                const res = await fetch(`/duel/room/${this.roomCode}/state`, {
                    headers: { 'Accept': 'application/json' }
                });

                if (!res.ok) {
                    if (res.status === 403 || res.status === 404) {
                        this.connectionState = 'error';
                        return;
                    }
                    throw new Error('Sync failed');
                }

                const newState = await res.json();
                this.connectionState = 'connected';
                this.applyStateUpdate(newState);
            } catch (e) {
                this.connectionState = 'syncing';
            } finally {
                this.isPolling = false;
            }
        },

        applyStateUpdate(newState) {
            const oldRound = this.room.current_round;
            const oldPhase = this.room.round_phase;

            this.room = newState;
            this.currentQuestion = newState.current_question;

            // Đồng bộ mốc thời gian server
            this.localTimeRemaining = newState.time_remaining;
            this.targetEndTimestamp = Date.now() + (newState.time_remaining * 1000);

            // Chuyển sang vòng mới hoặc reset khi bắt đầu phase answering mới
            if (newState.current_round !== oldRound || (oldPhase === 'revealing' && newState.round_phase === 'answering')) {
                this.selectedOption = null;
                this.answerResult = null;
                this.isSubmittingAnswer = false;
            }

            // Nếu phòng đã có rematch_room_id và cả hai đều đã tái đấu, tự động điều hướng
            if (newState.rematch_room_id && this.isRematching) {
                // Kiểm tra xem có mã phòng con trong API không
                this.requestRematch();
            }

            // Làm mới Lucide icons
            this.$nextTick(() => {
                if (window.refreshIcons) window.refreshIcons();
            });
        },

        async submitOption(option) {
            if (this.isOptionDisabled()) return;

            this.selectedOption = option;
            this.isSubmittingAnswer = true;

            const idempotencyKey = `${this.roomCode}_r${this.room.current_round}_s${this.slot}_${Date.now()}_${Math.random().toString(36).substring(2, 7)}`;

            try {
                const res = await fetch(`/duel/room/${this.roomCode}/answer`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: JSON.stringify({
                        answer: option,
                        idempotency_key: idempotencyKey,
                    })
                });

                const data = await res.json();

                if (res.ok && data.status === 'success') {
                    this.answerResult = data.data;
                    this.room.my_submitted = true;
                    // Kéo state ngay lập tức đề phòng cả 2 người đã nộp xong
                    this.fetchRoomState();
                } else if (data.status === 'already_submitted') {
                    this.room.my_submitted = true;
                    this.answerResult = data.data;
                } else if (data.status === 'timeout') {
                    this.room.my_submitted = true;
                    this.fetchRoomState();
                }
            } catch (err) {
                console.error('Answer submission error:', err);
            } finally {
                this.isSubmittingAnswer = false;
            }
        },

        handleKeyPress(e) {
            if (this.isOptionDisabled() || !this.currentQuestion?.options) return;

            const key = e.key.toUpperCase();
            const keyMap = {
                '1': 0, 'A': 0,
                '2': 1, 'B': 1,
                '3': 2, 'C': 2,
                '4': 3, 'D': 3
            };

            if (key in keyMap) {
                const idx = keyMap[key];
                const opt = this.currentQuestion.options[idx];
                if (opt) this.submitOption(opt);
            }
        },

        isOptionDisabled() {
            return this.room.status !== 'in_progress' ||
                   this.room.round_phase !== 'answering' ||
                   this.room.my_submitted ||
                   this.selectedOption !== null ||
                   this.isSubmittingAnswer;
        },

        get tugPercent() {
            const s1 = this.room.player1?.score || 0;
            const s2 = this.room.player2?.score || 0;
            if (s1 === 0 && s2 === 0) return 50;

            const diff = s1 - s2;
            // Tỷ lệ phân bổ điểm: Chênh lệch 1000 điểm tương ứng đạt gần mép (92%)
            let p = 50 + (diff / 1000) * 50;
            return Math.min(92, Math.max(8, p));
        },

        getScoreDiffLabel(playerNum) {
            const s1 = this.room.player1?.score || 0;
            const s2 = this.room.player2?.score || 0;
            if (s1 === s2) return 'Hòa';

            if (playerNum === 1) {
                return s1 > s2 ? `+${s1 - s2} đ` : '';
            } else {
                return s2 > s1 ? `+${s2 - s1} đ` : '';
            }
        },

        getOptionClass(opt, idx) {
            // Phase revealing: Hiển thị kết quả đúng/sai
            if (this.room.round_phase === 'revealing') {
                if (opt === this.currentQuestion?.correct_answer) {
                    return 'bg-emerald-50 border-emerald-500 text-emerald-950 ring-2 ring-emerald-300 font-bold';
                }
                if (this.selectedOption === opt && opt !== this.currentQuestion?.correct_answer) {
                    return 'bg-rose-50 border-rose-500 text-rose-950 ring-2 ring-rose-300 font-bold';
                }
                return 'bg-slate-50/50 border-slate-200 text-slate-400 opacity-50';
            }

            // Phase answering: Hiển thị trạng thái đã chọn
            if (this.selectedOption === opt) {
                return 'bg-amber-100/80 border-amber-500 text-amber-950 ring-2 ring-amber-300 font-bold shadow-md';
            }

            if (this.room.my_submitted) {
                return 'bg-slate-50 border-slate-200 text-slate-400 opacity-60';
            }

            return 'bg-white hover:bg-red-50/50 border-slate-200 hover:border-red-300 text-slate-800 shadow-xs hover:shadow-md';
        },

        getOptionBadgeClass(opt, idx) {
            if (this.room.round_phase === 'revealing') {
                if (opt === this.currentQuestion?.correct_answer) {
                    return 'bg-emerald-600 text-white';
                }
                if (this.selectedOption === opt && opt !== this.currentQuestion?.correct_answer) {
                    return 'bg-rose-600 text-white';
                }
                return 'bg-slate-100 text-slate-400';
            }

            if (this.selectedOption === opt) {
                return 'bg-amber-600 text-white';
            }

            return 'bg-slate-100 text-slate-600 group-hover:bg-red-100 group-hover:text-red-900';
        },

        getOptionLetter(idx) {
            return ['A', 'B', 'C', 'D'][idx] || '';
        },

        getInitial(name) {
            if (!name) return 'U';
            return name.trim().charAt(0).toUpperCase();
        },

        getConnectionLabel() {
            if (this.connectionState === 'connected') return 'Ổn định';
            if (this.connectionState === 'syncing') return 'Đang đồng bộ';
            return 'Lỗi kết nối';
        },

        getInviteUrl() {
            return `${window.location.origin}/duel/room/${this.roomCode}`;
        },

        copyToClipboard(text, type) {
            navigator.clipboard.writeText(text).then(() => {
                this.copySuccess = type;
                setTimeout(() => { this.copySuccess = ''; }, 2500);
            });
        },

        getMatchResultTitle() {
            if (this.room.status === 'abandoned') {
                return 'Đối Thủ Bỏ Cuộc!';
            }
            if (this.room.forfeit_by_slot) {
                return this.room.forfeit_by_slot === this.slot ? 'Bạn Đã Bỏ Cuộc' : 'Đối Thủ Bị Xử Thua (Forfeit)';
            }
            if (this.room.is_draw) {
                return 'Bất Phân Thắng Bại!';
            }
            return this.room.winner_slot === this.slot ? 'Chiến Thắng Tuyệt Đối!' : 'Tiếc Nuối... Thất Bại';
        },

        getMatchResultEmoji() {
            if (this.room.is_draw) return '🤝';
            if (this.room.winner_slot === this.slot) return '🏆';
            return '⚔️';
        },

        getMatchResultSubtitle() {
            if (this.room.is_draw) {
                return 'Cả hai người chơi có cùng điểm số và thành tích ngang ngửa.';
            }
            if (this.room.winner_slot === this.slot) {
                return 'Xuất sắc! Bạn đã thể hiện phản xạ và kiến thức tiếng Trung vượt trội.';
            }
            return 'Đừng nản lòng! Hãy bấm nút Tái Đấu để lật ngược tình thế ngay.';
        },

        async requestRematch() {
            this.isRematching = true;
            this.rematchButtonLabel = 'Đang ghép phòng...';

            try {
                const res = await fetch(`/duel/room/${this.roomCode}/rematch`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    }
                });

                const data = await res.json();

                if (data.status === 'ready' && data.room_code) {
                    // Cả hai đã sẵn sàng -> Điều hướng sang phòng tái đấu mới
                    window.location.href = `/duel/room/${data.room_code}`;
                } else if (data.status === 'waiting_opponent') {
                    this.rematchButtonLabel = 'Đang chờ đối thủ đồng ý...';
                }
            } catch (err) {
                this.rematchButtonLabel = 'Tái Đấu Ngay 🔄';
                this.isRematching = false;
            }
        },

        async cancelWaitingRoom() {
            try {
                await fetch('/duel/matchmake/cancel', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: JSON.stringify({ room_code: this.roomCode })
                });
            } catch (e) {}
            window.location.href = '{{ route('duel.lobby') }}';
        },

        async executeSurrender() {
            this.isSurrendering = true;
            try {
                const res = await fetch(`/duel/room/${this.roomCode}/surrender`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    }
                });
                const data = await res.json();
                this.confirmSurrender = false;
                await this.fetchRoomState();
            } catch (err) {
                console.error('Surrender error:', err);
            } finally {
                this.isSurrendering = false;
            }
        }
    }));
});
</script>
@endsection
