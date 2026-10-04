<div class="relative inline-flex flex-col items-center justify-center select-none pet-mascot-anim"
     :class="{
         'pet-anim-bounce': state === 'happy',
         'pet-anim-wiggle': state === 'speaking',
         'pet-anim-glow': state === 'evolving',
         'pet-anim-poked': state === 'poked',
         'pet-anim-petting': state === 'petting',
         'pet-anim-observing': state === 'observing',
         'pet-anim-reading': state === 'reading',
         'pet-anim-dozing': state === 'dozing',
         'pet-anim-waking': state === 'waking_up',
         'pet-anim-waving': state === 'waving',
         'pet-anim-dizzy': state === 'dizzy',
         'pet-anim-cuddle': state === 'cuddle',
         'animate-[bounce_0.6s_ease-in-out_infinite]': state === 'excited'
     }">

    {{-- Floating hearts / particles effect when happy or excited or petting or cuddle --}}
    <div x-show="showHearts || state === 'petting' || state === 'cuddle'" x-cloak class="absolute -top-3 left-1/2 -translate-x-1/2 pointer-events-none z-20 flex gap-1">
        <span class="pet-heart-particle text-rose-500 font-bold text-base inline-block">❤️</span>
        <span class="pet-heart-particle text-amber-400 font-bold text-xs inline-block" style="animation-delay: 0.2s;">✨</span>
        <span x-show="state === 'cuddle'" class="text-xs font-bold text-pink-400 inline-block animate-bounce">🥰</span>
    </div>

    {{-- Dizzy stars / swirl particle --}}
    <div x-show="state === 'dizzy'" x-cloak class="absolute -top-3 left-1/2 -translate-x-1/2 pointer-events-none z-20 flex gap-1">
        <span class="text-xs font-black inline-block animate-spin">💫</span>
        <span class="text-xs font-black inline-block animate-ping">✨</span>
    </div>

    {{-- Reading partner accessory (studying quietly beside user) --}}
    <div x-show="state === 'reading'" x-cloak class="absolute -top-2.5 -right-1 pointer-events-none z-20">
        <span class="text-xs inline-block select-none filter drop-shadow">📖</span>
    </div>

    {{-- Dozing / sleepy thought particle --}}
    <div x-show="state === 'dozing'" x-cloak class="absolute -top-2.5 -right-1 pointer-events-none z-20">
        <span class="text-xs inline-block select-none opacity-80 animate-pulse">💤</span>
    </div>

    {{-- Poked startled blush --}}
    <div x-show="state === 'poked'" x-cloak class="absolute -top-3 left-1/2 -translate-x-1/2 pointer-events-none z-20">
        <span class="text-xs font-black text-amber-600 inline-block animate-bounce">😳</span>
    </div>

    {{-- Sleeping ZZZ particles --}}
    <div x-show="state === 'sleeping'" x-cloak class="absolute -top-3 -right-2 pointer-events-none z-20 flex flex-col items-end">
        <span class="pet-sleep-particle text-[11px] font-black text-indigo-500">z</span>
        <span class="pet-sleep-particle text-[13px] font-black text-indigo-600" style="animation-delay: 0.6s;">Z</span>
        <span class="pet-sleep-particle text-[15px] font-black text-indigo-700" style="animation-delay: 1.2s;">Z</span>
    </div>

    {{-- Personality Characteristic Particle Aura (Luôn bay bổng nhẹ nhàng quanh Pet theo tính cách) --}}
    <div x-show="state !== 'sleeping'" x-cloak class="absolute inset-0 pointer-events-none z-20 overflow-visible">
        {{-- Playful: Sao lấp lánh & Tia chớp vui nhộn --}}
        <template x-if="!pet.personality || pet.personality === 'playful'">
            <div class="w-full h-full relative">
                <span class="aura-playful-particle absolute -top-1.5 -left-1 text-[11px] select-none">⭐</span>
                <span class="aura-playful-particle absolute top-3 -right-2 text-[10px] select-none" style="animation-delay: 0.8s;">✨</span>
            </div>
        </template>

        {{-- Curious: Kính lúp & Bóng đèn ý tưởng --}}
        <template x-if="pet.personality === 'curious'">
            <div class="w-full h-full relative">
                <span class="aura-curious-particle absolute -top-2 right-0 text-[11px] select-none">💡</span>
                <span class="aura-curious-particle absolute bottom-2 -left-2 text-[10px] select-none" style="animation-delay: 1.1s;">🔍</span>
            </div>
        </template>

        {{-- Shy: Cánh hoa anh đào & Trái tim phấn hồng --}}
        <template x-if="pet.personality === 'shy'">
            <div class="w-full h-full relative">
                <span class="aura-shy-particle absolute -top-1.5 -left-1 text-[11px] select-none">🌸</span>
                <span class="aura-shy-particle absolute top-4 -right-2 text-[10px] select-none" style="animation-delay: 1.2s;">💕</span>
            </div>
        </template>

        {{-- Cheerful: Ánh mặt trời vàng & Đốm sáng rực rỡ --}}
        <template x-if="pet.personality === 'cheerful'">
            <div class="w-full h-full relative">
                <span class="aura-cheerful-particle absolute -top-2 left-1/2 -translate-x-1/2 text-[12px] select-none">☀️</span>
                <span class="aura-playful-particle absolute top-5 -right-2 text-[10px] select-none" style="animation-delay: 0.5s;">🌟</span>
            </div>
        </template>

        {{-- Calm: Lá trà xanh & Làn sương thanh tịnh --}}
        <template x-if="pet.personality === 'calm'">
            <div class="w-full h-full relative">
                <span class="aura-calm-particle absolute -top-1 -right-1 text-[11px] select-none">🍃</span>
                <span class="aura-calm-particle absolute bottom-1 -left-2 text-[10px] select-none" style="animation-delay: 1.6s;">🍵</span>
            </div>
        </template>
    </div>

    {{-- Main SVG Container with Personality-specific Idle Animations & Head Tilt --}}
    <div class="w-16 h-16 sm:w-20 sm:h-20 transition-all duration-300 transform relative pet-head-tilt"
         :class="{
             'animate-[eggWobble_4s_ease-in-out_infinite]': pet && pet.stage === 0 && state === 'idle',
             'pet-anim-idle-playful': pet && pet.stage > 0 && (state === 'idle' || state === 'speaking') && (!pet.personality || pet.personality === 'playful'),
             'pet-anim-idle-curious': pet && pet.stage > 0 && (state === 'idle' || state === 'speaking') && pet.personality === 'curious',
             'pet-anim-idle-shy': pet && pet.stage > 0 && (state === 'idle' || state === 'speaking') && pet.personality === 'shy',
             'pet-anim-idle-cheerful': pet && pet.stage > 0 && (state === 'idle' || state === 'speaking') && pet.personality === 'cheerful',
             'pet-anim-idle-calm': pet && pet.stage > 0 && (state === 'idle' || state === 'speaking') && pet.personality === 'calm',
             'translate-y-1.5 opacity-90': state === 'hungry',
             'opacity-80 scale-95': state === 'sleeping',
             'scale-110': state === 'excited'
         }">

        {{-- Stage 0: Quả trứng rồng ma thuật --}}
        <svg x-show="pet && pet.stage === 0" x-cloak class="w-full h-full drop-shadow-md" viewBox="0 0 160 200">
            <defs>
                <linearGradient id="floatingEggGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="#fef3c7" />
                    <stop offset="50%" stop-color="#f59e0b" />
                    <stop offset="100%" stop-color="#b45309" />
                </linearGradient>
            </defs>
            <path d="M 80,10 C 130,10 150,90 150,140 C 150,180 120,195 80,195 C 40,195 10,180 10,140 C 10,90 30,10 80,10 Z"
                  fill="url(#floatingEggGrad)" stroke="#78350f" stroke-width="4" />
            <path d="M 60,60 Q 80,80 100,60" fill="none" stroke="#fff" stroke-width="3.5" opacity="0.6" stroke-linecap="round"/>
            <path d="M 45,95 Q 65,115 85,95" fill="none" stroke="#fff" stroke-width="3.5" opacity="0.6" stroke-linecap="round"/>
            <path d="M 75,100 Q 95,120 115,100" fill="none" stroke="#fff" stroke-width="3.5" opacity="0.6" stroke-linecap="round"/>
            <path d="M 60,135 Q 80,155 100,135" fill="none" stroke="#fff" stroke-width="3.5" opacity="0.6" stroke-linecap="round"/>
            <path d="M 80,75 L 88,90 L 78,105 L 92,125" fill="none" stroke="#fef08a" stroke-width="3" stroke-linecap="round"/>
            <ellipse cx="50" cy="50" rx="14" ry="24" fill="#ffffff" opacity="0.45" transform="rotate(-25 50 50)"/>

            {{-- Personality hint on Egg --}}
            <g x-show="pet && pet.personality === 'shy'">
                <circle cx="80" cy="115" r="8" fill="#f472b6" opacity="0.7"/>
            </g>
            <g x-show="pet && pet.personality === 'curious'">
                <text x="80" y="125" font-size="22" font-weight="bold" fill="#78350f" text-anchor="middle" opacity="0.6">?</text>
            </g>
            <g x-show="pet && pet.personality === 'calm'">
                <path d="M 75,115 Q 80,105 85,115 Z" fill="#22c55e" opacity="0.75"/>
            </g>
        </svg>

        {{-- Stage 1: Rồng sơ sinh trong vỏ trứng --}}
        <svg x-show="pet && pet.stage === 1" x-cloak class="w-full h-full drop-shadow-md" viewBox="0 0 180 200">
            {{-- Cheerful Sunny Halo Background --}}
            <g x-show="pet && pet.personality === 'cheerful'">
                <circle cx="90" cy="85" r="54" fill="none" stroke="#fde047" stroke-width="3" stroke-dasharray="8 6" opacity="0.85" class="aura-cheerful-particle"/>
            </g>

            <path class="anim-tail-joint" d="M 130,150 Q 165,160 160,140" fill="none" stroke="#f59e0b" stroke-width="10" stroke-linecap="round"/>
            <ellipse cx="90" cy="115" rx="50" ry="45" fill="#f59e0b" stroke="#b45309" stroke-width="3.5"/>
            <ellipse cx="90" cy="125" rx="32" ry="28" fill="#fef3c7"/>
            <path d="M 62,55 Q 55,35 68,38 Z" fill="#ef4444" stroke="#b91c1c" stroke-width="2"/>
            <path d="M 118,55 Q 125,35 112,38 Z" fill="#ef4444" stroke="#b91c1c" stroke-width="2"/>
            <circle cx="90" cy="85" r="48" fill="#f59e0b" stroke="#b45309" stroke-width="3.5"/>
            
            {{-- Má hồng (Thích ứng theo state & tính cách) --}}
            <circle cx="62" cy="98" :r="state === 'poked' || state === 'petting' || (pet && pet.personality === 'shy') ? 10 : 8" :fill="state === 'poked' || state === 'petting' || (pet && pet.personality === 'shy') ? '#fb7185' : '#f87171'" :opacity="state === 'poked' || state === 'petting' || (pet && pet.personality === 'shy') ? '0.98' : (state === 'happy' || state === 'excited' ? '0.9' : '0.5')"/>
            <circle cx="118" cy="98" :r="state === 'poked' || state === 'petting' || (pet && pet.personality === 'shy') ? 10 : 8" :fill="state === 'poked' || state === 'petting' || (pet && pet.personality === 'shy') ? '#fb7185' : '#f87171'" :opacity="state === 'poked' || state === 'petting' || (pet && pet.personality === 'shy') ? '0.98' : (state === 'happy' || state === 'excited' ? '0.9' : '0.5')"/>

            {{-- Đôi mắt linh hoạt theo State --}}
            {{-- Happy / Excited / Petting: mắt cười tít trăng khuyết --}}
            <g x-show="state === 'happy' || state === 'excited' || state === 'petting'">
                <path d="M 64,80 Q 72,72 80,80" fill="none" stroke="#78350f" stroke-width="3.5" stroke-linecap="round"/>
                <path d="M 100,80 Q 108,72 116,80" fill="none" stroke="#78350f" stroke-width="3.5" stroke-linecap="round"/>
            </g>
            {{-- Sleeping: mắt nhắm phẳng --}}
            <g x-show="state === 'sleeping'">
                <path d="M 64,82 L 80,82" stroke="#78350f" stroke-width="3.5" stroke-linecap="round"/>
                <path d="M 100,82 L 116,82" stroke="#78350f" stroke-width="3.5" stroke-linecap="round"/>
            </g>
            {{-- Dozing: mắt lim dim nửa nhắm --}}
            <g x-show="state === 'dozing'">
                <path d="M 64,81 Q 72,85 80,81" fill="none" stroke="#78350f" stroke-width="3" stroke-linecap="round"/>
                <path d="M 100,81 Q 108,85 116,81" fill="none" stroke="#78350f" stroke-width="3" stroke-linecap="round"/>
            </g>
            {{-- Poked: mắt tròn xoe ngơ ngác giật mình --}}
            <g x-show="state === 'poked'">
                <circle cx="72" cy="80" r="10.5" fill="#262626"/>
                <circle cx="108" cy="80" r="10.5" fill="#262626"/>
                <circle cx="70" cy="77" r="4.5" fill="#ffffff"/>
                <circle cx="106" cy="77" r="4.5" fill="#ffffff"/>
            </g>
            {{-- Hungry: mắt buồn long lanh --}}
            <g x-show="state === 'hungry'">
                <circle cx="72" cy="80" r="9" fill="#262626"/>
                <circle cx="108" cy="80" r="9" fill="#262626"/>
                <circle cx="69" cy="77" r="4" fill="#ffffff"/>
                <circle cx="105" cy="77" r="4" fill="#ffffff"/>
                <circle cx="75" cy="84" r="2" fill="#93c5fd"/>
                <circle cx="111" cy="84" r="2" fill="#93c5fd"/>
            </g>
            {{-- Idle, Observing, Reading, Curious, Waking: mắt to tròn với pupil tracker --}}
            <g x-show="state !== 'happy' && state !== 'excited' && state !== 'petting' && state !== 'sleeping' && state !== 'dozing' && state !== 'poked' && state !== 'hungry'">
                <circle cx="72" cy="80" r="8.5" fill="#262626"/>
                <circle cx="108" cy="80" r="8.5" fill="#262626"/>
                <g class="pet-pupil-tracker">
                    <circle cx="70" cy="78" r="3" fill="#ffffff"/>
                    <circle cx="106" cy="78" r="3" fill="#ffffff"/>
                </g>
            </g>

            {{-- Mũi & Miệng --}}
            <ellipse cx="90" cy="96" rx="14" ry="9" fill="#fef3c7" stroke="#d97706" stroke-width="1.5"/>
            <ellipse x-show="state === 'speaking'" cx="90" cy="98" rx="4" ry="3" fill="#dc2626"/>
            <path x-show="state !== 'speaking'" d="M 87,96 Q 90,99 93,96" fill="none" stroke="#78350f" stroke-width="2" stroke-linecap="round"/>

            {{-- Stage 1 Personality Accessories --}}
            {{-- Curious: Kính tròn tri thức --}}
            <g x-show="pet && pet.personality === 'curious'">
                <circle cx="72" cy="80" r="13" fill="none" stroke="#b45309" stroke-width="2.5" opacity="0.9"/>
                <circle cx="108" cy="80" r="13" fill="none" stroke="#b45309" stroke-width="2.5" opacity="0.9"/>
                <path d="M 85,80 L 95,80" fill="none" stroke="#b45309" stroke-width="2.5"/>
                <path d="M 66,74 L 71,71" stroke="#ffffff" stroke-width="2" stroke-linecap="round" opacity="0.8"/>
                <path d="M 102,74 L 107,71" stroke="#ffffff" stroke-width="2" stroke-linecap="round" opacity="0.8"/>
            </g>
            {{-- Shy: Bông hoa cài đầu trên sừng --}}
            <g x-show="pet && pet.personality === 'shy'">
                <circle cx="56" cy="46" r="5" fill="#f472b6" opacity="0.95"/>
                <circle cx="51" cy="48" r="4.5" fill="#fbcfe8" opacity="0.9"/>
                <circle cx="61" cy="48" r="4.5" fill="#fbcfe8" opacity="0.9"/>
                <circle cx="53" cy="53" r="4.5" fill="#fbcfe8" opacity="0.9"/>
                <circle cx="59" cy="53" r="4.5" fill="#fbcfe8" opacity="0.9"/>
                <circle cx="56" cy="49" r="2.5" fill="#fef08a"/>
            </g>
            {{-- Playful: Ngôi sao may mắn trên trán --}}
            <g x-show="!pet.personality || pet.personality === 'playful'">
                <path d="M 90,60 L 92,65 L 97,65 L 93,68 L 95,73 L 90,70 L 85,73 L 87,68 L 83,65 L 88,65 Z" fill="#fbbf24" stroke="#d97706" stroke-width="1"/>
            </g>
            {{-- Calm: Mầm lá trà xanh thanh tịnh trên đầu --}}
            <g x-show="pet && pet.personality === 'calm'">
                <path d="M 90,44 Q 82,34 92,30 Q 98,38 90,44 Z" fill="#22c55e" stroke="#15803d" stroke-width="1.5"/>
                <path d="M 90,44 Q 98,36 104,33 Q 102,44 90,44 Z" fill="#4ade80" stroke="#15803d" stroke-width="1.5"/>
            </g>

            {{-- Vỏ trứng vỡ --}}
            <path d="M 38,135 Q 35,185 90,185 Q 145,185 142,135 L 125,145 L 110,132 L 90,148 L 70,132 L 55,145 Z"
                  fill="#fef3c7" stroke="#78350f" stroke-width="3.5"/>
        </svg>

        {{-- Stage 2: Rồng bé có cánh --}}
        <svg x-show="pet && pet.stage === 2" x-cloak class="w-full h-full drop-shadow-md" viewBox="0 0 200 200">
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
            <circle cx="72" cy="98" r="8" fill="#f87171" :opacity="state === 'happy' || state === 'excited' ? '0.9' : '0.5'"/>
            <circle cx="128" cy="98" r="8" fill="#f87171" :opacity="state === 'happy' || state === 'excited' ? '0.9' : '0.5'"/>
            
            <g x-show="state === 'happy' || state === 'excited' || state === 'petting'">
                <path d="M 74,80 Q 82,72 90,80" fill="none" stroke="#78350f" stroke-width="3.5" stroke-linecap="round"/>
                <path d="M 110,80 Q 118,72 126,80" fill="none" stroke="#78350f" stroke-width="3.5" stroke-linecap="round"/>
            </g>
            <g x-show="state === 'sleeping'">
                <path d="M 74,82 L 90,82" stroke="#78350f" stroke-width="3.5" stroke-linecap="round"/>
                <path d="M 110,82 L 126,82" stroke="#78350f" stroke-width="3.5" stroke-linecap="round"/>
            </g>
            <g x-show="state === 'dozing'">
                <path d="M 74,81 Q 82,85 90,81" fill="none" stroke="#78350f" stroke-width="3" stroke-linecap="round"/>
                <path d="M 110,81 Q 118,85 126,81" fill="none" stroke="#78350f" stroke-width="3" stroke-linecap="round"/>
            </g>
            <g x-show="state === 'poked'">
                <circle cx="82" cy="82" r="10.5" fill="#262626"/>
                <circle cx="118" cy="82" r="10.5" fill="#262626"/>
                <circle cx="79" cy="79" r="4.5" fill="#ffffff"/>
                <circle cx="115" cy="79" r="4.5" fill="#ffffff"/>
            </g>
            <g x-show="state !== 'happy' && state !== 'excited' && state !== 'petting' && state !== 'sleeping' && state !== 'dozing' && state !== 'poked'">
                <circle cx="82" cy="82" r="8.5" fill="#262626"/>
                <circle cx="118" cy="82" r="8.5" fill="#262626"/>
                <g class="pet-pupil-tracker">
                    <circle cx="79.5" cy="79.5" r="3.2" fill="#ffffff"/>
                    <circle cx="115.5" cy="79.5" r="3.2" fill="#ffffff"/>
                </g>
            </g>

            <ellipse cx="100" cy="98" rx="16" ry="10" fill="#fef3c7" stroke="#d97706" stroke-width="1.5"/>
            <path d="M 94,98 Q 100,103 106,98" fill="none" stroke="#78350f" stroke-width="2" stroke-linecap="round"/>

            {{-- Stage 2 Personality Accessories --}}
            {{-- Cheerful: Radiant Sunny Halo --}}
            <g x-show="pet && pet.personality === 'cheerful'">
                <circle cx="100" cy="85" r="54" fill="none" stroke="#fde047" stroke-width="3" stroke-dasharray="8 6" opacity="0.85" class="aura-cheerful-particle"/>
            </g>
            {{-- Curious: Kính tròn tri thức --}}
            <g x-show="pet && pet.personality === 'curious'">
                <circle cx="82" cy="82" r="13" fill="none" stroke="#b45309" stroke-width="2.5" opacity="0.9"/>
                <circle cx="118" cy="82" r="13" fill="none" stroke="#b45309" stroke-width="2.5" opacity="0.9"/>
                <path d="M 95,82 L 105,82" fill="none" stroke="#b45309" stroke-width="2.5"/>
                <path d="M 76,76 L 81,73" stroke="#ffffff" stroke-width="2" stroke-linecap="round" opacity="0.8"/>
                <path d="M 112,76 L 117,73" stroke="#ffffff" stroke-width="2" stroke-linecap="round" opacity="0.8"/>
            </g>
            {{-- Shy: Bông hoa anh đào cài đầu trên sừng & Má hồng đậm --}}
            <g x-show="pet && pet.personality === 'shy'">
                <circle cx="66" cy="48" r="5.5" fill="#f472b6" opacity="0.95"/>
                <circle cx="61" cy="50" r="4.5" fill="#fbcfe8" opacity="0.9"/>
                <circle cx="71" cy="50" r="4.5" fill="#fbcfe8" opacity="0.9"/>
                <circle cx="63" cy="55" r="4.5" fill="#fbcfe8" opacity="0.9"/>
                <circle cx="69" cy="55" r="4.5" fill="#fbcfe8" opacity="0.9"/>
                <circle cx="66" cy="51" r="2.5" fill="#fef08a"/>
                <circle cx="72" cy="98" r="10" fill="#fb7185" opacity="0.95"/>
                <circle cx="128" cy="98" r="10" fill="#fb7185" opacity="0.95"/>
            </g>
            {{-- Playful: Ngôi sao may mắn trên trán --}}
            <g x-show="!pet.personality || pet.personality === 'playful'">
                <path d="M 100,60 L 102,65 L 107,65 L 103,68 L 105,73 L 100,70 L 95,73 L 97,68 L 93,65 L 98,65 Z" fill="#fbbf24" stroke="#d97706" stroke-width="1"/>
            </g>
            {{-- Calm: Mầm lá trà xanh thanh tịnh trên đầu & Làn sương dưới chân --}}
            <g x-show="pet && pet.personality === 'calm'">
                <path d="M 100,44 Q 92,34 102,30 Q 108,38 100,44 Z" fill="#22c55e" stroke="#15803d" stroke-width="1.5"/>
                <path d="M 100,44 Q 108,36 114,33 Q 112,44 100,44 Z" fill="#4ade80" stroke="#15803d" stroke-width="1.5"/>
                <path d="M 68,175 Q 84,168 100,175 Q 116,168 132,175" fill="none" stroke="#6ee7b7" stroke-width="3" stroke-linecap="round" opacity="0.75"/>
            </g>
        </svg>

        {{-- Stage 3: Rồng thiếu niên --}}
        <svg x-show="pet && pet.stage === 3" x-cloak class="w-full h-full drop-shadow-md" viewBox="0 0 220 220">
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
            <circle cx="92" cy="86" r="8" fill="#262626"/>
            <circle cx="128" cy="86" r="8" fill="#262626"/>
            <g class="pet-pupil-tracker">
                <circle cx="90" cy="84" r="3" fill="#ffffff"/>
                <circle cx="126" cy="84" r="3" fill="#ffffff"/>
            </g>
            <ellipse cx="110" cy="100" rx="17" ry="11" fill="#fef3c7" stroke="#c2410c" stroke-width="1.5"/>
        </svg>

        {{-- Stage 4: Rồng trưởng thành --}}
        <svg x-show="pet && pet.stage === 4" x-cloak class="w-full h-full drop-shadow-md" viewBox="0 0 240 240">
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
            <g class="pet-pupil-tracker">
                <circle cx="96" cy="86" r="2.5" fill="#ffffff"/>
                <circle cx="140" cy="86" r="2.5" fill="#ffffff"/>
            </g>
        </svg>

        {{-- Stage 5: Thần Long Hoàng Kim --}}
        <svg x-show="pet && pet.stage >= 5" x-cloak class="w-full h-full drop-shadow-lg" viewBox="0 0 260 260">
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
            <g class="pet-pupil-tracker">
                <circle cx="110" cy="90" r="2.5" fill="#ffffff"/>
                <circle cx="146" cy="90" r="2.5" fill="#ffffff"/>
            </g>
            <circle cx="130" cy="165" r="16" fill="#38bdf8" stroke="#e0f2fe" stroke-width="3"/>
        </svg>
    </div>

    {{-- 3D Soft Shadow on the ground --}}
    <div class="w-12 sm:w-14 h-2 sm:h-2.5 mx-auto rounded-full bg-slate-900/20 filter blur-[2px] transition-all duration-300"
         :class="{
             'scale-75 opacity-40 translate-y-1': state === 'happy' || state === 'excited',
             'scale-100 opacity-80': state !== 'happy' && state !== 'excited'
         }">
    </div>
</div>
