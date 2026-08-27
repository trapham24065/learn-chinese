<x-guest-layout>
    @section('title', 'Đăng nhập tài khoản | Learn Chinese')

    <div>
        {{-- Header --}}
        <div class="mb-6">
            <div class="inline-flex items-center gap-1.5 rounded-full bg-red-50 px-3 py-1 text-[11px] font-bold text-[#991b1b] border border-red-100">
                <i data-lucide="sparkles" class="h-3 w-3"></i>
                Học viên đăng nhập
            </div>
            <h1 class="mt-2.5 text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                Chào mừng trở lại!
            </h1>
            <p class="mt-1 text-xs sm:text-sm text-slate-500">
                Đăng nhập để tiếp tục lộ trình học và duy trì chuỗi ngày học của bạn.
            </p>
        </div>

        {{-- Session Status --}}
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="space-y-4" x-data="{ showPassword: false }">
            @csrf

            {{-- Email Address --}}
            <div>
                <label for="email" class="block text-xs font-bold uppercase tracking-[0.12em] text-slate-600 mb-1.5">
                    Địa chỉ Email
                </label>
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                        <i data-lucide="mail" class="h-4 w-4"></i>
                    </div>
                    <input id="email" 
                           type="email" 
                           name="email" 
                           value="{{ old('email') }}" 
                           required 
                           autofocus 
                           autocomplete="username" 
                           placeholder="tenban@email.com"
                           class="w-full rounded-2xl border border-slate-200 bg-slate-50/80 pl-10 pr-4 py-3 text-sm font-medium text-slate-900 placeholder:text-slate-400 transition focus:border-[#991b1b] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#991b1b]/10 @error('email') border-red-500 bg-red-50/30 @enderror">
                </div>
                <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
            </div>

            {{-- Password --}}
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="password" class="block text-xs font-bold uppercase tracking-[0.12em] text-slate-600">
                        Mật khẩu
                    </label>
                    @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-xs font-semibold text-[#991b1b] hover:text-[#7f1d1d] hover:underline transition">
                        Quên mật khẩu?
                    </a>
                    @endif
                </div>
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

            {{-- Remember Me --}}
            <div class="flex items-center justify-between pt-1">
                <label for="remember_me" class="inline-flex items-center cursor-pointer select-none">
                    <input id="remember_me" 
                           type="checkbox" 
                           name="remember" 
                           class="h-4 w-4 rounded-lg border-slate-300 text-[#991b1b] accent-[#991b1b] focus:ring-[#991b1b]/20">
                    <span class="ms-2 text-xs font-semibold text-slate-600">Ghi nhớ đăng nhập</span>
                </label>
            </div>

            {{-- Submit Button --}}
            <div class="pt-2">
                <button type="submit" 
                        class="w-full inline-flex items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-[#991b1b] to-[#b91c1c] px-6 py-3.5 text-sm font-bold text-white shadow-lg shadow-red-950/20 transition hover:from-[#7f1d1d] hover:to-[#991b1b] hover:shadow-xl hover:-translate-y-0.5 active:translate-y-0 active:scale-[0.99]">
                    <span>Đăng nhập ngay</span>
                    <i data-lucide="arrow-right" class="h-4 w-4"></i>
                </button>
            </div>

            {{-- Switch to Register --}}
            <div class="pt-5 border-t border-slate-100 text-center">
                <p class="text-xs text-slate-500">
                    Chưa có tài khoản học viên?
                    <a href="{{ route('register') }}" class="font-bold text-[#991b1b] hover:text-[#7f1d1d] hover:underline ms-1 inline-flex items-center gap-1">
                        Đăng ký miễn phí <i data-lucide="arrow-right" class="h-3 w-3 inline"></i>
                    </a>
                </p>
            </div>
        </form>
    </div>
</x-guest-layout>