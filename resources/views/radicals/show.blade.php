@extends('layouts.app')

@section('title', 'Bộ thủ ' . $radical->name_vi . ' (' . $radical->display_character . ') | Learn Chinese')

@section('content')
<div x-data="radicalDetailApp()" x-init="init()" class="space-y-8 pb-16">

    {{-- ══ 1. BREADCRUMBS ══ --}}
    <nav class="flex items-center gap-2 text-xs text-slate-500">
        <a href="{{ route('dashboard') }}" class="hover:text-[#991b1b] transition">Trang chủ</a>
        <i data-lucide="chevron-right" class="h-3.5 w-3.5 text-slate-400"></i>
        <a href="{{ route('radicals.index') }}" class="hover:text-[#991b1b] transition">214 Bộ thủ</a>
        <i data-lucide="chevron-right" class="h-3.5 w-3.5 text-slate-400"></i>
        <span class="font-bold text-slate-800">#{{ $radical->radical_number }} {{ $radical->name_vi }} ({{ $radical->display_character }})</span>
    </nav>

    {{-- ══ 2. RADICAL HERO DETAIL CARD ══ --}}
    <div class="w-full rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm">
        <div class="flex flex-col lg:flex-row gap-8 items-start">
            
            {{-- Left column: Visual HanziWriter Canvas & Controls (Fixed width on desktop, full on mobile) --}}
            <div class="w-full lg:w-72 shrink-0 flex flex-col items-center justify-center p-6 rounded-2xl bg-gradient-to-b from-amber-50/60 to-red-50/40 border border-amber-200/60 shadow-inner text-center">
                
                {{-- HanziWriter Canvas Target --}}
                <div class="relative overflow-hidden rounded-2xl border-2 border-dashed border-amber-300 bg-white shadow-sm" style="width: 220px; height: 220px;">
                    {{-- Standard grid lines (Mễ tự cách) --}}
                    <div class="absolute inset-0 top-1/2 border-b border-dashed border-amber-200 pointer-events-none"></div>
                    <div class="absolute inset-0 left-1/2 border-r border-dashed border-amber-200 pointer-events-none"></div>
                    
                    {{-- HanziWriter Target DOM --}}
                    <div id="radical-writer-box" class="absolute inset-0 cursor-crosshair"></div>
                </div>

                {{-- Interactive controls for HanziWriter & Audio --}}
                <div class="mt-4 flex flex-wrap items-center justify-center gap-2">
                    <button type="button" 
                            @click="animateWriter()"
                            class="inline-flex items-center gap-1.5 rounded-full bg-slate-900 px-3.5 py-1.5 text-xs font-bold text-white shadow-sm transition hover:bg-slate-800 active:scale-95">
                        <i data-lucide="play" class="h-3.5 w-3.5"></i>
                        <span>Nét viết</span>
                    </button>

                    <button type="button" 
                            @click="quizWriter()"
                            class="inline-flex items-center gap-1.5 rounded-full bg-amber-100 border border-amber-300 px-3.5 py-1.5 text-xs font-bold text-amber-800 transition hover:bg-amber-200 active:scale-95">
                        <i data-lucide="pen-tool" class="h-3.5 w-3.5"></i>
                        <span>Tập viết</span>
                    </button>

                    <button type="button" 
                            @click="speak('{{ $radical->character }}')"
                            class="inline-flex items-center gap-1.5 rounded-full bg-red-100 border border-red-300 px-3.5 py-1.5 text-xs font-bold text-[#991b1b] transition hover:bg-red-200 active:scale-95"
                            title="Phát âm tiếng Trung">
                        <i data-lucide="volume-2" class="h-3.5 w-3.5"></i>
                        <span>Phát âm</span>
                    </button>
                </div>

                {{-- Variants Note --}}
                @if($radical->character !== $radical->display_character || !empty($radical->variants))
                    <div class="mt-4 pt-3 border-t border-amber-200/60 text-xs text-slate-500 w-full text-center">
                        <span>Chữ gốc: </span>
                        <strong class="text-slate-800 font-bold text-sm">{{ $radical->character }}</strong>
                        @if(!empty($radical->variants))
                            <span class="mx-1">•</span>
                            <span>Biến thể: </span>
                            @foreach ($radical->variants as $var)
                                <span class="inline-block px-1.5 py-0.5 rounded-md bg-white border border-amber-200 font-bold text-slate-800 text-sm mx-0.5">{{ $var }}</span>
                            @endforeach
                        @endif
                    </div>
                @endif
            </div>

            {{-- Right column: Information, Mnemonic & Positioning --}}
            <div class="flex-1 min-w-0 space-y-6 w-full">
                
                {{-- Header line: Name, Badges --}}
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="rounded-lg bg-slate-900 px-2.5 py-1 text-xs font-mono font-bold text-amber-300">
                            Bộ thủ #{{ $radical->radical_number }}
                        </span>
                        @if($radical->is_common)
                            <span class="inline-flex items-center gap-1 rounded-lg bg-amber-100 border border-amber-300 px-2.5 py-1 text-xs font-bold text-amber-800">
                                <i data-lucide="star" class="h-3.5 w-3.5 fill-current text-amber-600"></i>
                                Top {{ $radical->common_rank }} thông dụng
                            </span>
                        @endif
                        <span class="rounded-lg bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-700">
                            {{ $radical->stroke_count }} nét
                        </span>
                        <span class="rounded-lg bg-blue-50 border border-blue-200 px-2.5 py-1 text-xs font-bold text-blue-800">
                            {{ $radical->position_badge_label }}
                        </span>
                    </div>

                    <h1 class="mt-3 text-3xl sm:text-4xl font-black text-slate-900 flex items-baseline gap-3">
                        <span>Bộ {{ $radical->name_vi }}</span>
                        <span class="text-2xl sm:text-3xl font-mono font-bold text-[#991b1b]">{{ $radical->pinyin }}</span>
                        <span class="text-2xl text-slate-400 font-normal">({{ $radical->display_character }})</span>
                    </h1>
                </div>

                {{-- Core Meaning Box --}}
                <div class="rounded-2xl bg-slate-50 border border-slate-200/80 p-4 space-y-2">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Ý nghĩa biểu trưng</p>
                    <p class="text-base sm:text-lg font-bold text-slate-800 leading-relaxed">
                        {{ $radical->meaning_vi }}
                    </p>
                    @if($radical->position_desc)
                        <p class="text-xs text-slate-600 pt-1 border-t border-slate-200/60">
                            <strong>Vị trí cấu tạo:</strong> {{ $radical->position_desc }}
                        </p>
                    @endif
                </div>

                {{-- Mnemonic Box (Gợi ý liên tưởng & ghi nhớ) --}}
                @if($radical->mnemonic)
                <div class="rounded-2xl bg-amber-50/70 border border-amber-200/80 p-4 space-y-1.5 text-amber-950">
                    <div class="flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-amber-800">
                        <i data-lucide="lightbulb" class="h-4 w-4 text-amber-600"></i>
                        <span>Điểm tựa liên tưởng ghi nhớ</span>
                    </div>
                    <p class="text-sm leading-relaxed text-amber-900 font-medium">
                        {{ $radical->mnemonic }}
                    </p>
                </div>
                @endif

                {{-- Scholarly Description (nếu có) --}}
                @if($radical->description)
                <div class="text-xs sm:text-sm text-slate-600 leading-relaxed space-y-1">
                    <strong class="text-slate-800">Nguồn gốc & phân tích:</strong>
                    <p>{{ $radical->description }}</p>
                </div>
                @endif

            </div>
        </div>
    </div>

    {{-- ══ 3. CHARACTERS CONTAINING THIS RADICAL ══ --}}
    <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-100">
            <div>
                <h2 class="text-xl font-bold text-slate-900 flex items-center gap-2">
                    <i data-lucide="sparkles" class="h-5 w-5 text-amber-500"></i>
                    <span>Chữ Hán chứa bộ thủ {{ $radical->name_vi }}</span>
                    <span class="rounded-full bg-red-50 border border-red-200 px-2.5 py-0.5 text-xs font-bold text-[#991b1b]">
                        {{ $radical->characters->count() }} chữ tiêu biểu
                    </span>
                </h2>
                <p class="mt-1 text-xs text-slate-500">
                    Bấm vào từng chữ để nghe phát âm, tập viết nét hoặc chuyển sang tra Từ điển đầy đủ.
                </p>
            </div>

            {{-- HSK Filter Buttons --}}
            <div class="flex flex-wrap items-center gap-1 text-xs">
                <button type="button" 
                        @click="selectedHsk = null"
                        :class="selectedHsk === null ? 'bg-[#991b1b] text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                        class="rounded-xl px-3 py-1.5 font-bold transition">
                    Tất cả ({{ $radical->characters->count() }})
                </button>
                @for ($lvl = 1; $lvl <= 6; $lvl++)
                    @php $cnt = isset($charactersByHsk[$lvl]) ? $charactersByHsk[$lvl]->count() : 0; @endphp
                    @if($cnt > 0)
                        <button type="button" 
                                @click="selectedHsk = {{ $lvl }}"
                                :class="selectedHsk === {{ $lvl }} ? 'bg-[#991b1b] text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                                class="rounded-xl px-2.5 py-1.5 font-bold transition">
                            HSK {{ $lvl }} ({{ $cnt }})
                        </button>
                    @endif
                @endfor
            </div>
        </div>

        {{-- Characters Grid --}}
        @if($radical->characters->isEmpty())
            <div class="py-12 text-center text-slate-400 text-sm">
                <i data-lucide="book-open" class="h-8 w-8 mx-auto mb-2 opacity-50"></i>
                <p>Đang cập nhật thêm các chữ Hán chứa bộ thủ này...</p>
            </div>
        @else
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3.5">
                @foreach ($radical->characters as $char)
                    <div x-show="selectedHsk === null || selectedHsk === {{ $char->hsk_level ?? 0 }}"
                         x-transition
                         class="group relative flex flex-col justify-between rounded-2xl border border-slate-200/90 bg-slate-50/50 p-4 text-center transition hover:-translate-y-1 hover:border-amber-300 hover:bg-white hover:shadow-md">
                        
                        {{-- Top: HSK Badge --}}
                        <div class="flex items-center justify-between text-[11px]">
                            @if($char->hsk_level)
                                <span class="rounded-md bg-red-100/80 px-1.5 py-0.5 text-[10px] font-bold text-red-700">
                                    HSK {{ $char->hsk_level }}
                                </span>
                            @else
                                <span></span>
                            @endif

                            {{-- Audio quick pronounce --}}
                            <button type="button" 
                                    @click="speak('{{ $char->character }}')"
                                    class="text-slate-400 hover:text-[#991b1b] transition p-0.5"
                                    title="Nghe phát âm">
                                <i data-lucide="volume-2" class="h-3.5 w-3.5"></i>
                            </button>
                        </div>

                        {{-- Center Hanzi --}}
                        <div class="my-2">
                            <span class="text-3xl sm:text-4xl font-black text-slate-800 group-hover:text-[#991b1b] transition">
                                {{ $char->character }}
                            </span>
                            <p class="mt-1 text-xs font-mono font-bold text-red-600">
                                {{ $char->pinyin }}
                            </p>
                        </div>

                        {{-- Meaning & Dictionary Link --}}
                        <div class="border-t border-slate-200/60 pt-2 space-y-1.5">
                            <p class="text-xs text-slate-600 font-medium line-clamp-2" title="{{ $char->meaning_vi }}">
                                {{ $char->meaning_vi }}
                            </p>
                            <a href="{{ route('dictionary.index', ['q' => $char->character]) }}" 
                               class="inline-flex items-center justify-center gap-1 w-full rounded-lg bg-white border border-slate-200 py-1 text-[11px] font-semibold text-slate-600 hover:border-red-300 hover:text-[#991b1b] transition shadow-2xs">
                                <span>Tra từ điển</span>
                                <i data-lucide="arrow-right" class="h-3 w-3"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- ══ 4. PREV / NEXT NAVIGATION FOOTER ══ --}}
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 pt-4">
        @if($prevRadical)
            <a href="{{ route('radicals.show', $prevRadical->slug) }}"
               class="inline-flex items-center gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-xs transition hover:border-amber-300 hover:shadow-md text-left">
                <i data-lucide="arrow-left" class="h-5 w-5 text-slate-400 shrink-0"></i>
                <div>
                    <span class="text-[11px] font-semibold text-slate-400">Bộ thủ trước</span>
                    <p class="text-sm font-bold text-slate-800">
                        #{{ $prevRadical->radical_number }} {{ $prevRadical->display_character }} ({{ $prevRadical->name_vi }})
                    </p>
                </div>
            </a>
        @else
            <div></div>
        @endif

        <a href="{{ route('radicals.index') }}" 
           class="inline-flex items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-white px-5 py-3 text-xs font-bold text-slate-700 shadow-xs hover:bg-slate-50 transition">
            <i data-lucide="grid" class="h-4 w-4"></i>
            <span>Về danh mục 214 Bộ thủ</span>
        </a>

        @if($nextRadical)
            <a href="{{ route('radicals.show', $nextRadical->slug) }}"
               class="inline-flex items-center justify-end gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-xs transition hover:border-amber-300 hover:shadow-md text-right">
                <div>
                    <span class="text-[11px] font-semibold text-slate-400">Bộ thủ tiếp theo</span>
                    <p class="text-sm font-bold text-slate-800">
                        #{{ $nextRadical->radical_number }} {{ $nextRadical->display_character }} ({{ $nextRadical->name_vi }})
                    </p>
                </div>
                <i data-lucide="arrow-right" class="h-5 w-5 text-slate-400 shrink-0"></i>
            </a>
        @endif
    </div>

