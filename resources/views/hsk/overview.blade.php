@extends('layouts.app')

@section('title', 'Lộ trình HSK | Chinese Deck')

@section('content')

{{-- Hero: Thiên Lý Giang Sơn · Học Đồ (Trường Quyển Sơn Thủy) --}}
<section class="relative mb-10 overflow-hidden hsk-scroll-frame p-6 sm:p-10">
    {{-- Traditional Corner Patterns (Hồi văn tứ giác) --}}
    <div class="pointer-events-none absolute left-3 top-3 text-amber-800/30 select-none">
        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <path d="M2 2h8v8H6V6H2" />
        </svg>
    </div>
    <div class="pointer-events-none absolute right-3 top-3 text-amber-800/30 select-none">
        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <path d="M22 2h-8v8h4V6h4" />
        </svg>
    </div>
    <div class="pointer-events-none absolute left-3 bottom-3 text-amber-800/30 select-none">
        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <path d="M2 22h8v-8H6v4H2" />
        </svg>
    </div>
    <div class="pointer-events-none absolute right-3 bottom-3 text-amber-800/30 select-none">
        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <path d="M22 22h-8v-8h4v4h4" />
        </svg>
    </div>

    {{-- Watermark Seal --}}
    <div class="pointer-events-none absolute -right-6 -bottom-8 opacity-5 text-slate-900 select-none font-chinese text-[13rem] leading-none font-black">
        山河
    </div>

    <div class="relative z-10 max-w-4xl">
        {{-- Poetic Badge & Couplet --}}
        <div class="inline-flex flex-wrap items-center gap-2.5 rounded-full border border-amber-800/20 bg-amber-900/5 px-4 py-1.5 text-xs">
            <span class="font-chinese font-bold text-[#991b1b]">千里江山 · 学途</span>
            <span class="text-amber-800/40">|</span>
            <span class="font-chinese italic text-slate-700">“千里之行，始于足下”</span>
            <span class="text-amber-800/40">·</span>
            <span class="font-serif italic text-slate-700">Hành trình vạn dặm khởi từ một bước chân</span>
        </div>

        {{-- Page Heading --}}
        <h1 class="mt-4 text-4xl font-black tracking-tight text-slate-900 sm:text-5xl">
            Lộ trình HSK <span class="text-slate-400 font-light">·</span> <span class="text-[#991b1b] font-serif">Giang Sơn Đồ Tri Thức</span>
        </h1>

        <p class="mt-3 text-base sm:text-lg leading-relaxed text-slate-700">
            Khép lại thư phòng tĩnh lặng, bước chân vào hành trình du sơn học đạo qua <strong class="text-slate-900">Sáu Tầng Vân Sơn</strong> của tiêu chuẩn HSK quốc tế (汉语水平考试). Mỗi cấp độ là một đỉnh cao tri thức, mỗi bài học là một dịch trạm dừng chân tiếp thêm sức mạnh.
        </p>

        {{-- Journey Stats Bar --}}
        <div class="mt-6 grid grid-cols-2 gap-3 sm:grid-cols-4">
            <div class="rounded-2xl border border-amber-900/10 bg-white/70 p-3.5 backdrop-blur-sm shadow-sm">
                <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-500">Hành Trình</p>
                <p class="mt-0.5 text-xl font-black text-slate-900">6 Vân Sơn</p>
                <p class="text-[11px] text-amber-800/80">HSK 1 ➔ HSK 6</p>
            </div>
            <div class="rounded-2xl border border-amber-900/10 bg-white/70 p-3.5 backdrop-blur-sm shadow-sm">
                <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-500">Vốn Từ Chuẩn</p>
                <p class="mt-0.5 text-xl font-black text-slate-900">~{{ number_format($totalCurriculumVocab) }}</p>
                <p class="text-[11px] text-slate-500">từ vựng cốt lõi</p>
            </div>
            <div class="rounded-2xl border border-amber-900/10 bg-white/70 p-3.5 backdrop-blur-sm shadow-sm">
                <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-500">Dịch Trạm Học</p>
                <p class="mt-0.5 text-xl font-black text-slate-900">{{ $totalCurriculumLessons }}</p>
                <p class="text-[11px] text-slate-500">trạm dừng chân</p>
            </div>
            <div class="rounded-2xl border border-amber-900/10 bg-white/70 p-3.5 backdrop-blur-sm shadow-sm">
                <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-500">Dấu Chân Bạn</p>
                <p class="mt-0.5 text-xl font-black text-[#991b1b]">{{ $totalCompletedLessons }}/{{ $totalCurriculumLessons }}</p>
                <p class="text-[11px] font-semibold text-emerald-700">Đã thông {{ $overallProgressPct }}%</p>
            </div>
        </div>
    </div>

    {{-- Layered Ink-wash Mountain Silhouette SVG Landscape (Zero raster images, 100% lightweight vector) --}}
    <div class="mt-8 border-t border-amber-900/10 pt-6">
        <div class="relative w-full overflow-hidden rounded-2xl bg-gradient-to-b from-amber-50/50 via-white/40 to-amber-100/30 p-2 sm:p-4">
            <svg class="w-full h-32 sm:h-44 text-slate-800" viewBox="0 0 1000 240" fill="none" preserveAspectRatio="none">
                {{-- Distant Mountains (Lớp Núi Xa - Sương Mờ) --}}
                <path d="M0 240 L0 160 Q120 70 240 150 Q360 80 480 140 Q620 50 750 130 Q880 70 1000 120 L1000 240 Z" fill="rgba(180, 83, 9, 0.05)" />
                <path d="M0 240 L0 180 Q160 110 320 170 Q480 95 640 165 Q800 110 1000 155 L1000 240 Z" fill="rgba(153, 27, 27, 0.05)" />

                {{-- Clouds Floating (Vân Khí Bay) --}}
                <ellipse cx="220" cy="110" rx="60" ry="14" fill="rgba(255, 255, 255, 0.75)" />
                <ellipse cx="580" cy="90" rx="80" ry="16" fill="rgba(255, 255, 255, 0.85)" />
                <ellipse cx="820" cy="100" rx="70" ry="15" fill="rgba(255, 255, 255, 0.8)" />

                {{-- Near Mountains (Lớp Núi Gần - Mực Đậm Dần) --}}
                <path d="M0 240 L0 195 Q140 135 280 190 Q430 140 580 195 Q740 130 890 185 Q950 170 1000 180 L1000 240 Z" fill="rgba(15, 23, 42, 0.08)" />

                {{-- Winding Journey Trail (Đường Mòn Du Sơn) --}}
                <path d="M60 215 C160 215, 180 175, 270 185 C360 195, 410 155, 500 165 C600 175, 660 135, 750 145 C840 155, 890 125, 940 135"
                      fill="none" stroke="rgba(185, 28, 28, 0.45)" stroke-width="2.5" class="hsk-trail-path" />

                {{-- 6 Vân Sơn Checkpoints (Dịch Trạm Các Đỉnh) --}}
                {{-- Peak 1 --}}
                <g transform="translate(60, 215)">
                    <circle cx="0" cy="0" r="7" fill="#16a34a" />
                    <circle cx="0" cy="0" r="12" fill="none" stroke="#16a34a" stroke-width="1.5" stroke-opacity="0.4" />
                    <text x="0" y="-14" text-anchor="middle" font-size="11" font-weight="900" fill="#166534" font-family="serif">HSK 1 · 初入</text>
                </g>
                {{-- Peak 2 --}}
                <g transform="translate(240, 180)">
                    <circle cx="0" cy="0" r="7" fill="#2563eb" />
                    <circle cx="0" cy="0" r="12" fill="none" stroke="#2563eb" stroke-width="1.5" stroke-opacity="0.4" />
                    <text x="0" y="-14" text-anchor="middle" font-size="11" font-weight="900" fill="#1e40af" font-family="serif">HSK 2 · 寻径</text>
                </g>
                {{-- Peak 3 --}}
                <g transform="translate(420, 160)">
                    <circle cx="0" cy="0" r="7" fill="#d97706" />
                    <circle cx="0" cy="0" r="12" fill="none" stroke="#d97706" stroke-width="1.5" stroke-opacity="0.4" />
                    <text x="0" y="-14" text-anchor="middle" font-size="11" font-weight="900" fill="#b45309" font-family="serif">HSK 3 · 入远</text>
                </g>
                {{-- Peak 4 --}}
                <g transform="translate(600, 150)">
                    <circle cx="0" cy="0" r="7" fill="#ea580c" />
                    <circle cx="0" cy="0" r="12" fill="none" stroke="#ea580c" stroke-width="1.5" stroke-opacity="0.4" />
                    <text x="0" y="-14" text-anchor="middle" font-size="11" font-weight="900" fill="#c2410c" font-family="serif">HSK 4 · 登高</text>
                </g>
                {{-- Peak 5 --}}
                <g transform="translate(770, 138)">
                    <circle cx="0" cy="0" r="7" fill="#9333ea" />
                    <circle cx="0" cy="0" r="12" fill="none" stroke="#9333ea" stroke-width="1.5" stroke-opacity="0.4" />
                    <text x="0" y="-14" text-anchor="middle" font-size="11" font-weight="900" fill="#7e22ce" font-family="serif">HSK 5 · 游历</text>
                </g>
                {{-- Peak 6 --}}
                <g transform="translate(940, 128)">
                    <circle cx="0" cy="0" r="7" fill="#be123c" />
                    <circle cx="0" cy="0" r="12" fill="none" stroke="#be123c" stroke-width="1.5" stroke-opacity="0.4" />
                    <text x="0" y="-14" text-anchor="middle" font-size="11" font-weight="900" fill="#9f1239" font-family="serif">HSK 6 · 观天地</text>
                </g>
            </svg>
        </div>
    </div>
