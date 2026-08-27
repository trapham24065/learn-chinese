@extends('layouts.app')

@section('title', 'Thi thử HSK mô phỏng | Chinese Deck')

@section('content')

{{-- Breadcrumb --}}
<nav class="mb-6 flex items-center gap-2 text-sm text-slate-500">
    <a href="{{ route('hsk.overview') }}" class="hover:text-[#991b1b] transition">Lộ trình HSK</a>
    <span>/</span>
    <span class="font-semibold text-slate-800">Thi thử HSK mô phỏng</span>
</nav>

{{-- Hero Section --}}
<section class="relative overflow-hidden rounded-[2.5rem] bg-gradient-to-br from-[#991b1b] via-[#7f1d1d] to-slate-950 p-8 sm:p-12 text-white shadow-2xl shadow-red-950/20 mb-10">
    <div class="absolute -right-10 -bottom-10 text-[14rem] font-black leading-none opacity-5 select-none pointer-events-none">
        考
    </div>
    
    <div class="relative grid gap-8 lg:grid-cols-[1.4fr_1fr] items-center">
        <div>
            <div class="inline-flex items-center gap-2 rounded-full bg-amber-400/20 border border-amber-300/30 px-4 py-1.5 text-xs font-bold uppercase tracking-widest text-amber-300 backdrop-blur">
                <i data-lucide="award" class="h-4 w-4 text-amber-400"></i>
                <span>HSK Mock Examination Room</span>
            </div>
            <h1 class="mt-5 text-4xl sm:text-5xl lg:text-6xl font-black tracking-tight leading-tight">
                Thi thử HSK mô phỏng chuẩn quốc tế
            </h1>
            <p class="mt-5 max-w-xl text-base sm:text-lg text-white/80 leading-relaxed font-normal">
                Làm bài thi tổng hợp có đồng hồ đếm ngược, phân tích toàn diện 3 kỹ năng <strong>Nghe hiểu</strong>, <strong>Đọc hiểu</strong>, <strong>Ngữ pháp</strong> và nhận <strong>Chứng chỉ Online</strong> ngay khi đạt điểm đỗ!
            </p>

            {{-- Feature Badges --}}
            <div class="mt-8 flex flex-wrap gap-4 text-xs font-semibold">
                <div class="flex items-center gap-2 rounded-2xl bg-white/10 px-4 py-2.5 backdrop-blur border border-white/10">
                    <i data-lucide="timer" class="h-4 w-4 text-amber-400"></i>
                    <span>Đồng hồ đếm ngược & Tự nộp</span>
                </div>
                <div class="flex items-center gap-2 rounded-2xl bg-white/10 px-4 py-2.5 backdrop-blur border border-white/10">
                    <i data-lucide="bar-chart-3" class="h-4 w-4 text-emerald-400"></i>
                    <span>Bảng điểm chi tiết 3 kỹ năng</span>
                </div>
                <div class="flex items-center gap-2 rounded-2xl bg-white/10 px-4 py-2.5 backdrop-blur border border-white/10">
                    <i data-lucide="award" class="h-4 w-4 text-amber-300"></i>
                    <span>Cấp chứng chỉ mã số thực</span>
                </div>
            </div>
        </div>

        {{-- Quick Stat Summary Card --}}
        <div class="rounded-3xl border border-white/15 bg-white/10 p-6 backdrop-blur shadow-xl">
            <h3 class="text-xs uppercase tracking-[0.24em] font-bold text-amber-200/90">Thành tích thi thử của bạn</h3>
            <div class="mt-4 grid grid-cols-2 gap-3">
                <div class="rounded-2xl bg-slate-950/40 p-4 border border-white/5">
                    <p class="text-xs text-slate-300">Số bài đã thi</p>
                    <p class="mt-1 text-3xl font-black text-white">{{ $stats['total_taken'] }}</p>
                </div>
                <div class="rounded-2xl bg-slate-950/40 p-4 border border-white/5">
                    <p class="text-xs text-slate-300">Điểm cao nhất</p>
                    <p class="mt-1 text-3xl font-black text-amber-300">
                        {{ $stats['highest_score'] }} <span class="text-xs font-normal text-slate-300">/ 300</span>
                    </p>
                </div>
                <div class="rounded-2xl bg-slate-950/40 p-4 border border-white/5">
                    <p class="text-xs text-slate-300">Bài thi Đạt</p>
                    <p class="mt-1 text-3xl font-black text-emerald-400">{{ $stats['passed_count'] }}</p>
                </div>
                <div class="rounded-2xl bg-slate-950/40 p-4 border border-white/5">
                    <p class="text-xs text-slate-300">Chứng chỉ đạt</p>
                    <p class="mt-1 text-3xl font-black text-amber-400 flex items-center gap-1.5">
                        <i data-lucide="award" class="h-6 w-6 fill-current text-amber-400"></i>
                        <span>{{ $stats['certificates_count'] }}</span>
                    </p>
                </div>
            </div>
            <p class="mt-4 text-xs text-slate-300/80 leading-relaxed italic">
                * Thang điểm 300 chuẩn quốc tế (100đ Nghe + 100đ Đọc + 100đ Ngữ pháp). Đạt từ 180 điểm trở lên được cấp chứng chỉ.
            </p>
        </div>
    </div>
