<x-guest-layout>
    @section('title', 'Đăng ký tài khoản học viên | Learn Chinese')

    <div>
        {{-- Header --}}
        <div class="mb-6">
            <div class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-[11px] font-bold text-emerald-700 border border-emerald-100">
                <i data-lucide="sparkles" class="h-3 w-3"></i>
                Khởi đầu học tập
            </div>
            <h1 class="mt-2.5 text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                Tạo tài khoản học viên
            </h1>
            <p class="mt-1 text-xs sm:text-sm text-slate-500">
                Bắt đầu học tiếng Trung bài bản với 5.000+ từ vựng và lộ trình HSK 1 – HSK 6.
            </p>
        </div>

        @if(function_exists('setting_bool') && ! setting_bool('allow_registration', true))
            <div class="rounded-2xl border border-amber-200 bg-amber-50/80 p-6 text-center space-y-4">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-100 text-amber-700">
                    <i data-lucide="lock" class="h-6 w-6"></i>
                </div>
                <div>
                    <h2 class="text-lg font-black text-slate-900">Đăng ký tạm thời đóng</h2>
                    <p class="mt-1.5 text-xs sm:text-sm text-slate-600 max-w-sm mx-auto">
                        Hiện tại hệ thống chưa nhận đăng ký tài khoản mới. Vui lòng quay lại sau hoặc liên hệ quản trị viên.
                    </p>
                </div>
                <div class="flex flex-wrap items-center justify-center gap-2.5 pt-2">
                    <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 rounded-xl bg-slate-900 px-4 py-2 text-xs font-bold text-white transition hover:bg-slate-800">
                        <i data-lucide="arrow-left" class="h-3.5 w-3.5"></i>
                        <span>Về trang chủ</span>
                    </a>
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-bold text-slate-700 transition hover:bg-slate-50">
                        <i data-lucide="log-in" class="h-3.5 w-3.5"></i>
                        <span>Đăng nhập</span>
                    </a>
                </div>
            </div>
        @else
        <form method="POST" action="{{ route('register') }}" class="space-y-3.5" x-data="{ showPassword: false }">
            @csrf

            {{-- Name --}}
            <div>
                <label for="name" class="block text-xs font-bold uppercase tracking-[0.12em] text-slate-600 mb-1.5">
                    Họ và tên của bạn
                </label>
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                        <i data-lucide="user" class="h-4 w-4"></i>
                    </div>
                    <input id="name" 
                           type="text" 
                           name="name" 
                           value="{{ old('name') }}" 
                           required 
                           autofocus 
                           autocomplete="name" 
                           placeholder="Nguyễn Văn A"
                           class="w-full rounded-2xl border border-slate-200 bg-slate-50/80 pl-10 pr-4 py-3 text-sm font-medium text-slate-900 placeholder:text-slate-400 transition focus:border-[#991b1b] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#991b1b]/10 @error('name') border-red-500 bg-red-50/30 @enderror">
                </div>
                <x-input-error :messages="$errors->get('name')" class="mt-1.5" />
            </div>

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
                           autocomplete="username" 
                           placeholder="tenban@email.com"
                           class="w-full rounded-2xl border border-slate-200 bg-slate-50/80 pl-10 pr-4 py-3 text-sm font-medium text-slate-900 placeholder:text-slate-400 transition focus:border-[#991b1b] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#991b1b]/10 @error('email') border-red-500 bg-red-50/30 @enderror">
                </div>
                <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
            </div>

            {{-- Password --}}
            <div>
                <label for="password" class="block text-xs font-bold uppercase tracking-[0.12em] text-slate-600 mb-1.5">
                    Mật khẩu
                </label>
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                        <i data-lucide="lock" class="h-4 w-4"></i>
                    </div>
                    <input id="password" 
                           :type="showPassword ? 'text' : 'password'" 
                           name="password" 
                           required 
                           autocomplete="new-password" 
                           placeholder="Ít nhất 8 ký tự"
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

            {{-- Confirm Password --}}
            <div>
                <label for="password_confirmation" class="block text-xs font-bold uppercase tracking-[0.12em] text-slate-600 mb-1.5">
                    Xác nhận lại mật khẩu
                </label>
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                        <i data-lucide="shield-check" class="h-4 w-4"></i>
                    </div>
                    <input id="password_confirmation" 
                           :type="showPassword ? 'text' : 'password'" 
                           name="password_confirmation" 
                           required 
                           autocomplete="new-password" 
                           placeholder="Nhập lại mật khẩu"
                           class="w-full rounded-2xl border border-slate-200 bg-slate-50/80 pl-10 pr-4 py-3 text-sm font-medium text-slate-900 placeholder:text-slate-400 transition focus:border-[#991b1b] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#991b1b]/10 @error('password_confirmation') border-red-500 bg-red-50/30 @enderror">
                </div>
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1.5" />
            </div>

            {{-- Submit Button --}}
            <div class="pt-2">
                <button type="submit" 
                        class="w-full inline-flex items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-[#991b1b] to-[#b91c1c] px-6 py-3.5 text-sm font-bold text-white shadow-lg shadow-red-950/20 transition hover:from-[#7f1d1d] hover:to-[#991b1b] hover:shadow-xl hover:-translate-y-0.5 active:translate-y-0 active:scale-[0.99]">
                    <span>Đăng ký tài khoản ngay</span>
                    <i data-lucide="arrow-right" class="h-4 w-4"></i>
                </button>
            </div>

            {{-- Switch to Login --}}
            <div class="pt-4 border-t border-slate-100 text-center">
                <p class="text-xs text-slate-500">
                    Đã có tài khoản học viên?
                    <a href="{{ route('login') }}" class="font-bold text-[#991b1b] hover:text-[#7f1d1d] hover:underline ms-1 inline-flex items-center gap-1">
                        Đăng nhập tại đây <i data-lucide="arrow-right" class="h-3 w-3 inline"></i>
                    </a>
                </p>
            </div>
        </form>
        @endif
    </div>
</x-guest-layout>