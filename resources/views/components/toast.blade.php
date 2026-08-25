@if (session()->has('success') || session()->has('error'))
    @php
         = session()->has('success');
         =  ? session('success') : session('error');
         =  ? 'bg-green-50' : 'bg-red-50';
         =  ? 'text-green-600' : 'text-red-600';
         =  ? 'border-green-200' : 'border-red-200';
         =  ? 'check-circle' : 'alert-circle';
    @endphp

    <div id="custom-toast" class="fixed bottom-5 right-5 z-[9999] flex items-center gap-3 rounded-lg border {{  }} {{  }} px-4 py-3 shadow-2xl transition-all duration-500 transform translate-y-0 opacity-100 min-w-[300px]">
        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-white flex items-center justify-center shadow-sm">
            <i data-lucide="{{  }}" class="w-5 h-5 {{  }}"></i>
        </div>
        <div class="flex-1">
            <h3 class="text-sm font-bold text-gray-900">{{  ? 'Thành công' : 'Có lỗi xảy ra' }}</h3>
            <p class="text-xs font-medium text-gray-700 mt-0.5">{{  }}</p>
        </div>
        <button onclick="document.getElementById('custom-toast').style.display='none'" class="text-gray-400 hover:text-gray-600 ml-4">
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
