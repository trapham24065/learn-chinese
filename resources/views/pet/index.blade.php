@extends('layouts.app')

@section('title', 'Phòng Nuôi Pet Học Tập | Chinese Deck')

@section('content')
<script>
window.petRoom = function petRoom(config) {
    return {
        activeTab: 'overview',
        dailyRemaining: config.dailyRemaining || 0,
        hunger: config.userPet?.hunger || 100,
        hungerState: config.userPet?.hunger_state || 'happy',
        currentDialogue: config.currentDialogue || 'Cùng học tiếng Trung chăm chỉ nhé!',
        dialogues: config.dialogues || [],
        dialogueIndex: 0,
        masteredWords: config.masteredWords || [],
        vocabSearch: '',
        feeding: false,
        feedMessage: '',
        isError: false,
        foodMenu: config.foodMenu || [],
        eatingFood: null,
        eatingPhase: null, // 'eating' | 'satisfied'
        foodReactionText: '',
        foodChineseSay: '',
        timelineMemories: config.timelineMemories || [],
        roomStats: config.roomStats || {},
        selectedMemoryFilter: 'all',
        roomScenery: (new Date().getHours() >= 18 || new Date().getHours() < 6) ? 'night' : 'day',
        roomTimeMode: (new Date().getHours() >= 18 || new Date().getHours() < 6) ? 'night' : 'day',
        petEmote: '',
        showHeartBurst: false,
        activeHotspot: null,

        // Audio reactivity
        sfxEnabled: (window.PetAudioEngine ? window.PetAudioEngine.isSfxEnabled() : true),
        voiceEnabled: (window.PetVoiceManager ? window.PetVoiceManager.isVoiceEnabled() : true),
        audioVolume: Math.round((window.PetAudioEngine ? window.PetAudioEngine.getVolume() : 0.8) * 100),

        // Rename modal
        renameModalOpen: false,
        renaming: false,
        petNewName: config.userPet?.name || '',

        // Stage up celebration
        celebrationModalOpen: false,
        celebrationTitle: '',
        celebrationDesc: '',
        celebrationEmoji: '🐉',

        // Personality & Affinity
        personality: config.userPet?.personality || 'playful',
        personalityLabel: config.affinitySummary?.personality?.label || 'Tinh nghịch & Vui nhộn',
        personalityEmoji: config.affinitySummary?.personality?.emoji || '✨',
        personalityDesc: config.affinitySummary?.personality?.description || '',
        affinity: config.userPet?.affinity || 0,
        affinityTier: config.affinityTier || {},
        affinitySummary: config.affinitySummary || {},
        savingPersonality: false,
        personalityMessage: '',

        async selectPersonality(type) {
            if (this.savingPersonality || this.personality === type) return;
            this.savingPersonality = true;
            this.personalityMessage = '';
            try {
                const res = await fetch("{{ route('pet.personality') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ personality: type })
                });
                const data = await res.json();
                if (data.success) {
                    this.personality = data.personality;
                    this.personalityLabel = data.label;
                    this.personalityEmoji = data.emoji;
                    this.personalityDesc = data.description;
                    if (data.random_dialogue) this.currentDialogue = data.random_dialogue;
                    if (data.dialogues) this.dialogues = data.dialogues;
                    this.personalityMessage = data.message;
                    if (window.PetAudioEngine) {
                        window.PetAudioEngine.pop();
                    }
                    setTimeout(() => { this.personalityMessage = ''; }, 3500);
                }
            } catch (e) {
                this.personalityMessage = 'Lỗi kết nối khi cập nhật tính cách';
            } finally {
                this.savingPersonality = false;
                setTimeout(() => window.refreshIcons?.(), 50);
            }
        },

        initRoom() {
            setTimeout(() => window.refreshIcons?.(), 100);
        },

        toggleSfx() {
            if (window.PetAudioEngine) {
                this.sfxEnabled = window.PetAudioEngine.toggleSfx();
            } else {
                this.sfxEnabled = !this.sfxEnabled;
            }
        },

        toggleVoice() {
            if (window.PetVoiceManager) {
                this.voiceEnabled = window.PetVoiceManager.toggleVoice();
            } else {
                this.voiceEnabled = !this.voiceEnabled;
            }
        },

        updateVolume(vol) {
            this.audioVolume = parseInt(vol, 10);
            if (window.PetAudioEngine) {
                window.PetAudioEngine.setVolume(this.audioVolume / 100);
            }
        },

        setTab(tab) {
            this.activeTab = tab;
            setTimeout(() => window.refreshIcons?.(), 50);
        },

        nextDialogue() {
            if (!this.dialogues || this.dialogues.length === 0) return;
            this.dialogueIndex = (this.dialogueIndex + 1) % this.dialogues.length;
            this.currentDialogue = this.dialogues[this.dialogueIndex];
            if (window.PetAudioEngine) {
                window.PetAudioEngine.pop();
            }
        },

        setScenery(scenery) {
            this.roomScenery = scenery;
            this.roomTimeMode = (scenery === 'night' || scenery === 'sunset' || scenery === 'rain') ? 'night' : 'day';
            if (window.PetAudioEngine) window.PetAudioEngine.pop();
            this.triggerPetEmote('happy');
            
            switch(scenery) {
                case 'day':
                    this.currentDialogue = 'Bầu trời ban ngày trong xanh rực rỡ! Cùng mở sách học chữ mới thật hứng khởi nào ☀️';
                    this.speakWord('晴天好！');
                    break;
                case 'sunset':
                    this.currentDialogue = 'Hoàng hôn buông sắc cam ấm áp quá... Cậu học cả ngày vất vả rồi, thư giãn một chút nhé 🌅';
                    this.speakWord('傍晚好！');
                    break;
                case 'night':
                    this.currentDialogue = 'Đêm về rồi, ngàn sao lấp lánh trên bầu trời... Cậu nhớ học vừa sức rồi ngủ sớm nha 🌙';
                    this.speakWord('晚安！');
                    break;
                case 'rain':
                    this.currentDialogue = 'Ngoài kia mưa rơi rả rích... Ngồi trong phòng ấm uống trà học bài cùng cậu thật bình yên 🌧️';
                    this.speakWord('下雨了！');
                    break;
            }
            setTimeout(() => window.refreshIcons?.(), 50);
        },

        cycleScenery() {
            const list = ['day', 'sunset', 'night', 'rain'];
            const idx = list.indexOf(this.roomScenery);
            const next = list[(idx + 1) % list.length];
            this.setScenery(next);
        },

        triggerPetEmote(emote) {
            this.petEmote = emote;
            if (emote === 'blush' || emote === 'happy') {
                this.showHeartBurst = true;
                setTimeout(() => this.showHeartBurst = false, 1500);
            }
            if (emote !== 'sleep') {
                setTimeout(() => { if (this.petEmote === emote) this.petEmote = ''; }, 3500);
            }
        },

        pokePet() {
            if (window.PetAudioEngine) window.PetAudioEngine.pop();
            this.triggerPetEmote('blush');
            
            const responses = {
                playful: ['Hi hi! Nhột quá nè~ Cậu muốn chơi đố chữ với tớ à? 😆', 'Oái! Cậu chọc tớ làm tớ cười nghiêng ngả luôn nè~ ✨', 'Haha, tớ cũng chọc lại cậu nè! Học tiếp thôi nào! 🎈'],
                curious: ['Ủa? Cậu chạm tớ có việc gì thế? Có chữ Hán nào cần tra cứu không? 👀', 'Mắt tớ đang dõi theo con trỏ của cậu này! Có gì hay ho thế? 🔍', 'Ồ! Cậu muốn tớ kể nghe chuyện gì về chữ Hán hôm nay à? ✨'],
                shy: ['Ưm... cậu xoa đầu làm tớ ngại ghê á... nhưng mà ấm áp lắm 😳', 'Hì hì... có cậu ở cạnh học cùng tớ vui lắm, mà hơi thẹn chút... 🌸', 'Tớ... tớ thích cậu vuốt ve như vậy lắm á... ❤️'],
                cheerful: ['Cậu chạm tớ một cái là tớ thấy tràn đầy năng lượng liền! ❤️', 'Yay! Hôm nay chúng mình nhất định sẽ học thuộc thật nhiều từ mới! 🌟', 'Vui quá đi! Được làm bạn học cùng cậu là tuyệt nhất trần đời! 🥳'],
                calm: ['Cảm nhận được sự ấm áp từ cậu rồi... Cảm ơn cậu luôn kiên nhẫn cùng tớ nhé 😊', 'Hít thở một hơi thật sâu nào. Chúng mình cứ từ từ học, không cần vội vã 🍵', 'Bình yên bên cạnh cậu... Cùng mở sách ôn lại bài học nhé 🌿']
            };

            const pool = responses[this.personality] || responses['playful'];
            this.currentDialogue = pool[Math.floor(Math.random() * pool.length)];
            this.speakWord('你好呀！');
        },

        petHead() {
            if (window.PetAudioEngine) window.PetAudioEngine.pop();
            this.triggerPetEmote('happy');
            this.currentDialogue = 'Được cậu vuốt ve ấm áp quá đi~ Cảm ơn người bạn học tuyệt vời của tớ! 🥰';
            this.speakWord('谢谢你！');
        },

        talkWithPet() {
            if (window.PetAudioEngine) window.PetAudioEngine.pop();
            this.triggerPetEmote('happy');
            this.nextDialogue();
            const cnMatch = (this.currentDialogue || '').match(/[\u4e00-\u9fa5]+/);
            if (cnMatch) {
                this.speakWord(cnMatch[0]);
            }
        },

        napPet() {
            if (window.PetAudioEngine) window.PetAudioEngine.pop();
            if (this.petEmote === 'sleep') {
                this.triggerPetEmote('happy');
                this.currentDialogue = 'Oáp~ Tớ tỉnh ngủ rồi nè! Tràn đầy năng lượng để học cùng cậu rồi! ☀️';
                this.speakWord('我醒了！');
            } else {
                this.triggerPetEmote('sleep');
                this.currentDialogue = 'Khò khò... Tớ chợp mắt một lát nha, cậu học bài xong nhớ đánh thức tớ nhé 💤';
                this.speakWord('我想睡觉');
            }
        },

        interactRoomItem(type) {
            if (window.PetAudioEngine) window.PetAudioEngine.pop();
            this.activeHotspot = type;
            setTimeout(() => this.activeHotspot = null, 3000);
            this.triggerPetEmote('happy');
            
            const streak = this.roomStats?.streak || 0;
            const words = this.roomStats?.mastered_words || 0;
            const days = this.roomStats?.days_together || 1;

            switch(type) {
                case 'window':
                    this.cycleScenery();
                    break;
                case 'bookshelf':
                    if (this.masteredWords && this.masteredWords.length > 0) {
                        const randomW = this.masteredWords[Math.floor(Math.random() * this.masteredWords.length)];
                        this.currentDialogue = `Kệ sách này có chữ "${randomW.hanzi}" (${randomW.pinyin} - ${randomW.meaning}) cậu dạy mình nè! Cậu còn nhớ phát âm không? 📚✨`;
                        this.speakWord(randomW.hanzi);
                    } else {
                        this.currentDialogue = 'Kệ sách đang chờ đón những chữ Hán đầu tiên cậu dạy cho mình! Cùng mở Flashcard học nhé 📖';
                        this.speakWord('学习');
                    }
                    break;
                case 'plant':
                    this.currentDialogue = streak > 0
                        ? `Chậu cây may mắn này đang xanh tốt nhờ chuỗi học ${streak} ngày bền bỉ của cậu đó! Cùng giữ streak nhé 🌱💚`
                        : 'Chậu mầm cây nhỏ này sẽ lớn nhanh theo mỗi ngày cậu chăm chỉ học tiếng Trung đấy! 🌱';
                    this.speakWord('加油！');
                    break;
                case 'teatable':
                    this.currentDialogue = 'Mời cậu một tách trà Ô Long ấm thơm 🍵 Học tiếng Trung cũng như thưởng trà, cần kiên nhẫn từng ngụm nhỏ!';
                    this.speakWord('喝茶');
                    break;
                case 'frame':
                    this.currentDialogue = `Khung ảnh ghi dấu ${days} ngày chúng mình làm bạn cùng nhau! Cậu xem lại Sổ Kỷ Niệm chưa? 🖼️❤️`;
                    this.speakWord('朋友');
                    break;
                case 'foodbowl':
                    this.currentDialogue = this.roomStats?.recent_food
                        ? `Đĩa món ăn ${this.roomStats.recent_food.emoji} ${this.roomStats.recent_food.hanzi} cậu cho mình vẫn còn ấm áp thơm lừng nè! 😋`
                        : 'Bát thức ăn sạch bóng nè, cậu có muốn đãi mình một món ngon Trung Hoa không? 🥟';
                    if (this.roomStats?.recent_food?.hanzi) {
                        this.speakWord(this.roomStats.recent_food.hanzi);
                    } else {
                        this.speakWord('好吃');
                    }
                    break;
            }
        },

        filteredTimelineMemories() {
            if (!this.timelineMemories || this.timelineMemories.length === 0) return [];
            if (this.selectedMemoryFilter === 'all') {
                return this.timelineMemories;
            }
            return this.timelineMemories.filter(m => m.category === this.selectedMemoryFilter);
        },

        filteredWords() {
            if (!this.vocabSearch || !this.vocabSearch.trim()) return this.masteredWords || [];
            const q = this.vocabSearch.toLowerCase().trim();
            return (this.masteredWords || []).filter(w => 
                (w.hanzi && w.hanzi.toLowerCase().includes(q)) ||
                (w.pinyin && w.pinyin.toLowerCase().includes(q)) ||
                (w.meaning && w.meaning.toLowerCase().includes(q))
            );
        },

        speakWord(hanzi) {
            if (!hanzi) return;
            if (window.PetVoiceManager) {
                window.PetVoiceManager.speak(hanzi);
            } else if ('speechSynthesis' in window) {
                window.speechSynthesis.cancel();
                const utter = new SpeechSynthesisUtterance(hanzi);
                utter.lang = 'zh-CN';
                utter.rate = 0.85;
                window.speechSynthesis.speak(utter);
            }
        },

        async feed(amount, foodObj = null) {
            if (this.feeding || this.dailyRemaining < amount) return;
            this.feeding = true;
            this.feedMessage = '';
            this.isError = false;

            if (!foodObj && this.foodMenu) {
                foodObj = this.foodMenu.find(f => f.amount === amount);
            }
            this.eatingFood = foodObj;
            this.eatingPhase = 'eating';

            if (window.PetAudioEngine) {
                window.PetAudioEngine.munch();
            }

            const key = 'room_feed_' + Date.now() + '_' + Math.random().toString(36).slice(2, 9);
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
                    this.hunger = data.hunger;
                    this.dailyRemaining = data.daily_remaining;
                    if (data.affinity !== undefined) this.affinity = data.affinity;
                    if (data.tier) this.affinityTier = data.tier;
                    if (data.dialogue) this.currentDialogue = data.dialogue;
                    if (data.dialogues) this.dialogues = data.dialogues;

                    this.eatingPhase = 'satisfied';
                    this.foodReactionText = data.food_reaction || '好吃！(Ngon tuyệt!)';
                    this.foodChineseSay = data.chinese_say || '好吃！';

                    // Pet Voice: Phát âm chuẩn tiếng Trung lời cảm ơn / khen ngon
                    if (window.PetVoiceManager) {
                        setTimeout(() => {
                            window.PetVoiceManager.speak(data.chinese_say || '好吃！');
                        }, 400);
                    }

                    if (data.hunger >= 95 && window.PetAudioEngine) {
                        setTimeout(() => window.PetAudioEngine.purr(), 600);
                    }

                    if (data.stage_up) {
                        if (window.PetAudioEngine) {
                            window.PetAudioEngine.levelUp();
                        }
                        if (window.PetEventBus) {
                            window.PetEventBus.emit('pet:evolved', { new_stage: data.new_stage });
                        }
                        this.celebrationTitle = `Pet đã tiến hóa lên Giai đoạn ${data.new_stage}!`;
                        this.celebrationDesc = `Pet của bạn đã trưởng thành hơn rất nhiều nhờ thành quả học tập chăm chỉ!`;
                        this.celebrationEmoji = data.emoji || '🐉';
                        this.celebrationModalOpen = true;
                        this.eatingFood = null;
                        this.eatingPhase = null;
                    } else {
                        const foodLabel = data.food ? (data.food.emoji + ' ' + data.food.hanzi) : 'Món ăn';
                        this.feedMessage = `Đã cho Pet ăn ${foodLabel}! +${data.xp_fed} EXP`;
                        setTimeout(() => {
                            this.eatingPhase = null;
                            this.eatingFood = null;
                            this.feedMessage = '';
                        }, 5000);
                    }
                } else {
                    this.isError = true;
                    this.eatingPhase = null;
                    this.eatingFood = null;
                    this.feedMessage = data.error || 'Có lỗi xảy ra khi cho ăn';
                }
            } catch (e) {
                this.isError = true;
                this.eatingPhase = null;
                this.eatingFood = null;
                this.feedMessage = 'Lỗi kết nối. Vui lòng thử lại!';
            } finally {
                this.feeding = false;
                setTimeout(() => window.refreshIcons?.(), 50);
            }
        },

        openRenameModal() {
            this.renameModalOpen = true;
        },

        async submitRename() {
            if (this.renaming || !this.petNewName.trim()) return;
            this.renaming = true;
            try {
                const res = await fetch("{{ route('pet.rename') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ name: this.petNewName.trim() })
                });
                const data = await res.json();
                if (data.success) {
                    this.renameModalOpen = false;
                    window.location.reload();
                }
            } catch (e) {
                alert('Không thể đổi tên lúc này');
            } finally {
                this.renaming = false;
            }
        },

        closeCelebration() {
            this.celebrationModalOpen = false;
            window.location.reload();
        },

        getPetBorderClass() {
            if (this.hunger >= 70) return 'border-emerald-300 ring-8 ring-emerald-500/10';
            if (this.hunger >= 40) return 'border-amber-300 ring-8 ring-amber-500/10';
            if (this.hunger >= 20) return 'border-orange-300 ring-8 ring-orange-500/10';
            return 'border-rose-300 ring-8 ring-rose-500/10';
        },

        getMoodBadgeClass() {
            if (this.hunger >= 70) return 'bg-emerald-500 text-white';
            if (this.hunger >= 40) return 'bg-amber-400 text-amber-950';
            if (this.hunger >= 20) return 'bg-orange-500 text-white';
            return 'bg-rose-500 text-white';
        },

        getMoodText() {
            if (this.hunger >= 70) return '😊 Rất vui & No';
            if (this.hunger >= 40) return '🙂 Hơi đói';
            if (this.hunger >= 20) return '😟 Đói nhiều';
            if (this.hunger >= 1) return '😢 Rất yếu';
            return '💤 Ngủ đông';
        },

        getHungerBarClass() {
            if (this.hunger >= 70) return 'bg-emerald-500';
            if (this.hunger >= 40) return 'bg-amber-400';
            if (this.hunger >= 20) return 'bg-orange-500';
            return 'bg-rose-500';
        },

        getHungerNote() {
            if (this.hunger >= 70) return 'No bụng thoải mái';
            if (this.hunger >= 40) return 'Nên cho ăn thêm một chút';
            if (this.hunger >= 20) return 'Cần cho ăn sớm';
            return 'Nguy cơ ngủ đông!';
        }
    };
};

