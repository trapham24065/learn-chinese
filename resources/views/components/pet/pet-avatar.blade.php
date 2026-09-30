<div class="relative inline-flex flex-col items-center justify-center select-none pet-mascot-anim"
     :class="{
         'pet-anim-bounce': state === 'happy',
         'pet-anim-wiggle': state === 'speaking',
         'pet-anim-glow': state === 'evolving',
         'animate-[bounce_0.6s_ease-in-out_infinite]': state === 'excited'
     }">

    {{-- Floating hearts / particles effect when happy or excited --}}
    <template x-for="heart in hearts" :key="heart.id">
        <span class="absolute pointer-events-none pet-heart-particle text-rose-500 font-bold z-20"
              :style="'left: ' + heart.x + 'px; top: ' + heart.y + 'px; font-size: ' + heart.size + 'px;'">
            <span x-text="heart.icon || '❤️'"></span>
        </span>
    </template>

    {{-- Sleeping ZZZ particles --}}
    <div x-show="state === 'sleeping'" class="absolute -top-3 -right-2 pointer-events-none z-20 flex flex-col items-end">
        <span class="pet-sleep-particle text-[11px] font-black text-indigo-500">z</span>
        <span class="pet-sleep-particle text-[13px] font-black text-indigo-600" style="animation-delay: 0.6s;">Z</span>
        <span class="pet-sleep-particle text-[15px] font-black text-indigo-700" style="animation-delay: 1.2s;">Z</span>
    </div>

    {{-- Main SVG Container --}}
    <div class="w-16 h-16 sm:w-20 sm:h-20 transition-all duration-300 transform"
         :class="{
             'animate-[eggWobble_4s_ease-in-out_infinite]': pet && pet.stage === 0 && state === 'idle',
             'animate-[floatBreathing_3.6s_ease-in-out_infinite]': pet && pet.stage > 0 && (state === 'idle' || state === 'speaking'),
             'translate-y-1.5 opacity-90': state === 'hungry',
             'opacity-80 scale-95': state === 'sleeping',
             'scale-110': state === 'excited'
         }">

        {{-- Stage 0: Quả trứng rồng ma thuật --}}
        <template x-if="pet && pet.stage === 0">
            <svg class="w-full h-full drop-shadow-md" viewBox="0 0 160 200">
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
            </svg>
        </template>

        {{-- Stage 1: Rồng sơ sinh trong vỏ trứng --}}
        <template x-if="pet && pet.stage === 1">
            <svg class="w-full h-full drop-shadow-md" viewBox="0 0 180 200">
                <path d="M 130,150 Q 165,160 160,140" fill="none" stroke="#f59e0b" stroke-width="10" stroke-linecap="round"/>
                <ellipse cx="90" cy="115" rx="50" ry="45" fill="#f59e0b" stroke="#b45309" stroke-width="3.5"/>
                <ellipse cx="90" cy="125" rx="32" ry="28" fill="#fef3c7"/>
                <path d="M 62,55 Q 55,35 68,38 Z" fill="#ef4444" stroke="#b91c1c" stroke-width="2"/>
                <path d="M 118,55 Q 125,35 112,38 Z" fill="#ef4444" stroke="#b91c1c" stroke-width="2"/>
                <circle cx="90" cy="85" r="48" fill="#f59e0b" stroke="#b45309" stroke-width="3.5"/>
                
                {{-- Má hồng --}}
                <circle cx="62" cy="98" r="8" fill="#f87171" :opacity="state === 'happy' || state === 'excited' ? '0.9' : '0.5'"/>
                <circle cx="118" cy="98" r="8" fill="#f87171" :opacity="state === 'happy' || state === 'excited' ? '0.9' : '0.5'"/>

                {{-- Mắt thích ứng theo State --}}
                <g>
                    {{-- Happy: mắt cười tít trăng khuyết --}}
                    <template x-if="state === 'happy' || state === 'excited'">
                        <g>
                            <path d="M 64,80 Q 72,72 80,80" fill="none" stroke="#78350f" stroke-width="3.5" stroke-linecap="round"/>
                            <path d="M 100,80 Q 108,72 116,80" fill="none" stroke="#78350f" stroke-width="3.5" stroke-linecap="round"/>
                        </g>
                    </template>
                    {{-- Sleeping: mắt nhắm phẳng --}}
                    <template x-if="state === 'sleeping'">
                        <g>
                            <path d="M 64,82 L 80,82" stroke="#78350f" stroke-width="3.5" stroke-linecap="round"/>
                            <path d="M 100,82 L 116,82" stroke="#78350f" stroke-width="3.5" stroke-linecap="round"/>
                        </g>
                    </template>
                    {{-- Hungry: mắt buồn long lanh --}}
                    <template x-if="state === 'hungry'">
                        <g>
                            <circle cx="72" cy="80" r="9" fill="#262626"/>
                            <circle cx="108" cy="80" r="9" fill="#262626"/>
                            <circle cx="69" cy="77" r="4" fill="#ffffff"/>
                            <circle cx="105" cy="77" r="4" fill="#ffffff"/>
                            <circle cx="75" cy="84" r="2" fill="#93c5fd"/>
                            <circle cx="111" cy="84" r="2" fill="#93c5fd"/>
                        </g>
                    </template>
                    {{-- Idle & Speaking: mắt to tròn sáng --}}
                    <template x-if="state !== 'happy' && state !== 'excited' && state !== 'sleeping' && state !== 'hungry'">
                        <g>
                            <circle cx="72" cy="80" r="8.5" fill="#262626"/>
                            <circle cx="108" cy="80" r="8.5" fill="#262626"/>
                            <circle cx="70" cy="78" r="3" fill="#ffffff"/>
                            <circle cx="106" cy="78" r="3" fill="#ffffff"/>
                        </g>
                    </template>
                </g>

                {{-- Mũi & Miệng --}}
                <ellipse cx="90" cy="96" rx="14" ry="9" fill="#fef3c7" stroke="#d97706" stroke-width="1.5"/>
                <template x-if="state === 'speaking'">
                    <ellipse cx="90" cy="98" rx="4" ry="3" fill="#dc2626"/>
                </template>
                <template x-if="state !== 'speaking'">
                    <path d="M 87,96 Q 90,99 93,96" fill="none" stroke="#78350f" stroke-width="2" stroke-linecap="round"/>
                </template>

                {{-- Vỏ trứng vỡ --}}
                <path d="M 38,135 Q 35,185 90,185 Q 145,185 142,135 L 125,145 L 110,132 L 90,148 L 70,132 L 55,145 Z"
                      fill="#fef3c7" stroke="#78350f" stroke-width="3.5"/>
            </svg>
        </template>

        {{-- Stage 2: Rồng bé có cánh --}}
        <template x-if="pet && pet.stage === 2">
            <svg class="w-full h-full drop-shadow-md" viewBox="0 0 200 200">
                <path d="M 125,145 Q 170,155 165,125" fill="none" stroke="#f59e0b" stroke-width="12" stroke-linecap="round"/>
                <circle cx="168" cy="120" r="7" fill="#ef4444"/>
                <g>
                    <path d="M 55,115 Q 15,85 30,125 Q 45,130 60,122 Z" fill="#ef4444" stroke="#991b1b" stroke-width="2.5"/>
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
                
                {{-- Eyes --}}
                <template x-if="state === 'happy' || state === 'excited'">
                    <g>
                        <path d="M 74,80 Q 82,72 90,80" fill="none" stroke="#78350f" stroke-width="3.5" stroke-linecap="round"/>
                        <path d="M 110,80 Q 118,72 126,80" fill="none" stroke="#78350f" stroke-width="3.5" stroke-linecap="round"/>
                    </g>
                </template>
                <template x-if="state === 'sleeping'">
                    <g>
                        <path d="M 74,82 L 90,82" stroke="#78350f" stroke-width="3.5" stroke-linecap="round"/>
                        <path d="M 110,82 L 126,82" stroke="#78350f" stroke-width="3.5" stroke-linecap="round"/>
                    </g>
                </template>
                <template x-if="state !== 'happy' && state !== 'excited' && state !== 'sleeping'">
                    <g>
                        <circle cx="82" cy="82" r="8.5" fill="#262626"/>
                        <circle cx="118" cy="82" r="8.5" fill="#262626"/>
                        <circle cx="79.5" cy="79.5" r="3.2" fill="#ffffff"/>
                        <circle cx="115.5" cy="79.5" r="3.2" fill="#ffffff"/>
                    </g>
                </template>

                <ellipse cx="100" cy="98" rx="16" ry="10" fill="#fef3c7" stroke="#d97706" stroke-width="1.5"/>
                <path d="M 94,98 Q 100,103 106,98" fill="none" stroke="#78350f" stroke-width="2" stroke-linecap="round"/>
            </svg>
        </template>

        {{-- Stage 3: Rồng thiếu niên --}}
        <template x-if="pet && pet.stage === 3">
            <svg class="w-full h-full drop-shadow-md" viewBox="0 0 220 220">
                <path d="M 130,160 Q 195,175 190,120" fill="none" stroke="#ea580c" stroke-width="14" stroke-linecap="round"/>
                <path d="M 185,120 Q 210,100 195,85 Q 180,105 175,115 Z" fill="#ef4444"/>
                <path d="M 60,115 Q 10,65 25,125 Q 45,145 65,128 Z" fill="#dc2626" stroke="#991b1b" stroke-width="3"/>
                <path d="M 160,115 Q 210,65 195,125 Q 175,145 155,128 Z" fill="#dc2626" stroke="#991b1b" stroke-width="3"/>
                <ellipse cx="110" cy="140" rx="46" ry="46" fill="#ea580c" stroke="#9a3412" stroke-width="3.5"/>
                <path d="M 90,115 Q 110,135 110,175 Q 85,160 85,125 Z" fill="#fef3c7" opacity="0.9"/>
                <path d="M 80,60 Q 55,20 85,38 Z" fill="#991b1b" stroke="#7f1d1d" stroke-width="2.5"/>
                <path d="M 140,60 Q 165,20 135,38 Z" fill="#991b1b" stroke="#7f1d1d" stroke-width="2.5"/>
                <circle cx="110" cy="88" r="42" fill="#ea580c" stroke="#9a3412" stroke-width="3.5"/>
                <circle cx="92" cy="86" r="8" fill="#262626"/>
                <circle cx="128" cy="86" r="8" fill="#262626"/>
                <circle cx="90" cy="84" r="3" fill="#ffffff"/>
                <circle cx="126" cy="84" r="3" fill="#ffffff"/>
                <ellipse cx="110" cy="100" rx="17" ry="11" fill="#fef3c7" stroke="#c2410c" stroke-width="1.5"/>
            </svg>
        </template>

        {{-- Stage 4: Rồng trưởng thành --}}
        <template x-if="pet && pet.stage === 4">
            <svg class="w-full h-full drop-shadow-md" viewBox="0 0 240 240">
                <path d="M 65,115 Q -10,40 15,135 Q 45,160 70,135 Z" fill="#b91c1c" stroke="#7f1d1d" stroke-width="3.5"/>
                <path d="M 175,115 Q 250,40 225,135 Q 195,160 170,135 Z" fill="#b91c1c" stroke="#7f1d1d" stroke-width="3.5"/>
                <path d="M 140,175 Q 220,195 210,125" fill="none" stroke="#dc2626" stroke-width="18" stroke-linecap="round"/>
                <ellipse cx="120" cy="150" rx="52" ry="50" fill="#dc2626" stroke="#991b1b" stroke-width="4"/>
                <path d="M 98,125 L 142,125 L 135,180 L 105,180 Z" fill="#fbbf24" stroke="#d97706" stroke-width="2.5"/>
                <path d="M 85,65 Q 45,5 92,30 Q 82,45 88,60 Z" fill="#f59e0b" stroke="#b45309" stroke-width="2.5"/>
                <path d="M 155,65 Q 195,5 148,30 Q 158,45 152,60 Z" fill="#f59e0b" stroke="#b45309" stroke-width="2.5"/>
                <path d="M 75,70 Q 120,40 165,70 Q 175,115 120,120 Q 65,115 75,70 Z" fill="#dc2626" stroke="#991b1b" stroke-width="4"/>
                <circle cx="98" cy="88" r="7" fill="#262626"/>
                <circle cx="142" cy="88" r="7" fill="#262626"/>
                <circle cx="96" cy="86" r="2.5" fill="#ffffff"/>
                <circle cx="140" cy="86" r="2.5" fill="#ffffff"/>
            </svg>
        </template>

        {{-- Stage 5: Thần Long Hoàng Kim --}}
        <template x-if="pet && pet.stage >= 5">
            <svg class="w-full h-full drop-shadow-lg" viewBox="0 0 260 260">
                <path d="M 40,200 Q 70,180 90,205 Q 120,185 150,210 Q 180,190 220,215" fill="none" stroke="#60a5fa" stroke-width="4" opacity="0.6" stroke-linecap="round"/>
                <path d="M 50,170 Q 110,230 190,160 Q 230,100 170,75 Q 110,65 95,115 Q 85,155 145,165"
                      fill="none" stroke="#f59e0b" stroke-width="26" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M 50,170 Q 110,230 190,160 Q 230,100 170,75 Q 110,65 95,115 Q 85,155 145,165"
                      fill="none" stroke="#fef08a" stroke-width="12" stroke-linecap="round"/>
                <path d="M 105,75 Q 75,30 95,40 Q 80,15 105,32 Q 110,50 115,70 Z" fill="#38bdf8" stroke="#0284c7" stroke-width="2"/>
                <path d="M 155,75 Q 185,30 165,40 Q 180,15 155,32 Q 150,50 145,70 Z" fill="#38bdf8" stroke="#0284c7" stroke-width="2"/>
                <ellipse cx="130" cy="95" rx="38" ry="32" fill="#f59e0b" stroke="#b45309" stroke-width="3.5"/>
                <circle cx="112" cy="92" r="7" fill="#262626"/>
                <circle cx="148" cy="92" r="7" fill="#262626"/>
                <circle cx="110" cy="90" r="2.5" fill="#ffffff"/>
                <circle cx="146" cy="90" r="2.5" fill="#ffffff"/>
                <circle cx="130" cy="165" r="16" fill="#38bdf8" stroke="#e0f2fe" stroke-width="3"/>
            </svg>
        </template>
    </div>

    {{-- 3D Soft Shadow on the ground --}}
    <div class="w-12 sm:w-14 h-2 sm:h-2.5 mx-auto rounded-full bg-slate-900/20 filter blur-[2px] transition-all duration-300"
         :class="{
             'scale-75 opacity-40 translate-y-1': state === 'happy' || state === 'excited',
             'scale-100 opacity-80': state !== 'happy' && state !== 'excited'
         }">
    </div>
</div>
