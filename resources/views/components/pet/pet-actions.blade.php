<div class="space-y-2 pt-2 border-t border-amber-100">
    {{-- Quick Feed Buttons (Server-Authoritative) --}}
    <div x-show="pet && pet.is_active" x-cloak>
        <div class="flex items-center justify-between text-[10px] font-bold text-slate-600 mb-1.5">
            <span class="flex items-center gap-1">
                <i data-lucide="utensils" class="h-3 w-3 text-amber-600"></i>
                Cho ăn hôm nay:
            </span>
            <span>Còn <strong class="text-amber-700" x-text="pet?.daily_remaining ?? 0"></strong>/100 XP</span>
        </div>

        <div class="grid grid-cols-2 gap-1.5">
            <button type="button" @click="feed(5)"
                    :disabled="feeding || !pet || (pet?.daily_remaining ?? 0) < 5"
                    title="Mời Pet ăn Táo tươi (苹果 píngguǒ)"
                    class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-amber-500 hover:bg-amber-600 py-1.5 px-2 text-[11px] font-bold text-white transition active:scale-95 disabled:opacity-40 disabled:cursor-not-allowed shadow-sm">
                <span class="text-sm">🍎</span>
                <span>苹果 (+5)</span>
            </button>
            <button type="button" @click="feed(10)"
                    :disabled="feeding || !pet || (pet?.daily_remaining ?? 0) < 10"
                    title="Mời Pet ăn Há cảo nóng (饺子 jiǎozi)"
                    class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 py-1.5 px-2 text-[11px] font-bold text-white transition active:scale-95 disabled:opacity-40 disabled:cursor-not-allowed shadow-sm">
                <span class="text-sm">🥟</span>
                <span>饺子 (+10)</span>
            </button>
        </div>

        <p x-show="feedNotice" x-text="feedNotice" class="text-center text-[10px] font-bold text-emerald-600 mt-1"></p>
    </div>

    {{-- Bottom Utility Bar --}}
    <div class="flex items-center justify-between pt-1 text-[11px]">
        <button type="button" @click="nextDialogue()" title="Nghe câu nói khác"
                class="inline-flex items-center gap-1 text-slate-500 hover:text-amber-700 font-medium transition py-0.5">
            <i data-lucide="refresh-cw" class="h-3 w-3"></i>
            <span>Đổi câu</span>
        </button>

        <a href="{{ route('pet.index') }}"
           class="inline-flex items-center gap-1 font-bold text-amber-800 hover:text-amber-950 transition py-0.5">
            <i data-lucide="sparkles" class="h-3 w-3 text-amber-600"></i>
            <span>Vào phòng Pet</span>
            <i data-lucide="arrow-right" class="h-3 w-3"></i>
        </a>
    </div>
</div>
