@extends('layouts.app')

@section('title', 'Đăng Ký Thành Công | ' . $registration->course->title)

@section('content')
<div class="max-w-3xl mx-auto space-y-8 py-4" x-data="{ copied: false, copyText(text) { navigator.clipboard.writeText(text); this.copied = true; setTimeout(() => this.copied = false, 2000); } }">

    {{-- Success Header --}}
    <div class="text-center space-y-3">
        <div class="inline-grid h-16 w-16 place-items-center rounded-full bg-emerald-100 text-emerald-600 shadow-inner">
            <i data-lucide="check-circle" class="h-8 w-8"></i>
        </div>
        <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
            Đăng Ký Khóa Học Thành Công!
        </h1>
        <p class="text-sm text-slate-600 max-w-md mx-auto">
            Cảm ơn bạn <strong class="text-slate-900">{{ $registration->full_name }}</strong>. Giáo viên đã ghi nhận thông tin và sẽ gọi điện xác nhận trong ít phút.
        </p>
    </div>

    {{-- Registration Summary Card --}}
    <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-5">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Mã đơn đăng ký</p>
                <div class="flex items-center gap-2 mt-0.5">
                    <span class="text-xl font-black font-mono text-[#991b1b]">{{ $registration->registration_code }}</span>
                    <button @click="copyText('{{ $registration->registration_code }}')"
                            type="button" title="Sao chép mã"
                            class="rounded-lg p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition">
                        <i data-lucide="copy" class="h-4 w-4"></i>
                    </button>
                </div>
            </div>
            <span class="rounded-full bg-amber-50 border border-amber-200 px-3 py-1 text-xs font-bold text-amber-800">
                {{ $registration->status_label }}
            </span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
            <div>
                <span class="text-slate-400 font-medium">Khóa học đăng ký:</span>
                <p class="font-bold text-slate-900 mt-0.5 text-sm">{{ $registration->course->title }}</p>
            </div>

            <div>
                <span class="text-slate-400 font-medium">Lớp / Lịch học:</span>
                <p class="font-bold text-slate-900 mt-0.5">
                    @if($registration->courseClass)
                        {{ $registration->courseClass->name }} ({{ $registration->courseClass->schedule_days }} {{ $registration->courseClass->schedule_time }})
                    @else
                        {{ $registration->preferred_schedule ?: 'Giáo viên sẽ tư vấn xếp lịch phù hợp' }}
                    @endif
                </p>
            </div>

            <div>
                <span class="text-slate-400 font-medium">Số điện thoại liên hệ:</span>
                <p class="font-bold text-slate-900 mt-0.5">{{ $registration->phone }}</p>
            </div>

            <div>
                <span class="text-slate-400 font-medium">Trình độ đầu vào:</span>
                <p class="font-bold text-slate-900 mt-0.5">{{ $registration->current_level_label }}</p>
            </div>
        </div>
    </div>

    {{-- VietQR Transfer Box --}}
    <div class="rounded-3xl border-2 border-emerald-200 bg-white p-6 sm:p-8 shadow-md space-y-6">
        <div class="text-center space-y-1">
            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 border border-emerald-200 px-3 py-1 text-xs font-bold text-emerald-800">
                <i data-lucide="qr-code" class="h-3.5 w-3.5"></i>
                Thanh toán giữ chỗ qua VietQR
            </span>
            <h2 class="text-xl font-bold text-slate-900">Quét Mã QR Chuyển Khoản Học Phí</h2>
            <p class="text-xs text-slate-500">Mở app ngân hàng bất kỳ để quét mã và tự động điền số tiền, nội dung</p>
        </div>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-6 sm:gap-8 pt-2">
            {{-- Dynamic QR Image --}}
            <div class="shrink-0 text-center">
                <div class="p-3 bg-white rounded-2xl border border-slate-200 shadow-md inline-block">
                    <img src="{{ $vietQrUrl }}" alt="VietQR Học Phí" class="w-56 h-56 rounded-xl object-contain mx-auto">
                </div>
                <p class="text-[11px] font-semibold text-slate-400 mt-2">Quét mã bằng camera hoặc app ngân hàng</p>
            </div>

            {{-- Bank Details --}}
            <div class="flex-1 space-y-3.5 text-xs w-full max-w-sm">
                <div class="rounded-xl bg-slate-50 p-3 border border-slate-100 flex items-center justify-between">
                    <div>
                        <span class="text-slate-400 text-[10px] uppercase font-bold">Ngân hàng thụ hưởng</span>
                        <p class="font-bold text-slate-900 text-sm mt-0.5">{{ $bankId }}</p>
                    </div>
                </div>

                <div class="rounded-xl bg-slate-50 p-3 border border-slate-100 flex items-center justify-between">
                    <div>
                        <span class="text-slate-400 text-[10px] uppercase font-bold">Số tài khoản</span>
                        <p class="font-bold font-mono text-slate-900 text-base mt-0.5">{{ $bankAccount }}</p>
                    </div>
                    <button @click="copyText('{{ $bankAccount }}')" type="button"
                            class="rounded-lg border border-slate-200 bg-white px-2 py-1 text-[11px] font-bold text-slate-700 hover:bg-slate-100 transition shadow-sm">
                        Sao chép
                    </button>
                </div>

                <div class="rounded-xl bg-slate-50 p-3 border border-slate-100">
                    <span class="text-slate-400 text-[10px] uppercase font-bold">Chủ tài khoản</span>
                    <p class="font-bold text-slate-900 mt-0.5">{{ $bankAccountName }}</p>
                </div>

                <div class="rounded-xl bg-emerald-50/70 border border-emerald-200 p-3 flex items-center justify-between">
                    <div>
                        <span class="text-emerald-800 text-[10px] uppercase font-bold">Số tiền học phí</span>
                        <p class="font-black text-emerald-800 text-base mt-0.5">{{ number_format($registration->payment_amount, 0, ',', '.') }} ₫</p>
                    </div>
                    <span class="text-[10px] font-medium text-emerald-700">Có thể cọc trước 500k</span>
                </div>

                <div class="rounded-xl bg-amber-50 border border-amber-200 p-3 flex items-center justify-between">
                    <div>
                        <span class="text-amber-900 text-[10px] uppercase font-bold">Nội dung chuyển khoản</span>
                        <p class="font-black font-mono text-amber-900 text-sm mt-0.5">{{ $registration->registration_code }}</p>
                    </div>
                    <button @click="copyText('{{ $registration->registration_code }}')" type="button"
                            class="rounded-lg border border-amber-300 bg-white px-2 py-1 text-[11px] font-bold text-amber-900 hover:bg-amber-100 transition shadow-sm">
                        Sao chép
                    </button>
                </div>
            </div>
        </div>

        <div class="rounded-2xl bg-slate-50 p-4 border border-slate-100 text-xs text-slate-600 space-y-1.5 leading-relaxed">
            <p class="font-bold text-slate-800 flex items-center gap-1.5">
                <i data-lucide="info" class="h-4 w-4 text-[#991b1b]"></i>
                Lưu ý sau khi chuyển khoản:
            </p>
            <p>
                Hệ thống sẽ đối chiếu mã nội dung <strong class="font-mono text-slate-900">{{ $registration->registration_code }}</strong> và giáo viên sẽ kích hoạt giữ chỗ lớp học, đồng thời gửi lời mời tham gia nhóm Zalo của lớp qua số điện thoại <strong class="text-slate-900">{{ $registration->phone }}</strong>.
            </p>
        </div>
    </div>

    {{-- Action Buttons --}}
    @php
        $consultPhone = function_exists('setting') ? setting('course_consult_phone', '0988888888') : '0988888888';
        $consultZalo = function_exists('setting') ? setting('course_consult_zalo', '0988888888') : '0988888888';
    @endphp
    <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
        <a href="https://zalo.me/{{ $consultZalo }}" target="_blank" rel="noopener noreferrer"
           class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-2xl bg-emerald-600 px-6 py-3 text-xs font-bold text-white shadow-md shadow-emerald-950/15 hover:bg-emerald-700 transition">
            <i data-lucide="message-square" class="h-4 w-4"></i>
            <span>Nhắn Zalo xác nhận ngay</span>
        </a>
        <a href="{{ route('courses.index') }}"
           class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-white px-6 py-3 text-xs font-bold text-slate-700 hover:bg-slate-50 transition shadow-sm">
            <i data-lucide="arrow-left" class="h-4 w-4"></i>
            <span>Xem các khóa học khác</span>
        </a>
    </div>

    {{-- Toast notice when copied --}}
    <div x-show="copied" x-transition
         class="fixed bottom-6 right-6 z-50 rounded-xl bg-slate-900 text-white px-4 py-2.5 text-xs font-bold shadow-xl flex items-center gap-2">
        <i data-lucide="check" class="h-4 w-4 text-emerald-400"></i>
        <span>Đã sao chép vào bộ nhớ tạm!</span>
    </div>

</div>
@endsection
