@extends('layouts.app')

@section('title', 'Khám phá 214 Bộ thủ tiếng Trung | Learn Chinese')

@section('content')
<div class="space-y-8 pb-16">

    {{-- ══ 1. HERO HEADER ══ --}}
    <div class="relative overflow-hidden rounded-[2.5rem] border border-amber-900/10 bg-gradient-to-br from-slate-950 via-slate-900 to-red-950 p-6 sm:p-10 text-white shadow-2xl">
        {{-- Glow effects --}}
        <div class="absolute -left-12 -top-12 h-48 w-48 rounded-full bg-red-600/20 blur-3xl pointer-events-none"></div>
        <div class="absolute right-0 bottom-0 h-64 w-64 rounded-full bg-amber-500/15 blur-3xl pointer-events-none"></div>

        <div class="relative max-w-3xl mx-auto text-center space-y-4">
            <div class="inline-flex items-center gap-2 rounded-full border border-amber-400/30 bg-amber-400/10 px-4 py-1.5 text-xs font-semibold text-amber-300">
                <i data-lucide="layers" class="h-3.5 w-3.5"></i>
                <span>Nền tảng Hán tự chuẩn Khang Hy</span>
            </div>

            <h1 class="text-3xl sm:text-4xl md:text-5xl font-black tracking-tight text-white">
                Khám phá <span class="bg-gradient-to-r from-amber-200 via-amber-300 to-red-300 bg-clip-text text-transparent">Bộ thủ Tiếng Trung</span>
            </h1>

            <p class="text-sm sm:text-base text-slate-300 max-w-2xl mx-auto leading-relaxed">
                Bộ thủ là hệ thống phân loại chữ Hán truyền thống và là manh mối gợi ý về nghĩa trong nhiều chữ. Hiểu rõ bộ thủ giúp người học có thêm điểm tựa liên tưởng để nhận diện và ghi nhớ từ vựng dễ dàng hơn.
            </p>

            {{-- Stat pills --}}
            <div class="flex flex-wrap items-center justify-center gap-2 pt-2 text-xs">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-white/10 px-3.5 py-1.5 backdrop-blur text-slate-200">
                    <i data-lucide="book-open" class="h-3.5 w-3.5 text-amber-400"></i>
                    <strong>214</strong> Bộ thủ Khang Hy
                </span>
                <span class="inline-flex items-center gap-1.5 rounded-full bg-white/10 px-3.5 py-1.5 backdrop-blur text-slate-200">
                    <i data-lucide="star" class="h-3.5 w-3.5 text-amber-400 fill-amber-400"></i>
                    <strong>{{ $commonCount }}</strong> Bộ thủ cốt lõi
                </span>
                <span class="inline-flex items-center gap-1.5 rounded-full bg-white/10 px-3.5 py-1.5 backdrop-blur text-slate-200">
                    <i data-lucide="layout-grid" class="h-3.5 w-3.5 text-emerald-400"></i>
                    Phân loại từ 1 đến 17 nét
                </span>
            </div>
        </div>
    </div>

    {{-- ══ 2. SEARCH & FILTER TOOLBAR ══ --}}
    <div class="rounded-3xl border border-slate-200 bg-white/80 p-5 sm:p-6 shadow-sm backdrop-blur space-y-5">
        
        {{-- Row 1: Search Form + Common Toggle --}}
        <div class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4">
            
            {{-- Search Bar --}}
            <form action="{{ route('radicals.index') }}" method="GET" class="relative flex-1">
                @if($selectedStrokes) <input type="hidden" name="strokes" value="{{ $selectedStrokes }}"> @endif
                @if($onlyCommon) <input type="hidden" name="common" value="1"> @endif
                @if($selectedPosition) <input type="hidden" name="position" value="{{ $selectedPosition }}"> @endif
                @if($perPage && $perPage !== 36) <input type="hidden" name="per_page" value="{{ $perPage }}"> @endif

                <input type="text" 
                       name="q" 
                       value="{{ $search }}"
                       placeholder="Tìm theo chữ (亻), Hán Việt (Nhân), Pinyin (ren), ý nghĩa (người)..."
                       class="w-full rounded-2xl border border-slate-200 bg-slate-50/80 py-3 pl-11 pr-10 text-sm text-slate-800 placeholder-slate-400 outline-none transition focus:border-[#991b1b] focus:bg-white focus:ring-2 focus:ring-red-100">
                <i data-lucide="search" class="absolute left-4 top-3.5 h-4 w-4 text-slate-400"></i>
                @if($search)
                    <a href="{{ route('radicals.index', array_filter(['strokes' => $selectedStrokes, 'common' => $onlyCommon ? 1 : null, 'position' => $selectedPosition])) }}" 
                       class="absolute right-3.5 top-3.5 text-slate-400 hover:text-slate-600 transition"
                       title="Xóa tìm kiếm">
                        <i data-lucide="x" class="h-4 w-4"></i>
                    </a>
                @endif
            </form>

            {{-- Mode Switcher (Common vs All) --}}
            <div class="flex items-center gap-1.5 rounded-2xl bg-slate-100 p-1.5 self-start md:self-auto shrink-0">
                <a href="{{ route('radicals.index', array_filter(['q' => $search, 'strokes' => $selectedStrokes, 'position' => $selectedPosition, 'common' => 1])) }}"
                   class="inline-flex items-center gap-1.5 rounded-xl px-4 py-2 text-xs font-bold transition {{ $onlyCommon ? 'bg-[#991b1b] text-white shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60' }}">
                    <i data-lucide="star" class="h-3.5 w-3.5 {{ $onlyCommon ? 'fill-current' : '' }}"></i>
                    100 Bộ thủ thông dụng
                </a>
                <a href="{{ route('radicals.index', array_filter(['q' => $search, 'strokes' => $selectedStrokes, 'position' => $selectedPosition])) }}"
                   class="inline-flex items-center gap-1.5 rounded-xl px-4 py-2 text-xs font-bold transition {{ !$onlyCommon ? 'bg-[#991b1b] text-white shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60' }}">
                    <i data-lucide="grid" class="h-3.5 w-3.5"></i>
                    Tất cả 214 bộ thủ
                </a>
            </div>
        </div>

        {{-- Row 2: Stroke Count Filter Pills (1..17) --}}
        <div class="space-y-2 pt-2 border-t border-slate-100">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
                    <i data-lucide="pencil" class="h-3.5 w-3.5"></i>
                    Lọc theo số nét
                </span>
                @if($selectedStrokes)
                    <a href="{{ route('radicals.index', array_filter(['q' => $search, 'common' => $onlyCommon ? 1 : null, 'position' => $selectedPosition])) }}" 
                       class="text-xs font-semibold text-[#991b1b] hover:underline">
                        Xóa lọc nét
                    </a>
                @endif
            </div>

            <div class="flex flex-wrap items-center gap-1.5">
                <a href="{{ route('radicals.index', array_filter(['q' => $search, 'common' => $onlyCommon ? 1 : null, 'position' => $selectedPosition])) }}"
                   class="rounded-xl px-3 py-1.5 text-xs font-semibold transition {{ $selectedStrokes === null ? 'bg-slate-900 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    Tất cả nét
                </a>
                @for ($s = 1; $s <= 17; $s++)
                    @php $cnt = $strokeDistribution[$s] ?? 0; @endphp
                    @if($cnt > 0)
                        <a href="{{ route('radicals.index', array_filter(['strokes' => $s, 'q' => $search, 'common' => $onlyCommon ? 1 : null, 'position' => $selectedPosition])) }}"
                           class="inline-flex items-center gap-1 rounded-xl px-2.5 py-1.5 text-xs font-semibold transition {{ $selectedStrokes === $s ? 'bg-[#991b1b] text-white shadow-sm' : 'bg-slate-100 text-slate-700 hover:bg-amber-100/60 hover:text-amber-900' }}">
                            <span>{{ $s }} nét</span>
                            <span class="text-[10px] opacity-70">({{ $cnt }})</span>
                        </a>
                    @endif
                @endfor
            </div>
        </div>

        {{-- Row 3: Position Filter Pills --}}
        <div class="flex flex-wrap items-center gap-2 pt-2 border-t border-slate-100 text-xs">
            <span class="font-medium text-slate-400 flex items-center gap-1">
                <i data-lucide="compass" class="h-3.5 w-3.5"></i>
                Vị trí:
            </span>
            <a href="{{ route('radicals.index', array_filter(['q' => $search, 'strokes' => $selectedStrokes, 'common' => $onlyCommon ? 1 : null])) }}"
               class="rounded-lg px-2.5 py-1 font-semibold transition {{ empty($selectedPosition) ? 'bg-slate-800 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Tất cả
            </a>
            @foreach ($positionOptions as $posKey => $posLabel)
                <a href="{{ route('radicals.index', array_filter(['position' => $posKey, 'q' => $search, 'strokes' => $selectedStrokes, 'common' => $onlyCommon ? 1 : null])) }}"
                   class="rounded-lg px-2.5 py-1 font-semibold transition {{ $selectedPosition === $posKey ? 'bg-[#991b1b] text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    {{ $posLabel }}
                </a>
            @endforeach
        </div>

        {{-- Active Filters Summary --}}
        @if($search || $selectedStrokes || $onlyCommon || $selectedPosition)
        <div class="flex flex-wrap items-center justify-between gap-2 pt-2 text-xs border-t border-slate-100">
            <div class="flex flex-wrap items-center gap-1.5 text-slate-500">
                <span>Đang hiển thị:</span>
                <strong class="text-slate-800">{{ $radicals->total() }} bộ thủ</strong>
                @if($onlyCommon) <span class="rounded-full bg-amber-100 px-2 py-0.5 text-amber-800 font-medium">Top 100 thông dụng</span> @endif
                @if($selectedStrokes) <span class="rounded-full bg-red-100 px-2 py-0.5 text-red-800 font-medium">{{ $selectedStrokes }} nét</span> @endif
                @if($selectedPosition) <span class="rounded-full bg-blue-100 px-2 py-0.5 text-blue-800 font-medium">{{ $positionOptions[$selectedPosition] ?? $selectedPosition }}</span> @endif
                @if($search) <span class="rounded-full bg-slate-100 px-2 py-0.5 text-slate-700 font-medium">"{{ $search }}"</span> @endif
            </div>
            <a href="{{ route('radicals.index') }}" class="text-[#991b1b] hover:underline font-semibold">
                Xóa tất cả bộ lọc
            </a>
        </div>
        @endif
    </div>

    {{-- ══ 3. RADICAL CARDS GRID ══ --}}
    @if($radicals->isEmpty())
        <div class="flex flex-col items-center justify-center rounded-[2.5rem] border border-dashed border-slate-300 bg-white/60 py-16 px-6 text-center shadow-sm backdrop-blur">
            <div class="grid h-16 w-16 place-items-center rounded-3xl bg-red-50 text-[#991b1b]">
                <i data-lucide="search-x" class="h-8 w-8"></i>
            </div>
            <h3 class="mt-4 text-xl font-bold text-slate-800">Không tìm thấy bộ thủ phù hợp</h3>
            <p class="mt-2 max-w-md text-sm text-slate-500">
                Không có bộ thủ nào khớp với điều kiện tìm kiếm. Hãy thử tìm từ khóa khác hoặc bỏ các bộ lọc nét/vị trí.
            </p>
            <div class="mt-6">
                <a href="{{ route('radicals.index') }}" class="inline-flex items-center gap-2 rounded-full bg-[#991b1b] px-5 py-2.5 text-sm font-semibold text-white shadow-md transition hover:bg-red-800">
                    <i data-lucide="rotate-ccw" class="h-4 w-4"></i>
                    Xem tất cả 214 bộ thủ
                </a>
            </div>
        </div>
    @else
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-3.5 sm:gap-4">
            @foreach ($radicals as $radical)
                <a href="{{ route('radicals.show', $radical->slug) }}"
                   class="group relative flex flex-col justify-between rounded-2xl border border-slate-200/90 bg-white p-4 text-left shadow-xs transition hover:-translate-y-1 hover:border-amber-300 hover:shadow-lg hover:shadow-amber-950/5">
                    
                    {{-- Top bar: Number & badges --}}
                    <div class="flex items-center justify-between text-[11px] text-slate-400">
                        <span class="font-mono font-bold text-slate-400">#{{ $radical->radical_number }}</span>
                        <div class="flex items-center gap-1">
                            @if($radical->is_common)
                                <span title="Bộ thủ cốt lõi thông dụng" class="text-amber-500">
                                    <i data-lucide="star" class="h-3 w-3 fill-amber-400"></i>
                                </span>
                            @endif
                            <span class="rounded-md bg-slate-100 px-1.5 py-0.5 text-[10px] font-semibold text-slate-600">
                                {{ $radical->stroke_count }} nét
                            </span>
                        </div>
                    </div>

                    {{-- Center: Big Character --}}
                    <div class="my-3 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <span class="text-4xl font-black text-slate-800 transition group-hover:scale-110 group-hover:text-[#991b1b]">
                                {{ $radical->display_character }}
                            </span>
                            @if($radical->character !== $radical->display_character)
                                <span class="text-xl font-bold text-slate-400" title="Chữ bộ thủ gốc">
                                    ({{ $radical->character }})
                                </span>
                            @endif
                        </div>
                        <p class="mt-1.5 text-sm font-bold text-slate-900 group-hover:text-[#991b1b] transition">
                            {{ $radical->name_vi }}
                        </p>
                        <p class="text-xs font-semibold text-red-600 font-mono">
                            {{ $radical->pinyin }}
                        </p>
                    </div>

                    {{-- Bottom: Meaning & HSK count badge --}}
                    <div class="border-t border-slate-100 pt-2.5 space-y-1 text-left">
                        <p class="text-xs text-slate-600 line-clamp-1 font-medium" title="{{ $radical->meaning_vi }}">
                            {{ $radical->meaning_vi }}
                        </p>
                        <div class="flex items-center justify-between text-[10px] text-slate-400 pt-0.5">
                            <span class="truncate">{{ $radical->position_badge_label }}</span>
                            @if($radical->characters_count > 0)
                                <span class="rounded-full bg-red-50 px-2 py-0.5 font-bold text-red-600 border border-red-100 shrink-0">
                                    {{ $radical->characters_count }} chữ HSK
                                </span>
                            @endif
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        {{-- ══ 4. PAGINATION & PER-PAGE SELECTOR ══ --}}
        @if($radicals->hasPages() || $radicals->total() > 24)
        <div class="pt-6 flex flex-col sm:flex-row items-center justify-between gap-4 border-t border-slate-200/80">
            
            {{-- Page info & Per-page switcher --}}
            <div class="flex flex-wrap items-center gap-3 text-xs text-slate-500 order-2 sm:order-1">
                <span>
                    Hiển thị <strong class="text-slate-800">{{ $radicals->firstItem() ?? 0 }}</strong> – <strong class="text-slate-800">{{ $radicals->lastItem() ?? 0 }}</strong> trên tổng số <strong class="text-slate-800">{{ $radicals->total() }}</strong> bộ thủ
                </span>
                
                <span class="text-slate-300 hidden sm:inline">|</span>
                
                <div class="flex items-center gap-1.5">
                    <span>Số lượng:</span>
                    <select onchange="window.location.href=this.value" 
                            class="rounded-xl border border-slate-200 bg-white py-1 pl-2.5 pr-7 text-xs font-semibold text-slate-700 shadow-2xs transition hover:border-slate-300 focus:border-[#991b1b] focus:ring-1 focus:ring-red-100 cursor-pointer">
                        <option value="{{ request()->fullUrlWithQuery(['per_page' => 24, 'page' => 1]) }}" {{ $perPage == 24 ? 'selected' : '' }}>24 / trang</option>
                        <option value="{{ request()->fullUrlWithQuery(['per_page' => 36, 'page' => 1]) }}" {{ $perPage == 36 ? 'selected' : '' }}>36 / trang</option>
                        <option value="{{ request()->fullUrlWithQuery(['per_page' => 48, 'page' => 1]) }}" {{ $perPage == 48 ? 'selected' : '' }}>48 / trang</option>
                        <option value="{{ request()->fullUrlWithQuery(['per_page' => 'all', 'page' => 1]) }}" {{ $perPage === 'all' ? 'selected' : '' }}>Tất cả (214)</option>
                    </select>
                </div>
            </div>

            {{-- Pagination Buttons --}}
            @if($radicals->hasPages())
            <div class="flex items-center gap-1.5 order-1 sm:order-2">
                {{-- Prev --}}
                @if($radicals->onFirstPage())
                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-slate-200 bg-slate-50 text-slate-300 cursor-not-allowed">
                        <i data-lucide="chevron-left" class="h-4 w-4"></i>
                    </span>
                @else
                    <a href="{{ $radicals->previousPageUrl() }}"
                       class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-700 shadow-2xs transition hover:border-[#991b1b] hover:text-[#991b1b]"
                       title="Trang trước">
                        <i data-lucide="chevron-left" class="h-4 w-4"></i>
                    </a>
                @endif

                {{-- First page button if far --}}
                @if($radicals->currentPage() > 3)
                    <a href="{{ $radicals->url(1) }}"
                       class="inline-flex h-9 min-w-[36px] px-2.5 items-center justify-center rounded-xl border border-slate-200 bg-white text-xs font-bold text-slate-700 transition hover:border-[#991b1b] hover:text-[#991b1b]">
                        1
                    </a>
                    @if($radicals->currentPage() > 4)
                        <span class="px-1 text-slate-400 text-xs">...</span>
                    @endif
                @endif

                {{-- Middle pages window --}}
                @foreach($radicals->getUrlRange(max(1, $radicals->currentPage() - 2), min($radicals->lastPage(), $radicals->currentPage() + 2)) as $page => $url)
                    <a href="{{ $url }}"
                       class="inline-flex h-9 min-w-[36px] px-2.5 items-center justify-center rounded-xl text-xs font-bold transition
                              {{ $page == $radicals->currentPage()
                                  ? 'bg-[#991b1b] text-white shadow-md shadow-red-950/15'
                                  : 'border border-slate-200 bg-white text-slate-700 hover:border-[#991b1b] hover:text-[#991b1b]' }}">
                        {{ $page }}
                    </a>
                @endforeach

                {{-- Last page button if far --}}
                @if($radicals->currentPage() < $radicals->lastPage() - 2)
                    @if($radicals->currentPage() < $radicals->lastPage() - 3)
                        <span class="px-1 text-slate-400 text-xs">...</span>
                    @endif
                    <a href="{{ $radicals->url($radicals->lastPage()) }}"
                       class="inline-flex h-9 min-w-[36px] px-2.5 items-center justify-center rounded-xl border border-slate-200 bg-white text-xs font-bold text-slate-700 transition hover:border-[#991b1b] hover:text-[#991b1b]">
                        {{ $radicals->lastPage() }}
                    </a>
                @endif

                {{-- Next --}}
                @if($radicals->hasMorePages())
                    <a href="{{ $radicals->nextPageUrl() }}"
                       class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-700 shadow-2xs transition hover:border-[#991b1b] hover:text-[#991b1b]"
                       title="Trang tiếp theo">
                        <i data-lucide="chevron-right" class="h-4 w-4"></i>
                    </a>
                @else
                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-slate-200 bg-slate-50 text-slate-300 cursor-not-allowed">
                        <i data-lucide="chevron-right" class="h-4 w-4"></i>
                    </span>
                @endif
            </div>
            @endif

        </div>
        @endif
    @endif

</div>
@endsection
