<x-guest-layout>
    @section('title', 'Quên mật khẩu | Learn Chinese')

    <div>
        {{-- Header --}}
        <div class="mb-6">
            <div class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-3 py-1 text-[11px] font-bold text-amber-800 border border-amber-200">
                <i data-lucide="key-round" class="h-3 w-3"></i>
                Khôi phục tài khoản
            </div>
            <h1 class="mt-2.5 text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                Quên mật khẩu?
            </h1>
            <p class="mt-1 text-xs sm:text-sm text-slate-500 leading-relaxed">
                Đừng lo lắng! Hãy nhập email đã đăng ký của bạn. Chúng tôi sẽ gửi liên kết để bạn thiết lập mật khẩu mới ngay lập tức.
            </p>
        </div>

        {{-- Session Status --}}
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
            @csrf

            {{-- Email Address --}}
            <div>
                <label for="email" class="block text-xs font-bold uppercase tracking-[0.12em] text-slate-600 mb-1.5">
                    Địa chỉ Email của bạn
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
                           placeholder="tenban@email.com"
                           class="w-full rounded-2xl border border-slate-200 bg-slate-50/80 pl-10 pr-4 py-3 text-sm font-medium text-slate-900 placeholder:text-slate-400 transition focus:border-[#991b1b] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#991b1b]/10 @error('email') border-red-500 bg-red-50/30 @enderror">
                </div>
                <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
            </div>

            {{-- Submit Button --}}
            <div class="pt-2">
                <button type="submit" 
                        class="w-full inline-flex items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-[#991b1b] to-[#b91c1c] px-6 py-3.5 text-sm font-bold text-white shadow-lg shadow-red-950/20 transition hover:from-[#7f1d1d] hover:to-[#991b1b] hover:shadow-xl hover:-translate-y-0.5 active:translate-y-0 active:scale-[0.99]">
                    <i data-lucide="send" class="h-4 w-4"></i>
                    <span>Gửi email đặt lại mật khẩu</span>
                </button>
            </div>

            {{-- Back to Login --}}
            <div class="pt-5 border-t border-slate-100 text-center">
                <a href="{{ route('login') }}" class="text-xs font-bold text-slate-600 hover:text-[#991b1b] transition inline-flex items-center gap-1.5">
                    <i data-lucide="arrow-left" class="h-3.5 w-3.5"></i>
                    <span>Quay lại trang Đăng nhập</span>
                </a>
            </div>
        </form>
    </div>
</x-guest-layout>