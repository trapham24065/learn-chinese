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
        <div class="flex items-center gap-1.5 min-w-0">
            <span class="inline-flex items-center gap-1 font-bold text-slate-900 truncate">
                <span x-text="pet ? (pet.name || 'Tiểu Long') : 'Tiểu Long'"></span>
            </span>
            <span class="rounded-full px-2 py-0.5 text-[9px] font-bold text-white shrink-0"
                  :class="getMoodBadgeClass()"
                  x-text="getMoodText()"></span>
        </div>
        <button type="button" @click="closeSpeechBubble()" title="Đóng bóng thoại"
                class="rounded-lg p-1 text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition">
            <i data-lucide="x" class="h-3.5 w-3.5"></i>
        </button>
    </div>

    {{-- Dialogue Text --}}
    <div class="mb-3">
        <p class="leading-relaxed text-slate-700 font-medium italic text-xs" x-text="currentDialogue"></p>
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
