@extends('layouts.app')

@section('title', 'Từ điển 2 chiều & Video Ngữ cảnh | Chinese Deck')

@section('content')
{{-- Alpine.js Dictionary Logic declared before DOM mount --}}
<script>
window.loadYouGlish = function () {
    return new Promise((resolve, reject) => {
        if (window.YG && typeof window.YG.Widget === 'function') {
            resolve(window.YG);
            return;
        }
        if (window.__youglishLoading) {
            const interval = setInterval(() => {
                if (window.YG && typeof window.YG.Widget === 'function') {
                    clearInterval(interval);
                    resolve(window.YG);
                }
            }, 100);
            return;
        }
        window.__youglishLoading = true;
        window.onYouglishAPIReady = function () {
            resolve(window.YG);
        };
        const script = document.createElement('script');
        script.src = 'https://youglish.com/public/emb/widget.js';
        script.async = true;
        script.onerror = () => {
            window.__youglishLoading = false;
            reject(new Error('Không thể tải YouGlish script'));
        };
        document.head.appendChild(script);
    });
};

window.dictionaryApp = function () {
    return {
        query: '{{ addslashes($query) }}',
        hskFilter: '{{ $selectedHsk ?? "" }}',
        activeWord: @json($initialResult),
        searchResults: [],
        isSearching: false,
        showSuggestions: false,
        searchTimeout: null,
        detectedType: 'hanzi',

        // YouGlish State
        ygWidget: null,
        ygLoading: false,
        ygError: false,
        ygTotal: 0,
        ygCurrent: 1,
        ygSpeed: 1.0,
        ygLoadedWord: null,
        ygActivated: false,

        // Hanzi Writer State
        showWriterModal: false,
        writerInstance: null,

        init() {
            if (window.refreshIcons) {
                this.$nextTick(() => window.refreshIcons());
            }
        },

        startVideo() {
            this.ygActivated = true;
            if (this.activeWord && this.activeWord.hanzi) {
                this.$nextTick(() => {
                    this.initYouGlish(this.activeWord.hanzi);
                    if (window.refreshIcons) window.refreshIcons();
                });
            }
        },

        onInput() {
            if (this.searchTimeout) clearTimeout(this.searchTimeout);

            const q = this.query.trim();
            if (!q) {
                this.searchResults = [];
                this.showSuggestions = false;
                return;
            }

            this.showSuggestions = true;
            this.isSearching = true;

            this.searchTimeout = setTimeout(() => {
                this.performSearch(q);
            }, 280);
        },

        async performSearch(q) {
            try {
                const params = new URLSearchParams({ q: q });
                if (this.hskFilter) params.append('hsk', this.hskFilter);

                const res = await fetch(`{{ route('dictionary.search') }}?${params.toString()}`, {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();

                if (data.success) {
                    this.searchResults = data.results || [];
                    this.detectedType = data.detected_type || 'hanzi';
                }
            } catch (err) {
                console.error('Search error:', err);
            } finally {
                this.isSearching = false;
            }
        },

        selectWord(word) {
            this.showSuggestions = false;
            this.query = word.hanzi;
            this.fetchWordDetails(word.hanzi);
        },

        async fetchWordDetails(wordQuery) {
            this.isSearching = true;
            try {
                const res = await fetch(`{{ route('dictionary.search') }}?q=${encodeURIComponent(wordQuery)}`, {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();
                if (data.success && data.exact) {
                    this.activeWord = data.exact;
                    if (data.detected_type) {
                        this.detectedType = data.detected_type;
                    }
                    if (this.ygActivated && this.activeWord.hanzi) {
                        this.initYouGlish(this.activeWord.hanzi);
                    }
                    if (window.history && window.history.replaceState) {
                        const newUrl = new URL(window.location.href);
                        newUrl.searchParams.set('q', wordQuery);
                        window.history.replaceState({}, '', newUrl);
                    }
                }
            } catch (err) {
                console.error('Word fetch error:', err);
            } finally {
                this.isSearching = false;
                this.$nextTick(() => window.refreshIcons && window.refreshIcons());
            }
        },

        applyHskFilter(level) {
            this.hskFilter = this.hskFilter === String(level) ? '' : String(level);
            if (this.query.trim()) {
                this.performSearch(this.query.trim());
            }
        },

        // YouGlish Integration (components: 88 = Caption 8 + Speed 16 + Controls 64, Title 4 removed)
        async initYouGlish(word) {
            if (!word) return;
            if (this.ygLoadedWord === word && this.ygWidget) {
                this.ygWidget.replay();
                return;
            }

            this.ygLoading = true;
            this.ygError = false;
            this.ygTotal = 0;
            this.ygCurrent = 1;
            this.ygLoadedWord = word;

            try {
                await window.loadYouGlish();

                const container = document.getElementById('youglish-container');
                if (!container) {
                    this.ygLoading = false;
                    return;
                }

                if (!this.ygWidget) {
                    this.ygWidget = new window.YG.Widget("youglish-container", {
                        autoStart: 0,
                        components: 88, // Caption (8) + Speed (16) + Controls (64) = 88 (Title 4 removed to hide English header)
                        events: {
                            'onFetchDone': (event) => {
                                this.ygTotal = event.totalResult || 0;
                                this.ygLoading = false;
                                if (event.totalResult === 0) {
                                    this.ygError = true;
                                }
                            },
                            'onVideoChange': (event) => {
                                this.ygCurrent = event.trackNumber || 1;
                            },
                            'onError': () => {
                                this.ygError = true;
                                this.ygLoading = false;
                            }
                        }
                    });
                }

                this.ygWidget.fetch(word, 'chinese');
            } catch (err) {
                console.error('YouGlish init error:', err);
                this.ygError = true;
                this.ygLoading = false;
            }
        },

        moveYG(seconds) {
            if (this.ygWidget && typeof this.ygWidget.move === 'function') {
                this.ygWidget.move(seconds);
            }
        },

        replayYG() {
            if (this.ygWidget && typeof this.ygWidget.replay === 'function') {
                this.ygWidget.replay();
            }
        },

        setSpeedYG(speed) {
            this.ygSpeed = speed;
            if (this.ygWidget && typeof this.ygWidget.setSpeed === 'function') {
                this.ygWidget.setSpeed(speed);
            }
        },

        nextYG() {
            if (this.ygWidget && typeof this.ygWidget.next === 'function') {
                this.ygWidget.next();
            }
        },

        prevYG() {
            if (this.ygWidget && typeof this.ygWidget.previous === 'function') {
                this.ygWidget.previous();
            }
        },

        speakCurrent() {
            if (this.activeWord && this.activeWord.hanzi && window.playChineseVoice) {
                window.playChineseVoice(this.activeWord.hanzi);
            }
        },

        speakText(text) {
            if (text && window.playChineseVoice) {
                window.playChineseVoice(text);
            }
        },

        async toggleStar() {
            if (!this.activeWord || !this.activeWord.id) return;
            const prev = this.activeWord.is_starred;
            this.activeWord.is_starred = !prev;

            try {
                const res = await fetch('{{ route("flashcards.toggleStar") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ flashcard_id: this.activeWord.id })
                });
                const data = await res.json();
                if (data.success) {
                    this.activeWord.is_starred = data.is_starred;
                }
            } catch (e) {
                this.activeWord.is_starred = prev;
            }
        },

        // Hanzi Writer modal
        openHanziWriter(hanzi) {
            const firstChar = hanzi ? hanzi.charAt(0) : '字';
            this.showWriterModal = true;

            this.$nextTick(() => {
                const target = document.getElementById('dict-hanzi-writer-box');
                if (!target || !window.HanziWriter) return;
                target.innerHTML = '';
                this.writerInstance = window.HanziWriter.create(target, firstChar, {
                    width: 220,
                    height: 220,
                    padding: 15,
                    showOutline: true,
                    strokeColor: '#991b1b',
                    outlineColor: '#cbd5e1',
                    highlightColor: '#10b981',
                    drawingColor: '#333333',
                    drawingWidth: 14,
                });
                this.writerInstance.animateCharacter();
            });
        },

        animateWriter() {
            if (this.writerInstance) this.writerInstance.animateCharacter();
        },

        quizWriter() {
            if (this.writerInstance) this.writerInstance.quiz();
        }
    };
};
</script>

<div x-data="dictionaryApp()" class="space-y-8 pb-16">

    {{-- ══ 1. HEADER & SEARCH HERO ══ --}}
    <div class="relative overflow-hidden rounded-[2.5rem] border border-amber-900/10 bg-gradient-to-br from-slate-950 via-slate-900 to-red-950 p-6 sm:p-10 text-white shadow-2xl">
        {{-- Glow Orbs --}}
        <div class="absolute -left-12 -top-12 h-48 w-48 rounded-full bg-red-600/20 blur-3xl pointer-events-none"></div>
        <div class="absolute right-0 bottom-0 h-64 w-64 rounded-full bg-amber-500/15 blur-3xl pointer-events-none"></div>

        <div class="relative max-w-3xl mx-auto text-center space-y-4">
            <div class="inline-flex items-center gap-2 rounded-full border border-amber-400/30 bg-white/10 px-4 py-1.5 text-xs font-bold uppercase tracking-widest text-amber-200 backdrop-blur">
                <i data-lucide="sparkles" class="h-3.5 w-3.5 text-amber-300"></i>
                Từ điển 2 Chiều & Video YouGlish
            </div>

            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight text-white">
                Tra từ thông minh <span class="text-transparent bg-clip-text bg-gradient-to-r from-amber-300 to-red-400">ngữ cảnh thực tế</span>
            </h1>

            <p class="text-sm sm:text-base text-slate-300 max-w-xl mx-auto leading-relaxed">
                Gõ <strong class="text-white">Tiếng Việt</strong>, <strong class="text-white">Chữ Hán</strong> hoặc <strong class="text-white">Pinyin</strong> để tra nghĩa, nghe người bản xứ nói trong phim và trích đoạn bài đọc.
            </p>

            {{-- Smart Universal Search Bar --}}
            <div class="relative pt-2" @click.outside="showSuggestions = false">
                <div class="relative flex items-center rounded-2xl bg-white text-slate-900 shadow-2xl transition focus-within:ring-4 focus-within:ring-amber-400/40">
                    <div class="pl-4 pr-2 text-slate-400">
                        <i data-lucide="search" class="h-5 w-5"></i>
                    </div>

                    <input type="text"
                           x-model="query"
                           @input="onInput()"
                           @keydown.enter.prevent="if (query.trim()) { showSuggestions = false; fetchWordDetails(query.trim()); }"
                           @focus="if (searchResults.length > 0) showSuggestions = true"
                           placeholder="Nhập tiếng Việt (vd: xin chào) hoặc Chữ Hán (vd: 你好, 高兴) hoặc Pinyin..."
                           class="w-full bg-transparent py-4 pr-12 text-sm sm:text-base font-semibold placeholder:text-slate-400 placeholder:font-normal focus:outline-none"
                           autocomplete="off">

                    {{-- Clear button --}}
                    <button type="button"
                            x-show="query.length > 0"
                            @click="query = ''; searchResults = []; showSuggestions = false"
                            class="p-2 text-slate-400 hover:text-slate-600 transition"
                            title="Xóa tìm kiếm">
                        <i data-lucide="x" class="h-4 w-4"></i>
                    </button>

                    {{-- Search Submit --}}
                    <button type="button"
                            @click="if (query.trim()) { showSuggestions = false; fetchWordDetails(query.trim()); }"
                            class="mr-2 hidden sm:inline-flex items-center gap-1.5 rounded-xl bg-[#991b1b] px-5 py-2.5 text-xs font-bold text-white transition hover:bg-[#7f1717] shadow-sm">
                        Tra từ
                    </button>
                </div>

                {{-- Live Autocomplete Dropdown --}}
                <div x-show="showSuggestions && (searchResults.length > 0 || isSearching || query.trim().length > 0)"
                     x-cloak
                     x-transition
                     class="absolute left-0 right-0 top-full z-50 mt-2 max-h-80 overflow-y-auto rounded-2xl border border-slate-200 bg-white p-2 text-left text-slate-900 shadow-2xl custom-scrollbar">
                    
                    <div x-show="isSearching" class="p-3 text-center text-xs text-slate-400 flex items-center justify-center gap-2">
                        <i data-lucide="loader-2" class="h-4 w-4 animate-spin text-[#991b1b]"></i>
                        Đang tìm kiếm...
                    </div>

                    <template x-for="item in searchResults" :key="item.id">
                        <button type="button"
                                @click="selectWord(item)"
                                class="w-full flex items-center justify-between gap-3 rounded-xl p-3 text-left transition hover:bg-amber-50/80 group">
                            <div class="flex items-center gap-3">
                                <span class="text-xl font-black text-slate-900 group-hover:text-[#991b1b] transition" x-text="item.hanzi"></span>
                                <div>
                                    <span class="text-xs font-bold text-amber-700 font-mono" x-text="item.pinyin"></span>
                                    <p class="text-xs text-slate-500 line-clamp-1" x-text="item.meaning"></p>
                                </div>
                            </div>
                            <span class="rounded-lg bg-slate-100 px-2 py-0.5 text-[10px] font-bold text-slate-600 uppercase" x-text="'HSK ' + item.hsk_level"></span>
                        </button>
                    </template>

                    {{-- When query has no HSK matches: prompt to press enter or click for full fallback --}}
                    <div x-show="searchResults.length === 0 && !isSearching && query.trim().length > 0" class="p-3 text-center space-y-2">
                        <p class="text-xs text-slate-500">Chưa có trong bộ từ vựng HSK 1-6.</p>
                        <button type="button"
                                @click="showSuggestions = false; fetchWordDetails(query.trim())"
                                class="inline-flex items-center gap-1.5 rounded-xl bg-amber-50 border border-amber-200 px-3 py-1.5 text-xs font-bold text-amber-800 hover:bg-amber-100 transition shadow-2xs">
                            <i data-lucide="sparkles" class="h-3.5 w-3.5 text-amber-600"></i>
                            <span>Tra mở rộng & Video YouGlish</span>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Quick HSK Filters & Hot Suggestions --}}
            <div class="flex flex-wrap items-center justify-center gap-2 pt-2 text-xs">
                <span class="text-slate-400 font-medium mr-1">Cấp độ:</span>
                @foreach([1, 2, 3, 4, 5, 6] as $lvl)
                <button type="button"
                        @click="applyHskFilter({{ $lvl }})"
                        :class="hskFilter === '{{ $lvl }}' ? 'bg-amber-400 text-slate-950 font-bold shadow-md' : 'bg-white/10 text-white/80 hover:bg-white/20'"
                        class="rounded-full px-3 py-1 transition border border-white/10">
                    HSK {{ $lvl }}
                </button>
                @endforeach
            </div>

            {{-- Suggestions Pills --}}
            <div class="flex flex-wrap items-center justify-center gap-1.5 text-xs text-slate-300 pt-1">
                <span class="text-slate-400">Gợi ý tra:</span>
                @foreach($suggestions as $sug)
                <button type="button"
                        @click="query = '{{ $sug->hanzi }}'; fetchWordDetails('{{ $sug->hanzi }}')"
                        class="rounded-lg bg-white/5 border border-white/10 px-2.5 py-0.5 text-amber-200/90 hover:bg-white/15 hover:text-white transition">
                    {{ $sug->hanzi }} ({{ $sug->pinyin }})
                </button>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ══ 2. RESULT BENTO-BOX SECTION ══ --}}
    <template x-if="activeWord">
        <div class="space-y-8">

            {{-- SMART FALLBACK ALERT BANNER (Shown when activeWord.is_fallback is true) --}}
            <template x-if="activeWord.is_fallback">
                <div class="rounded-3xl border border-amber-200/90 bg-gradient-to-r from-amber-50 via-orange-50/50 to-amber-50/30 p-5 sm:p-6 shadow-sm">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-start sm:items-center gap-3.5">
                            <div class="h-10 w-10 sm:h-12 sm:w-12 rounded-2xl bg-amber-500/10 border border-amber-500/20 text-amber-700 flex items-center justify-center shrink-0">
                                <i data-lucide="info" class="h-5 w-5 sm:h-6 sm:w-6"></i>
                            </div>
                            <div>
                                <h3 class="text-sm sm:text-base font-bold text-amber-950 flex items-center gap-2">
                                    <span>Từ vựng mở rộng</span>
                                    <span class="text-[11px] font-semibold text-amber-800 bg-amber-100/90 border border-amber-300/60 px-2.5 py-0.5 rounded-full"
                                          x-text="activeWord.detected_type === 'vietnamese' ? 'Dịch Tiếng Việt ➔ Chữ Hán' : (activeWord.detected_type === 'hanzi' ? 'Chữ Hán ngoài HSK' : 'Ngoài danh mục HSK')"></span>
                                </h3>
                                <p class="text-xs sm:text-sm text-amber-900/80 mt-0.5"
                                   x-text="activeWord.hanzi 
                                       ? (activeWord.detected_type === 'vietnamese' 
                                           ? 'Hệ thống đã tự động dịch từ khóa tiếng Việt sang Chữ Hán tương ứng (' + activeWord.hanzi + '). Bạn có thể nghe phát âm AI, luyện viết nét chữ và xem video người bản xứ phát âm câu thoại bên phải!'
                                           : 'Từ này chưa có trong bộ 5,000 từ HSK cốt lõi, nhưng bạn vẫn có thể nghe phát âm AI, luyện viết nét chữ và xem người bản xứ phát âm qua YouGlish bên dưới!')
                                       : 'Không tìm thấy từ vựng HSK tương ứng với từ khóa này. Bạn có thể tra nhanh trên Google Dịch hoặc khám phá các từ gợi ý bên dưới.'">
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 self-start sm:self-center shrink-0">
                            <a :href="'https://translate.google.com/?sl=' + (activeWord.detected_type === 'hanzi' ? 'zh-CN&tl=vi' : 'auto&tl=zh-CN') + '&text=' + encodeURIComponent(activeWord.query || '') + '&op=translate'"
                               target="_blank"
                               rel="noopener noreferrer"
                               class="inline-flex items-center gap-1.5 rounded-xl bg-white border border-amber-300/80 px-3.5 py-2 text-xs font-bold text-amber-900 hover:bg-amber-100/60 shadow-sm transition">
                                <span>Google Dịch</span>
                                <i data-lucide="external-link" class="h-3.5 w-3.5"></i>
                            </a>
                            <template x-if="activeWord.hanzi">
                                <a :href="'https://baike.baidu.com/item/' + encodeURIComponent(activeWord.hanzi || '')"
                                   target="_blank"
                                   rel="noopener noreferrer"
                                   class="inline-flex items-center gap-1.5 rounded-xl bg-amber-900 text-amber-100 px-3.5 py-2 text-xs font-bold hover:bg-amber-950 shadow-sm transition">
                                    <span>Baidu Baike</span>
                                    <i data-lucide="external-link" class="h-3.5 w-3.5"></i>
                                </a>
                            </template>
                        </div>
                    </div>
                </div>
            </template>

            <div class="grid gap-6 lg:grid-cols-12 items-start">
                
                {{-- ── LEFT COLUMN: WORD DETAILS CARD (5 COLS) ── --}}
                <div class="lg:col-span-5 space-y-6">

                    {{-- Standard Flashcard Card (When word is in database) --}}
                    <div x-show="!activeWord.is_fallback" class="rounded-3xl border border-slate-200/80 bg-white p-6 sm:p-8 shadow-sm relative overflow-hidden">
                        {{-- Top Badges & Actions --}}
                        <div class="flex items-center justify-between gap-3 border-b border-slate-100 pb-4">
                            <div class="flex items-center gap-2">
                                <span class="rounded-xl bg-red-100 border border-red-200 px-3 py-1 text-xs font-black text-[#991b1b] uppercase tracking-wider"
                                      x-text="'HSK ' + activeWord.hsk_level"></span>
                                <span class="rounded-xl bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-500">Từ vựng</span>
                            </div>

                            <div class="flex items-center gap-1">
                                {{-- Star button --}}
                                <button type="button"
                                        @click="toggleStar()"
                                        :class="activeWord.is_starred ? 'text-amber-500 bg-amber-50 border-amber-200' : 'text-slate-400 bg-slate-50 hover:text-slate-600'"
                                        class="h-9 w-9 rounded-xl border border-slate-200 flex items-center justify-center transition"
                                        title="Lưu từ vào danh sách ôn tập">
                                    <i data-lucide="star" :class="activeWord.is_starred ? 'fill-amber-400' : ''" class="h-4 w-4"></i>
                                </button>

                                {{-- Pronunciation audio --}}
                                <button type="button"
                                        @click="speakCurrent()"
                                        class="h-9 w-9 rounded-xl border border-slate-200 bg-slate-50 text-slate-700 hover:text-[#991b1b] hover:bg-red-50 flex items-center justify-center transition"
                                        title="Nghe phát âm chuẩn giọng Bắc Kinh">
                                    <i data-lucide="volume-2" class="h-4 w-4"></i>
                                </button>

                                {{-- Hanzi writer --}}
                                <button type="button"
                                        @click="openHanziWriter(activeWord.hanzi)"
                                        class="h-9 w-9 rounded-xl border border-slate-200 bg-slate-50 text-slate-700 hover:text-[#991b1b] hover:bg-red-50 flex items-center justify-center transition"
                                        title="Tập viết nét chữ Hán">
                                    <i data-lucide="pen-tool" class="h-4 w-4"></i>
                                </button>
                            </div>
                        </div>

                        {{-- Big Hanzi & Pinyin Display --}}
                        <div class="py-6 text-center">
                            <h2 class="text-6xl sm:text-7xl font-black text-slate-900 tracking-wide font-chinese select-all"
                                x-text="activeWord.hanzi"></h2>
                            <p class="mt-2 text-xl sm:text-2xl font-bold text-amber-700 font-mono"
                               x-text="activeWord.pinyin"></p>
                        </div>

                        {{-- Meaning Definition --}}
                        <div class="rounded-2xl bg-amber-50/70 border border-amber-200/60 p-4 space-y-1">
                            <p class="text-xs font-bold uppercase tracking-wider text-amber-900/80">Định nghĩa tiếng Việt:</p>
                            <p class="text-base sm:text-lg font-bold text-slate-900" x-text="activeWord.meaning"></p>
                        </div>

                        {{-- Example Sentence --}}
                        <template x-if="activeWord.example">
                            <div class="mt-5 space-y-2">
                                <div class="flex items-center justify-between">
                                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Câu ví dụ mẫu:</p>
                                    <button type="button"
                                            @click="speakText(activeWord.example)"
                                            class="text-xs font-bold text-amber-700 hover:text-amber-800 flex items-center gap-1">
                                        <i data-lucide="volume-2" class="h-3.5 w-3.5"></i>
                                        Nghe câu
                                    </button>
                                </div>
                                <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-4 space-y-1">
                                    <p class="text-base font-bold text-slate-900 font-chinese" x-text="activeWord.example"></p>
                                    <p class="text-xs font-semibold text-amber-800 font-mono" x-text="activeWord.example_pinyin"></p>
                                    <p class="text-xs text-slate-600 font-medium pt-1 border-t border-slate-200/60" x-text="activeWord.example_meaning"></p>
                                </div>
                            </div>
                        </template>

                        {{-- Action Buttons --}}
                        <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between gap-3">
                            <button type="button"
                                    @click="openHanziWriter(activeWord.hanzi)"
                                    class="flex-1 inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-4 py-2.5 text-xs font-bold text-white transition hover:bg-slate-800">
                                <i data-lucide="pen-tool" class="h-3.5 w-3.5 text-amber-300"></i>
                                Tập viết nét chữ
                            </button>
                            <button type="button"
                                    @click="speakCurrent()"
                                    class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-bold text-slate-700 transition hover:bg-slate-50">
                                <i data-lucide="volume-2" class="h-3.5 w-3.5 text-red-600"></i>
                                Phát âm AI
                            </button>
                        </div>
                    </div>

                    {{-- Smart Fallback Card (When word is NOT in database) --}}
                    <div x-show="activeWord.is_fallback" class="rounded-3xl border border-amber-200/80 bg-white p-6 sm:p-8 shadow-sm relative overflow-hidden space-y-6">
                        
                        {{-- Fallback Case A: Hanzi word available (either directly entered or translated from Vietnamese) --}}
                        <div x-show="activeWord.hanzi" class="space-y-6">
                            <div class="flex items-center justify-between gap-3 border-b border-slate-100 pb-4">
                                <div class="flex items-center gap-2">
                                    <span class="rounded-xl bg-amber-100 border border-amber-300/80 px-3 py-1 text-xs font-black text-amber-900 uppercase tracking-wider"
                                          x-text="activeWord.detected_type === 'vietnamese' ? 'Dịch Tiếng Việt ➔ Chữ Hán' : 'Chữ Hán mở rộng'">
                                    </span>
                                    <span class="rounded-xl bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-500">Ngoài HSK</span>
                                </div>

                                <div class="flex items-center gap-1">
                                    <button type="button"
                                            @click="speakCurrent()"
                                            class="h-9 w-9 rounded-xl border border-slate-200 bg-slate-50 text-slate-700 hover:text-[#991b1b] hover:bg-red-50 flex items-center justify-center transition"
                                            title="Nghe phát âm chuẩn AI">
                                        <i data-lucide="volume-2" class="h-4 w-4"></i>
                                    </button>
                                    <button type="button"
                                            @click="openHanziWriter(activeWord.hanzi)"
                                            class="h-9 w-9 rounded-xl border border-slate-200 bg-slate-50 text-slate-700 hover:text-[#991b1b] hover:bg-red-50 flex items-center justify-center transition"
                                            title="Tập viết nét chữ Hán">
                                        <i data-lucide="pen-tool" class="h-4 w-4"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="py-4 text-center">
                                <h2 class="text-6xl sm:text-7xl font-black text-slate-900 tracking-wide font-chinese select-all"
                                    x-text="activeWord.hanzi"></h2>
                                <div class="mt-2 text-xs font-semibold text-slate-500 flex items-center justify-center gap-1.5">
                                    <span x-show="activeWord.query && activeWord.query !== activeWord.hanzi">
                                        Từ gốc: <strong class="text-amber-800" x-text="activeWord.query"></strong> •
                                    </span>
                                    <span>Từ vựng ngoài danh mục HSK cốt lõi</span>
                                </div>
                            </div>

                            {{-- Tra cứu ngoài --}}
                            <div class="rounded-2xl bg-amber-50/60 border border-amber-200/70 p-4 space-y-3">
                                <p class="text-xs font-bold uppercase tracking-wider text-amber-900/80">Tra cứu nhanh từ điển ngoài:</p>
                                <div class="space-y-2">
                                    <a :href="'https://translate.google.com/?sl=zh-CN&tl=vi&text=' + encodeURIComponent(activeWord.hanzi || '') + '&op=translate'"
                                       target="_blank"
                                       rel="noopener noreferrer"
                                       class="flex items-center justify-between p-3 rounded-xl bg-white border border-slate-200 hover:border-amber-300 hover:bg-amber-50/50 transition group">
                                        <div class="flex items-center gap-2.5">
                                            <div class="h-7 w-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                                                <i data-lucide="globe" class="h-4 w-4"></i>
                                            </div>
                                            <div>
                                                <p class="text-xs font-bold text-slate-900 group-hover:text-blue-700">Google Dịch (Nghĩa & Ví dụ)</p>
                                                <p class="text-[11px] text-slate-400">Xem thêm các tầng nghĩa & câu ví dụ</p>
                                            </div>
                                        </div>
                                        <i data-lucide="external-link" class="h-3.5 w-3.5 text-slate-400 group-hover:text-blue-600"></i>
                                    </a>

                                    <a :href="'https://baike.baidu.com/item/' + encodeURIComponent(activeWord.hanzi || '')"
                                       target="_blank"
                                       rel="noopener noreferrer"
                                       class="flex items-center justify-between p-3 rounded-xl bg-white border border-slate-200 hover:border-red-300 hover:bg-red-50/30 transition group">
                                        <div class="flex items-center gap-2.5">
                                            <div class="h-7 w-7 rounded-lg bg-red-50 text-red-600 flex items-center justify-center">
                                                <i data-lucide="book-open" class="h-4 w-4"></i>
                                            </div>
                                            <div>
                                                <p class="text-xs font-bold text-slate-900 group-hover:text-red-700">Baidu Baike (百度百科)</p>
                                                <p class="text-[11px] text-slate-400">Bách khoa toàn thư tiếng Trung</p>
                                            </div>
                                        </div>
                                        <i data-lucide="external-link" class="h-3.5 w-3.5 text-slate-400 group-hover:text-red-600"></i>
                                    </a>
                                </div>
                            </div>

                            <div class="pt-2 border-t border-slate-100 flex items-center justify-between gap-3">
                                <button type="button"
                                        @click="openHanziWriter(activeWord.hanzi)"
                                        class="flex-1 inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-4 py-2.5 text-xs font-bold text-white transition hover:bg-slate-800">
                                    <i data-lucide="pen-tool" class="h-3.5 w-3.5 text-amber-300"></i>
                                    Tập viết nét chữ
                                </button>
                                <button type="button"
                                        @click="speakCurrent()"
                                        class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-bold text-slate-700 transition hover:bg-slate-50">
                                    <i data-lucide="volume-2" class="h-3.5 w-3.5 text-red-600"></i>
                                    Phát âm AI
                                </button>
                            </div>
                        </div>

                        {{-- Fallback Case B: When NO Hanzi could be determined at all --}}
                        <div x-show="!activeWord.hanzi" class="space-y-6">
                            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                                <span class="rounded-xl bg-slate-100 border border-slate-200 px-3 py-1 text-xs font-bold text-slate-600">
                                    Chưa có trong từ điển HSK
                                </span>
                                <span class="text-xs text-slate-400" x-text="activeWord.detected_type === 'vietnamese' ? 'Tiếng Việt' : 'Pinyin'"></span>
                            </div>

                            <div class="py-6 text-center space-y-2">
                                <h2 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight"
                                    x-text="activeWord.query"></h2>
                                <p class="text-xs text-slate-500 max-w-xs mx-auto">
                                    Từ khóa này chưa nằm trong bộ dữ liệu từ vựng HSK 1-6 đã biên soạn.
                                </p>
                            </div>

                            <div class="rounded-2xl bg-amber-50/70 border border-amber-200/70 p-4 space-y-3">
                                <p class="text-xs font-bold uppercase tracking-wider text-amber-900">Giải pháp tra cứu nhanh:</p>
                                <p class="text-xs text-slate-600 leading-relaxed">
                                    Bạn có thể dịch từ khóa này sang Chữ Hán qua Google Dịch rồi quay lại đây để luyện phát âm, viết nét và xem video YouGlish nhé!
                                </p>
                                <a :href="'https://translate.google.com/?sl=auto&tl=zh-CN&text=' + encodeURIComponent(activeWord.query || '') + '&op=translate'"
                                   target="_blank"
                                   rel="noopener noreferrer"
                                   class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-[#991b1b] px-4 py-2.5 text-xs font-bold text-white hover:bg-[#7f1717] transition shadow-sm">
                                    <i data-lucide="languages" class="h-4 w-4"></i>
                                    <span>Dịch sang Chữ Hán trên Google Dịch</span>
                                    <i data-lucide="external-link" class="h-3.5 w-3.5"></i>
                                </a>
                            </div>
                        </div>

                    </div>

                </div>

                {{-- ── RIGHT COLUMN: YOUGLISH VIDEO CONTEXT (7 COLS) ── --}}
                <div class="lg:col-span-7 space-y-6">
                    
                    {{-- 1. Full YouGlish Video Container (When Hanzi is present) --}}
                    <div x-show="activeWord.hanzi" class="rounded-3xl border border-slate-200/80 bg-white p-6 sm:p-8 shadow-sm space-y-4">
                        
                        {{-- Video Header --}}
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-4">
                            <div>
                                <div class="inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-red-700">
                                    <i data-lucide="video" class="h-4 w-4"></i>
                                    Video Ngữ Cảnh YouGlish Chinese
                                </div>
                                <h3 class="text-xl font-black text-slate-900 mt-0.5">
                                    Nghe người bản xứ phát âm từ <span class="text-[#991b1b]" x-text="activeWord.hanzi"></span>
                                    <span class="text-xs text-slate-500 font-normal ml-1"
                                          x-show="activeWord.query && activeWord.query !== activeWord.hanzi"
                                          x-text="'(' + activeWord.query + ')'"></span>
                                </h3>
                            </div>

                            <div class="flex items-center gap-2">
                                {{-- Direct Open on YouGlish External Tab --}}
                                <a :href="'https://youglish.com/pronounce/' + encodeURIComponent(activeWord ? activeWord.hanzi : '') + '/chinese'"
                                   target="_blank"
                                   rel="noopener noreferrer"
                                   class="inline-flex items-center gap-1 rounded-xl border border-slate-200 bg-slate-50 px-3 py-1.5 text-xs font-semibold text-slate-600 hover:text-[#991b1b] hover:bg-red-50 transition"
                                   title="Mở video trực tiếp trên tab mới của YouGlish">
                                    <span>Mở tab mới</span>
                                    <i data-lucide="external-link" class="h-3.5 w-3.5"></i>
                                </a>

                                {{-- Track Counter --}}
                                <div x-show="ygActivated && !ygLoading && !ygError && ygTotal > 0" class="shrink-0">
                                    <span class="rounded-full bg-slate-100 border border-slate-200 px-3 py-1 text-xs font-bold text-slate-700">
                                        Clip <span class="text-red-700" x-text="ygCurrent"></span> / <span x-text="ygTotal"></span>
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Video Player Container --}}
                        <div class="relative w-full rounded-2xl overflow-hidden bg-slate-950 border border-slate-800 shadow-inner">
                            
                            {{-- Click-to-Play Poster State (Prevents Cloudflare Bot Challenges & Speeds Up Page) --}}
                            <div x-show="!ygActivated" class="min-h-[260px] sm:min-h-[340px] flex flex-col items-center justify-center p-6 sm:p-10 text-center space-y-4 bg-gradient-to-br from-slate-950 via-slate-900 to-red-950/30 text-white">
                                <div class="relative group cursor-pointer" @click="startVideo()">
                                    <div class="h-16 w-16 rounded-full bg-red-600 text-white flex items-center justify-center shadow-lg shadow-red-900/50 transition transform group-hover:scale-110 group-hover:bg-red-500">
                                        <i data-lucide="play" class="h-7 w-7 fill-white ml-1"></i>
                                    </div>
                                    <div class="absolute -inset-2 rounded-full bg-red-600/20 blur-md -z-10 animate-pulse"></div>
                                </div>

                                <div class="space-y-1">
                                    <h4 class="text-base sm:text-lg font-black text-white">
                                        Xem video người bản xứ phát âm: <span class="text-amber-300 font-chinese text-2xl" x-text="activeWord ? activeWord.hanzi : ''"></span>
                                        <span class="text-xs text-amber-200/80 font-normal ml-1"
                                              x-show="activeWord && activeWord.query && activeWord.query !== activeWord.hanzi"
                                              x-text="'(' + activeWord.query + ')'"></span>
                                    </h4>
                                    <p class="text-xs sm:text-sm text-slate-300 max-w-md mx-auto leading-relaxed">
                                        Trích đoạn từ phim ảnh, show thực tế và tin tức có câu thoại chứa từ này kèm phụ đề tiếng Trung.
                                    </p>
                                </div>

                                <div class="flex flex-wrap items-center justify-center gap-3 pt-1">
                                    <button type="button"
                                            @click="startVideo()"
                                            class="inline-flex items-center gap-2 rounded-xl bg-red-600 px-5 py-2.5 text-xs font-bold text-white transition hover:bg-red-700 shadow-md shadow-red-950/20">
                                        <i data-lucide="play" class="h-3.5 w-3.5 fill-white"></i>
                                        Bật video YouGlish
                                    </button>
                                    <a :href="'https://youglish.com/pronounce/' + encodeURIComponent(activeWord ? activeWord.hanzi : '') + '/chinese'"
                                       target="_blank"
                                       rel="noopener noreferrer"
                                       class="inline-flex items-center gap-1.5 rounded-xl border border-white/20 bg-white/10 px-4 py-2.5 text-xs font-semibold text-slate-200 hover:bg-white/20 hover:text-white transition">
                                        <span>Xem trên YouGlish</span>
                                        <i data-lucide="external-link" class="h-3.5 w-3.5"></i>
                                    </a>
                                </div>
                            </div>

                            {{-- Loading Overlay --}}
                            <div x-show="ygActivated && ygLoading" class="min-h-[260px] sm:min-h-[340px] flex flex-col items-center justify-center p-8 text-center text-slate-400 space-y-3">
                                <i data-lucide="loader-2" class="h-8 w-8 animate-spin text-amber-400"></i>
                                <p class="text-sm font-semibold">Đang tìm các đoạn phim người bản xứ phát âm "<span x-text="activeWord.hanzi"></span>"...</p>
                                <p class="text-xs text-slate-500">Hệ thống đang đồng bộ video và phụ đề</p>
                            </div>

                            {{-- Error / No Video Fallback --}}
                            <div x-show="ygActivated && !ygLoading && ygError" x-cloak class="min-h-[260px] sm:min-h-[320px] flex flex-col items-center justify-center p-8 text-center space-y-3 bg-slate-900/90 text-white">
                                <div class="h-12 w-12 rounded-2xl bg-amber-400/10 border border-amber-400/20 text-amber-400 flex items-center justify-center">
                                    <i data-lucide="video-off" class="h-6 w-6"></i>
                                </div>
                                <h4 class="text-base font-bold">Chưa tìm thấy video hoặc mạng bị chặn</h4>
                                <p class="text-xs text-slate-400 max-w-md leading-relaxed">
                                    Nếu có bảng xác minh Cloudflare, cậu hãy tích xác nhận hoặc bấm mở trực tiếp trên tab mới của YouGlish nhé!
                                </p>
                                <div class="flex items-center gap-2 pt-1">
                                    <a :href="'https://youglish.com/pronounce/' + encodeURIComponent(activeWord ? activeWord.hanzi : '') + '/chinese'"
                                       target="_blank"
                                       rel="noopener noreferrer"
                                       class="inline-flex items-center gap-1.5 rounded-xl bg-white/10 border border-white/20 px-4 py-2 text-xs font-bold text-white hover:bg-white/20 transition">
                                        <span>Xem trên YouGlish</span>
                                        <i data-lucide="external-link" class="h-3.5 w-3.5"></i>
                                    </a>
                                    <button type="button"
                                            @click="speakCurrent()"
                                            class="inline-flex items-center gap-2 rounded-xl bg-red-600 px-4 py-2 text-xs font-bold text-white hover:bg-red-700 transition">
                                        <i data-lucide="volume-2" class="h-4 w-4"></i>
                                        Nghe giọng AI
                                    </button>
                                </div>
                            </div>

                            {{-- The actual YouGlish iframe target div --}}
                            <div id="youglish-container" class="w-full" x-show="ygActivated && !ygError"></div>
                        </div>

                        {{-- Custom YouGlish Learning Control Bar --}}
                        <div x-show="ygActivated && !ygLoading && !ygError && ygTotal > 0" class="space-y-3 pt-1">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                
                                {{-- Skip -3s / Replay / Skip +3s --}}
                                <div class="inline-flex items-center gap-1.5 bg-slate-100 p-1 rounded-xl border border-slate-200 text-xs font-bold text-slate-700">
                                    <button type="button"
                                            @click="moveYG(-3)"
                                            class="px-2.5 py-1.5 rounded-lg hover:bg-white transition flex items-center gap-1"
                                            title="Tua lùi 3 giây">
                                        <i data-lucide="rewind" class="h-3.5 w-3.5"></i>
                                        -3s
                                    </button>
                                    <button type="button"
                                            @click="replayYG()"
                                            class="px-3 py-1.5 rounded-lg bg-slate-900 text-white hover:bg-slate-800 transition flex items-center gap-1"
                                            title="Nghe lại câu này từ đầu">
                                        <i data-lucide="rotate-ccw" class="h-3.5 w-3.5 text-amber-300"></i>
                                        Nghe lại câu
                                    </button>
                                    <button type="button"
                                            @click="moveYG(3)"
                                            class="px-2.5 py-1.5 rounded-lg hover:bg-white transition flex items-center gap-1"
                                            title="Tua tới 3 giây">
                                        +3s
                                        <i data-lucide="fast-forward" class="h-3.5 w-3.5"></i>
                                    </button>
                                </div>

                                {{-- Playback Speed Selector --}}
                                <div class="inline-flex items-center gap-1 bg-slate-100 p-1 rounded-xl border border-slate-200 text-xs font-bold text-slate-600">
                                    <span class="text-[10px] text-slate-400 px-1">Tốc độ:</span>
                                    <button type="button"
                                            @click="setSpeedYG(0.75)"
                                            :class="ygSpeed === 0.75 ? 'bg-amber-400 text-slate-950 font-black shadow-sm' : 'hover:bg-white'"
                                            class="px-2 py-1 rounded-lg transition">0.75x</button>
                                    <button type="button"
                                            @click="setSpeedYG(1.0)"
                                            :class="ygSpeed === 1.0 ? 'bg-amber-400 text-slate-950 font-black shadow-sm' : 'hover:bg-white'"
                                            class="px-2 py-1 rounded-lg transition">1.0x</button>
                                    <button type="button"
                                            @click="setSpeedYG(1.25)"
                                            :class="ygSpeed === 1.25 ? 'bg-amber-400 text-slate-950 font-black shadow-sm' : 'hover:bg-white'"
                                            class="px-2 py-1 rounded-lg transition">1.25x</button>
                                </div>

                                {{-- Prev / Next Clip --}}
                                <div class="flex items-center gap-1.5">
                                    <button type="button"
                                            @click="prevYG()"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl border border-slate-200 bg-white text-xs font-bold text-slate-700 hover:bg-slate-50 transition">
                                        <i data-lucide="chevron-left" class="h-4 w-4"></i>
                                        Clip trước
                                    </button>
                                    <button type="button"
                                            @click="nextYG()"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-red-600 text-xs font-bold text-white hover:bg-red-700 transition shadow-sm">
                                        Clip kế tiếp
                                        <i data-lucide="chevron-right" class="h-4 w-4"></i>
                                    </button>
                                </div>

                            </div>

                            <p class="text-[11px] text-slate-400 text-center sm:text-left flex items-center justify-between pt-1">
                                <span>Powered by <a href="https://youglish.com" target="_blank" rel="noopener" class="font-semibold text-slate-600 hover:text-red-700 underline">YouGlish.com</a> • Phụ đề tiếng Trung tự động</span>
                                <span class="hidden sm:inline">Phím tắt: Space (Dừng/Phát)</span>
                            </p>
                        </div>
                    </div>

                    {{-- 2. Non-Hanzi Guidance Card (When user searches Vietnamese / Latin not found in HSK) --}}
                    <div x-show="!activeWord.hanzi" class="rounded-3xl border border-slate-200/80 bg-white p-8 sm:p-12 shadow-sm text-center space-y-5">
                        <div class="h-16 w-16 mx-auto rounded-3xl bg-amber-500/10 border border-amber-500/20 text-amber-700 flex items-center justify-center">
                            <i data-lucide="video" class="h-8 w-8"></i>
                        </div>
                        <div class="max-w-md mx-auto space-y-2">
                            <h3 class="text-lg font-black text-slate-900">Tính năng Video cần từ khóa Chữ Hán</h3>
                            <p class="text-xs sm:text-sm text-slate-500 leading-relaxed">
                                YouGlish Chinese đối soát câu thoại thực tế dựa trên Chữ Hán giản thể hoặc phồn thể. Bạn hãy tra một từ vựng tiếng Trung hoặc bấm vào các từ gợi ý bên dưới để xem video người bản xứ nhé!
                            </p>
                        </div>
                        <div class="pt-2">
                            <a :href="'https://translate.google.com/?sl=auto&tl=zh-CN&text=' + encodeURIComponent(activeWord.query || '') + '&op=translate'"
                               target="_blank"
                               rel="noopener noreferrer"
                               class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-5 py-2.5 text-xs font-bold text-white hover:bg-slate-800 transition shadow-sm">
                                <i data-lucide="languages" class="h-4 w-4 text-amber-300"></i>
                                <span>Tra chữ Hán tương đương trên Google Dịch</span>
                                <i data-lucide="external-link" class="h-3.5 w-3.5"></i>
                            </a>
                        </div>
                    </div>

                </div>

            </div>

            {{-- ══ 3. PERSONAL CONTEXT: XUẤT HIỆN TRONG BÀI ĐỌC CỦA BẠN ══ --}}
            <div class="rounded-3xl border border-slate-200/80 bg-white p-6 sm:p-8 shadow-sm space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-4">
                    <div>
                        <div class="inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-amber-700">
                            <i data-lucide="book-open-check" class="h-4 w-4"></i>
                            Ngữ cảnh học tập cá nhân hóa
                        </div>
                        <h3 class="text-xl font-black text-slate-900 mt-0.5">
                            Xuất hiện trong các bài đọc của bạn
                        </h3>
                    </div>

                    <template x-if="activeWord.story_count > 0">
                        <span class="rounded-full bg-emerald-100 border border-emerald-200 px-3 py-1 text-xs font-bold text-emerald-900">
                            Tìm thấy <span x-text="activeWord.story_count"></span> câu hội thoại
                        </span>
                    </template>
                </div>

                {{-- Matches List --}}
                <template x-if="activeWord.story_count > 0">
                    <div class="grid gap-4 sm:grid-cols-2">
                        <template x-for="(match, mIdx) in activeWord.story_matches" :key="mIdx">
                            <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-5 space-y-3 transition hover:bg-white hover:border-amber-300/80 hover:shadow-md group">
                                <div class="flex items-center justify-between gap-2">
                                    <div class="flex items-center gap-2">
                                        <span class="rounded-lg bg-red-100 px-2 py-0.5 text-[10px] font-black text-[#991b1b] uppercase" x-text="'HSK ' + match.hsk_level"></span>
                                        <h4 class="text-xs font-bold text-slate-800 line-clamp-1 group-hover:text-[#991b1b] transition" x-text="match.story_title_vi"></h4>
                                    </div>
                                    <span class="text-[10px] text-slate-400 font-semibold" x-text="'Câu #' + match.sentence_index"></span>
                                </div>

                                <div class="space-y-1">
                                    <p class="text-base font-bold text-slate-900 font-chinese" x-text="match.chinese"></p>
                                    <p class="text-xs font-medium text-amber-700 font-mono" x-text="match.pinyin"></p>
                                    <p class="text-xs text-slate-600 pt-1 border-t border-slate-200/50" x-text="match.vietnamese"></p>
                                </div>

                                <div class="pt-2 flex items-center justify-between">
                                    <button type="button"
                                            @click="speakText(match.chinese)"
                                            class="text-xs font-bold text-slate-500 hover:text-[#991b1b] transition flex items-center gap-1">
                                        <i data-lucide="volume-2" class="h-3.5 w-3.5"></i>
                                        Nghe câu này
                                    </button>
                                    <a :href="'{{ url('/stories') }}/' + match.story_slug"
                                       class="inline-flex items-center gap-1 text-xs font-bold text-red-700 hover:text-red-800 transition">
                                        Đọc cả bài
                                        <i data-lucide="arrow-right" class="h-3.5 w-3.5"></i>
                                    </a>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>

                {{-- No Matches in Library State --}}
                <template x-if="!activeWord.story_count || activeWord.story_count === 0">
                    <div class="rounded-2xl border border-dashed border-slate-200 p-8 text-center space-y-2 text-slate-500">
                        <i data-lucide="book-open" class="h-8 w-8 mx-auto text-slate-300"></i>
                        <p class="text-sm font-semibold text-slate-700">Từ này chưa xuất hiện trong các bài đọc hiện có</p>
                        <p class="text-xs text-slate-400 max-w-md mx-auto leading-relaxed">
                            Cậu có thể luyện nghe từ này qua Video YouGlish và luyện viết nét chữ ở trên. Các bài đọc mới sẽ liên tục được cập nhật thêm!
                        </p>
                        <a href="{{ route('stories.index') }}" class="inline-flex items-center gap-1 text-xs font-bold text-red-700 hover:underline pt-2">
                            Khám phá thư viện bài đọc HSK →
                        </a>
                    </div>
                </template>
            </div>

            {{-- ══ 4. GỢI Ý TỪ VỰNG HSK LIÊN QUAN (KHI TRA TỪ MỞ RỘNG / FALLBACK) ══ --}}
            <template x-if="activeWord.is_fallback && activeWord.related_words && activeWord.related_words.length > 0">
                <div class="rounded-3xl border border-slate-200/80 bg-white p-6 sm:p-8 shadow-sm space-y-5">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-4">
                        <div>
                            <div class="inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-amber-700">
                                <i data-lucide="sparkles" class="h-4 w-4"></i>
                                Gợi ý mở rộng
                            </div>
                            <h3 class="text-xl font-black text-slate-900 mt-0.5">
                                Các từ vựng HSK có thể bạn quan tâm
                            </h3>
                        </div>
                        <span class="text-xs text-slate-400 font-medium">Bấm vào thẻ bất kỳ để tra nghĩa & xem video</span>
                    </div>

                    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                        <template x-for="rel in activeWord.related_words" :key="rel.id">
                            <div @click="selectWord(rel)"
                                 class="cursor-pointer rounded-2xl border border-slate-200 bg-slate-50/60 p-4 transition-all hover:bg-white hover:border-amber-300 hover:shadow-md group flex items-start justify-between gap-3">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <span class="text-2xl font-black text-slate-900 font-chinese group-hover:text-[#991b1b] transition" x-text="rel.hanzi"></span>
                                        <span class="rounded-md bg-amber-100/80 px-2 py-0.5 text-[10px] font-bold text-amber-900 font-mono" x-text="rel.pinyin"></span>
                                    </div>
                                    <p class="text-xs text-slate-600 line-clamp-1 font-medium" x-text="rel.meaning"></p>
                                </div>
                                <span class="shrink-0 rounded-lg bg-white border border-slate-200 px-2 py-1 text-[10px] font-bold text-slate-600 uppercase shadow-2xs"
                                      x-text="'HSK ' + rel.hsk_level"></span>
                            </div>
                        </template>
                    </div>
                </div>
            </template>

        </div>
    </template>

    {{-- ══ 4. HANZI WRITER PRACTICE MODAL ══ --}}
    <div x-show="showWriterModal"
         x-cloak
         style="display: none;"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4 backdrop-blur-sm">
        <div @click.outside="showWriterModal = false"
             class="relative w-full max-w-sm rounded-[2rem] bg-white p-6 shadow-2xl text-center space-y-4">
            
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h4 class="text-sm font-bold uppercase tracking-wider text-slate-900">
                    Tập viết nét: <span class="text-2xl text-[#991b1b] font-chinese font-black" x-text="activeWord ? activeWord.hanzi : ''"></span>
                </h4>
                <button type="button" @click="showWriterModal = false" class="p-1 text-slate-400 hover:text-slate-600 transition">
                    <i data-lucide="x" class="h-5 w-5"></i>
                </button>
            </div>

            {{-- Writer Canvas Target --}}
            <div class="flex justify-center">
                <div id="dict-hanzi-writer-box" class="h-[220px] w-[220px] border-2 border-dashed border-red-200 rounded-2xl bg-amber-50/40 flex items-center justify-center shadow-inner"></div>
            </div>

            {{-- Controls --}}
            <div class="flex items-center justify-center gap-2 pt-2">
                <button type="button" @click="animateWriter()" class="px-4 py-2 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-slate-800 transition flex items-center gap-1.5">
                    <i data-lucide="play" class="h-3.5 w-3.5 text-amber-300"></i>
                    Viết mẫu
                </button>
                <button type="button" @click="quizWriter()" class="px-4 py-2 rounded-xl bg-red-600 text-white text-xs font-bold hover:bg-red-700 transition flex items-center gap-1.5 shadow-sm">
                    <i data-lucide="pen-tool" class="h-3.5 w-3.5"></i>
                    Tập tô nét
                </button>
            </div>
        </div>
    </div>

</div>
@endsection
