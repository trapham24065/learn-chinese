@if (session()->has('success') || session()->has('error'))
    <script>
        alert("Thông báo: {{ session('success') ?? session('error') }}");
    </script>
    <div x-data="{ show: true }" 
         x-show="show" 
         x-init="setTimeout(() => show = false, 3000)"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
         class="fixed bottom-5 right-5 z-50 rounded-xl px-5 py-4 shadow-xl border flex items-center gap-3 min-w-[280px]
         {{ session('success') ? 'bg-white border-green-100' : 'bg-white border-red-100' }}">
         
        @if (session('success'))
            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-green-50 flex items-center justify-center">
                <i data-lucide="check-circle-2" class="w-5 h-5 text-green-500"></i>
            </div>
            <div class="flex-1">
                <h3 class="text-sm font-bold text-gray-900">Thành công</h3>
                <p class="text-xs font-medium text-gray-500 mt-0.5">{{ session('success') }}</p>
            </div>
        @elseif (session('error'))
            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-red-50 flex items-center justify-center">
                <i data-lucide="alert-circle" class="w-5 h-5 text-red-500"></i>
            </div>
            <div class="flex-1">
                <h3 class="text-sm font-bold text-gray-900">Có lỗi xảy ra</h3>
                <p class="text-xs font-medium text-gray-500 mt-0.5">{{ session('error') }}</p>
            </div>
        @endif

        <button @click="show = false" class="text-gray-400 hover:text-gray-600 transition-colors ml-4 focus:outline-none">
            <i data-lucide="x" class="w-4 h-4"></i>
        </button>
    </div>
@endif