if (typeof Alpine !== 'undefined' && Alpine.data) {
    Alpine.data('petRoom', (config) => window.petRoom(config));
} else {
    document.addEventListener('alpine:init', () => {
        Alpine.data('petRoom', (config) => window.petRoom(config));
    });
}
</script>

<div class="max-w-5xl mx-auto px-4 py-6 sm:py-8 space-y-6"
     x-data="petRoom({
         activeTab: 'overview',
         dailyRemaining: {{ $dailyRemaining }},
         userPet: {{ Js::from($userPet) }},
         progress: {{ Js::from($progress) }},
         dialogues: {{ Js::from($dialogues) }},
         currentDialogue: {{ Js::from($randomDialogue) }},
         masteredWords: {{ Js::from($masteredWords) }},
         affinitySummary: {{ Js::from($affinitySummary) }},
         affinityTier: {{ Js::from($affinityTier) }},
         foodMenu: {{ Js::from($foodMenu) }},
         timelineMemories: {{ Js::from($timelineMemories) }},
         roomStats: {{ Js::from($roomStats) }},
     })"
     x-init="initRoom()">

    {{-- 1. Page Header & Pet Quick Banner --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-200/80 pb-5">
        <div>
            <div class="inline-flex items-center gap-1.5 rounded-full bg-amber-100 border border-amber-200 px-3 py-1 text-xs font-bold uppercase tracking-wider text-amber-900">
                <i data-lucide="sparkles" class="h-3.5 w-3.5 text-amber-600"></i>
                Bạn đồng hành học tiếng Trung
            </div>
            <h1 class="mt-2 text-3xl sm:text-4xl font-black text-slate-900 flex items-center gap-2">
                <span>{{ $userPet->name ?? $userPet->pet->name }}</span>
                <button type="button" @click="openRenameModal()" title="Đổi tên cho Pet"
                        class="grid h-8 w-8 place-items-center rounded-xl bg-slate-100 text-slate-500 hover:bg-amber-100 hover:text-amber-800 transition text-sm">
                    <i data-lucide="pencil" class="h-4 w-4"></i>
                </button>
            </h1>
            <p class="text-sm text-slate-500 mt-0.5">
                Pet lớn lên theo từng từ vựng và bài học bạn hoàn thành. Cùng nhau chinh phục tiếng Trung!
            </p>
        </div>

        {{-- Top Quick Stats Pill --}}
        <div class="flex items-center gap-2 sm:gap-3">
            <div class="rounded-2xl border border-slate-200/90 bg-white px-3.5 py-2 shadow-sm text-center">
                <p class="text-[10px] uppercase font-bold text-slate-400">Giai đoạn</p>
                <p class="text-base font-black text-slate-900">{{ $userPet->stage }}/5</p>
            </div>
            <div class="rounded-2xl border border-slate-200/90 bg-white px-3.5 py-2 shadow-sm text-center">
                <p class="text-[10px] uppercase font-bold text-slate-400">Tổng EXP</p>
                <p class="text-base font-black text-purple-600">{{ $userPet->exp }}</p>
            </div>
            <div class="rounded-2xl border border-slate-200/90 bg-white px-3.5 py-2 shadow-sm text-center">
                <p class="text-[10px] uppercase font-bold text-slate-400">Từ đã dạy</p>
                <p class="text-base font-black text-emerald-600">{{ $masteredCount }}</p>
            </div>
            <div @click="setTab('affinity')" role="button" class="cursor-pointer rounded-2xl border border-rose-200 bg-rose-50/70 hover:bg-rose-100/80 px-3.5 py-2 shadow-sm text-center transition">
                <p class="text-[10px] uppercase font-bold text-rose-500">Thân thiết</p>
                <p class="text-base font-black text-rose-700 flex items-center justify-center gap-1">
                    <span x-text="affinityTier?.emoji || '🌱'"></span>
                    <span x-text="affinity + '/100'"></span>
                </p>
            </div>
        </div>
    </div>

    {{-- 2. Navigation Tabs --}}
    <div class="flex items-center gap-2 border-b border-slate-200 overflow-x-auto pb-px custom-scrollbar">
        <button type="button"
                @click="setTab('overview')"
                :class="activeTab === 'overview' ? 'border-[#991b1b] text-[#991b1b] bg-red-50/50' : 'border-transparent text-slate-600 hover:text-slate-900 hover:border-slate-300'"
                class="flex items-center gap-2 border-b-2 px-4 py-3 text-sm font-bold transition whitespace-nowrap rounded-t-xl">
            <i data-lucide="layout-dashboard" class="h-4 w-4"></i>
            <span>Tổng quan & Cho ăn</span>
        </button>

        <button type="button"
                @click="setTab('growth')"
                :class="activeTab === 'growth' ? 'border-[#991b1b] text-[#991b1b] bg-red-50/50' : 'border-transparent text-slate-600 hover:text-slate-900 hover:border-slate-300'"
                class="flex items-center gap-2 border-b-2 px-4 py-3 text-sm font-bold transition whitespace-nowrap rounded-t-xl">
            <i data-lucide="git-fork" class="h-4 w-4"></i>
            <span>Lộ trình tiến hóa (Giai đoạn)</span>
        </button>

        <button type="button"
                @click="setTab('vocab')"
                :class="activeTab === 'vocab' ? 'border-[#991b1b] text-[#991b1b] bg-red-50/50' : 'border-transparent text-slate-600 hover:text-slate-900 hover:border-slate-300'"
                class="flex items-center gap-2 border-b-2 px-4 py-3 text-sm font-bold transition whitespace-nowrap rounded-t-xl">
            <i data-lucide="book-open" class="h-4 w-4"></i>
            <span>Vốn từ vựng đã học</span>
            <span class="rounded-full bg-slate-200 px-2 py-0.2 text-[10px] font-bold text-slate-700" x-text="masteredWords.length"></span>
        </button>

        <button type="button"
                @click="setTab('affinity')"
                :class="activeTab === 'affinity' ? 'border-[#991b1b] text-[#991b1b] bg-red-50/50' : 'border-transparent text-slate-600 hover:text-slate-900 hover:border-slate-300'"
                class="flex items-center gap-2 border-b-2 px-4 py-3 text-sm font-bold transition whitespace-nowrap rounded-t-xl">
            <i data-lucide="heart-handshake" class="h-4 w-4 text-rose-500"></i>
            <span>Tri kỷ & Tính cách</span>
            <span class="rounded-full bg-rose-100 text-rose-700 px-2 py-0.2 text-[10px] font-bold" x-text="(affinityTier?.name || 'Bỡ ngỡ') + ' • ' + affinity + '/100'"></span>
        </button>

        <button type="button"
                @click="setTab('memories')"
                :class="activeTab === 'memories' ? 'border-[#991b1b] text-[#991b1b] bg-red-50/50' : 'border-transparent text-slate-600 hover:text-slate-900 hover:border-slate-300'"
                class="flex items-center gap-2 border-b-2 px-4 py-3 text-sm font-bold transition whitespace-nowrap rounded-t-xl">
            <i data-lucide="book-heart" class="h-4 w-4 text-amber-600"></i>
            <span>Sổ Kỷ niệm & Cột mốc</span>
            <span class="rounded-full bg-amber-100 text-amber-800 px-2 py-0.2 text-[10px] font-bold" x-text="timelineMemories.length"></span>
        </button>
    </div>

    {{-- TAB 1: TỔNG QUAN & CĂN PHÒNG SỐNG CÙNG (COZY LIVING ROOM & CARE) --}}
    <div x-show="activeTab === 'overview'" class="space-y-6">
        {{-- Hero Living Room Stage --}}
        <div class="relative overflow-hidden rounded-3xl border transition-all duration-700 shadow-2xl p-5 sm:p-8"
             :class="roomScenery === 'night'
                ? 'border-indigo-900/80 bg-gradient-to-b from-slate-950 via-indigo-950 to-slate-900 text-white shadow-indigo-950/40'
                : (roomScenery === 'sunset'
                    ? 'border-orange-300/80 bg-gradient-to-b from-amber-100 via-rose-100/60 to-orange-100/90 text-slate-800 shadow-orange-950/20'
                    : (roomScenery === 'rain'
                        ? 'border-slate-800 bg-gradient-to-b from-slate-900 via-slate-950 to-sky-950 text-slate-100 shadow-slate-950/40'
                        : 'border-amber-200/90 bg-gradient-to-b from-amber-50 via-orange-50/50 to-amber-100/80 text-slate-800 shadow-amber-950/15'))">

            {{-- Ambient light spots --}}
            <div class="pointer-events-none absolute -top-12 -right-12 h-72 w-72 rounded-full blur-3xl transition-all duration-700 opacity-60"
                 :class="roomScenery === 'night' ? 'bg-indigo-500/30' : (roomScenery === 'sunset' ? 'bg-orange-400/40' : (roomScenery === 'rain' ? 'bg-sky-700/20' : 'bg-amber-300/40'))"></div>
            <div class="pointer-events-none absolute -bottom-12 -left-12 h-72 w-72 rounded-full blur-3xl transition-all duration-700 opacity-50"
                 :class="roomScenery === 'night' ? 'bg-purple-900/30' : (roomScenery === 'sunset' ? 'bg-rose-400/30' : (roomScenery === 'rain' ? 'bg-slate-800/40' : 'bg-orange-200/50'))"></div>

            {{-- 1. Room Top Bar: Room Title & Interactive Scenery Switcher Bar --}}
            <div class="relative z-10 flex flex-wrap items-center justify-between gap-3 border-b pb-4 mb-6 transition-colors duration-500"
                 :class="roomTimeMode === 'night' ? 'border-white/10' : 'border-amber-200/80'">
                <div class="flex items-center gap-3">
                    <span class="text-2xl select-none">🏡</span>
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-lg font-black tracking-tight"
                                :class="roomTimeMode === 'night' ? 'text-white' : 'text-slate-900'">
                                Căn phòng nhỏ của {{ $userPet->name ?? $userPet->pet->name }}
                            </h2>
                            <button type="button" @click="openRenameModal()" title="Đổi tên Pet"
                                    class="p-1 rounded-lg text-slate-400 hover:text-amber-500 transition">
                                <i data-lucide="pencil" class="h-3.5 w-3.5"></i>
                            </button>
                            <span class="rounded-full px-2.5 py-0.5 text-[10px] font-bold"
                                  :class="roomTimeMode === 'night' ? 'bg-indigo-900/80 text-indigo-200 border border-indigo-700/60' : 'bg-amber-100 text-amber-900 border border-amber-300'">
                                Cấp {{ $userPet->stage }}: {{ optional($userPet->pet->stages->where('stage', $userPet->stage)->first())->name ?? 'Trứng' }}
                            </span>
                        </div>
                        <p class="text-xs mt-0.5" :class="roomTimeMode === 'night' ? 'text-slate-400' : 'text-slate-500'">
                            Không gian sống ấm cúng cùng người bạn học tiếng Trung
                        </p>
                    </div>
                </div>

                {{-- Interactive Scenery Switcher Pills (Sáng / Chiều / Tối / Mưa) --}}
                <div class="flex items-center gap-1.5 p-1 rounded-2xl border backdrop-blur transition shadow-sm"
                     :class="roomTimeMode === 'night' ? 'bg-slate-900/90 border-white/10' : 'bg-white/90 border-amber-200'">
                    <span class="text-[10px] font-bold uppercase tracking-wider px-2"
                          :class="roomTimeMode === 'night' ? 'text-slate-400' : 'text-slate-500'">Khung cảnh:</span>

                    <button type="button"
                            @click="setScenery('day')"
                            title="Bầu trời ban ngày quang đãng"
                            :class="roomScenery === 'day' ? 'bg-amber-400 text-slate-900 shadow-sm font-black' : 'text-slate-500 hover:text-slate-900'"
                            class="rounded-xl px-2.5 py-1 text-xs font-bold transition flex items-center gap-1">
                        <span>☀️</span>
                        <span class="hidden sm:inline">Sáng</span>
                    </button>

                    <button type="button"
                            @click="setScenery('sunset')"
                            title="Hoàng hôn buông sắc cam ấm áp"
                            :class="roomScenery === 'sunset' ? 'bg-orange-500 text-white shadow-sm font-black' : 'text-slate-500 hover:text-slate-900'"
                            class="rounded-xl px-2.5 py-1 text-xs font-bold transition flex items-center gap-1">
                        <span>🌅</span>
                        <span class="hidden sm:inline">Chiều</span>
                    </button>

                    <button type="button"
                            @click="setScenery('night')"
                            title="Bầu trời đêm trăng sao tĩnh lặng"
                            :class="roomScenery === 'night' ? 'bg-indigo-600 text-white shadow-sm font-black' : 'text-slate-500 hover:text-slate-900'"
                            class="rounded-xl px-2.5 py-1 text-xs font-bold transition flex items-center gap-1">
                        <span>🌙</span>
                        <span class="hidden sm:inline">Đêm</span>
                    </button>

                    <button type="button"
                            @click="setScenery('rain')"
                            title="Mưa rơi rả rích tĩnh lặng"
                            :class="roomScenery === 'rain' ? 'bg-sky-600 text-white shadow-sm font-black' : 'text-slate-500 hover:text-slate-900'"
                            class="rounded-xl px-2.5 py-1 text-xs font-bold transition flex items-center gap-1">
                        <span>🌧️</span>
                        <span class="hidden sm:inline">Mưa</span>
                    </button>
                </div>
            </div>

            {{-- 2. Room Scene: Wall with Window, Photo Frame, and Center Pet Living Area --}}
            <div class="relative z-10 grid grid-cols-1 md:grid-cols-[1fr_auto_1fr] items-center gap-6 my-2">
                {{-- Left: Beautiful Arched Scenic Window (Hotspot) --}}
                <div class="flex flex-col items-center md:items-start space-y-2">
                    <div @click="interactRoomItem('window')"
                         role="button"
                         title="Bấm vào cửa sổ để đổi khung cảnh bên ngoài"
                         class="group relative w-36 h-44 sm:w-44 sm:h-52 rounded-t-full border-4 shadow-xl overflow-hidden cursor-pointer transition-all duration-500 hover:scale-105 active:scale-95"
                         :class="roomScenery === 'night'
                            ? 'border-indigo-800 bg-gradient-to-b from-slate-950 via-indigo-950 to-slate-900 shadow-indigo-900/50'
                            : (roomScenery === 'sunset'
                                ? 'border-amber-700/80 bg-gradient-to-b from-indigo-900 via-rose-500 to-amber-300 shadow-orange-900/30'
                                : (roomScenery === 'rain'
                                    ? 'border-slate-700 bg-gradient-to-b from-slate-800 via-sky-950 to-slate-900 shadow-slate-900/50'
                                    : 'border-amber-300 bg-gradient-to-b from-sky-400 via-sky-200 to-amber-100 shadow-amber-900/20'))">
                        
                        {{-- Window Glass Grid Frame --}}
                        <div class="absolute inset-x-0 top-1/2 h-1 bg-white/30 backdrop-blur-xs z-10"></div>
                        <div class="absolute inset-y-0 left-1/2 w-1 bg-white/30 backdrop-blur-xs z-10"></div>

                        {{-- Dynamic Sky Scene inside Window --}}
                        <div class="absolute inset-0 flex flex-col items-center justify-center p-3 select-none">
                            {{-- Day: Sun & Clouds --}}
                            <template x-if="roomScenery === 'day'">
                                <div class="relative flex flex-col items-center">
                                    <span class="text-4xl cozy-lamp-glow drop-shadow">☀️</span>
                                    <span class="text-2xl cozy-sky-drift mt-1 opacity-90">☁️</span>
                                </div>
                            </template>
                            {{-- Sunset: Setting Sun & Rose sky --}}
                            <template x-if="roomScenery === 'sunset'">
                                <div class="relative flex flex-col items-center">
                                    <span class="text-4xl animate-pulse">🌅</span>
                                    <span class="text-xs font-bold text-amber-200 mt-2 tracking-wider">HOÀNG HÔN</span>
                                </div>
                            </template>
                            {{-- Night: Moon & Stars --}}
                            <template x-if="roomScenery === 'night'">
                                <div class="relative flex flex-col items-center">
                                    <span class="text-4xl cozy-sky-drift drop-shadow">🌙</span>
                                    <div class="flex gap-2 mt-1 text-xs text-amber-300 animate-pulse">
                                        <span>✨</span><span>⭐</span><span>✨</span>
                                    </div>
                                </div>
                            </template>
                            {{-- Rain: Raindrops --}}
                            <template x-if="roomScenery === 'rain'">
                                <div class="relative flex flex-col items-center">
                                    <span class="text-3xl">🌧️</span>
                                    <div class="flex gap-2 mt-2 text-sky-300 text-xs font-mono">
                                        <span class="cozy-rain-drop">💧</span>
                                        <span class="cozy-rain-drop" style="animation-delay: 0.3s">💧</span>
                                        <span class="cozy-rain-drop" style="animation-delay: 0.6s">💧</span>
                                    </div>
                                </div>
                            </template>
                        </div>

                        {{-- Window sill hint --}}
                        <div class="absolute bottom-0 inset-x-0 h-6 bg-amber-900/40 backdrop-blur-xs border-t border-white/20 flex items-center justify-center z-20">
                            <span class="text-[9px] font-bold text-white tracking-wider uppercase opacity-90 group-hover:scale-110 transition">
                                🪟 Cửa sổ phòng
                            </span>
                        </div>
                    </div>
                    <p class="text-[11px] font-medium" :class="roomTimeMode === 'night' ? 'text-slate-400' : 'text-slate-500'">
                        Bấm cửa sổ để đổi khung cảnh
                    </p>
                </div>

                {{-- Center: Living Pet Avatar & Comic Speech Bubble --}}
                <div class="flex flex-col items-center text-center my-2 max-w-sm">
                    {{-- 1. Comic Speech Bubble directly above Pet --}}
                    <div class="relative mb-4 w-full rounded-2xl p-4 shadow-lg border text-sm transition-all duration-300 group"
                         :class="roomTimeMode === 'night' ? 'bg-slate-800/95 border-indigo-700/80 text-white' : 'bg-white/95 border-amber-200 text-slate-800'">
                        <div class="flex items-start justify-between gap-2.5">
                            <div class="flex items-start gap-2 text-left flex-1 min-w-0">
                                <span class="text-lg select-none shrink-0">💬</span>
                                <p class="text-sm font-bold leading-relaxed italic"
                                   :class="roomTimeMode === 'night' ? 'text-slate-100' : 'text-slate-800'"
                                   x-text="currentDialogue">
                                    "{{ $randomDialogue }}"
                                </p>
                            </div>
                            <div class="flex items-center gap-1 shrink-0">
                                <button type="button" @click="talkWithPet()" title="Nghe Pet phát âm câu này"
                                        class="p-1.5 rounded-lg transition active:scale-95"
                                        :class="roomTimeMode === 'night' ? 'text-amber-400 hover:bg-slate-700' : 'text-amber-700 hover:bg-amber-100'">
                                    <i data-lucide="volume-2" class="h-4 w-4"></i>
                                </button>
                                <button type="button" @click="nextDialogue()" title="Đổi câu nói khác"
                                        class="p-1.5 rounded-lg transition active:scale-95"
                                        :class="roomTimeMode === 'night' ? 'text-amber-400 hover:bg-slate-700' : 'text-amber-700 hover:bg-amber-100'">
                                    <i data-lucide="refresh-cw" class="h-4 w-4"></i>
                                </button>
                            </div>
                        </div>

                        {{-- Speech Bubble Tail pointing down to Pet --}}
                        <div class="absolute -bottom-2 left-1/2 -translate-x-1/2 w-4 h-4 rotate-45 border-r border-b"
                             :class="roomTimeMode === 'night' ? 'bg-slate-800 border-indigo-700' : 'bg-white border-amber-200'"></div>
                    </div>

                    {{-- 2. Interactive Pet Avatar Stage --}}
                    <div class="relative flex flex-col items-center justify-center cursor-pointer select-none"
                         @click="pokePet()"
                         title="Chạm vào Pet để tương tác và vuốt ve">
                        
                        {{-- Cozy Oriental Carpet / Rug under Pet --}}
                        <div class="absolute -bottom-4 h-28 sm:h-32 w-64 sm:w-76 rounded-[50%] transition-all duration-500 pointer-events-none"
                             :class="roomTimeMode === 'night'
                                ? 'bg-gradient-to-r from-indigo-900/50 via-purple-900/60 to-indigo-900/50 border border-indigo-700/50 shadow-lg shadow-indigo-950/60'
                                : 'bg-gradient-to-r from-amber-200/80 via-orange-200/90 to-amber-200/80 border-2 border-dashed border-amber-400/70 shadow-inner'">
                        </div>

                        {{-- Floating Heart Particles on Click --}}
                        <div x-show="showHeartBurst" x-cloak class="absolute -top-6 inset-x-0 flex justify-center gap-2 pointer-events-none z-30">
                            <span class="text-2xl pet-heart-particle">💖</span>
                            <span class="text-xl pet-heart-particle" style="animation-delay: 0.15s">❤️</span>
                            <span class="text-2xl pet-heart-particle" style="animation-delay: 0.3s">🥰</span>
                        </div>

                        {{-- Floating Sleep Particles --}}
                        <div x-show="petEmote === 'sleep'" x-cloak class="absolute -top-6 -right-2 flex flex-col pointer-events-none z-30">
                            <span class="text-lg pet-sleep-particle font-bold text-indigo-400">Z</span>
                            <span class="text-sm pet-sleep-particle font-bold text-indigo-400" style="animation-delay: 0.4s">z</span>
                            <span class="text-xs pet-sleep-particle font-bold text-indigo-400" style="animation-delay: 0.8s">z</span>
                        </div>

                        {{-- Pet Avatar Box --}}
                        <div class="relative z-10 pb-2 transition-transform duration-300 hover:scale-105 active:scale-95">
                            <div class="flex h-44 w-44 sm:h-52 sm:w-52 items-center justify-center rounded-[2.5rem] shadow-2xl border-4 p-3"
                                 :class="[getPetBorderClass(), roomTimeMode === 'night' ? 'bg-slate-900/95 shadow-indigo-900/50' : 'bg-white shadow-amber-900/10']">
                                <x-pet-avatar :stage="$userPet->stage" :mood="$userPet->getHungerState()" :personality="$userPet->personality ?? 'playful'" size="xl" :interactive="true" />
                            </div>

                            {{-- Mood Badge --}}
                            <div class="absolute -bottom-1 inset-x-0 flex justify-center z-20">
                                <span class="rounded-full px-3.5 py-1 text-xs font-black uppercase shadow-md border-2 border-white tracking-wide"
                                      :class="getMoodBadgeClass()"
                                      x-text="getMoodText()">
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- 3. Direct Touch Companion Actions Bar --}}
                    <div class="relative z-10 mt-5 flex items-center justify-center gap-1.5 sm:gap-2">
                        <button type="button" @click="petHead()"
                                title="Vuốt ve xoa đầu Pet"
                                class="inline-flex items-center gap-1 rounded-xl px-2.5 py-1.5 text-xs font-bold transition shadow-sm border active:scale-95"
                                :class="roomTimeMode === 'night' ? 'bg-slate-800 text-rose-300 border-rose-800/80 hover:bg-slate-700' : 'bg-white text-rose-700 border-rose-200 hover:bg-rose-50'">
                            <span>🫳</span>
                            <span>Vuốt ve</span>
                        </button>

                        <button type="button" @click="talkWithPet()"
                                title="Nói chuyện cùng Pet"
                                class="inline-flex items-center gap-1 rounded-xl px-2.5 py-1.5 text-xs font-bold transition shadow-sm border active:scale-95"
                                :class="roomTimeMode === 'night' ? 'bg-slate-800 text-amber-300 border-amber-800/80 hover:bg-slate-700' : 'bg-white text-amber-800 border-amber-200 hover:bg-amber-50'">
                            <span>💬</span>
                            <span>Trò chuyện</span>
                        </button>

                        <button type="button" @click="interactRoomItem('teatable')"
                                title="Mời Pet một chén trà Ô Long"
                                class="inline-flex items-center gap-1 rounded-xl px-2.5 py-1.5 text-xs font-bold transition shadow-sm border active:scale-95"
                                :class="roomTimeMode === 'night' ? 'bg-slate-800 text-emerald-300 border-emerald-800/80 hover:bg-slate-700' : 'bg-white text-emerald-800 border-emerald-200 hover:bg-emerald-50'">
                            <span>🍵</span>
                            <span>Mời trà</span>
                        </button>

                        <button type="button" @click="napPet()"
                                title="Ru Pet ngủ hoặc đánh thức"
                                class="inline-flex items-center gap-1 rounded-xl px-2.5 py-1.5 text-xs font-bold transition shadow-sm border active:scale-95"
                                :class="roomTimeMode === 'night' ? 'bg-slate-800 text-indigo-300 border-indigo-800/80 hover:bg-slate-700' : 'bg-white text-indigo-800 border-indigo-200 hover:bg-indigo-50'">
                            <span x-text="petEmote === 'sleep' ? '☀️' : '💤'"></span>
                            <span x-text="petEmote === 'sleep' ? 'Đánh thức' : 'Ru ngủ'"></span>
                        </button>
                    </div>
                </div>

                {{-- Right: Wall Fixtures (Memory Frame & Cozy Vintage Lamp) --}}
                <div class="flex flex-col items-center md:items-end space-y-4">
                    {{-- Photo Frame of Memories (Hotspot) --}}
                    <div @click="interactRoomItem('frame')"
                         role="button"
                         title="Khung ảnh ghi dấu ngày gặp gỡ (bấm để Pet tâm sự)"
                         class="group relative w-36 sm:w-40 p-3 rounded-2xl border transition-all cursor-pointer hover:scale-105 active:scale-95 text-center shadow-md"
                         :class="roomTimeMode === 'night'
                            ? 'bg-slate-800/80 border-indigo-700/70 hover:border-amber-400'
                            : 'bg-white/90 border-amber-200 hover:border-amber-400 hover:bg-white'">
                        <div class="flex items-center justify-center h-16 rounded-xl border-2 relative overflow-hidden mb-2"
                             :class="roomTimeMode === 'night' ? 'bg-indigo-950/80 border-amber-500/50' : 'bg-amber-100/70 border-amber-700/50'">
                            <span class="text-3xl select-none">🖼️</span>
                            <span class="absolute top-1 right-1 rounded-full bg-rose-500 text-white text-[9px] px-1.5 py-0.2 font-black">
                                {{ $roomStats['days_together'] }}d
                            </span>
                        </div>
                        <p class="text-xs font-black group-hover:text-amber-500 transition"
                           :class="roomTimeMode === 'night' ? 'text-slate-200' : 'text-slate-800'">
                            Khung ảnh kỷ niệm
                        </p>
                        <p class="text-[10px]" :class="roomTimeMode === 'night' ? 'text-slate-400' : 'text-slate-500'">
                            {{ $roomStats['days_together'] }} ngày quen nhau
                        </p>
                    </div>

                    {{-- Cozy Vintage Lantern / Lamp (Hotspot) --}}
                    <div @click="cycleScenery()"
                         role="button"
                         title="Đèn phòng ấm áp (bấm để đổi ánh sáng)"
                         class="group relative w-36 sm:w-40 p-3 rounded-2xl border transition-all cursor-pointer hover:scale-105 active:scale-95 text-center shadow-md"
                         :class="roomTimeMode === 'night'
                            ? 'bg-slate-800/80 border-indigo-700/70 hover:border-amber-400'
                            : 'bg-white/90 border-amber-200 hover:border-amber-400 hover:bg-white'">
                        <div class="flex items-center justify-center h-16 rounded-xl border relative mb-2 cozy-lamp-glow"
                             :class="roomTimeMode === 'night' ? 'bg-amber-950/60 border-amber-500/80' : 'bg-amber-100 border-amber-300'">
                            <span class="text-3xl select-none" x-text="roomTimeMode === 'night' ? '🏮' : '💡'"></span>
                        </div>
                        <p class="text-xs font-black group-hover:text-amber-500 transition"
                           :class="roomTimeMode === 'night' ? 'text-slate-200' : 'text-slate-800'">
                            Đèn phòng ấm cúng
                        </p>
                        <p class="text-[10px]" :class="roomTimeMode === 'night' ? 'text-amber-300 font-semibold' : 'text-slate-500'">
                            Bấm để đổi ánh sáng
                        </p>
                    </div>
                </div>
            </div>

            {{-- 3. Floor Furnishings Row (Bookshelf, Teatable, Bonsai, Food Bowl) --}}
            <div class="relative z-10 grid grid-cols-2 sm:grid-cols-4 gap-3 mt-6 pt-5 border-t transition-colors duration-500"
                 :class="roomTimeMode === 'night' ? 'border-white/10' : 'border-amber-200/80'">
                {{-- Bookshelf --}}
                <div @click="interactRoomItem('bookshelf')"
                     role="button"
                     title="Kệ sách từ vựng đã dạy cho Pet (bấm để đố chữ)"
                     class="group flex flex-col items-center justify-center p-3 rounded-2xl border transition-all cursor-pointer hover:scale-105 active:scale-95 text-center"
                     :class="[activeHotspot === 'bookshelf' ? 'ring-2 ring-amber-400 bg-amber-50/20' : '',
                              roomTimeMode === 'night' ? 'bg-slate-800/70 border-indigo-800/60 hover:border-amber-400' : 'bg-white/80 border-amber-200 hover:border-amber-400 hover:bg-white']">
                    <div class="text-2xl select-none mb-1">📚</div>
                    <span class="text-xs font-black group-hover:text-amber-500 transition"
                          :class="roomTimeMode === 'night' ? 'text-slate-200' : 'text-slate-700'">Kệ sách Hán tự</span>
                    <span class="text-[10px] font-bold text-emerald-600">
                        {{ $roomStats['mastered_words'] }} từ đã học
                    </span>
                </div>

                {{-- Tea Table --}}
                <div @click="interactRoomItem('teatable')"
                     role="button"
                     title="Bàn trà Ô Long tỏa khói (bấm để Pet mời trà)"
                     class="group flex flex-col items-center justify-center p-3 rounded-2xl border transition-all cursor-pointer hover:scale-105 active:scale-95 text-center"
                     :class="[activeHotspot === 'teatable' ? 'ring-2 ring-emerald-400 bg-emerald-50/20' : '',
                              roomTimeMode === 'night' ? 'bg-slate-800/70 border-indigo-800/60 hover:border-amber-400' : 'bg-white/80 border-amber-200 hover:border-amber-400 hover:bg-white']">
                    <div class="relative flex items-center justify-center text-2xl select-none mb-1">
                        <span class="cozy-steam absolute -top-3 text-xs">♨️</span>
                        <span>🍵</span>
                    </div>
                    <span class="text-xs font-black group-hover:text-amber-500 transition"
                          :class="roomTimeMode === 'night' ? 'text-slate-200' : 'text-slate-700'">Bàn trà Ô Long</span>
                    <span class="text-[10px]" :class="roomTimeMode === 'night' ? 'text-slate-400' : 'text-slate-500'">Hơi ấm dịu nhẹ</span>
                </div>

                {{-- Bonsai Plant --}}
                <div @click="interactRoomItem('plant')"
                     role="button"
                     title="Chậu cây may mắn theo chuỗi Streak (bấm để xem phản ứng)"
                     class="group flex flex-col items-center justify-center p-3 rounded-2xl border transition-all cursor-pointer hover:scale-105 active:scale-95 text-center"
                     :class="[activeHotspot === 'plant' ? 'ring-2 ring-emerald-400 bg-emerald-50/20' : '',
                              roomTimeMode === 'night' ? 'bg-slate-800/70 border-indigo-800/60 hover:border-amber-400' : 'bg-white/80 border-amber-200 hover:border-amber-400 hover:bg-white']">
                    <div class="text-2xl select-none mb-1">
                        {{ ($roomStats['streak'] ?? 0) >= 7 ? '🌳' : (($roomStats['streak'] ?? 0) >= 3 ? '🌿' : '🌱') }}
                    </div>
                    <span class="text-xs font-black group-hover:text-amber-500 transition"
                          :class="roomTimeMode === 'night' ? 'text-slate-200' : 'text-slate-700'">Cây may mắn</span>
                    <span class="text-[10px] font-bold text-amber-500">
                        Chuỗi {{ $roomStats['streak'] }} ngày 🔥
                    </span>
                </div>

                {{-- Food Bowl --}}
                <div @click="interactRoomItem('foodbowl')"
                     role="button"
                     title="Đĩa thức ăn gần nhất (bấm để Pet nhớ lại bữa ăn)"
                     class="group flex flex-col items-center justify-center p-3 rounded-2xl border transition-all cursor-pointer hover:scale-105 active:scale-95 text-center"
                     :class="[activeHotspot === 'foodbowl' ? 'ring-2 ring-amber-400 bg-amber-50/20' : '',
                              roomTimeMode === 'night' ? 'bg-slate-800/70 border-indigo-800/60 hover:border-amber-400' : 'bg-white/80 border-amber-200 hover:border-amber-400 hover:bg-white']">
                    <div class="text-2xl select-none mb-1">
                        {{ $roomStats['recent_food']['emoji'] ?? '🥣' }}
                    </div>
                    <span class="text-xs font-black group-hover:text-amber-500 transition"
                          :class="roomTimeMode === 'night' ? 'text-slate-200' : 'text-slate-700'">Khay ẩm thực</span>
                    <span class="text-[10px] truncate max-w-[100px]" :class="roomTimeMode === 'night' ? 'text-slate-400' : 'text-slate-500'">
                        {{ $roomStats['recent_food']['hanzi'] ?? 'Chưa ăn gì' }}
                    </span>
                </div>
            </div>

            {{-- 4. Clean Vital Status Bars (Hunger & Evolution EXP) --}}
            <div class="relative z-10 mt-6 pt-5 border-t grid grid-cols-1 md:grid-cols-2 gap-4 transition-colors duration-500"
                 :class="roomTimeMode === 'night' ? 'border-white/10' : 'border-amber-200/80'">
                {{-- Hunger Bar --}}
                <div class="space-y-1.5 text-left">
                    <div class="flex justify-between text-xs font-semibold"
                         :class="roomTimeMode === 'night' ? 'text-slate-300' : 'text-slate-600'">
                        <span>Độ no bụng: <strong :class="roomTimeMode === 'night' ? 'text-white' : 'text-slate-900'" x-text="hunger"></strong>/100</span>
                        <span x-text="getHungerNote()"></span>
                    </div>
                    <div class="h-2.5 w-full overflow-hidden rounded-full"
                         :class="roomTimeMode === 'night' ? 'bg-slate-800' : 'bg-slate-200'">
                        <div class="h-full rounded-full transition-all duration-500"
                             :class="getHungerBarClass()"
                             :style="'width: ' + hunger + '%'"></div>
                    </div>
                </div>

                {{-- EXP Progress Bar --}}
                <div class="space-y-1.5 text-left">
                    <div class="flex justify-between text-xs font-semibold"
                         :class="roomTimeMode === 'night' ? 'text-slate-300' : 'text-slate-600'">
                        <span>Tiến hóa EXP: <strong class="text-purple-400">{{ $progress['current_exp'] }}</strong>/{{ $progress['required_exp'] ?? 'Tối đa' }}</span>
                        @if(!$progress['is_max'])
                        <span>Cần thêm <strong>{{ $progress['exp_needed'] }}</strong> EXP</span>
                        @else
                        <span class="text-emerald-400 font-bold">🐉 Đã đạt cấp tối đa</span>
                        @endif
                    </div>
                    <div class="h-2.5 w-full overflow-hidden rounded-full"
                         :class="roomTimeMode === 'night' ? 'bg-slate-800' : 'bg-slate-200'">
                        <div class="h-full rounded-full bg-gradient-to-r from-purple-500 to-indigo-500 transition-all duration-500"
                             style="width: {{ $progress['percent'] }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Feeding & Dining Tray (Thực đơn dinh dưỡng Trung Hoa) --}}
        <div class="rounded-3xl border border-slate-200/90 bg-white p-6 sm:p-8 shadow-xl shadow-slate-900/5 space-y-5">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-slate-100 pb-4">
                <div>
                    <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
                        <i data-lucide="utensils" class="h-5 w-5 text-amber-600"></i>
                        <span>Bữa ăn dinh dưỡng Trung Hoa</span>
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Dùng điểm XP học tập để mời Pet ăn những món ăn ngon truyền thống Trung Hoa.
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="rounded-full bg-amber-50 border border-amber-200 px-3 py-1 text-xs font-bold text-amber-900">
                        Hạn mức nuôi hôm nay: <strong x-text="dailyRemaining"></strong>/100 XP
                    </span>
                </div>
            </div>

            @if($userPet->isActive())
            {{-- Sensory Eating Ritual Feedback (Shows when Pet is fed) --}}
            <div x-show="eatingFood" x-cloak
                 class="rounded-2xl p-4 transition-all duration-300 border shadow-md space-y-2"
                 :class="eatingPhase === 'satisfied' ? 'bg-gradient-to-r from-amber-50 via-emerald-50 to-amber-50 border-emerald-300 ring-2 ring-emerald-400/20' : 'bg-gradient-to-r from-amber-100/90 to-orange-100/90 border-amber-300 animate-pulse'">
                <div class="flex items-center gap-3">
                    <span class="text-4xl transition-transform"
                          :class="eatingPhase === 'eating' ? 'animate-bounce scale-110' : 'scale-100'"
                          x-text="eatingFood?.emoji || '🍎'"></span>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="text-base font-black text-slate-900" x-text="eatingFood?.hanzi"></span>
                            <span class="text-xs font-bold text-amber-700 font-mono" x-text="eatingFood?.pinyin"></span>
                            <span class="text-xs text-slate-500 font-medium" x-text="'• ' + (eatingFood?.name ?? '')"></span>
                        </div>
                        <p class="text-xs font-bold mt-0.5 leading-snug"
                           :class="eatingPhase === 'satisfied' ? 'text-emerald-800' : 'text-amber-900'"
                           x-text="eatingPhase === 'eating' ? '😋 Pet đang ngửi rồi chóp chép nhai ngon lành...' : foodReactionText">
                        </p>
                    </div>
                    <button type="button"
                            x-show="eatingPhase === 'satisfied'"
                            @click="speakWord(foodChineseSay || '好吃！')"
                            title="Nghe Pet nói tiếng Trung"
                            class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-white shadow-sm border border-amber-200 text-amber-800 hover:bg-amber-50 transition active:scale-95 text-xs">
                        <i data-lucide="volume-2" class="h-4 w-4"></i>
                    </button>
                </div>
            </div>

            {{-- 4 Authentic Chinese Dishes Menu --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
                @foreach($foodMenu as $food)
                <button type="button"
                        @click="feed({{ $food['amount'] }}, {{ Js::from($food) }})"
                        :disabled="feeding || dailyRemaining < {{ $food['amount'] }}"
                        class="group relative flex flex-col justify-between rounded-2xl p-4 border transition-all duration-200 active:scale-[0.98] disabled:opacity-40 disabled:cursor-not-allowed text-left
                               {{ $food['amount'] === 50
                                    ? 'border-orange-300/90 bg-gradient-to-br from-amber-50 via-orange-50 to-rose-50/60 hover:border-orange-400 hover:shadow-md'
                                    : 'border-amber-200/90 bg-white hover:bg-amber-50/50 hover:border-amber-300 hover:shadow-sm' }}">
                    <div>
                        <div class="flex items-start justify-between gap-2 mb-2">
                            <span class="text-4xl select-none transition-transform duration-200 group-hover:scale-110">
                                {{ $food['emoji'] }}
                            </span>
                            <span class="rounded-full px-2 py-0.5 text-[10px] font-black shrink-0 shadow-sm
                                         {{ $food['amount'] === 50 ? 'bg-orange-500 text-white' : 'bg-amber-100 text-amber-900' }}">
                                +{{ $food['amount'] }} XP
                            </span>
                        </div>
                        <div class="flex items-baseline gap-1.5">
                            <span class="text-base font-black text-slate-900">{{ $food['hanzi'] }}</span>
                            <span class="text-xs font-bold text-amber-700 font-mono">{{ $food['pinyin'] }}</span>
                        </div>
                        <p class="text-xs font-bold text-slate-700 mt-0.5">{{ $food['name'] }}</p>
                        <p class="text-[11px] text-slate-500 italic mt-1 line-clamp-2">
                            {{ $food['description'] }}
                        </p>
                    </div>

                    <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-[11px]">
                        <span class="text-slate-400">+{{ $food['amount'] }} độ no</span>
                        <span class="font-bold text-amber-700 group-hover:text-amber-900 transition flex items-center gap-0.5">
                            <span>Mời ăn</span>
                            <i data-lucide="chevron-right" class="h-3 w-3 inline"></i>
                        </span>
                    </div>
                </button>
                @endforeach
            </div>

            <div x-show="feedMessage && !eatingFood" x-cloak
                 class="rounded-xl p-3 text-center text-xs font-bold transition"
                 :class="isError ? 'bg-red-50 text-red-700 border border-red-200' : 'bg-emerald-50 text-emerald-800 border border-emerald-200'"
                 x-text="feedMessage"></div>

            @elseif($userPet->isDormant())
            <div class="rounded-2xl bg-amber-50 border border-amber-200 p-5 text-center space-y-2">
                <span class="text-4xl">💤</span>
                <h4 class="text-sm font-bold text-amber-900">Pet đang ngủ đông vì quá đói!</h4>
                <p class="text-xs text-amber-700">Hãy học thêm bài học để kiếm XP và cho ăn để đánh thức pet dậy nhé.</p>
            </div>
            @elseif($userPet->isEgg())
            <div class="rounded-2xl bg-red-50 border border-red-200 p-5 text-center space-y-2">
                <span class="text-4xl">🥚</span>
                <h4 class="text-sm font-bold text-red-900">Pet đã quay về dạng trứng</h4>
                <p class="text-xs text-red-700">Đã quay về dạng trứng lần thứ {{ $userPet->reset_count }}. Hãy học bài đều đặn mỗi ngày để duy trì sự sống cho pet nhé!</p>
            </div>
            @endif

            {{-- Compact Audio & Preferences Bar --}}
            <div class="pt-4 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3 text-xs text-slate-500">
                <div class="flex items-center gap-4">
                    <label class="flex items-center gap-2 cursor-pointer font-medium select-none">
                        <input type="checkbox" :checked="sfxEnabled" @change="toggleSfx()" class="rounded text-amber-600 focus:ring-amber-500">
                        <span>Âm thanh (SFX)</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer font-medium select-none">
                        <input type="checkbox" :checked="voiceEnabled" @change="toggleVoice()" class="rounded text-indigo-600 focus:ring-indigo-500">
                        <span>Giọng đọc tiếng Trung (TTS)</span>
                    </label>
                </div>
                <div>
                    <span>Đã nuôi hôm nay: <strong class="text-slate-800">{{ $dailyFed }} XP</strong></span>
                </div>
            </div>
        </div>
    </div>

    {{-- TAB 2: LỘ TRÌNH TIẾN HÓA (GROWTH ROADMAP) --}}
    <div x-show="activeTab === 'growth'" class="space-y-6">
        <div class="rounded-3xl border border-slate-200/90 bg-white p-6 sm:p-8 shadow-xl shadow-slate-900/5 space-y-6">
            <div>
                <h3 class="text-xl font-black text-slate-900">Lộ trình tiến hóa sinh vật</h3>
                <p class="text-sm text-slate-500 mt-1">
                    Để pet tiến hóa lên giai đoạn cao hơn, bạn không chỉ cần nuôi EXP mà còn phải thực sự nâng cao năng lực tiếng Trung.
                </p>
            </div>

            <div class="space-y-4">
                @foreach($stages as $stage)
                @php
                    $isUnlocked = $userPet->stage >= $stage->stage;
                    $isCurrent = $userPet->stage === $stage->stage;
                    $isNext = $userPet->stage + 1 === $stage->stage;
                @endphp
                <div class="rounded-2xl border p-4 sm:p-5 transition-all
                            {{ $isCurrent ? 'border-amber-400 bg-amber-50/40 ring-2 ring-amber-400/20 shadow-md' : ($isUnlocked ? 'border-emerald-200 bg-emerald-50/20' : 'border-slate-200/80 bg-slate-50/50 opacity-90') }}">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-center gap-3.5">
                            <div class="grid h-14 w-14 shrink-0 place-items-center rounded-2xl text-3xl shadow-sm border
                                        {{ $isCurrent ? 'bg-amber-100 border-amber-300' : ($isUnlocked ? 'bg-emerald-100 border-emerald-300' : 'bg-slate-100 border-slate-200') }}">
                                {{ $stage->emoji }}
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h4 class="text-base font-black text-slate-900">
                                        Giai đoạn {{ $stage->stage }}: {{ $stage->name }}
                                    </h4>
                                    @if($isCurrent)
                                    <span class="rounded-full bg-amber-500 px-2 py-0.5 text-[10px] font-black uppercase text-white">Đang ở đây</span>
                                    @elseif($isUnlocked)
                                    <span class="rounded-full bg-emerald-500 px-2 py-0.5 text-[10px] font-black uppercase text-white">✓ Đã đạt</span>
                                    @elseif($isNext)
                                    <span class="rounded-full bg-purple-600 px-2 py-0.5 text-[10px] font-black uppercase text-white">Mục tiêu tiếp theo</span>
                                    @endif
                                </div>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    Yêu cầu tối thiểu: <strong>{{ number_format($stage->required_exp) }} EXP</strong>
                                </p>
                            </div>
                        </div>

                        {{-- Requirement Badges --}}
                        <div class="flex flex-wrap gap-2 text-xs">
                            {{-- EXP --}}
                            <span class="rounded-xl px-2.5 py-1 font-semibold flex items-center gap-1 border
                                         {{ $userReqStats['exp'] >= $stage->required_exp ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-slate-100 border-slate-200 text-slate-600' }}">
                                <i data-lucide="{{ $userReqStats['exp'] >= $stage->required_exp ? 'check' : 'circle' }}" class="h-3.5 w-3.5"></i>
                                <span>EXP: {{ $userReqStats['exp'] }}/{{ $stage->required_exp }}</span>
                            </span>

                            {{-- Mastered Vocab --}}
                            @if($stage->required_mastered_vocabulary > 0)
                            <span class="rounded-xl px-2.5 py-1 font-semibold flex items-center gap-1 border
                                         {{ $userReqStats['mastered_vocab'] >= $stage->required_mastered_vocabulary ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-slate-100 border-slate-200 text-slate-600' }}">
                                <i data-lucide="{{ $userReqStats['mastered_vocab'] >= $stage->required_mastered_vocabulary ? 'check' : 'circle' }}" class="h-3.5 w-3.5"></i>
                                <span>Từ thành thạo: {{ $userReqStats['mastered_vocab'] }}/{{ $stage->required_mastered_vocabulary }}</span>
                            </span>
                            @endif

                            {{-- Used Vocab --}}
                            @if($stage->required_used_vocabulary > 0)
                            <span class="rounded-xl px-2.5 py-1 font-semibold flex items-center gap-1 border
                                         {{ $userReqStats['used_vocab'] >= $stage->required_used_vocabulary ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-slate-100 border-slate-200 text-slate-600' }}">
                                <i data-lucide="{{ $userReqStats['used_vocab'] >= $stage->required_used_vocabulary ? 'check' : 'circle' }}" class="h-3.5 w-3.5"></i>
                                <span>Từ tích cực: {{ $userReqStats['used_vocab'] }}/{{ $stage->required_used_vocabulary }}</span>
                            </span>
                            @endif

                            {{-- Reading Activities --}}
                            @if($stage->required_reading_activities > 0)
                            <span class="rounded-xl px-2.5 py-1 font-semibold flex items-center gap-1 border
                                         {{ $userReqStats['reading_activity'] >= $stage->required_reading_activities ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-slate-100 border-slate-200 text-slate-600' }}">
                                <i data-lucide="{{ $userReqStats['reading_activity'] >= $stage->required_reading_activities ? 'check' : 'circle' }}" class="h-3.5 w-3.5"></i>
                                <span>Bài đọc: {{ $userReqStats['reading_activity'] }}/{{ $stage->required_reading_activities }}</span>
                            </span>
                            @endif

                            {{-- Listening Activities --}}
                            @if($stage->required_listening_activities > 0)
                            <span class="rounded-xl px-2.5 py-1 font-semibold flex items-center gap-1 border
                                         {{ $userReqStats['listening_activity'] >= $stage->required_listening_activities ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-slate-100 border-slate-200 text-slate-600' }}">
                                <i data-lucide="{{ $userReqStats['listening_activity'] >= $stage->required_listening_activities ? 'check' : 'circle' }}" class="h-3.5 w-3.5"></i>
                                <span>Bài nghe: {{ $userReqStats['listening_activity'] }}/{{ $stage->required_listening_activities }}</span>
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- TAB 3: VỐN TỪ VỰNG ĐÃ HỌC (PET VOCABULARY) --}}
    <div x-show="activeTab === 'vocab'" class="space-y-6">
        <div class="rounded-3xl border border-slate-200/90 bg-white p-6 sm:p-8 shadow-xl shadow-slate-900/5 space-y-5">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-slate-100 pb-4">
                <div>
                    <h3 class="text-xl font-black text-slate-900 flex items-center gap-2">
                        <span>Vốn từ Pet đã học cùng bạn</span>
                        <span class="rounded-full bg-emerald-100 border border-emerald-200 px-2.5 py-0.5 text-xs font-bold text-emerald-800" x-text="filteredWords().length"></span>
                    </h3>
                    <p class="text-xs text-slate-500 mt-1">
                        Từ vựng đạt độ ghi nhớ vững (đã ôn ít nhất 2 lần đúng) sẽ được truyền dạy lại cho Pet.
                    </p>
                </div>

                {{-- Search filter --}}
                <div class="w-full sm:w-64">
                    <input type="text"
                           x-model="vocabSearch"
                           placeholder="Tìm từ Hán, Pinyin hoặc nghĩa..."
                           class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-xs font-medium placeholder:text-slate-400 focus:border-[#991b1b] focus:outline-none">
                </div>
            </div>

            {{-- Word Grid --}}
            <template x-if="filteredWords().length > 0">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    <template x-for="word in filteredWords()" :key="word.id">
                        <div class="group relative rounded-2xl border border-slate-200/80 bg-slate-50/50 p-4 transition-all hover:bg-white hover:border-amber-300 hover:shadow-md">
                            <div class="flex items-start justify-between">
                                <div>
                                    <p class="text-2xl font-black text-slate-900 group-hover:text-amber-700 transition" x-text="word.hanzi"></p>
                                    <p class="text-xs font-semibold text-amber-600 mt-0.5" x-text="word.pinyin"></p>
                                </div>
                                <button type="button" @click="speakWord(word.hanzi)" title="Nghe phát âm chuẩn"
                                        class="grid h-8 w-8 place-items-center rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-amber-50 hover:text-amber-700 transition">
                                    <i data-lucide="volume-2" class="h-4 w-4"></i>
                                </button>
                            </div>
                            <p class="text-xs text-slate-700 font-medium mt-2 leading-relaxed" x-text="word.meaning"></p>
                            <div class="mt-2.5 flex items-center justify-between text-[10px] text-slate-400 pt-2 border-t border-slate-200/60">
                                <span>Độ nhớ: <strong class="text-emerald-700" x-text="'Cấp ' + (word.repetition || 2)"></strong></span>
                                <span class="text-amber-700 font-semibold">Pet đã nhớ ✓</span>
                            </div>
                        </div>
                    </template>
                </div>
            </template>

            {{-- Empty State --}}
            <template x-if="filteredWords().length === 0">
                <div class="rounded-2xl border border-dashed border-slate-200 p-10 text-center space-y-3">
                    <span class="text-4xl">📚</span>
                    <h4 class="text-base font-bold text-slate-800">Chưa có từ vựng nào đạt độ nhớ</h4>
                    <p class="text-xs text-slate-500 max-w-md mx-auto">
                        Hãy vào Thẻ ghi nhớ (Flashcard) học bài và ôn tập ít nhất 2 lần để giúp Pet nạp thêm nhiều vốn từ nhé!
                    </p>
                    <a href="{{ route('flashcards') }}"
                       class="inline-flex items-center gap-1.5 rounded-xl bg-[#991b1b] px-4 py-2.5 text-xs font-bold text-white shadow-md shadow-red-950/15 hover:bg-red-800 transition">
                        <i data-lucide="layers" class="h-3.5 w-3.5"></i>
                        <span>Học Flashcard ngay</span>
                    </a>
                </div>
            </template>
        </div>
    </div>

    {{-- TAB 4: SỔ KỶ NIỆM SỐNG ĐỘNG & CỘT MỐC TRI KỶ (SCRAPBOOK TIMELINE) --}}
    <div x-show="activeTab === 'memories'" class="space-y-6">
        {{-- Scrapbook Milestone Header Card --}}
        <div class="rounded-3xl border border-amber-200/90 bg-gradient-to-br from-amber-50/80 via-white to-orange-50/50 p-6 sm:p-8 shadow-xl shadow-amber-950/5">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="space-y-2">
                    <div class="inline-flex items-center gap-1.5 rounded-full bg-amber-100 border border-amber-300 px-3 py-0.5 text-xs font-bold text-amber-900">
                        <i data-lucide="book-heart" class="h-3.5 w-3.5 text-amber-700"></i>
                        <span>Cuốn sổ Kỷ niệm của Pet & Bạn</span>
                    </div>
                    <h3 class="text-2xl font-black text-slate-900">
                        Ký ức & Dấu mốc Đồng hành
                    </h3>
                    <p class="text-sm text-slate-600 max-w-2xl leading-relaxed">
                        Mỗi bước tiến bộ học tập, từng bữa ăn ngon và những cột mốc kiên trì của bạn đều được Pet trân trọng khắc ghi thành những trang nhật ký sống động.
                    </p>
                </div>

                {{-- 3 Milestone Summary Badges --}}
                <div class="grid grid-cols-3 gap-2 sm:gap-3 shrink-0">
                    <div class="rounded-2xl border border-amber-200 bg-white/90 p-3 text-center shadow-sm">
                        <span class="text-xl">🌱</span>
                        <p class="text-[10px] font-bold uppercase text-slate-400 mt-1">Gặp nhau</p>
                        <p class="text-sm font-black text-slate-800">
                            {{ ($roomStats['days_together'] ?? 0) > 0 ? $roomStats['days_together'] . ' ngày' : 'Hôm nay' }}
                        </p>
                    </div>

                    <div class="rounded-2xl border border-amber-200 bg-white/90 p-3 text-center shadow-sm">
                        <span class="text-xl">📸</span>
                        <p class="text-[10px] font-bold uppercase text-slate-400 mt-1">Kỷ niệm</p>
                        <p class="text-sm font-black text-amber-700" x-text="timelineMemories.length + ' mốc'"></p>
                    </div>

                    <div class="rounded-2xl border border-rose-200 bg-rose-50/70 p-3 text-center shadow-sm">
                        <span class="text-xl" x-text="affinityTier?.emoji || '❤️'"></span>
                        <p class="text-[10px] font-bold uppercase text-rose-400 mt-1">Gắn kết</p>
                        <p class="text-xs font-black text-rose-700 truncate" x-text="affinityTier?.name || 'Bỡ ngỡ'"></p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main Scrapbook Container --}}
        <div class="rounded-3xl border border-slate-200/90 bg-white p-6 sm:p-8 shadow-xl shadow-slate-900/5 space-y-6">
            {{-- Category Filter Chips --}}
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 pb-4">
                <div class="flex flex-wrap items-center gap-1.5 sm:gap-2">
                    <button type="button"
                            @click="selectedMemoryFilter = 'all'; setTimeout(() => window.refreshIcons?.(), 50)"
                            :class="selectedMemoryFilter === 'all'
                                ? 'bg-[#991b1b] text-white shadow-sm'
                                : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                            class="rounded-xl px-3.5 py-1.5 text-xs font-bold transition">
                        Tất cả (<span x-text="timelineMemories.length"></span>)
                    </button>

                    <button type="button"
                            @click="selectedMemoryFilter = 'growth'; setTimeout(() => window.refreshIcons?.(), 50)"
                            :class="selectedMemoryFilter === 'growth'
                                ? 'bg-emerald-600 text-white shadow-sm'
                                : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                            class="rounded-xl px-3.5 py-1.5 text-xs font-bold transition flex items-center gap-1">
                        <span>🌱</span>
                        <span>Trưởng thành</span>
                    </button>

                    <button type="button"
                            @click="selectedMemoryFilter = 'learning'; setTimeout(() => window.refreshIcons?.(), 50)"
                            :class="selectedMemoryFilter === 'learning'
                                ? 'bg-blue-600 text-white shadow-sm'
                                : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                            class="rounded-xl px-3.5 py-1.5 text-xs font-bold transition flex items-center gap-1">
                        <span>📚</span>
                        <span>Học tập</span>
                    </button>

                    <button type="button"
                            @click="selectedMemoryFilter = 'care'; setTimeout(() => window.refreshIcons?.(), 50)"
                            :class="selectedMemoryFilter === 'care'
                                ? 'bg-rose-600 text-white shadow-sm'
                                : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                            class="rounded-xl px-3.5 py-1.5 text-xs font-bold transition flex items-center gap-1">
                        <span>❤️</span>
                        <span>Chăm sóc</span>
                    </button>

                    <button type="button"
                            @click="selectedMemoryFilter = 'special'; setTimeout(() => window.refreshIcons?.(), 50)"
                            :class="selectedMemoryFilter === 'special'
                                ? 'bg-purple-600 text-white shadow-sm'
                                : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                            class="rounded-xl px-3.5 py-1.5 text-xs font-bold transition flex items-center gap-1">
                        <span>🌟</span>
                        <span>Đặc biệt</span>
                    </button>
                </div>

                <div class="text-xs text-slate-400 font-medium">
                    Đang hiển thị <strong class="text-slate-700" x-text="filteredTimelineMemories().length"></strong> mốc kỷ niệm
                </div>
            </div>

            {{-- Timeline / Scrapbook Grid --}}
            <template x-if="filteredTimelineMemories().length > 0">
                <div class="space-y-4">
                    <template x-for="memory in filteredTimelineMemories()" :key="memory.id">
                        <div class="group relative rounded-2xl border border-slate-200/80 bg-slate-50/60 p-5 transition-all hover:bg-white hover:border-amber-300 hover:shadow-md space-y-3">
                            {{-- Top Header Row: Category Badge, Days Ago & Timestamp --}}
                            <div class="flex items-center justify-between flex-wrap gap-2">
                                <div class="flex items-center gap-2">
                                    <span class="grid h-8 w-8 place-items-center rounded-xl bg-white shadow-xs border text-base select-none"
                                          :class="memory.category === 'growth' ? 'border-emerald-200 bg-emerald-50/50' : (memory.category === 'learning' ? 'border-blue-200 bg-blue-50/50' : (memory.category === 'care' ? 'border-rose-200 bg-rose-50/50' : 'border-amber-200 bg-amber-50/50'))"
                                          x-text="memory.badge_emoji || '🌟'"></span>
                                    <div>
                                        <span class="rounded-full px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider border"
                                              :class="memory.category === 'growth' ? 'bg-emerald-50 text-emerald-800 border-emerald-200' : (memory.category === 'learning' ? 'bg-blue-50 text-blue-800 border-blue-200' : (memory.category === 'care' ? 'bg-rose-50 text-rose-800 border-rose-200' : 'bg-purple-50 text-purple-800 border-purple-200'))"
                                              x-text="memory.category === 'growth' ? 'Tiến hóa' : (memory.category === 'learning' ? 'Học tập' : (memory.category === 'care' ? 'Chăm sóc' : 'Cột mốc'))"></span>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 text-xs">
                                    <span class="rounded-full bg-slate-200/80 text-slate-700 px-2.5 py-0.5 text-[10px] font-bold"
                                          x-text="memory.days_ago_badge || 'Gần đây'"></span>
                                    <span class="text-slate-400 tabular-nums text-[11px]" x-text="memory.formatted_date"></span>
                                </div>
                            </div>

                            {{-- Memory Title & Description --}}
                            <div>
                                <h4 class="text-base font-black text-slate-900 group-hover:text-amber-800 transition" x-text="memory.title"></h4>
                                <p class="text-xs text-slate-600 mt-1 leading-relaxed" x-text="memory.description"></p>
                            </div>

                            {{-- Pet Reflection Speech Bubble (Touching companion dialogue) --}}
                            <template x-if="memory.pet_reflection">
                                <div class="rounded-2xl bg-amber-50/80 border border-amber-200/70 p-3.5 flex items-start gap-3">
                                    <span class="text-xl select-none shrink-0 mt-0.5">🐉</span>
                                    <div class="space-y-0.5">
                                        <p class="text-[10px] font-bold uppercase tracking-wider text-amber-800">
                                            Pet thỏ thẻ nhớ lại:
                                        </p>
                                        <p class="text-xs italic text-amber-950 font-medium leading-relaxed"
                                           x-text="'“' + memory.pet_reflection + '”'"></p>
                                    </div>
                                </div>
                            </template>

                            {{-- Related Word Audio Button (If milestone is connected to Chinese word) --}}
                            <template x-if="memory.metadata && memory.metadata.related_word">
                                <div class="flex items-center justify-between pt-2 border-t border-slate-200/60 text-xs">
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-slate-500 text-[11px]">Chữ Hán ghi dấu:</span>
                                        <span class="font-bold text-slate-900 text-sm" x-text="memory.metadata.related_word"></span>
                                        <template x-if="memory.metadata.pinyin">
                                            <span class="text-amber-600 text-[11px] font-semibold" x-text="'(' + memory.metadata.pinyin + ')'"></span>
                                        </template>
                                    </div>

                                    <button type="button"
                                            @click="speakWord(memory.metadata.related_word)"
                                            title="Nghe Pet phát âm lại chữ này"
                                            class="inline-flex items-center gap-1 rounded-xl bg-white border border-slate-200 px-2.5 py-1 text-[11px] font-bold text-slate-700 hover:bg-amber-50 hover:text-amber-800 hover:border-amber-300 transition active:scale-95 shadow-2xs">
                                        <i data-lucide="volume-2" class="h-3.5 w-3.5 text-amber-600"></i>
                                        <span>Phát âm lại</span>
                                    </button>
                                </div>
                            </template>

                            {{-- Related Food Item (If milestone is first feeding) --}}
                            <template x-if="memory.metadata && memory.metadata.food_item">
                                <div class="flex items-center gap-2 pt-2 border-t border-slate-200/60 text-xs text-slate-600">
                                    <span class="text-slate-500 text-[11px]">Món ăn đầu tiên:</span>
                                    <span class="font-bold text-slate-900" x-text="(memory.metadata.food_emoji || '🍎') + ' ' + memory.metadata.food_item"></span>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
            </template>

            {{-- Empty State --}}
            <template x-if="filteredTimelineMemories().length === 0">
                <div class="rounded-2xl border border-dashed border-slate-200 p-10 text-center space-y-3">
                    <span class="text-4xl">📖</span>
                    <h4 class="text-base font-bold text-slate-800">Chưa có kỷ niệm nào trong mục này</h4>
                    <p class="text-xs text-slate-500 max-w-md mx-auto">
                        Hãy tiếp tục học bài, cho Pet ăn và cùng nhau ôn tập từ vựng để tạo nên thêm thật nhiều cột mốc ý nghĩa nhé!
                    </p>
                </div>
            </template>
        </div>

        {{-- Section 2: Thế giới Tâm thức & Giấc mơ Ngôn ngữ (Pet Memory World 🌏) --}}
        <div class="rounded-3xl border border-indigo-200/90 bg-gradient-to-br from-indigo-950 via-slate-900 to-indigo-900 p-6 sm:p-8 shadow-xl shadow-indigo-950/20 text-white space-y-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-white/10 pb-5">
                <div>
                    <div class="inline-flex items-center gap-1.5 rounded-full bg-indigo-500/20 border border-indigo-400/30 px-3 py-0.5 text-xs font-bold text-indigo-300">
                        <i data-lucide="globe-2" class="h-3.5 w-3.5 text-indigo-300"></i>
                        <span>Thế giới Tâm thức & Ký ức Ngôn ngữ</span>
                    </div>
                    <h3 class="text-2xl font-black text-white mt-1.5 flex items-center gap-2">
                        <span>Pet Memory World</span>
                        <span class="text-xl">🌏</span>
                    </h3>
                    <p class="text-xs text-indigo-200/80 max-w-2xl mt-1 leading-relaxed">
                        Mỗi từ vựng bạn làm chủ (mastered) sẽ hóa thân thành một sinh vật, đồ vật hoặc phong cảnh bay lượn trong thế giới tâm thức của Pet.
                    </p>
                </div>

                <div class="shrink-0 flex items-center gap-2">
                    <span class="rounded-2xl border border-indigo-400/30 bg-white/10 px-4 py-2 text-xs font-bold text-indigo-200 flex items-center gap-2 shadow-inner">
                        <span class="text-base">✨</span>
                        <span><strong>{{ count($worldObjs) }}</strong> thực thể đang hiện hữu</span>
                    </span>
                </div>
            </div>

            {{-- Floating Word Objects Universe Canvas --}}
            <div class="relative overflow-hidden rounded-2xl border border-indigo-500/30 bg-gradient-to-b from-slate-950 via-indigo-950/80 to-slate-950 p-4 shadow-2xl"
                 style="min-height: 280px;">
                {{-- Ambient stars background --}}
                <div class="absolute inset-0 opacity-40 pointer-events-none bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-indigo-400/20 via-transparent to-transparent"></div>
                <div class="absolute top-4 right-6 text-indigo-300/20 text-5xl select-none pointer-events-none">✨</div>
                <div class="absolute bottom-6 left-8 text-indigo-300/10 text-4xl select-none pointer-events-none">🌙</div>

                @if(count($worldObjs) > 0)
                <div class="relative w-full h-[240px]">
                    @foreach($worldObjs as $obj)
                    <div class="absolute group cursor-pointer transition-all duration-300 hover:scale-125 hover:z-30"
                         style="left: {{ $obj['pos_x'] }}%; top: {{ $obj['pos_y'] }}%; transform: translate(-50%, -50%); font-size: {{ 14 + ($obj['size'] ?? 3) * 4 }}px;"
                         @click="speakWord('{{ $obj['hanzi'] }}')">
                        <span class="select-none inline-block transition-transform duration-300 group-hover:animate-bounce drop-shadow-md">
                            {{ $obj['emoji'] }}
                        </span>

                        {{-- Rich Tooltip on Hover --}}
                        <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 hidden group-hover:flex flex-col items-center bg-slate-900/95 backdrop-blur-md text-white border border-indigo-400/40 text-xs px-3 py-2 rounded-xl shadow-2xl whitespace-nowrap z-50 pointer-events-none">
                            <div class="flex items-center gap-1.5">
                                <span class="font-black text-amber-300 text-sm">{{ $obj['hanzi'] }}</span>
                                <span class="font-mono text-indigo-200 text-[11px]">({{ $obj['pinyin'] }})</span>
                            </div>
                            <span class="text-slate-200 text-[11px] font-medium mt-0.5">{{ $obj['meaning'] }}</span>
                            <span class="text-[9px] text-indigo-300/80 mt-1 uppercase tracking-wider">Bấm để nghe phát âm 🔊</span>
                            <div class="w-2 h-2 bg-slate-900 rotate-45 -mb-1 mt-0.5 border-r border-b border-indigo-400/40"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="relative z-10 flex items-center justify-between pt-2 border-t border-white/10 text-[11px] text-indigo-300/70">
                    <span>💡 Rê chuột để xem chữ Hán · Bấm vào biểu tượng để Pet đọc mẫu</span>
                    <span>Tự động cập nhật khi bạn học từ mới</span>
                </div>
                @else
                <div class="flex flex-col items-center justify-center py-12 text-center space-y-3">
                    <span class="text-4xl animate-pulse">🌌</span>
                    <h5 class="text-sm font-bold text-indigo-200">Thế giới tâm thức đang đón chờ những từ đầu tiên</h5>
                    <p class="text-xs text-indigo-300/70 max-w-sm">
                        Khi bạn ôn tập Flashcard đạt từ 2 lần đúng trở lên, các chữ Hán sẽ hóa thành đồ vật và phong cảnh sống động tại đây!
                    </p>
                </div>
                @endif
            </div>

            {{-- Pet Dream Sentence (Giấc mơ dệt bằng từ vựng) --}}
            @if($dreamSent)
            <div class="rounded-2xl border border-indigo-400/30 bg-white/5 backdrop-blur-sm p-4 sm:p-5 space-y-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2 text-xs font-bold text-indigo-300 uppercase tracking-wider">
                        <span class="text-lg">💤</span>
                        <span>Giấc mơ của Pet đêm qua:</span>
                    </div>
                    <button type="button"
                            @click="speakWord('{{ addslashes($dreamSent) }}')"
                            title="Nghe Pet kể lại giấc mơ bằng tiếng Trung"
                            class="inline-flex items-center gap-1.5 rounded-xl bg-indigo-500/30 hover:bg-indigo-500/50 border border-indigo-400/40 px-3 py-1.5 text-xs font-bold text-indigo-100 transition active:scale-95 shadow-sm">
                        <i data-lucide="volume-2" class="h-3.5 w-3.5 text-indigo-200"></i>
                        <span>Nghe kể lại</span>
                    </button>
                </div>

                <div class="rounded-xl bg-slate-950/60 border border-indigo-500/20 p-4">
                    <p class="text-lg sm:text-xl font-bold text-amber-200 tracking-wide font-sans leading-relaxed">
                        “{{ $dreamSent }}”
                    </p>
                </div>
                <p class="text-[11px] text-indigo-300/70 italic">
                    ✨ Pet tự động dệt nên giấc mơ này từ các từ vựng bạn đã cùng học. Mỗi lần mở ra là một giấc mơ mới!
                </p>
            </div>
            @endif
        </div>
    </div>

    {{-- TAB 5: TRI KỶ & TÍNH CÁCH (AFFINITY & PERSONALITY) --}}
    <div x-show="activeTab === 'affinity'" class="space-y-6">
        {{-- Hero Intro Card --}}
        <div class="rounded-3xl border border-rose-200/90 bg-gradient-to-br from-rose-50/80 via-white to-amber-50/40 p-6 sm:p-8 shadow-xl shadow-rose-950/5">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="space-y-2">
                    <div class="inline-flex items-center gap-1.5 rounded-full bg-rose-100 border border-rose-200 px-3 py-0.5 text-xs font-bold text-rose-800">
                        <i data-lucide="heart" class="h-3.5 w-3.5 fill-rose-500 text-rose-500"></i>
                        <span>Tâm hồn & Chiều sâu cảm xúc</span>
                    </div>
                    <h3 class="text-2xl font-black text-slate-900">
                        Tri kỷ đồng hành — Không áp lực, chỉ có thấu hiểu
                    </h3>
                    <p class="text-sm text-slate-600 max-w-2xl leading-relaxed">
                        Pet không phải là một chuỗi thanh chỉ số để bạn phải quản lý áp lực, mà là một sinh linh có tính cách riêng biệt, có ký ức về từng chữ bạn học và ngày càng gắn bó sâu sắc theo thời gian.
                    </p>
                </div>

                {{-- Current Affinity Ring/Pill --}}
                <div class="shrink-0 rounded-2xl border-2 border-rose-200 bg-white p-4 shadow-md text-center min-w-[200px]">
                    <div class="text-3xl mb-1" x-text="affinityTier?.emoji || '🌱'"></div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Cấp độ quan hệ</p>
                    <p class="text-lg font-black text-rose-700" x-text="affinityTier?.title || 'Người lạ mới gặp'"></p>
                    <p class="text-xs font-semibold text-slate-500 mt-0.5" x-text="affinity + ' / 100 điểm Thân thiết'"></p>
                    <div class="mt-2 h-2 w-full overflow-hidden rounded-full bg-slate-100">
                        <div class="h-full rounded-full bg-gradient-to-r from-rose-400 to-pink-500 transition-all duration-500"
                             :style="'width: ' + affinity + '%'"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 1: Personality Archetypes Picker --}}
        <div class="rounded-3xl border border-slate-200/90 bg-white p-6 sm:p-8 shadow-xl shadow-slate-900/5 space-y-5">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-4">
                <div>
                    <h4 class="text-lg font-black text-slate-900 flex items-center gap-2">
                        <i data-lucide="sparkles" class="h-5 w-5 text-amber-500"></i>
                        <span>1. Định hình Tính cách Pet (Personality Archetype)</span>
                    </h4>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Chọn một phong cách tính cách phù hợp với tâm trạng và sở thích của bạn. Mỗi tính cách sẽ thay đổi biểu cảm, âm thanh và lời thoại.
                    </p>
                </div>
                <div x-show="personalityMessage" x-cloak
                     class="rounded-xl px-3 py-1.5 text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200"
                     x-text="personalityMessage"></div>
            </div>

            {{-- 5 Archetype Cards Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                {{-- Archetype 1: Playful --}}
                <div @click="selectPersonality('playful')"
                     role="button"
                     :class="personality === 'playful' ? 'border-amber-500 ring-2 ring-amber-500/30 bg-amber-50/40' : 'border-slate-200 hover:border-amber-300 hover:bg-slate-50/70'"
                     class="relative rounded-2xl border p-5 transition text-left cursor-pointer space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-3xl">✨</span>
                        <span x-show="personality === 'playful'" class="rounded-full bg-amber-500 text-white px-2.5 py-0.5 text-[10px] font-black uppercase">Đang chọn</span>
                    </div>
                    <div>
                        <h5 class="text-base font-black text-slate-900">Tinh nghịch & Vui nhộn</h5>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                            Hiếu động, thích nhún nhảy, luôn bày trò đố vui ôn bài và rủ bạn quẩy tiếp sau mỗi bài học.
                        </p>
                    </div>
                    <div class="rounded-xl bg-white/80 p-2.5 border border-amber-200/60 text-[11px] italic text-amber-900">
                        "Nè nè! Đố bạn nhớ chữ này nghĩa là gì đó, giỏi thì đọc lại xem nào~ ✨"
                    </div>
                </div>

                {{-- Archetype 2: Curious --}}
                <div @click="selectPersonality('curious')"
                     role="button"
                     :class="personality === 'curious' ? 'border-blue-500 ring-2 ring-blue-500/30 bg-blue-50/40' : 'border-slate-200 hover:border-blue-300 hover:bg-slate-50/70'"
                     class="relative rounded-2xl border p-5 transition text-left cursor-pointer space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-3xl">🔍</span>
                        <span x-show="personality === 'curious'" class="rounded-full bg-blue-600 text-white px-2.5 py-0.5 text-[10px] font-black uppercase">Đang chọn</span>
                    </div>
                    <div>
                        <h5 class="text-base font-black text-slate-900">Tò mò & Khám phá</h5>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                            Ham học hỏi, thích giải mã các bộ thủ và đặt câu hỏi gợi mở để kích thích trí nhớ dài hạn của bạn.
                        </p>
                    </div>
                    <div class="rounded-xl bg-white/80 p-2.5 border border-blue-200/60 text-[11px] italic text-blue-900">
                        "Bộ thủ của chữ này thú vị ghê! Chúng mình cùng tìm hiểu xem vì sao nó ghép lại nhé? 🔍"
                    </div>
                </div>

                {{-- Archetype 3: Shy --}}
                <div @click="selectPersonality('shy')"
                     role="button"
                     :class="personality === 'shy' ? 'border-pink-500 ring-2 ring-pink-500/30 bg-pink-50/40' : 'border-slate-200 hover:border-pink-300 hover:bg-slate-50/70'"
                     class="relative rounded-2xl border p-5 transition text-left cursor-pointer space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-3xl">🌸</span>
                        <span x-show="personality === 'shy'" class="rounded-full bg-pink-500 text-white px-2.5 py-0.5 text-[10px] font-black uppercase">Đang chọn</span>
                    </div>
                    <div>
                        <h5 class="text-base font-black text-slate-900">E thẹn & Dịu dàng</h5>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                            Bẽn lẽn, đáng yêu, luôn dùng lời an ủi ngọt ngào và ở cạnh bên bạn mỗi khi gặp từ vựng khó.
                        </p>
                    </div>
                    <div class="rounded-xl bg-white/80 p-2.5 border border-pink-200/60 text-[11px] italic text-pink-900">
                        "Bạn đừng buồn nhé... Chữ này khó thật mà, tớ luôn ở đây cùng bạn nè 🌸"
                    </div>
                </div>

                {{-- Archetype 4: Cheerful --}}
                <div @click="selectPersonality('cheerful')"
                     role="button"
                     :class="personality === 'cheerful' ? 'border-orange-500 ring-2 ring-orange-500/30 bg-orange-50/40' : 'border-slate-200 hover:border-orange-300 hover:bg-slate-50/70'"
                     class="relative rounded-2xl border p-5 transition text-left cursor-pointer space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-3xl">☀️</span>
                        <span x-show="personality === 'cheerful'" class="rounded-full bg-orange-500 text-white px-2.5 py-0.5 text-[10px] font-black uppercase">Đang chọn</span>
                    </div>
                    <div>
                        <h5 class="text-base font-black text-slate-900">Lạc quan & Ánh nắng</h5>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                            Tràn trề năng lượng như hoạt náo viên, luôn nhiệt huyết tiếp thêm sức mạnh cho bạn vươn xa.
                        </p>
                    </div>
                    <div class="rounded-xl bg-white/80 p-2.5 border border-orange-200/60 text-[11px] italic text-orange-900">
                        "Tuyệt đỉnh luôn! Bạn đã cố gắng hết mình rồi, hôm nay hãy cùng tỏa sáng rực rỡ nhé! ☀️"
                    </div>
                </div>

                {{-- Archetype 5: Calm --}}
                <div @click="selectPersonality('calm')"
                     role="button"
                     :class="personality === 'calm' ? 'border-emerald-600 ring-2 ring-emerald-600/30 bg-emerald-50/40' : 'border-slate-200 hover:border-emerald-300 hover:bg-slate-50/70'"
                     class="relative rounded-2xl border p-5 transition text-left cursor-pointer space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-3xl">🍵</span>
                        <span x-show="personality === 'calm'" class="rounded-full bg-emerald-600 text-white px-2.5 py-0.5 text-[10px] font-black uppercase">Đang chọn</span>
                    </div>
                    <div>
                        <h5 class="text-base font-black text-slate-900">Điềm tĩnh & Uyên bác</h5>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                            Trầm tĩnh, sâu sắc, nhắc nhở bạn học hành như thưởng trà, tích tiểu thành đại từng ngày.
                        </p>
                    </div>
                    <div class="rounded-xl bg-white/80 p-2.5 border border-emerald-200/60 text-[11px] italic text-emerald-900">
                        "Học tập như ngâm trà ngon, cần thời gian và kiên nhẫn. Từng bước vững vàng mỗi ngày 🍵"
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 2: Relationship Progression Roadmap --}}
        <div class="rounded-3xl border border-slate-200/90 bg-white p-6 sm:p-8 shadow-xl shadow-slate-900/5 space-y-6">
            <div class="border-b border-slate-100 pb-4">
                <h4 class="text-lg font-black text-slate-900 flex items-center gap-2">
                    <i data-lucide="compass" class="h-5 w-5 text-rose-500"></i>
                    <span>2. Lộ trình Mối quan hệ (Affinity Progression Roadmap)</span>
                </h4>
                <p class="text-xs text-slate-500 mt-0.5">
                    Độ thân thiết (0 - 100) tăng lên tự nhiên khi bạn học tập và chăm sóc Pet đều đặn. Mỗi cấp độ mở ra chiều sâu trò chuyện mới.
                </p>
            </div>

            {{-- 6 Tiers Roadmap Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @php
                    $tiersList = [
                        ['tier' => 'stranger', 'name' => 'Bỡ ngỡ', 'title' => 'Người lạ mới gặp', 'emoji' => '🌱', 'range' => '0 - 19 điểm', 'desc' => 'Pet còn bẽn lẽn, giao tiếp lễ phép lịch sự và bắt đầu làm quen với bạn qua các bài học đầu tiên.'],
                        ['tier' => 'met', 'name' => 'Làm quen', 'title' => 'Bạn mới quen', 'emoji' => '🌿', 'range' => '20 - 39 điểm', 'desc' => 'Pet đã nhớ mặt bạn, bắt đầu gọi tên thân mật và hào hứng khi thấy bạn mở bài học.'],
                        ['tier' => 'familiar', 'name' => 'Thân quen', 'title' => 'Bạn bè quen thuộc', 'emoji' => '🌸', 'range' => '40 - 59 điểm', 'desc' => 'Pet ghi nhớ các từ vựng bạn đã từng học và thường xuyên nhắc lại những kỷ niệm xưa cũ.'],
                        ['tier' => 'companion', 'name' => 'Đồng hành', 'title' => 'Bạn đồng hành', 'emoji' => '⭐', 'range' => '60 - 79 điểm', 'desc' => 'Pet luôn chủ động tiếp sức khi bạn gặp câu khó, không để bạn nản lòng hay cô đơn.'],
                        ['tier' => 'close_friend', 'name' => 'Thân thiết', 'title' => 'Bạn thân thiết', 'emoji' => '💖', 'range' => '80 - 94 điểm', 'desc' => 'Gắn kết bền chặt, Pet chia sẻ nhiều tâm sự riêng và cùng bạn ăn mừng mọi thành tựu.'],
                        ['tier' => 'partner', 'name' => 'Tri kỷ', 'title' => 'Tri kỷ học tập', 'emoji' => '🐉', 'range' => '95 - 100 điểm', 'desc' => 'Đỉnh cao của sự gắn kết. Pet xem bạn là tri kỷ trọn đời trên con đường chinh phục tiếng Trung.'],
                    ];
                @endphp

                @foreach($tiersList as $t)
                <div class="rounded-2xl border p-4.5 space-y-2.5 transition
                            {{ ($userPet->getAffinityTier()['tier'] === $t['tier']) ? 'border-rose-400 bg-rose-50/60 ring-2 ring-rose-400/30 shadow-sm' : 'border-slate-200 bg-slate-50/40 opacity-80' }}">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="text-2xl">{{ $t['emoji'] }}</span>
                            <div>
                                <h6 class="text-sm font-black text-slate-900">{{ $t['name'] }}</h6>
                                <span class="text-[10px] font-bold text-rose-600">{{ $t['range'] }}</span>
                            </div>
                        </div>
                        @if($userPet->getAffinityTier()['tier'] === $t['tier'])
                        <span class="rounded-full bg-rose-600 text-white px-2.5 py-0.5 text-[9px] font-black uppercase">Hiện tại</span>
                        @endif
                    </div>
                    <p class="text-xs text-slate-600 leading-relaxed">{{ $t['desc'] }}</p>
                </div>
                @endforeach
            </div>

            {{-- How to earn affinity naturally --}}
            <div class="rounded-2xl border border-amber-200/80 bg-amber-50/50 p-4.5 space-y-2 text-xs text-slate-700">
                <h6 class="font-bold text-amber-900 flex items-center gap-1.5">
                    <i data-lucide="lightbulb" class="h-4 w-4 text-amber-600"></i>
                    <span>Cách tích lũy Độ thân thiết tự nhiên mỗi ngày (Không cày cuốc áp lực):</span>
                </h6>
                <ul class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2 pt-1 text-slate-600">
                    <li class="flex items-center gap-1.5">
                        <span class="text-emerald-500 font-bold">✓</span>
                        <span>Làm chủ từ vựng Flashcard: <strong>+1 điểm</strong> (tối đa 5/ngày)</span>
                    </li>
                    <li class="flex items-center gap-1.5">
                        <span class="text-emerald-500 font-bold">✓</span>
                        <span>Hoàn thành phiên học: <strong>+2 điểm</strong> (tối đa 4/ngày)</span>
                    </li>
                    <li class="flex items-center gap-1.5">
                        <span class="text-emerald-500 font-bold">✓</span>
                        <span>Đạt Mục tiêu ngày (Daily Goal): <strong>+3 điểm</strong> / ngày</span>
                    </li>
                    <li class="flex items-center gap-1.5">
                        <span class="text-emerald-500 font-bold">✓</span>
                        <span>Đạt mốc chuỗi học (Streak): <strong>+2 điểm</strong> / mốc</span>
                    </li>
                    <li class="flex items-center gap-1.5">
                        <span class="text-emerald-500 font-bold">✓</span>
                        <span>Chăm sóc cho ăn mỗi ngày: <strong>+1 điểm</strong> (tối đa 2/ngày)</span>
                    </li>
                    <li class="flex items-center gap-1.5">
                        <span class="text-emerald-500 font-bold">✓</span>
                        <span>Pet tiến hóa cấp mới: <strong>+10 điểm</strong> thưởng</span>
                    </li>
                </ul>
            </div>
        </div>

        {{-- Section 3: Learning DNA 🧬 Engine (DNA Học tập & Định hình Tính cách Tự nhiên) --}}
        <div class="rounded-3xl border border-slate-200/90 bg-white p-6 sm:p-8 shadow-xl shadow-slate-900/5 space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-4">
                <div>
                    <h4 class="text-lg font-black text-slate-900 flex items-center gap-2">
                        <i data-lucide="dna" class="h-5 w-5 text-indigo-600"></i>
                        <span>3. Learning DNA 🧬 — Năng lực & Tính cách tự nhiên của Pet</span>
                    </h4>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Tính cách Pet được nuôi dưỡng tự động từ phong cách học tập thực tế của bạn (Từ vựng, Flashcard, Quiz, Luyện nghe, Kiên trì & Vượt khó).
                    </p>
                </div>

                {{-- Dominant Trait Badge --}}
                @php
                    $dominantTrait = $userPet->learning_dna['dominant_trait'] ?? null;
                    $traitLabels = [
                        'scholar'    => ['name' => 'Uyên bác (Scholar)', 'emoji' => '📚', 'desc' => 'Tập trung học sâu từ vựng', 'color' => 'bg-indigo-50 text-indigo-800 border-indigo-200'],
                        'listener'   => ['name' => 'Thính giác nhạy bén (Listener)', 'emoji' => '🎧', 'desc' => 'Chăm chỉ luyện nghe ngữ âm', 'color' => 'bg-emerald-50 text-emerald-800 border-emerald-200'],
                        'explorer'   => ['name' => 'Nhà thám hiểm (Explorer)', 'emoji' => '⚡', 'desc' => 'Hăng say thử thách Quiz', 'color' => 'bg-amber-50 text-amber-800 border-amber-200'],
                        'resilient'  => ['name' => 'Kiên cường (Resilient)', 'emoji' => '🛡️', 'desc' => 'Bền bỉ vượt qua lỗi sai', 'color' => 'bg-rose-50 text-rose-800 border-rose-200'],
                        'consistent' => ['name' => 'Bền bỉ (Consistent)', 'emoji' => '⭐', 'desc' => 'Duy trì chuỗi học mỗi ngày', 'color' => 'bg-blue-50 text-blue-800 border-blue-200'],
                    ];
                    $currentTraitInfo = $traitLabels[$dominantTrait] ?? ['name' => 'Đang định hình', 'emoji' => '🌱', 'desc' => 'Tiếp tục học để Pet rõ nét phong cách', 'color' => 'bg-slate-50 text-slate-700 border-slate-200'];
                @endphp
                <div class="shrink-0 flex items-center gap-2">
                    <span class="rounded-2xl border px-3.5 py-1.5 text-xs font-bold flex items-center gap-1.5 {{ $currentTraitInfo['color'] }}">
                        <span class="text-base">{{ $currentTraitInfo['emoji'] }}</span>
                        <span>Thiên hướng: <strong>{{ $currentTraitInfo['name'] }}</strong></span>
                    </span>
                </div>
            </div>

            {{-- 6 Trait Progress Bars Grid --}}
            @if(count($dna) > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @php
                    $traitDescriptions = [
                        'vocabulary'  => 'Mật độ từ vựng thành thạo (đã ôn trên 2 lần)',
                        'flashcard'   => 'Tỷ lệ tích cực ôn tập bộ thẻ Flashcard',
                        'quiz'        => 'Tần suất tham gia và cọ xát bài kiểm tra Quiz',
                        'listening'   => 'Số bài luyện nghe phát âm và câu hỏi âm thanh',
                        'consistency' => 'Chỉ số duy trì chuỗi Streak và tần suất vào học',
                        'resilience'  => 'Chỉ số kiên trì sửa sai khi gặp từ vựng khó',
                    ];
                @endphp
                @foreach($dna as $trait)
                <div class="rounded-2xl border border-slate-100 bg-slate-50/70 p-4 space-y-2 hover:bg-slate-50 transition">
                    <div class="flex items-center justify-between text-xs font-semibold">
                        <div class="flex items-center gap-2">
                            <span class="h-2.5 w-2.5 rounded-full" style="background-color: {{ $trait['color'] }}"></span>
                            <span class="font-bold text-slate-800 text-sm">{{ $trait['label'] }}</span>
                        </div>
                        <span class="font-black text-slate-900 font-mono text-sm">{{ $trait['score'] }}/100</span>
                    </div>

                    {{-- Progress Bar --}}
                    <div class="h-2.5 w-full overflow-hidden rounded-full bg-slate-200">
                        <div class="h-full rounded-full transition-all duration-700"
                             style="width: {{ $trait['score'] }}%; background-color: {{ $trait['color'] }}"></div>
                    </div>

                    <p class="text-[11px] text-slate-500 leading-tight">
                        {{ $traitDescriptions[$trait['key']] ?? 'Chỉ số học tập của người dùng' }}
                    </p>
                </div>
                @endforeach
            </div>
            @else
            <div class="rounded-2xl border border-dashed border-slate-200 p-8 text-center space-y-2">
                <span class="text-3xl">🧬</span>
                <p class="text-xs font-semibold text-slate-600">DNA của Pet đang được thu thập từ các bài học đầu tiên.</p>
                <p class="text-[11px] text-slate-400">Hãy bắt đầu ôn từ vựng và làm bài tập để kích hoạt biểu đồ nhé!</p>
            </div>
            @endif

            {{-- DNA Evolution Note --}}
            <div class="rounded-2xl border border-indigo-100 bg-indigo-50/50 p-4 flex items-start gap-3 text-xs text-indigo-950">
                <i data-lucide="info" class="h-4 w-4 text-indigo-600 shrink-0 mt-0.5"></i>
                <div class="space-y-0.5 leading-relaxed">
                    <p class="font-bold text-indigo-900">Cách DNA tương tác với đời sống của Pet:</p>
                    <p class="text-indigo-800/90 text-[11px]">
                        Khi một chỉ số vượt trội hơn các chỉ số khác từ 15 điểm trở lên, Pet sẽ tự động chuyển hướng tính cách tương ứng. Ví dụ: Người học chăm nghe bài sẽ giúp Pet trở nên tinh tế tò mò (Curious), người học kiên trì vượt khó giúp Pet trở nên lạc quan tươi vui (Cheerful).
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL 1: Đổi tên Pet --}}
    <div x-show="renameModalOpen" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        <div class="w-full max-w-sm rounded-3xl bg-white p-6 shadow-2xl space-y-4"
             @click.away="renameModalOpen = false">
            <h3 class="text-lg font-black text-slate-900">Đặt tên cho Pet</h3>
            <p class="text-xs text-slate-500">
                Hãy chọn một cái tên thật ý nghĩa để gắn bó cùng Pet nhé!
            </p>
            <input type="text"
                   x-model="petNewName"
                   maxlength="30"
                   placeholder="Nhập tên mới (VD: Tiểu Long, Bảo Bảo)..."
                   class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm font-medium focus:border-amber-500 focus:outline-none">
            
            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" @click="renameModalOpen = false"
                        class="rounded-xl px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 transition">
                    Hủy
                </button>
                <button type="button" @click="submitRename()" :disabled="renaming || !petNewName.trim()"
                        class="rounded-xl bg-[#991b1b] px-4 py-2 text-xs font-bold text-white transition hover:bg-red-800 disabled:opacity-50">
                    Lưu tên mới
                </button>
            </div>
        </div>
    </div>

    {{-- MODAL 2: Chúc mừng tiến hóa (Stage Up Celebration) --}}
    <div x-show="celebrationModalOpen" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-md"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-90"
         x-transition:enter-end="opacity-100 scale-100">
        <div class="w-full max-w-md rounded-3xl bg-gradient-to-b from-amber-50 via-white to-orange-50 p-8 shadow-2xl text-center border-2 border-amber-300 space-y-4">
            <div class="text-7xl animate-bounce">
                <span x-text="celebrationEmoji">🐉</span>
            </div>
            <span class="inline-block rounded-full bg-amber-100 border border-amber-300 px-3 py-1 text-xs font-black uppercase text-amber-900">
                🎉 TIẾN HÓA THÀNH CÔNG! 🎉
            </span>
            <h3 class="text-2xl font-black text-slate-900" x-text="celebrationTitle"></h3>
            <p class="text-sm text-slate-600 leading-relaxed" x-text="celebrationDesc"></p>

            <button type="button" @click="closeCelebration()"
                    class="w-full rounded-2xl bg-gradient-to-r from-amber-500 to-orange-500 py-3 text-sm font-black text-white shadow-lg shadow-orange-500/30 hover:from-amber-600 hover:to-orange-600 transition active:scale-95">
                Tiếp tục học cùng Pet! 🚀
            </button>
        </div>
    </div>
</div>
@endsection
