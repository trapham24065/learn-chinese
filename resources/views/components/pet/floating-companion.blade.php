@auth
@if(!request()->routeIs('pet.*'))
<div x-data="petFloatingCompanion()" x-init="initCompanion()" x-cloak
     class="fixed bottom-6 right-4 sm:bottom-6 sm:right-6 z-40 select-none">

    {{-- A. Minimized Pill (Khi người dùng thu nhỏ Pet) --}}
    <div x-show="isMinimized"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-2 scale-90"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         class="flex items-center">
        <button type="button" @click="restorePet()"
                title="Mở Pet đồng hành"
                class="inline-flex items-center gap-1.5 rounded-full bg-white/95 px-3 py-1.5 text-xs font-bold text-slate-800 shadow-lg border border-amber-200/90 backdrop-blur hover:bg-amber-50 hover:scale-105 active:scale-95 transition">
            <span class="text-base" x-text="pet ? pet.emoji : '🐉'"></span>
            <span class="text-[11px]" x-text="pet ? (pet.name || 'Pet') : 'Pet'"></span>
            <span class="h-2 w-2 rounded-full" :class="getMoodDotClass()"></span>
        </button>
    </div>

    {{-- B. Full Living Mascot Container --}}
    <div x-show="!isMinimized && pet && pet.has_pet" class="relative group">

        {{-- 1. Subtle Hover Hint ("Psst... 👀") --}}
        <div x-show="hoverHint && !speechBubbleOpen"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-1 scale-90"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-1 scale-90"
             class="absolute -top-7 left-1/2 -translate-x-1/2 pointer-events-none z-30 whitespace-nowrap rounded-full bg-slate-900/80 px-2.5 py-0.5 text-[10px] font-bold text-white backdrop-blur shadow-sm">
            <span x-text="hoverHintText"></span>
        </div>

        {{-- 2. Comic Speech Bubble Popover --}}
        <x-pet.speech-bubble />

        {{-- 3. Living Pet Mascot Model --}}
        <div class="relative cursor-pointer"
             @mouseenter="onHoverPet()"
             @mouseleave="hoverHint = false"
             @click="pokePet()"
             title="Chạm để nói chuyện cùng Pet!">

            {{-- Living SVG Avatar --}}
            <x-pet.pet-avatar />

            {{-- Minimize Button on Hover (Subtle, non-intrusive) --}}
            <button type="button" @click.stop="minimizePet()"
                    title="Thu nhỏ Pet"
                    class="opacity-0 group-hover:opacity-100 transition-opacity duration-200 absolute -top-1 -right-1 grid h-5 w-5 place-items-center rounded-full bg-slate-800/80 text-white hover:bg-slate-900 text-[10px] shadow-sm z-30">
                <i data-lucide="minus" class="h-3 w-3"></i>
            </button>
        </div>

    </div>
</div>

