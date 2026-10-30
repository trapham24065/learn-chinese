@extends('layouts.app')

@section('title', 'Chinese Duel · Đấu Trường HSK 1v1 Đối Kháng | Learn Chinese')

@section('content')
<div x-data="duelLobbyApp()" class="space-y-8 pb-16 font-serif-custom max-w-5xl mx-auto">

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
                    <a href="{{ route('duel.history') }}" class="inline-flex items-center gap-2 py-2 px-4 rounded-xl bg-white/90 hover:bg-white text-slate-800 hover:text-red-900 border border-red-200/80 font-bold text-xs shadow-xs font-sans transition">
                        <i data-lucide="history" class="h-4 w-4 text-red-700"></i>
                        <span>Xem Lịch Sử Thi Đấu Cá Nhân</span>
                    </a>
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
                    <p class="text-xl font-black text-emerald-700 font-sans mt-0.5">+100 XP</p>
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

    {{-- Step 1: Select HSK Level --}}
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
                    @click="startMatchmaking()"
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
                    @click="createPrivateRoom()"
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
                        @keyup.enter="joinPrivateRoom()"
                        maxlength="6"
                        placeholder="VD: ABC123"
                        class="w-full text-center tracking-[0.25em] uppercase font-mono font-bold text-lg rounded-xl border border-slate-300 py-2.5 px-3 focus:border-[#991b1b] focus:ring-2 focus:ring-red-100 focus:outline-none transition">
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-slate-100">
                <button type="button"
                    @click="joinPrivateRoom()"
                    :disabled="isLoading || !joinCode || joinCode.length < 4"
                    class="w-full py-3.5 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 active:scale-[0.98] text-white font-bold text-sm shadow-md shadow-slate-950/20 transition flex items-center justify-center gap-2 cursor-pointer disabled:opacity-40">
                    <i data-lucide="log-in" class="h-4 w-4" x-show="!isLoading"></i>
                    <i data-lucide="loader-2" class="h-4 w-4 animate-spin" x-show="isLoading && loadingType === 'join'" x-cloak></i>
                    <span>Vào Phòng Thi Đấu</span>
                </button>
            </div>
        </div>

    </div>

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
                <p class="font-bold text-slate-900 mb-1">⚡ Không Treo Phòng</p>
                <p>Mỗi người có 25 giây dự trữ mất kết nối (Heartbeat grace). Nếu quá thời gian không gửi tín hiệu, hệ thống sẽ xử thua Forfeit.</p>
            </div>
        </div>
    </section>

</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('duelLobbyApp', () => ({
        selectedLevel: 3,
        joinCode: '',
        isLoading: false,
        loadingType: '',
        errorMessage: '',

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
