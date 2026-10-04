@props([
    'stage' => 0,
    'mood' => 'happy',
    'personality' => 'playful',
    'size' => 'md', // 'xs', 'sm', 'md', 'lg', 'xl', '2xl'
    'interactive' => false,
])

@php
    $stage = (int) $stage;
    $sizeClasses = match($size) {
        'xs' => 'w-8 h-8',
        'sm' => 'w-12 h-12',
        'md' => 'w-20 h-20 sm:w-24 sm:h-24',
        'lg' => 'w-32 h-32 sm:w-36 sm:h-36',
        'xl' => 'w-40 h-40 sm:w-48 sm:h-48',
        '2xl' => 'w-48 h-48 sm:w-56 sm:h-56',
        default => 'w-20 h-20',
    };

    $isDormant = ($mood === 'dormant');
    $isHungry = in_array($mood, ['hungry', 'very_hungry', 'weak']);
    $animClass = $isDormant ? 'animate-pulse opacity-75' : ($stage === 0 ? 'animate-[eggWobble_4s_ease-in-out_infinite]' : 'pet-anim-idle-' . ($personality ?? 'playful'));
@endphp

<div {{ $attributes->merge(['class' => "relative inline-flex items-center justify-center select-none {$sizeClasses}"]) }}
     data-pet-avatar="true"
     x-data="{
         clicking: false,
         getParentData() {
             if (!this.$el) return null;
             const parentEl = this.$el.parentElement ? this.$el.parentElement.closest('[x-data]') : null;
             return parentEl && window.Alpine ? window.Alpine.$data(parentEl) : null;
         },
         resolveExpr() {
             const parent = this.getParentData();
             if (parent) {
                 if (typeof parent.getPetExpression === 'function') {
                     return parent.getPetExpression();
                 }
                 if (parent.currentAction === 'dizzy') return 'dizzy';
                 if (parent.currentAction === 'surprised') return 'surprised';
                 if (parent.currentAction === 'tea') return 'tea';
                 if (parent.currentAction === 'petting') return 'petting';
                 if (parent.currentAction === 'talking') return 'talking';
                 if (parent.currentAction === 'sleep') return 'sleeping';
                 if (parent.currentAction === 'waking') return 'waking';
                 if (parent.eatingPhase === 'eating') return 'eating';
                 if (parent.eatingPhase === 'satisfied') return 'satisfied';
                 if (parent.petEmote === 'dizzy') return 'dizzy';
                 if (parent.petEmote === 'love') return 'cuddle';
                 if (parent.petEmote === 'surprised') return 'surprised';
                 if (parent.petEmote === 'happy' || parent.petEmote === 'blush') return 'happy';
                 if (parent.state) {
                     if (parent.state === 'dizzy') return 'dizzy';
                     if (parent.state === 'cuddle') return 'cuddle';
                     if (parent.state === 'petting') return 'petting';
                     if (parent.state === 'happy' || parent.state === 'excited') return 'happy';
                     if (parent.state === 'poked' || parent.state === 'surprised') return 'surprised';
                     if (parent.state === 'speaking') return 'talking';
                     if (parent.state === 'sleeping') return 'sleeping';
                     if (parent.state === 'dozing') return 'dozing';
                     if (parent.state === 'waking_up') return 'waking';
                     if (parent.state === 'eating') return 'eating';
                     if (parent.state === 'tea') return 'tea';
                     if (parent.state === 'hungry') return 'hungry';
                 }
                 if (parent.hunger !== undefined && parent.hunger <= 20) return 'hungry';
                 if (parent.hungerState === 'dormant') return 'sleeping';
             }
             if (this.clicking) return 'surprised';
             const propMood = '{{ $mood }}';
             if (propMood === 'dormant') return 'sleeping';
             if (propMood === 'hungry' || propMood === 'very_hungry' || propMood === 'weak') return 'hungry';
             return 'idle';
         },
         getDynamicAnimClass() {
             const expr = this.resolveExpr();
             if (expr === 'dizzy') return 'pet-anim-dizzy';
             if (expr === 'cuddle') return 'pet-anim-cuddle';
             if (expr === 'petting') return 'pet-anim-petting';
             if (expr === 'happy' || expr === 'satisfied') return 'pet-anim-happy';
             if (expr === 'surprised') return 'pet-anim-poked';
             if (expr === 'talking') return 'pet-anim-talking';
             if (expr === 'tea') return 'pet-anim-tea';
             if (expr === 'eating') return 'pet-anim-chew';
             if (expr === 'sleeping') return 'pet-anim-sleeping';
             if (expr === 'dozing') return 'pet-anim-dozing';
             if (expr === 'waking') return 'pet-anim-waking';
             return '{{ $animClass }}';
         }
     }"
     @if($interactive)
     @click="clicking = true; setTimeout(() => clicking = false, 700)"
     :class="clicking ? 'scale-105 -translate-y-1' : ''"
     style="cursor: pointer; transition: transform 0.25s cubic-bezier(0.175, 0.885, 0.32, 1.275);"
     @endif>

    {{-- Personality Characteristic Particle Aura --}}
    @if(!$isDormant)
    <div x-cloak class="absolute inset-0 pointer-events-none z-20 overflow-visible" x-show="resolveExpr() !== 'sleeping'">
        {{-- Playful: Sao lấp lánh --}}
        <div x-show="(getParentData()?.personality || '{{ $personality }}') === 'playful'" class="w-full h-full relative">
            <span class="aura-playful-particle absolute -top-1.5 -left-1 text-[13px] select-none">⭐</span>
            <span class="aura-playful-particle absolute top-3 -right-2 text-[11px] select-none" style="animation-delay: 0.8s;">✨</span>
        </div>
        {{-- Curious: Kính lúp & Bóng đèn --}}
        <div x-show="(getParentData()?.personality || '{{ $personality }}') === 'curious'" class="w-full h-full relative">
            <span class="aura-curious-particle absolute -top-2 right-0 text-[13px] select-none">💡</span>
            <span class="aura-curious-particle absolute bottom-2 -left-2 text-[11px] select-none" style="animation-delay: 1.1s;">🔍</span>
        </div>
        {{-- Shy: Hoa anh đào & Trái tim --}}
        <div x-show="(getParentData()?.personality || '{{ $personality }}') === 'shy'" class="w-full h-full relative">
            <span class="aura-shy-particle absolute -top-1.5 -left-1 text-[13px] select-none">🌸</span>
            <span class="aura-shy-particle absolute top-4 -right-2 text-[11px] select-none" style="animation-delay: 1.2s;">💕</span>
        </div>
        {{-- Cheerful: Mặt trời & Đốm sáng --}}
        <div x-show="(getParentData()?.personality || '{{ $personality }}') === 'cheerful'" class="w-full h-full relative">
            <span class="aura-cheerful-particle absolute -top-2 left-1/2 -translate-x-1/2 text-[14px] select-none">☀️</span>
            <span class="aura-playful-particle absolute top-5 -right-2 text-[11px] select-none" style="animation-delay: 0.5s;">🌟</span>
        </div>
        {{-- Calm: Lá trà & Sương thanh tịnh --}}
        <div x-show="(getParentData()?.personality || '{{ $personality }}') === 'calm'" class="w-full h-full relative">
            <span class="aura-calm-particle absolute -top-1 -right-1 text-[13px] select-none">🍃</span>
            <span class="aura-calm-particle absolute bottom-1 -left-2 text-[11px] select-none" style="animation-delay: 1.6s;">🍵</span>
        </div>
    </div>
    @endif

    {{-- GIAI ĐOẠN 0: QUẢ TRỨNG RỒNG MA THUẬT --}}
    @if($stage === 0)
    <svg class="w-full h-full drop-shadow-md"
         :class="resolveExpr() === 'dizzy' || resolveExpr() === 'surprised' ? 'animate-[eggWobbleFast_0.6s_ease-in-out_infinite]' : getDynamicAnimClass()"
         viewBox="0 0 160 200">
        <defs>
            <linearGradient id="eggGrad_{{ $stage }}" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" stop-color="#fef3c7" />
                <stop offset="50%" stop-color="#f59e0b" />
                <stop offset="100%" stop-color="#b45309" />
            </linearGradient>
        </defs>
        <path d="M 80,10 C 130,10 150,90 150,140 C 150,180 120,195 80,195 C 40,195 10,180 10,140 C 10,90 30,10 80,10 Z"
              fill="url(#eggGrad_{{ $stage }})" stroke="#78350f" stroke-width="4" />
        <path d="M 60,60 Q 80,80 100,60" fill="none" stroke="#fff" stroke-width="3.5" opacity="0.6" stroke-linecap="round"/>
        <path d="M 45,95 Q 65,115 85,95" fill="none" stroke="#fff" stroke-width="3.5" opacity="0.6" stroke-linecap="round"/>
        <path d="M 75,100 Q 95,120 115,100" fill="none" stroke="#fff" stroke-width="3.5" opacity="0.6" stroke-linecap="round"/>
        <path d="M 60,135 Q 80,155 100,135" fill="none" stroke="#fff" stroke-width="3.5" opacity="0.6" stroke-linecap="round"/>
        <path d="M 80,75 L 88,90 L 78,105 L 92,125" fill="none" stroke="#fef08a" stroke-width="3" stroke-linecap="round"/>
        <ellipse cx="50" cy="50" rx="14" ry="24" fill="#ffffff" opacity="0.45" transform="rotate(-25 50 50)"/>
        
        {{-- Nứt sáng khi tương tác mạnh --}}
        <g x-show="resolveExpr() === 'dizzy' || resolveExpr() === 'surprised'" x-cloak>
            <path d="M 70,70 L 82,92 L 68,110 L 85,130" fill="none" stroke="#ffffff" stroke-width="3" stroke-linecap="round" class="animate-ping"/>
        </g>
    </svg>

    {{-- GIAI ĐOẠN 1: RỒNG SƠ SINH TRONG VỎ TRỨNG --}}
    @elseif($stage === 1)
    <svg class="w-full h-full drop-shadow-md" :class="getDynamicAnimClass()" viewBox="0 0 180 200">
        <!-- Baby Tail (Joint-anchored) -->
        <path class="anim-tail-joint" d="M 130,150 Q 165,160 160,140" fill="none" stroke="#f59e0b" stroke-width="10" stroke-linecap="round"/>
        <ellipse cx="90" cy="115" rx="50" ry="45" fill="#f59e0b" stroke="#b45309" stroke-width="3.5"/>
        <ellipse cx="90" cy="125" rx="32" ry="28" fill="#fef3c7"/>
        <path d="M 62,55 Q 55,35 68,38 Z" fill="#ef4444" stroke="#b91c1c" stroke-width="2"/>
        <path d="M 118,55 Q 125,35 112,38 Z" fill="#ef4444" stroke="#b91c1c" stroke-width="2"/>
        <circle cx="90" cy="85" r="48" fill="#f59e0b" stroke="#b45309" stroke-width="3.5"/>

        {{-- Đôi má hồng động (Blush morphing) --}}
        <circle cx="62" cy="98"
                :r="['petting', 'cuddle', 'happy', 'satisfied'].includes(resolveExpr()) ? 11 : 8"
                :fill="['petting', 'cuddle', 'happy', 'satisfied'].includes(resolveExpr()) ? '#fb7185' : '#f87171'"
                :opacity="['petting', 'cuddle', 'happy', 'satisfied'].includes(resolveExpr()) ? 0.95 : 0.6"/>
        <circle cx="118" cy="98"
                :r="['petting', 'cuddle', 'happy', 'satisfied'].includes(resolveExpr()) ? 11 : 8"
                :fill="['petting', 'cuddle', 'happy', 'satisfied'].includes(resolveExpr()) ? '#fb7185' : '#f87171'"
                :opacity="['petting', 'cuddle', 'happy', 'satisfied'].includes(resolveExpr()) ? 0.95 : 0.6"/>

        {{-- Má xoáy khi chóng mặt --}}
        <g x-show="resolveExpr() === 'dizzy'" x-cloak>
            <circle cx="62" cy="98" r="9" fill="none" stroke="#fb7185" stroke-width="2" stroke-dasharray="3 3"/>
            <circle cx="118" cy="98" r="9" fill="none" stroke="#fb7185" stroke-width="2" stroke-dasharray="3 3"/>
        </g>

        {{-- 1. MẮT HÌNH THÁI LINH ĐỘNG (EYES MORPHING) --}}
        {{-- Happy / Petting / Cuddle / Satisfied: Mắt cười tít trăng khuyết --}}
        <g x-show="['happy', 'petting', 'cuddle', 'satisfied'].includes(resolveExpr())">
            <path d="M 64,80 Q 72,71 80,80" fill="none" stroke="#78350f" stroke-width="3.8" stroke-linecap="round"/>
            <path d="M 100,80 Q 108,71 116,80" fill="none" stroke="#78350f" stroke-width="3.8" stroke-linecap="round"/>
        </g>

        {{-- Tea: Mắt nhắm thư thái nhâm nhi trà --}}
        <g x-show="resolveExpr() === 'tea'" x-cloak>
            <path d="M 65,79 Q 72,84 79,79" fill="none" stroke="#78350f" stroke-width="3.2" stroke-linecap="round"/>
            <path d="M 101,79 Q 108,84 115,79" fill="none" stroke="#78350f" stroke-width="3.2" stroke-linecap="round"/>
        </g>

        {{-- Dizzy: Mắt xoắn ốc xoay tròn @ @ --}}
        <g x-show="resolveExpr() === 'dizzy'" x-cloak>
            <g style="transform-origin: 72px 80px; animation: dizzySpiral 1.2s linear infinite;">
                <path d="M 72,80 m -7,0 a 7,7 0 1,0 14,0 a 7,7 0 1,0 -14,0 m 3,0 a 4,4 0 1,1 8,0 a 4,4 0 1,1 -8,0" fill="none" stroke="#78350f" stroke-width="2.5" stroke-linecap="round"/>
            </g>
            <g style="transform-origin: 108px 80px; animation: dizzySpiral 1.2s linear infinite;">
                <path d="M 108,80 m -7,0 a 7,7 0 1,0 14,0 a 7,7 0 1,0 -14,0 m 3,0 a 4,4 0 1,1 8,0 a 4,4 0 1,1 -8,0" fill="none" stroke="#78350f" stroke-width="2.5" stroke-linecap="round"/>
            </g>
        </g>

        {{-- Surprised: Mắt mở to tròn O O --}}
        <g x-show="resolveExpr() === 'surprised'" x-cloak>
            <circle cx="72" cy="80" r="11" fill="#ffffff" stroke="#78350f" stroke-width="2.5"/>
            <circle cx="108" cy="80" r="11" fill="#ffffff" stroke="#78350f" stroke-width="2.5"/>
            <circle cx="72" cy="80" r="4.5" fill="#262626"/>
            <circle cx="108" cy="80" r="4.5" fill="#262626"/>
            <circle cx="70.5" cy="78.5" r="1.5" fill="#ffffff"/>
            <circle cx="106.5" cy="78.5" r="1.5" fill="#ffffff"/>
        </g>

        {{-- Eating: Mắt híp nhai thức ăn > < --}}
        <g x-show="resolveExpr() === 'eating'" x-cloak>
            <path d="M 65,76 L 73,80 L 65,84" fill="none" stroke="#78350f" stroke-width="3.2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M 115,76 L 107,80 L 115,84" fill="none" stroke="#78350f" stroke-width="3.2" stroke-linecap="round" stroke-linejoin="round"/>
        </g>

        {{-- Sleeping: Mắt nhắm thẳng --}}
        <g x-show="resolveExpr() === 'sleeping'" x-cloak>
            <path d="M 64,82 L 80,82" stroke="#78350f" stroke-width="3.5" stroke-linecap="round"/>
            <path d="M 100,82 L 116,82" stroke="#78350f" stroke-width="3.5" stroke-linecap="round"/>
        </g>

        {{-- Dozing: Mắt sụp mí lim dim --}}
        <g x-show="resolveExpr() === 'dozing'" x-cloak>
            <path d="M 64,81 Q 72,85 80,81" fill="none" stroke="#78350f" stroke-width="3.2" stroke-linecap="round"/>
            <path d="M 100,81 Q 108,85 116,81" fill="none" stroke="#78350f" stroke-width="3.2" stroke-linecap="round"/>
        </g>

        {{-- Hungry: Mắt long lanh ngấn lệ 🥺 --}}
        <g x-show="resolveExpr() === 'hungry'" x-cloak>
            <circle cx="72" cy="80" r="9.5" fill="#262626"/>
            <circle cx="108" cy="80" r="9.5" fill="#262626"/>
            <circle cx="69" cy="77" r="4.2" fill="#ffffff"/>
            <circle cx="105" cy="77" r="4.2" fill="#ffffff"/>
            <circle cx="75" cy="84" r="2.5" fill="#60a5fa" opacity="0.8"/>
            <circle cx="111" cy="84" r="2.5" fill="#60a5fa" opacity="0.8"/>
        </g>

        {{-- Idle / Talking / Default: Mắt to tròn đen láy liếc nhìn theo chuột --}}
        <g x-show="!['happy', 'petting', 'cuddle', 'satisfied', 'tea', 'dizzy', 'surprised', 'eating', 'sleeping', 'dozing', 'hungry'].includes(resolveExpr())">
            <circle cx="72" cy="80" r="8.5" fill="#262626"/>
            <circle cx="108" cy="80" r="8.5" fill="#262626"/>
            <g class="pet-pupil-tracker">
                <circle cx="70" cy="78" r="3" fill="#ffffff"/>
                <circle cx="106" cy="78" r="3" fill="#ffffff"/>
            </g>
        </g>

        {{-- 2. MŨI & MIỆNG HÌNH THÁI LINH ĐỘNG (MOUTH & SNOUT) --}}
        <ellipse cx="90" cy="96" rx="14" ry="9" fill="#fef3c7" stroke="#d97706" stroke-width="1.5"/>

        {{-- Talking: Miệng mở nhấp nháy phát âm --}}
        <g x-show="resolveExpr() === 'talking'" x-cloak>
            <ellipse cx="90" cy="98" rx="4.5" ry="3.8" fill="#dc2626" style="animation: mouthTalking 0.35s ease-in-out infinite alternate;"/>
            <ellipse cx="90" cy="99" rx="2.8" ry="1.8" fill="#fca5a5"/>
        </g>

        {{-- Eating: Miệng nhai phồng má --}}
        <g x-show="resolveExpr() === 'eating'" x-cloak>
            <ellipse cx="90" cy="98" rx="6" ry="4.5" fill="#dc2626" style="animation: chewNom 0.5s ease-in-out infinite;"/>
            <circle cx="90" cy="97" r="2.5" fill="#fbbf24"/>
        </g>

        {{-- Happy / Petting / Cuddle / Satisfied: Miệng cười tươi hé lưỡi hồng ‿ --}}
        <g x-show="['happy', 'petting', 'cuddle', 'satisfied'].includes(resolveExpr())" x-cloak>
            <path d="M 85,96 Q 90,103 95,96" fill="#ef4444" stroke="#78350f" stroke-width="2" stroke-linecap="round"/>
            <circle cx="90" cy="98.5" r="2" fill="#fca5a5"/>
        </g>

        {{-- Dizzy: Miệng lượn sóng ~ --}}
        <path x-show="resolveExpr() === 'dizzy'" x-cloak d="M 85,97 Q 87.5,94 90,97 Q 92.5,100 95,97" fill="none" stroke="#78350f" stroke-width="2.2" stroke-linecap="round"/>

        {{-- Surprised: Miệng tròn O --}}
        <g x-show="resolveExpr() === 'surprised'" x-cloak>
            <ellipse cx="90" cy="98" rx="3.5" ry="4" fill="#78350f"/>
            <ellipse cx="90" cy="98" rx="2" ry="2.5" fill="#fef3c7"/>
        </g>

        {{-- Hungry: Miệng mếu ︵ --}}
        <path x-show="resolveExpr() === 'hungry'" x-cloak d="M 86,99 Q 90,95 94,99" fill="none" stroke="#78350f" stroke-width="2.2" stroke-linecap="round"/>

        {{-- Tea / Default / Idle: Miệng cong nhẹ xinh xắn --}}
        <path x-show="!['talking', 'eating', 'happy', 'petting', 'cuddle', 'satisfied', 'dizzy', 'surprised', 'hungry'].includes(resolveExpr())"
              d="M 87,96 Q 90,99 93,96" fill="none" stroke="#78350f" stroke-width="2" stroke-linecap="round"/>

        {{-- Vỏ trứng vỡ bao chân --}}
        <path d="M 38,135 Q 35,185 90,185 Q 145,185 142,135 L 125,145 L 110,132 L 90,148 L 70,132 L 55,145 Z"
              fill="#fef3c7" stroke="#78350f" stroke-width="3.5"/>
    </svg>

    {{-- GIAI ĐOẠN 2: RỒNG BÉ CÓ CÁNH --}}
    @elseif($stage === 2)
    <svg class="w-full h-full drop-shadow-md" :class="getDynamicAnimClass()" viewBox="0 0 200 200">
        <!-- Tail with flame tip (Joint-anchored) -->
        <g class="anim-tail-joint">
            <path d="M 125,145 Q 170,155 165,125" fill="none" stroke="#f59e0b" stroke-width="12" stroke-linecap="round"/>
            <circle cx="168" cy="120" r="7" fill="#ef4444"/>
        </g>
        <!-- Wings (Left & Right - Joint Anchored) -->
        <g class="anim-wing-left">
            <path d="M 55,115 Q 15,85 30,125 Q 45,130 60,122 Z" fill="#ef4444" stroke="#991b1b" stroke-width="2.5"/>
        </g>
        <g class="anim-wing-right">
            <path d="M 145,115 Q 185,85 170,125 Q 155,130 140,122 Z" fill="#ef4444" stroke="#991b1b" stroke-width="2.5"/>
        </g>
        <ellipse cx="100" cy="130" rx="46" ry="42" fill="#f59e0b" stroke="#b45309" stroke-width="3.5"/>
        <ellipse cx="100" cy="135" rx="30" ry="28" fill="#fef3c7"/>
        <ellipse cx="78" cy="168" rx="14" ry="10" fill="#f59e0b" stroke="#b45309" stroke-width="2.5"/>
        <ellipse cx="122" cy="168" rx="14" ry="10" fill="#f59e0b" stroke="#b45309" stroke-width="2.5"/>
        <path d="M 72,60 Q 58,30 76,40 Z" fill="#dc2626" stroke="#991b1b" stroke-width="2.5"/>
        <path d="M 128,60 Q 142,30 124,40 Z" fill="#dc2626" stroke="#991b1b" stroke-width="2.5"/>
        <circle cx="100" cy="85" r="44" fill="#f59e0b" stroke="#b45309" stroke-width="3.5"/>

        {{-- Đôi má hồng linh động --}}
        <circle cx="72" cy="98"
                :r="['petting', 'cuddle', 'happy', 'satisfied'].includes(resolveExpr()) ? 11 : 8"
                :fill="['petting', 'cuddle', 'happy', 'satisfied'].includes(resolveExpr()) ? '#fb7185' : '#f87171'"
                :opacity="['petting', 'cuddle', 'happy', 'satisfied'].includes(resolveExpr()) ? 0.95 : 0.6"/>
        <circle cx="128" cy="98"
                :r="['petting', 'cuddle', 'happy', 'satisfied'].includes(resolveExpr()) ? 11 : 8"
                :fill="['petting', 'cuddle', 'happy', 'satisfied'].includes(resolveExpr()) ? '#fb7185' : '#f87171'"
                :opacity="['petting', 'cuddle', 'happy', 'satisfied'].includes(resolveExpr()) ? 0.95 : 0.6"/>

        {{-- 1. MẮT HÌNH THÁI STAGE 2 --}}
        {{-- Happy / Petting / Cuddle / Satisfied --}}
        <g x-show="['happy', 'petting', 'cuddle', 'satisfied'].includes(resolveExpr())">
            <path d="M 74,80 Q 82,71 90,80" fill="none" stroke="#78350f" stroke-width="3.8" stroke-linecap="round"/>
            <path d="M 110,80 Q 118,71 126,80" fill="none" stroke="#78350f" stroke-width="3.8" stroke-linecap="round"/>
        </g>

        {{-- Tea: Mắt nhắm dịu dàng --}}
        <g x-show="resolveExpr() === 'tea'" x-cloak>
            <path d="M 75,79 Q 82,84 89,79" fill="none" stroke="#78350f" stroke-width="3.2" stroke-linecap="round"/>
            <path d="M 111,79 Q 118,84 125,79" fill="none" stroke="#78350f" stroke-width="3.2" stroke-linecap="round"/>
        </g>

        {{-- Dizzy: Mắt xoắn ốc @ @ --}}
        <g x-show="resolveExpr() === 'dizzy'" x-cloak>
            <g style="transform-origin: 82px 82px; animation: dizzySpiral 1.2s linear infinite;">
                <path d="M 82,82 m -7,0 a 7,7 0 1,0 14,0 a 7,7 0 1,0 -14,0 m 3,0 a 4,4 0 1,1 8,0 a 4,4 0 1,1 -8,0" fill="none" stroke="#78350f" stroke-width="2.5" stroke-linecap="round"/>
            </g>
            <g style="transform-origin: 118px 82px; animation: dizzySpiral 1.2s linear infinite;">
                <path d="M 118,82 m -7,0 a 7,7 0 1,0 14,0 a 7,7 0 1,0 -14,0 m 3,0 a 4,4 0 1,1 8,0 a 4,4 0 1,1 -8,0" fill="none" stroke="#78350f" stroke-width="2.5" stroke-linecap="round"/>
            </g>
        </g>

        {{-- Surprised: Mắt mở to O O --}}
        <g x-show="resolveExpr() === 'surprised'" x-cloak>
            <circle cx="82" cy="82" r="11" fill="#ffffff" stroke="#78350f" stroke-width="2.5"/>
            <circle cx="118" cy="82" r="11" fill="#ffffff" stroke="#78350f" stroke-width="2.5"/>
            <circle cx="82" cy="82" r="4.5" fill="#262626"/>
            <circle cx="118" cy="82" r="4.5" fill="#262626"/>
            <circle cx="80" cy="80.5" r="1.5" fill="#ffffff"/>
            <circle cx="116" cy="80.5" r="1.5" fill="#ffffff"/>
        </g>

        {{-- Eating: Mắt híp nhai thức ăn > < --}}
        <g x-show="resolveExpr() === 'eating'" x-cloak>
            <path d="M 75,78 L 83,82 L 75,86" fill="none" stroke="#78350f" stroke-width="3.2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M 125,78 L 117,82 L 125,86" fill="none" stroke="#78350f" stroke-width="3.2" stroke-linecap="round" stroke-linejoin="round"/>
        </g>

        {{-- Sleeping --}}
        <g x-show="resolveExpr() === 'sleeping'" x-cloak>
            <path d="M 74,82 L 90,82" stroke="#78350f" stroke-width="3.5" stroke-linecap="round"/>
            <path d="M 110,82 L 126,82" stroke="#78350f" stroke-width="3.5" stroke-linecap="round"/>
        </g>

        {{-- Dozing --}}
        <g x-show="resolveExpr() === 'dozing'" x-cloak>
            <path d="M 74,81 Q 82,85 90,81" fill="none" stroke="#78350f" stroke-width="3.2" stroke-linecap="round"/>
            <path d="M 110,81 Q 118,85 126,81" fill="none" stroke="#78350f" stroke-width="3.2" stroke-linecap="round"/>
        </g>

        {{-- Hungry --}}
        <g x-show="resolveExpr() === 'hungry'" x-cloak>
            <circle cx="82" cy="82" r="9.5" fill="#262626"/>
            <circle cx="118" cy="82" r="9.5" fill="#262626"/>
            <circle cx="79.5" cy="79.5" r="4.2" fill="#ffffff"/>
            <circle cx="115.5" cy="79.5" r="4.2" fill="#ffffff"/>
            <circle cx="85" cy="86" r="2.5" fill="#60a5fa" opacity="0.8"/>
            <circle cx="121" cy="86" r="2.5" fill="#60a5fa" opacity="0.8"/>
        </g>

        {{-- Idle / Default --}}
        <g x-show="!['happy', 'petting', 'cuddle', 'satisfied', 'tea', 'dizzy', 'surprised', 'eating', 'sleeping', 'dozing', 'hungry'].includes(resolveExpr())">
            <circle cx="82" cy="82" r="8.5" fill="#262626"/>
            <circle cx="118" cy="82" r="8.5" fill="#262626"/>
            <g class="pet-pupil-tracker">
                <circle cx="79.5" cy="79.5" r="3.2" fill="#ffffff"/>
                <circle cx="115.5" cy="79.5" r="3.2" fill="#ffffff"/>
            </g>
        </g>

        {{-- 2. MŨI & MIỆNG STAGE 2 --}}
        <ellipse cx="100" cy="98" rx="16" ry="10" fill="#fef3c7" stroke="#d97706" stroke-width="1.5"/>

        {{-- Talking --}}
        <g x-show="resolveExpr() === 'talking'" x-cloak>
            <ellipse cx="100" cy="100" rx="5" ry="4" fill="#dc2626" style="animation: mouthTalking 0.35s ease-in-out infinite alternate;"/>
            <ellipse cx="100" cy="101" rx="3" ry="2" fill="#fca5a5"/>
        </g>

        {{-- Eating --}}
        <g x-show="resolveExpr() === 'eating'" x-cloak>
            <ellipse cx="100" cy="100" rx="6.5" ry="5" fill="#dc2626" style="animation: chewNom 0.5s ease-in-out infinite;"/>
            <circle cx="100" cy="99" r="2.8" fill="#fbbf24"/>
        </g>

        {{-- Happy / Petting / Cuddle / Satisfied --}}
        <g x-show="['happy', 'petting', 'cuddle', 'satisfied'].includes(resolveExpr())" x-cloak>
            <path d="M 94,98 Q 100,105 106,98" fill="#ef4444" stroke="#78350f" stroke-width="2" stroke-linecap="round"/>
            <circle cx="100" cy="100.5" r="2" fill="#fca5a5"/>
        </g>

        {{-- Dizzy --}}
        <path x-show="resolveExpr() === 'dizzy'" x-cloak d="M 94,99 Q 97,96 100,99 Q 103,102 106,99" fill="none" stroke="#78350f" stroke-width="2.2" stroke-linecap="round"/>

        {{-- Surprised --}}
        <g x-show="resolveExpr() === 'surprised'" x-cloak>
            <ellipse cx="100" cy="100" rx="4" ry="4.5" fill="#78350f"/>
            <ellipse cx="100" cy="100" rx="2.5" ry="3" fill="#fef3c7"/>
        </g>

        {{-- Hungry --}}
        <path x-show="resolveExpr() === 'hungry'" x-cloak d="M 95,101 Q 100,97 105,101" fill="none" stroke="#78350f" stroke-width="2.2" stroke-linecap="round"/>

        {{-- Tea / Default --}}
        <path x-show="!['talking', 'eating', 'happy', 'petting', 'cuddle', 'satisfied', 'dizzy', 'surprised', 'hungry'].includes(resolveExpr())"
              d="M 94,98 Q 100,103 106,98" fill="none" stroke="#78350f" stroke-width="2" stroke-linecap="round"/>
    </svg>

    {{-- GIAI ĐOẠN 3: RỒNG THIẾU NIÊN --}}
    @elseif($stage === 3)
    <svg class="w-full h-full drop-shadow-md" :class="getDynamicAnimClass()" viewBox="0 0 220 220">
        <!-- Tail with flaming plume (Joint-anchored) -->
        <g class="anim-tail-joint">
            <path d="M 130,160 Q 195,175 190,120" fill="none" stroke="#ea580c" stroke-width="14" stroke-linecap="round"/>
            <path d="M 185,120 Q 210,100 195,85 Q 180,105 175,115 Z" fill="#ef4444"/>
            <path d="M 188,115 Q 200,100 192,92 Z" fill="#fef08a"/>
        </g>
        <!-- Large Dragon Wings (Joint-anchored) -->
        <g class="anim-wing-left">
            <path d="M 60,115 Q 10,65 25,125 Q 45,145 65,128 Z" fill="#dc2626" stroke="#991b1b" stroke-width="3"/>
        </g>
        <g class="anim-wing-right">
            <path d="M 160,115 Q 210,65 195,125 Q 175,145 155,128 Z" fill="#dc2626" stroke="#991b1b" stroke-width="3"/>
        </g>
        <ellipse cx="110" cy="140" rx="46" ry="46" fill="#ea580c" stroke="#9a3412" stroke-width="3.5"/>
        <path d="M 90,115 Q 110,135 110,175 Q 85,160 85,125 Z" fill="#fef3c7" opacity="0.9"/>
        <path d="M 80,60 Q 55,20 85,38 Z" fill="#991b1b" stroke="#7f1d1d" stroke-width="2.5"/>
        <path d="M 140,60 Q 165,20 135,38 Z" fill="#991b1b" stroke="#7f1d1d" stroke-width="2.5"/>
        <circle cx="110" cy="88" r="42" fill="#ea580c" stroke="#9a3412" stroke-width="3.5"/>

        {{-- Má hồng Stage 3 --}}
        <circle cx="80" cy="102"
                :r="['petting', 'cuddle', 'happy', 'satisfied'].includes(resolveExpr()) ? 10 : 7"
                :fill="['petting', 'cuddle', 'happy', 'satisfied'].includes(resolveExpr()) ? '#fb7185' : '#f87171'"
                :opacity="['petting', 'cuddle', 'happy', 'satisfied'].includes(resolveExpr()) ? 0.95 : 0.55"/>
        <circle cx="140" cy="102"
                :r="['petting', 'cuddle', 'happy', 'satisfied'].includes(resolveExpr()) ? 10 : 7"
                :fill="['petting', 'cuddle', 'happy', 'satisfied'].includes(resolveExpr()) ? '#fb7185' : '#f87171'"
                :opacity="['petting', 'cuddle', 'happy', 'satisfied'].includes(resolveExpr()) ? 0.95 : 0.55"/>

        {{-- 1. MẮT HÌNH THÁI STAGE 3 --}}
        {{-- Happy / Petting / Cuddle / Satisfied --}}
        <g x-show="['happy', 'petting', 'cuddle', 'satisfied'].includes(resolveExpr())">
            <path d="M 84,84 Q 92,75 100,84" fill="none" stroke="#78350f" stroke-width="3.8" stroke-linecap="round"/>
            <path d="M 120,84 Q 128,75 136,84" fill="none" stroke="#78350f" stroke-width="3.8" stroke-linecap="round"/>
        </g>

        {{-- Tea --}}
        <g x-show="resolveExpr() === 'tea'" x-cloak>
            <path d="M 85,83 Q 92,88 99,83" fill="none" stroke="#78350f" stroke-width="3.2" stroke-linecap="round"/>
            <path d="M 121,83 Q 128,88 135,83" fill="none" stroke="#78350f" stroke-width="3.2" stroke-linecap="round"/>
        </g>

        {{-- Dizzy @ @ --}}
        <g x-show="resolveExpr() === 'dizzy'" x-cloak>
            <g style="transform-origin: 92px 86px; animation: dizzySpiral 1.2s linear infinite;">
                <path d="M 92,86 m -7,0 a 7,7 0 1,0 14,0 a 7,7 0 1,0 -14,0 m 3,0 a 4,4 0 1,1 8,0 a 4,4 0 1,1 -8,0" fill="none" stroke="#78350f" stroke-width="2.5" stroke-linecap="round"/>
            </g>
            <g style="transform-origin: 128px 86px; animation: dizzySpiral 1.2s linear infinite;">
                <path d="M 128,86 m -7,0 a 7,7 0 1,0 14,0 a 7,7 0 1,0 -14,0 m 3,0 a 4,4 0 1,1 8,0 a 4,4 0 1,1 -8,0" fill="none" stroke="#78350f" stroke-width="2.5" stroke-linecap="round"/>
            </g>
        </g>

        {{-- Surprised O O --}}
        <g x-show="resolveExpr() === 'surprised'" x-cloak>
            <circle cx="92" cy="86" r="11" fill="#ffffff" stroke="#78350f" stroke-width="2.5"/>
            <circle cx="128" cy="86" r="11" fill="#ffffff" stroke="#78350f" stroke-width="2.5"/>
            <circle cx="92" cy="86" r="4.5" fill="#262626"/>
            <circle cx="128" cy="86" r="4.5" fill="#262626"/>
        </g>

        {{-- Eating > < --}}
        <g x-show="resolveExpr() === 'eating'" x-cloak>
            <path d="M 85,82 L 93,86 L 85,90" fill="none" stroke="#78350f" stroke-width="3.2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M 135,82 L 127,86 L 135,90" fill="none" stroke="#78350f" stroke-width="3.2" stroke-linecap="round" stroke-linejoin="round"/>
        </g>

        {{-- Sleeping --}}
        <g x-show="resolveExpr() === 'sleeping'" x-cloak>
            <path d="M 84,86 L 100,86" stroke="#78350f" stroke-width="3.5" stroke-linecap="round"/>
            <path d="M 120,86 L 136,86" stroke="#78350f" stroke-width="3.5" stroke-linecap="round"/>
        </g>

        {{-- Dozing --}}
        <g x-show="resolveExpr() === 'dozing'" x-cloak>
            <path d="M 84,85 Q 92,89 100,85" fill="none" stroke="#78350f" stroke-width="3.2" stroke-linecap="round"/>
            <path d="M 120,85 Q 128,89 136,85" fill="none" stroke="#78350f" stroke-width="3.2" stroke-linecap="round"/>
        </g>

        {{-- Hungry --}}
        <g x-show="resolveExpr() === 'hungry'" x-cloak>
            <circle cx="92" cy="86" r="9.5" fill="#262626"/>
            <circle cx="128" cy="86" r="9.5" fill="#262626"/>
            <circle cx="89.5" cy="83.5" r="4.2" fill="#ffffff"/>
            <circle cx="125.5" cy="83.5" r="4.2" fill="#ffffff"/>
            <circle cx="95" cy="90" r="2.5" fill="#60a5fa" opacity="0.8"/>
            <circle cx="131" cy="90" r="2.5" fill="#60a5fa" opacity="0.8"/>
        </g>

        {{-- Idle / Default --}}
        <g x-show="!['happy', 'petting', 'cuddle', 'satisfied', 'tea', 'dizzy', 'surprised', 'eating', 'sleeping', 'dozing', 'hungry'].includes(resolveExpr())">
            <circle cx="92" cy="86" r="8" fill="#262626"/>
            <circle cx="128" cy="86" r="8" fill="#262626"/>
            <g class="pet-pupil-tracker">
                <circle cx="90" cy="84" r="3" fill="#ffffff"/>
                <circle cx="126" cy="84" r="3" fill="#ffffff"/>
            </g>
        </g>

        {{-- 2. MŨI & MIỆNG STAGE 3 --}}
        <ellipse cx="110" cy="100" rx="17" ry="11" fill="#fef3c7" stroke="#c2410c" stroke-width="1.5"/>

        <g x-show="resolveExpr() === 'talking'" x-cloak>
            <ellipse cx="110" cy="102" rx="5" ry="4" fill="#dc2626" style="animation: mouthTalking 0.35s ease-in-out infinite alternate;"/>
        </g>
        <g x-show="resolveExpr() === 'eating'" x-cloak>
            <ellipse cx="110" cy="102" rx="6.5" ry="5" fill="#dc2626" style="animation: chewNom 0.5s ease-in-out infinite;"/>
        </g>
        <g x-show="['happy', 'petting', 'cuddle', 'satisfied'].includes(resolveExpr())" x-cloak>
            <path d="M 104,100 Q 110,107 116,100" fill="#ef4444" stroke="#78350f" stroke-width="2" stroke-linecap="round"/>
        </g>
        <path x-show="resolveExpr() === 'dizzy'" x-cloak d="M 104,101 Q 107,98 110,101 Q 113,104 116,101" fill="none" stroke="#78350f" stroke-width="2.2" stroke-linecap="round"/>
        <path x-show="resolveExpr() === 'hungry'" x-cloak d="M 105,103 Q 110,99 115,103" fill="none" stroke="#78350f" stroke-width="2.2" stroke-linecap="round"/>
        <path x-show="!['talking', 'eating', 'happy', 'petting', 'cuddle', 'satisfied', 'dizzy', 'hungry'].includes(resolveExpr())"
              d="M 105,100 Q 110,104 115,100" fill="none" stroke="#78350f" stroke-width="2" stroke-linecap="round"/>
    </svg>

    {{-- GIAI ĐOẠN 4: RỒNG TRƯỞNG THÀNH --}}
    @elseif($stage === 4)
    <svg class="w-full h-full drop-shadow-md" :class="getDynamicAnimClass()" viewBox="0 0 240 240">
        <!-- Grand Wings (Joint Anchored) -->
        <g class="anim-wing-left">
            <path d="M 65,115 Q -10,40 15,135 Q 45,160 70,135 Z" fill="#b91c1c" stroke="#7f1d1d" stroke-width="3.5"/>
            <path d="M 15,135 Q 40,90 65,115" stroke="#f87171" stroke-width="2"/>
        </g>
        <g class="anim-wing-right">
            <path d="M 175,115 Q 250,40 225,135 Q 195,160 170,135 Z" fill="#b91c1c" stroke="#7f1d1d" stroke-width="3.5"/>
            <path d="M 225,135 Q 200,90 175,115" stroke="#f87171" stroke-width="2"/>
        </g>
        <!-- Powerful Tail with Golden Crest -->
        <g class="anim-tail-joint">
            <path d="M 140,175 Q 220,195 210,125" fill="none" stroke="#dc2626" stroke-width="18" stroke-linecap="round"/>
            <polygon points="210,125 235,100 215,95 200,115" fill="#f59e0b"/>
        </g>
        <ellipse cx="120" cy="150" rx="52" ry="50" fill="#dc2626" stroke="#991b1b" stroke-width="4"/>
        <path d="M 98,125 L 142,125 L 135,180 L 105,180 Z" fill="#fbbf24" stroke="#d97706" stroke-width="2.5"/>
        <path d="M 85,65 Q 45,5 92,30 Q 82,45 88,60 Z" fill="#f59e0b" stroke="#b45309" stroke-width="2.5"/>
        <path d="M 155,65 Q 195,5 148,30 Q 158,45 152,60 Z" fill="#f59e0b" stroke="#b45309" stroke-width="2.5"/>
        <path d="M 75,70 Q 120,40 165,70 Q 175,115 120,120 Q 65,115 75,70 Z" fill="#dc2626" stroke="#991b1b" stroke-width="4"/>

        {{-- 1. MẮT HÌNH THÁI STAGE 4 --}}
        <g x-show="['happy', 'petting', 'cuddle', 'satisfied'].includes(resolveExpr())">
            <path d="M 90,86 Q 98,77 106,86" fill="none" stroke="#fef08a" stroke-width="3.8" stroke-linecap="round"/>
            <path d="M 134,86 Q 142,77 150,86" fill="none" stroke="#fef08a" stroke-width="3.8" stroke-linecap="round"/>
        </g>
        <g x-show="resolveExpr() === 'tea'" x-cloak>
            <path d="M 91,85 Q 98,90 105,85" fill="none" stroke="#fef08a" stroke-width="3.2" stroke-linecap="round"/>
            <path d="M 135,85 Q 142,90 149,85" fill="none" stroke="#fef08a" stroke-width="3.2" stroke-linecap="round"/>
        </g>
        <g x-show="resolveExpr() === 'dizzy'" x-cloak>
            <g style="transform-origin: 98px 88px; animation: dizzySpiral 1.2s linear infinite;">
                <path d="M 98,88 m -6,0 a 6,6 0 1,0 12,0 a 6,6 0 1,0 -12,0 m 2.5,0 a 3.5,3.5 0 1,1 7,0 a 3.5,3.5 0 1,1 -7,0" fill="none" stroke="#fef08a" stroke-width="2.5" stroke-linecap="round"/>
            </g>
            <g style="transform-origin: 142px 88px; animation: dizzySpiral 1.2s linear infinite;">
                <path d="M 142,88 m -6,0 a 6,6 0 1,0 12,0 a 6,6 0 1,0 -12,0 m 2.5,0 a 3.5,3.5 0 1,1 7,0 a 3.5,3.5 0 1,1 -7,0" fill="none" stroke="#fef08a" stroke-width="2.5" stroke-linecap="round"/>
            </g>
        </g>
        <g x-show="resolveExpr() === 'sleeping'" x-cloak>
            <path d="M 90,88 L 106,88" stroke="#fef08a" stroke-width="3.5" stroke-linecap="round"/>
            <path d="M 134,88 L 150,88" stroke="#fef08a" stroke-width="3.5" stroke-linecap="round"/>
        </g>
        <g x-show="!['happy', 'petting', 'cuddle', 'satisfied', 'tea', 'dizzy', 'sleeping'].includes(resolveExpr())">
            <circle cx="98" cy="88" r="7.5" fill="#262626"/>
            <circle cx="142" cy="88" r="7.5" fill="#262626"/>
            <g class="pet-pupil-tracker">
                <circle cx="96" cy="86" r="2.8" fill="#ffffff"/>
                <circle cx="140" cy="86" r="2.8" fill="#ffffff"/>
            </g>
        </g>

        {{-- Râu rồng oai vệ --}}
        <path d="M 95,112 Q 70,120 60,110" fill="none" stroke="#fbbf24" stroke-width="2.5" stroke-linecap="round"/>
        <path d="M 145,112 Q 170,120 180,110" fill="none" stroke="#fbbf24" stroke-width="2.5" stroke-linecap="round"/>

        {{-- Miệng Stage 4 --}}
        <g x-show="resolveExpr() === 'talking'" x-cloak>
            <ellipse cx="120" cy="112" rx="6" ry="4.5" fill="#7f1d1d" style="animation: mouthTalking 0.35s ease-in-out infinite alternate;"/>
        </g>
        <g x-show="['happy', 'petting', 'cuddle', 'satisfied'].includes(resolveExpr())" x-cloak>
            <path d="M 113,111 Q 120,117 127,111" fill="none" stroke="#fbbf24" stroke-width="2.5" stroke-linecap="round"/>
        </g>
    </svg>

    {{-- GIAI ĐOẠN 5: THẦN LONG HOÀNG KIM --}}
    @elseif($stage === 5)
    <svg class="w-full h-full drop-shadow-lg" :class="getDynamicAnimClass()" viewBox="0 0 260 260">
        <!-- Mây ngũ sắc bao quanh -->
        <path d="M 40,200 Q 70,180 90,205 Q 120,185 150,210 Q 180,190 220,215" fill="none" stroke="#60a5fa" stroke-width="4" opacity="0.6" stroke-linecap="round"/>
        <!-- Thân rồng vàng uốn lượn -->
        <path d="M 50,170 Q 110,230 190,160 Q 230,100 170,75 Q 110,65 95,115 Q 85,155 145,165"
              fill="none" stroke="#f59e0b" stroke-width="26" stroke-linecap="round" stroke-linejoin="round"/>
        <path d="M 50,170 Q 110,230 190,160 Q 230,100 170,75 Q 110,65 95,115 Q 85,155 145,165"
              fill="none" stroke="#fef08a" stroke-width="12" stroke-linecap="round"/>
        <!-- Sừng lân ngọc bích -->
        <path d="M 105,75 Q 75,30 95,40 Q 80,15 105,32 Q 110,50 115,70 Z" fill="#38bdf8" stroke="#0284c7" stroke-width="2"/>
        <path d="M 155,75 Q 185,30 165,40 Q 180,15 155,32 Q 150,50 145,70 Z" fill="#38bdf8" stroke="#0284c7" stroke-width="2"/>
        <ellipse cx="130" cy="95" rx="38" ry="32" fill="#f59e0b" stroke="#b45309" stroke-width="3.5"/>

        <!-- Râu rồng hoàng kim (Joint-anchored) -->
        <path class="anim-whisker-left" d="M 105,108 Q 65,120 40,105 Q 25,120 50,135" fill="none" stroke="#fef08a" stroke-width="3" stroke-linecap="round"/>
        <path class="anim-whisker-right" d="M 155,108 Q 195,120 220,105 Q 235,120 210,135" fill="none" stroke="#fef08a" stroke-width="3" stroke-linecap="round"/>

        {{-- 1. MẮT HÌNH THÁI STAGE 5 --}}
        <g x-show="['happy', 'petting', 'cuddle', 'satisfied'].includes(resolveExpr())">
            <path d="M 104,90 Q 112,81 120,90" fill="none" stroke="#78350f" stroke-width="3.8" stroke-linecap="round"/>
            <path d="M 140,90 Q 148,81 156,90" fill="none" stroke="#78350f" stroke-width="3.8" stroke-linecap="round"/>
        </g>
        <g x-show="resolveExpr() === 'tea'" x-cloak>
            <path d="M 105,89 Q 112,94 119,89" fill="none" stroke="#78350f" stroke-width="3.2" stroke-linecap="round"/>
            <path d="M 141,89 Q 148,94 155,89" fill="none" stroke="#78350f" stroke-width="3.2" stroke-linecap="round"/>
        </g>
        <g x-show="resolveExpr() === 'dizzy'" x-cloak>
            <g style="transform-origin: 112px 92px; animation: dizzySpiral 1.2s linear infinite;">
                <path d="M 112,92 m -6,0 a 6,6 0 1,0 12,0 a 6,6 0 1,0 -12,0 m 2.5,0 a 3.5,3.5 0 1,1 7,0 a 3.5,3.5 0 1,1 -7,0" fill="none" stroke="#78350f" stroke-width="2.5" stroke-linecap="round"/>
            </g>
            <g style="transform-origin: 148px 92px; animation: dizzySpiral 1.2s linear infinite;">
                <path d="M 148,92 m -6,0 a 6,6 0 1,0 12,0 a 6,6 0 1,0 -12,0 m 2.5,0 a 3.5,3.5 0 1,1 7,0 a 3.5,3.5 0 1,1 -7,0" fill="none" stroke="#78350f" stroke-width="2.5" stroke-linecap="round"/>
            </g>
        </g>
        <g x-show="resolveExpr() === 'sleeping'" x-cloak>
            <path d="M 104,92 L 120,92" stroke="#78350f" stroke-width="3.5" stroke-linecap="round"/>
            <path d="M 140,92 L 156,92" stroke="#78350f" stroke-width="3.5" stroke-linecap="round"/>
        </g>
        <g x-show="!['happy', 'petting', 'cuddle', 'satisfied', 'tea', 'dizzy', 'sleeping'].includes(resolveExpr())">
            <circle cx="112" cy="92" r="7.5" fill="#262626"/>
            <circle cx="148" cy="92" r="7.5" fill="#262626"/>
            <g class="pet-pupil-tracker">
                <circle cx="110" cy="90" r="2.8" fill="#ffffff"/>
                <circle cx="146" cy="90" r="2.8" fill="#ffffff"/>
            </g>
        </g>

        {{-- 2. MIỆNG STAGE 5 --}}
        <g x-show="resolveExpr() === 'talking'" x-cloak>
            <ellipse cx="130" cy="106" rx="5.5" ry="4" fill="#dc2626" style="animation: mouthTalking 0.35s ease-in-out infinite alternate;"/>
        </g>
        <g x-show="['happy', 'petting', 'cuddle', 'satisfied'].includes(resolveExpr())" x-cloak>
            <path d="M 124,104 Q 130,111 136,104" fill="#ef4444" stroke="#78350f" stroke-width="2" stroke-linecap="round"/>
        </g>
        <path x-show="!['talking', 'happy', 'petting', 'cuddle', 'satisfied'].includes(resolveExpr())"
              d="M 125,104 Q 130,108 135,104" fill="none" stroke="#78350f" stroke-width="2" stroke-linecap="round"/>

        <!-- Long Châu Lam Ngọc Thần Bí (Phát quang rực rỡ) -->
        <circle cx="130" cy="165" r="16" fill="#38bdf8" stroke="#e0f2fe" stroke-width="3"
                :class="resolveExpr() === 'happy' || resolveExpr() === 'satisfied' ? 'animate-ping' : ''"
                style="filter: drop-shadow(0 0 10px #38bdf8);"/>
    </svg>
    @endif

</div>
