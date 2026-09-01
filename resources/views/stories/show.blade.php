@extends('layouts.app')

@section('content')
<script>
window.gradedReaderApp = function() {
    return {
        showPinyin: true,
        showTranslation: true,
        fontSizes: ['text-base', 'text-lg', 'text-xl', 'text-2xl'],
        fontSizeIndex: 1,
        playbackSpeed: 1.0,
        isPlayingAll: false,
        currentPlayingSentenceIndex: -1,
        
        // Word Lookup State
        lookupVisible: false,
        activeWordHanzi: null,
        popoverPos: { x: 0, y: 0 },
        currentWord: {
            id: null,
            hanzi: '',
            pinyin: '',
            meaning: '',
            hsk_level: {{ $story->hsk_level }},
            is_starred: false
        },
        starredList: @json($starredCharacters),

        // Quiz State
        quizAnswers: {},
        quizSubmitted: false,
        quizScore: 0,
        isCompleted: {{ $story->isCompletedBy() ? 'true' : 'false' }},

        // Hanzi Writer State
        showWriterModal: false,
        writerInstance: null,

        get fontSizeClass() {
            return this.fontSizes[this.fontSizeIndex];
        },

        get fontSizeLabel() {
            return ['Nhỏ', 'Vừa', 'Lớn', 'Cực đại'][this.fontSizeIndex];
        },

        init() {
            if (window.refreshIcons) window.refreshIcons();
        },

        increaseFontSize() {
            if (this.fontSizeIndex < this.fontSizes.length - 1) {
                this.fontSizeIndex++;
            }
        },

        decreaseFontSize() {
            if (this.fontSizeIndex > 0) {
                this.fontSizeIndex--;
            }
        },

        cycleSpeed() {
            const speeds = [0.75, 1.0, 1.25];
            const currentIdx = speeds.indexOf(this.playbackSpeed);
            this.playbackSpeed = speeds[(currentIdx + 1) % speeds.length];
        },

        openLookup(event, word) {
            const rect = event.currentTarget.getBoundingClientRect();
            const popoverWidth = window.innerWidth < 640 ? 280 : 320;
            
            // Smart calculate horizontal X position to prevent offscreen overflow
            let posX = rect.left + (rect.width / 2) - (popoverWidth / 2);
            if (posX < 12) posX = 12;
            if (posX + popoverWidth > window.innerWidth - 12) {
                posX = window.innerWidth - popoverWidth - 12;
            }

            // Smart calculate vertical Y position (prefer above, fallback below)
            let posY = rect.top - 170;
            if (posY < 70) {
                posY = rect.bottom + 12;
            }

            this.popoverPos = { x: posX, y: posY };
            this.activeWordHanzi = word.hanzi;
            this.currentWord = {
                id: null,
                hanzi: word.hanzi,
                pinyin: word.pinyin || '',
                meaning: word.meaning || '',
                hsk_level: {{ $story->hsk_level }},
                is_starred: this.starredList.includes(word.hanzi)
            };
            this.lookupVisible = true;

            // Fetch dynamic backend dictionary metadata & flashcard ID if meaning missing
            fetch('{{ route("stories.lookup") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ character: word.hanzi })
            })
            .then(res => res.json())
            .then(data => {
                if (data.found) {
                    this.currentWord.id = data.id;
                    if (!this.currentWord.meaning) this.currentWord.meaning = data.meaning;
                    if (!this.currentWord.pinyin) this.currentWord.pinyin = data.pinyin;
                    this.currentWord.hsk_level = data.hsk_level;
                    this.currentWord.is_starred = data.is_starred;
                }
            })
            .catch(() => {});

            this.$nextTick(() => window.refreshIcons && window.refreshIcons());
        },

        closeLookup() {
            this.lookupVisible = false;
            this.activeWordHanzi = null;
        },

        speakWord(text) {
            if (!text) return;
            fetch('{{ route("tts.generate") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ text: text })
            })
            .then(res => res.json())
            .then(data => {
                if (data.audio_url) {
                    const audio = new Audio(data.audio_url);
                    audio.playbackRate = this.playbackSpeed;
                    audio.play();
                } else {
                    this.fallbackWebSpeech(text);
                }
            })
            .catch(() => {
                this.fallbackWebSpeech(text);
            });
        },

        fallbackWebSpeech(text) {
            if ('speechSynthesis' in window) {
                window.speechSynthesis.cancel();
                const utter = new SpeechSynthesisUtterance(text);
                utter.lang = 'zh-CN';
                utter.rate = this.playbackSpeed;
                window.speechSynthesis.speak(utter);
            }
        },

        playSentence(idx, text) {
            this.currentPlayingSentenceIndex = idx;
            this.speakWord(text);
            setTimeout(() => {
                if (!this.isPlayingAll) {
                    this.currentPlayingSentenceIndex = -1;
                }
            }, 3500);
        },

        togglePlayAll() {
            if (this.isPlayingAll) {
                this.isPlayingAll = false;
                this.currentPlayingSentenceIndex = -1;
                if ('speechSynthesis' in window) window.speechSynthesis.cancel();
                this.$nextTick(() => window.refreshIcons && window.refreshIcons());
                return;
            }

            this.isPlayingAll = true;
            this.$nextTick(() => window.refreshIcons && window.refreshIcons());

            const sentences = @json(array_column($story->content_json, 'chinese'));
            let current = 0;

            const playNext = () => {
                if (!this.isPlayingAll || current >= sentences.length) {
                    this.isPlayingAll = false;
                    this.currentPlayingSentenceIndex = -1;
                    this.$nextTick(() => window.refreshIcons && window.refreshIcons());
                    return;
                }

                this.currentPlayingSentenceIndex = current;
                const block = document.getElementById('sentence-block-' + current);
                if (block) block.scrollIntoView({ behavior: 'smooth', block: 'nearest' });

                this.speakWord(sentences[current]);
                current++;
                setTimeout(playNext, 4000 / this.playbackSpeed);
            };

            playNext();
        },

        toggleStarWord() {
            if (!this.currentWord.id) {
                // If not in DB yet, toggle locally
                this.currentWord.is_starred = !this.currentWord.is_starred;
                return;
            }

            fetch('{{ route("flashcards.toggleStar") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ flashcard_id: this.currentWord.id })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    this.currentWord.is_starred = data.is_starred;
                    if (data.is_starred) {
                        this.starredList.push(this.currentWord.hanzi);
                    } else {
                        this.starredList = this.starredList.filter(c => c !== this.currentWord.hanzi);
                    }
                }
            })
            .catch(() => {});
        },

        openHanziWriter(hanzi) {
            const firstChar = hanzi ? hanzi.charAt(0) : '字';
            this.showWriterModal = true;
            this.closeLookup();

            this.$nextTick(() => {
                const target = document.getElementById('story-hanzi-writer-box');
                if (!target || !window.HanziWriter) return;
                target.innerHTML = '';
                this.writerInstance = window.HanziWriter.create(target, firstChar, {
                    width: 180,
                    height: 180,
                    padding: 10,
                    showOutline: true,
                    strokeColor: '#991b1b',
                    outlineColor: '#cbd5e1'
                });
                this.writerInstance.animateCharacter();
            });
        },

        animateWriter() {
            if (this.writerInstance) this.writerInstance.animateCharacter();
        },

        quizWriter() {
            if (this.writerInstance) this.writerInstance.quiz();
        },

        submitQuiz() {
            const quizList = @json($story->quiz_json ?? []);
            if (!quizList || quizList.length === 0) return;

            let correctCount = 0;
            quizList.forEach((q, idx) => {
                if (this.quizAnswers[idx] && this.quizAnswers[idx].trim() === q.correct_answer.trim()) {
                    correctCount++;
                }
            });

            this.quizScore = Math.round((correctCount / quizList.length) * 100);
            this.quizSubmitted = true;
            this.isCompleted = true;

            // Save completion to Backend
            fetch('{{ route("stories.complete", $story->id) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ quiz_score: this.quizScore })
            }).catch(() => {});
        },

        resetQuiz() {
            this.quizAnswers = {};
            this.quizSubmitted = false;
            this.quizScore = 0;
        },

        markCompletedOnly() {
            this.isCompleted = true;
            fetch('{{ route("stories.complete", $story->id) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ quiz_score: 100 })
            }).catch(() => {});
        }
    };
};
</script>

