@extends('layouts.app')

@section('title', 'Bảng Phát Âm Pinyin Chuẩn Quốc Tế | Learn Chinese')

@section('content')
<div class="space-y-8 pb-16" x-data="pinyinApp()">

    {{-- ══ 1. HERO BANNER ══ --}}
    <div class="relative overflow-hidden rounded-[2.5rem] border border-red-950/20 bg-gradient-to-br from-slate-950 via-slate-900 to-red-950 p-6 sm:p-10 text-white shadow-2xl">
        {{-- Ambient Glows --}}
        <div class="absolute -left-16 -top-16 h-56 w-56 rounded-full bg-red-600/20 blur-3xl pointer-events-none"></div>
        <div class="absolute right-0 bottom-0 h-64 w-64 rounded-full bg-amber-500/15 blur-3xl pointer-events-none"></div>

        <div class="relative max-w-4xl mx-auto text-center space-y-4">
            <div class="inline-flex items-center gap-2 rounded-full border border-rose-400/30 bg-rose-400/10 px-4 py-1.5 text-xs font-semibold text-rose-300">
                <i data-lucide="mic" class="h-3.5 w-3.5"></i>
                <span>Hệ Thống Ngữ Âm Chuẩn Quốc Tế (Hanyu Pinyin)</span>
            </div>

            <h1 class="text-3xl sm:text-4xl md:text-5xl font-black tracking-tight text-white">
                Bảng Phát Âm &amp; <span class="bg-gradient-to-r from-rose-200 via-amber-200 to-red-300 bg-clip-text text-transparent">Luyện Âm Pinyin</span>
            </h1>

            <p class="text-sm sm:text-base text-slate-300 max-w-2xl mx-auto leading-relaxed">
                Nền tảng phát âm bài bản dành riêng cho người Việt: 23 Thanh mẫu, 36 Vận mẫu, 4 Thanh điệu và Ma trận 400 âm tiết chuẩn Bắc Kinh kèm quy tắc biến điệu thực tế.
            </p>

            {{-- Stat badges & Audio Speed --}}
            <div class="flex flex-wrap items-center justify-center gap-2.5 pt-2 text-xs">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-white/10 px-3.5 py-1.5 backdrop-blur text-slate-200">
                    <i data-lucide="sparkles" class="h-3.5 w-3.5 text-rose-400"></i>
                    <strong>23</strong> Thanh mẫu (Initials)
                </span>
                <span class="inline-flex items-center gap-1.5 rounded-full bg-white/10 px-3.5 py-1.5 backdrop-blur text-slate-200">
                    <i data-lucide="layers" class="h-3.5 w-3.5 text-amber-400"></i>
                    <strong>36</strong> Vận mẫu (Finals)
                </span>
                <span class="inline-flex items-center gap-1.5 rounded-full bg-white/10 px-3.5 py-1.5 backdrop-blur text-slate-200">
                    <i data-lucide="activity" class="h-3.5 w-3.5 text-emerald-400"></i>
                    <strong>4</strong> Thanh điệu + Khinh thanh
                </span>
                <span class="inline-flex items-center gap-1.5 rounded-full bg-white/10 px-3.5 py-1.5 backdrop-blur text-slate-200">
                    <i data-lucide="grid" class="h-3.5 w-3.5 text-cyan-400"></i>
                    <strong>{{ $matrix['total_syllables'] }}</strong> Âm tiết thực tế
                </span>

                {{-- Speed Selector --}}
                <div class="inline-flex items-center gap-1.5 rounded-full bg-slate-800/80 border border-slate-700 px-3 py-1 text-slate-300 ml-2">
                    <i data-lucide="gauge" class="h-3.5 w-3.5 text-rose-400"></i>
                    <span class="font-medium">Tốc độ:</span>
                    <button type="button" @click="setSpeed(1.0)" :class="speed === 1.0 ? 'bg-rose-600 text-white font-bold' : 'hover:text-white'" class="rounded px-1.5 py-0.5 transition">1.0x</button>
                    <button type="button" @click="setSpeed(0.75)" :class="speed === 0.75 ? 'bg-rose-600 text-white font-bold' : 'hover:text-white'" class="rounded px-1.5 py-0.5 transition">0.75x</button>
                    <button type="button" @click="setSpeed(0.5)" :class="speed === 0.5 ? 'bg-rose-600 text-white font-bold' : 'hover:text-white'" class="rounded px-1.5 py-0.5 transition">0.5x</button>
                </div>
            </div>
        </div>
    </div>

    {{-- ══ 2. NAVIGATION TABS ══ --}}
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4 border-b border-slate-200 pb-2">
        <div class="flex flex-wrap items-center gap-2">
            <button type="button" 
                    @click="activeTab = 'matrix'"
                    :class="activeTab === 'matrix' ? 'bg-[#991b1b] text-white shadow-md shadow-red-950/20' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200'"
                    class="inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-sm font-semibold transition">
                <i data-lucide="grid" class="h-4 w-4"></i>
                <span>Ma Trận Pinyin</span>
            </button>

            <button type="button" 
                    @click="activeTab = 'initials'"
                    :class="activeTab === 'initials' ? 'bg-[#991b1b] text-white shadow-md shadow-red-950/20' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200'"
                    class="inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-sm font-semibold transition">
                <i data-lucide="sparkles" class="h-4 w-4"></i>
                <span>23 Thanh Mẫu</span>
            </button>

            <button type="button" 
                    @click="activeTab = 'finals'"
                    :class="activeTab === 'finals' ? 'bg-[#991b1b] text-white shadow-md shadow-red-950/20' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200'"
                    class="inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-sm font-semibold transition">
                <i data-lucide="layers" class="h-4 w-4"></i>
                <span>36 Vận Mẫu</span>
            </button>

            <button type="button" 
                    @click="activeTab = 'tones'"
                    :class="activeTab === 'tones' ? 'bg-[#991b1b] text-white shadow-md shadow-red-950/20' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200'"
                    class="inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-sm font-semibold transition">
                <i data-lucide="activity" class="h-4 w-4"></i>
                <span>Thanh Điệu &amp; Biến Điệu</span>
            </button>
        </div>

        {{-- Global Quick Search --}}
        <div class="relative w-full sm:w-64">
            <i data-lucide="search" class="absolute left-3.5 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400"></i>
            <input type="text"
                   x-model="searchQuery"
                   @input.debounce.250ms="performSearch()"
                   placeholder="Tìm âm (vd: ma, ni, 好)..."
                   class="w-full rounded-xl border border-slate-300 bg-white pl-9 pr-8 py-2 text-sm text-slate-800 focus:border-[#991b1b] focus:outline-none focus:ring-2 focus:ring-[#991b1b]/20">
            <button x-show="searchQuery" @click="searchQuery = ''; searchResults = []" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="h-4 w-4"></i>
            </button>
        </div>
    </div>

    {{-- Search Results Dropdown/Box (if query active) --}}
    <div x-show="searchQuery.trim().length > 0" class="rounded-2xl border border-slate-200 bg-white p-4 shadow-lg space-y-3">
        <div class="flex items-center justify-between text-xs text-slate-500 font-medium">
            <span>Kết quả tìm kiếm cho "<strong x-text="searchQuery"></strong>" (<span x-text="searchResults.length"></span> kết quả)</span>
            <button @click="searchQuery = ''; searchResults = []" class="text-[#991b1b] hover:underline">Đóng tìm kiếm</button>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-2">
            <template x-for="item in searchResults" :key="item.syllable">
                <button type="button" 
                        @click="openModal(item.syllable)"
                        class="flex flex-col items-center justify-center p-2.5 rounded-xl border border-slate-200 bg-slate-50 hover:bg-red-50 hover:border-red-300 hover:shadow transition">
                    <span class="text-base font-bold text-slate-900" x-text="item.syllable"></span>
                    <span class="text-xs text-slate-500 truncate max-w-[100px]" x-text="item.tones[1] ? item.tones[1].pinyin + ' ' + item.tones[1].char : ''"></span>
                </button>
            </template>
        </div>
        <div x-show="searchResults.length === 0" class="py-4 text-center text-sm text-slate-400">
            Không tìm thấy âm tiết nào khớp với từ khóa.
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════ --}}
    {{-- TAB 1: MA TRẬN PINYIN THÔNG MINH (~400 Âm Hợp Lệ)  --}}
    {{-- ══════════════════════════════════════════════════ --}}
    <div x-show="activeTab === 'matrix'" class="space-y-4">
        
        {{-- Matrix Guidance Banner --}}
        <div class="rounded-2xl border border-amber-200 bg-amber-50/70 p-4 text-sm text-amber-900 flex items-start gap-3">
            <i data-lucide="info" class="h-5 w-5 text-amber-700 shrink-0 mt-0.5"></i>
            <div class="space-y-1">
                <p class="font-semibold text-amber-950">Ma trận âm tiết tiếng Trung có chọn lọc (Chỉ hiển thị âm có thật):</p>
                <p class="text-xs text-amber-800 leading-relaxed">
                    Khác với các ứng dụng thông thường ghép mù quáng 23 thanh mẫu x 36 vận mẫu tạo ra hơn 800 âm sai, ma trận này <strong>chỉ kích hoạt ~400 âm tiết chuẩn có nghĩa</strong> trong tiếng Hán. Các ô có nền trắng là âm có thật, click vào để nghe đầy đủ 4 thanh điệu; các ô có dấu chấm mờ là kết hợp không tồn tại trong tiếng Trung.
                </p>
            </div>
        </div>

        {{-- Filter Buttons for Initials Group --}}
        <div class="flex flex-wrap items-center gap-1.5 text-xs">
            <span class="text-slate-500 font-medium mr-1">Lọc theo Thanh mẫu:</span>
            <button type="button" 
                    @click="matrixInitialFilter = 'all'"
                    :class="matrixInitialFilter === 'all' ? 'bg-slate-900 text-white font-bold' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'"
                    class="rounded-lg px-2.5 py-1 transition">
                Tất cả (23)
            </button>
            @foreach($initials['groups'] as $group)
                <button type="button" 
                    @click="matrixInitialFilter = '{{ $group['id'] }}'"
                    :class="matrixInitialFilter === '{{ $group['id'] }}' ? 'bg-[#991b1b] text-white font-bold' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'"
                    class="rounded-lg px-2.5 py-1 transition">
                    {{ $group['name'] }}
                </button>
            @endforeach
        </div>

        {{-- The Matrix Scrollable Grid Table --}}
        <div class="rounded-3xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="overflow-x-auto max-h-[700px] overflow-y-auto custom-scrollbar relative">
                <table class="w-full text-center border-collapse">
                    <thead class="bg-slate-900 text-white sticky top-0 z-20 shadow-sm text-xs font-semibold">
                        <tr>
                            <th class="sticky left-0 z-30 bg-slate-900 px-3 py-3 border-r border-slate-800 min-w-[70px] text-amber-300">
                                Thanh \ Vận
                            </th>
                            @foreach($matrix['finals'] as $f)
                                <th class="px-2.5 py-3 border-r border-slate-800 min-w-[56px] text-slate-200 hover:text-white cursor-pointer transition"
                                    title="{{ $f['ipa'] ?? '' }}"
                                    @click="playFinal('{{ $f['final'] }}')">
                                    <span class="block text-sm font-bold text-amber-300">{{ $f['final'] }}</span>
                                    <span class="block text-[10px] text-slate-400 font-mono">{{ $f['ipa'] ?? '' }}</span>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        @foreach($matrix['initials'] as $initItem)
                            @php
                                $initCode = $initItem['initial'];
                                // Find which group this initial belongs to
                                $groupId = 'zero';
                                foreach ($initials['groups'] as $g) {
                                    foreach ($g['items'] as $it) {
                                        if ($it === $initCode) {
                                            $groupId = $g['id'];
                                            break 2;
                                        }
                                    }
                                }
                            @endphp
                            <tr x-show="matrixInitialFilter === 'all' || matrixInitialFilter === '{{ $groupId }}'"
                                class="hover:bg-slate-50/70 transition">
                                {{-- Sticky Left Initial Column --}}
                                <th class="sticky left-0 z-10 bg-slate-100 px-3 py-2 font-bold text-slate-900 border-r border-slate-200 text-center shadow-sm">
                                    <div class="flex flex-col items-center justify-center">
                                        <span class="text-sm text-[#991b1b]">{{ $initCode === '' ? 'Ø' : $initCode }}</span>
                                        @if($initCode !== '')
                                            <button type="button" 
                                                    @click="playInitial('{{ $initCode }}')"
                                                    class="inline-flex items-center gap-0.5 text-[10px] text-slate-500 hover:text-red-700 transition"
                                                    title="Nghe phụ âm {{ $initCode }}">
                                                <i data-lucide="volume-2" class="h-2.5 w-2.5"></i>
                                            </button>
                                        @endif
                                    </div>
                                </th>

                                {{-- Cells for each final --}}
                                @foreach($matrix['finals'] as $f)
                                    @php
                                        $fCode = $f['final'];
                                        $cell = $matrix['cells'][$initCode][$fCode] ?? null;
                                    @endphp
                                    <td class="p-1 border-r border-slate-100 align-middle">
                                        @if($cell)
                                            <button type="button"
                                                    @click="openModal('{{ $cell['syllable'] }}')"
                                                    class="w-full min-h-[38px] rounded-lg px-1.5 py-1 font-semibold text-slate-800 bg-white border border-slate-200 hover:bg-[#991b1b] hover:text-white hover:border-[#991b1b] hover:shadow-md transition group text-center"
                                                    title="{{ $cell['syllable'] }} (Click nghe 4 thanh)">
                                                <span class="block text-xs font-bold leading-tight">{{ $cell['syllable'] }}</span>
                                                @if(isset($cell['tones'][1]['char']))
                                                    <span class="block text-[10px] text-slate-600 group-hover:text-red-100 font-medium">{{ $cell['tones'][1]['char'] }}</span>
                                                @endif
                                            </button>
                                        @else
                                            <div class="w-full min-h-[38px] flex items-center justify-center text-slate-400 select-none">
                                                &middot;
                                            </div>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════ --}}
    {{-- TAB 2: 23 THANH MẪU (Nhóm Theo Khẩu Hình Miệng)     --}}
    {{-- ══════════════════════════════════════════════════ --}}
    <div x-show="activeTab === 'initials'" class="space-y-6">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-lg font-bold text-slate-900 mb-1">23 Thanh Mẫu (Phụ Âm Đầu Trong Tiếng Trung)</h2>
            <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                Tiếng Trung chuẩn có 23 thanh mẫu (gồm 21 phụ âm thực và 2 bán nguyên âm y, w). Điểm quan trọng nhất đối với người Việt là phân biệt rõ ràng giữa <strong>Âm Bật Hơi</strong> (p, t, k, q, ch, c) và <strong>Âm Không Bật Hơi</strong> (b, d, g, j, zh, z).
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            @foreach($initials['groups'] as $group)
                <div class="rounded-3xl border border-slate-200 bg-white p-5 sm:p-6 shadow-sm space-y-4 hover:border-slate-300 transition">
                    <div class="flex items-start justify-between gap-3 border-b border-slate-100 pb-3">
                        <div>
                            <span class="inline-block rounded-md bg-rose-100 px-2 py-0.5 text-[10px] font-black uppercase text-rose-800">
                                Nhóm {{ $loop->iteration }}
                            </span>
                            <h3 class="text-base font-bold text-slate-900 mt-1">{{ $group['name'] }}</h3>
                            <p class="text-xs text-slate-500">{{ $group['desc'] }}</p>
                        </div>
                    </div>

                    <div class="space-y-3">
                        @foreach($group['items'] as $itemKey)
                            @php
                                $item = $initials['list'][$itemKey] ?? null;
                            @endphp
                            @if($item)
                                <div class="rounded-2xl border border-slate-100 bg-slate-50/70 p-3.5 space-y-2.5 hover:bg-slate-50 transition">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div class="grid h-10 w-10 place-items-center rounded-xl bg-white border border-slate-200 shadow-sm">
                                                <span class="text-xl font-black text-[#991b1b]">{{ $item['initial'] }}</span>
                                            </div>
                                            <div>
                                                <div class="flex items-center gap-2">
                                                    <span class="font-mono text-xs font-semibold text-slate-600 bg-slate-200/70 px-1.5 py-0.5 rounded">
                                                        {{ $item['ipa'] }}
                                                    </span>
                                                    <span class="text-xs font-bold text-slate-800">{{ $item['name'] }}</span>
                                                </div>
                                                <p class="text-xs text-slate-500 mt-0.5 leading-snug">{{ $item['vn_hint'] }}</p>
                                            </div>
                                        </div>

                                        <button type="button" 
                                                @click="playInitial('{{ $item['initial'] }}')"
                                                class="grid h-8 w-8 place-items-center rounded-full bg-white border border-slate-200 text-slate-700 hover:text-white hover:bg-[#991b1b] hover:border-[#991b1b] transition shadow-sm"
                                                title="Nghe phát âm {{ $item['initial'] }}">
                                            <i data-lucide="volume-2" class="h-4 w-4"></i>
                                        </button>
                                    </div>

                                    @if(!empty($item['common_mistake']))
                                        <div class="rounded-xl border border-amber-200 bg-amber-50/60 px-3 py-1.5 text-[11px] text-amber-900 flex items-start gap-2">
                                            <i data-lucide="alert-triangle" class="h-3.5 w-3.5 text-amber-600 shrink-0 mt-0.5"></i>
                                            <span><strong>Lưu ý:</strong> {{ $item['common_mistake'] }}</span>
                                        </div>
                                    @endif

                                    {{-- Example --}}
                                    @if(!empty($item['example']))
                                        <div class="flex items-center justify-between pt-1 border-t border-slate-200/50 text-xs">
                                            <span class="text-[10px] uppercase font-bold text-slate-400">Ví dụ:</span>
                                            <button type="button"
                                                    @click="playWord('{{ $item['example']['char'] }}', '{{ $item['example']['pinyin'] }}')"
                                                    class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-2 py-0.5 text-xs text-slate-700 hover:border-red-300 hover:bg-red-50 hover:text-[#991b1b] transition">
                                                <span class="font-bold">{{ $item['example']['char'] }}</span>
                                                <span class="text-[10px] text-slate-500 font-medium">{{ $item['example']['pinyin'] }} ({{ $item['example']['meaning'] }})</span>
                                                <i data-lucide="volume-2" class="h-2.5 w-2.5 opacity-60"></i>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════ --}}
    {{-- TAB 3: 36 VẬN MẪU (Nguyên Âm & Nhóm Âm Mũi)        --}}
    {{-- ══════════════════════════════════════════════════ --}}
    <div x-show="activeTab === 'finals'" class="space-y-6">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-lg font-bold text-slate-900 mb-1">36 Vận Mẫu (Nguyên Âm Tiếng Trung)</h2>
            <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                Vận mẫu bao gồm 6 nguyên âm đơn cơ bản (a, o, e, i, u, ü), các nguyên âm kép và các âm mũi kết thúc bằng <strong>-n</strong> (mũi trước) hoặc <strong>-ng</strong> (mũi sau). Nhấn vào biểu tượng loa để nghe khẩu hình mẫu của từng vận mẫu.
            </p>
        </div>

        <div class="space-y-6">
            @foreach($finals['groups'] as $group)
                <div class="rounded-3xl border border-slate-200 bg-white p-5 sm:p-6 shadow-sm space-y-4">
                    <div class="border-b border-slate-100 pb-3">
                        <div class="flex items-center gap-2">
                            <span class="rounded-md bg-amber-100 px-2 py-0.5 text-[10px] font-black uppercase text-amber-800">
                                Nhóm Vận Mẫu
                            </span>
                            <h3 class="text-base font-bold text-slate-900">{{ $group['name'] }}</h3>
                        </div>
                        <p class="text-xs text-slate-500 mt-1">{{ $group['desc'] }}</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5">
                        @foreach($group['items'] as $itemKey)
                            @php
                                $item = $finals['list'][$itemKey] ?? null;
                            @endphp
                            @if($item)
                                <div class="rounded-2xl border border-slate-100 bg-slate-50/70 p-3.5 space-y-2 hover:bg-slate-50 transition">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2.5">
                                            <div class="grid h-9 w-9 place-items-center rounded-xl bg-white border border-slate-200 font-mono text-base font-black text-[#991b1b] shadow-sm">
                                                {{ $item['final'] }}
                                            </div>
                                            <div>
                                                <span class="font-mono text-xs font-semibold text-slate-600 bg-slate-200/70 px-1.5 py-0.5 rounded">
                                                    {{ $item['ipa'] }}
                                                </span>
                                                <p class="text-xs font-bold text-slate-800 mt-0.5">{{ $item['name'] }}</p>
                                            </div>
                                        </div>

                                        <button type="button" 
                                                @click="playFinal('{{ $item['final'] }}')"
                                                class="grid h-7 w-7 place-items-center rounded-full bg-white border border-slate-200 text-slate-700 hover:text-white hover:bg-[#991b1b] hover:border-[#991b1b] transition shadow-sm"
                                                title="Nghe vận mẫu {{ $item['final'] }}">
                                            <i data-lucide="volume-2" class="h-3.5 w-3.5"></i>
                                        </button>
                                    </div>

                                    <p class="text-xs text-slate-600 leading-snug">{{ $item['vn_hint'] }}</p>

                                    @if(!empty($item['notes']))
                                        <div class="rounded-lg bg-amber-50 px-2 py-1 text-[11px] text-amber-900 border border-amber-200/60">
                                            {{ $item['notes'] }}
                                        </div>
                                    @endif

                                    @if(!empty($item['example']))
                                        <div class="pt-1 border-t border-slate-200/50 flex items-center justify-between text-xs">
                                            <span class="text-[10px] text-slate-400 uppercase font-semibold">Ví dụ:</span>
                                            <button type="button"
                                                    @click="playWord('{{ $item['example']['char'] }}', '{{ $item['example']['pinyin'] }}')"
                                                    class="inline-flex items-center gap-1 font-medium text-slate-700 hover:text-[#991b1b] transition">
                                                <span class="font-bold">{{ $item['example']['char'] }}</span>
                                                <span class="text-slate-500">({{ $item['example']['pinyin'] }}: {{ $item['example']['meaning'] }})</span>
                                                <i data-lucide="volume-2" class="h-2.5 w-2.5"></i>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════ --}}
    {{-- TAB 4: THANH ĐIỆU & QUY TẮC BIẾN ĐIỆU (Sandhi)     --}}
    {{-- ══════════════════════════════════════════════════ --}}
    <div x-show="activeTab === 'tones'" class="space-y-8">
        
        {{-- Part 1: 4 Tones + Neutral Tone Cards --}}
        <div class="space-y-4">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-bold text-slate-900 mb-1">4 Thanh Điệu Chính &amp; Khinh Thanh</h2>
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                    Tiếng Trung là ngôn ngữ thanh điệu (tonal language). Cùng một âm tiết nhưng khác thanh điệu sẽ mang nghĩa hoàn toàn khác biệt. Dưới đây là 4 thanh cơ bản cùng đường cao độ (pitch contour theo thang 5 bậc 1-5).
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                @foreach($toneRules['tones'] as $tone)
                    <div class="rounded-3xl border border-slate-200 bg-white p-4 sm:p-5 shadow-sm space-y-3 flex flex-col justify-between hover:border-slate-300 transition">
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="rounded-md bg-rose-100 px-2 py-0.5 text-[10px] font-black uppercase text-rose-800">
                                    Thanh {{ $tone['tone'] }}
                                </span>
                                <span class="font-mono text-xs font-black text-slate-500">Cao độ: {{ $tone['contour'] }}</span>
                            </div>

                            <div class="text-center py-2">
                                <span class="text-lg font-black text-slate-900">{{ $tone['name'] }}</span>
                                <p class="text-xs text-slate-500 mt-0.5">{{ $tone['symbol'] }}</p>
                            </div>

                            {{-- Tone Curve Diagram SVG --}}
                            <div class="h-16 w-full bg-slate-50 rounded-xl border border-slate-100 flex items-center justify-center p-2 relative overflow-hidden">
                                @if($tone['tone'] == 1)
                                    {{-- Thanh 1: 55 phẳng trên cao --}}
                                    <svg viewBox="0 0 100 40" class="w-full h-full stroke-emerald-600 fill-none stroke-[3] stroke-linecap-round">
                                        <line x1="10" y1="10" x2="90" y2="10" />
                                    </svg>
                                @elseif($tone['tone'] == 2)
                                    {{-- Thanh 2: 35 vút lên --}}
                                    <svg viewBox="0 0 100 40" class="w-full h-full stroke-blue-600 fill-none stroke-[3] stroke-linecap-round">
                                        <line x1="15" y1="30" x2="85" y2="10" />
                                    </svg>
                                @elseif($tone['tone'] == 3)
                                    {{-- Thanh 3: 214 võng xuống rồi lên --}}
                                    <svg viewBox="0 0 100 40" class="w-full h-full stroke-amber-600 fill-none stroke-[3] stroke-linecap-round">
                                        <path d="M 15 20 Q 50 38 85 14" />
                                    </svg>
                                @elseif($tone['tone'] == 4)
                                    {{-- Thanh 4: 51 rơi dốc dứt khoát --}}
                                    <svg viewBox="0 0 100 40" class="w-full h-full stroke-red-600 fill-none stroke-[3] stroke-linecap-round">
                                        <line x1="15" y1="10" x2="85" y2="34" />
                                    </svg>
                                @else
                                    {{-- Khinh thanh: chấm nhẹ giữa --}}
                                    <svg viewBox="0 0 100 40" class="w-full h-full fill-slate-400">
                                        <circle cx="50" cy="20" r="4" />
                                    </svg>
                                @endif
                            </div>

                            <p class="text-xs text-slate-600 leading-snug">{{ $tone['desc'] }}</p>
                        </div>

                        {{-- Tone Sample with Audio --}}
                        <div class="pt-2 border-t border-slate-100 space-y-1.5">
                            <div class="flex items-center justify-between text-xs">
                                <div>
                                    <span class="text-base font-bold text-[#991b1b]">{{ $tone['sample_char'] }}</span>
                                    <span class="font-bold text-slate-800 ml-1">{{ $tone['sample_syllable'] }}</span>
                                    <span class="text-[11px] text-slate-500 block">{{ $tone['meaning'] }}</span>
                                </div>
                                <button type="button" 
                                        @click="playWord('{{ $tone['sample_char'] }}', '{{ $tone['sample_syllable'] }}')"
                                        class="grid h-8 w-8 place-items-center rounded-full bg-slate-100 hover:bg-[#991b1b] hover:text-white transition shadow-sm"
                                        title="Nghe {{ $tone['sample_syllable'] }}">
                                    <i data-lucide="volume-2" class="h-4 w-4"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Part 2: 5 Tone Sandhi Rules (Biến Điệu Cốt Lõi) --}}
        <div class="space-y-4">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center gap-2">
                    <span class="rounded-md bg-rose-100 px-2 py-0.5 text-[10px] font-black uppercase text-rose-800">
                        Cực kỳ quan trọng
                    </span>
                    <h2 class="text-lg font-bold text-slate-900">5 Quy Tắc Biến Điệu (Tone Sandhi) Thực Tế</h2>
                </div>
                <p class="text-xs sm:text-sm text-slate-600 mt-1 leading-relaxed">
                    Rất nhiều người học nhầm lẫn giữa <strong>chính tả Pinyin</strong> (vẫn giữ nguyên trên sách báo) và <strong>cách phát âm thực tế trong lời nói</strong>. Hãy nắm chắc 5 quy tắc dưới đây để nói chuẩn như người bản xứ.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                @foreach($toneRules['sandhi_rules'] as $rule)
                    <div class="rounded-3xl border border-slate-200 bg-white p-5 sm:p-6 shadow-sm space-y-4 hover:border-slate-300 transition">
                        <div class="flex items-start justify-between gap-3 border-b border-slate-100 pb-3">
                            <div>
                                <span class="rounded-md bg-amber-100 px-2 py-0.5 text-[10px] font-black uppercase text-amber-800">
                                    {{ $rule['tag'] ?? 'Quy tắc' }}
                                </span>
                                <h3 class="text-base font-bold text-slate-900 mt-1">{{ $rule['title'] }}</h3>
                                <p class="text-xs text-slate-500 mt-0.5">{{ $rule['rule_summary'] }}</p>
                            </div>
                        </div>

                        <p class="text-xs text-slate-600 leading-relaxed">{{ $rule['explanation'] }}</p>

                        {{-- If cases present (e.g. Yi, Bu) --}}
                        @if(!empty($rule['cases']))
                            <div class="space-y-3 pt-1 border-t border-slate-100">
                                @foreach($rule['cases'] as $case)
                                    <div class="space-y-1.5">
                                        <span class="text-xs font-bold text-slate-700 block">{{ $case['condition'] }}</span>
                                        <div class="space-y-1">
                                            @foreach($case['examples'] as $ex)
                                                <div class="flex items-center justify-between rounded-xl bg-slate-50 px-3 py-1.5 text-xs">
                                                    <div class="flex items-center gap-2">
                                                        <span class="text-sm font-bold text-slate-900">{{ $ex['characters'] }}</span>
                                                        <span class="text-slate-500 font-mono">Viết: {{ $ex['orthography'] }}</span>
                                                        <span class="text-rose-600 font-mono font-bold">&rarr; Đọc: {{ $ex['pronunciation'] }}</span>
                                                        <span class="text-[11px] text-slate-400">({{ $ex['meaning'] }})</span>
                                                    </div>
                                                    <button type="button"
                                                            @click="playWord('{{ $ex['characters'] }}', '{{ $ex['pronunciation'] }}')"
                                                            class="grid h-6 w-6 place-items-center rounded-full bg-white border border-slate-200 text-slate-700 hover:bg-[#991b1b] hover:text-white transition shadow-sm"
                                                            title="Nghe {{ $ex['characters'] }}">
                                                        <i data-lucide="volume-2" class="h-3 w-3"></i>
                                                    </button>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @elseif(!empty($rule['examples']))
                            {{-- Normal examples --}}
                            <div class="space-y-1.5 pt-1 border-t border-slate-100">
                                <span class="text-[10px] uppercase font-bold text-slate-400">Ví dụ minh họa:</span>
                                @foreach($rule['examples'] as $ex)
                                    <div class="flex items-center justify-between rounded-xl bg-slate-50 px-3 py-1.5 text-xs">
                                        <div class="flex items-center gap-2">
                                            <span class="text-sm font-bold text-slate-900">{{ $ex['characters'] }}</span>
                                            @if(isset($ex['orthography']))
                                                <span class="text-slate-500 font-mono">Viết: {{ $ex['orthography'] }}</span>
                                            @endif
                                            @if(isset($ex['pronunciation']))
                                                <span class="text-rose-600 font-mono font-bold">&rarr; Đọc: {{ $ex['pronunciation'] }}</span>
                                            @endif
                                            <span class="text-[11px] text-slate-400">({{ $ex['meaning'] }})</span>
                                        </div>
                                        <button type="button"
                                                @click="playWord('{{ $ex['characters'] }}', '{{ $ex['pronunciation'] ?? $ex['orthography'] }}')"
                                                class="grid h-6 w-6 place-items-center rounded-full bg-white border border-slate-200 text-slate-700 hover:bg-[#991b1b] hover:text-white transition shadow-sm"
                                                title="Nghe {{ $ex['characters'] }}">
                                            <i data-lucide="volume-2" class="h-3 w-3"></i>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════ --}}
    {{-- MODAL: CHI TIẾT ÂM TIẾT & 4 THANH ĐIỆU           --}}
    {{-- ══════════════════════════════════════════════════ --}}
    <div x-show="modalOpen" 
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4"
         @keydown.escape.window="modalOpen = false">
        
        <div class="relative w-full max-w-lg rounded-3xl bg-white p-6 shadow-2xl space-y-6 border border-slate-200"
             @click.away="modalOpen = false"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95">

            {{-- Close Button --}}
            <button type="button" 
                    @click="modalOpen = false"
                    class="absolute right-4 top-4 grid h-8 w-8 place-items-center rounded-full bg-slate-100 text-slate-500 hover:bg-slate-200 hover:text-slate-800 transition">
                <i data-lucide="x" class="h-4 w-4"></i>
            </button>

            {{-- Modal Header --}}
            <div class="text-center space-y-1">
                <span class="inline-block rounded-md bg-rose-100 px-2.5 py-0.5 text-xs font-black text-rose-800 uppercase">
                    Chi tiết âm tiết
                </span>
                <h3 class="text-3xl font-black text-slate-900" x-text="currentSyllable ? currentSyllable.syllable : ''"></h3>
                <p class="text-xs text-slate-500">
                    Thanh mẫu: <strong class="text-slate-800" x-text="currentSyllable && currentSyllable.initial ? currentSyllable.initial : 'Ø (Không có)'"></strong>
                    &bull; Vận mẫu: <strong class="text-slate-800" x-text="currentSyllable ? currentSyllable.final : ''"></strong>
                </p>
            </div>

            {{-- 4 Tones Cards List --}}
            <div class="space-y-2.5 max-h-[360px] overflow-y-auto custom-scrollbar pr-1" x-show="currentSyllable && currentSyllable.tones">
                <template x-for="(toneData, toneNum) in (currentSyllable ? currentSyllable.tones : {})" :key="toneNum">
                    <div class="flex items-center justify-between rounded-2xl border border-slate-200 bg-slate-50/70 p-3 hover:bg-red-50/50 hover:border-red-200 transition">
                        <div class="flex items-center gap-3">
                            {{-- Tone Badge --}}
                            <div class="grid h-10 w-10 place-items-center rounded-xl bg-white border border-slate-200 shadow-sm">
                                <span class="text-xs font-bold text-slate-500" x-text="'T' + toneNum"></span>
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="text-lg font-black text-[#991b1b]" x-text="toneData.pinyin"></span>
                                    <span class="text-base font-bold text-slate-800" x-text="toneData.char"></span>
                                </div>
                                <p class="text-xs text-slate-600" x-text="toneData.meaning"></p>
                            </div>
                        </div>

                        {{-- Play Tone Audio Button --}}
                        <button type="button" 
                                @click="playSyllableTone(currentSyllable.syllable, toneNum, toneData.char, toneData.pinyin)"
                                class="flex items-center gap-1.5 rounded-xl bg-white border border-slate-200 px-3 py-1.5 text-xs font-bold text-slate-700 hover:bg-[#991b1b] hover:text-white hover:border-[#991b1b] transition shadow-sm">
                            <i data-lucide="volume-2" class="h-4 w-4"></i>
                            <span>Nghe</span>
                        </button>
                    </div>
                </template>
            </div>

            {{-- Footer info --}}
            <div class="rounded-xl bg-slate-100 p-3 text-[11px] text-slate-500 flex items-center justify-between">
                <span>Âm thanh chuẩn giọng Bắc Kinh &bull; Tốc độ: <strong x-text="speed + 'x'"></strong></span>
                <button type="button" @click="modalOpen = false" class="font-bold text-[#991b1b] hover:underline">
                    Xong
                </button>
            </div>
        </div>
    </div>

