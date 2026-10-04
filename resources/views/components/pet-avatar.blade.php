@props([
    'stage' => 0,
    'mood' => 'happy',
    'personality' => 'playful',
    'size' => 'md', // 'xs', 'sm', 'md', 'lg', 'xl'
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
     @if($interactive)
     x-data="{ clicking: false }"
     @click="clicking = true; setTimeout(() => clicking = false, 700)"
     :class="clicking ? 'scale-110 -translate-y-2' : ''"
     style="cursor: pointer; transition: transform 0.25s cubic-bezier(0.175, 0.885, 0.32, 1.275);"
     @endif>

    {{-- Personality Characteristic Particle Aura --}}
    @if(!$isDormant)
    <div x-cloak class="absolute inset-0 pointer-events-none z-20 overflow-visible">
        {{-- Playful: Sao lấp lánh --}}
        <div x-show="typeof personality !== 'undefined' ? (!personality || personality === 'playful') : ('{{ $personality }}' === 'playful')" class="w-full h-full relative">
            <span class="aura-playful-particle absolute -top-1.5 -left-1 text-[13px] select-none">⭐</span>
            <span class="aura-playful-particle absolute top-3 -right-2 text-[11px] select-none" style="animation-delay: 0.8s;">✨</span>
        </div>
        {{-- Curious: Kính lúp & Bóng đèn --}}
        <div x-show="typeof personality !== 'undefined' ? (personality === 'curious') : ('{{ $personality }}' === 'curious')" class="w-full h-full relative">
            <span class="aura-curious-particle absolute -top-2 right-0 text-[13px] select-none">💡</span>
            <span class="aura-curious-particle absolute bottom-2 -left-2 text-[11px] select-none" style="animation-delay: 1.1s;">🔍</span>
        </div>
        {{-- Shy: Hoa anh đào & Trái tim --}}
        <div x-show="typeof personality !== 'undefined' ? (personality === 'shy') : ('{{ $personality }}' === 'shy')" class="w-full h-full relative">
            <span class="aura-shy-particle absolute -top-1.5 -left-1 text-[13px] select-none">🌸</span>
            <span class="aura-shy-particle absolute top-4 -right-2 text-[11px] select-none" style="animation-delay: 1.2s;">💕</span>
        </div>
        {{-- Cheerful: Mặt trời & Đốm sáng --}}
        <div x-show="typeof personality !== 'undefined' ? (personality === 'cheerful') : ('{{ $personality }}' === 'cheerful')" class="w-full h-full relative">
            <span class="aura-cheerful-particle absolute -top-2 left-1/2 -translate-x-1/2 text-[14px] select-none">☀️</span>
            <span class="aura-playful-particle absolute top-5 -right-2 text-[11px] select-none" style="animation-delay: 0.5s;">🌟</span>
        </div>
        {{-- Calm: Lá trà & Sương thanh tịnh --}}
        <div x-show="typeof personality !== 'undefined' ? (personality === 'calm') : ('{{ $personality }}' === 'calm')" class="w-full h-full relative">
            <span class="aura-calm-particle absolute -top-1 -right-1 text-[13px] select-none">🍃</span>
            <span class="aura-calm-particle absolute bottom-1 -left-2 text-[11px] select-none" style="animation-delay: 1.6s;">🍵</span>
        </div>
    </div>
    @endif

    {{-- Giai đoạn 0: Quả trứng rồng ma thuật --}}
    @if($stage === 0)
    <svg class="w-full h-full drop-shadow-md" :class="typeof getDynamicPetAnimClass === 'function' ? getDynamicPetAnimClass() : '{{ $animClass }}'" viewBox="0 0 160 200">
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
    </svg>

    {{-- Giai đoạn 1: Rồng sơ sinh trong vỏ trứng --}}
    @elseif($stage === 1)
    <svg class="w-full h-full drop-shadow-md" :class="typeof getDynamicPetAnimClass === 'function' ? getDynamicPetAnimClass() : '{{ $animClass }}'" viewBox="0 0 180 200">
        <!-- Baby Tail (Joint-anchored) -->
        <path class="anim-tail-joint" d="M 130,150 Q 165,160 160,140" fill="none" stroke="#f59e0b" stroke-width="10" stroke-linecap="round"/>
        <ellipse cx="90" cy="115" rx="50" ry="45" fill="#f59e0b" stroke="#b45309" stroke-width="3.5"/>
        <ellipse cx="90" cy="125" rx="32" ry="28" fill="#fef3c7"/>
        <path d="M 62,55 Q 55,35 68,38 Z" fill="#ef4444" stroke="#b91c1c" stroke-width="2"/>
        <path d="M 118,55 Q 125,35 112,38 Z" fill="#ef4444" stroke="#b91c1c" stroke-width="2"/>
        <circle cx="90" cy="85" r="48" fill="#f59e0b" stroke="#b45309" stroke-width="3.5"/>
        <circle cx="62" cy="98" r="8" fill="#f87171" opacity="0.6"/>
        <circle cx="118" cy="98" r="8" fill="#f87171" opacity="0.6"/>
        {{-- Mắt --}}
        @if($isDormant)
            <path d="M 65,82 Q 72,88 80,82" fill="none" stroke="#78350f" stroke-width="3" stroke-linecap="round"/>
            <path d="M 100,82 Q 108,88 115,82" fill="none" stroke="#78350f" stroke-width="3" stroke-linecap="round"/>
        @else
            <circle cx="72" cy="80" r="8" fill="#262626"/>
            <circle cx="108" cy="80" r="8" fill="#262626"/>
            <circle cx="70" cy="78" r="3" fill="#ffffff"/>
            <circle cx="106" cy="78" r="3" fill="#ffffff"/>
        @endif
        <ellipse cx="90" cy="96" rx="14" ry="9" fill="#fef3c7" stroke="#d97706" stroke-width="1.5"/>
        <path d="M 87,96 Q 90,99 93,96" fill="none" stroke="#78350f" stroke-width="2" stroke-linecap="round"/>
        <path d="M 38,135 Q 35,185 90,185 Q 145,185 142,135 L 125,145 L 110,132 L 90,148 L 70,132 L 55,145 Z"
              fill="#fef3c7" stroke="#78350f" stroke-width="3.5"/>
    </svg>

    {{-- Giai đoạn 2: Rồng bé có cánh --}}
    @elseif($stage === 2)
    <svg class="w-full h-full drop-shadow-md" :class="typeof getDynamicPetAnimClass === 'function' ? getDynamicPetAnimClass() : '{{ $animClass }}'" viewBox="0 0 200 200">
        <!-- Tail with flame tip (Joint-anchored group) -->
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
        <circle cx="72" cy="98" r="8" fill="#f87171" opacity="0.6"/>
        <circle cx="128" cy="98" r="8" fill="#f87171" opacity="0.6"/>
        @if($isDormant)
            <path d="M 74,82 Q 82,88 90,82" fill="none" stroke="#78350f" stroke-width="3" stroke-linecap="round"/>
            <path d="M 110,82 Q 118,88 126,82" fill="none" stroke="#78350f" stroke-width="3" stroke-linecap="round"/>
        @else
            <circle cx="82" cy="82" r="8.5" fill="#262626"/>
            <circle cx="118" cy="82" r="8.5" fill="#262626"/>
            <circle cx="79.5" cy="79.5" r="3.2" fill="#ffffff"/>
            <circle cx="115.5" cy="79.5" r="3.2" fill="#ffffff"/>
        @endif
        <ellipse cx="100" cy="98" rx="16" ry="10" fill="#fef3c7" stroke="#d97706" stroke-width="1.5"/>
        <path d="M 94,98 Q 100,103 106,98" fill="none" stroke="#78350f" stroke-width="2" stroke-linecap="round"/>
    </svg>

    {{-- Giai đoạn 3: Rồng thiếu niên --}}
    @elseif($stage === 3)
    <svg class="w-full h-full drop-shadow-md" :class="typeof getDynamicPetAnimClass === 'function' ? getDynamicPetAnimClass() : '{{ $animClass }}'" viewBox="0 0 220 220">
        <!-- Tail with flaming plume (Joint-anchored group) -->
        <g class="anim-tail-joint">
            <path d="M 130,160 Q 195,175 190,120" fill="none" stroke="#ea580c" stroke-width="14" stroke-linecap="round"/>
            <path d="M 185,120 Q 210,100 195,85 Q 180,105 175,115 Z" fill="#ef4444"/>
            <path d="M 188,115 Q 200,100 192,92 Z" fill="#fef08a"/>
        </g>
        <!-- Large Dragon Wings (Left & Right - Joint Anchored) -->
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
        @if($isDormant)
            <path d="M 84,86 Q 92,92 100,86" fill="none" stroke="#78350f" stroke-width="3" stroke-linecap="round"/>
            <path d="M 120,86 Q 128,92 136,86" fill="none" stroke="#78350f" stroke-width="3" stroke-linecap="round"/>
        @else
            <circle cx="92" cy="86" r="8" fill="#262626"/>
            <circle cx="128" cy="86" r="8" fill="#262626"/>
            <circle cx="90" cy="84" r="3" fill="#ffffff"/>
            <circle cx="126" cy="84" r="3" fill="#ffffff"/>
        @endif
        <ellipse cx="110" cy="100" rx="17" ry="11" fill="#fef3c7" stroke="#c2410c" stroke-width="1.5"/>
    </svg>

    {{-- Giai đoạn 4: Rồng trưởng thành --}}
    @elseif($stage === 4)
    <svg class="w-full h-full drop-shadow-md" :class="typeof getDynamicPetAnimClass === 'function' ? getDynamicPetAnimClass() : '{{ $animClass }}'" viewBox="0 0 240 240">
        <!-- Grand Wings Background (Left & Right - Joint Anchored) -->
        <g class="anim-wing-left">
            <path d="M 65,115 Q -10,40 15,135 Q 45,160 70,135 Z" fill="#b91c1c" stroke="#7f1d1d" stroke-width="3.5"/>
            <path d="M 15,135 Q 40,90 65,115" stroke="#f87171" stroke-width="2"/>
        </g>
        <g class="anim-wing-right">
            <path d="M 175,115 Q 250,40 225,135 Q 195,160 170,135 Z" fill="#b91c1c" stroke="#7f1d1d" stroke-width="3.5"/>
            <path d="M 225,135 Q 200,90 175,115" stroke="#f87171" stroke-width="2"/>
        </g>
        <!-- Powerful Tail with Golden Crest (Joint-anchored group) -->
        <g class="anim-tail-joint">
            <path d="M 140,175 Q 220,195 210,125" fill="none" stroke="#dc2626" stroke-width="18" stroke-linecap="round"/>
            <polygon points="210,125 235,100 215,95 200,115" fill="#f59e0b"/>
        </g>
        <ellipse cx="120" cy="150" rx="52" ry="50" fill="#dc2626" stroke="#991b1b" stroke-width="4"/>
        <path d="M 98,125 L 142,125 L 135,180 L 105,180 Z" fill="#fbbf24" stroke="#d97706" stroke-width="2.5"/>
        <path d="M 85,65 Q 45,5 92,30 Q 82,45 88,60 Z" fill="#f59e0b" stroke="#b45309" stroke-width="2.5"/>
        <path d="M 155,65 Q 195,5 148,30 Q 158,45 152,60 Z" fill="#f59e0b" stroke="#b45309" stroke-width="2.5"/>
        <path d="M 75,70 Q 120,40 165,70 Q 175,115 120,120 Q 65,115 75,70 Z" fill="#dc2626" stroke="#991b1b" stroke-width="4"/>
        <circle cx="98" cy="88" r="7" fill="#262626"/>
        <circle cx="142" cy="88" r="7" fill="#262626"/>
        <circle cx="96" cy="86" r="2.5" fill="#ffffff"/>
        <circle cx="140" cy="86" r="2.5" fill="#ffffff"/>
        <path d="M 95,112 Q 70,120 60,110" fill="none" stroke="#fbbf24" stroke-width="2.5" stroke-linecap="round"/>
        <path d="M 145,112 Q 170,120 180,110" fill="none" stroke="#fbbf24" stroke-width="2.5" stroke-linecap="round"/>
    </svg>

    {{-- Giai đoạn 5: Thần Long Hoàng Kim --}}
    @elseif($stage === 5)
    <svg class="w-full h-full drop-shadow-lg" :class="typeof getDynamicPetAnimClass === 'function' ? getDynamicPetAnimClass() : '{{ $animClass }}'" viewBox="0 0 260 260">
        <path d="M 40,200 Q 70,180 90,205 Q 120,185 150,210 Q 180,190 220,215" fill="none" stroke="#60a5fa" stroke-width="4" opacity="0.6" stroke-linecap="round"/>
        <path d="M 50,170 Q 110,230 190,160 Q 230,100 170,75 Q 110,65 95,115 Q 85,155 145,165"
              fill="none" stroke="#f59e0b" stroke-width="26" stroke-linecap="round" stroke-linejoin="round"/>
        <path d="M 50,170 Q 110,230 190,160 Q 230,100 170,75 Q 110,65 95,115 Q 85,155 145,165"
              fill="none" stroke="#fef08a" stroke-width="12" stroke-linecap="round"/>
        <path d="M 105,75 Q 75,30 95,40 Q 80,15 105,32 Q 110,50 115,70 Z" fill="#38bdf8" stroke="#0284c7" stroke-width="2"/>
        <path d="M 155,75 Q 185,30 165,40 Q 180,15 155,32 Q 150,50 145,70 Z" fill="#38bdf8" stroke="#0284c7" stroke-width="2"/>
        <ellipse cx="130" cy="95" rx="38" ry="32" fill="#f59e0b" stroke="#b45309" stroke-width="3.5"/>
        <!-- Golden Whiskers flowing (Anchored to snout joints) -->
        <path class="anim-whisker-left" d="M 105,108 Q 65,120 40,105 Q 25,120 50,135" fill="none" stroke="#fef08a" stroke-width="3" stroke-linecap="round"/>
        <path class="anim-whisker-right" d="M 155,108 Q 195,120 220,105 Q 235,120 210,135" fill="none" stroke="#fef08a" stroke-width="3" stroke-linecap="round"/>
        <circle cx="112" cy="92" r="7" fill="#262626"/>
        <circle cx="148" cy="92" r="7" fill="#262626"/>
        <circle cx="110" cy="90" r="2.5" fill="#ffffff"/>
        <circle cx="146" cy="90" r="2.5" fill="#ffffff"/>
        <circle cx="130" cy="165" r="16" fill="#38bdf8" stroke="#e0f2fe" stroke-width="3"/>
    </svg>
    @endif

</div>