</section>

{{-- The 6 Mountain Peak Cards (Lục Đại Vân Sơn) --}}
<section class="mb-12">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-2xl font-black text-slate-900 font-serif flex items-center gap-2.5">
                <span class="inline-block h-6 w-1 rounded-full bg-[#991b1b]"></span>
                Lục Đại Vân Sơn · Hành Trình Chinh Phục
            </h2>
            <p class="text-sm text-slate-600 mt-1">Chọn một cảnh giới để khởi hành hoặc tiếp tục con đường tu dưỡng ngôn ngữ.</p>
        </div>
        <div class="flex items-center gap-2 text-xs font-serif text-slate-500">
            <span class="inline-flex items-center gap-1"><span class="h-2.5 w-2.5 rounded-full bg-emerald-500"></span> Đã hoàn thành</span>
            <span class="inline-flex items-center gap-1"><span class="h-2.5 w-2.5 rounded-full bg-amber-500"></span> Đang học</span>
            <span class="inline-flex items-center gap-1"><span class="h-2.5 w-2.5 rounded-full bg-slate-300"></span> Chưa bắt đầu</span>
        </div>
    </div>

    <div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-3">
        @foreach($levelData as $level => $data)
        @php
            $isDone = $data['completed_count'] > 0 && $data['progress_pct'] >= 100;
            $isInProgress = !$isDone && $data['progress_pct'] > 0;
            $hasContent = $data['flashcard_count'] > 0 || $data['lesson_count'] > 0;
            $levelOrdinal = ['Đệ Nhất Phong', 'Đệ Nhị Phong', 'Đệ Tam Phong', 'Đệ Tứ Phong', 'Đệ Ngũ Phong', 'Đệ Lục Phong'][$level - 1];
            $chineseDigit = ['一','二','三','四','五','六'][$level - 1];
        @endphp
        <div class="group relative flex flex-col justify-between overflow-hidden rounded-[2rem] border bg-white/90 p-6 sm:p-7 shadow-lg shadow-amber-950/5 transition-all duration-300 hover:-translate-y-1.5 hover:shadow-2xl hover:shadow-amber-900/10 {{ $isDone ? 'border-emerald-300/80 ring-1 ring-emerald-400/20' : ($isInProgress ? 'border-amber-300/80 ring-2 ring-amber-400/20' : 'border-amber-900/15') }}">

            {{-- Top Accent Ribbon --}}
            <div class="absolute inset-x-0 top-0 h-1.5" style="background: {{ $data['color'] }}"></div>

            {{-- Giant Background Calligraphic Watermark Character --}}
            <div class="pointer-events-none absolute -right-2 -top-4 font-chinese text-8xl font-black leading-none opacity-5 transition-opacity duration-300 group-hover:opacity-10 select-none"
                 style="color: {{ $data['color'] }}">
                {{ $chineseDigit }}
            </div>

            <div>
                {{-- Header row: Level label & Milestone Status Stamp --}}
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-center gap-2">
                        <span class="rounded-full px-3 py-1 text-xs font-black uppercase tracking-wider {{ $data['badge'] }}">
                            {{ $data['label'] }}
                        </span>
                        <span class="text-[11px] font-bold text-slate-400 font-serif">{{ $levelOrdinal }}</span>
                    </div>

                    {{-- Visual Journey State --}}
                    @if($isDone)
                        {{-- Chu Sa Triện: Đã Thông Núi Này --}}
                        <div class="seal-yitong-mini font-chinese" title="Đã hoàn thành toàn bộ bài học của cấp độ này!">
                            已通此山
                        </div>
                    @elseif($isInProgress)
                        {{-- Đèn Lồng Đang Học --}}
                        <div class="hsk-lantern-anim flex items-center gap-1.5 rounded-full border border-amber-300 bg-amber-50 px-2.5 py-0.5 text-xs font-bold text-amber-800 shadow-sm" title="Đang học dở">
                            <span class="text-sm">🏮</span>
                            <span>{{ $data['progress_pct'] }}%</span>
                        </div>
                    @else
                        {{-- Sương Mờ Chưa Bắt Đầu --}}
                        <div class="flex items-center gap-1 rounded-full border border-slate-200 bg-slate-50 px-2.5 py-0.5 text-xs font-semibold text-slate-400" title="Chưa bắt đầu">
                            <span>🌫️</span>
                            <span>Chờ bước chân</span>
                        </div>
                    @endif
                </div>

                {{-- Mountain Title & Poetic Narrative --}}
                <div class="mt-4">
                    <div class="flex items-baseline gap-2">
                        <h3 class="text-2xl font-black text-slate-900 font-chinese tracking-tight">
                            {{ $data['mountain_cn'] }}
                        </h3>
                        <span class="text-sm font-semibold text-slate-600 font-serif">· {{ $data['mountain_vi'] }}</span>
                    </div>

                    <p class="mt-1 text-xs font-medium text-amber-800/80">
                        {{ $data['mountain_sub'] }}
                    </p>

                    {{-- Poetic Couplet --}}
                    <div class="mt-2.5 border-l-2 border-amber-500/40 pl-2.5 py-0.5">
                        <p class="text-xs italic text-slate-600 font-chinese">
                            “{{ $data['mountain_quote'] }}”
                        </p>
                    </div>

                    <p class="mt-2.5 text-xs text-slate-600 leading-relaxed">
                        {{ $data['description'] }}
                    </p>
                </div>

                {{-- Stats Grid --}}
                <div class="mt-5 grid grid-cols-3 gap-2 text-center text-xs">
                    <div class="rounded-xl border border-slate-100 bg-slate-50/70 p-2">
                        <p class="font-black text-slate-900">~{{ number_format($data['vocab_count']) }}</p>
                        <p class="text-[11px] text-slate-500">từ vựng</p>
                    </div>
                    <div class="rounded-xl border border-slate-100 bg-slate-50/70 p-2">
                        <p class="font-black text-slate-900">{{ $data['lesson_count'] }}</p>
                        <p class="text-[11px] text-slate-500">dịch trạm</p>
                    </div>
                    <div class="rounded-xl border border-slate-100 bg-slate-50/70 p-2">
                        <p class="font-black text-slate-900">{{ $data['flashcard_count'] }}</p>
                        <p class="text-[11px] text-slate-500">thư thiêm</p>
                    </div>
                </div>

                {{-- Progress Bar (Mặc Tuyến Hành Trình) --}}
                @if($data['lesson_count'] > 0)
                <div class="mt-4">
                    <div class="flex items-center justify-between mb-1.5 text-xs font-semibold text-slate-600">
                        <span class="font-serif">Tiến trình thông sơn:</span>
                        <span>{{ $data['completed_count'] }}/{{ $data['lesson_count'] }} trạm</span>
                    </div>
                    <div class="h-2 rounded-full bg-slate-100 overflow-hidden relative">
                        <div class="h-2 rounded-full transition-all duration-700"
                            style="width: {{ $data['progress_pct'] }}%; background: {{ $data['color'] }}"></div>
                    </div>
                </div>
                @else
                <p class="mt-4 text-xs text-slate-400 italic">Dịch trạm đang được mở rộng...</p>
                @endif
            </div>

            {{-- Action Link --}}
            <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between">
                <a href="{{ route('hsk.show', $level) }}"
                   class="inline-flex w-full items-center justify-center gap-2 rounded-xl py-2.5 text-sm font-bold text-white shadow-sm transition hover:opacity-95 active:scale-95"
                   style="background: {{ $data['color'] }}">
                    <span>{{ $isDone ? 'Vào ngắm lại giang sơn' : ($isInProgress ? 'Tiếp tục leo núi' : 'Nhập sơn học đạo') }}</span>
                    <i data-lucide="arrow-right" class="h-4 w-4 transition-transform group-hover:translate-x-1"></i>
                </a>
            </div>
        </div>
        @endforeach
    </div>
