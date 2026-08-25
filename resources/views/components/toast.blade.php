@if (session()->has('success') || session()->has('error'))
    @php
        $isSuccess = session()->has('success');
        $message = $isSuccess ? session('success') : session('error');
        $bgColor = $isSuccess ? 'bg-green-50' : 'bg-red-50';
        $textColor = $isSuccess ? 'text-green-600' : 'text-red-600';
        $borderColor = $isSuccess ? 'border-green-200' : 'border-red-200';
        $icon = $isSuccess ? 'check-circle' : 'alert-circle';
    @endphp

    <div id="custom-toast" class="fixed bottom-5 right-5 z-[9999] flex items-center gap-3 rounded-lg border {{ $borderColor }} {{ $bgColor }} px-4 py-3 shadow-2xl transition-all duration-500 transform translate-y-0 opacity-100 min-w-[300px]">
        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-white flex items-center justify-center shadow-sm">
            <i data-lucide="{{ $icon }}" class="w-5 h-5 {{ $textColor }}"></i>
        </div>
        <div class="flex-1">
            <h3 class="text-sm font-bold text-gray-900">{{ $isSuccess ? 'Thành công' : 'Có lỗi xảy ra' }}</h3>
            <p class="text-xs font-medium text-gray-700 mt-0.5">{{ $message }}</p>
        </div>
        <button onclick="document.getElementById('custom-toast').style.display='none'" class="text-gray-400 hover:text-gray-600 ml-4 focus:outline-none">
            <i data-lucide="x" class="w-4 h-4"></i>
        </button>
    </div>

    <script>
        setTimeout(() => {
            const toast = document.getElementById('custom-toast');
            if (toast) {
                toast.style.opacity = '0';
                toast.style.transform = 'translateY(20px)';
                setTimeout(() => toast.remove(), 500);
            }
        }, 4000);
    </script>
@endif