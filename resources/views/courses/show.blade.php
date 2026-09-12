@extends('layouts.app')

@section('title', $course->title . ' | Khóa Học Online')

@section('content')
<div class="max-w-6xl mx-auto space-y-8">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-xs font-medium text-slate-500">
        <a href="{{ route('home') }}" class="hover:text-slate-900 transition">Trang chủ</a>
        <i data-lucide="chevron-right" class="h-3.5 w-3.5"></i>
        <a href="{{ route('courses.index') }}" class="hover:text-slate-900 transition">Khóa học Online</a>
        <i data-lucide="chevron-right" class="h-3.5 w-3.5"></i>
        <span class="text-slate-800 font-semibold truncate">{{ $course->title }}</span>
    </nav>

    {{-- Alert Messages --}}
    @if(session('info'))
    <div class="rounded-2xl border border-blue-200 bg-blue-50 p-4 text-xs font-semibold text-blue-900 flex items-center gap-2">
        <i data-lucide="info" class="h-4 w-4 text-blue-600 shrink-0"></i>
        <span>{{ session('info') }}</span>
    </div>
    @endif

    {{-- Main Grid: 2 Columns on Desktop --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

        {{-- Left Column: Course Details & Curriculum (7 cols) --}}
        <div class="lg:col-span-7 space-y-8">

            {{-- Course Header Box --}}
            <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-4">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="rounded-full bg-red-50 border border-red-200 px-3 py-1 text-xs font-bold text-[#991b1b]">
                        {{ $course->category_label }}
                    </span>
                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600">
                        <i data-lucide="calendar" class="inline h-3 w-3 mr-1 text-slate-500"></i>
                        {{ $course->duration_weeks }} tuần ({{ $course->total_sessions }} buổi)
                    </span>
                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600">
                        <i data-lucide="users" class="inline h-3 w-3 mr-1 text-slate-500"></i>
                        8 - 12 học viên/lớp
                    </span>
                </div>

                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black text-slate-900 leading-tight">
                    {{ $course->title }}
                </h1>

                <p class="text-slate-600 text-sm sm:text-base leading-relaxed">
                    {{ $course->summary }}
                </p>

                {{-- Highlights list --}}
                @if(!empty($course->highlights) && is_array($course->highlights))
                <div class="pt-4 border-t border-slate-100 space-y-3">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Bạn sẽ nhận được gì từ khóa học này?</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach($course->highlights as $hl)
                        <div class="flex items-start gap-2.5 rounded-2xl bg-slate-50 p-3 border border-slate-100 text-xs font-semibold text-slate-800">
                            <i data-lucide="check-circle" class="h-4 w-4 text-emerald-600 shrink-0 mt-0.5"></i>
                            <span>{{ $hl }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            {{-- Upcoming Classes Schedule --}}
            <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-[#991b1b]">Lịch mở lớp gần nhất</p>
                        <h2 class="text-xl font-bold text-slate-900 mt-0.5">Các Lớp Sắp Khai Giảng</h2>
                    </div>
                    <span class="rounded-full bg-emerald-50 border border-emerald-200 px-2.5 py-1 text-[11px] font-bold text-emerald-700">
                        Học qua Google Meet
                    </span>
                </div>

                @if($course->openClasses->isNotEmpty())
                <div class="space-y-3 pt-2">
                    @foreach($course->openClasses as $cls)
                    <div class="rounded-2xl border border-slate-200 p-4 hover:border-red-200 transition bg-gradient-to-r from-slate-50 to-white flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-slate-900 text-sm">{{ $cls->name }}</span>
                                <span class="rounded-md bg-slate-200/80 px-1.5 py-0.5 text-[10px] font-mono font-bold text-slate-700">{{ $cls->code }}</span>
                            </div>
                            <div class="flex flex-wrap items-center gap-3 text-xs text-slate-600">
                                <span><i data-lucide="calendar" class="inline h-3.5 w-3.5 text-slate-400 mr-1"></i>Khai giảng: <strong>{{ $cls->start_date ? $cls->start_date->format('d/m/Y') : 'Sắp mở' }}</strong></span>
                                <span><i data-lucide="clock" class="inline h-3.5 w-3.5 text-slate-400 mr-1"></i>{{ $cls->schedule_days }} ({{ $cls->schedule_time }})</span>
                            </div>
                            @if($cls->notes)
                            <p class="text-[11px] text-slate-500 italic mt-1">{{ $cls->notes }}</p>
                            @endif
                        </div>
                        <div class="shrink-0 flex sm:flex-col items-center sm:items-end justify-between gap-1">
                            <span class="rounded-full px-2.5 py-0.5 text-xs font-bold {{ $cls->is_full ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800' }}">
                                {{ $cls->status_label }}
                            </span>
                            <span class="text-[11px] text-slate-500">
                                Sĩ số: tối đa {{ $cls->max_students }} bạn
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="rounded-2xl bg-slate-50 p-6 text-center text-xs text-slate-500 border border-slate-100">
                    Lớp mới đang được xếp lịch. Bạn vẫn có thể điền form đăng ký bên cạnh để được giữ chỗ và chọn khung giờ sớm nhất!
                </div>
                @endif
            </div>

            {{-- Curriculum Roadmap --}}
            @if(!empty($course->curriculum) && is_array($course->curriculum))
            <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-4">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-[#991b1b]">Lộ trình đào tạo</p>
                    <h2 class="text-xl font-bold text-slate-900 mt-0.5">Chi Tiết Từng Chặng Học Tập</h2>
                </div>

                <div class="space-y-4 pt-2">
                    @foreach($course->curriculum as $idx => $step)
                    <div class="relative pl-6 border-l-2 border-red-200 space-y-1">
                        <div class="absolute -left-2 top-0 h-4 w-4 rounded-full bg-[#991b1b] border-2 border-white"></div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-[#991b1b]">{{ $step['sessions'] ?? ('Chặng ' . ($idx + 1)) }}</span>
                            <span class="text-slate-300">•</span>
                            <h3 class="text-sm font-bold text-slate-900">{{ $step['phase'] ?? '' }}</h3>
                        </div>
                        <p class="text-xs text-slate-600 leading-relaxed pt-0.5">
                            {{ $step['content'] ?? '' }}
                        </p>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Detailed Description / Why Learn Online --}}
            <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-4 text-slate-700 text-sm leading-relaxed">
                <h2 class="text-xl font-bold text-slate-900">Cam Kết Chất Lượng Khóa Học</h2>
                
                <div class="space-y-3">
                    <p>
                        Mô hình học trực tuyến tại Learn Chinese được thiết kế tối ưu cho học viên bận rộn nhưng vẫn đòi hỏi chất lượng tương đương lớp học trực tiếp:
                    </p>
                    <div class="rounded-2xl bg-amber-50/80 border border-amber-200 p-4 space-y-2 text-xs text-amber-950">
                        <p class="font-bold flex items-center gap-2 text-amber-900">
                            <i data-lucide="video" class="h-4 w-4 text-[#991b1b]"></i>
                            Quyền lợi video xem lại trọn đời khóa học:
                        </p>
                        <p class="leading-relaxed">
                            Được cung cấp bản ghi buổi học để xem lại trong suốt khóa học và thời gian hỗ trợ. Học viên không bao giờ lo lắng bị hổng kiến thức nếu có buổi bận công việc hoặc gia đình.
                        </p>
                    </div>
                    <p>
                        Giáo viên sẽ theo dõi sát sao bài tập nộp hàng tuần qua nhóm Zalo lớp, sửa từng lỗi phát âm và phản hồi chi tiết trước mỗi buổi học tiếp theo.
                    </p>
                </div>
            </div>

        </div>

        {{-- Right Column: Sticky Registration Form (5 cols) --}}
        <div class="lg:col-span-5 lg:sticky lg:top-6 space-y-6">

            <div class="rounded-3xl border-2 border-red-200 bg-white p-6 sm:p-7 shadow-xl shadow-red-950/5 space-y-5">
                
                {{-- Pricing Box --}}
                <div class="flex items-baseline justify-between border-b border-slate-100 pb-4">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Học phí ưu đãi</p>
                        <div class="flex items-baseline gap-2 mt-0.5">
                            <span class="text-3xl font-black text-[#991b1b]">{{ $course->formatted_price }}</span>
                            @if($course->formatted_original_price)
                            <span class="text-xs text-slate-400 line-through">{{ $course->formatted_original_price }}</span>
                            @endif
                        </div>
                    </div>
                    @if($course->discount_percentage)
                    <span class="rounded-full bg-red-100 border border-red-200 px-2.5 py-1 text-xs font-black text-[#991b1b]">
                        -{{ $course->discount_percentage }}%
                    </span>
                    @endif
                </div>

                {{-- Form Title --}}
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Đăng Ký Nhận Tư Vấn &amp; Giữ Chỗ</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Giáo viên sẽ liên hệ qua SĐT/Zalo để xếp lịch học phù hợp nhất.</p>
                </div>

                {{-- Validation Errors --}}
                @if($errors->any())
                <div class="rounded-xl border border-rose-200 bg-rose-50 p-3 text-xs text-rose-800 space-y-1">
                    @foreach($errors->all() as $error)
                    <p class="flex items-center gap-1.5"><i data-lucide="alert-circle" class="h-3.5 w-3.5 shrink-0 text-rose-600"></i>{{ $error }}</p>
                    @endforeach
                </div>
                @endif

                {{-- The Form --}}
                <form action="{{ route('courses.register', $course->slug) }}" method="POST" class="space-y-3.5">
                    @csrf

                    {{-- Honeypot field (hidden from humans, catches automated spam bots) --}}
                    <input type="text" name="website_url_hp" style="display:none !important;" tabindex="-1" autocomplete="off">

                    {{-- Chọn Lớp --}}
                    @if($course->openClasses->isNotEmpty())
                    <div>
                        <label for="course_class_id" class="block text-xs font-bold text-slate-700 mb-1">
                            Chọn kỳ / lớp khai giảng mong muốn:
                        </label>
                        <select name="course_class_id" id="course_class_id"
                                class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3.5 py-2.5 text-xs text-slate-800 focus:border-[#991b1b] focus:bg-white focus:ring-1 focus:ring-[#991b1b] transition">
                            <option value="">-- Để giáo viên xếp lớp phù hợp --</option>
                            @foreach($course->openClasses as $cls)
                            <option value="{{ $cls->id }}" {{ old('course_class_id') == $cls->id ? 'selected' : '' }}>
                                {{ $cls->name }} ({{ $cls->schedule_days }} {{ $cls->schedule_time }})
                            </option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    {{-- Họ và tên --}}
                    <div>
                        <label for="full_name" class="block text-xs font-bold text-slate-700 mb-1">
                            Họ và tên của bạn <span class="text-rose-600">*</span>
                        </label>
                        <input type="text" name="full_name" id="full_name" required
                               value="{{ old('full_name', auth()->user()?->name ?? '') }}"
                               placeholder="Ví dụ: Nguyễn Văn An"
                               class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs text-slate-900 placeholder:text-slate-400 focus:border-[#991b1b] focus:ring-1 focus:ring-[#991b1b] transition">
                    </div>

                    {{-- Số điện thoại & Zalo --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label for="phone" class="block text-xs font-bold text-slate-700 mb-1">
                                Số điện thoại <span class="text-rose-600">*</span>
                            </label>
                            <input type="tel" name="phone" id="phone" required
                                   value="{{ old('phone') }}"
                                   placeholder="0912 345 678"
                                   class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs text-slate-900 placeholder:text-slate-400 focus:border-[#991b1b] focus:ring-1 focus:ring-[#991b1b] transition">
                        </div>
                        <div>
                            <label for="zalo" class="block text-xs font-bold text-slate-700 mb-1">
                                Số Zalo (nếu khác SĐT)
                            </label>
                            <input type="text" name="zalo" id="zalo"
                                   value="{{ old('zalo') }}"
                                   placeholder="SĐT Zalo"
                                   class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs text-slate-900 placeholder:text-slate-400 focus:border-[#991b1b] focus:ring-1 focus:ring-[#991b1b] transition">
                        </div>
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-xs font-bold text-slate-700 mb-1">
                            Địa chỉ Email (để nhận tài liệu &amp; link Meet)
                        </label>
                        <input type="email" name="email" id="email"
                               value="{{ old('email', auth()->user()?->email ?? '') }}"
                               placeholder="email@example.com"
                               class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs text-slate-900 placeholder:text-slate-400 focus:border-[#991b1b] focus:ring-1 focus:ring-[#991b1b] transition">
                    </div>

                    {{-- Trình độ hiện tại --}}
                    <div>
                        <label for="current_level" class="block text-xs font-bold text-slate-700 mb-1">
                            Trình độ hiện tại của bạn <span class="text-rose-600">*</span>
                        </label>
                        <select name="current_level" id="current_level" required
                                class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-xs text-slate-800 focus:border-[#991b1b] focus:ring-1 focus:ring-[#991b1b] transition">
                            <option value="chua_biet_gi" {{ old('current_level') == 'chua_biet_gi' ? 'selected' : '' }}>Chưa biết gì (Bắt đầu từ con số 0)</option>
                            <option value="co_ban_phat_am" {{ old('current_level') == 'co_ban_phat_am' ? 'selected' : '' }}>Đã biết phát âm cơ bản (Pinyin)</option>
                            <option value="hsk1_2" {{ old('current_level') == 'hsk1_2' ? 'selected' : '' }}>Đã học qua HSK 1 - 2</option>
                            <option value="hsk3_4" {{ old('current_level') == 'hsk3_4' ? 'selected' : '' }}>Đã học qua HSK 3 - 4</option>
                            <option value="giao_tiep" {{ old('current_level') == 'giao_tiep' ? 'selected' : '' }}>Chủ yếu muốn luyện phản xạ giao tiếp</option>
                        </select>
                    </div>

                    {{-- Khung giờ mong muốn --}}
                    <div>
                        <label for="preferred_schedule" class="block text-xs font-bold text-slate-700 mb-1">
                            Khung giờ học mong muốn
                        </label>
                        <input type="text" name="preferred_schedule" id="preferred_schedule"
                               value="{{ old('preferred_schedule') }}"
                               placeholder="Ví dụ: Tối 2-4-6 sau 19h hoặc Cuối tuần"
                               class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs text-slate-900 placeholder:text-slate-400 focus:border-[#991b1b] focus:ring-1 focus:ring-[#991b1b] transition">
                    </div>

                    {{-- Mục tiêu học tập --}}
                    <div>
                        <label for="learning_goal" class="block text-xs font-bold text-slate-700 mb-1">
                            Mục tiêu học tập của bạn
                        </label>
                        <textarea name="learning_goal" id="learning_goal" rows="2"
                                  placeholder="Ví dụ: Cần thi HSK 3 trong 3 tháng tới để xin việc, du lịch tự túc..."
                                  class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-xs text-slate-900 placeholder:text-slate-400 focus:border-[#991b1b] focus:ring-1 focus:ring-[#991b1b] transition">{{ old('learning_goal') }}</textarea>
                    </div>

                    {{-- Submit Button --}}
                    <button type="submit"
                            class="w-full rounded-2xl bg-[#991b1b] py-3 text-center text-sm font-black text-white shadow-lg shadow-red-950/20 hover:bg-red-800 active:scale-[0.99] transition">
                        Gửi Đăng Ký &amp; Nhận Lộ Trình Học
                    </button>

                    <div class="pt-2 text-[11px] text-slate-400 text-center space-y-1">
                        <p class="flex items-center justify-center gap-1.5">
                            <i data-lucide="shield-check" class="h-3.5 w-3.5 text-emerald-600"></i>
                            Bảo mật thông tin 100% • Không gọi spam
                        </p>
                        <p>Giáo viên sẽ chủ động liên hệ trong vòng 15 - 30 phút</p>
                    </div>
                </form>

            </div>

            {{-- Teacher Direct Hotline Card --}}
            @php 
                $consultPhone = function_exists('setting') ? setting('course_consult_phone', '0988888888') : '0988888888'; 
                $consultZalo = function_exists('setting') ? setting('course_consult_zalo', '0988888888') : '0988888888'; 
            @endphp
            <div class="rounded-2xl bg-slate-50 border border-slate-200 p-4 text-xs text-slate-600 flex items-center justify-between">
                <div class="space-y-0.5">
                    <p class="font-bold text-slate-800">Cần tư vấn trực tiếp ngay?</p>
                    <p class="text-[11px] text-slate-500">Hỗ trợ xếp lịch và giải đáp thắc mắc</p>
                </div>
                <div class="flex items-center gap-2">
                    <a href="tel:{{ $consultPhone }}" title="Gọi hotline"
                       class="grid h-8 w-8 place-items-center rounded-xl bg-white border border-slate-200 text-blue-600 hover:bg-blue-50 transition shadow-sm">
                        <i data-lucide="phone" class="h-3.5 w-3.5"></i>
                    </a>
                    <a href="https://zalo.me/{{ $consultZalo }}" target="_blank" rel="noopener noreferrer" title="Chat Zalo"
                       class="grid h-8 w-8 place-items-center rounded-xl bg-emerald-600 text-white hover:bg-emerald-700 transition shadow-sm">
                        <i data-lucide="message-square" class="h-3.5 w-3.5"></i>
                    </a>
                </div>
            </div>

        </div>

    </div>

    {{-- Other Recommended Courses --}}
    @if($otherCourses->isNotEmpty())
    <div class="pt-8 border-t border-slate-200/80 space-y-6">
        <h2 class="text-xl font-black text-slate-900">Các Khóa Học Khác Có Thể Bạn Quan Tâm</h2>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            @foreach($otherCourses as $oc)
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm hover:shadow-md hover:border-red-200 transition space-y-3 flex flex-col justify-between">
                <div class="space-y-2">
                    <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-[10px] font-bold text-slate-600">
                        {{ $oc->category_label }}
                    </span>
                    <h3 class="font-bold text-slate-900 text-sm leading-snug">
                        <a href="{{ route('courses.show', $oc->slug) }}" class="hover:text-[#991b1b] transition">
                            {{ $oc->title }}
                        </a>
                    </h3>
                    <p class="text-xs text-slate-500 line-clamp-2">{{ $oc->summary }}</p>
                </div>
                <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-sm font-black text-[#991b1b]">{{ $oc->formatted_price }}</span>
                    <a href="{{ route('courses.show', $oc->slug) }}" class="text-xs font-bold text-slate-700 hover:text-[#991b1b]">
                        Xem thêm &rarr;
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>
@endsection
