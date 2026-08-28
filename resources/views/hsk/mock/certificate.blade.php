<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chứng chỉ HSK {{ $test->hsk_level }} - {{ $test->certificate_code }} | Chinese Deck</title>
    
    {{-- Fonts & Vite Assets --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;900&family=Noto+Serif+SC:wght@600;700;900&family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .font-cinzel { font-family: 'Cinzel', serif; }
        .font-chinese { font-family: 'Noto Serif SC', serif; }
        
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: white !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .certificate-container {
                box-shadow: none !important;
                border: 2px solid #b45309 !important;
                margin: 0 !important;
                width: 100% !important;
                height: 100vh !important;
                max-width: 100% !important;
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body class="bg-slate-950 min-h-screen py-6 sm:py-12 px-4 flex flex-col items-center justify-center font-sans antialiased text-slate-800">

    {{-- Top Action Toolbar (Hidden in print mode) --}}
    <div class="no-print w-full max-w-4xl mb-6 flex flex-wrap items-center justify-between gap-4">
        <a href="{{ route('hsk.mock.result', $test->id) }}"
           class="inline-flex items-center gap-2 rounded-2xl bg-white/10 hover:bg-white/20 text-white px-4 py-2.5 text-xs font-bold transition backdrop-blur border border-white/10">
            <i data-lucide="arrow-left" class="h-4 w-4"></i>
            <span>Quay lại bảng điểm</span>
        </a>

        <div class="flex items-center gap-3">
            <button type="button"
                    onclick="navigator.clipboard.writeText(window.location.href); alert('Đã sao chép liên kết chứng chỉ vào bộ nhớ tạm!');"
                    class="inline-flex items-center gap-2 rounded-2xl bg-white/10 hover:bg-white/20 text-white px-4 py-2.5 text-xs font-bold transition backdrop-blur border border-white/10">
                <i data-lucide="share-2" class="h-4 w-4"></i>
                <span>Sao chép link</span>
            </button>
            <button type="button"
                    onclick="window.print()"
                    class="inline-flex items-center gap-2 rounded-2xl bg-amber-400 hover:bg-amber-300 text-slate-950 px-5 py-2.5 text-xs font-black transition shadow-lg shadow-amber-400/20 active:scale-95">
                <i data-lucide="printer" class="h-4 w-4"></i>
                <span>In / Tải PDF chứng chỉ</span>
            </button>
        </div>
    </div>

    {{-- Certificate Body --}}
    <div class="certificate-container relative w-full max-w-4xl rounded-[2.5rem] bg-[#fffdfa] p-8 sm:p-14 shadow-2xl border-8 border-double border-amber-800/40 text-slate-900 overflow-hidden">
        
        {{-- Inner Decorative Golden Border --}}
        <div class="absolute inset-4 rounded-[1.8rem] border-2 border-amber-600/30 pointer-events-none"></div>
        <div class="absolute inset-6 rounded-[1.5rem] border border-amber-600/20 pointer-events-none"></div>

        {{-- Background Chinese Seal Watermark --}}
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 text-[18rem] font-black text-amber-900/[0.03] select-none pointer-events-none font-chinese leading-none">
            HSK
        </div>

        {{-- Certificate Header --}}
        <div class="relative text-center">
            
            {{-- Top Emblem / Badge --}}
            <div class="inline-flex items-center justify-center h-16 w-16 rounded-full bg-gradient-to-br from-amber-500 to-amber-700 text-white shadow-lg shadow-amber-600/30 mx-auto mb-3">
                <i data-lucide="award" class="h-8 w-8"></i>
            </div>

            <p class="font-cinzel text-xs sm:text-sm tracking-[0.35em] text-amber-800 uppercase font-black">
                LEARN CHINESE ONLINE ACADEMY
            </p>
            
            <h1 class="mt-2 text-2xl sm:text-4xl font-black font-chinese tracking-wide text-[#991b1b]">
                汉语水平考试模拟合格证书
            </h1>
            
            <p class="font-cinzel text-xs tracking-[0.25em] text-slate-500 uppercase mt-1 font-bold">
                HSK MOCK EXAMINATION CERTIFICATE OF COMPLETION
            </p>
        </div>

        {{-- Certificate Recipient --}}
        <div class="relative mt-8 sm:mt-10 text-center">
            <p class="text-xs uppercase tracking-[0.2em] text-slate-500 font-bold">Chứng nhận cấp cho / Awarded to</p>
            
            <h2 class="mt-2 text-3xl sm:text-4xl font-black text-slate-950 font-serif border-b-2 border-amber-600/30 inline-block px-8 pb-2">
                {{ $test->user?->name ?? 'Học viên Chinese Deck' }}
            </h2>

            <p class="mt-4 text-xs sm:text-sm text-slate-700 max-w-xl mx-auto leading-relaxed">
                Đã hoàn thành xuất sắc bài thi thử chuẩn hóa <strong class="text-[#991b1b]">HSK Cấp độ {{ $test->hsk_level }}</strong>, đạt đủ tiêu chuẩn đánh giá năng lực ngôn ngữ theo khung chuẩn quốc tế.
            </p>
            <p class="text-xs text-slate-500 font-chinese mt-1">
                兹证明该学员在汉语水平考试（HSK {{ $test->hsk_level }}级）模拟测试中成绩优异，特发此证。
            </p>
        </div>

        {{-- Score Summary Table Grid --}}
        <div class="relative mt-8 rounded-2xl bg-amber-50/70 border border-amber-200/80 p-5 max-w-xl mx-auto shadow-sm">
            <div class="grid grid-cols-4 gap-2 text-center text-xs">
                <div class="border-r border-amber-200/80 pr-2">
                    <p class="text-slate-500 text-[11px] font-semibold">听力 (Nghe)</p>
                    <p class="mt-1 text-base sm:text-lg font-black text-blue-700">{{ $test->listening_score }} <span class="text-[10px] text-slate-400 font-normal">/100</span></p>
                </div>
                <div class="border-r border-amber-200/80 pr-2">
                    <p class="text-slate-500 text-[11px] font-semibold">阅读 (Đọc)</p>
                    <p class="mt-1 text-base sm:text-lg font-black text-emerald-700">{{ $test->reading_score }} <span class="text-[10px] text-slate-400 font-normal">/100</span></p>
                </div>
                <div class="border-r border-amber-200/80 pr-2">
                    <p class="text-slate-500 text-[11px] font-semibold">语法 (Ngữ pháp)</p>
                    <p class="mt-1 text-base sm:text-lg font-black text-purple-700">{{ $test->grammar_score }} <span class="text-[10px] text-slate-400 font-normal">/100</span></p>
                </div>
                <div>
                    <p class="text-slate-500 text-[11px] font-bold">总分 (Tổng)</p>
                    <p class="mt-1 text-lg sm:text-xl font-black text-[#991b1b]">{{ $test->total_score }} <span class="text-[10px] text-slate-400 font-normal">/300</span></p>
                </div>
            </div>
            
            <div class="mt-3 pt-2.5 border-t border-amber-200/80 flex items-center justify-between text-xs text-slate-600 font-semibold px-2">
                <span>Xếp loại: <strong class="text-emerald-700">{{ $test->grade_text }}</strong></span>
                <span>Tỷ lệ đạt: <strong class="text-slate-900">{{ (int)round(($test->total_score / 300) * 100) }}%</strong></span>
            </div>
        </div>

        {{-- Footer Signature & Red Seal Stamp --}}
        <div class="relative mt-10 pt-6 border-t border-amber-900/10 flex flex-col sm:flex-row items-center justify-between gap-6">
            
            {{-- Left: Issue Date & Verification Code --}}
            <div class="text-center sm:text-left text-xs text-slate-500 space-y-1">
                <p><strong>Ngày cấp chứng nhận:</strong> {{ $test->completed_at?->format('d/m/Y') ?? now()->format('d/m/Y') }}</p>
                <p><strong>Mã tra cứu chứng chỉ:</strong> <span class="font-mono font-bold text-slate-900">{{ $test->certificate_code }}</span></p>
                <p class="text-[10px] text-slate-400">Xác thực trực tuyến tại: <span class="font-mono text-[#991b1b]">learnchinese.io.vn</span></p>
            </div>

            {{-- Right: Traditional Red Seal Stamp --}}
            <div class="relative flex items-center gap-4">
                {{-- Official Stamp Circle --}}
                <div class="relative flex h-24 w-24 items-center justify-center rounded-full border-4 border-dashed border-red-700 bg-red-50/20 text-red-700 rotate-[-8deg] shadow-sm">
                    <div class="text-center font-chinese text-[11px] font-black leading-tight tracking-tighter">
                        <span class="block text-[8px] tracking-widest font-cinzel">LEARN CHINESE</span>
                        汉语水平考试
                        <span class="block text-[9px] text-red-800">★ 合格认证 ★</span>
                        模拟通过
                    </div>
                </div>
                <div class="text-left text-xs font-semibold text-slate-600">
                    <p class="font-bold text-slate-900">Ban Khảo Thí Quốc Tế</p>
                    <p class="text-[11px] text-slate-400">Chinese Deck Examination Board</p>
                </div>
            </div>

        {{-- Disclaimer Footnote --}}
        <div class="relative mt-8 pt-4 border-t border-dashed border-amber-900/15 text-center">
            <p class="text-[10px] text-slate-400 leading-relaxed italic">
                * Lưu ý: Đây là <strong>Giấy chứng nhận hoàn thành bài thi thử mô phỏng (Mock Exam Certificate)</strong> trên nền tảng học trực tuyến Chinese Deck nhằm mục đích đánh giá năng lực học tập và tạo động lực rèn luyện, không thay thế chứng chỉ HSK chính thức do Tổng bộ Viện Khổng Tử / Chinese Testing International (CTI) cấp.
            </p>
        </div>

    </div>

</body>
</html>