</div>

{{-- Alpine.js App Logic for HanziWriter & Sound --}}
<script>
function radicalDetailApp() {
    return {
        selectedHsk: null,
        writer: null,
        currentChar: '{{ $radical->display_character }}',

        init() {
            this.$nextTick(() => {
                this.initHanziWriter();
            });
        },

        initHanziWriter() {
            const target = document.getElementById('radical-writer-box');
            if (!target || !window.HanziWriter) return;
            target.innerHTML = '';
            
            try {
                this.writer = window.HanziWriter.create(target, this.currentChar, {
                    width: 220,
                    height: 220,
                    padding: 20,
                    showOutline: true,
                    strokeColor: '#991b1b',
                    outlineColor: '#e2e8f0',
                    highlightColor: '#10b981',
                    drawingColor: '#1e293b',
                    drawingWidth: 16,
                });
                this.writer.animateCharacter();
            } catch (e) {
                console.warn('HanziWriter not available for this character:', e);
            }
        },

        animateWriter() {
            if (this.writer) {
                this.writer.animateCharacter();
            }
        },

        quizWriter() {
            if (this.writer) {
                this.writer.quiz();
            }
        },

        speak(text) {
            if (window.playChineseVoice) {
                window.playChineseVoice(text);
            }
        }
    };
}
</script>
@endsection
