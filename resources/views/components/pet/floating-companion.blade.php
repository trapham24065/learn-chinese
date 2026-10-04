@auth
@if(!request()->routeIs('pet.*'))
@php
    $pageContext = match(true) {
        request()->routeIs('dashboard') => 'dashboard',
        request()->routeIs('flashcards.*') => 'flashcard',
        request()->routeIs('quiz.*') || request()->routeIs('hsk.mock-exam.*') => 'quiz',
        request()->routeIs('lessons.*') || request()->routeIs('hsk.show') => 'lesson',
        request()->routeIs('pet.*') => 'pet_room',
        default => 'other',
    };
@endphp
<div x-data="petFloatingCompanion({
    statusUrl: '{{ route('pet.status') }}',
    feedUrl: '{{ route('pet.feed') }}',
    interactUrl: '{{ route('pet.interact') }}',
    miniQuizRewardUrl: '{{ route('pet.mini-quiz-reward') }}',
    pageContext: '{{ $pageContext }}'
})" x-init="initCompanion()" x-cloak
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

        {{-- 1. Subtle Hover Hint --}}
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
        <div x-ref="petWrapper"
             tabindex="0"
             class="relative cursor-pointer transition-transform duration-150 active:scale-95 focus:outline-none"
             @mouseenter="isHovered = true; onHoverPet()"
             @mouseleave="isHovered = false; hoverHint = false; endPress()"
             @focus="isHovered = true"
             @blur="isHovered = false"
             @mousedown="startPress()"
             @mouseup="endPress()"
             @touchstart.passive="startPress()"
             @touchend="endPress()"
             @touchcancel="endPress()"
             @click="handleMascotClick()"
             title="Chạm để tương tác và trò chuyện cùng Pet (Giữ 1.5s để ôm)">

            {{-- Living SVG Avatar --}}
            <x-pet.pet-avatar />

            {{-- Thought / Speech Toggle Button on Hover --}}
            <button type="button" @click.stop="toggleSpeechBubble()"
                    title="Trò chuyện cùng Pet"
                    class="opacity-0 group-hover:opacity-100 transition-opacity duration-200 absolute -top-1 -left-1 grid h-5 w-5 place-items-center rounded-full bg-amber-500/90 text-white hover:bg-amber-600 text-[10px] shadow-sm z-30">
                <i data-lucide="message-circle" class="h-3 w-3"></i>
            </button>

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
// Global helper for triggering pet events from anywhere
if (typeof window.triggerPetEvent === 'undefined') {
    window.triggerPetEvent = function(type, detail = {}) {
        if (window.PetEventBus) {
            window.PetEventBus.emit(type.startsWith('pet:') ? type : `pet:${type}`, detail);
        } else {
            window.dispatchEvent(new CustomEvent('pet-event', { detail: { type, ...detail } }));
        }
    };
}
</script>
@endif
@endauth