</div>

{{-- ══ Alpine.js + 3-Tier Audio Player ══ --}}
<script>
function pinyinApp() {
    return {
        activeTab: 'matrix',
        matrixInitialFilter: 'all',
        searchQuery: '',
        searchResults: [],
        speed: 1.0,
        modalOpen: false,
        currentSyllable: null,
        audioElement: null,

        init() {
            // Restore speed preference
            const savedSpeed = localStorage.getItem('pinyin_playback_speed');
            if (savedSpeed) {
                this.speed = parseFloat(savedSpeed) || 1.0;
            }
            if (window.lucide) {
                this.$nextTick(() => window.lucide.createIcons());
            }
        },

        setSpeed(rate) {
            this.speed = rate;
            localStorage.setItem('pinyin_playback_speed', rate);
        },

        openModal(syllableKey) {
            fetch(`/pinyin/syllable/${encodeURIComponent(syllableKey)}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.syllable) {
                        this.currentSyllable = data.syllable;
                        this.modalOpen = true;
                        this.$nextTick(() => {
                            if (window.lucide) window.lucide.createIcons();
                        });
                    }
                })
                .catch(err => {
                    console.error('Error fetching syllable:', err);
                });
        },

        performSearch() {
            const q = this.searchQuery.trim();
            if (!q) {
                this.searchResults = [];
                return;
            }
            fetch(`/pinyin/search?q=${encodeURIComponent(q)}`)
                .then(res => res.json())
                .then(data => {
                    this.searchResults = data.results || [];
                })
                .catch(err => console.error(err));
        },

        // Play Initial Sound (e.g. 'b', 'p', 'm')
        playInitial(initial) {
            const soundMap = {
                'b': 'bo', 'p': 'po', 'm': 'mo', 'f': 'fo',
                'd': 'de', 't': 'te', 'n': 'ne', 'l': 'le',
                'g': 'ge', 'k': 'ke', 'h': 'he',
                'j': 'ji', 'q': 'qi', 'x': 'xi',
                'zh': 'zhi', 'ch': 'chi', 'sh': 'shi', 'r': 'ri',
                'z': 'zi', 'c': 'ci', 's': 'si',
                'y': 'yi', 'w': 'wu'
            };
            const sound = soundMap[initial] || initial;
            this.play3TierAudio(`/audio/pinyin/${sound}1.mp3`, sound, sound);
        },

        // Play Final Sound (e.g. 'a', 'ang')
        playFinal(final) {
            this.play3TierAudio(`/audio/pinyin/${final}1.mp3`, final, final);
        },

        // Play Word / Character
        playWord(hanzi, pinyin) {
            this.play3TierAudio(`/audio/pinyin/${pinyin}.mp3`, hanzi, pinyin);
        },

        // Play specific syllable with tone
        playSyllableTone(syllable, tone, char, pinyin) {
            const file = `/audio/pinyin/${syllable}${tone}.mp3`;
            this.play3TierAudio(file, char || pinyin, pinyin);
        },

        /**
         * 3-Tier Audio Engine:
         * 1. Try static pre-cached MP3 file
         * 2. Fallback to Web Speech API (window.speechSynthesis)
         * 3. Fallback to server-side Azure Neural TTS (/tts POST)
         */
        play3TierAudio(staticUrl, chineseText, pinyinFallback) {
            const speed = this.speed;

            // Tier 1: Check static MP3
            const audio = new Audio(staticUrl);
            audio.playbackRate = speed;

            audio.play().catch(() => {
                // Tier 2: Web Speech API fallback
                if ('speechSynthesis' in window) {
                    window.speechSynthesis.cancel();
                    const utter = new SpeechSynthesisUtterance(chineseText || pinyinFallback);
                    utter.lang = 'zh-CN';
                    utter.rate = speed;
                    window.speechSynthesis.speak(utter);
                } else {
                    // Tier 3: Azure TTS API fallback
                    fetch('/tts', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                        },
                        body: JSON.stringify({
                            text: chineseText || pinyinFallback,
                            voice: 'zh-CN-XiaoxiaoNeural'
                        })
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.url) {
                            const fallbackAudio = new Audio(data.url);
                            fallbackAudio.playbackRate = speed;
                            fallbackAudio.play();
                        }
                    })
                    .catch(e => console.warn('All 3 audio tiers failed:', e));
                }
            });
        }
    };
}
</script>
@endsection