</section>

{{-- HSK Mock Exam Callout Banner (Khoa Cử Trường Thi) --}}
<section class="mb-10 rounded-[2.5rem] bg-gradient-to-r from-[#991b1b] via-[#7f1d1d] to-slate-950 p-8 sm:p-10 text-white shadow-2xl shadow-red-950/20 relative overflow-hidden">
    <div class="pointer-events-none absolute -right-6 -bottom-10 opacity-10 font-chinese text-[12rem] font-black select-none text-white leading-none">
        科举
    </div>
    <div class="relative z-10 grid gap-6 lg:grid-cols-[1fr_auto] lg:items-center">
        <div>
            <div class="inline-flex items-center gap-2 rounded-full bg-amber-400/20 px-3.5 py-1 text-xs font-black uppercase tracking-widest text-amber-300 border border-amber-300/30">
                <i data-lucide="award" class="h-4 w-4"></i>
                <span>Trường Thi Trực Tuyến · Khoa Cử Kim Bảng</span>
            </div>
            <h2 class="mt-3 text-3xl font-black tracking-tight sm:text-4xl font-serif">
                Thi thử HSK mô phỏng có cấp chứng chỉ điện tử
            </h2>
            <p class="mt-3 max-w-2xl text-white/80 leading-relaxed text-sm sm:text-base">
                Sau khi vượt qua các dịch trạm trên đường du sơn, hãy bước vào phòng thi chuẩn mực với <strong>30 - 50 câu hỏi</strong>, đồng hồ đếm ngược, phân tích chi tiết 3 kỹ năng <strong>Nghe · Đọc hiểu · Ngữ pháp</strong> và nhận <strong>Chứng chỉ Online</strong> ngay khi đạt điểm đỗ!
            </p>
        </div>
        <a href="{{ route('hsk.mock.index') }}"
            class="shrink-0 inline-flex items-center gap-2.5 justify-center rounded-2xl bg-amber-300 hover:bg-amber-200 px-7 py-4 text-sm font-black text-slate-950 shadow-xl shadow-amber-300/20 transition hover:-translate-y-0.5 active:scale-95">
            <i data-lucide="play" class="h-4 w-4 fill-current"></i>
            <span>Vào trường thi HSK ngay</span>
        </a>
    </div>
