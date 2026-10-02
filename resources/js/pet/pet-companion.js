/**
 * Pet Learning Companion Coordinator (pet-companion.js)
 * Bridges Alpine UI, Living Mascot animations, PetEventBus,
 * PetAudioEngine (procedural SFX), and PetVoiceManager (Chinese TTS).
 */

import { PetEventBus } from './PetEventBus.js';
import { PetAudioEngine } from './PetAudioEngine.js';
import { PetVoiceManager } from './PetVoiceManager.js';

export function petFloatingCompanion(config = {}) {
    return {
        pet: {
            has_pet: false,
            name: '',
            stage: 0,
            emoji: '🥚',
            hunger: 100,
            hunger_state: 'happy',
            is_active: false,
            daily_remaining: 0,
            random_word: null,
            dialogues: []
        },
        state: 'idle', // 'idle' | 'happy' | 'excited' | 'hungry' | 'sleeping' | 'speaking' | 'evolving'
        speechBubbleOpen: false,
        soundMenuOpen: false,
        isMinimized: (typeof window !== 'undefined' && localStorage.getItem('pet_companion_minimized') === '1'),
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

        // Live Audio Reactive Settings
        sfxEnabled: PetAudioEngine.isSfxEnabled(),
        voiceEnabled: PetVoiceManager.isVoiceEnabled(),
        audioVolume: Math.round(PetAudioEngine.getVolume() * 100),

        async initCompanion() {
            try {
                const statusEndpoint = config.statusUrl || '/student/pet/status';
                const res = await fetch(statusEndpoint, {
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

            // Bind unified event bus listeners
            this.bindEventBusListeners();

            // Setup sleep detection and idle watchers
            this.resetSleepTimer();
            ['mousemove', 'keydown', 'touchstart'].forEach(evt => {
                window.addEventListener(evt, () => this.onUserActivity(), { passive: true });
            });

            setTimeout(() => window.refreshIcons?.(), 100);
        },

        bindEventBusListeners() {
            // Unify with PetEventBus
            PetEventBus.on('pet:poked', () => this.handlePokeReaction());
            PetEventBus.on('pet:fed', (payload) => this.handleFeedReaction(payload));
            PetEventBus.on('pet:word-mastered', (payload) => this.handleWordMasteredReaction(payload));
            PetEventBus.on('pet:daily-goal-completed', (payload) => this.handleDailyGoalReaction(payload));
            PetEventBus.on('pet:evolved', (payload) => this.handleEvolvedReaction(payload));
            PetEventBus.on('pet:xp-earned', (payload) => this.handleXpEarnedReaction(payload));
            PetEventBus.on('pet:quiz-completed', (payload) => this.handleQuizCompletedReaction(payload));

            // Backward compatibility listener for legacy pet-event CustomEvent
            if (typeof window !== 'undefined') {
                window.addEventListener('pet-event', (e) => {
                    const detail = e.detail || {};
                    const type = detail.type;
                    if (!type) return;

                    switch (type) {
                        case 'word-mastered':
                            this.handleWordMasteredReaction(detail);
                            break;
                        case 'daily-goal-completed':
                            this.handleDailyGoalReaction(detail);
                            break;
                        case 'pet-evolved':
                            this.handleEvolvedReaction(detail);
                            break;
                        case 'xp-earned':
                            this.handleXpEarnedReaction(detail);
                            break;
                        case 'quiz-completed':
                            this.handleQuizCompletedReaction(detail);
                            break;
                    }
                });
            }
        },

        isReducedMotion() {
            if (typeof window === 'undefined') return false;
            return window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        },

        canPlayAudio() {
            // Suppress audio while sleeping, without changing user settings
            return this.state !== 'sleeping';
        },

        toggleSfx() {
            this.sfxEnabled = PetAudioEngine.toggleSfx();
            setTimeout(() => window.refreshIcons?.(), 50);
        },

        toggleVoice() {
            this.voiceEnabled = PetVoiceManager.toggleVoice();
            setTimeout(() => window.refreshIcons?.(), 50);
        },

        updateVolume(vol) {
            this.audioVolume = parseInt(vol, 10);
            PetAudioEngine.setVolume(this.audioVolume / 100);
        },

        determineInitialState() {
            if (!this.pet || !this.pet.has_pet) return;
            const hour = new Date().getHours();
            if (hour >= 22 || hour < 6) {
                this.state = 'sleeping';
                return;
            }
            if ((this.pet?.hunger ?? 100) < 25) {
                this.state = 'hungry';
                if (this.canPlayAudio()) {
                    PetAudioEngine.hungry();
                }
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
            // After 3 minutes of no user activity, Pet sleeps
            this.sleepTimer = setTimeout(() => {
                if (!this.speechBubbleOpen && this.state !== 'evolving') {
                    this.state = 'sleeping';
                    PetVoiceManager.stop();
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
            const wasSleeping = (this.state === 'sleeping');
            this.onUserActivity();

            // Audio & Animation reaction
            if (!wasSleeping && this.canPlayAudio()) {
                PetAudioEngine.chirp();
            }

            this.state = 'happy';
            this.spawnHeart();

            // Open or toggle speech bubble
            if (!this.speechBubbleOpen) {
                this.speechBubbleOpen = true;
                if (this.canPlayAudio()) {
                    PetAudioEngine.pop();
                }
                setTimeout(() => {
                    if (this.speechBubbleOpen && this.state === 'happy') {
                        this.state = 'speaking';
                    }
                }, 700);
            } else {
                this.nextDialogue();
            }

            // Return to idle after 2.5s if bubble is closed
            setTimeout(() => {
                if (!this.speechBubbleOpen && this.state === 'happy') {
                    this.determineInitialState();
                }
            }, 2500);

            this.scheduleBubbleAutoDismiss();
            setTimeout(() => window.refreshIcons?.(), 60);
        },

        handlePokeReaction() {
            if (this.state === 'sleeping') {
                this.determineInitialState();
            }
            if (this.canPlayAudio()) {
                PetAudioEngine.chirp();
            }
            this.state = 'happy';
            this.spawnHeart();
        },

        closeSpeechBubble() {
            this.speechBubbleOpen = false;
            this.soundMenuOpen = false;
            PetVoiceManager.stop();
            this.determineInitialState();
            clearTimeout(this.autoCloseTimer);
        },

        scheduleBubbleAutoDismiss() {
            clearTimeout(this.autoCloseTimer);
            this.autoCloseTimer = setTimeout(() => {
                if (!this.feeding && !this.soundMenuOpen) {
                    this.closeSpeechBubble();
                }
            }, 14000);
        },

        spawnHeart() {
            if (this.isReducedMotion()) return;
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
            if (this.canPlayAudio()) {
                PetAudioEngine.pop();
            }
            this.scheduleBubbleAutoDismiss();
        },

        async feed(amount) {
            if (this.feeding || !this.pet || this.pet.daily_remaining < amount) return;
            this.feeding = true;
            this.feedNotice = '';

            const key = 'mascot_feed_' + Date.now() + '_' + Math.random().toString(36).slice(2, 9);
            const feedEndpoint = config.feedUrl || '/student/pet/feed';

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                const res = await fetch(feedEndpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
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

                    // Play Munch SFX
                    if (this.canPlayAudio()) {
                        PetAudioEngine.munch();
                        if (data.hunger >= 95) {
                            setTimeout(() => PetAudioEngine.purr(), 300);
                        }
                    }

                    this.spawnHeart();

                    if (data.stage_up) {
                        this.pet.stage = data.new_stage;
                        this.state = 'evolving';
                        if (this.canPlayAudio()) {
                            PetAudioEngine.levelUp();
                        }
                        this.feedNotice = `🎉 Pet vừa tiến hóa lên Giai đoạn ${data.new_stage}!`;
                        PetEventBus.emit('pet:evolved', { new_stage: data.new_stage });
                        setTimeout(() => { this.state = 'happy'; }, 3200);
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
            if (!hanzi || !this.canPlayAudio()) return;

            this.state = 'speaking';
            PetVoiceManager.speak(hanzi, {
                onStart: () => {
                    this.state = 'speaking';
                },
                onEnd: () => {
                    if (this.speechBubbleOpen) {
                        this.state = 'idle';
                    } else {
                        this.determineInitialState();
                    }
                },
                onError: () => {
                    if (this.speechBubbleOpen) {
                        this.state = 'idle';
                    } else {
                        this.determineInitialState();
                    }
                }
            });

            this.scheduleBubbleAutoDismiss();
        },

        minimizePet() {
            this.isMinimized = true;
            this.speechBubbleOpen = false;
            this.soundMenuOpen = false;
            PetVoiceManager.stop();
            if (typeof localStorage !== 'undefined') {
                localStorage.setItem('pet_companion_minimized', '1');
            }
            setTimeout(() => window.refreshIcons?.(), 50);
        },

        restorePet() {
            this.isMinimized = false;
            if (typeof localStorage !== 'undefined') {
                localStorage.setItem('pet_companion_minimized', '0');
            }
            this.pokePet();
        },

        // React to learning events
        handleWordMasteredReaction(payload = {}) {
            this.onUserActivity();
            this.state = 'excited';
            if (this.canPlayAudio()) {
                PetAudioEngine.chirp();
            }
            this.spawnHeart();

            const word = payload.word || payload.hanzi || '';
            this.currentDialogue = `Tuyệt quá! Cậu đã làm chủ từ vựng ${word ? '[' + word + ']' : ''}! 🎉`;
            this.speechBubbleOpen = true;
            this.scheduleBubbleAutoDismiss();
            setTimeout(() => this.determineInitialState(), 4500);
            setTimeout(() => window.refreshIcons?.(), 50);
        },

        handleDailyGoalReaction(payload = {}) {
            this.onUserActivity();
            this.state = 'excited';
            if (this.canPlayAudio()) {
                PetAudioEngine.celebration(); // Harmonic triad C5-E5-G5
            }
            this.spawnHeart();

            this.currentDialogue = 'Xuất sắc! Cậu đã hoàn thành toàn bộ mục tiêu hôm nay! 🌟';
            this.speechBubbleOpen = true;
            this.scheduleBubbleAutoDismiss();
            setTimeout(() => this.determineInitialState(), 5000);
            setTimeout(() => window.refreshIcons?.(), 50);
        },

        handleEvolvedReaction(payload = {}) {
            this.onUserActivity();
            this.state = 'evolving';
            if (this.canPlayAudio()) {
                PetAudioEngine.levelUp(); // Epic fanfare
            }
            const stage = payload.new_stage || '';
            this.currentDialogue = `🎉 Pet vừa tiến hóa lên hình dạng mới ${stage ? 'Giai đoạn ' + stage : ''} rực rỡ!`;
            this.speechBubbleOpen = true;
            setTimeout(() => this.determineInitialState(), 5000);
            setTimeout(() => window.refreshIcons?.(), 50);
        },

        handleFeedReaction(payload = {}) {
            this.onUserActivity();
            if (this.canPlayAudio()) {
                PetAudioEngine.munch();
            }
            this.spawnHeart();
        },

        handleXpEarnedReaction(payload = {}) {
            this.onUserActivity();
            this.state = 'excited';
            this.spawnHeart();
            const amount = payload.amount || payload.xp || '';
            this.currentDialogue = `Tuyệt vời! Cậu vừa nhận +${amount} XP học tập! ✨`;
            this.speechBubbleOpen = true;
            this.scheduleBubbleAutoDismiss();
            setTimeout(() => this.determineInitialState(), 3500);
            setTimeout(() => window.refreshIcons?.(), 50);
        },

        handleQuizCompletedReaction(payload = {}) {
            this.onUserActivity();
            this.state = 'excited';
            this.spawnHeart();
            this.currentDialogue = 'Chúc mừng cậu hoàn thành bài quiz! Kiến thức vững vàng hơn rồi đó! 🎯';
            this.speechBubbleOpen = true;
            this.scheduleBubbleAutoDismiss();
            setTimeout(() => this.determineInitialState(), 4000);
            setTimeout(() => window.refreshIcons?.(), 50);
        },

        getMoodDotClass() {
            if (!this.pet || !this.pet.has_pet) return 'bg-amber-400';
            switch (this.pet?.hunger_state) {
                case 'happy': return 'bg-emerald-500';
                case 'hungry': return 'bg-amber-400';
                case 'very_hungry': return 'bg-orange-500';
                case 'weak': return 'bg-rose-500';
                default: return 'bg-slate-400';
            }
        },

        getMoodBadgeClass() {
            if (!this.pet || !this.pet.has_pet) return 'bg-slate-500';
            switch (this.pet?.hunger_state) {
                case 'happy': return 'bg-emerald-500';
                case 'hungry': return 'bg-amber-500';
                case 'very_hungry': return 'bg-orange-500';
                case 'weak': return 'bg-rose-500';
                default: return 'bg-slate-500';
            }
        },

        getMoodText() {
            if (!this.pet || !this.pet.has_pet) return '';
            switch (this.pet?.hunger_state) {
                case 'happy': return '😊 Vui vẻ';
                case 'hungry': return '🙂 Hơi đói';
                case 'very_hungry': return '😟 Rất đói';
                case 'weak': return '😢 Yếu';
                case 'dormant': return '💤 Ngủ đông';
                default: return 'Bình thường';
            }
        }
    };
}

if (typeof window !== 'undefined') {
    window.petFloatingCompanion = petFloatingCompanion;
}

if (typeof Alpine !== 'undefined' && Alpine.data) {
    Alpine.data('petFloatingCompanion', (cfg) => petFloatingCompanion(cfg));
} else if (typeof document !== 'undefined') {
    document.addEventListener('alpine:init', () => {
        if (typeof Alpine !== 'undefined' && Alpine.data) {
            Alpine.data('petFloatingCompanion', (cfg) => petFloatingCompanion(cfg));
        }
    });
}
