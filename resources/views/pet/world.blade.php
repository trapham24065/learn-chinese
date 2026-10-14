@extends('layouts.app')

@section('title', 'Thế giới Ký ức của Pet | Pet Memory World')

@section('content')
@php
    $petStageObj = $userPet?->pet?->stages?->where('stage', $userPet->stage)?->first();
    $petEmoji = $petStageObj?->emoji ?? '🐉';
    $petName = $userPet?->name ?? 'Tiểu Long';
@endphp

<div class="max-w-6xl mx-auto px-4 py-6 sm:py-8 space-y-6"
     x-data="{
        objects: {{ Js::from($worldObjs) }},
        selectedCategory: 'all',
        searchQuery: '',
        activeWord: null,

        get filteredObjects() {
            let list = this.objects;
            if (this.selectedCategory !== 'all') {
                list = list.filter(o => o.type === this.selectedCategory);
            }
            if (this.searchQuery.trim()) {
                const q = this.searchQuery.toLowerCase().trim();
                list = list.filter(o => 
                    (o.hanzi && o.hanzi.toLowerCase().includes(q)) ||
                    (o.pinyin && o.pinyin.toLowerCase().includes(q)) ||
                    (o.meaning && o.meaning.toLowerCase().includes(q))
                );
            }
            return list;
        },

        speak(text) {
            if (!text) return;
            if (window.playChineseVoice) {
                window.playChineseVoice(text);
            } else if ('speechSynthesis' in window) {
                window.speechSynthesis.cancel();
                const u = new SpeechSynthesisUtterance(text);
                u.lang = 'zh-CN';
                window.speechSynthesis.speak(u);
            }
        },

        inspect(obj) {
            this.activeWord = obj;
            this.speak(obj.hanzi);
            window.soundEngine?.play('ding');
        }
     }">

    {{-- Top Navigation & Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('pet.index') }}"
               class="grid h-10 w-10 place-items-center rounded-2xl bg-white border border-slate-200 text-slate-600 shadow-sm transition hover:bg-slate-50 hover:text-[#991b1b] active:scale-95"
               title="Quay lại Phòng Thú Cưng">
                <i data-lucide="arrow-left" class="h-4 w-4"></i>
            </a>
            <div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1 rounded-full bg-indigo-50 border border-indigo-200 px-2.5 py-0.5 text-xs font-bold text-indigo-700">
                        <i data-lucide="globe-2" class="h-3 w-3"></i>
                        <span>Vùng đất Ký ức</span>
                    </span>
                    <span class="text-xs text-slate-400">• {{ $petName }}</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight mt-0.5">
                    Thế giới Ký ức của Pet
                </h1>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('pet.index') }}"
               class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-2 text-xs font-bold text-slate-700 shadow-sm transition hover:bg-slate-50">
                <i data-lucide="home" class="h-3.5 w-3.5"></i>
                <span>Phòng Pet</span>
            </a>
            <a href="{{ route('flashcards') }}"
               class="inline-flex items-center gap-2 rounded-full bg-[#991b1b] px-4 py-2 text-xs font-bold text-white shadow-md shadow-red-950/15 transition hover:bg-red-800">
                <i data-lucide="sparkles" class="h-3.5 w-3.5"></i>
                <span>Luyện thêm từ mới</span>
            </a>
        </div>
    </div>

    {{-- Poetic Dream Bubble Banner (if pet has a dream sentence) --}}
    @if($dreamSent)
    <div class="relative overflow-hidden rounded-3xl border border-indigo-200/80 bg-gradient-to-r from-indigo-900 via-slate-900 to-purple-950 p-5 sm:p-6 text-white shadow-xl shadow-indigo-950/10">
        <div class="absolute -right-8 -top-8 h-36 w-36 rounded-full bg-indigo-500/10 blur-2xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-start sm:items-center gap-3.5">
                <div class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-white/10 border border-white/10 text-2xl shadow-inner">
                    <span>{{ $petEmoji }}</span>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-[10px] font-bold uppercase tracking-widest text-indigo-300">Giấc mơ đêm qua của {{ $petName }} 💤</span>
                    </div>
                    <p class="text-base sm:text-lg font-bold text-white mt-0.5 tracking-wide">
                        "{{ $dreamSent }}"
                    </p>
                    <p class="text-xs text-indigo-200/70 mt-0.5">
                        Dệt nên từ những từ vựng bạn đã làm chủ trong quá trình học tập.
                    </p>
                </div>
            </div>

            <button type="button"
                    @click="speak('{{ addslashes($dreamSent) }}')"
                    class="inline-flex shrink-0 items-center gap-2 self-start sm:self-center rounded-full bg-white/15 border border-white/20 px-4 py-2 text-xs font-bold text-white transition hover:bg-white/25 active:scale-95 shadow-sm">
                <i data-lucide="volume-2" class="h-3.5 w-3.5"></i>
                <span>Nghe phát âm giấc mơ</span>
            </button>
        </div>
    </div>
    @endif

    {{-- Main Universe Canvas Card --}}
    <div class="rounded-3xl border border-slate-200/90 bg-white p-5 sm:p-7 shadow-xl shadow-slate-900/5 space-y-6">
        
        {{-- Toolbar: Search & Categories Filter --}}
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 border-b border-slate-100 pb-5">
            {{-- Category Filter Chips --}}
            <div class="flex flex-wrap items-center gap-1.5">
                <button type="button"
                        @click="selectedCategory = 'all'"
                        :class="selectedCategory === 'all' ? 'bg-slate-900 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                        class="rounded-full px-3.5 py-1.5 text-xs font-bold transition">
                    Tất cả (<span x-text="objects.length"></span>)
                </button>
                <button type="button"
                        @click="selectedCategory = 'nature'"
                        :class="selectedCategory === 'nature' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-emerald-50 text-emerald-800 border border-emerald-200/60 hover:bg-emerald-100'"
                        class="rounded-full px-3 py-1.5 text-xs font-bold transition flex items-center gap-1">
                    <span>🌸</span> <span>Thiên nhiên</span>
                </button>
                <button type="button"
                        @click="selectedCategory = 'animal'"
                        :class="selectedCategory === 'animal' ? 'bg-amber-600 text-white shadow-sm' : 'bg-amber-50 text-amber-900 border border-amber-200/60 hover:bg-amber-100'"
                        class="rounded-full px-3 py-1.5 text-xs font-bold transition flex items-center gap-1">
                    <span>🐱</span> <span>Muông thú</span>
                </button>
                <button type="button"
                        @click="selectedCategory = 'food'"
                        :class="selectedCategory === 'food' ? 'bg-rose-600 text-white shadow-sm' : 'bg-rose-50 text-rose-900 border border-rose-200/60 hover:bg-rose-100'"
                        class="rounded-full px-3 py-1.5 text-xs font-bold transition flex items-center gap-1">
                    <span>🥟</span> <span>Ẩm thực</span>
                </button>
                <button type="button"
                        @click="selectedCategory = 'home'"
                        :class="selectedCategory === 'home' ? 'bg-blue-600 text-white shadow-sm' : 'bg-blue-50 text-blue-900 border border-blue-200/60 hover:bg-blue-100'"
                        class="rounded-full px-3 py-1.5 text-xs font-bold transition flex items-center gap-1">
                    <span>🏠</span> <span>Gia đình & Đồ vật</span>
                </button>
                <button type="button"
                        @click="selectedCategory = 'school'"
                        :class="selectedCategory === 'school' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-indigo-50 text-indigo-900 border border-indigo-200/60 hover:bg-indigo-100'"
                        class="rounded-full px-3 py-1.5 text-xs font-bold transition flex items-center gap-1">
                    <span>✏️</span> <span>Học đường</span>
                </button>
            </div>

            {{-- Search Box --}}
            <div class="relative w-full sm:w-64 shrink-0">
                <input type="text"
                       x-model="searchQuery"
                       placeholder="Tìm chữ Hán, Pinyin, nghĩa..."
                       class="w-full rounded-full border border-slate-200 bg-slate-50/80 py-2 pl-9 pr-8 text-xs font-medium text-slate-800 outline-none transition focus:border-[#991b1b] focus:bg-white focus:ring-1 focus:ring-[#991b1b]">
                <i data-lucide="search" class="absolute left-3 top-2.5 h-3.5 w-3.5 text-slate-400"></i>
                <button type="button"
                        x-show="searchQuery"
                        @click="searchQuery = ''"
                        class="absolute right-2.5 top-2.5 text-slate-400 hover:text-slate-600">
                    <i data-lucide="x" class="h-3.5 w-3.5"></i>
                </button>
            </div>
        </div>

        {{-- Celestial Sky Canvas Visualizer --}}
        <div class="relative rounded-3xl bg-gradient-to-b from-slate-950 via-indigo-950 to-slate-900 p-6 sm:p-8 min-h-[360px] overflow-hidden border border-indigo-900/50 shadow-inner">
            {{-- Star particles in background --}}
            <div class="absolute inset-0 bg-[radial-gradient(#ffffff_1px,transparent_1px)] [background-size:24px_24px] opacity-20 pointer-events-none"></div>
            <div class="absolute top-6 right-8 text-amber-300/30 text-5xl select-none pointer-events-none">🌙</div>
            <div class="absolute bottom-6 left-8 text-indigo-300/20 text-4xl select-none pointer-events-none">☁️</div>

            @if(count($worldObjs) > 0)
                {{-- Floating entities container --}}
                <div class="relative w-full h-[280px]">
                    <template x-for="obj in filteredObjects" :key="obj.id || obj.hanzi">
                        <div class="absolute group cursor-pointer transition-all duration-300 hover:scale-125 hover:z-30"
                             :style="`left: ${obj.pos_x}%; top: ${obj.pos_y}%; transform: translate(-50%, -50%); font-size: ${16 + (obj.size || 3) * 4}px;`"
                             @click="inspect(obj)">
                            <span class="select-none inline-block transition-transform duration-300 group-hover:animate-bounce drop-shadow-lg"
                                  x-text="obj.emoji"></span>

                            {{-- Tooltip hover card --}}
                            <div class="pointer-events-none absolute bottom-full left-1/2 -translate-x-1/2 mb-2 hidden group-hover:block bg-slate-950/95 border border-indigo-400/40 text-white rounded-xl p-2.5 shadow-2xl backdrop-blur-md whitespace-nowrap z-40 text-center min-w-[110px]">
                                <p class="text-sm font-black text-amber-300" x-text="obj.hanzi"></p>
                                <p class="text-[11px] font-mono text-indigo-200" x-text="obj.pinyin"></p>
                                <p class="text-[10px] text-slate-300 mt-0.5 border-t border-white/10 pt-1" x-text="obj.meaning"></p>
                                <span class="text-[9px] text-amber-400/90 font-bold block mt-1">Bấm để nghe đọc 🔊</span>
                            </div>
                        </div>
                    </template>
                </div>
            @else
                {{-- Empty state when no objects yet --}}
                <div class="flex flex-col items-center justify-center py-16 text-center text-white">
                    <div class="grid h-16 w-16 place-items-center rounded-3xl bg-white/10 border border-white/10 text-3xl mb-3 shadow-inner">
                        <span>🌱</span>
                    </div>
                    <h3 class="text-lg font-bold">Vùng đất ký ức đang đợi bạn gieo mầm</h3>
                    <p class="text-xs text-indigo-200/80 max-w-sm mt-1 leading-relaxed">
                        Mỗi khi bạn học và ôn từ vựng (thuộc 2 lần trở lên), từ đó sẽ hóa thân thành sinh linh hoặc cảnh vật trong thế giới giấc mơ này!
                    </p>
                    <div class="mt-5 flex gap-2.5">
                        <a href="{{ route('flashcards') }}" class="inline-flex items-center gap-1.5 rounded-full bg-amber-400 px-4 py-2 text-xs font-black text-slate-950 shadow-md hover:bg-amber-300 transition">
                            <i data-lucide="layers" class="h-3.5 w-3.5"></i>
                            <span>Ôn Flashcard ngay</span>
                        </a>
                        <a href="{{ route('quiz') }}" class="inline-flex items-center gap-1.5 rounded-full bg-white/20 border border-white/30 px-4 py-2 text-xs font-bold text-white hover:bg-white/30 transition">
                            <i data-lucide="target" class="h-3.5 w-3.5"></i>
                            <span>Luyện tập Quiz</span>
                        </a>
                    </div>
                </div>
            @endif
        </div>

        {{-- Entity Gallery Grid --}}
        <div>
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-700 flex items-center gap-2">
                    <i data-lucide="grid" class="h-4 w-4 text-[#991b1b]"></i>
                    <span>Danh sách <span x-text="filteredObjects.length"></span> sinh linh & đồ vật đã thu thập</span>
                </h3>
                <span class="text-xs text-slate-400">Bấm vào bất kỳ thẻ nào để nghe phát âm</span>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                <template x-for="obj in filteredObjects" :key="'card_' + (obj.id || obj.hanzi)">
                    <div @click="inspect(obj)"
                         class="group relative flex flex-col items-center justify-center p-3.5 rounded-2xl border border-slate-200/80 bg-slate-50/70 hover:bg-white hover:border-amber-300 hover:shadow-md transition-all cursor-pointer active:scale-95 text-center">
                        <span class="text-3xl transition-transform duration-300 group-hover:scale-125" x-text="obj.emoji"></span>
                        <p class="text-base font-black text-slate-900 mt-2" x-text="obj.hanzi"></p>
                        <p class="text-[11px] font-mono text-[#991b1b] font-bold" x-text="obj.pinyin"></p>
                        <p class="text-[11px] text-slate-500 truncate w-full mt-0.5" x-text="obj.meaning" :title="obj.meaning"></p>
                        <span class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition text-slate-400 hover:text-amber-600">
                            <i data-lucide="volume-2" class="h-3 w-3"></i>
                        </span>
                    </div>
                </template>
            </div>

            <div x-show="filteredObjects.length === 0 && objects.length > 0"
                 class="p-8 text-center text-xs text-slate-500 rounded-2xl border border-dashed border-slate-200">
                Không tìm thấy thực thể nào khớp với bộ lọc hoặc từ khóa tìm kiếm.
            </div>
        </div>
    </div>
</div>
@endsection