<script>
window.petFloatingCompanion = function petFloatingCompanion() {
    return {
        pet: null,
        state: 'idle', // 'idle' | 'happy' | 'excited' | 'hungry' | 'sleeping' | 'speaking' | 'evolving'
        speechBubbleOpen: false,
        isMinimized: localStorage.getItem('pet_companion_minimized') === '1',
        hoverHint: false,
        hoverHintText: 'Psst... 👀',
        hoverTimer: null,
        autoCloseTimer: null,
        sleepTimer: null,
        currentDialogue: 'Cùng học tiếng Trung chăm chỉ nhé! 🇨🇳',
        dialogueIndex: 0,
        feeding: false,
        feedNotice: '',
        showHearts: false,
        heartTimer: null,

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
                        this.determineInitialState();
                    }
                }
            } catch (e) {}

            // Setup Event-Driven Pet listener
            window.addEventListener('pet-event', (e) => {
                this.handlePetEvent(e.detail || {});
            });

            // Sleep detection (idle check / night time)
            this.resetSleepTimer();
            ['mousemove', 'keydown', 'touchstart'].forEach(evt => {
                window.addEventListener(evt, () => this.onUserActivity(), { passive: true });
            });

            setTimeout(() => window.refreshIcons?.(), 100);
        },

        determineInitialState() {
            if (!this.pet) return;
            const hour = new Date().getHours();
            if (hour >= 22 || hour < 6) {
                this.state = 'sleeping';
                return;
            }
            if (this.pet.hunger < 25) {
                this.state = 'hungry';
                return;
            }
            this.state = 'idle';
        },

        onUserActivity() {
            if (this.state === 'sleeping') {
                this.determineInitialState();
            }
            this.resetSleepTimer();
        },

        resetSleepTimer() {
            clearTimeout(this.sleepTimer);
            // After 3 minutes of no user activity, Pet rests
            this.sleepTimer = setTimeout(() => {
                if (!this.speechBubbleOpen && this.state !== 'evolving') {
                    this.state = 'sleeping';
                }
            }, 180000);
        },

        onHoverPet() {
            if (this.speechBubbleOpen) return;
            this.hoverHintText = this.getHoverHint();
            this.hoverHint = true;
            clearTimeout(this.hoverTimer);
            this.hoverTimer = setTimeout(() => {
                this.hoverHint = false;
            }, 3000);
            setTimeout(() => window.refreshIcons?.(), 50);
        },

        getHoverHint() {
            if (this.state === 'sleeping') return 'Khò khò... 💤';
            if (this.state === 'hungry') return 'Tớ đói quá... 🥺';
            const hints = ['Psst... 👀', 'Chạm tớ nè! ✨', 'Cùng học nào! 📚', 'Hehe! 🐲'];
            return hints[Math.floor(Math.random() * hints.length)];
        },

        pokePet() {
            this.hoverHint = false;
            this.onUserActivity();

            // Trigger Happy / Bouncing State
            this.state = 'happy';
            this.spawnHeart();

            // Open or toggle speech bubble
            if (!this.speechBubbleOpen) {
                this.speechBubbleOpen = true;
                setTimeout(() => {
                    if (this.speechBubbleOpen) this.state = 'speaking';
                }, 700);
            } else {
                // If already open, cycle dialogue
                this.nextDialogue();
            }

            // Auto return to idle after 2.5s if speech bubble is closed
            setTimeout(() => {
                if (!this.speechBubbleOpen && this.state === 'happy') {
                    this.determineInitialState();
                }
            }, 2500);

            // Auto dismiss speech bubble after 14s if user doesn't interact
            this.scheduleBubbleAutoDismiss();
            setTimeout(() => window.refreshIcons?.(), 60);
        },

        closeSpeechBubble() {
            this.speechBubbleOpen = false;
            this.determineInitialState();
            clearTimeout(this.autoCloseTimer);
        },

        scheduleBubbleAutoDismiss() {
            clearTimeout(this.autoCloseTimer);
            this.autoCloseTimer = setTimeout(() => {
                if (!this.feeding) {
                    this.closeSpeechBubble();
                }
            }, 14000);
        },

        spawnHeart() {
            this.showHearts = true;
            clearTimeout(this.heartTimer);
            this.heartTimer = setTimeout(() => {
                this.showHearts = false;
            }, 1400);
        },

        nextDialogue() {
            if (!this.pet || !this.pet.dialogues || this.pet.dialogues.length === 0) return;
            this.dialogueIndex = (this.dialogueIndex + 1) % this.pet.dialogues.length;
            this.currentDialogue = this.pet.dialogues[this.dialogueIndex];
            this.scheduleBubbleAutoDismiss();
        },

        async feed(amount) {
            if (this.feeding || !this.pet || this.pet.daily_remaining < amount) return;
            this.feeding = true;
            this.feedNotice = '';

            const key = 'mascot_feed_' + Date.now() + '_' + Math.random().toString(36).slice(2, 9);
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
                    if (data.dialogue) this.currentDialogue = data.dialogue;

                    // Pet Reaction
                    this.spawnHeart();
                    this.spawnHeart();

                    if (data.stage_up) {
                        this.pet.stage = data.new_stage;
                        this.state = 'evolving';
                        this.feedNotice = `🎉 Pet vừa tiến hóa lên Giai đoạn ${data.new_stage}!`;
                        setTimeout(() => { this.state = 'happy'; }, 3000);
                    } else {
                        this.state = 'happy';
                        this.feedNotice = `+${data.xp_fed} EXP thành công cho Pet! 🍖`;
                        setTimeout(() => {
                            if (this.speechBubbleOpen) this.state = 'speaking';
                            else this.determineInitialState();
                        }, 1800);
                    }
                    setTimeout(() => { this.feedNotice = ''; }, 3500);
                    this.scheduleBubbleAutoDismiss();
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
            this.scheduleBubbleAutoDismiss();
        },

        minimizePet() {
            this.isMinimized = true;
            this.speechBubbleOpen = false;
            localStorage.setItem('pet_companion_minimized', '1');
            setTimeout(() => window.refreshIcons?.(), 50);
        },

        restorePet() {
            this.isMinimized = false;
            localStorage.setItem('pet_companion_minimized', '0');
            this.pokePet();
        },

        // Event-Driven Pet Reactor
        handlePetEvent(detail) {
            const { type, amount, word } = detail;
            this.onUserActivity();

            switch (type) {
                case 'xp-earned':
                    this.state = 'excited';
                    this.spawnHeart();
                    this.currentDialogue = `Tuyệt vời! Cậu vừa nhận +${amount || ''} XP học tập! ✨`;
                    this.speechBubbleOpen = true;
                    this.scheduleBubbleAutoDismiss();
                    setTimeout(() => this.determineInitialState(), 3500);
                    break;

                case 'flashcard-completed':
                    this.state = 'happy';
                    this.spawnHeart();
                    this.currentDialogue = 'Cậu vừa hoàn thành một lượt ôn thẻ flashcard! Giỏi lắm! 👏';
                    this.speechBubbleOpen = true;
                    this.scheduleBubbleAutoDismiss();
                    setTimeout(() => this.determineInitialState(), 4000);
                    break;

                case 'quiz-completed':
                    this.state = 'excited';
                    this.spawnHeart();
                    this.currentDialogue = 'Chúc mừng cậu hoàn thành bài quiz! Kiến thức vững vàng hơn rồi đó! 🎯';
                    this.speechBubbleOpen = true;
                    this.scheduleBubbleAutoDismiss();
                    setTimeout(() => this.determineInitialState(), 4000);
                    break;

                case 'word-mastered':
                    this.state = 'excited';
                    this.spawnHeart();
                    this.currentDialogue = `Tuyệt quá! Cậu đã làm chủ từ vựng ${word ? '[' + word + ']' : ''}! 🎉`;
                    this.speechBubbleOpen = true;
                    this.scheduleBubbleAutoDismiss();
                    setTimeout(() => this.determineInitialState(), 4500);
                    break;

                case 'streak-up':
                    this.state = 'happy';
                    this.spawnHeart();
                    this.currentDialogue = 'Chuỗi ngày học liên tục đã tăng lên! Cùng giữ vững phong độ nhé! 🔥';
                    this.speechBubbleOpen = true;
                    this.scheduleBubbleAutoDismiss();
                    setTimeout(() => this.determineInitialState(), 4000);
                    break;

                case 'pet-evolved':
                    this.state = 'evolving';
                    this.currentDialogue = '🎉 Pet của cậu vừa tiến hóa lên hình dạng mới rực rỡ!';
                    this.speechBubbleOpen = true;
                    setTimeout(() => this.determineInitialState(), 4500);
                    break;
            }
            setTimeout(() => window.refreshIcons?.(), 50);
        },

        getMoodDotClass() {
            if (!this.pet) return 'bg-amber-400';
            switch (this.pet.hunger_state) {
                case 'happy': return 'bg-emerald-500';
                case 'hungry': return 'bg-amber-400';
                case 'very_hungry': return 'bg-orange-500';
                case 'weak': return 'bg-rose-500';
                default: return 'bg-slate-400';
            }
        },

        getMoodBadgeClass() {
            if (!this.pet) return 'bg-slate-500';
            switch (this.pet.hunger_state) {
                case 'happy': return 'bg-emerald-500';
                case 'hungry': return 'bg-amber-500';
                case 'very_hungry': return 'bg-orange-500';
                case 'weak': return 'bg-rose-500';
                default: return 'bg-slate-500';
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
        }
    };
};

// Global helper for triggering pet events from anywhere
window.triggerPetEvent = function(type, detail = {}) {
    window.dispatchEvent(new CustomEvent('pet-event', { detail: { type, ...detail } }));
};

if (typeof Alpine !== 'undefined' && Alpine.data) {
    Alpine.data('petFloatingCompanion', () => window.petFloatingCompanion());
} else {
    document.addEventListener('alpine:init', () => {
        Alpine.data('petFloatingCompanion', () => window.petFloatingCompanion());
    });
}
</script>
@endif
@endauth