</section>

{{-- Level Selection Grid --}}
<section class="mb-14">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.24em] text-[#991b1b]">Chọn cấp độ</p>
            <h2 class="mt-1 text-2xl sm:text-3xl font-black text-slate-900">Danh sách các phòng thi HSK</h2>
        </div>
    </div>

    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @foreach($specs as $level => $spec)
        <div class="group relative overflow-hidden rounded-[2rem] border border-slate-200/80 bg-white p-7 shadow-xl shadow-slate-900/5 transition hover:-translate-y-1.5 hover:shadow-2xl hover:shadow-slate-900/10 flex flex-col justify-between">
            {{-- Top Color Bar --}}
            <div class="absolute inset-x-0 top-0 h-1.5" style="background: {{ $spec['color'] }}"></div>

            <div>
                {{-- Header --}}
                <div class="flex items-center justify-between">
                    <span class="rounded-full px-3.5 py-1 text-xs font-black uppercase tracking-wider border {{ $spec['badge_bg'] }}">
                        {{ $spec['label'] }}
                    </span>
                    <span class="inline-flex items-center gap-1 text-xs font-bold text-slate-500 bg-slate-100 px-3 py-1 rounded-full">
                        <i data-lucide="timer" class="h-3.5 w-3.5 text-slate-400"></i>
                        {{ $spec['time_limit'] }} phút
                    </span>
                </div>

                {{-- Level Chinese Number Watermark --}}
                <p class="mt-4 text-6xl font-black leading-none tracking-tight select-none opacity-20 transition group-hover:opacity-35"
                   style="color: {{ $spec['color'] }}">
                    {{ ['一','二','三','四','五','六'][$level - 1] }}
                </p>

                <h3 class="mt-2 text-xl font-black text-slate-900">{{ $spec['title'] }}</h3>
                <p class="mt-2 text-sm text-slate-600 leading-relaxed">{{ $spec['desc'] }}</p>

                {{-- Structure Breakdown --}}
                <div class="mt-5 space-y-2 rounded-2xl bg-slate-50 p-4 text-xs font-medium text-slate-700">
                    <div class="flex items-center justify-between">
                        <span class="flex items-center gap-1.5">
                            <i data-lucide="headphones" class="h-3.5 w-3.5 text-blue-600"></i>
                            Phần 1: Nghe hiểu
                        </span>
                        <span class="font-bold text-slate-900">{{ $spec['listening_count'] }} câu (100đ)</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="flex items-center gap-1.5">
                            <i data-lucide="book-open" class="h-3.5 w-3.5 text-emerald-600"></i>
                            Phần 2: Đọc hiểu
                        </span>
                        <span class="font-bold text-slate-900">{{ $spec['reading_count'] }} câu (100đ)</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="flex items-center gap-1.5">
                            <i data-lucide="pen-tool" class="h-3.5 w-3.5 text-purple-600"></i>
                            Phần 3: Ngữ pháp
                        </span>
                        <span class="font-bold text-slate-900">{{ $spec['grammar_count'] }} câu (100đ)</span>
                    </div>
                    <div class="pt-2 border-t border-slate-200/80 flex items-center justify-between font-bold text-slate-900">
                        <span>Tổng số câu:</span>
                        <span>{{ $spec['question_count'] }} câu · 300 điểm</span>
                    </div>
                </div>
            </div>

            {{-- Action Button --}}
            <div class="mt-6 pt-4 border-t border-slate-100">
                <a href="{{ route('hsk.mock.start', $level) }}"
                   class="w-full inline-flex items-center justify-center gap-2 rounded-2xl py-3.5 px-4 text-sm font-bold text-white shadow-lg transition active:scale-95 hover:opacity-95"
                   style="background: {{ $spec['color'] }}">
                    <i data-lucide="play" class="h-4 w-4 fill-current"></i>
                    <span>Vào phòng thi ngay</span>
                </a>
            </div>
        </div>
        @endforeach
    </div>
</section>

{{-- Exam History Section --}}
@if($history->isNotEmpty())
<section class="rounded-[2.5rem] border border-white/80 bg-white/80 p-8 shadow-xl shadow-slate-900/5 backdrop-blur mb-12">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.24em] text-[#991b1b]">Lịch sử làm bài</p>
            <h2 class="mt-1 text-2xl font-black text-slate-900">Các bài thi thử gần đây của bạn</h2>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-600">
            <thead class="bg-slate-50 text-xs uppercase text-slate-400 font-bold border-b border-slate-200">
                <tr>
                    <th class="px-5 py-3.5 rounded-l-2xl">Bài thi</th>
                    <th class="px-5 py-3.5">Thời gian</th>
                    <th class="px-5 py-3.5">Nghe</th>
                    <th class="px-5 py-3.5">Đọc</th>
                    <th class="px-5 py-3.5">Ngữ pháp</th>
                    <th class="px-5 py-3.5">Tổng điểm</th>
                    <th class="px-5 py-3.5">Kết quả</th>
                    <th class="px-5 py-3.5 rounded-r-2xl text-right">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($history as $item)
                <tr class="hover:bg-slate-50/80 transition">
                    <td class="px-5 py-4 font-bold text-slate-900 flex items-center gap-2.5">
                        <span class="grid h-8 w-8 place-items-center rounded-xl font-black text-xs text-white"
                              style="background: {{ $specs[$item->hsk_level]['color'] ?? '#991b1b' }}">
                            {{ $item->hsk_level }}
                        </span>
                        <span>{{ $item->title }}</span>
                    </td>
                    <td class="px-5 py-4 text-xs text-slate-500">
                        {{ $item->completed_at?->format('d/m/Y H:i') ?? $item->created_at->format('d/m/Y H:i') }}
                        <span class="block text-[11px] text-slate-400">Thời gian: {{ $item->formatted_duration }}</span>
                    </td>
                    <td class="px-5 py-4 font-semibold text-blue-600">{{ $item->listening_score }}/100</td>
                    <td class="px-5 py-4 font-semibold text-emerald-600">{{ $item->reading_score }}/100</td>
                    <td class="px-5 py-4 font-semibold text-purple-600">{{ $item->grammar_score }}/100</td>
                    <td class="px-5 py-4">
                        <span class="text-base font-black {{ $item->passed ? 'text-emerald-600' : 'text-slate-800' }}">
                            {{ $item->total_score }}
                        </span>
                        <span class="text-xs text-slate-400">/300</span>
                    </td>
                    <td class="px-5 py-4">
                        @if($item->passed)
                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 border border-emerald-200 px-3 py-1 text-xs font-bold text-emerald-700">
                            <i data-lucide="check" class="h-3 w-3"></i> Đạt (合格)
                        </span>
                        @else
                        <span class="inline-flex items-center gap-1 rounded-full bg-red-50 border border-red-200 px-3 py-1 text-xs font-bold text-red-700">
                            <i data-lucide="x" class="h-3 w-3"></i> Chưa đạt
                        </span>
                        @endif
                    </td>
                    <td class="px-5 py-4 text-right">
                        <div class="inline-flex items-center gap-2">
                            <a href="{{ route('hsk.mock.result', $item->id) }}"
                               class="rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-xs font-bold text-slate-700 hover:bg-slate-50 transition shadow-sm">
                                Chi tiết
                            </a>
                            @if($item->certificate_code)
                            <a href="{{ route('hsk.mock.certificate', $item->certificate_code) }}"
                               class="rounded-xl bg-amber-400/20 text-amber-900 border border-amber-300 px-3 py-1.5 text-xs font-bold hover:bg-amber-400/30 transition shadow-sm inline-flex items-center gap-1">
                                <i data-lucide="award" class="h-3.5 w-3.5 text-amber-600"></i>
                                Chứng chỉ
                            </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>
@endif

@endsection
