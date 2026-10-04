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

        // Interaction Engine & Memory Loop State
        consecutivePokes: 0,
        lastPokeTime: 0,
        consecutiveTimer: null,
        pressStartTime: 0,
        pressTimer: null,
        isLongPress: false,
        isHovered: false,
        keyBuffer: '',
        keyBufferTimer: null,
        interactionStats: {
            poke_count: 0,
            cuddle_count: 0,
            consecutive_pokes: 0,
            interaction_level: 'newcomer'
        },
        recentDialoguesHistory: [],
        miniQuizActive: false,
        miniQuizAnswered: false,
        miniQuizFeedback: '',
        currentMiniQuiz: null,
        recentQuizWordIds: [],

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
                        if (data.interaction_stats) {
                            this.interactionStats = data.interaction_stats;
                        }
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

            // Bind key sequence listener with hover/focus guardrail
            if (typeof window !== 'undefined') {
                window.addEventListener('keydown', (e) => this.handleKeySequence(e));
            }

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
            PetEventBus.on('pet:mini-quiz-completed', (payload) => this.handleMiniQuizCompletedReaction(payload));

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
            if (this.state === 'eating') return 'Nhai nhóp nhép ngon quá... 😋';
            if (this.state === 'petting') return 'Thích quá... ❤️';
            if (this.state === 'cuddle') return 'Ấm áp quá... 🥰';
            if (this.state === 'dizzy') return 'Chóng mặt quá... 😵';
            if (this.state === 'poked' || this.state === 'surprised') return 'Ủa! 😳';
            return this.pet?.name || 'Pet đồng hành';
        },

        getPetExpression() {
            if (this.state === 'dizzy') return 'dizzy';
            if (this.state === 'cuddle') return 'cuddle';
            if (this.state === 'petting') return 'petting';
            if (this.state === 'happy' || this.state === 'excited') return 'happy';
            if (this.state === 'poked' || this.state === 'surprised') return 'surprised';
            if (this.state === 'speaking') return 'talking';
            if (this.state === 'sleeping') return 'sleeping';
            if (this.state === 'dozing') return 'dozing';
            if (this.state === 'waking_up') return 'waking';
            if (this.state === 'eating') return 'eating';
            if (this.state === 'tea') return 'tea';
            if (this.state === 'hungry' || (this.pet && this.pet.hunger < 40)) return 'hungry';
            return 'idle';
        },

        // Primary Single-Click Action & FSM Reactions
        handleMascotClick() {
            if (this.isLongPress) {
                this.isLongPress = false;
                return;
            }

            this.hoverHint = false;
            const now = Date.now();

            // 1. If currently sleeping, wake up pet first!
            if (this.state === 'sleeping' || this.lifeEngine?.currentState === 'sleeping') {
                this.wakeUpPet();
                return;
            }

            // 2. Track consecutive pokes (<3.5s window)
            if (now - this.lastPokeTime < 3500) {
                this.consecutivePokes++;
            } else {
                this.consecutivePokes = 1;
            }
            this.lastPokeTime = now;
            clearTimeout(this.consecutiveTimer);
            this.consecutiveTimer = setTimeout(() => {
                this.consecutivePokes = 0;
            }, 3500);

            const consecutive = this.consecutivePokes;
            let action = 'poke';
            let chosenDialogue = '';

            // 3. FSM reaction branch with genuine facial expression changes
            if (consecutive >= 6) {
                // Dizzy Easter Egg!
                action = 'dizzy';
                this.state = 'dizzy';
                if (this.canPlayAudio()) {
                    PetAudioEngine.pop();
                    PetAudioEngine.chirp(1.4);
                }
                chosenDialogue = "哎呀，头好晕呀！(Āiyā, tóu hǎo yūn ya!) Oái, hoa hết cả mắt rồi nè! Chóng mặt quá, tha cho tớ đi mà~ 😵💫";
                setTimeout(() => {
                    if (this.state === 'dizzy') {
                        if (this.speechBubbleOpen) this.state = 'speaking';
                        else this.determineInitialState();
                    }
                }, 2800);
            } else if (consecutive >= 3) {
                // Annoyed / Playful consecutive reactions
                action = 'poke';
                this.state = 'poked';
                if (this.canPlayAudio()) {
                    PetAudioEngine.chirp(1.25);
                }
                const consecutivePool = [
                    "别闹啦，快去学习！(Bié nào la, kuài qù xuéxí!) Haha đừng trêu nữa, mau tập trung học bài đi nào! 📚",
                    "哈哈，好痒好痒！(Hāha, hǎo yǎng hǎo yǎng!) Haha nhột quá đi thôi! Cậu chọc lét tớ à? 😆",
                    "真拿你没办法~ (Zhēn ná nǐ méi bànfǎ~) Thật là hết cách với cậu luôn á~ Chọc hoài à! 🎈"
                ];
                chosenDialogue = this.pickNonRepeatingDialogue(consecutivePool);
                setTimeout(() => {
                    if (this.state === 'poked') this.state = 'happy';
                }, 600);
            } else if (consecutive === 2) {
                // Repeated poke
                action = 'poke';
                this.state = 'poked';
                this.lifeEngine?.handlePoke();
                if (this.canPlayAudio()) {
                    PetAudioEngine.pop();
                }
                const repeatPool = [
                    "你又戳我啦……(Nǐ yòu chuō wǒ la...) Cậu lại chọc tớ nữa rồi nè... Có chuyện gì thế? 😳",
                    "哎呀，怎么又来啦！(Āiyā, zěnme yòu lái la!) Oái, sao lại bấm tớ tiếp thế!",
                    "别急别急，慢慢戳！(Bié jí bié jí, mànman chuō!) Từ từ thôi nào, chọc gì mà vội vàng thế!"
                ];
                chosenDialogue = this.pickNonRepeatingDialogue(repeatPool);
                setTimeout(() => {
                    if (this.state === 'poked') this.state = 'happy';
                }, 800);
            } else {
                // Single-click primary reaction: instant surprised blink then happy smile
                action = 'poke';
                this.state = 'surprised';
                this.lifeEngine?.handlePoke();
                if (this.canPlayAudio()) {
                    PetAudioEngine.pop();
                    PetAudioEngine.chirp(1.1);
                }
                this.spawnHeart();

                setTimeout(() => {
                    if (this.state === 'surprised') {
                        this.state = 'happy';
                    }
                }, 450);

                chosenDialogue = this.determineResponsiveDialogue();
            }

            // Immediate 0ms UI reaction (Speech Bubble + Dialogue + TTS)
            this.setDialogueAndSpeak(chosenDialogue);
            this.speechBubbleOpen = true;
            this.scheduleBubbleAutoDismiss();

            // Background server sync with optimistic stats
            this.syncInteraction(action, consecutive);
        },

        // Alias for backwards compatibility
        pokePet() {
            this.handleMascotClick();
        },

        wakeUpPet() {
            this.state = 'waking_up';
            this.lifeEngine?.wakeUp();
            if (this.canPlayAudio()) {
                PetAudioEngine.chirp(1.1);
            }
            this.spawnHeart();

            const wakePool = [
                "早安！我充满电啦！(Zǎo'ān! Wǒ chōngmǎn diàn la!) Oáp~ Tớ tỉnh ngủ rồi nè! Sạc đầy năng lượng để cùng cậu học bài rồi! ☀️",
                "睡醒啦，今天也一起努力！(Shuì xǐng la, jīntiān yě yīqǐ nǔlì!) Tớ đã dậy rồi, hôm nay chúng mình lại cùng nhau cố gắng nhé! 🚀",
                "揉揉眼睛，看到你真好。(Róurou yǎnjīng, kàndào nǐ zhēn hǎo.) Dụi dụi mắt một cái, mở mắt ra thấy bạn học là vui nhất trần đời! ✨"
            ];
            const dialogue = this.pickNonRepeatingDialogue(wakePool);
            this.setDialogueAndSpeak(dialogue);
            this.speechBubbleOpen = true;
            this.scheduleBubbleAutoDismiss();

            this.syncInteraction('wake', 1);

            setTimeout(() => {
                if (this.state === 'waking_up') {
                    if (this.speechBubbleOpen) this.state = 'speaking';
                    else this.determineInitialState();
                }
            }, 2000);
        },

        startPress() {
            this.isLongPress = false;
            this.pressStartTime = Date.now();
            clearTimeout(this.pressTimer);
            this.pressTimer = setTimeout(() => {
                this.isLongPress = true;
                this.triggerCuddle();
            }, 1500);
        },

        endPress() {
            clearTimeout(this.pressTimer);
        },

        async triggerCuddle() {
            this.hoverHint = false;
            this.state = 'cuddle';
            this.spawnHeart();
            if (this.canPlayAudio()) {
                PetAudioEngine.purr();
            }

            const cuddlePool = [
                "抱抱！感觉好温暖呀。(Bàobào! Gǎnjué hǎo wēnnuǎn ya!) Được bạn ôm tớ thấy ấm áp và hạnh phúc lắm! 🥰",
                "好舒服，谢谢你的拥抱！(Hǎo shūfu, xièxie nǐ de yōngbào!) Thật là dễ chịu, cảm ơn cái ôm ấm lòng của bạn nhé! ❤️",
                "有你陪着，心里超踏实。(Yǒu nǐ péizhe, xīnlǐ chāo tàshi!) Có bạn ở bên, trong lòng tớ luôn thấy bình yên và hạnh phúc 🌸"
            ];
            const dialogue = this.pickNonRepeatingDialogue(cuddlePool);
            this.setDialogueAndSpeak(dialogue);
            this.speechBubbleOpen = true;
            this.scheduleBubbleAutoDismiss();

            this.syncInteraction('cuddle', 1);

            setTimeout(() => {
                if (this.state === 'cuddle') {
                    if (this.speechBubbleOpen) this.state = 'speaking';
                    else this.determineInitialState();
                }
            }, 2500);
        },

        determineResponsiveDialogue() {
            // Check starving
            if ((this.pet?.hunger ?? 100) <= 20 && this.pet?.is_active) {
                const hungryPool = [
                    "肚子咕咕叫了，好饿呀……(Dùzi gūgū jiào le, hǎo è ya...) Bụng tớ đang réo ùng ục rồi nè, vào phòng cho tớ ăn chút đi mà~ 🥣",
                    "没力气啦，想吃好吃的！(Méi lìqi la, xiǎng chī hǎochī de!) Hết sạch năng lượng rồi, thèm một món ngon do cậu thưởng quá! 🥺"
                ];
                return this.pickNonRepeatingDialogue(hungryPool);
            }

            // Check late night (23h - 5h)
            const hour = new Date().getHours();
            if (hour >= 23 || hour < 5) {
                const nightPool = [
                    "夜深了，注意休息哦。(Yè shēn le, zhùyì xiūxi ó.) Khuya lắm rồi... học bài xong nhớ ngủ sớm giữ gìn sức khỏe nhé, mai gặp lại! 🌙",
                    "快去睡觉吧，明天见！(Kuài qù shuìjiào ba, míngtiān jiàn!) Đi ngủ thật ngon thôi nào, chúc bạn học của tớ có giấc mơ đẹp! 💤"
                ];
                return this.pickNonRepeatingDialogue(nightPool);
            }

            // Check page context (35% chance)
            const ctx = config.pageContext || 'other';
            if (Math.random() < 0.35 && ['flashcard', 'quiz', 'lesson'].includes(ctx)) {
                if (ctx === 'flashcard') {
                    return "一张一张翻，把生词都记住！(Yī zhāng yī zhāng fān, bǎ shēngcí dōu jìzhù!) Lật từng tấm thẻ thật tập trung, cùng làm chủ toàn bộ chữ Hán nào! 🃏";
                }
                if (ctx === 'quiz') {
                    return "仔细读题，你可以拿满分的！(Zǐxì dú tí, nǐ kěyǐ ná mǎnfēn de!) Đọc đề thật cẩn thận nha, tớ tin cậu chắc chắn sẽ đạt điểm tuyệt đối! 🎯";
                }
                if (ctx === 'lesson') {
                    return "循序渐进，一课一课通关！(Xúnxù jiànjìn, yī kè yī kè tōngguān!) Từng bước vững chắc, chinh phục từng bài học một cách tự tin nhé! 📖";
                }
            }

            // Interaction Memory Loop: dialogue conditioned on historical pokes
            const level = this.interactionStats?.interaction_level || 'newcomer';
            if (level === 'soulmate') {
                const soulmatePool = [
                    "你真的很喜欢戳我呀！(Nǐ zhēn de hěn xǐhuan chuō wǒ ya!) Cậu thực sự thích chọc tớ ghê á! Thôi cho cậu chọc đó, có cậu ở cạnh vui lắm~ ❤️",
                    "无论什么时候，我都在你身边。(Wúlùn shénme shíhou, wǒ dōu zài nǐ shēnbiān.) Bất kể lúc nào, tớ cũng luôn ở đây đồng hành học tiếng Trung cùng cậu! 🥰",
                    "我们是最好的学习搭档！(Wǒmen shì zuì hǎo de xuéxí dādàng!) Đôi bạn học tuyệt vời nhất quả đất chính là chúng mình! 🐉✨"
                ];
                return this.pickNonRepeatingDialogue(soulmatePool);
            } else if (level === 'familiar') {
                const familiarPool = [
                    "又来了…… 找我有事吗？(Yòu lái le... Zhǎo wǒ yǒu shì ma?) Lại trêu tớ rồi... Có chữ nào khó hiểu cần tớ giúp không nào? 🔍",
                    "嗨！今天状态看起来不错！(Hāi! Jīntiān zhuàngtài kàn qǐlai bùcuò!) Chào cậu! Trông tinh thần học tập hôm nay của cậu đỉnh quá nè! ☀️",
                    "我们已经越来越有默契了！(Wǒmen yǐjīng yuè lái yuè yǒu mòqì le!) Chúng mình ngày càng hiểu ý nhau hơn rồi đó, học tiếp thôi! 🍵"
                ];
                return this.pickNonRepeatingDialogue(familiarPool);
            } else {
                // Newcomer (<10 pokes)
                const newcomerPool = [
                    "哎呀，你碰我啦！(Āiyā, nǐ pèng wǒ la!) Oái, cậu vừa chạm vào tớ kìa! Chào bạn học mới nhé! ✨",
                    "你好呀！今天我们学什么？(Nǐ hǎo ya! Jīntiān wǒmen xué shénme?) Xin chào! Hôm nay chúng mình sẽ cùng học nội dung gì nào? 🎈",
                    "初次见面，请多关照哦！(Chūcì jiànmiàn, qǐng duō guānzhào ó!) Lần đầu đồng hành, hãy chiếu cố và giúp đỡ tớ nhiều nha! 🌸"
                ];
                return this.pickNonRepeatingDialogue(newcomerPool);
            }
        },

        pickNonRepeatingDialogue(pool) {
            if (!pool || pool.length === 0) return '你好呀！';
            const available = pool.filter(d => !this.recentDialoguesHistory.includes(d));
            const selected = available.length > 0
                ? available[Math.floor(Math.random() * available.length)]
                : pool[Math.floor(Math.random() * pool.length)];
            return selected;
        },

        recordDialogueHistory(dialogue) {
            this.recentDialoguesHistory.push(dialogue);
            if (this.recentDialoguesHistory.length > 5) {
                this.recentDialoguesHistory.shift();
            }
        },

        setDialogueAndSpeak(dialogue) {
            this.currentDialogue = dialogue;
            this.recordDialogueHistory(dialogue);
            if (this.voiceEnabled) {
                const parsed = this.parseDialogueItem(dialogue);
                if (parsed.audio_text || parsed.chinese) {
                    PetVoiceManager.speak(parsed.audio_text || parsed.chinese);
                }
            }
        },

        async syncInteraction(action, consecutive = 1) {
            try {
                const endpoint = config.interactUrl || '/student/pet/interact';
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                const res = await fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        action: action,
                        consecutive: consecutive,
                        page_context: config.pageContext || 'other',
                    }),
                });
                if (res.ok) {
                    const data = await res.json();
                    if (data && data.stats) {
                        this.interactionStats = data.stats;
                    }
                }
            } catch (e) {}
        },

        handleKeySequence(e) {
            // Strict guardrail: Pet must be hovered or focused
            if (!this.isHovered) return;
            const target = e.target;
            const tag = target ? target.tagName : '';
            if (tag === 'INPUT' || tag === 'TEXTAREA' || target?.isContentEditable) return;

            if (e.key && e.key.length === 1 && /[a-zA-Z]/.test(e.key)) {
                this.keyBuffer += e.key.toLowerCase();
                if (this.keyBuffer.length > 20) {
                    this.keyBuffer = this.keyBuffer.slice(-20);
                }
                clearTimeout(this.keyBufferTimer);
                this.keyBufferTimer = setTimeout(() => { this.keyBuffer = ''; }, 4000);

                if (this.keyBuffer.endsWith('dragon')) {
                    this.keyBuffer = '';
                    this.triggerDragonEasterEgg();
                } else if (this.keyBuffer.endsWith('hsk')) {
                    this.keyBuffer = '';
                    this.triggerHskEasterEgg();
                }
            }
        },

        triggerDragonEasterEgg() {
            this.state = 'excited';
            if (this.canPlayAudio()) {
                PetAudioEngine.celebration();
            }
            this.spawnHeart();
            const easterEggDialogue = "神龙现身！召唤神龙成功啦！(Shénlóng xiànshēn!) Chúc mừng bạn đã giải mã mật mã bí mật Tiểu Long! 🐉✨";
            this.setDialogueAndSpeak(easterEggDialogue);
            this.speechBubbleOpen = true;
            this.scheduleBubbleAutoDismiss();
        },

        triggerHskEasterEgg() {
            this.state = 'happy';
            if (this.canPlayAudio()) {
                PetAudioEngine.levelUp();
            }
            this.spawnHeart();
            const easterEggDialogue = "HSK必胜！(HSK bì shèng!) Chúc bạn thi đỗ HSK điểm số tối đa! Cùng Tiểu Long cố gắng nhé! 🎓🎉";
            this.setDialogueAndSpeak(easterEggDialogue);
            this.speechBubbleOpen = true;
            this.scheduleBubbleAutoDismiss();
        },

        async startMiniQuiz() {
            this.miniQuizActive = true;
            this.miniQuizAnswered = false;
            this.miniQuizFeedback = '';
            this.speechBubbleOpen = true;

            // Fetch a fresh, non-repeating quiz from server
            let quiz = null;
            try {
                const endpoint = config.miniQuizUrl || '/student/pet/mini-quiz';
                const excludeQuery = (this.recentQuizWordIds && this.recentQuizWordIds.length > 0)
                    ? `?exclude_ids=${this.recentQuizWordIds.join(',')}`
                    : '';
                const res = await fetch(`${endpoint}${excludeQuery}`, {
                    headers: { 'Accept': 'application/json' }
                });
                if (res.ok) {
                    const data = await res.json();
                    if (data && data.success && data.options && data.options.length > 0) {
                        quiz = data;
                    }
                }
            } catch (e) {
                // Network failure fallback
            }

            // Rich rotating fallback pool (20 diverse vocabulary items) if server request failed
            if (!quiz) {
                const fallbackPool = [
                    { word_id: 101, word: '学习', pinyin: 'xuéxí', question: 'Chữ 「学习」[xuéxí] có nghĩa là gì?', correct: 'Học tập', options: ['Học tập', 'Ăn cơm', 'Uống nước', 'Đi ngủ'] },
                    { word_id: 102, word: '朋友', pinyin: 'péngyou', question: 'Chữ 「朋友」[péngyou] có nghĩa là gì?', correct: 'Bạn bè', options: ['Bạn bè', 'Thầy cô', 'Gia đình', 'Bác sĩ'] },
                    { word_id: 103, word: '高兴', pinyin: 'gāoxìng', question: 'Chữ 「高兴」[gāoxìng] có nghĩa là gì?', correct: 'Vui mừng', options: ['Vui mừng', 'Tức giận', 'Buồn bã', 'Mệt mỏi'] },
                    { word_id: 104, word: '谢谢', pinyin: 'xièxie', question: 'Chữ 「谢谢」[xièxie] có nghĩa là gì?', correct: 'Cảm ơn', options: ['Cảm ơn', 'Tạm biệt', 'Xin chào', 'Không có gì'] },
                    { word_id: 105, word: '苹果', pinyin: 'píngguǒ', question: 'Chữ 「苹果」[píngguǒ] có nghĩa là gì?', correct: 'Quả táo', options: ['Quả táo', 'Quả chuối', 'Quả dưa', 'Quả đào'] },
                    { word_id: 106, word: '喝茶', pinyin: 'hē chá', question: 'Từ 「喝茶」[hē chá] có nghĩa là gì?', correct: 'Uống trà', options: ['Uống trà', 'Ăn cơm', 'Nấu ăn', 'Rửa bát'] },
                    { word_id: 107, word: '学校', pinyin: 'xuéxiào', question: 'Chữ 「学校」[xuéxiào] có nghĩa là gì?', correct: 'Trường học', options: ['Trường học', 'Bệnh viện', 'Ngân hàng', 'Sân bay'] },
                    { word_id: 108, word: '再见', pinyin: 'zàijiàn', question: 'Chữ 「再见」[zàijiàn] có nghĩa là gì?', correct: 'Tạm biệt', options: ['Tạm biệt', 'Hẹn gặp lại', 'Xin lỗi', 'Hoan nghênh'] },
                    { word_id: 109, word: '老师', pinyin: 'lǎoshī', question: 'Chữ 「老师」[lǎoshī] có nghĩa là gì?', correct: 'Thầy cô giáo', options: ['Thầy cô giáo', 'Học sinh', 'Hiệu trưởng', 'Bạn học'] },
                    { word_id: 110, word: '天气', pinyin: 'tiānqì', question: 'Chữ 「天气」[tiānqì] có nghĩa là gì?', correct: 'Thời tiết', options: ['Thời tiết', 'Mùa xuân', 'Bầu trời', 'Ánh nắng'] },
                    { word_id: 111, word: '喜欢', pinyin: 'xǐhuan', question: 'Chữ 「喜欢」[xǐhuan] có nghĩa là gì?', correct: 'Thích', options: ['Thích', 'Ghét', 'Sợ', 'Yêu thương'] },
                    { word_id: 112, word: '明天', pinyin: 'míngtiān', question: 'Chữ 「明天」[míngtiān] có nghĩa là gì?', correct: 'Ngày mai', options: ['Ngày mai', 'Hôm nay', 'Hôm qua', 'Năm sau'] },
                    { word_id: 113, word: '漂亮', pinyin: 'piàoliang', question: 'Chữ 「漂亮」[piàoliang] có nghĩa là gì?', correct: 'Xinh đẹp', options: ['Xinh đẹp', 'Thông minh', 'Dễ thương', 'Hiền lành'] },
                    { word_id: 114, word: '中文', pinyin: 'zhōngwén', question: 'Chữ 「中文」[zhōngwén] có nghĩa là gì?', correct: 'Tiếng Trung', options: ['Tiếng Trung', 'Chữ Hán', 'Tiếng Anh', 'Văn hóa'] },
                    { word_id: 115, word: '中国', pinyin: 'zhōngguó', question: 'Từ có nghĩa là "Trung Quốc" viết thế nào?', correct: '中国', options: ['中国', '美国', '英国', '越南'] }
                ];

                const available = fallbackPool.filter(p => !this.recentQuizWordIds.includes(p.word_id));
                const candidates = available.length > 0 ? available : fallbackPool;
                const picked = candidates[Math.floor(Math.random() * candidates.length)];
                quiz = {
                    ...picked,
                    options: [...picked.options].sort(() => 0.5 - Math.random())
                };
            }

            this.currentMiniQuiz = quiz;
            if (quiz.word_id) {
                this.recentQuizWordIds.push(quiz.word_id);
                if (this.recentQuizWordIds.length > 20) {
                    this.recentQuizWordIds.shift();
                }
            }

            setTimeout(() => window.refreshIcons?.(), 50);
        },

        async answerMiniQuiz(selected) {
            if (this.miniQuizAnswered || !this.currentMiniQuiz) return;
            this.miniQuizAnswered = true;

            const isCorrect = (selected === this.currentMiniQuiz.correct);

            // Audio & pronunciation reinforcement
            if (this.currentMiniQuiz?.word && this.voiceEnabled) {
                PetVoiceManager.speak(this.currentMiniQuiz.word);
            }

            if (isCorrect) {
                this.state = 'excited';
                this.miniQuizFeedback = '🎉 Chính xác! Đang nhận +2 XP thưởng...';
                if (this.canPlayAudio()) {
                    PetAudioEngine.celebration();
                }
                this.spawnHeart();

                // Server-Authoritative XP reward event
                PetEventBus.emit('pet:mini-quiz-completed', {
                    correct: true,
                    word: this.currentMiniQuiz.word
                });

                try {
                    const endpoint = config.miniQuizRewardUrl || '/student/pet/mini-quiz-reward';
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                    const res = await fetch(endpoint, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            correct: true,
                            idempotency_key: 'mascot_quiz_' + Date.now() + '_' + Math.random().toString(36).slice(2, 8)
                        })
                    });
                    if (res.ok) {
                        const data = await res.json();
                        this.miniQuizFeedback = `🎉 Xuất sắc! +${data.xp_earned || 2} XP học tập!`;
                    }
                } catch (e) {
                    this.miniQuizFeedback = '🎉 Chính xác!';
                }
            } else {
                this.state = 'surprised';
                this.miniQuizFeedback = `Chưa đúng rồi! Đáp án là "${this.currentMiniQuiz.correct}"`;
                if (this.canPlayAudio()) {
                    PetAudioEngine.pop();
                }
            }

            setTimeout(() => {
                this.miniQuizActive = false;
                this.miniQuizAnswered = false;
                this.miniQuizFeedback = '';
                this.determineInitialState();
                setTimeout(() => window.refreshIcons?.(), 50);
            }, 3000);
        },

        handleMiniQuizCompletedReaction(payload = {}) {
            this.onUserActivity();
            this.state = 'excited';
            if (this.canPlayAudio()) {
                PetAudioEngine.celebration();
            }
            this.spawnHeart();
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
                        this.state = 'eating';
                        const foodLabel = data.food ? `${data.food.emoji} ${data.food.hanzi}: ` : '';
                        const reaction = data.food_reaction || `+${data.xp_fed} EXP thành công!`;
                        this.feedNotice = `${foodLabel}${reaction}`;
                        setTimeout(() => {
                            this.state = 'happy';
                            setTimeout(() => {
                                if (this.speechBubbleOpen) this.state = 'speaking';
                                else this.determineInitialState();
                            }, 1800);
                        }, 1600);
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

        parseDialogueItem(item) {
            if (!item) return { chinese: '你好呀！', pinyin: '', vietnamese: '', audio_text: '你好呀！' };
            if (typeof item === 'object') {
                return {
                    chinese: item.chinese || '',
                    pinyin: item.pinyin || '',
                    vietnamese: item.vietnamese || '',
                    audio_text: item.audio_text || item.chinese || ''
                };
            }
            const str = String(item).trim();
            const match = str.match(/^([^\(\)（）]+?)\s*[\(（]([^\(\)（）]+?)[\)）]\s*(.*)$/u);
            if (match) {
                const chinese = match[1].trim();
                const pinyin = match[2].trim();
                const vietnamese = match[3].trim();
                const audioText = chinese.replace(/[\u{1F600}-\u{1F64F}\u{1F300}-\u{1F5FF}\u{1F680}-\u{1F6FF}\u{1F1E0}-\u{1F1FF}\u{2600}-\u{26FF}\u{2700}-\u{27BF}\u{FE00}-\u{FE0F}\u{1F900}-\u{1F9FF}\u{1F018}-\u{1F270}]/gu, '').trim() || chinese;
                return { chinese, pinyin, vietnamese, audio_text: audioText };
            }
            const cnMatches = str.match(/[\u4e00-\u9fa5，。？！、\s]+/g);
            if (cnMatches) {
                const cnStr = cnMatches.join('').trim();
                return { chinese: cnStr, pinyin: '', vietnamese: str, audio_text: cnStr };
            }
            return { chinese: '你好呀！', pinyin: 'Nǐ hǎo ya!', vietnamese: str, audio_text: '你好呀！' };
        },

        speakCurrentDialogue() {
            const parsed = this.parseDialogueItem(this.currentDialogue);
            this.speakWord(parsed.audio_text || parsed.chinese);
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
