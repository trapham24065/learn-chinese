<footer class="mt-16 border-t border-slate-200/80 bg-white/70 backdrop-blur-md">
    <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-10 lg:py-16">
        <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 lg:gap-10">
            
            {{-- Brand Column (2 cols on lg, full on mobile) --}}
            <div class="space-y-4 sm:col-span-2 md:col-span-3 lg:col-span-2">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-3">
                    <div class="grid h-10 w-10 place-items-center rounded-2xl bg-[#991b1b] text-white shadow-md shadow-red-950/20">
                        <span class="text-lg font-black">中</span>
                    </div>
                    <div>
                        <span class="text-base font-black tracking-tight text-slate-950">Learn Chinese</span>
                        <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-[#991b1b]">Nền tảng học tiếng Trung</p>
                    </div>
                </a>
                
                <p class="text-sm leading-relaxed text-slate-600 max-w-sm">
                    Website học tiếng Trung hiện đại với phương pháp ôn tập Flashcard 3D, luyện phát âm AI, tập viết chữ Hán chuẩn nét và trắc nghiệm tương tác từ HSK 1 đến HSK 6.
                </p>

                <div class="flex flex-wrap items-center gap-2 pt-2">
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-red-50 px-3 py-1 text-xs font-bold text-[#991b1b] border border-red-100">
                        <i data-lucide="sparkles" class="h-3 w-3"></i> 5.000+ từ vựng HSK
                    </span>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-3 py-1 text-xs font-bold text-amber-800 border border-amber-100">
                        <i data-lucide="volume-2" class="h-3 w-3"></i> Giọng đọc AI Azure
                    </span>
                </div>
            </div>

            {{-- Column 1: Lộ trình --}}
            <div class="space-y-3">
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-slate-900">Lộ trình học</p>
                <ul class="space-y-2 text-sm text-slate-600">
                    <li>
                        <a href="{{ route('hsk.overview') }}" class="hover:text-[#991b1b] transition inline-flex items-center gap-1.5">
                            <i data-lucide="graduation-cap" class="h-3.5 w-3.5 text-slate-400"></i> Tổng quan HSK 1 - 6
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('hsk.show', 1) }}" class="hover:text-[#991b1b] transition inline-flex items-center gap-1.5">
                            <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span> HSK 1 - Sơ cấp (150 từ)
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('hsk.show', 2) }}" class="hover:text-[#991b1b] transition inline-flex items-center gap-1.5">
                            <span class="h-1.5 w-1.5 rounded-full bg-blue-500"></span> HSK 2 - Sơ cấp cao (300 từ)
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('hsk.show', 3) }}" class="hover:text-[#991b1b] transition inline-flex items-center gap-1.5">
                            <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span> HSK 3 - Trung cấp thấp
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('hsk.show', 4) }}" class="hover:text-[#991b1b] transition inline-flex items-center gap-1.5">
                            <span class="h-1.5 w-1.5 rounded-full bg-orange-500"></span> HSK 4 - Trung cấp
                        </a>
                    </li>
                </ul>
            </div>

            {{-- Column 2: Công cụ học tập --}}
            <div class="space-y-3">
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-slate-900">Công cụ học tập</p>
                <ul class="space-y-2 text-sm text-slate-600">
                    <li>
                        <a href="{{ route('flashcards') }}" class="hover:text-[#991b1b] transition inline-flex items-center gap-1.5">
                            <i data-lucide="layers" class="h-3.5 w-3.5 text-slate-400"></i> Thẻ Flashcard 3D
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('quiz') }}" class="hover:text-[#991b1b] transition inline-flex items-center gap-1.5">
                            <i data-lucide="target" class="h-3.5 w-3.5 text-slate-400"></i> Trắc nghiệm kiểm tra
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard') }}" class="hover:text-[#991b1b] transition inline-flex items-center gap-1.5">
                            <i data-lucide="flame" class="h-3.5 w-3.5 text-amber-500"></i> Theo dõi chuỗi Streak
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard') }}" class="hover:text-[#991b1b] transition inline-flex items-center gap-1.5">
                            <i data-lucide="bar-chart-3" class="h-3.5 w-3.5 text-slate-400"></i> Thống kê kết quả
                        </a>
                    </li>
                </ul>
            </div>

            {{-- Column 3: Hỗ trợ & Tài khoản --}}
            <div class="space-y-3">
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-slate-900">Học viên</p>
                <ul class="space-y-2 text-sm text-slate-600">
                    @auth
                        <li>
                            <a href="{{ route('dashboard') }}" class="hover:text-[#991b1b] transition inline-flex items-center gap-1.5">
                                <i data-lucide="user" class="h-3.5 w-3.5 text-slate-400"></i> Trang cá nhân
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('profile.edit') }}" class="hover:text-[#991b1b] transition inline-flex items-center gap-1.5">
                                <i data-lucide="settings" class="h-3.5 w-3.5 text-slate-400"></i> Cài đặt tài khoản
                            </a>
                        </li>
                    @else
                        <li>
                            <a href="{{ route('login') }}" class="hover:text-[#991b1b] transition inline-flex items-center gap-1.5">
                                <i data-lucide="log-in" class="h-3.5 w-3.5 text-slate-400"></i> Đăng nhập học viên
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('register') }}" class="hover:text-[#991b1b] transition inline-flex items-center gap-1.5">
                                <i data-lucide="user-plus" class="h-3.5 w-3.5 text-[#991b1b]"></i> Đăng ký tài khoản mới
                            </a>
                        </li>
                    @endauth
                    <li>
                        <a href="{{ route('home') }}#features" class="hover:text-[#991b1b] transition inline-flex items-center gap-1.5">
                            <i data-lucide="help-circle" class="h-3.5 w-3.5 text-slate-400"></i> Giới thiệu phương pháp
                        </a>
                    </li>
                </ul>
            </div>

        </div>

        {{-- Bottom Copyright Bar --}}
        <div class="mt-12 flex flex-col items-center justify-between gap-4 border-t border-slate-200/60 pt-8 sm:flex-row text-xs text-slate-500">
            <p>© {{ date('Y') }} Learn Chinese (Chinese Deck). Tất cả quyền được bảo lưu.</p>
            <div class="flex items-center gap-4">
                <span>Học mỗi ngày • Nhớ lâu hơn</span>
                <span>•</span>
                <span class="text-slate-600 font-medium">Phiên bản 2.0</span>
            </div>
        </div>
    </div>
</footer>
