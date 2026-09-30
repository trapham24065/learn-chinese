@auth
@if(!request()->routeIs('pet.*'))
<div x-data="floatingPetCompanion()" x-init="initCompanion()" x-cloak class="fixed bottom-5 right-5 z-40 select-none">
    {{-- 1. Floating Mini Speech Bubble (when collapsed) --}}
    <div x-show="!isOpen && previewBubble && pet && pet.has_pet"
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="opacity-0 translate-y-2 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-2 scale-95"
         class="absolute bottom-16 right-0 mb-2 w-64 sm:w-72 rounded-2xl bg-white/95 p-3 shadow-xl border border-amber-200/90 text-xs text-slate-800 backdrop-blur pointer-events-auto">
        <div class="flex items-start justify-between gap-1.5">
            <div class="flex items-center gap-1.5 font-bold text-amber-900 mb-1">
                <span x-text="pet ? pet.emoji : '🥚'"></span>
                <span x-text="pet ? (pet.name || 'Pet') : 'Pet'"></span>
                <span class="text-[10px] text-amber-600 font-normal" x-text="'• ' + (pet ? pet.stage_name : '')"></span>
            </div>
            <button type="button" @click.stop="previewBubble = false" class="text-slate-400 hover:text-slate-600 p-0.5">
                <i data-lucide="x" class="h-3 w-3"></i>
            </button>
        </div>
        <p class="leading-relaxed text-slate-700 italic cursor-pointer" @click="toggleOpen()" x-text="currentDialogue"></p>
        {{-- Speech bubble small triangle pointer --}}
        <div class="absolute -bottom-2 right-6 h-3 w-3 rotate-45 border-b border-r border-amber-200/90 bg-white"></div>
    </div>

    {{-- 2. Floating Orb / Trigger Button --}}
    <div x-show="pet && pet.has_pet" class="relative">
        <button type="button"
                @click="toggleOpen()"
                title="Tương tác với Pet đồng hành"
                :class="getGlowClass()"
                class="group relative flex h-14 w-14 items-center justify-center rounded-full bg-white shadow-xl border-2 transition-all duration-300 hover:scale-110 active:scale-95">
            {{-- Pet Avatar --}}
            <span class="text-2xl transition-transform duration-300 group-hover:scale-125" x-text="pet ? pet.emoji : '🥚'"></span>

            {{-- Hunger Mood Status Badge Dot --}}
            <span class="absolute -top-1 -right-1 flex h-4 w-4">
                <span :class="getPingClass()" class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75"></span>
                <span :class="getStatusDotClass()" class="relative inline-flex rounded-full h-4 w-4 border-2 border-white"></span>
            </span>
        </button>
    </div>

    {{-- 3. Expanded Mini-Chat / Companion Card --}}
    <div x-show="isOpen"
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="opacity-0 translate-y-4 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 scale-95"
         class="absolute bottom-0 right-0 w-[320px] sm:w-[360px] max-h-[85vh] overflow-y-auto rounded-3xl bg-white shadow-2xl border border-amber-200/90 custom-scrollbar">
        
        {{-- Card Header --}}
        <div class="sticky top-0 z-10 flex items-center justify-between border-b border-amber-200/70 bg-gradient-to-r from-amber-500 via-orange-500 to-amber-600 px-4 py-3 text-white shadow-sm">
            <div class="flex items-center gap-2.5 min-w-0">
                <div class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-white/20 text-xl backdrop-blur">
                    <span x-text="pet ? pet.emoji : '🥚'"></span>
                </div>
                <div class="truncate">
                    <h3 class="truncate text-sm font-black leading-tight" x-text="pet ? (pet.name || 'Pet đồng hành') : 'Pet đồng hành'"></h3>
                    <p class="text-[10px] text-amber-100" x-text="'Giai đoạn ' + (pet ? pet.stage : 0) + ': ' + (pet ? pet.stage_name : '')"></p>
                </div>
            </div>
            <div class="flex items-center gap-1">
                <a href="{{ route('pet.index') }}" title="Mở phòng Pet đầy đủ"
                   class="grid h-7 w-7 place-items-center rounded-lg bg-white/15 text-white hover:bg-white/25 transition">
                    <i data-lucide="maximize-2" class="h-3.5 w-3.5"></i>
                </a>
                <button type="button" @click="isOpen = false" title="Thu nhỏ"
                        class="grid h-7 w-7 place-items-center rounded-lg bg-white/15 text-white hover:bg-white/25 transition">
                    <i data-lucide="x" class="h-3.5 w-3.5"></i>
                </button>
            </div>
        </div>

        {{-- Card Body --}}
        <div class="p-4 space-y-3.5">
            {{-- Speech Bubble --}}
            <div class="relative rounded-2xl bg-amber-50/80 p-3 border border-amber-200/70 text-xs text-slate-800">
                <div class="flex items-start justify-between gap-2">
                    <p class="leading-relaxed font-medium italic" x-text="currentDialogue"></p>
                    <button type="button" @click="nextDialogue()" title="Đổi câu nói khác"
                            class="shrink-0 rounded-lg p-1 text-amber-700 hover:bg-amber-100 transition">
                        <i data-lucide="refresh-cw" class="h-3.5 w-3.5"></i>
                    </button>
                </div>
            </div>

            {{-- Hunger & Daily Progress Bar --}}
            <div class="rounded-2xl border border-slate-100 bg-slate-50/80 p-3 space-y-2.5">
                <div>
                    <div class="flex items-center justify-between text-[11px] font-semibold text-slate-600 mb-1">
                        <span>Độ no bụng: <strong class="text-slate-800" x-text="pet ? pet.hunger : 0"></strong>/100</span>
                        <span class="rounded-full px-2 py-0.5 text-[9px] font-bold text-white" :class="getMoodBadgeClass()" x-text="getMoodText()"></span>
                    </div>
                    <div class="h-2 w-full overflow-hidden rounded-full bg-slate-200">
                        <div class="h-full rounded-full transition-all duration-500" :class="getProgressBarClass()" :style="'width: ' + (pet ? pet.hunger : 0) + '%'"></div>
                    </div>
                </div>

                <div class="flex items-center justify-between text-[10px] text-slate-500 pt-1 border-t border-slate-200/60">
                    <span>Hạn mức cho ăn hôm nay:</span>
                    <span>Còn <strong class="text-amber-700 font-bold" x-text="pet ? pet.daily_remaining : 0"></strong> / 100 XP</span>
                </div>
            </div>

            {{-- Quick Feed Buttons --}}
            <template x-if="pet && pet.is_active">
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between text-[11px] font-bold text-slate-700">
                        <span class="flex items-center gap-1">
                            <i data-lucide="utensils" class="h-3 w-3 text-amber-600"></i>
                            Cho ăn nhanh (+XP):
                        </span>
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                        <button type="button" @click="feed(5)"
                                :disabled="feeding || !pet || pet.daily_remaining < 5"
                                class="rounded-xl py-2 text-xs font-bold transition active:scale-95 disabled:opacity-40 disabled:cursor-not-allowed bg-amber-500 hover:bg-amber-600 text-white shadow-sm shadow-amber-500/20">
                            +5 XP
                        </button>
                        <button type="button" @click="feed(10)"
                                :disabled="feeding || !pet || pet.daily_remaining < 10"
                                class="rounded-xl py-2 text-xs font-bold transition active:scale-95 disabled:opacity-40 disabled:cursor-not-allowed bg-amber-500 hover:bg-amber-600 text-white shadow-sm shadow-amber-500/20">
                            +10 XP
                        </button>
                        <button type="button" @click="feed(20)"
                                :disabled="feeding || !pet || pet.daily_remaining < 20"
                                class="rounded-xl py-2 text-xs font-bold transition active:scale-95 disabled:opacity-40 disabled:cursor-not-allowed bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white shadow-sm shadow-orange-500/20">
                            +20 XP
                        </button>
                    </div>
                    <p x-show="feedNotice" x-text="feedNotice" class="text-center text-[11px] font-bold text-emerald-600 mt-1"></p>
                </div>
            </template>

            {{-- Today's Mastered Vocabulary Card from Pet --}}
            <template x-if="pet && pet.random_word">
                <div class="rounded-2xl border border-indigo-100 bg-indigo-50/60 p-3 space-y-1.5">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-indigo-800 flex items-center gap-1">
                            <i data-lucide="book-open" class="h-3 w-3"></i>
                            Từ vựng Pet nhớ hôm nay:
                        </span>
                        <button type="button" @click="speakWord(pet.random_word.hanzi)" title="Nghe phát âm"
                                class="rounded-lg p-1 text-indigo-700 hover:bg-indigo-100 transition">
                            <i data-lucide="volume-2" class="h-3.5 w-3.5"></i>
                        </button>
                    </div>
                    <div class="flex items-baseline justify-between">
                        <div>
                            <span class="text-base font-black text-slate-900" x-text="pet.random_word.hanzi"></span>
                            <span class="text-xs text-indigo-600 font-medium ml-1.5" x-text="'[' + pet.random_word.pinyin + ']'"></span>
                        </div>
                        <span class="text-xs text-slate-600 truncate max-w-[140px]" x-text="pet.random_word.meaning"></span>
                    </div>
                </div>
            </template>

            {{-- Footer Button to Pet Room --}}
            <div class="pt-1 text-center">
                <a href="{{ route('pet.index') }}"
                   class="inline-flex items-center justify-center gap-1.5 w-full rounded-xl bg-slate-900 px-3.5 py-2 text-xs font-bold text-white transition hover:bg-[#991b1b] shadow-md shadow-slate-900/10 active:scale-95">
                    <i data-lucide="sparkles" class="h-3.5 w-3.5 text-amber-400"></i>
                    <span>Vào phòng chăm sóc Pet đầy đủ</span>
                    <i data-lucide="arrow-right" class="h-3.5 w-3.5"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<script>
window.floatingPetCompanion = function floatingPetCompanion() {
    return {
        pet: null,
        isOpen: false,
        previewBubble: true,
        feeding: false,
        feedNotice: '',
        currentDialogue: 'Cùng học tiếng Trung chăm chỉ nhé! 🇨🇳',
        dialogueIndex: 0,

        async initCompanion() {
            try {
                const res = await fetch("{{ route('pet.status') }}", {
                    headers: { 'Accept': 'application/json' }
                });
                if (res.ok) {
                    const data = await res.json();
                    if (data && data.has_pet) {
                        this.pet = data;
                        if (data.random_dialogue) {
                            this.currentDialogue = data.random_dialogue;
                        }
                    }
                }
            } catch (e) {}

            // Auto hide preview bubble after 10s if not clicked
            setTimeout(() => {
                this.previewBubble = false;
            }, 10000);

            setTimeout(() => window.refreshIcons?.(), 100);
        },

        toggleOpen() {
            this.isOpen = !this.isOpen;
            if (this.isOpen) {
                this.previewBubble = false;
            }
            setTimeout(() => window.refreshIcons?.(), 50);
        },

        nextDialogue() {
            if (!this.pet || !this.pet.dialogues || this.pet.dialogues.length === 0) return;
            this.dialogueIndex = (this.dialogueIndex + 1) % this.pet.dialogues.length;
            this.currentDialogue = this.pet.dialogues[this.dialogueIndex];
        },

        async feed(amount) {
            if (this.feeding || !this.pet || this.pet.daily_remaining < amount) return;
            this.feeding = true;
            this.feedNotice = '';

            const key = 'comp_feed_' + Date.now() + '_' + Math.random().toString(36).slice(2, 9);
            try {
                const res = await fetch("{{ route('pet.feed') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ amount: amount, idempotency_key: key })
                });
                const data = await res.json();
                if (data.success) {
                    this.pet.hunger = data.hunger;
                    this.pet.hunger_state = data.hunger_state;
                    this.pet.daily_remaining = data.daily_remaining;
                    if (data.dialogue) {
                        this.currentDialogue = data.dialogue;
                    }
                    if (data.stage_up) {
                        this.feedNotice = `🎉 Pet vừa tiến hóa lên Giai đoạn ${data.new_stage}!`;
                    } else {
                        this.feedNotice = `+${data.xp_fed} EXP cho pet! 🍖`;
                    }
                    setTimeout(() => { this.feedNotice = ''; }, 3500);
                } else {
                    this.feedNotice = data.error || 'Không thể cho ăn';
                }
            } catch (e) {
                this.feedNotice = 'Lỗi kết nối';
            } finally {
                this.feeding = false;
                setTimeout(() => window.refreshIcons?.(), 50);
            }
        },

        speakWord(hanzi) {
            if (!hanzi || !('speechSynthesis' in window)) return;
            window.speechSynthesis.cancel();
            const utter = new SpeechSynthesisUtterance(hanzi);
            utter.lang = 'zh-CN';
            utter.rate = 0.85;
            window.speechSynthesis.speak(utter);
        },

        getGlowClass() {
            if (!this.pet) return 'border-amber-300';
            switch (this.pet.hunger_state) {
                case 'happy': return 'border-emerald-400 shadow-emerald-500/20';
                case 'hungry': return 'border-amber-400 shadow-amber-500/20';
                case 'very_hungry': return 'border-orange-400 shadow-orange-500/20';
                case 'weak': return 'border-rose-400 shadow-rose-500/20';
                default: return 'border-indigo-400 shadow-indigo-500/20';
            }
        },

        getPingClass() {
            if (!this.pet) return 'bg-amber-400';
            switch (this.pet.hunger_state) {
                case 'happy': return 'bg-emerald-400';
                case 'hungry': return 'bg-amber-400';
                case 'very_hungry': return 'bg-orange-400';
                case 'weak': return 'bg-rose-400';
                default: return 'bg-indigo-400';
            }
        },

        getStatusDotClass() {
            if (!this.pet) return 'bg-amber-500';
            switch (this.pet.hunger_state) {
                case 'happy': return 'bg-emerald-500';
                case 'hungry': return 'bg-amber-500';
                case 'very_hungry': return 'bg-orange-500';
                case 'weak': return 'bg-rose-500';
                default: return 'bg-indigo-500';
            }
        },

        getMoodBadgeClass() {
            if (!this.pet) return 'bg-slate-500';
            switch (this.pet.hunger_state) {
                case 'happy': return 'bg-emerald-500';
                case 'hungry': return 'bg-amber-500';
                case 'very_hungry': return 'bg-orange-500';
                case 'weak': return 'bg-rose-500';
                default: return 'bg-indigo-600';
            }
        },

        getMoodText() {
            if (!this.pet) return '';
            switch (this.pet.hunger_state) {
                case 'happy': return '😊 Vui vẻ';
                case 'hungry': return '🙂 Hơi đói';
                case 'very_hungry': return '😟 Rất đói';
                case 'weak': return '😢 Yếu';
                case 'dormant': return '💤 Ngủ đông';
                default: return 'Bình thường';
            }
        },

        getProgressBarClass() {
            if (!this.pet) return 'bg-amber-400';
            if (this.pet.hunger >= 70) return 'bg-emerald-500';
            if (this.pet.hunger >= 40) return 'bg-amber-400';
            if (this.pet.hunger >= 20) return 'bg-orange-500';
            return 'bg-rose-500';
        }
    };
};

if (typeof Alpine !== 'undefined' && Alpine.data) {
    Alpine.data('floatingPetCompanion', () => window.floatingPetCompanion());
} else {
    document.addEventListener('alpine:init', () => {
        Alpine.data('floatingPetCompanion', () => window.floatingPetCompanion());
    });
}
</script>
@endif
@endauth