</section>

{{-- Info banner: Triết lý học HSK --}}
<section class="rounded-[2rem] bg-slate-950 p-8 text-white shadow-2xl shadow-slate-950/20 relative overflow-hidden">
    <div class="pointer-events-none absolute -right-4 -bottom-6 opacity-10 font-chinese text-[9rem] font-black select-none text-white leading-none">
        问道
    </div>
    <div class="relative z-10 grid gap-6 lg:grid-cols-[1fr_auto] lg:items-center">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.28em] text-amber-300/80 font-serif">Tâm Pháp Học Đạo</p>
            <h2 class="mt-2 text-2xl sm:text-3xl font-black tracking-tight font-serif">Lộ trình rõ ràng, đích đến vững vàng</h2>
            <p class="mt-3 max-w-2xl text-white/70 leading-relaxed text-sm sm:text-base">
                Thay vì học lan man, hệ thống Giang Sơn Đồ giúp bạn xác định rõ từng trạm dừng chân, biết chính xác lượng từ vựng cần tích lũy để chinh phục các bậc thang quốc tế. Hãy kiên trì mỗi ngày một dịch trạm nhỏ.
            </p>
        </div>
        <a href="{{ route('flashcards') }}"
            class="shrink-0 inline-flex items-center gap-2 justify-center rounded-full bg-slate-800 hover:bg-slate-700 px-6 py-3 text-sm font-bold text-white transition hover:-translate-y-0.5 border border-slate-700">
            <span>Rèn luyện với Thư Thiêm</span>
            <i data-lucide="arrow-right" class="h-4 w-4"></i>
        </a>
    </div>
</section>

@endsection