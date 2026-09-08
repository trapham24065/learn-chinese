<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hệ thống đang bảo trì | {{ function_exists('setting') ? setting('site_name', 'Learn Chinese') : 'Learn Chinese' }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#f4ede3] text-slate-950 antialiased flex items-center justify-center p-4">
    <div class="relative w-full max-w-xl overflow-hidden rounded-3xl bg-slate-950 p-8 sm:p-12 text-white shadow-2xl shadow-slate-950/20 text-center">
        {{-- Decorative background gradients & Chinese watermark --}}
        <div class="absolute -right-6 -bottom-6 select-none text-[14rem] font-black leading-none text-white/[0.03] pointer-events-none">
            修
        </div>
        <div class="absolute -left-12 -top-12 h-48 w-48 rounded-full bg-[#991b1b]/30 blur-3xl pointer-events-none"></div>
        <div class="absolute right-10 top-10 h-36 w-36 rounded-full bg-amber-400/15 blur-2xl pointer-events-none"></div>
        <div class="absolute inset-x-0 top-0 h-1.5 bg-gradient-to-r from-[#991b1b] via-amber-400 to-[#991b1b]"></div>

        <div class="relative z-10 space-y-6">
            {{-- Badge --}}
            <div class="inline-flex items-center gap-2 rounded-full border border-amber-400/30 bg-amber-400/10 px-4 py-1.5 text-xs font-bold uppercase tracking-widest text-amber-300">
                <span class="h-2 w-2 rounded-full bg-amber-400 animate-pulse"></span>
                Bảo trì hệ thống • 系统维护
            </div>

            {{-- Chinese Hanzi Visual --}}
            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-2xl bg-[#991b1b] text-white shadow-lg shadow-red-950/40">
                <span class="text-3xl font-black">修</span>
            </div>

            {{-- Heading --}}
            <div>
                <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">
                    Website đang được nâng cấp
                </h1>
                <p class="mt-3 text-sm sm:text-base leading-relaxed text-slate-300">
                    {{ $message ?? 'Website đang được nâng cấp để mang lại trải nghiệm học tập tốt hơn. Vui lòng quay lại sau ít phút!' }}
                </p>
            </div>

            {{-- Chinese Learning Tip --}}
            <div class="rounded-2xl border border-white/10 bg-white/5 p-4 text-left backdrop-blur">
                <p class="text-xs font-bold uppercase tracking-wider text-amber-300/90">Học nhanh từ vựng:</p>
                <div class="mt-1 flex items-baseline gap-2">
                    <span class="text-xl font-bold text-white">维护</span>
                    <span class="text-xs font-mono text-slate-300">wéihù</span>
                    <span class="text-xs text-slate-400">— bảo trì, duy trì</span>
                </div>
            </div>

            {{-- Contact / Action --}}
            <div class="pt-2 text-xs text-slate-500">
                <p>{{ function_exists('setting') ? setting('site_name', 'Learn Chinese') : 'Learn Chinese' }} &bull; Cảm ơn bạn đã kiên nhẫn</p>
            </div>
        </div>
    </div>
</body>
</html>
