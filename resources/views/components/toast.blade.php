@php
    $flashSuccess = session('success');
    $flashError = session('error');
    $flashInfo = session('info') ?? session('status');
@endphp

@if ($flashSuccess || $flashError || $flashInfo)
    <script>
        (function() {
            const triggerFlashToast = () => {
                if (window.toast) {
                    @if ($flashSuccess)
                        window.toast.success(@json($flashSuccess));
                    @elseif ($flashError)
                        window.toast.error(@json($flashError));
                    @elseif ($flashInfo)
                        window.toast.info(@json($flashInfo));
                    @endif
                } else {
                    setTimeout(triggerFlashToast, 50);
                }
            };

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', triggerFlashToast);
            } else {
                triggerFlashToast();
            }
        })();
    </script>
@endif