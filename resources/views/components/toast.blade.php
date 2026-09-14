@php
    $flashSuccess = session('success');
    $flashError = session('error') ?? ($errors->any() ? $errors->first() : null);
    $flashInfo = session('info') ?? session('status');
@endphp

<script>
    (function() {
        // Đảm bảo SweetAlert2 và các helper window.toast, window.dialog luôn sẵn sàng
        function initNotifyHelpers() {
            if (typeof Swal === 'undefined') return false;

            if (!window.toast) {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3500,
                    timerProgressBar: true,
                    showCloseButton: true,
                    didOpen: (toast) => {
                        toast.onmouseenter = Swal.stopTimer;
                        toast.onmouseleave = Swal.resumeTimer;
                        if (window.refreshIcons) window.refreshIcons();
                    },
                    customClass: {
                        popup: 'swal-custom-toast rounded-2xl shadow-xl border border-slate-200/80 bg-white/95 backdrop-blur-md text-slate-900',
                        title: 'text-xs font-bold text-slate-800 m-0',
                        htmlContainer: 'text-xs font-medium text-slate-600 m-0 mt-0.5',
                        timerProgressBar: 'bg-[#991b1b]',
                    }
                });

                window.toast = {
                    success: (title, message = '') => Toast.fire({ icon: 'success', title: title || 'Thành công!', text: message || undefined, iconColor: '#10b981' }),
                    error: (title, message = '') => Toast.fire({ icon: 'error', title: title || 'Có lỗi xảy ra!', text: message || undefined, iconColor: '#ef4444', timer: 5000 }),
                    warning: (title, message = '') => Toast.fire({ icon: 'warning', title: title || 'Lưu ý!', text: message || undefined, iconColor: '#f59e0b' }),
                    info: (title, message = '') => Toast.fire({ icon: 'info', title: title || 'Thông báo', text: message || undefined, iconColor: '#3b82f6' }),
                };
            }

            if (!window.dialog) {
                window.dialog = {
                    confirm: async ({
                        title = 'Xác nhận hành động',
                        text = 'Bạn có chắc chắn muốn thực hiện thao tác này?',
                        confirmText = 'Xác nhận',
                        cancelText = 'Hủy bỏ',
                        icon = 'warning',
                        isDestructive = false,
                    } = {}) => {
                        const result = await Swal.fire({
                            title,
                            text,
                            icon,
                            showCancelButton: true,
                            confirmButtonColor: isDestructive ? '#dc2626' : '#991b1b',
                            cancelButtonColor: '#64748b',
                            confirmButtonText: confirmText,
                            cancelButtonText: cancelText,
                            reverseButtons: true,
                            customClass: {
                                popup: 'rounded-3xl p-6 border border-slate-100 shadow-2xl font-sans',
                                title: 'text-lg font-black text-slate-900 tracking-tight',
                                htmlContainer: 'text-sm text-slate-600 leading-relaxed',
                                confirmButton: 'rounded-xl font-bold px-5 py-2.5 shadow-md transition',
                                cancelButton: 'rounded-xl font-bold px-5 py-2.5 transition',
                            }
                        });
                        return result.isConfirmed;
                    },

                    alert: async ({ title, text, icon = 'info', confirmText = 'Đã hiểu' }) => {
                        return Swal.fire({
                            title,
                            text,
                            icon,
                            confirmButtonColor: '#991b1b',
                            confirmButtonText,
                            customClass: {
                                popup: 'rounded-3xl p-6 border border-slate-100 shadow-2xl font-sans',
                                title: 'text-lg font-black text-slate-900 tracking-tight',
                                htmlContainer: 'text-sm text-slate-600',
                                confirmButton: 'rounded-xl font-bold px-5 py-2.5 shadow-md',
                            }
                        });
                    },

                    authRequired: ({
                        title = 'Lưu từ vào Sổ tay yêu thích',
                        word = '',
                        pinyin = '',
                        meaning = '',
                        redirectUrl = window.location.href,
                    } = {}) => {
                        const wordPreviewHtml = word ? `
                            <div class="my-3 rounded-2xl bg-amber-50/90 border border-amber-200/80 p-3.5 text-center shadow-inner">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-amber-700 block">Từ vựng bạn vừa chọn</span>
                                <div class="mt-1 flex items-baseline justify-center gap-2">
                                    <span class="text-3xl font-black text-slate-900 font-serif">${word}</span>
                                    ${pinyin ? `<span class="text-base font-bold text-[#991b1b] font-mono">${pinyin}</span>` : ''}
                                </div>
                                ${meaning ? `<p class="mt-1 text-xs font-semibold text-slate-700">${meaning}</p>` : ''}
                            </div>
                        ` : '';

                        const loginUrl = '/login?redirect=' + encodeURIComponent(redirectUrl);
                        const registerUrl = '/register?redirect=' + encodeURIComponent(redirectUrl);

                        Swal.fire({
                            title: `
                                <div class="flex items-center justify-center gap-2 text-slate-900 font-black text-xl tracking-tight">
                                    <span class="text-amber-500">⭐</span>
                                    <span>${title}</span>
                                </div>
                            `,
                            html: `
                                ${wordPreviewHtml}
                                <div class="text-left space-y-2 text-xs text-slate-600 px-1 py-1">
                                    <div class="flex items-center gap-2.5">
                                        <span class="grid h-5 w-5 shrink-0 place-items-center rounded-full bg-emerald-100 text-emerald-700 font-bold text-[11px]">✓</span>
                                        <span><strong>Lưu trữ vĩnh viễn:</strong> Đồng bộ từ vựng trên điện thoại và máy tính.</span>
                                    </div>
                                    <div class="flex items-center gap-2.5">
                                        <span class="grid h-5 w-5 shrink-0 place-items-center rounded-full bg-amber-100 text-amber-700 font-bold text-[11px]">✓</span>
                                        <span><strong>Thuật toán SRS:</strong> Xếp lịch ôn tập ngắt quãng thông minh để nhớ lâu.</span>
                                    </div>
                                    <div class="flex items-center gap-2.5">
                                        <span class="grid h-5 w-5 shrink-0 place-items-center rounded-full bg-blue-100 text-blue-700 font-bold text-[11px]">✓</span>
                                        <span><strong>Hoàn toàn miễn phí:</strong> Tạo tài khoản học viên chỉ mất 30 giây.</span>
                                    </div>
                                </div>
                            `,
                            showCancelButton: true,
                            showDenyButton: true,
                            confirmButtonColor: '#991b1b',
                            denyButtonColor: '#0f172a',
                            cancelButtonColor: '#94a3b8',
                            confirmButtonText: 'Đăng nhập ngay',
                            denyButtonText: 'Tạo tài khoản mới',
                            cancelButtonText: 'Để sau',
                            reverseButtons: false,
                            customClass: {
                                popup: 'rounded-3xl p-6 border border-slate-100 shadow-2xl max-w-md w-full font-sans',
                                confirmButton: 'rounded-xl font-bold px-4 py-2.5 text-xs shadow-md transition',
                                denyButton: 'rounded-xl font-bold px-4 py-2.5 text-xs shadow-md transition',
                                cancelButton: 'rounded-xl font-medium px-4 py-2.5 text-xs transition',
                                actions: 'flex flex-wrap gap-2 justify-center mt-4',
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = loginUrl;
                            } else if (result.isDenied) {
                                window.location.href = registerUrl;
                            }
                        });
                    }
                };

                window.showAuthModal = window.dialog.authRequired;
            }

            return true;
        }

        // Khởi tạo ngay hoặc thử lại cho đến khi SweetAlert2 load xong
        let attempts = 0;
        function setup() {
            if (initNotifyHelpers()) {
                @if ($flashSuccess)
                    window.toast.success(@json($flashSuccess));
                @elseif ($flashError)
                    window.toast.error(@json($flashError));
                @elseif ($flashInfo)
                    window.toast.info(@json($flashInfo));
                @endif
            } else if (attempts < 50) {
                attempts++;
                setTimeout(setup, 60);
            }
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', setup);
        } else {
            setup();
        }
    })();
</script>