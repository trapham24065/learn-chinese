@extends('layouts.app')

@section('title', 'Khóa Học Tiếng Trung Online Qua Google Meet | Learn Chinese')

@section('content')
<div class="max-w-6xl mx-auto space-y-12">

    {{-- Hero Section --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-950 via-slate-900 to-[#7f1d1d] p-8 sm:p-12 text-white shadow-2xl shadow-slate-950/20 border border-slate-800">
        {{-- Decorative Chinese Watermark --}}
        <div class="pointer-events-none absolute -right-6 -bottom-10 select-none text-[160px] sm:text-[220px] font-black text-white/[0.03] leading-none">
            漢語
        </div>

        <div class="relative z-10 max-w-2xl space-y-5">
            <div class="inline-flex items-center gap-2 rounded-full border border-rose-500/30 bg-rose-500/10 px-3.5 py-1 text-xs font-semibold text-rose-300 backdrop-blur">
                <span class="h-2 w-2 rounded-full bg-rose-400 animate-pulse"></span>
                <span>Lớp Học Trực Tuyến Tương Tác Qua Google Meet</span>
            </div>

            <h1 class="text-3xl sm:text-5xl font-black tracking-tight text-white leading-tight">
                Chinh Phục Tiếng Trung Thực Chiến Cùng Giáo Viên
            </h1>

            <p class="text-base sm:text-lg text-slate-300 leading-relaxed">
                Học trực tiếp cùng giáo viên qua Google Meet. Sĩ số nhỏ chỉ <strong class="text-amber-300">8 - 12 học viên</strong> để được sửa phát âm khẩu hình 1:1, phản xạ tự nhiên và cam kết chuẩn đầu ra.
            </p>

            <div class="pt-2 flex flex-wrap gap-4 text-xs sm:text-sm font-medium text-slate-200">
                <div class="flex items-center gap-2 rounded-xl bg-white/10 px-3 py-2 backdrop-blur">
                    <i data-lucide="users" class="h-4 w-4 text-amber-400"></i>
                    <span>Tối đa 8-12 học viên/lớp</span>
                </div>
                <div class="flex items-center gap-2 rounded-xl bg-white/10 px-3 py-2 backdrop-blur">
                    <i data-lucide="video" class="h-4 w-4 text-rose-400"></i>
                    <span>Tương tác trực tiếp 100%</span>
                </div>
                <div class="flex items-center gap-2 rounded-xl bg-white/10 px-3 py-2 backdrop-blur">
                    <i data-lucide="history" class="h-4 w-4 text-emerald-400"></i>
                    <span>Xem lại video buổi học trọn khóa</span>
                </div>
                <div class="flex items-center gap-2 rounded-xl bg-white/10 px-3 py-2 backdrop-blur">
                    <i data-lucide="message-circle" class="h-4 w-4 text-blue-400"></i>
                    <span>Nhóm Zalo hỗ trợ bài tập 24/7</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Course List Section --}}
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 border-b border-slate-200/80 pb-4">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-[#991b1b]">Danh sách chương trình</p>
                <h2 class="text-2xl sm:text-3xl font-black text-slate-900 mt-1">Các Khóa Học Đang Mở Đăng Ký</h2>
            </div>
            <p class="text-sm text-slate-500">Hỗ trợ xếp lớp phù hợp với lịch làm việc và học tập</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-8">
            @forelse($courses as $c)
            <div class="group flex flex-col justify-between rounded-3xl border border-slate-200 bg-white p-6 sm:p-7 shadow-lg shadow-slate-950/5 hover:border-red-200 hover:shadow-xl hover:shadow-red-950/5 transition-all duration-200">
                
                <div class="space-y-4">
                    {{-- Badge & Sĩ số --}}
                    <div class="flex items-center justify-between gap-2">
                        <span class="rounded-full bg-red-50 border border-red-200 px-3 py-1 text-xs font-bold text-[#991b1b]">
                            {{ $c->category_label }}
                        </span>
                        <div class="flex items-center gap-1.5 text-xs text-slate-500">
                            <i data-lucide="clock" class="h-3.5 w-3.5"></i>
                            <span>{{ $c->duration_weeks }} tuần ({{ $c->total_sessions }} buổi)</span>
                        </div>
                    </div>

                    {{-- Title & Summary --}}
                    <div>
                        <h3 class="text-xl font-bold text-slate-900 group-hover:text-[#991b1b] transition leading-snug">
                            <a href="{{ route('courses.show', $c->slug) }}">
                                {{ $c->title }}
                            </a>
                        </h3>
                        <p class="mt-2 text-sm text-slate-600 line-clamp-3 leading-relaxed">
                            {{ $c->summary }}
                        </p>
                    </div>

                    {{-- Highlights --}}
                    @if(!empty($c->highlights) && is_array($c->highlights))
                    <ul class="space-y-2 pt-1 border-t border-slate-100 text-xs text-slate-700">
                        @foreach(array_slice($c->highlights, 0, 3) as $h)
                        <li class="flex items-start gap-2">
                            <i data-lucide="check-circle-2" class="h-4 w-4 text-emerald-600 shrink-0 mt-0.5"></i>
                            <span>{{ $h }}</span>
                        </li>
                        @endforeach
                    </ul>
                    @endif

                    {{-- Upcoming Classes Box --}}
                    @if($c->openClasses->isNotEmpty())
                    <div class="rounded-2xl bg-amber-50/70 border border-amber-200/80 p-3.5 space-y-1.5">
                        <div class="flex items-center justify-between text-xs font-bold text-amber-900">
                            <span class="flex items-center gap-1.5">
                                <i data-lucide="calendar" class="h-3.5 w-3.5 text-amber-700"></i>
                                Lớp khai giảng gần nhất:
                            </span>
                            <span class="rounded-md bg-amber-200/80 px-1.5 py-0.5 text-[10px] uppercase font-black text-amber-900">
                                Đang tuyển
                            </span>
                        </div>
                        @php $firstClass = $c->openClasses->first(); @endphp
                        <p class="text-xs font-semibold text-slate-800">
                            {{ $firstClass->name }}
                        </p>
                        <p class="text-[11px] text-slate-600">
                            Khai giảng: <strong>{{ $firstClass->start_date ? $firstClass->start_date->format('d/m/Y') : 'Sớm nhất' }}</strong> • {{ $firstClass->schedule_days }} ({{ $firstClass->schedule_time }})
                        </p>
                    </div>
                    @endif
                </div>

                {{-- Price & CTA --}}
                <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between gap-4">
                    <div>
                        <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Học phí trọn gói</p>
                        <div class="flex items-baseline gap-2 mt-0.5">
                            <span class="text-2xl font-black text-[#991b1b]">{{ $c->formatted_price }}</span>
                            @if($c->formatted_original_price)
                            <span class="text-xs text-slate-400 line-through">{{ $c->formatted_original_price }}</span>
                            @endif
                        </div>
                    </div>

                    <a href="{{ route('courses.show', $c->slug) }}"
                       class="inline-flex items-center gap-1.5 rounded-xl bg-slate-950 px-4 py-2.5 text-xs font-bold text-white shadow-md shadow-slate-950/15 hover:bg-[#991b1b] transition">
                        <span>Chi tiết &amp; Đăng ký</span>
                        <i data-lucide="arrow-right" class="h-3.5 w-3.5"></i>
                    </a>
                </div>

            </div>
            @empty
            <div class="col-span-full rounded-3xl border border-dashed border-slate-300 p-12 text-center text-slate-500">
                <i data-lucide="book-open" class="h-10 w-10 mx-auto text-slate-400 mb-3"></i>
                <p class="font-bold text-slate-700">Hiện chưa có khóa học nào được mở công khai.</p>
                <p class="text-xs text-slate-500 mt-1">Vui lòng quay lại sau hoặc liên hệ ban quản trị để được tư vấn lộ trình.</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- FAQs Section --}}
    <div class="rounded-3xl border border-slate-200 bg-white p-7 sm:p-10 shadow-sm space-y-6">
        <div class="text-center max-w-xl mx-auto space-y-2">
            <h2 class="text-2xl sm:text-3xl font-black text-slate-900">Câu Hỏi Thường Gặp Khi Học Online</h2>
            <p class="text-sm text-slate-500">Mọi thông tin bạn cần biết trước khi đăng ký khóa học</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4 text-sm text-slate-700">
            <div class="rounded-2xl bg-slate-50 p-5 border border-slate-100 space-y-2">
                <h3 class="font-bold text-slate-900 flex items-center gap-2">
                    <i data-lucide="help-circle" class="h-4 w-4 text-[#991b1b] shrink-0"></i>
                    Học qua Google Meet có hiệu quả như học offline không?
                </h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Lớp giới hạn chỉ 8 - 12 học viên. Mọi học viên đều bật camera và mic để giáo viên trực tiếp sửa khẩu hình, uốn nắn từng thanh điệu. Thời lượng tương tác nói chiếm đến 60% buổi học.
                </p>
            </div>

            <div class="rounded-2xl bg-slate-50 p-5 border border-slate-100 space-y-2">
                <h3 class="font-bold text-slate-900 flex items-center gap-2">
                    <i data-lucide="video" class="h-4 w-4 text-[#991b1b] shrink-0"></i>
                    Nếu có buổi bận việc đột xuất thì làm thế nào?
                </h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Mỗi buổi học đều có bản ghi video chất lượng cao được lưu trữ để học viên xem lại trong suốt khóa học và thời gian hỗ trợ. Giáo viên vẫn giải đáp bài tập bạn nộp qua nhóm Zalo.
                </p>
            </div>

            <div class="rounded-2xl bg-slate-50 p-5 border border-slate-100 space-y-2">
                <h3 class="font-bold text-slate-900 flex items-center gap-2">
                    <i data-lucide="credit-card" class="h-4 w-4 text-[#991b1b] shrink-0"></i>
                    Hình thức thanh toán học phí ra sao?
                </h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Sau khi điền form đăng ký, bạn sẽ nhận được mã thanh toán VietQR tự động kèm mã ghi chú. Bạn có thể chuyển cọc giữ chỗ hoặc thanh toán toàn khóa để nhận ưu đãi giáo trình.
                </p>
            </div>

            <div class="rounded-2xl bg-slate-50 p-5 border border-slate-100 space-y-2">
                <h3 class="font-bold text-slate-900 flex items-center gap-2">
                    <i data-lucide="shield-check" class="h-4 w-4 text-[#991b1b] shrink-0"></i>
                    Cam kết chuẩn đầu ra như thế nào?
                </h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Học viên tham gia tối thiểu 80% số buổi và hoàn thành các bài tập sẽ được cam kết thi đỗ chứng chỉ HSK tương ứng hoặc được học lại lớp sau hoàn toàn miễn phí.
                </p>
            </div>
        </div>
    </div>

    {{-- Contact Banner --}}
    <div class="rounded-3xl bg-slate-100 border border-slate-200 p-6 sm:p-8 flex flex-col sm:flex-row items-center justify-between gap-6">
        <div class="space-y-1 text-center sm:text-left">
            <h3 class="text-lg font-bold text-slate-900">Chưa biết nên chọn khóa học nào phù hợp với bản thân?</h3>
            <p class="text-xs text-slate-600">Đừng ngần ngại liên hệ trực tiếp với giáo viên để được kiểm tra trình độ và xếp lớp miễn phí.</p>
        </div>
        <div class="flex items-center gap-3 shrink-0">
            @php 
                $consultPhone = function_exists('setting') ? setting('course_consult_phone', '0988888888') : '0988888888'; 
                $consultZalo = function_exists('setting') ? setting('course_consult_zalo', '0988888888') : '0988888888'; 
            @endphp
            <a href="tel:{{ $consultPhone }}"
               class="inline-flex items-center gap-2 rounded-xl bg-white border border-slate-200 px-4 py-2.5 text-xs font-bold text-slate-800 hover:bg-slate-50 transition shadow-sm">
                <i data-lucide="phone" class="h-3.5 w-3.5 text-blue-600"></i>
                <span>Hotline: {{ $consultPhone }}</span>
            </a>
            <a href="https://zalo.me/{{ $consultZalo }}" target="_blank" rel="noopener noreferrer"
               class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-4 py-2.5 text-xs font-bold text-white hover:bg-emerald-700 transition shadow-md shadow-emerald-950/15">
                <i data-lucide="message-square" class="h-3.5 w-3.5"></i>
                <span>Chat Zalo ngay</span>
            </a>
        </div>
    </div>

</div>
@endsection