<div x-data="gradedReaderApp()" x-init="init()" class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6 relative" @click="closeLookup()">


    {{-- ══ 1. TOP TOOLBAR & BREADCRUMB ══ --}}
    <div class="relative bg-white rounded-2xl p-3.5 sm:p-4 shadow-sm border border-slate-200 mb-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            
            {{-- Back button & Story Title --}}
            <div class="flex items-center gap-3 min-w-0">
                <a href="{{ route('stories.index') }}"
                   class="inline-flex items-center justify-center h-9 w-9 rounded-xl bg-slate-100 text-slate-600 hover:bg-slate-200 transition"
                   title="Quay lại danh sách bài đọc">
                    <i data-lucide="arrow-left" class="h-4 w-4"></i>
                </a>
                
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-0.5 rounded-md text-[11px] font-black {{ $story->hsk_badge_bg }} border">
                            HSK {{ $story->hsk_level }}
                        </span>
                        <h1 class="text-sm sm:text-base font-extrabold text-slate-900 truncate">
                            {{ $story->title }}
                        </h1>
                    </div>
                    <div class="text-xs text-slate-500 truncate mt-0.5">
                        {{ $story->title_vi }}
                    </div>
                </div>
            </div>

            {{-- Interactive Reading Toggles --}}
            <div class="flex flex-wrap items-center gap-1.5 sm:gap-2">
                
                {{-- Toggle Pinyin --}}
                <button type="button" @click="showPinyin = !showPinyin"
                        :class="showPinyin ? 'bg-red-50 text-red-700 border-red-300 font-bold' : 'bg-slate-100 text-slate-500 border-slate-200 font-medium'"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs border transition shadow-sm"
                        title="Bật/Tắt Pinyin trên đầu chữ Hán">
                    <i data-lucide="languages" class="h-3.5 w-3.5"></i>
                    <span x-text="showPinyin ? 'Pinyin: Bật' : 'Pinyin: Tắt'"></span>
                </button>

                {{-- Toggle Translation --}}
                <button type="button" @click="showTranslation = !showTranslation"
                        :class="showTranslation ? 'bg-amber-50 text-amber-800 border-amber-300 font-bold' : 'bg-slate-100 text-slate-500 border-slate-200 font-medium'"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs border transition shadow-sm"
                        title="Bật/Tắt dịch nghĩa tiếng Việt">
                    <i data-lucide="message-square-text" class="h-3.5 w-3.5"></i>
                    <span x-text="showTranslation ? 'Dịch: Bật' : 'Dịch: Tắt'"></span>
                </button>

                {{-- Font Size Zoom --}}
                <div class="inline-flex items-center bg-slate-100 rounded-xl p-0.5 border border-slate-200 text-xs font-bold text-slate-700">
                    <button type="button" @click="decreaseFontSize()" class="px-2 py-1 hover:bg-white rounded-lg transition" title="Giảm cỡ chữ">A-</button>
                    <span class="px-1.5 text-[11px] text-slate-400" x-text="fontSizeLabel"></span>
                    <button type="button" @click="increaseFontSize()" class="px-2 py-1 hover:bg-white rounded-lg transition" title="Tăng cỡ chữ">A+</button>
                </div>

                {{-- Audio Play Story Karaoke --}}
                <button type="button" @click="togglePlayAll()"
                        :class="isPlayingAll ? 'bg-red-600 text-white shadow-red-200' : 'bg-slate-900 text-white hover:bg-slate-800'"
                        class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-bold transition shadow-sm">
                    <i :data-lucide="isPlayingAll ? 'pause' : 'play'" class="h-3.5 w-3.5"></i>
                    <span x-text="isPlayingAll ? 'Tạm dừng' : 'Nghe toàn bài'"></span>
                </button>

                {{-- Playback Speed Selector --}}
                <button type="button" @click="cycleSpeed()"
                        class="px-2.5 py-1.5 rounded-xl text-xs font-bold bg-slate-100 text-slate-600 hover:bg-slate-200 border border-slate-200 transition"
                        title="Tốc độ phát âm thanh">
                    <span x-text="playbackSpeed + 'x'"></span>
                </button>
            </div>

        </div>
    </div>

    {{-- ══ 2. STORY READING CANVAS ══ --}}
    <div class="bg-white rounded-3xl p-6 sm:p-10 shadow-sm border border-slate-200 mb-8 relative">
        
        {{-- Story Title Header in Canvas --}}
        <div class="text-center pb-8 mb-8 border-b border-slate-100">
            <h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-wide">
                {{ $story->title }}
            </h2>
            <div class="text-sm font-semibold text-amber-700 mt-1">
                {{ $story->title_pinyin }}
            </div>
            <div class="text-sm font-medium text-slate-500 mt-0.5">
                {{ $story->title_vi }}
            </div>
            <div class="inline-flex items-center gap-3 mt-3 text-xs text-slate-400 font-semibold">
                <span><i data-lucide="tag" class="inline h-3 w-3 mr-0.5"></i> {{ $story->category }}</span>
                <span>•</span>
                <span><i data-lucide="file-text" class="inline h-3 w-3 mr-0.5"></i> {{ $story->word_count }} chữ Hán</span>
                <span>•</span>
                <span class="text-amber-600">💡 Mẹo: Bấm vào bất kỳ từ nào để tra nghĩa & nghe phát âm</span>
            </div>
        </div>

        {{-- Sentences List --}}
        <div class="space-y-6 sm:space-y-8">
            @foreach($story->content_json as $sIdx => $sentence)
                <div class="group relative rounded-2xl p-4 sm:p-5 transition-all duration-200 border border-transparent"
                     :class="{
                         'bg-amber-50/80 border-amber-200 shadow-sm': currentPlayingSentenceIndex === {{ $sIdx }},
                         'hover:bg-slate-50 hover:border-slate-100': currentPlayingSentenceIndex !== {{ $sIdx }}
                     }"
                     id="sentence-block-{{ $sIdx }}">
                    
                    {{-- Sentence Top Toolbar (Play Audio Single Sentence) --}}
                    <div class="flex items-center justify-between mb-2">
                        <button type="button" @click.stop="playSentence({{ $sIdx }}, '{{ addslashes($sentence['chinese']) }}')"
                                class="inline-flex items-center gap-1 text-xs font-bold text-slate-400 hover:text-red-600 transition"
                                title="Nghe đọc riêng câu này">
                            <i data-lucide="volume-2" class="h-4 w-4"></i>
                            <span class="text-[11px]">Câu {{ $sIdx + 1 }}</span>
                        </button>
                    </div>

                    {{-- Chinese Text with Word-by-Word Clickable Chips --}}
                    <div class="chinese-reading-line flex flex-wrap items-end gap-x-2 gap-y-3 leading-loose select-text"
                         :class="fontSizeClass">
                        @if(isset($sentence['words']) && is_array($sentence['words']) && count($sentence['words']) > 0)
                            @foreach($sentence['words'] as $wIdx => $word)
                                <span class="interactive-word inline-flex flex-col items-center cursor-pointer rounded-lg px-1.5 py-0.5 transition-all duration-150 group/word relative select-text"
                                      :class="activeWordHanzi === '{{ $word['hanzi'] }}' ? 'bg-red-100 text-red-900 ring-2 ring-red-400' : 'hover:bg-amber-100/80 hover:text-slate-900 text-slate-800'"
                                      @click.stop="openLookup($event, {{ json_encode($word) }})">
                                    {{-- Ruby Pinyin Annotation --}}
                                    <span x-show="showPinyin" x-cloak
                                          class="text-[11px] sm:text-xs font-semibold text-amber-700 tracking-normal pointer-events-none select-none -mb-1">
                                        {{ $word['pinyin'] ?? '' }}
                                    </span>
                                    {{-- Hanzi Character --}}
                                    <span class="font-medium tracking-wide">
                                        {{ $word['hanzi'] }}
                                    </span>
                                </span>
                            @endforeach
                        @else
                            @php
                                $chineseText = $sentence['chinese'] ?? '';
                                if (str_contains($chineseText, ' ')) {
                                    $tokens = array_filter(explode(' ', $chineseText));
                                } else {
                                    $tokens = preg_split('/(?<!^)(?!$)/u', $chineseText) ?: [];
                                }
                            @endphp
                            @foreach($tokens as $token)
                                @php $trimmed = trim($token); @endphp
                                @if(preg_match('/[\x{4e00}-\x{9fa5}]/u', $trimmed))
                                    <span class="interactive-word inline-flex flex-col items-center cursor-pointer rounded-lg px-1 py-0.5 transition-all duration-150 group/word relative select-text"
                                          :class="activeWordHanzi === '{{ $trimmed }}' ? 'bg-red-100 text-red-900 ring-2 ring-red-400' : 'hover:bg-amber-100/80 hover:text-slate-900 text-slate-800'"
                                          @click.stop="openLookup($event, { hanzi: '{{ $trimmed }}' })">
                                        <span class="font-medium tracking-wide">
                                            {{ $trimmed }}
                                        </span>
                                    </span>
                                @else
                                    <span class="font-normal text-slate-600 tracking-wide px-0.5">
                                        {{ $trimmed }}
                                    </span>
                                @endif
                            @endforeach
                        @endif
                    </div>

                    {{-- Full Sentence Pinyin (if words not pre-tokenized) --}}
                    @if((empty($sentence['words']) || count($sentence['words']) === 0) && !empty($sentence['pinyin']))
                        <div x-show="showPinyin" x-cloak class="mt-2 text-xs sm:text-sm font-semibold text-amber-800 tracking-wide">
                            {{ $sentence['pinyin'] }}
                        </div>
                    @endif

                    {{-- Vietnamese Translation --}}
                    <div x-show="showTranslation" x-cloak class="mt-2.5 pt-2 border-t border-slate-100 text-xs sm:text-sm text-slate-500 font-medium leading-relaxed">
                        {{ $sentence['vietnamese'] ?? '' }}
                    </div>

                </div>
            @endforeach
        </div>

    </div>

    {{-- ══ 3. COMPREHENSION CHECK MINI QUIZ ══ --}}
    @if(!empty($story->quiz_json) && count($story->quiz_json) > 0)
    <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200 mb-8" id="comprehension-quiz">
        <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
                <div class="h-9 w-9 rounded-xl bg-red-100 text-red-600 flex items-center justify-center">
                    <i data-lucide="help-circle" class="h-5 w-5"></i>
                </div>
                <div>
                    <h3 class="text-base font-extrabold text-slate-900">Kiểm tra độ hiểu bài (Comprehension Quiz)</h3>
                    <p class="text-xs text-slate-500">Trả lời các câu hỏi ngắn dưới đây để củng cố kiến thức và hoàn thành bài đọc</p>
                </div>
            </div>

            <div class="text-xs font-bold px-3 py-1 rounded-full bg-slate-100 text-slate-600">
                {{ count($story->quiz_json) }} câu hỏi
            </div>
        </div>

        <div class="space-y-6">
            @foreach($story->quiz_json as $qIdx => $q)
                <div class="p-4 sm:p-5 rounded-2xl bg-slate-50 border border-slate-200/80">
                    <div class="flex items-start gap-2">
                        <span class="inline-flex items-center justify-center h-6 w-6 rounded-lg bg-slate-900 text-white text-xs font-black shrink-0 mt-0.5">
                            {{ $qIdx + 1 }}
                        </span>
                        <div>
                            <div class="text-sm sm:text-base font-bold text-slate-900">
                                {{ $q['question'] }}
                            </div>
                            @if(!empty($q['pinyin']))
                                <div class="text-xs text-amber-700 font-medium mt-0.5">
                                    {{ $q['pinyin'] }}
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Options List --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 mt-4">
                        @foreach($q['options'] as $optIdx => $opt)
                            <label class="flex items-center gap-3 p-3 rounded-xl border text-xs sm:text-sm font-semibold cursor-pointer transition-all"
                                   :class="{
                                       'bg-white border-slate-200 hover:border-slate-300 text-slate-700': !quizSubmitted && quizAnswers[{{ $qIdx }}] !== '{{ addslashes($opt) }}',
                                       'bg-red-50 border-red-500 text-red-900 ring-2 ring-red-200': !quizSubmitted && quizAnswers[{{ $qIdx }}] === '{{ addslashes($opt) }}',
                                       'bg-emerald-50 border-emerald-500 text-emerald-900 font-bold': quizSubmitted && '{{ addslashes($opt) }}' === '{{ addslashes($q['correct_answer']) }}',
                                       'bg-red-50 border-red-500 text-red-900 line-through': quizSubmitted && quizAnswers[{{ $qIdx }}] === '{{ addslashes($opt) }}' && '{{ addslashes($opt) }}' !== '{{ addslashes($q['correct_answer']) }}',
                                       'opacity-50 pointer-events-none': quizSubmitted
                                   }">
                                <input type="radio" name="question_{{ $qIdx }}" value="{{ $opt }}"
                                       x-model="quizAnswers[{{ $qIdx }}]"
                                       :disabled="quizSubmitted"
                                       class="sr-only">
                                <span class="h-5 w-5 rounded-full border flex items-center justify-center text-[10px] font-bold"
                                      :class="{
                                          'border-slate-300 text-slate-500': quizAnswers[{{ $qIdx }}] !== '{{ addslashes($opt) }}',
                                          'border-red-600 bg-red-600 text-white': quizAnswers[{{ $qIdx }}] === '{{ addslashes($opt) }}'
                                      }">
                                    {{ chr(65 + $optIdx) }}
                                </span>
                                <span>{{ $opt }}</span>
                            </label>
                        @endforeach
                    </div>

                    {{-- Explanation after submission --}}
                    @if(!empty($q['explanation']))
                        <div x-show="quizSubmitted" x-cloak class="mt-3 p-3 rounded-xl bg-white border border-slate-200 text-xs text-slate-600">
                            <strong>💡 Giải thích:</strong> {{ $q['explanation'] }}
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        {{-- Submit Quiz Button --}}
        <div class="mt-6 flex flex-wrap items-center justify-between gap-4 pt-4 border-t border-slate-100">
            <div>
                <span x-show="quizSubmitted" x-cloak class="text-sm font-bold" :class="quizScore >= 70 ? 'text-emerald-600' : 'text-amber-600'">
                    Kết quả của bạn: <span x-text="quizScore + '%'"></span>
                </span>
            </div>

            <div class="flex items-center gap-3">
                <button type="button" @click="submitQuiz()"
                        x-show="!quizSubmitted"
                        class="px-6 py-2.5 rounded-xl bg-red-600 text-white text-xs sm:text-sm font-bold hover:bg-red-700 transition shadow-lg shadow-red-200">
                    Kiểm tra đáp án & Hoàn thành
                </button>

                <button type="button" @click="resetQuiz()"
                        x-show="quizSubmitted" x-cloak
                        class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 text-xs font-bold hover:bg-slate-200 transition">
                    Làm lại bài kiểm tra
                </button>
            </div>
        </div>

    </div>
    @else
        {{-- If no quiz, simple Mark Complete CTA --}}
        <div class="bg-white rounded-3xl p-6 text-center border border-slate-200 mb-8">
            <h3 class="text-base font-bold text-slate-800">Bạn đã đọc xong câu chuyện này chưa?</h3>
            <p class="text-xs text-slate-500 mt-1">Đánh dấu hoàn thành để ghi nhận thời gian học và chuỗi Streak của bạn.</p>
            <button type="button" @click="markCompletedOnly()"
                    :disabled="isCompleted"
                    :class="isCompleted ? 'bg-emerald-100 text-emerald-800' : 'bg-red-600 text-white hover:bg-red-700'"
                    class="mt-4 inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition shadow-md">
                <i :data-lucide="isCompleted ? 'check-circle' : 'circle-check'" class="h-4 w-4"></i>
                <span x-text="isCompleted ? 'Đã hoàn thành bài đọc ✓' : 'Đánh dấu hoàn thành bài đọc'"></span>
            </button>
        </div>
    @endif

    {{-- ══ 4. RELATED STORIES FOOTER ══ --}}
    @if($relatedStories->isNotEmpty())
        <div class="mb-10">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-bold text-slate-900">Bài đọc tiếp theo cùng cấp độ</h3>
                <a href="{{ route('stories.index', ['level' => $story->hsk_level]) }}" class="text-xs font-bold text-red-600 hover:underline">
                    Xem tất cả HSK {{ $story->hsk_level }} ↗
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                @foreach($relatedStories as $rel)
                    <a href="{{ route('stories.show', $rel->slug) }}"
                       class="bg-white p-4 rounded-2xl border border-slate-200 hover:border-red-300 hover:shadow-md transition group">
                        <div class="flex items-center gap-2 text-xs font-bold text-slate-400 mb-1">
                            <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700">HSK {{ $rel->hsk_level }}</span>
                            <span>•</span>
                            <span>{{ $rel->category }}</span>
                        </div>
                        <div class="font-bold text-sm text-slate-900 group-hover:text-red-600 transition truncate">
                            {{ $rel->title }}
                        </div>
                        <div class="text-xs text-slate-500 truncate mt-0.5">
                            {{ $rel->title_vi }}
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    {{-- ══ 5. FLOATING CLICK-TO-LOOKUP TOOLTIP / POPOVER ══ --}}
    <div x-show="lookupVisible" x-cloak
         @click.stop
         class="fixed z-50 bg-slate-900 text-white rounded-2xl p-4 shadow-2xl border border-slate-700 w-72 sm:w-80 backdrop-blur-xl transition-all duration-150 animate-in fade-in zoom-in-95"
         :style="`top: ${popoverPos.y}px; left: ${popoverPos.x}px;`">
        
        {{-- Close button --}}
        <button type="button" @click="closeLookup()" class="absolute right-3 top-3 text-slate-400 hover:text-white transition">
            <i data-lucide="x" class="h-4 w-4"></i>
        </button>

        {{-- Character & Pinyin Header --}}
        <div class="flex items-start justify-between pr-6">
            <div>
                <div class="text-2xl font-black tracking-wide text-white" x-text="currentWord?.hanzi || ''"></div>
                <div class="text-xs font-bold text-amber-400 mt-0.5" x-text="currentWord?.pinyin || ''"></div>
            </div>

            <div class="flex items-center gap-1.5">
                {{-- Play TTS Word audio --}}
                <button type="button" @click="speakWord(currentWord?.hanzi)"
                        class="h-8 w-8 rounded-xl bg-slate-800 hover:bg-slate-700 text-amber-400 flex items-center justify-center transition"
                        title="Phát âm từ này">
                    <i data-lucide="volume-2" class="h-4 w-4"></i>
                </button>

                {{-- Toggle Star / Save to favorites --}}
                <button type="button" @click="toggleStarWord()"
                        :class="currentWord?.is_starred ? 'bg-amber-400/20 text-amber-400 border border-amber-400/40' : 'bg-slate-800 text-slate-400 hover:text-white'"
                        class="h-8 w-8 rounded-xl flex items-center justify-center transition"
                        title="Lưu vào Sổ từ vựng yêu thích">
                    <i data-lucide="star" :class="currentWord?.is_starred ? 'fill-amber-400' : ''" class="h-4 w-4"></i>
                </button>
            </div>
        </div>

        {{-- Meaning --}}
        <div class="mt-3 pt-3 border-t border-slate-800">
            <div class="text-xs text-slate-400 font-medium">Nghĩa tiếng Việt:</div>
            <div class="text-sm font-bold text-slate-100 mt-0.5" x-text="currentWord?.meaning || 'Đang tra nghĩa...'"></div>
        </div>

        {{-- Practice writing character CTA --}}
        <div class="mt-3 pt-2 flex items-center justify-between text-xs">
            <span class="text-slate-400" x-text="'HSK ' + (currentWord?.hsk_level || '{{ $story->hsk_level }}')"></span>
            
            <button type="button" @click="openHanziWriter(currentWord?.hanzi)"
                    class="inline-flex items-center gap-1 text-amber-400 hover:underline font-bold">
                <i data-lucide="pen-tool" class="h-3 w-3"></i>
                <span>Tập viết chữ ↗</span>
            </button>
        </div>
    </div>

    {{-- ══ 6. HANZI WRITER PRACTICE MODAL ══ --}}
    <div x-show="showWriterModal" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
        <div class="bg-white rounded-3xl p-6 max-w-sm w-full shadow-2xl border border-slate-200 text-center relative"
             @click.stop>
            <button type="button" @click="showWriterModal = false" class="absolute right-4 top-4 text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="h-5 w-5"></i>
            </button>

            <h4 class="text-lg font-black text-slate-900">Tập Viết Chữ Hán</h4>
            <p class="text-xs text-slate-500 mt-0.5">Thứ tự nét chuẩn quốc tế</p>

            {{-- Hanzi Target Box --}}
            <div class="my-4 flex justify-center">
                <div id="story-hanzi-writer-box" class="h-[200px] w-[200px] border-2 border-dashed border-red-200 rounded-2xl bg-amber-50/50 flex items-center justify-center"></div>
            </div>

            {{-- Controls --}}
            <div class="flex items-center justify-center gap-2">
                <button type="button" @click="animateWriter()" class="px-3 py-1.5 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-slate-800 transition">
                    ▶ Viết mẫu
                </button>
                <button type="button" @click="quizWriter()" class="px-3 py-1.5 rounded-xl bg-red-600 text-white text-xs font-bold hover:bg-red-700 transition">
                    ✍ Tập tô nét
                </button>
            </div>
        </div>
    </div>

</div>
@endsection

