@extends('layouts.app')

@section('title', 'Chinese Duel · Đấu Trường HSK 1v1 Đối Kháng | Learn Chinese')

@section('content')
<div x-data="duelLobbyApp({{ Js::from($waitingCounts) }}, {{ Auth::guard('web')->check() ? 'true' : 'false' }})" class="space-y-8 pb-16 font-serif-custom max-w-5xl mx-auto">

    {{-- Top Ambience Banner --}}
    <section class="relative overflow-hidden rounded-[2.25rem] border border-red-900/20 bg-gradient-to-br from-[#faf3eb] via-[#f7ebe1] to-[#f2dfd5] p-6 sm:p-8 shadow-xl shadow-red-950/5">
        {{-- Chinese Watermark --}}
        <div class="pointer-events-none absolute -right-4 -bottom-6 select-none text-[8rem] sm:text-[10rem] font-serif font-black leading-none text-red-900/[0.04]">
            争锋
        </div>

        <div class="relative z-10 flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
            <div class="max-w-2xl">
                <div class="flex flex-wrap items-center gap-2 mb-3">
                    <span class="seal-zhusha-round text-xs font-bold tracking-widest">
                        对 决 · 争 锋
                    </span>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-red-100/80 border border-red-300/60 px-3 py-1 text-xs font-semibold text-red-900">
                        <i data-lucide="swords" class="h-3.5 w-3.5 text-red-800"></i>
                        <span>1v1 Real-time HSK Battle</span>
                    </span>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-white/80 border border-red-900/10 px-3 py-1 text-xs font-medium text-slate-700 shadow-2xs">
                        <i data-lucide="zap" class="h-3.5 w-3.5 text-amber-600"></i>
                        <span>Tug-of-War Kéo Co Điểm Số</span>
                    </span>
                </div>

                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight text-slate-900 font-serif">
                    Đấu Trường Chinese Duel
                </h1>
                <p class="mt-2.5 text-sm sm:text-base leading-relaxed text-slate-700">
                    <span class="italic text-red-950 font-medium">"Trí tuệ giao phong · Bút tích tương tranh"</span> — Thử thách phản xạ tiếng Trung trong 10 câu hỏi tốc độ cao. Cùng một câu hỏi, cùng một thời điểm, ai nhanh và chính xác hơn sẽ kéo thanh chiến thắng về phía mình!
                </p>

                <div class="mt-4 flex items-center gap-3">
                    @auth
                        <a href="{{ route('duel.history') }}" class="inline-flex items-center gap-2 py-2 px-4 rounded-xl bg-white/90 hover:bg-white text-slate-800 hover:text-red-900 border border-red-200/80 font-bold text-xs shadow-xs font-sans transition">
                            <i data-lucide="history" class="h-4 w-4 text-red-700"></i>
                            <span>Xem Lịch Sử Thi Đấu Cá Nhân</span>
                        </a>
                    @else
                        <button type="button" @click="showAuthModal = true" class="inline-flex items-center gap-2 py-2 px-4 rounded-xl bg-white/90 hover:bg-white text-slate-800 hover:text-red-900 border border-red-200/80 font-bold text-xs shadow-xs font-sans transition cursor-pointer">
                            <i data-lucide="log-in" class="h-4 w-4 text-red-700"></i>
                            <span>Đăng nhập để xem Lịch Sử & Thống kê</span>
                        </button>
                    @endauth
                </div>
            </div>

            {{-- Feature Badges --}}
            <div class="grid grid-cols-2 gap-3 shrink-0 lg:w-72">
                <div class="rounded-2xl bg-white/80 border border-red-100 p-3.5 shadow-sm text-center">
                    <p class="text-xs text-slate-500 font-sans">Thời gian mỗi câu</p>
                    <p class="text-xl font-black text-red-800 font-sans mt-0.5">10 Giây</p>
                </div>
                <div class="rounded-2xl bg-white/80 border border-red-100 p-3.5 shadow-sm text-center">
                    <p class="text-xs text-slate-500 font-sans">Điểm tối đa</p>
                    <p class="text-xl font-black text-amber-700 font-sans mt-0.5">200 Đ/câu</p>
                </div>
                <div class="rounded-2xl bg-white/80 border border-red-100 p-3.5 shadow-sm text-center">
                    <p class="text-xs text-slate-500 font-sans">Thưởng thắng trận</p>
                    <p class="text-xl font-black text-emerald-700 font-sans mt-0.5">+40 XP</p>
                </div>
                <div class="rounded-2xl bg-white/80 border border-red-100 p-3.5 shadow-sm text-center">
                    <p class="text-xs text-slate-500 font-sans">Số câu mỗi trận</p>
                    <p class="text-xl font-black text-slate-800 font-sans mt-0.5">10 Hiệp</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Error / Notification Alert --}}
    <div x-show="errorMessage" x-cloak class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm flex items-center justify-between">
        <div class="flex items-center gap-2">
            <i data-lucide="alert-circle" class="h-5 w-5 shrink-0 text-rose-600"></i>
            <span x-text="errorMessage"></span>
        </div>
        <button @click="errorMessage = ''" class="text-rose-500 hover:text-rose-700">
            <i data-lucide="x" class="h-4 w-4"></i>
        </button>
    </div>

    {{-- Banner Active Room của User (nếu đang có trận chưa hoàn tất) --}}
    @if($myActiveRoom)
        <div class="rounded-3xl border-2 {{ $myActiveRoom->status === 'waiting' ? 'border-amber-300 bg-amber-50/80 text-amber-900' : 'border-red-300 bg-red-50/90 text-red-950 animate-pulse' }} p-5 sm:p-6 shadow-md font-sans">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-start sm:items-center gap-3">
                    <div class="grid h-10 w-10 shrink-0 place-items-center rounded-2xl {{ $myActiveRoom->status === 'waiting' ? 'bg-amber-200 text-amber-800' : 'bg-red-200 text-red-800' }}">
                        <i data-lucide="{{ $myActiveRoom->status === 'waiting' ? 'clock' : 'swords' }}" class="h-5 w-5"></i>
                    </div>
                    <div>
                        <p class="font-bold text-sm sm:text-base">
                            @if($myActiveRoom->status === 'waiting')
                                Bạn đang có phòng chờ tìm kiếm HSK {{ $myActiveRoom->hsk_level }} (Mã: {{ $myActiveRoom->room_code }})
                            @else
                                Bạn đang có một trận đấu HSK {{ $myActiveRoom->hsk_level }} đang diễn ra!
                            @endif
                        </p>
                        <p class="text-xs text-slate-600 mt-0.5">
                            @if($myActiveRoom->status === 'waiting')
                                Trận đấu chưa bắt đầu. Bạn có thể vào phòng chờ hoặc hủy tìm trận.
                            @else
                                Hãy quay lại sàn đấu ngay để tiếp tục thi đấu và tránh bị xử thua Forfeit.
                            @endif
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    <a href="{{ route('duel.arena', $myActiveRoom->room_code) }}"
                        class="py-2.5 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-xs transition flex items-center gap-1.5">
                        <i data-lucide="arrow-right" class="h-4 w-4"></i>
                        <span>{{ $myActiveRoom->status === 'waiting' ? 'Vào Phòng Chờ' : 'Quay Lại Sàn Đấu' }}</span>
                    </a>
                    @if($myActiveRoom->status === 'waiting')
                        <button type="button"
                            @click="cancelWaiting('{{ $myActiveRoom->room_code }}')"
                            :disabled="isLoading"
                            class="py-2.5 px-3 rounded-xl bg-white hover:bg-rose-50 text-rose-700 border border-rose-200 font-bold text-xs shadow-xs transition flex items-center gap-1 cursor-pointer disabled:opacity-50">
                            <i data-lucide="x" class="h-3.5 w-3.5"></i>
                            <span>Hủy tìm trận</span>
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- Step 1: Select HSK Level with Live Counters --}}
    <section class="bg-white/90 backdrop-blur rounded-3xl border border-slate-200/80 p-6 sm:p-8 shadow-sm">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h2 class="text-xl sm:text-2xl font-bold text-slate-900 font-serif">1. Chọn Cấp Độ Thi Đấu</h2>
                <p class="text-xs sm:text-sm text-slate-500 font-sans mt-1">Câu hỏi sẽ được rút ngẫu nhiên từ ngân hàng đề thi chuẩn của cấp độ này.</p>
            </div>
            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600 font-sans">
                Đang chọn: HSK <span x-text="selectedLevel"></span>
            </span>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
            <template x-for="lvl in [1, 2, 3, 4, 5, 6]" :key="lvl">
                <button type="button"
                    @click="selectedLevel = lvl"
                    :class="selectedLevel === lvl 
                        ? 'border-[#991b1b] bg-red-50/70 text-[#991b1b] ring-2 ring-red-200 shadow-md' 
                        : 'border-slate-200 hover:border-slate-300 bg-white text-slate-700 hover:bg-slate-50'"
                    class="flex flex-col items-center justify-center py-4 px-3 rounded-2xl border-2 transition text-center group cursor-pointer">
                    <span class="text-xs font-semibold text-slate-400 group-hover:text-slate-500 uppercase tracking-wider font-sans">Cấp độ</span>
                    <span class="text-2xl font-black font-sans my-0.5" x-text="'HSK ' + lvl"></span>
                    <span class="text-[11px] font-medium" :class="selectedLevel === lvl ? 'text-red-700' : 'text-slate-500'" x-text="getLevelTitle(lvl)"></span>

                    {{-- Live Counter Badge --}}
                    <div class="mt-2 flex items-center justify-center font-sans">
                        <template x-if="waitingCounts[lvl] && waitingCounts[lvl] > 0">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-bold text-[10px]">
                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                <span x-text="waitingCounts[lvl] + ' đang chờ'"></span>
                            </span>
                        </template>
                        <template x-if="!waitingCounts[lvl] || waitingCounts[lvl] === 0">
                            <span class="text-slate-400 text-[10px]">0 đang chờ</span>
                        </template>
                    </div>
                </button>
            </template>
        </div>
    </section>

    {{-- Step 2: Choose Mode (Public Matchmake vs Private Lobby vs Join Room) --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        {{-- Card 1: Public Matchmaking --}}
        <div class="rounded-3xl bg-gradient-to-b from-white to-red-50/40 border-2 border-red-200/80 p-6 sm:p-7 shadow-md flex flex-col justify-between hover:shadow-lg transition">
            <div>
                <div class="grid h-12 w-12 place-items-center rounded-2xl bg-red-100 text-red-800 mb-4">
                    <i data-lucide="zap" class="h-6 w-6"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-900 font-serif">Ghép Trận Nhanh</h3>
                <p class="text-xs sm:text-sm text-slate-600 font-sans mt-2 leading-relaxed">
                    Hệ thống sẽ tự động tìm kiếm đối thủ đang chờ cùng cấp độ HSK <span x-text="selectedLevel" class="font-bold"></span> hoặc xếp bạn vào phòng chờ công khai.
                </p>
            </div>

            <div class="mt-6 pt-4 border-t border-red-100">
                <button type="button"
                    @click="handleAction('matchmake')"
                    :disabled="isLoading"
                    class="w-full py-3.5 px-4 rounded-xl bg-[#991b1b] hover:bg-red-800 active:scale-[0.98] text-white font-bold text-sm shadow-md shadow-red-950/20 transition flex items-center justify-center gap-2 cursor-pointer disabled:opacity-50">
                    <i data-lucide="swords" class="h-4 w-4" x-show="!isLoading"></i>
                    <i data-lucide="loader-2" class="h-4 w-4 animate-spin" x-show="isLoading && loadingType === 'matchmake'" x-cloak></i>
                    <span>Tìm Đối Thủ Ngay</span>
                </button>
            </div>
        </div>

        {{-- Card 2: Create Private Room --}}
        <div class="rounded-3xl bg-gradient-to-b from-white to-amber-50/40 border-2 border-amber-200/80 p-6 sm:p-7 shadow-md flex flex-col justify-between hover:shadow-lg transition">
            <div>
                <div class="grid h-12 w-12 place-items-center rounded-2xl bg-amber-100 text-amber-800 mb-4">
                    <i data-lucide="users" class="h-6 w-6"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-900 font-serif">Tạo Phòng Thách Đấu</h3>
                <p class="text-xs sm:text-sm text-slate-600 font-sans mt-2 leading-relaxed">
                    Tạo phòng riêng và nhận mã mời (6 ký tự) hoặc link trực tiếp để gửi cho bạn bè so tài kiến thức tiếng Trung 1v1.
                </p>
            </div>

            <div class="mt-6 pt-4 border-t border-amber-100">
                <button type="button"
                    @click="handleAction('create')"
                    :disabled="isLoading"
                    class="w-full py-3.5 px-4 rounded-xl bg-amber-600 hover:bg-amber-700 active:scale-[0.98] text-white font-bold text-sm shadow-md shadow-amber-950/20 transition flex items-center justify-center gap-2 cursor-pointer disabled:opacity-50">
                    <i data-lucide="plus-circle" class="h-4 w-4" x-show="!isLoading"></i>
                    <i data-lucide="loader-2" class="h-4 w-4 animate-spin" x-show="isLoading && loadingType === 'create'" x-cloak></i>
                    <span>Tạo Phòng Riêng</span>
                </button>
            </div>
        </div>

        {{-- Card 3: Join with Invite Code --}}
        <div class="rounded-3xl bg-gradient-to-b from-white to-slate-50 border-2 border-slate-200/80 p-6 sm:p-7 shadow-md flex flex-col justify-between hover:shadow-lg transition">
            <div>
                <div class="grid h-12 w-12 place-items-center rounded-2xl bg-slate-100 text-slate-800 mb-4">
                    <i data-lucide="key-round" class="h-6 w-6"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-900 font-serif">Nhập Mã Phòng</h3>
                <p class="text-xs sm:text-sm text-slate-600 font-sans mt-2 leading-relaxed">
                    Bạn được bạn bè gửi mã mời? Hãy nhập 6 ký tự để tham gia sàn đấu ngay lập tức.
                </p>

                <div class="mt-4">
                    <input type="text"
                        x-model="joinCode"
                        @keyup.enter="handleAction('join')"
                        maxlength="6"
                        placeholder="VD: ABC123"
                        class="w-full text-center tracking-[0.25em] uppercase font-mono font-bold text-lg rounded-xl border border-slate-300 py-2.5 px-3 focus:border-[#991b1b] focus:ring-2 focus:ring-red-100 focus:outline-none transition">
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-slate-100">
                <button type="button"
                    @click="handleAction('join')"
                    :disabled="isLoading || !joinCode || joinCode.length < 4"
                    class="w-full py-3.5 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 active:scale-[0.98] text-white font-bold text-sm shadow-md shadow-slate-950/20 transition flex items-center justify-center gap-2 cursor-pointer disabled:opacity-40">
                    <i data-lucide="log-in" class="h-4 w-4" x-show="!isLoading"></i>
                    <i data-lucide="loader-2" class="h-4 w-4 animate-spin" x-show="isLoading && loadingType === 'join'" x-cloak></i>
                    <span>Vào Phòng Thi Đấu</span>
                </button>
            </div>
        </div>

    </div>

    {{-- Room Browser: Danh Sách Phòng Chờ Công Khai --}}
    <section class="bg-white/90 backdrop-blur rounded-3xl border border-slate-200/80 p-6 sm:p-8 shadow-sm">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h3 class="text-lg sm:text-xl font-bold text-slate-900 font-serif flex items-center gap-2">
                    <i data-lucide="compass" class="h-5 w-5 text-red-800"></i>
                    <span>Phòng Chờ Đang Tìm Đối Thủ (Room Browser)</span>
                </h3>
                <p class="text-xs sm:text-sm text-slate-500 font-sans mt-0.5">Chọn ngay một phòng chờ công khai để bắt đầu so tài mà không cần chờ đợi.</p>
            </div>
            <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-red-50 text-red-800 border border-red-200 font-sans">
                {{ $publicWaitingRooms->count() }} phòng đang chờ
            </span>
        </div>

        @if($publicWaitingRooms->isEmpty())
            <div class="py-10 text-center text-slate-400 font-sans text-xs sm:text-sm bg-slate-50/70 rounded-2xl border border-dashed border-slate-200">
                <i data-lucide="inbox" class="h-8 w-8 mx-auto text-slate-300 mb-2"></i>
                <p class="font-medium text-slate-600">Hiện chưa có phòng chờ công khai nào.</p>
                <p class="text-slate-400 text-xs mt-1">Hãy bấm <strong>"Tìm Đối Thủ Ngay"</strong> ở trên để tạo phòng chờ đầu tiên!</p>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5">
                @foreach($publicWaitingRooms as $pRoom)
                    <div class="p-4 rounded-2xl border border-slate-200 bg-white hover:border-red-300 hover:shadow-md transition flex items-center justify-between font-sans">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="h-10 w-10 rounded-full bg-red-100 ring-2 ring-red-200 overflow-hidden shrink-0 flex items-center justify-center font-bold text-red-800 text-sm">
                                @if($pRoom->player1_avatar)
                                    <img src="{{ $pRoom->player1_avatar }}" alt="{{ $pRoom->player1_name }}" class="h-full w-full object-cover">
                                @else
                                    {{ mb_substr($pRoom->player1_name ?? 'U', 0, 1) }}
                                @endif
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs font-bold text-slate-800 truncate">{{ $pRoom->player1_name }}</p>
                                <div class="flex items-center gap-1.5 mt-0.5">
                                    <span class="px-1.5 py-0.5 rounded bg-red-100 text-red-800 font-extrabold text-[10px]">HSK {{ $pRoom->hsk_level }}</span>
                                    <span class="text-[10px] text-slate-400">{{ $pRoom->created_at?->diffForHumans() ?? 'vừa xong' }}</span>
                                </div>
                            </div>
                        </div>
                        <button type="button"
                            @click="joinDirect('{{ $pRoom->room_code }}')"
                            class="py-1.5 px-3 rounded-xl bg-[#991b1b] hover:bg-red-800 active:scale-95 text-white font-bold text-xs shadow-xs transition cursor-pointer shrink-0 ml-2">
                            Vào Đấu
                        </button>
                    </div>
                @endforeach
            </div>
        @endif
    </section>

    {{-- Rules & Tips --}}
    <section class="rounded-3xl border border-slate-200 bg-white/80 p-6 sm:p-8">
        <h3 class="text-lg font-bold text-slate-900 font-serif mb-4 flex items-center gap-2">
            <i data-lucide="scroll" class="h-5 w-5 text-red-800"></i>
            <span>Luật Đấu & Cơ Chế Điểm Số</span>
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 text-xs sm:text-sm text-slate-600 font-sans">
            <div class="rounded-2xl bg-slate-50 p-4 border border-slate-100">
                <p class="font-bold text-slate-900 mb-1">⏱️ Tốc độ & Điểm số</p>
                <p>10 giây cho mỗi câu. Trả lời đúng trong giây đầu nhận trọn 200 điểm. Càng chậm điểm càng giảm dần đến mức sàn 75 điểm.</p>
            </div>
            <div class="rounded-2xl bg-slate-50 p-4 border border-slate-100">
                <p class="font-bold text-slate-900 mb-1">🎯 Tug-of-War Kéo Co</p>
                <p>Thanh đối đầu giằng co ở giữa theo chênh lệch điểm số hai bên sau mỗi hiệp. Dẫn trước điểm sẽ kéo thanh về phía mình!</p>
            </div>
            <div class="rounded-2xl bg-slate-50 p-4 border border-slate-100">
                <p class="font-bold text-slate-900 mb-1">🏆 Tie-Breaker Phân Thắng</p>
                <p>Nếu bằng điểm sau 10 câu, người có nhiều câu đúng hơn sẽ thắng. Nếu vẫn bằng, người có tổng thời gian trả lời câu đúng nhanh hơn sẽ thắng.</p>
            </div>
            <div class="rounded-2xl bg-slate-50 p-4 border border-slate-100">
                <p class="font-bold text-slate-900 mb-1">⚡ Chống Bỏ Cuộc Sớm</p>
                <p>Người chủ động đầu hàng nhận 0 XP. Nếu đối thủ thoát trận ngay hiệp 1, bạn nhận +15 XP an ủi; từ hiệp 2 nhận trọn vẹn +40 XP.</p>
            </div>
        </div>
    </section>

    {{-- Modal Yêu Cầu Đăng Nhập Cho Khách Vãng Lai --}}
    <div x-show="showAuthModal" x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/70 backdrop-blur-xs p-4">
        <div @click.away="showAuthModal = false" class="max-w-md w-full bg-white rounded-3xl border border-red-200 p-6 sm:p-7 shadow-2xl space-y-5 font-sans text-center">
            <div class="grid h-14 w-14 place-items-center rounded-2xl bg-red-100 text-red-800 mx-auto">
                <i data-lucide="lock" class="h-7 w-7"></i>
            </div>
            <div>
                <h3 class="text-xl font-black text-slate-900 font-serif">Đăng Nhập Để Thi Đấu</h3>
                <p class="text-xs sm:text-sm text-slate-600 mt-2 leading-relaxed">
                    Đấu trường Chinese Duel 1v1 yêu cầu bạn đăng nhập để đảm bảo tính công bằng, tích lũy điểm XP và lưu các từ vựng sai vào bộ Flashcard SRS cá nhân.
                </p>
            </div>
            <div class="space-y-2.5 pt-2">
                <a href="{{ route('login') }}?redirect={{ urlencode(request()->getRequestUri()) }}"
                    class="block w-full py-3 px-4 rounded-xl bg-[#991b1b] hover:bg-red-800 text-white font-bold text-sm shadow-md shadow-red-950/20 transition">
                    Đăng Nhập Ngay
                </a>
                <a href="{{ route('register') }}?redirect={{ urlencode(request()->getRequestUri()) }}"
                    class="block w-full py-3 px-4 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-sm transition">
                    Tạo Tài Khoản Mới (Miễn phí)
                </a>
                <button type="button" @click="showAuthModal = false"
                    class="block w-full py-2 text-xs font-medium text-slate-400 hover:text-slate-600 transition cursor-pointer">
                    Để sau, tôi muốn xem tiếp
                </button>
            </div>
        </div>
    </div>

</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('duelLobbyApp', (waitingCountsInit, isUserLoggedIn) => ({
        selectedLevel: 3,
        joinCode: '',
        isLoading: false,
        loadingType: '',
        errorMessage: '',
        showAuthModal: false,
        isLoggedIn: isUserLoggedIn,
        waitingCounts: waitingCountsInit || {},

        getLevelTitle(lvl) {
            const titles = {
                1: 'Sơ cấp 1 · 150 từ',
                2: 'Sơ cấp 2 · 300 từ',
                3: 'Trung cấp 1 · 600 từ',
                4: 'Trung cấp 2 · 1200 từ',
                5: 'Cao cấp 1 · 2500 từ',
                6: 'Cao cấp 2 · 5000+ từ',
            };
            return titles[lvl] || '';
        },

        handleAction(type) {
            if (!this.isLoggedIn) {
                this.showAuthModal = true;
                return;
            }

            if (type === 'matchmake') {
                this.startMatchmaking();
            } else if (type === 'create') {
                this.createPrivateRoom();
            } else if (type === 'join') {
                this.joinPrivateRoom();
            }
        },

        joinDirect(roomCode) {
            if (!this.isLoggedIn) {
                this.showAuthModal = true;
                return;
            }
            window.location.href = `/duel/room/${roomCode}`;
        },

        async cancelWaiting(roomCode) {
            this.isLoading = true;
            try {
                const res = await fetch('{{ route('duel.matchmake.cancel') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: JSON.stringify({ room_code: roomCode })
                });
                const data = await res.json();
                if (res.ok) {
                    window.location.reload();
                } else {
                    this.errorMessage = data.message || 'Không thể hủy tìm kiếm.';
                }
            } catch (err) {
                this.errorMessage = err.message;
            } finally {
                this.isLoading = false;
            }
        },

        async startMatchmaking() {
            this.isLoading = true;
            this.loadingType = 'matchmake';
            this.errorMessage = '';

            try {
                const res = await fetch('{{ route('duel.matchmake') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: JSON.stringify({ hsk_level: this.selectedLevel })
                });

                const data = await res.json();
                if (!res.ok) {
                    if (res.status === 409 && data.room_code) {
                        // Trận đấu đang diễn ra -> Điều hướng thẳng vào sàn đấu
                        window.location.href = `/duel/room/${data.room_code}`;
                        return;
                    }
                    throw new Error(data.message || 'Không thể ghép trận. Vui lòng thử lại.');
                }

                // Chuyển hướng sang Sàn đấu Arena
                window.location.href = `/duel/room/${data.room_code}`;
            } catch (err) {
                this.errorMessage = err.message;
            } finally {
                this.isLoading = false;
                this.loadingType = '';
            }
        },

        async createPrivateRoom() {
            this.isLoading = true;
            this.loadingType = 'create';
            this.errorMessage = '';

            try {
                const res = await fetch('{{ route('duel.room.create') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: JSON.stringify({ hsk_level: this.selectedLevel })
                });

                const data = await res.json();
                if (!res.ok) {
                    if (res.status === 409 && data.room_code) {
                        window.location.href = `/duel/room/${data.room_code}`;
                        return;
                    }
                    throw new Error(data.message || 'Không thể tạo phòng riêng. Vui lòng thử lại.');
                }

                window.location.href = `/duel/room/${data.room_code}`;
            } catch (err) {
                this.errorMessage = err.message;
            } finally {
                this.isLoading = false;
                this.loadingType = '';
            }
        },

        async joinPrivateRoom() {
            if (!this.joinCode) return;
            this.isLoading = true;
            this.loadingType = 'join';
            this.errorMessage = '';

            try {
                const res = await fetch('{{ route('duel.room.join') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: JSON.stringify({ invite_code: this.joinCode.trim().toUpperCase() })
                });

                const data = await res.json();
                if (!res.ok) {
                    throw new Error(data.message || 'Mã phòng không hợp lệ hoặc phòng đã đủ người.');
                }

                window.location.href = `/duel/room/${data.room_code}`;
            } catch (err) {
                this.errorMessage = err.message;
            } finally {
                this.isLoading = false;
                this.loadingType = '';
            }
        }
    }));
});
</script>
@endsection
