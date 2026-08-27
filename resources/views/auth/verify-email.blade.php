<x-guest-layout>
    @section('title', 'Xác thực Email | Learn Chinese')

    <div>
        {{-- Header --}}
        <div class="mb-6">
            <div class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-3 py-1 text-[11px] font-bold text-amber-800 border border-amber-200">
                <i data-lucide="mail-check" class="h-3 w-3"></i>
                Xác thực tài khoản
            </div>
            <h1 class="mt-2.5 text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                Xác nhận địa chỉ Email
            </h1>
            <p class="mt-2 text-xs sm:text-sm text-slate-500 leading-relaxed">
                Cảm ơn bạn đã đăng ký! Trước khi bắt đầu, hãy kiểm tra hộp thư và bấm vào đường dẫn xác thực mà chúng tôi vừa gửi đến email của bạn nhé.
            </p>
        </div>

        @if (session('status') == 'verification-link-sent')
            <div class="mb-5 rounded-2xl bg-emerald-50 border border-emerald-200 p-4 text-xs font-semibold text-emerald-800 flex items-center gap-2.5">
                <i data-lucide="circle-check" class="h-4 w-4 text-emerald-600 shrink-0"></i>
                <span>Một liên kết xác thực mới đã được gửi đến email bạn đã đăng ký!</span>
            </div>
        @endif

        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-2">
            <form method="POST" action="{{ route('verification.send') }}" class="w-full sm:w-auto">
                @csrf
                <button type="submit" 
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-2xl bg-[#991b1b] hover:bg-[#7f1d1d] px-5 py-3 text-xs font-bold text-white shadow-md shadow-red-950/15 transition hover:-translate-y-0.5">
                    <i data-lucide="refresh-cw" class="h-3.5 w-3.5"></i>
                    <span>Gửi lại email xác thực</span>
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}" class="w-full sm:w-auto text-center sm:text-right">
                @csrf
                <button type="submit" class="text-xs font-semibold text-slate-500 hover:text-red-600 transition underline">
                    Đăng xuất
                </button>
            </form>
        </div>
    </div>
</x-guest-layout>

