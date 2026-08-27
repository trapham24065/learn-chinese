<x-guest-layout>
    @section('title', 'Xác nhận mật khẩu | Learn Chinese')

    <div>
        {{-- Header --}}
        <div class="mb-6">
            <div class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-3 py-1 text-[11px] font-bold text-amber-800 border border-amber-200">
                <i data-lucide="shield-alert" class="h-3 w-3"></i>
                Khu vực bảo mật
            </div>
            <h1 class="mt-2.5 text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                Xác nhận mật khẩu
            </h1>
            <p class="mt-1 text-xs sm:text-sm text-slate-500">
                Đây là khu vực bảo mật. Vui lòng xác nhận mật khẩu của bạn trước khi tiếp tục.
            </p>
        </div>

        <form method="POST" action="{{ route('password.confirm') }}" class="space-y-4" x-data="{ showPassword: false }">
            @csrf

            <!-- Password -->
            <div>
                <label for="password" class="block text-xs font-bold uppercase tracking-[0.12em] text-slate-600 mb-1.5">
                    Mật khẩu hiện tại
                </label>
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                        <i data-lucide="lock" class="h-4 w-4"></i>
                    </div>
                    <input id="password" 
                           :type="showPassword ? 'text' : 'password'" 
                           name="password" 
                           required 
                           autocomplete="current-password" 
                           placeholder="••••••••"
                           class="w-full rounded-2xl border border-slate-200 bg-slate-50/80 pl-10 pr-11 py-3 text-sm font-medium text-slate-900 placeholder:text-slate-400 transition focus:border-[#991b1b] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#991b1b]/10 @error('password') border-red-500 bg-red-50/30 @enderror">
                    <button type="button" 
                            @click="showPassword = !showPassword" 
                            class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400 hover:text-slate-600 focus:outline-none"
                            title="Hiện/Ẩn mật khẩu">
                        <i x-show="!showPassword" data-lucide="eye" class="h-4 w-4"></i>
                        <i x-show="showPassword" data-lucide="eye-off" class="h-4 w-4" style="display: none;"></i>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
            </div>

            <div class="pt-2">
                <button type="submit" 
                        class="w-full inline-flex items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-[#991b1b] to-[#b91c1c] px-6 py-3.5 text-sm font-bold text-white shadow-lg shadow-red-950/20 transition hover:from-[#7f1d1d] hover:to-[#991b1b] hover:shadow-xl hover:-translate-y-0.5 active:translate-y-0 active:scale-[0.99]">
                    <i data-lucide="check" class="h-4 w-4"></i>
                    <span>Xác nhận mật khẩu</span>
                </button>
            </div>
        </form>
    </div>
</x-guest-layout>

