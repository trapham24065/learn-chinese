@extends('layouts.app')

@section('title', 'Mini-Game Học Tập | Fast Match & Audio Pop Quiz | Learn Chinese')

@section('content')
<div class="space-y-8 max-w-6xl mx-auto pb-12">
    {{-- Header Banner --}}
    <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-900 via-slate-800 to-red-950 p-6 sm:p-10 text-white shadow-2xl">
        <div class="absolute -right-10 -bottom-10 h-64 w-64 rounded-full bg-red-600/20 blur-3xl pointer-events-none"></div>
        <div class="absolute right-20 top-6 h-32 w-32 rounded-full bg-amber-500/10 blur-2xl pointer-events-none"></div>

        <div class="relative z-10 max-w-2xl">
            <div class="inline-flex items-center gap-2 rounded-full bg-red-500/20 border border-red-500/30 px-3.5 py-1 text-xs font-bold uppercase tracking-wider text-red-300">
                <i data-lucide="gamepad-2" class="h-3.5 w-3.5"></i>
                Luyện tập tương tác & Phản xạ
            </div>
            <h1 class="mt-4 text-3xl sm:text-4xl font-black tracking-tight text-white">
                Mini-Game Học Tập Tiếng Trung
            </h1>
            <p class="mt-3 text-slate-300 text-sm sm:text-base leading-relaxed">
                Rèn phản xạ từ vựng tức thì và luyện đôi tai nhạy bén với ngữ âm Hán ngữ. Mỗi phiên hoàn thành đều đóng góp thực chất vào mục tiêu học tập hàng ngày!
            </p>

            <div class="mt-6 flex flex-wrap items-center gap-4 text-xs font-semibold text-slate-300">
                <span class="inline-flex items-center gap-1.5 rounded-xl bg-white/10 px-3 py-1.5 backdrop-blur-sm">
                    <i data-lucide="shield-check" class="h-4 w-4 text-emerald-400"></i>
                    Chống cày điểm ảo (Anti-farming XP)
                </span>
                <span class="inline-flex items-center gap-1.5 rounded-xl bg-white/10 px-3 py-1.5 backdrop-blur-sm">
                    <i data-lucide="rotate-ccw" class="h-4 w-4 text-amber-400"></i>
                    SRS: Tự động gom từ nhầm để ôn ngay
                </span>
                <span class="inline-flex items-center gap-1.5 rounded-xl bg-white/10 px-3 py-1.5 backdrop-blur-sm">
                    <i data-lucide="volume-2" class="h-4 w-4 text-rose-400"></i>
                    Âm thanh Web Audio mượt mà
                </span>
            </div>
        </div>

        {{-- Mini Stats Bar for Logged-in Users --}}
        @if($user && $dailySummary)
        <div class="relative z-10 mt-8 grid grid-cols-2 sm:grid-cols-4 gap-3 border-t border-white/10 pt-6">
            <div class="rounded-2xl bg-white/5 border border-white/10 p-3.5">
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Luyện tập hôm nay</p>
                <p class="mt-1 text-xl font-black text-amber-300">
                    {{ $dailySummary['practice']['current'] ?? 0 }}/{{ $dailySummary['practice']['target'] ?? 3 }} phiên
                </p>
                <p class="text-[10px] text-slate-400 mt-0.5">+34% mục tiêu/phiên</p>
            </div>
            <div class="rounded-2xl bg-white/5 border border-white/10 p-3.5">
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Chuỗi ngày (Streak)</p>
                <p class="mt-1 text-xl font-black text-white flex items-center gap-1">
                    <i data-lucide="flame" class="h-4 w-4 text-amber-400"></i>
                    {{ $stats->current_streak ?? 0 }} ngày
                </p>
                <p class="text-[10px] text-slate-400 mt-0.5">Kỷ lục: {{ $stats->longest_streak ?? 0 }} ngày</p>
            </div>
            <div class="rounded-2xl bg-white/5 border border-white/10 p-3.5">
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Tổng điểm kinh nghiệm</p>
                <p class="mt-1 text-xl font-black text-white flex items-center gap-1">
                    <i data-lucide="sparkles" class="h-4 w-4 text-amber-400"></i>
                    {{ $stats->total_xp ?? 0 }} XP
                </p>
                <p class="text-[10px] text-slate-400 mt-0.5">+15 XP cho ván đầu</p>
            </div>
            <div class="rounded-2xl bg-white/5 border border-white/10 p-3.5">
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Mục tiêu ngày</p>
                <p class="mt-1 text-xl font-black {{ $dailySummary['completed'] ? 'text-emerald-400' : 'text-amber-400' }}">
                    {{ $dailySummary['progress_percent'] }}%
                </p>
                <p class="text-[10px] text-slate-400 mt-0.5">
                    {{ $dailySummary['completed'] ? 'Đã hoàn thành! 🎉' : 'Đang tiến hành' }}
                </p>
            </div>
        </div>
        @endif
    </section>

    {{-- Game Cards Grid --}}
    <div class="grid gap-6 md:grid-cols-2">
        {{-- Game 1: Fast Match --}}
        <div class="group relative flex flex-col justify-between overflow-hidden rounded-3xl border border-slate-200 bg-white p-7 shadow-lg shadow-slate-900/5 transition duration-300 hover:-translate-y-1 hover:border-red-300 hover:shadow-xl hover:shadow-red-950/10">
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-red-100 px-3 py-1 text-xs font-bold uppercase text-[#991b1b]">
                        <i data-lucide="zap" class="h-3.5 w-3.5"></i>
                        Trí nhớ &amp; Tốc độ
                    </span>
                    <span class="rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-semibold text-slate-600">
                        8 - 12 thẻ
                    </span>
                </div>

                <div class="flex items-center gap-4 pt-1">
                    <div class="grid h-14 w-14 shrink-0 place-items-center rounded-2xl bg-red-50 text-[#991b1b] border border-red-100 group-hover:bg-[#991b1b] group-hover:text-white transition">
                        <i data-lucide="layers" class="h-7 w-7"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-black text-slate-950">Fast Match</h2>
                        <p class="text-xs font-semibold text-slate-500">Nối Từ Siêu Tốc (Hán tự ↔ Nghĩa / Pinyin)</p>
                    </div>
                </div>

                <p class="text-sm leading-relaxed text-slate-600">
                    Lật mở và kết nối các cặp thẻ tương ứng trong thời gian nhanh nhất. Giữ combo liên tiếp để nhân điểm số và giành Huy chương Vàng!
                </p>

                <div class="space-y-2 rounded-2xl bg-slate-50 p-4 text-xs text-slate-700 border border-slate-100">
                    <div class="flex items-center gap-2 font-semibold">
                        <i data-lucide="check-circle-2" class="h-4 w-4 text-emerald-600 shrink-0"></i>
                        <span>3 mức độ: Dễ (4 cặp) • Vừa (5 cặp) • Thử thách (6 cặp)</span>
                    </div>
                    <div class="flex items-center gap-2 font-semibold">
                        <i data-lucide="check-circle-2" class="h-4 w-4 text-emerald-600 shrink-0"></i>
                        <span>2 chế độ: Luyện tập tự do &amp; Chạy đua thời gian</span>
                    </div>
                    <div class="flex items-center gap-2 font-semibold">
                        <i data-lucide="check-circle-2" class="h-4 w-4 text-emerald-600 shrink-0"></i>
                        <span>Bảng ôn tập tức thì các từ vừa nối nhầm (SRS Loop)</span>
                    </div>
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-slate-100">
                <a href="{{ route('games.fast-match') }}"
                   class="flex w-full items-center justify-center gap-2 rounded-2xl bg-[#991b1b] px-5 py-3.5 text-sm font-bold text-white shadow-md shadow-red-950/20 transition hover:bg-red-800 active:scale-[0.99]">
                    <i data-lucide="play" class="h-4 w-4 fill-current"></i>
                    <span>Chơi Fast Match ngay</span>
                </a>
            </div>
        </div>

        {{-- Game 2: Audio Pop Quiz --}}
        <div class="group relative flex flex-col justify-between overflow-hidden rounded-3xl border border-slate-200 bg-white p-7 shadow-lg shadow-slate-900/5 transition duration-300 hover:-translate-y-1 hover:border-amber-300 hover:shadow-xl hover:shadow-amber-950/10">
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-100 px-3 py-1 text-xs font-bold uppercase text-amber-900">
                        <i data-lucide="headphones" class="h-3.5 w-3.5"></i>
                        Thính lực &amp; Bẫy phát âm
                    </span>
                    <span class="rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-semibold text-slate-600">
                        5 câu / phiên
                    </span>
                </div>

                <div class="flex items-center gap-4 pt-1">
                    <div class="grid h-14 w-14 shrink-0 place-items-center rounded-2xl bg-amber-50 text-amber-700 border border-amber-100 group-hover:bg-amber-600 group-hover:text-white transition">
                        <i data-lucide="volume-2" class="h-7 w-7"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-black text-slate-950">Audio Pop Quiz</h2>
                        <p class="text-xs font-semibold text-slate-500">Đố Vui Phản Xạ Âm Thanh &amp; Cặp Âm Dễ Nhầm</p>
                    </div>
                </div>

                <p class="text-sm leading-relaxed text-slate-600">
                    Nghe phát âm chuẩn Mandarin và chọn đáp án chính xác. Chinh phục triệt để các cạm bẫy thanh mẫu: <span class="font-bold text-slate-900">zh / z</span>, <span class="font-bold text-slate-900">q / j</span>, <span class="font-bold text-slate-900">b / p</span> và 4 thanh điệu!
                </p>

                <div class="space-y-2 rounded-2xl bg-slate-50 p-4 text-xs text-slate-700 border border-slate-100">
                    <div class="flex items-center gap-2 font-semibold">
                        <i data-lucide="check-circle-2" class="h-4 w-4 text-emerald-600 shrink-0"></i>
                        <span>3 chế độ: Cặp âm dễ nhầm (Minimal Pairs) • Thanh điệu • Từ vựng</span>
                    </div>
                    <div class="flex items-center gap-2 font-semibold">
                        <i data-lucide="check-circle-2" class="h-4 w-4 text-emerald-600 shrink-0"></i>
                        <span>Hỗ trợ nghe chậm 0.75x &amp; Nghe tốc độ chuẩn 1.0x</span>
                    </div>
                    <div class="flex items-center gap-2 font-semibold">
                        <i data-lucide="check-circle-2" class="h-4 w-4 text-emerald-600 shrink-0"></i>
                        <span>Cung cấp liên kết thẳng đến Bảng Pinyin để ôn cặp âm vừa nhầm</span>
                    </div>
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-slate-100">
                <a href="{{ route('games.audio-quiz') }}"
                   class="flex w-full items-center justify-center gap-2 rounded-2xl bg-amber-600 px-5 py-3.5 text-sm font-bold text-white shadow-md shadow-amber-950/20 transition hover:bg-amber-700 active:scale-[0.99]">
                    <i data-lucide="play" class="h-4 w-4 fill-current"></i>
                    <span>Chơi Audio Pop Quiz ngay</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Recent Mini-game Activity for User --}}
    @if($recentGames->isNotEmpty())
    <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                <i data-lucide="history" class="h-4 w-4 text-slate-500"></i>
                Lịch sử luyện tập gần đây
            </h3>
            <span class="text-xs text-slate-400">5 lượt gần nhất</span>
        </div>

        <div class="mt-4 divide-y divide-slate-100">
            @foreach($recentGames as $act)
            <div class="py-3 flex items-center justify-between text-xs sm:text-sm">
                <div class="flex items-center gap-3">
                    <span class="grid h-8 w-8 place-items-center rounded-xl {{ $act->activity_type === 'fast_match_completed' ? 'bg-red-50 text-red-700' : 'bg-amber-50 text-amber-700' }}">
                        <i data-lucide="{{ $act->activity_type === 'fast_match_completed' ? 'layers' : 'volume-2' }}" class="h-4 w-4"></i>
                    </span>
                    <div>
                        <p class="font-bold text-slate-900">
                            {{ $act->activity_type === 'fast_match_completed' ? 'Fast Match' : 'Audio Pop Quiz' }}
                        </p>
                        <p class="text-[11px] text-slate-500">
                            {{ $act->created_at->diffForHumans() }} • Độ chính xác: {{ $act->meta['accuracy'] ?? 0 }}% • {{ $act->meta['duration_seconds'] ?? 0 }}s
                        </p>
                    </div>
                </div>

                <div class="text-right">
                    <span class="inline-flex items-center gap-1 font-bold text-amber-600 bg-amber-50 border border-amber-200/60 rounded-full px-2.5 py-0.5 text-xs">
                        +{{ $act->xp_earned }} XP
                    </span>
                    @if(isset($act->meta['score']))
                        <p class="text-[11px] text-slate-400 font-semibold mt-0.5">{{ $act->meta['score'] }} điểm</p>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </section>
    @endif
</div>
@endsection
