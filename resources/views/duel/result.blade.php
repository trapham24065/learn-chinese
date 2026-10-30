@extends('layouts.app')

@section('title', 'Kết Quả Trận Đấu · ' . $roomCode . ' | Chinese Duel')

@section('content')
<div class="max-w-4xl mx-auto space-y-8 pb-16 font-serif-custom select-none">

    {{-- Top Ambience Result Header --}}
    <section class="relative overflow-hidden rounded-[2.5rem] border-2 p-8 sm:p-10 text-center shadow-xl backdrop-blur"
        :class="{
            'bg-gradient-to-b from-emerald-50/80 via-white to-emerald-50/30 border-emerald-300 shadow-emerald-950/5': '{{ $outcome }}' === 'win',
            'bg-gradient-to-b from-rose-50/80 via-white to-rose-50/30 border-rose-300 shadow-rose-950/5': '{{ $outcome }}' === 'loss',
            'bg-gradient-to-b from-amber-50/80 via-white to-amber-50/30 border-amber-300 shadow-amber-950/5': '{{ $outcome }}' === 'draw',
            'bg-gradient-to-b from-blue-50/80 via-white to-blue-50/30 border-blue-300 shadow-blue-950/5': '{{ $outcome }}' === 'abandoned',
        }">

        <div class="space-y-3">
            {{-- Big Emoji Badge --}}
            <div class="text-6xl sm:text-7xl">
                @if ($outcome === 'win') 🏆
                @elseif ($outcome === 'loss') ⚔️
                @elseif ($outcome === 'draw') 🤝
                @else 🏳️
                @endif
            </div>

            <h1 class="text-3xl sm:text-4xl md:text-5xl font-black font-serif text-slate-900 tracking-tight">
                @if ($outcome === 'win')
                    Chiến Thắng Tuyệt Đối!
                @elseif ($outcome === 'loss')
                    Tiếc Nuối... Thất Bại
                @elseif ($outcome === 'draw')
                    Bất Phân Thắng Bại!
                @else
                    Trận Đấu Đã Kết Thúc (Đối Thủ Rời Trận)
                @endif
            </h1>

            <p class="text-xs sm:text-sm font-sans text-slate-600 max-w-lg mx-auto leading-relaxed">
                Phòng thi đấu: <span class="font-mono font-bold text-red-900">{{ $roomCode }}</span> · HSK {{ $room->hsk_level }} · 
                Hoàn thành lúc {{ $room->finished_at ? $room->finished_at->format('H:i, d/m/Y') : now()->format('H:i, d/m/Y') }}
            </p>
        </div>

        {{-- Head-to-Head Scoreboard --}}
        <div class="mt-8 grid grid-cols-2 gap-4 max-w-lg mx-auto bg-white/90 backdrop-blur rounded-3xl border border-slate-200/80 p-5 sm:p-6 shadow-sm font-sans">
            {{-- My Side --}}
            <div class="border-r border-slate-200 pr-3 sm:pr-4 text-center">
                <span class="inline-block rounded-full bg-amber-400 text-slate-950 text-[10px] font-black px-2 py-0.5 mb-1.5 uppercase tracking-wider">
                    Bạn
                </span>
                <p class="truncate text-sm sm:text-base font-bold text-slate-900">{{ $stats['my_name'] }}</p>
                <p class="text-3xl sm:text-4xl font-black text-red-800 tracking-tight mt-1">{{ $stats['my_score'] }} <span class="text-xs text-slate-400 font-normal">điểm</span></p>
                <div class="flex items-center justify-center gap-2 mt-2 text-xs text-slate-500 font-medium">
                    <span class="text-emerald-700 font-bold">{{ $stats['my_correct'] }} đúng</span> ·
                    <span class="text-rose-700 font-bold">{{ $stats['my_incorrect'] }} sai</span> ·
                    <span class="text-amber-700 font-bold">{{ $stats['my_timeout'] }} hết giờ</span>
                </div>
                <p class="text-[11px] text-slate-400 mt-1">TB: {{ $stats['avg_time'] }}s / câu</p>
            </div>

            {{-- Opponent Side --}}
            <div class="pl-3 sm:pr-4 text-center">
                <span class="inline-block rounded-full bg-slate-100 text-slate-600 text-[10px] font-bold px-2 py-0.5 mb-1.5 uppercase tracking-wider">
                    Đối thủ
                </span>
                <p class="truncate text-sm sm:text-base font-bold text-slate-900">{{ $stats['opp_name'] }}</p>
                <p class="text-3xl sm:text-4xl font-black text-indigo-800 tracking-tight mt-1">{{ $stats['opp_score'] }} <span class="text-xs text-slate-400 font-normal">điểm</span></p>
                <div class="flex items-center justify-center gap-2 mt-2 text-xs text-slate-500 font-medium">
                    <span>{{ ($slot === 1 ? $room->player2_correct_count : $room->player1_correct_count) ?? 0 }} câu đúng</span>
                </div>
            </div>
        </div>

        {{-- Primary Action Buttons --}}
        <div class="mt-8 flex flex-wrap items-center justify-center gap-3 font-sans">
            @if ($hasMistakes)
                <a href="{{ route('duel.review', $roomCode) }}"
                    class="py-3 px-6 rounded-xl bg-amber-600 hover:bg-amber-700 active:scale-[0.98] text-white font-bold text-sm shadow-md shadow-amber-950/20 transition flex items-center gap-2">
                    <i data-lucide="book-open" class="h-4 w-4"></i>
                    <span>Ôn Tập {{ $stats['my_incorrect'] + $stats['my_timeout'] }} Câu Sai</span>
                </a>
            @endif

            <a href="{{ route('duel.arena', $roomCode) }}"
                class="py-3 px-6 rounded-xl bg-[#991b1b] hover:bg-red-800 active:scale-[0.98] text-white font-bold text-sm shadow-md shadow-red-950/20 transition flex items-center gap-2">
                <i data-lucide="rotate-ccw" class="h-4 w-4"></i>
                <span>Tái Đấu Phòng Này</span>
            </a>

            <a href="{{ route('duel.history') }}"
                class="py-3 px-5 rounded-xl bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 font-bold text-sm shadow-xs transition flex items-center gap-1.5">
                <i data-lucide="history" class="h-4 w-4"></i>
                <span>Lịch Sử Đấu</span>
            </a>

            <a href="{{ route('duel.lobby') }}"
                class="py-3 px-5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-sm transition flex items-center gap-1.5">
                <i data-lucide="home" class="h-4 w-4"></i>
                <span>Về Sảnh</span>
            </a>
        </div>
    </section>

    {{-- Detailed Round-by-Round Breakdown --}}
    <section class="bg-white/90 backdrop-blur rounded-3xl border border-slate-200/80 p-6 sm:p-8 shadow-sm space-y-6">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <div>
                <h2 class="text-xl sm:text-2xl font-bold text-slate-900 font-serif">Chi Tiết 10 Hiệp Đấu</h2>
                <p class="text-xs sm:text-sm text-slate-500 font-sans mt-0.5">Xem lại lựa chọn của bạn, đối thủ và đáp án chính xác từng câu.</p>
            </div>
            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600 font-sans">
                10 Câu Hỏi
            </span>
        </div>

        <div class="space-y-4 font-sans">
            @foreach ($roundDetails as $r)
                <div class="rounded-2xl border p-4 sm:p-5 transition {{ $r['my_is_correct'] ? 'bg-emerald-50/40 border-emerald-200' : ($r['my_is_timeout'] ? 'bg-amber-50/40 border-amber-200' : 'bg-rose-50/40 border-rose-200') }}">
                    {{-- Row Top: Round & Result Badge --}}
                    <div class="flex flex-wrap items-center justify-between gap-2 mb-3">
                        <div class="flex items-center gap-2">
                            <span class="rounded-lg bg-slate-900 text-white font-mono font-bold text-xs px-2.5 py-1">
                                Hiệp {{ $r['round_number'] }}
                            </span>
                            @if ($r['my_is_correct'])
                                <span class="inline-flex items-center gap-1 text-xs font-bold text-emerald-800 bg-emerald-100 px-2.5 py-0.5 rounded-full">
                                    <i data-lucide="check-circle" class="h-3.5 w-3.5"></i>
                                    <span>Đúng (+{{ $r['my_score'] }} đ)</span>
                                </span>
                            @elseif ($r['my_is_timeout'])
                                <span class="inline-flex items-center gap-1 text-xs font-bold text-amber-800 bg-amber-100 px-2.5 py-0.5 rounded-full">
                                    <i data-lucide="clock" class="h-3.5 w-3.5"></i>
                                    <span>Hết giờ (Timeout)</span>
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 text-xs font-bold text-rose-800 bg-rose-100 px-2.5 py-0.5 rounded-full">
                                    <i data-lucide="x-circle" class="h-3.5 w-3.5"></i>
                                    <span>Sai (0 đ)</span>
                                </span>
                            @endif
                        </div>

                        <span class="text-xs text-slate-500">
                            {{ $r['my_time'] !== null ? $r['my_time'] . 's' : 'Quá 10s' }}
                        </span>
                    </div>

                    {{-- Question Content --}}
                    <div class="space-y-1 mb-4">
                        <p class="text-xl sm:text-2xl font-black font-serif text-slate-900 tracking-wide">
                            {{ $r['question'] }}
                        </p>
                        @if ($r['pinyin'])
                            <p class="text-xs sm:text-sm font-medium text-amber-800 font-sans">
                                {{ $r['pinyin'] }}
                            </p>
                        @endif
                    </div>

                    {{-- Answers Comparison Grid --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs sm:text-sm">
                        {{-- My Chosen Answer --}}
                        <div class="rounded-xl p-3 bg-white border border-slate-200">
                            <span class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Bạn đã chọn:</span>
                            <span class="font-bold {{ $r['my_is_correct'] ? 'text-emerald-700' : 'text-rose-700' }}">
                                {{ $r['my_selected'] ?? 'Hết giờ (Không chọn)' }}
                            </span>
                        </div>

                        {{-- Correct Answer --}}
                        <div class="rounded-xl p-3 bg-emerald-50 border border-emerald-200">
                            <span class="block text-[11px] font-bold text-emerald-800 uppercase tracking-wider mb-1">Đáp án đúng:</span>
                            <span class="font-bold text-emerald-950">
                                {{ $r['correct_answer'] }}
                            </span>
                        </div>
                    </div>

                    {{-- Explanation --}}
                    @if (!empty($r['explanation']))
                        <div class="mt-3 text-xs text-slate-600 bg-white/60 rounded-xl p-3 border border-slate-200/60 leading-relaxed">
                            <span class="font-bold text-slate-800">💡 Giải thích:</span> {{ $r['explanation'] }}
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </section>

</div>
@endsection
