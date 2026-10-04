<div x-show="speechBubbleOpen"
     x-transition:enter="transition ease-out duration-300 transform"
     x-transition:enter-start="opacity-0 translate-y-3 scale-95"
     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
     x-transition:leave="transition ease-in duration-200 transform"
     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
     x-transition:leave-end="opacity-0 translate-y-3 scale-95"
     @click.outside="speechBubbleOpen = false"
     class="absolute bottom-full right-0 mb-3.5 w-72 sm:w-80 rounded-2xl bg-white/95 backdrop-blur-md p-4 shadow-2xl border border-amber-200/90 text-xs text-slate-800 z-50 select-none">

    {{-- Comic Speech Bubble Triangle Pointer --}}
    <div class="absolute -bottom-2 right-8 sm:right-10 h-3.5 w-3.5 rotate-45 border-b border-r border-amber-200/90 bg-white"></div>

    {{-- Header --}}
    <div class="flex items-center justify-between pb-2 border-b border-amber-100 mb-2.5">
        <div class="flex items-center gap-1.5 min-w-0 flex-wrap">
            <span class="inline-flex items-center gap-1 font-bold text-slate-900 truncate">
                <span x-text="pet ? (pet.name || 'Tiểu Long') : 'Tiểu Long'"></span>
            </span>

            {{-- Affinity Relationship Pill --}}
            <span x-show="pet && pet.affinity_tier"
                  class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[9px] font-bold border shrink-0"
                  :class="getAffinityPillClass()"
                  :title="'Độ thân thiết: ' + (pet?.affinity || 0) + '/100 (' + (pet?.affinity_tier?.title || '') + ')'">
                <span x-text="pet?.affinity_tier?.emoji || '🌱'"></span>
                <span x-text="pet?.affinity_tier?.name || 'Bỡ ngỡ'"></span>
            </span>

            {{-- Personality Emoji Indicator --}}
            <span x-show="pet && pet.personality_emoji"
                  class="rounded-full bg-amber-100/80 px-1.5 py-0.5 text-[9px] font-bold text-amber-900 shrink-0"
                  :title="'Tính cách: ' + (pet?.personality_label || '')"
                  x-text="pet?.personality_emoji + ' ' + (pet?.personality_label ? pet.personality_label.split('&')[0].trim() : '')"></span>
        </div>
        <div class="flex items-center gap-1">
            {{-- Audio Settings Toggle --}}
            <button type="button" @click="soundMenuOpen = !soundMenuOpen"
                    :title="soundMenuOpen ? 'Đóng cài đặt âm thanh' : 'Cài đặt âm thanh'"
                    class="rounded-lg p-1 text-slate-400 hover:text-amber-700 hover:bg-amber-50 transition"
                    :class="soundMenuOpen ? 'text-amber-700 bg-amber-100/70' : ''">
                <i data-lucide="volume-2" class="h-3.5 w-3.5"></i>
            </button>

            <button type="button" @click="closeSpeechBubble()" title="Đóng bóng thoại"
                    class="rounded-lg p-1 text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition">
                <i data-lucide="x" class="h-3.5 w-3.5"></i>
            </button>
        </div>
    </div>

    {{-- Audio Controls Popover Panel --}}
    <div x-show="soundMenuOpen" x-cloak
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 -translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="mb-3 rounded-xl border border-amber-200 bg-amber-50/90 p-2.5 space-y-2 text-[11px] shadow-inner">
        <div class="flex items-center justify-between">
            <span class="font-bold text-slate-700 flex items-center gap-1">
                <i data-lucide="bell" class="h-3 w-3 text-amber-600"></i>
                Hiệu ứng âm thanh (SFX)
            </span>
            <button type="button" @click="toggleSfx()"
                    class="relative inline-flex h-4 w-7 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
                    :class="sfxEnabled ? 'bg-amber-500' : 'bg-slate-300'">
                <span class="pointer-events-none inline-block h-3 w-3 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                      :class="sfxEnabled ? 'translate-x-3' : 'translate-x-0'"></span>
            </button>
        </div>

        <div class="flex items-center justify-between">
            <span class="font-bold text-slate-700 flex items-center gap-1">
                <i data-lucide="mic" class="h-3 w-3 text-indigo-600"></i>
                Giọng đọc tiếng Trung (TTS)
            </span>
            <button type="button" @click="toggleVoice()"
                    class="relative inline-flex h-4 w-7 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
                    :class="voiceEnabled ? 'bg-indigo-600' : 'bg-slate-300'">
                <span class="pointer-events-none inline-block h-3 w-3 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                      :class="voiceEnabled ? 'translate-x-3' : 'translate-x-0'"></span>
            </button>
        </div>

        <div class="pt-1 border-t border-amber-200/60">
            <div class="flex items-center justify-between text-[10px] text-slate-500 mb-1">
                <span>Âm lượng</span>
                <span x-text="audioVolume + '%'"></span>
            </div>
            <input type="range" min="0" max="100" step="5"
                   x-model="audioVolume"
                   @input="updateVolume($event.target.value)"
                   class="w-full h-1.5 bg-amber-200 rounded-lg appearance-none cursor-pointer accent-amber-600">
        </div>
    </div>

    {{-- Dialogue Text (Bilingual Chinese + Pinyin + Vietnamese) --}}
    <div class="mb-3 rounded-xl bg-amber-50/70 border border-amber-200/70 p-2.5">
        <div class="flex items-start justify-between gap-1.5">
            <div class="flex-1 min-w-0 cursor-pointer" @click="speakCurrentDialogue()" title="Bấm để nghe Pet phát âm">
                <template x-if="parseDialogueItem(currentDialogue).pinyin">
                    <div>
                        <div class="text-sm font-black text-amber-950 tracking-wide" x-text="parseDialogueItem(currentDialogue).chinese"></div>
                        <div class="text-[10px] font-mono text-amber-700 opacity-80" x-text="parseDialogueItem(currentDialogue).pinyin"></div>
                        <div class="text-[11px] text-slate-700 font-medium italic mt-1 leading-snug" x-text="parseDialogueItem(currentDialogue).vietnamese"></div>
                    </div>
                </template>
                <template x-if="!parseDialogueItem(currentDialogue).pinyin">
                    <p class="leading-relaxed text-slate-700 font-medium italic text-xs" x-text="currentDialogue"></p>
                </template>
            </div>
            <button type="button" @click="speakCurrentDialogue()" title="Nghe Pet phát âm chuẩn câu này"
                    class="shrink-0 rounded-lg p-1 text-slate-400 hover:text-amber-700 hover:bg-amber-100/70 transition">
                <i data-lucide="volume-2" class="h-3.5 w-3.5"></i>
            </button>
        </div>
    </div>

    {{-- Vocabulary Companion Card (Chỉ hiện khi có từ đã đạt Mastery) --}}
    <div x-show="pet && pet.random_word" x-cloak class="mb-3 rounded-xl border border-indigo-100 bg-indigo-50/70 p-2.5 space-y-1.5">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold uppercase tracking-wider text-indigo-800 flex items-center gap-1">
                    <i data-lucide="book-open" class="h-3 w-3"></i>
                    Từ bạn đã làm chủ:
                </span>
                <span class="text-[9px] text-indigo-500 font-medium">Ôn tập nhanh</span>
            </div>
            
            <div class="flex items-baseline justify-between gap-2">
                <div class="flex items-baseline gap-1.5 min-w-0">
                    <span class="text-base font-black text-slate-900" x-text="pet?.random_word?.hanzi || ''"></span>
                    <span class="text-xs text-indigo-600 font-semibold" x-text="pet?.random_word?.pinyin ? '[' + pet.random_word.pinyin + ']' : ''"></span>
                </div>
                <span class="text-xs text-slate-600 truncate max-w-[120px]" x-text="pet?.random_word?.meaning || ''"></span>
            </div>

            {{-- Vocab Actions: Nghe & Ôn lại --}}
            <div class="flex items-center gap-2 pt-1 border-t border-indigo-100/80">
                <button type="button" @click="speakWord(pet?.random_word?.hanzi)"
                        class="inline-flex items-center gap-1 rounded-lg bg-indigo-600 px-2 py-1 text-[11px] font-bold text-white transition hover:bg-indigo-700 active:scale-95 shadow-sm">
                    <i data-lucide="volume-2" class="h-3 w-3"></i>
                    <span>Nghe</span>
                </button>

                <a href="{{ route('flashcards') }}"
                   class="inline-flex items-center gap-1 rounded-lg bg-white border border-indigo-200 px-2 py-1 text-[11px] font-bold text-indigo-700 transition hover:bg-indigo-50 active:scale-95">
                    <i data-lucide="sparkles" class="h-3 w-3 text-amber-500"></i>
                    <span>Ôn lại</span>
                </a>
            </div>
        </div>

    {{-- Pet Quick Actions Component --}}
    <x-pet.pet-actions />

</div>
