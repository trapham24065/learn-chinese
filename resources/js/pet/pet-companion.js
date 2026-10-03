/**
 * Pet Learning Companion Coordinator (pet-companion.js)
 * Bridges Alpine UI, Living Mascot animations, PetEventBus,
 * PetAudioEngine (procedural SFX), and PetVoiceManager (Chinese TTS).
 */

import { PetEventBus } from './PetEventBus.js';
import { PetAudioEngine } from './PetAudioEngine.js';
import { PetVoiceManager } from './PetVoiceManager.js';
import { PetLifeEngine } from './PetLifeEngine.js';

export function petFloatingCompanion(config = {}) {
    return {
        lifeEngine: null,
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
            this.initLifeEngine();

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
                        if (this.lifeEngine) {
                            this.lifeEngine.setPersonality(data.personality);
                            this.lifeEngine.setHunger(data.hunger);
                            this.lifeEngine.evaluateNextState(true);
                        }
                    }
                }
            } catch (e) {}

            // Attach element to lifeEngine for cursor tracking
            setTimeout(() => {
                if (this.$refs && this.$refs.petWrapper && this.lifeEngine) {
                    this.lifeEngine.setPetElement(this.$refs.petWrapper);
                }
            }, 100);

            // Bind unified event bus listeners
            this.bindEventBusListeners();

            setTimeout(() => window.refreshIcons?.(), 100);
        },

        initLifeEngine() {
            this.lifeEngine = new PetLifeEngine({
                pageContext: config.pageContext || 'other',
                personality: this.pet?.personality || 'playful',
                hunger: this.pet?.hunger ?? 100,
                onStateChange: (newState) => {
                    this.state = newState;
                },
                onPetting: () => {
                    this.showHearts = true;
                    clearTimeout(this.heartTimer);
                    this.heartTimer = setTimeout(() => { this.showHearts = false; }, 2200);
                    if (this.canPlayAudio()) {
                        PetAudioEngine.purr();
                    }
                },
                onPoked: () => {
                    if (this.canPlayAudio()) {
                        const personality = this.pet?.personality || 'playful';
                        const pitch = personality === 'shy' ? 0.85 : (personality === 'playful' ? 1.25 : 1.0);
                        PetAudioEngine.pop();
                        PetAudioEngine.chirp(pitch);
                    }
                },
                onWakeUp: () => {
                    if (this.canPlayAudio()) {
                        PetAudioEngine.chirp(1.1);
                    }
                },
                onWave: () => {
                    this.state = 'waving';
                }
            });
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
            PetEventBus.on('pet:struggle', (payload) => this.handleStruggleReaction(payload));
            PetEventBus.on('pet:affinity-up', (payload) => this.handleAffinityUpReaction(payload));

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
            if (this.state === 'dozing') return 'Gật gù buồn ngủ... 😴';
            if (this.state === 'reading') return 'Đang cùng học bài 📖';
            if (this.state === 'observing') return 'Đang chăm chú theo dõi 👀';
            if (this.state === 'hungry') return 'Tớ hơi đói... 🥺';
            if (this.state === 'petting') return 'Thích quá... ❤️';
            if (this.state === 'poked') return 'Ủa! 😳';
            return this.pet?.name || 'Pet đồng hành';
        },

        pokePet() {
            this.hoverHint = false;
            if (this.lifeEngine) {
                this.lifeEngine.handlePoke();
            }
        },

        toggleSpeechBubble() {
            this.speechBubbleOpen = !this.speechBubbleOpen;
            if (this.speechBubbleOpen) {
                if (this.canPlayAudio()) {
                    PetAudioEngine.pop();
                }
                this.scheduleBubbleAutoDismiss();
            } else {
                PetVoiceManager.stop();
            }
            setTimeout(() => window.refreshIcons?.(), 50);
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

                    // Play Munch SFX & Voice Reaction
                    if (this.canPlayAudio()) {
                        PetAudioEngine.munch();
                        if (data.chinese_say) {
                            setTimeout(() => {
                                PetVoiceManager.speak(data.chinese_say);
                            }, 500);
                        }
                        if (data.hunger >= 95) {
                            setTimeout(() => PetAudioEngine.purr(), 900);
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
                        const foodLabel = data.food ? `${data.food.emoji} ${data.food.hanzi}: ` : '';
                        const reaction = data.food_reaction || `+${data.xp_fed} EXP thành công!`;
                        this.feedNotice = `${foodLabel}${reaction}`;
                        setTimeout(() => {
                            if (this.speechBubbleOpen) this.state = 'speaking';
                            else this.determineInitialState();
                        }, 2500);
                    }
                    setTimeout(() => { this.feedNotice = ''; }, 4500);
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
            const personality = this.pet?.personality || 'playful';

            if (this.canPlayAudio()) {
                const pitch = personality === 'playful' ? 1.25
                            : personality === 'shy' ? 0.95
                            : personality === 'cheerful' ? 1.2
                            : 1.05;
                PetAudioEngine.chirp(pitch);
            }
            this.spawnHeart();

            const hanzi = payload.hanzi || payload.word || '';
            const pinyin = payload.pinyin ? `[${payload.pinyin}]` : '';
            const meaning = payload.meaning ? ` ("${payload.meaning}")` : '';

            // 1. Immediately update random_word in pet state so the vocabulary companion card updates
            if (hanzi) {
                this.pet.random_word = {
                    hanzi: hanzi,
                    pinyin: payload.pinyin || '',
                    meaning: payload.meaning || ''
                };
            }

            // 2. Generate lively personality-specific congratulations for this specific word
            let congrats = '';
            switch (personality) {
                case 'playful':
                    congrats = `Yatta! Cậu vừa chinh phục từ 「${hanzi}」${meaning} siêu đỉnh luôn! Tớ tặng cậu 10 điểm tinh nghịch nè! 🎮✨`;
                    break;
                case 'curious':
                    congrats = `Oa! Từ 「${hanzi}」${pinyin}${meaning} là một từ rất thú vị đó! Để tớ ghi nhớ thật kỹ cùng cậu nhé! 🔍💡`;
                    break;
                case 'shy':
                    congrats = `Giỏi quá đi mất... Cậu nhớ được từ 「${hanzi}」${meaning} rồi kìa! Nhìn cậu học tớ vui lắm 🌸💕`;
                    break;
                case 'cheerful':
                    congrats = `Tuyệt vời ông mặt trời! Đã làm chủ từ 「${hanzi}」${meaning} rồi! Cứ đà này HSK trong tầm tay nhé! ☀️🌟`;
                    break;
                case 'calm':
                default:
                    congrats = `Tâm an trí sáng. Bạn đã thấu suốt từ 「${hanzi}」${meaning}. Từng bước tiến bộ rất vững vàng 🍵🍃`;
                    break;
            }

            this.currentDialogue = congrats;

            // 3. Prepend into dialogues array so nextDialogue() cycles smoothly without old word loops
            if (!Array.isArray(this.pet.dialogues)) {
                this.pet.dialogues = [];
            }
            this.pet.dialogues = this.pet.dialogues.filter(d => !d.includes('vừa chinh phục') && !d.includes('làm chủ từ') && !d.includes('nhớ được từ'));
            this.pet.dialogues.unshift(congrats);
            this.dialogueIndex = 0;

            this.speechBubbleOpen = true;
            this.scheduleBubbleAutoDismiss();

            // 4. Silently refresh pet status with recent_word in the background
            this.refreshPetStatsSilently(hanzi);

            setTimeout(() => {
                if (this.state === 'excited') {
                    this.determineInitialState();
                }
            }, 4500);
            setTimeout(() => window.refreshIcons?.(), 50);
        },

        async refreshPetStatsSilently(recentWord = '') {
            try {
                let statusEndpoint = config.statusUrl || '/student/pet/status';
                if (recentWord) {
                    const separator = statusEndpoint.includes('?') ? '&' : '?';
                    statusEndpoint += `${separator}recent_word=${encodeURIComponent(recentWord)}`;
                }
                const res = await fetch(statusEndpoint, {
                    headers: { 'Accept': 'application/json' }
                });
                if (res.ok) {
                    const data = await res.json();
                    if (data && data.has_pet) {
                        this.pet.hunger = data.hunger;
                        this.pet.hunger_state = data.hunger_state;
                        this.pet.exp = data.exp;
                        this.pet.daily_remaining = data.daily_remaining;
                        this.pet.affinity = data.affinity;
                        this.pet.affinity_tier = data.affinity_tier;
                        if (data.dialogues && data.dialogues.length > 0) {
                            this.pet.dialogues = [this.currentDialogue, ...data.dialogues.filter(d => d !== this.currentDialogue)];
                        }
                    }
                }
            } catch (e) {}
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
        },

        getAffinityPillClass() {
            if (!this.pet || !this.pet.affinity_tier) return 'bg-slate-50 text-slate-600 border-slate-200';
            const tier = this.pet.affinity_tier.tier;
            switch (tier) {
                case 'partner': return 'bg-rose-50 text-rose-700 border-rose-300';
                case 'close_friend': return 'bg-purple-50 text-purple-700 border-purple-300';
                case 'companion': return 'bg-amber-50 text-amber-700 border-amber-300';
                case 'familiar': return 'bg-emerald-50 text-emerald-700 border-emerald-300';
                case 'met': return 'bg-blue-50 text-blue-700 border-blue-300';
                default: return 'bg-slate-50 text-slate-600 border-slate-200';
            }
        },

        handleStruggleReaction(payload = {}) {
            this.onUserActivity();
            if (this.canPlayAudio()) {
                PetAudioEngine.purr();
            }
            const personality = this.pet?.personality || 'playful';
            const comfortMessages = {
                playful: 'Không sao đâu nè! Sai một chút thôi, thử lại lần nữa là nhớ ngay thôi! 🎮',
                curious: 'Chữ này hơi lắt léo đúng không? Để ý bộ thủ một chút là giải mã được ngay! 🔍',
                shy: 'Đừng buồn nhé... Tớ biết chữ này khó, tớ luôn ở đây cùng bạn mà 🌸',
                cheerful: 'Không bỏ cuộc là bạn đã chiến thắng rồi! Lần tới chắc chắn bạn sẽ làm đúng! ☀️',
                calm: 'Vạn sự khởi đầu nan. Người học giỏi là người kiên nhẫn vượt qua thử thách 🍵'
            };
            this.currentDialogue = payload.message || comfortMessages[personality] || 'Cố lên bạn nhé! Từng bước một bạn sẽ nhớ được! ✨';
            this.speechBubbleOpen = true;
            this.scheduleBubbleAutoDismiss();
            setTimeout(() => window.refreshIcons?.(), 50);
        },

        handleAffinityUpReaction(payload = {}) {
            this.onUserActivity();
            if (this.canPlayAudio()) {
                PetAudioEngine.levelUp();
            }
            this.currentDialogue = `Mối quan hệ giữa hai chúng mình đã nâng lên mức ${payload.tier_name || 'mới'}! 💖 Cảm ơn bạn đã luôn chăm chỉ!`;
            this.speechBubbleOpen = true;
            this.scheduleBubbleAutoDismiss();
            setTimeout(() => window.refreshIcons?.(), 50);
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
