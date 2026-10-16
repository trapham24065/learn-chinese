@extends('layouts.app')

@section('title', 'Nhã Trai · 雅斋 | Thư Phòng Của Bạn')

@section('content')
@php
    // 1. Xác định thời điểm trong ngày & lời chào thư phòng
    $hour = now()->hour;
    if ($hour >= 5 && $hour < 11) {
        $timeOfDay = 'morning';
        $timeGreeting = '早安 · Sương mai vừa tan, khơi nguồn trí tuệ';
    } elseif ($hour >= 11 && $hour < 17) {
        $timeOfDay = 'day';
        $timeGreeting = '静心 · Thư án tĩnh lặng, đọc sách ngẫm đạo';
    } elseif ($hour >= 17 && $hour < 22) {
        $timeOfDay = 'evening';
        $timeGreeting = '暮色 · Hoàng hôn buông xuống, hương trầm sưởi ấm';
    } else {
        $timeOfDay = 'night';
        $timeGreeting = '夜深了 · Đêm đã sâu, chong đèn đọc sách';
    }

    // 2. Phân cấp Học Vị (Học Tịch · 学籍) dựa theo XP
    $hocVi = match(true) {
        $totalXp >= 10000 => ['rank' => 'Trạng Nguyên', 'hanzi' => '状元', 'badge' => 'Kinh luân quán triệt'],
        $totalXp >= 5000  => ['rank' => 'Hàn Lâm',     'hanzi' => '翰林', 'badge' => 'Văn hoa tú xuất'],
        $totalXp >= 2500  => ['rank' => 'Tiến Sĩ',     'hanzi' => '进士', 'badge' => 'Đăng khoa bảng vàng'],
        $totalXp >= 1000  => ['rank' => 'Cử Nhân',     'hanzi' => '举人', 'badge' => 'Văn chương đắc cách'],
        $totalXp >= 300   => ['rank' => 'Tú Tài',      'hanzi' => '秀才', 'badge' => 'Khai thông kinh nghĩa'],
        default           => ['rank' => 'Đồng Sinh',   'hanzi' => '童生', 'badge' => 'Sơ nhập văn đàn'],
    };

    // 3. Trạng thái Lư Trầm (香炉) và khói hương
    $burnerState = match(true) {
        $streakDays >= 30 => [
            'status'   => '久习',
            'title'    => 'Cửu Tập · 久习',
            'desc'     => 'Hương hỏa trường tồn cùng tháng năm, công phu thâm hậu.',
            'has_seal' => true,
        ],
        $streakDays >= 8 => [
            'status'   => '香火不绝',
            'title'    => 'Hương Hỏa Bất Tuyệt · 香火不绝',
            'desc'     => 'Khói trầm uyển chuyển ngày đêm, việc học đã thành nếp sống.',
            'has_seal' => false,
        ],
        $streakDays >= 4 => [
            'status'   => '香火渐盛',
            'title'    => 'Hương Hỏa Tiệm Thịnh · 香火渐盛',
            'desc'     => 'Lửa đều khói ấm, thư phòng ngày càng thanh tịnh.',
            'has_seal' => false,
        ],
        $streakDays >= 1 => [
            'status'   => '初燃',
            'title'    => 'Sơ Nhiên · 初燃',
            'desc'     => 'Tia lửa đầu tiên đã bén, làn hương dịu nhẹ bắt đầu lan tỏa.',
            'has_seal' => false,
        ],
        default => [
            'status'   => '烟未起',
            'title'    => 'Yên Vị Khởi · 烟未起',
            'desc'     => 'Hương hỏa đã nguội một chút. Khi nào bạn sẵn sàng, ta cùng thắp lại nhé.',
            'has_seal' => false,
        ],
    };

    // 4. Pet Thư Đồng Data
    $userPetObj = (isset($petData) && is_array($petData)) ? ($petData['userPet'] ?? null) : null;
    $petStageObj = $userPetObj?->pet?->stages?->where('stage', $userPetObj->stage ?? 0)?->first();
    $petDefaultEmoji = $petStageObj?->emoji ?? '🥚';
    $petDefaultName = $userPetObj?->name ?? 'Bạn đồng hành nhỏ';
    $petStageNumber = $userPetObj->stage ?? 0;
    $petStageName = $petStageObj?->name ?? 'Trứng';

    // 5. Safe Lesson & Goal Data
    $hasCurrentLesson = !empty($currentLesson) && is_array($currentLesson);
    $currentLessonTitle = $hasCurrentLesson ? ($currentLesson['title'] ?? 'Lộ trình bài học HSK') : 'Lộ trình bài học HSK';
    $currentLessonSlug = $hasCurrentLesson ? ($currentLesson['slug'] ?? null) : null;
    $currentLessonHsk = $hasCurrentLesson ? ($currentLesson['hsk_level'] ?? 1) : 1;
    $currentLessonSummary = $hasCurrentLesson ? ($currentLesson['summary'] ?? null) : null;
    $currentLessonPercent = $hasCurrentLesson ? ($currentLesson['progress_percent'] ?? 0) : 0;
    $dailyGoalPercent = (isset($dailyGoal) && is_array($dailyGoal)) ? ($dailyGoal['progress_percent'] ?? 0) : 0;
@endphp

<div class="max-w-4xl mx-auto space-y-8 py-2 sm:py-4 font-serif">

    {{-- ==============================================================
         1. HERO: NHÃ TRAI · HOAN NGHÊNH TRỞ LẠI (雅斋 · 欢迎归来)
         ============================================================== --}}
    <header class="relative overflow-hidden rounded-[2.25rem] border border-amber-900/18 bg-gradient-to-br from-[#faf7f0] via-[#f7f2e7] to-[#f4ebe1] p-6 sm:p-8 shadow-sm">
        {{-- Watermark chữ Hán mờ --}}
        <div class="pointer-events-none absolute -right-6 -bottom-8 select-none text-[9rem] font-serif font-black leading-none text-amber-900/[0.04]">
            雅斋
        </div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div class="space-y-2 max-w-xl">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="seal-zhusha-round text-xs font-bold tracking-widest">
                        欢 迎 归 来 · HOAN NGHÊNH
                    </span>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-100/80 border border-amber-300/60 px-3 py-0.5 text-xs font-semibold text-amber-900">
                        <span>{{ $timeGreeting }}</span>
                    </span>
                </div>

                <h1 class="text-3xl sm:text-4xl font-black tracking-tight text-slate-900">
                    Chào mừng trở về thư phòng{{ $student ? ', ' . $student->name : '' }}.
                </h1>

                <p class="text-sm sm:text-base text-slate-700 leading-relaxed">
                    Hôm nay, ta tiếp tục hành trình. Tĩnh tâm ngồi bên án thư, tích lũy từng chữ Hán như tích tụ từng giọt sương mai.
                </p>

                {{-- Con Dấu Học Tịch (学籍) & Điểm Tích Lũy --}}
                <div class="flex flex-wrap items-center gap-3 pt-2 text-xs">
                    <span class="hoc-tich-seal">
                        <span>学籍</span>
                        <span>•</span>
                        <span>{{ $hocVi['hanzi'] }} ({{ $hocVi['rank'] }})</span>
                    </span>
                    <span class="text-slate-400">•</span>
                    <span class="text-slate-600 font-medium">
                        Tích lũy XP: <strong class="text-amber-950 font-bold">{{ number_format($totalXp) }} XP</strong>
                    </span>
                    <span class="text-slate-400">•</span>
                    <span class="text-slate-600 font-medium">
                        Mục tiêu hôm nay: <strong class="text-[#991b1b] font-bold">{{ $dailyGoalPercent }}%</strong>
                    </span>
                </div>
            </div>

            {{-- Thư Tiết Can Chi & Mùa Màng --}}
            <div class="shrink-0 text-left md:text-right border-t md:border-t-0 md:border-l border-amber-900/15 pt-3 md:pt-0 md:pl-6 space-y-1">
                <p class="text-xs uppercase tracking-widest text-amber-900/70 font-bold">
                    Tiết Khí Thư Phòng
                </p>
                <p class="text-xl font-black text-slate-900 tracking-wider">
                    丙午 · 暮秋
                </p>
                <p class="text-xs text-slate-500 font-medium">
                    Ngày {{ now()->format('d/m/Y') }} · Thu Thư Phòng
                </p>
            </div>
        </div>
    </header>


    {{-- ==============================================================
         2. CENTERPIECE: LƯ TRẦM (香炉) & BẠN ĐỒNG HÀNH BÊN ÁN THƯ
         ============================================================== --}}
    <section class="nha-trai-room p-6 sm:p-8">
        <div class="grid gap-6 md:grid-cols-[1.1fr_1.3fr] items-center">
            
            {{-- Vùng Lư Trầm (香炉 Altar) --}}
            <div class="flex flex-col items-center text-center p-5 rounded-3xl bg-white/70 border border-amber-900/15 shadow-2xs">
                
                {{-- Lư Trầm SVG với Làn Khói Uyển Chuyển --}}
                <div class="incense-burner-altar relative mb-2">
                    <svg class="h-28 w-28 sm:h-32 sm:w-32 drop-shadow-sm select-none" viewBox="0 0 120 120" fill="none" xmlns="http://www.w3.org/2000/svg">
                        {{-- Làn khói trầm uốn lượn bay lên --}}
                        @if($streakDays > 0)
                        <g class="smoke-stream-1" opacity="0.75">
                            <path d="M 60 45 Q 56 32 63 22 Q 70 12 58 2" stroke="rgba(180, 83, 9, 0.45)" stroke-width="2.5" stroke-linecap="round" fill="none" />
                        </g>
                        <g class="smoke-stream-2" opacity="0.65">
                            <path d="M 60 45 Q 65 34 57 24 Q 49 14 62 4" stroke="rgba(245, 158, 11, 0.4)" stroke-width="2" stroke-linecap="round" fill="none" />
                        </g>
                        @else
                        {{-- Khói mỏng nhẹ khi chưa thắp --}}
                        <g opacity="0.25">
                            <path d="M 60 46 Q 62 38 60 30" stroke="rgba(148, 163, 184, 0.5)" stroke-width="1.5" stroke-dasharray="2 2" stroke-linecap="round" fill="none" />
                        </g>
                        @endif

                        {{-- Núm nắp quả hồ lô --}}
                        <circle cx="60" cy="46" r="4" fill="#92400e" stroke="#78350f" stroke-width="1.5" />
                        {{-- Vòm nắp khum hình bán cầu với các lỗ thoát khói --}}
                        <path d="M 45 60 C 45 49 75 49 75 60 Z" fill="#b45309" stroke="#78350f" stroke-width="2" />
                        <circle cx="53" cy="55" r="1.5" fill="#fef3c7" opacity="0.9" />
                        <circle cx="60" cy="53" r="1.5" fill="#fef3c7" opacity="0.9" />
                        <circle cx="67" cy="55" r="1.5" fill="#fef3c7" opacity="0.9" />

                        {{-- Miệng và thân vạc bầu lư trầm --}}
                        <rect x="42" y="60" width="36" height="5" rx="2.5" fill="#78350f" />
                        <path d="M 44 65 C 44 86 76 86 76 65 Z" fill="url(#burnerGradient)" stroke="#78350f" stroke-width="2" />
                        <path d="M 52 74 C 56 71 64 71 68 74" stroke="#fef3c7" stroke-width="1.5" stroke-linecap="round" opacity="0.6" />
                        <circle cx="60" cy="73" r="1" fill="#fef3c7" opacity="0.8" />

                        {{-- Hai tai quai cong nhã nhặn --}}
                        <path d="M 44 67 C 36 67 36 78 45 78" stroke="#78350f" stroke-width="2.5" stroke-linecap="round" fill="none" />
                        <path d="M 76 67 C 84 67 84 78 75 78" stroke="#78350f" stroke-width="2.5" stroke-linecap="round" fill="none" />

                        {{-- Ba Chân Vạc Tam Túc --}}
                        <path d="M 48 83 L 45 94 C 45 96 48 96 49 94 L 52 84 Z" fill="#78350f" />
                        <path d="M 72 83 L 75 94 C 75 96 72 96 71 94 L 68 84 Z" fill="#78350f" />
                        <path d="M 58 84 L 60 92 L 62 84 Z" fill="#5c2605" />

                        {{-- Đế gỗ gụ thư án --}}
                        <ellipse cx="60" cy="95" rx="22" ry="4" fill="#451a03" opacity="0.8" />
                        <ellipse cx="60" cy="96" rx="25" ry="3" fill="#1c1917" opacity="0.3" />

                        <defs>
                            <linearGradient id="burnerGradient" x1="44" y1="65" x2="76" y2="85" gradientUnits="userSpaceOnUse">
                                <stop stop-color="#b45309" />
                                <stop offset="0.5" stop-color="#d97706" />
                                <stop offset="1" stop-color="#78350f" />
                            </linearGradient>
                        </defs>
                    </svg>
                </div>

                {{-- Dòng chữ Hương Hỏa Vị Đoạn --}}
                <div class="space-y-1">
                    <div class="flex items-center justify-center gap-2">
                        <span class="text-xs uppercase tracking-widest font-bold text-amber-900 font-serif">
                            香 炉 · LƯ TRẦM
                        </span>
                        @if(!empty($burnerState['has_seal']))
                            <span class="seal-zhusha-round text-[10px]">久习</span>
                        @endif
                    </div>

                    <div class="flex items-baseline justify-center gap-1.5" title="{{ $streakDays }} ngày liên tiếp">
                        <span class="text-3xl font-black text-slate-900 font-serif">{{ $streakDays }}</span>
                        <span class="text-sm font-bold text-amber-900">ngày</span>
                        <span class="text-xs font-serif text-slate-400">(日)</span>
                        <span class="sr-only">{{ $streakDays }} ngày</span>
                    </div>

                    <p class="text-xs font-bold text-[#991b1b]">
                        {{ $streakDays > 0 ? '香火未断 · Hương hỏa vị đoạn' : '香火渐冷 · Hương hỏa tiệm lãnh' }}
                    </p>

                    <p class="text-xs text-slate-600 max-w-xs mt-1 leading-relaxed">
                        {{ $burnerState['desc'] }}
                    </p>
                </div>
            </div>

            {{-- Thư Đồng Đồng Viện (Pet Companion bên Án Thư) --}}
            <div class="flex flex-col justify-between p-5 rounded-3xl bg-white/70 border border-amber-900/15 shadow-2xs h-full"
                 x-data="{
                     dialogue: {{ Js::from($petData['randomDialogue'] ?? '你回来啦。Chào bạn đã về nhã trai, ta cùng học thêm một chút nhé!') }},
                     speakDialogue() {
                         if (window.playChineseVoice) {
                             window.playChineseVoice(this.dialogue);
                         }
                     }
                 }">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="seal-zhusha text-xs">伴读书童</span>
                            <span class="text-xs font-bold text-slate-700">Thư Đồng {{ $petDefaultName }}</span>
                        </div>
                        <span class="text-[11px] font-semibold text-amber-900 bg-amber-100/70 px-2.5 py-0.5 rounded-full">
                            {{ match($userPetObj?->getHungerState() ?? 'happy') {
                                'happy' => '😊 Đang rất vui',
                                'hungry' => '🙂 Hơi đói bụng',
                                'very_hungry' => '😟 Cần dâng trà',
                                'dormant' => '💤 Đang ngủ say',
                                default => '✨ Bạn đồng hành'
                            } }}
                        </span>
                    </div>

                    <div class="flex items-start gap-4 pt-1">
                        {{-- Pet Avatar bên án thư --}}
                        <a href="{{ route('pet.index') }}" class="shrink-0 group">
                            <div class="grid h-16 w-16 place-items-center rounded-2xl bg-[#faf7f0] border border-amber-300 shadow-2xs group-hover:scale-105 transition p-1">
                                @if($userPetObj)
                                    <x-pet-avatar :stage="$userPetObj->stage" :mood="$userPetObj->getHungerState()" size="sm" :interactive="false" />
                                @else
                                    <span class="text-3xl select-none">🥚</span>
                                @endif
                            </div>
                        </a>

                        {{-- Lời thì thầm của Thư Đồng --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-xs font-bold text-slate-900 truncate">
                                        {{ $petDefaultName }}
                                    </h3>
                                    <p class="text-[11px] text-slate-500 font-medium">
                                        Giai đoạn {{ $petStageNumber }}: {{ $petStageName }}
                                    </p>
                                </div>
                                <button type="button" @click="speakDialogue()"
                                    class="rounded-lg p-1 text-slate-400 hover:text-amber-800 hover:bg-amber-100 transition"
                                    title="Nghe phát âm tiếng Trung">
                                    <i data-lucide="volume-2" class="h-3.5 w-3.5"></i>
                                </button>
                            </div>
                            <p class="mt-1 text-xs sm:text-sm text-slate-700 leading-relaxed italic line-clamp-3 cursor-pointer hover:text-amber-900"
                                @click="speakDialogue()">
                                "<span x-text="dialogue"></span>"
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Nút Ghé Thăm Phòng Pet --}}
                <div class="mt-4 pt-3 border-t border-amber-900/10 flex items-center justify-between">
                    <span class="text-xs text-slate-500 font-medium">Thư phòng là mái nhà chung</span>
                    <a href="{{ route('pet.index') }}"
                       class="inline-flex items-center gap-1.5 text-xs font-bold text-[#991b1b] hover:underline">
                        <span>Thăm phòng Pet</span>
                        <i data-lucide="arrow-right" class="h-3 w-3"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>


    {{-- ==============================================================
         3. TAM ĐẠI CÔNG PHU (三大功夫) — TRUNG TÂM HÀNH ĐỘNG
         ============================================================== --}}
    <section class="space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div class="flex items-center gap-2.5">
                <span class="seal-zhusha text-xs">三大功夫</span>
                <div>
                    <h2 class="text-xl sm:text-2xl font-black text-slate-900">
                        Tam Đại Công Phu · Việc Nên Làm Hôm Nay
                    </h2>
                    <p class="text-xs text-slate-500">Ba phép luyện tâm hằng ngày: Khai Thư — Ôn Cố — Luận Kiếm</p>
                </div>
            </div>
            <span class="text-xs font-semibold text-amber-900/80 hidden sm:block">
                Học sâu · Nhớ lâu · Đắc chuẩn
            </span>
        </div>

        <div class="grid gap-4 sm:grid-cols-3">
            {{-- Công Phu 1: Khai Thư Nhập Đạo (Học bài mới) --}}
            <div class="tam-dai-card p-5 flex flex-col justify-between">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="seal-zhusha text-xs">
                            ① 开书 · Nhập học
                        </span>
                        @if($hasCurrentLesson && !empty($currentLessonHsk))
                        <span class="rounded-full bg-amber-100/80 px-2.5 py-0.5 text-[11px] font-bold text-amber-900">
                            HSK {{ $currentLessonHsk }}
                        </span>
                        @endif
                    </div>

                    <div>
                        <h3 class="text-base font-black text-slate-900 leading-snug">
                            {{ $currentLessonTitle }}
                        </h3>
                        <p class="text-xs text-slate-600 mt-1 line-clamp-2 leading-relaxed">
                            {{ $currentLessonSummary ?: 'Đọc bài khóa, nắm vững kết cấu ngữ pháp và mở rộng tầm hiểu biết.' }}
                        </p>
                    </div>

                    @if($hasCurrentLesson)
                    <div class="pt-1">
                        <div class="flex justify-between text-[11px] text-slate-500 font-semibold mb-1">
                            <span>Tiến độ bài học</span>
                            <span class="text-[#991b1b] font-bold">{{ $currentLessonPercent }}%</span>
                        </div>
                        <div class="h-1.5 w-full overflow-hidden rounded-full bg-slate-100">
                            <div class="h-full rounded-full bg-gradient-to-r from-amber-400 to-[#991b1b]"
                                style="width: {{ $currentLessonPercent }}%"></div>
                        </div>
                    </div>
                    @endif
                </div>

                <div class="mt-5 pt-3 border-t border-amber-900/10">
                    <a href="{{ $currentLessonSlug ? route('lesson.show', ['slug' => $currentLessonSlug]) : route('hsk.overview') }}"
                       class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-[#991b1b] hover:bg-red-800 text-white px-4 py-2.5 text-xs font-bold shadow-md shadow-red-950/15 transition active:scale-95">
                        <span>{{ $hasCurrentLesson ? 'Tiếp tục bài học' : 'Khai mở bài mới' }}</span>
                        <i data-lucide="arrow-right" class="h-3.5 w-3.5"></i>
                    </a>
                </div>
            </div>

            {{-- Công Phu 2: Ôn Cố Tri Tân (Ôn từ vựng Flashcard) --}}
            <div class="tam-dai-card p-5 flex flex-col justify-between">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="seal-zhusha text-xs">
                            ② 温故 · Ôn tập
                        </span>
                        <span class="text-[11px] font-bold text-amber-900 bg-amber-100/70 px-2 py-0.5 rounded-full">
                            Thư Thiêm SRS
                        </span>
                    </div>

                    <div>
                        <div class="flex items-baseline gap-2">
                            <span class="text-3xl font-black text-slate-900">{{ $dueFlashcardsCount }}</span>
                            <span class="text-xs font-bold text-slate-600">từ đến hạn ôn</span>
                        </div>
                        <p class="text-xs text-slate-600 mt-1 line-clamp-2 leading-relaxed">
                            {{ $dueFlashcardsCount > 0 ? 'Các từ vựng đã đến chu kỳ ôn tập ngắt quãng để khắc sâu vào trí nhớ dài hạn.' : 'Bạn đã ôn hết từ vựng hôm nay. Thư phòng thanh thản, tâm trí an nhiên.' }}
                        </p>
                    </div>

                    @if(isset($starredCount) && $starredCount > 0)
                    <div class="pt-1 text-[11px] text-amber-900">
                        <a href="{{ route('flashcards', ['filter' => 'starred']) }}" class="inline-flex items-center gap-1 hover:underline font-medium">
                            <i data-lucide="star" class="h-3 w-3 fill-amber-400 text-amber-500"></i>
                            <span>Sổ từ vựng đã lưu: <strong>{{ $starredCount }} từ</strong></span>
                        </a>
                    </div>
                    @endif
                </div>

                <div class="mt-5 pt-3 border-t border-amber-900/10">
                    <a href="{{ route('flashcards', $currentLessonSlug ? ['lesson' => $currentLessonSlug] : []) }}"
                       class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-amber-900/20 bg-amber-50/80 hover:bg-amber-100 text-amber-950 px-4 py-2.5 text-xs font-bold shadow-2xs transition active:scale-95">
                        <span>{{ $dueFlashcardsCount > 0 ? 'Khắc sâu Thư Thiêm' : 'Xem Thư Thiêm' }}</span>
                        <i data-lucide="bookmark" class="h-3.5 w-3.5 text-amber-800"></i>
                    </a>
                </div>
            </div>

            {{-- Công Phu 3: Luận Kiếm Thí Tài (Luyện Quiz / Khoa Cử) --}}
            <div class="tam-dai-card p-5 flex flex-col justify-between">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="seal-zhusha text-xs">
                            ③ 试才 · Thí tài
                        </span>
                        <span class="text-[11px] font-bold text-emerald-800 bg-emerald-100/70 px-2 py-0.5 rounded-full">
                            Khoa Cử
                        </span>
                    </div>

                    <div>
                        <h3 class="text-base font-black text-slate-900 leading-snug">
                            Khoa Cử Trường Thi
                        </h3>
                        <p class="text-xs text-slate-600 mt-1 line-clamp-2 leading-relaxed">
                            Hạ bút giải các đạo thi đề, nhận ấn triện chuẩn xác và vinh danh trên Kim Bảng cá nhân.
                        </p>
                    </div>

                    <div class="pt-1 text-[11px] text-emerald-800 font-semibold flex items-center gap-1">
                        <span>🎁 Thưởng hoàn thành: +15~25 EXP cho Thư Đồng</span>
                    </div>
                </div>

                <div class="mt-5 pt-3 border-t border-amber-900/10">
                    <a href="{{ route('quiz', $currentLessonSlug ? ['lesson' => $currentLessonSlug] : []) }}"
                       class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-slate-950 hover:bg-slate-800 text-white px-4 py-2.5 text-xs font-bold shadow-md transition active:scale-95">
                        <span>Lên điện Thí Tài</span>
                        <i data-lucide="target" class="h-3.5 w-3.5"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>


    {{-- ==============================================================
         4. HỌC TRÌNH: GIANG SƠN ĐỒ MINI (学途 · 千里江山)
         ============================================================== --}}
    <section class="rounded-3xl border border-amber-900/18 bg-white/80 p-5 sm:p-6 shadow-2xs">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
            <div class="flex items-center gap-2">
                <span class="seal-zhusha text-xs">学途</span>
                <div>
                    <h3 class="text-base font-black text-slate-900">
                        Học Trình Giang Sơn Đồ · 千里江山
                    </h3>
                    <p class="text-xs text-slate-500">
                        Đã đi qua <strong class="text-[#991b1b]">{{ $completedLessonsCount }}</strong> / {{ $totalLessonsCount }} dịch trạm trên toàn bộ hành trình
                    </p>
                </div>
            </div>

            <a href="{{ route('hsk.overview') }}"
               class="inline-flex items-center gap-1 text-xs font-bold text-[#991b1b] hover:underline self-start sm:self-center">
                <span>Mở toàn cảnh Giang Sơn Đồ</span>
                <i data-lucide="arrow-right" class="h-3 w-3"></i>
            </a>
        </div>

        {{-- Dòng suối dịch trạm mini --}}
        <div class="relative py-3">
            <div class="flex items-center justify-between text-xs font-bold text-slate-600 mb-2">
                <span>Khởi đầu (HSK 1)</span>
                <span class="text-amber-900">Tiến độ chung: {{ $completionRate }}% 已行</span>
                <span>Đỉnh cao (HSK 6)</span>
            </div>

            {{-- Đồ họa dịch trạm thanh nhã --}}
            <div class="relative flex items-center justify-between px-3">
                <div class="absolute left-4 right-4 h-0.5 bg-amber-900/15 top-1/2 -translate-y-1/2"></div>
                
                @foreach([1, 2, 3, 4, 5, 6] as $lvl)
                    @php
                        $lvlCompleted = $lessons->where('hsk_level', $lvl)->where('status', 'completed')->count();
                        $lvlTotal = $lessons->where('hsk_level', $lvl)->count();
                        $isPassed = $lvlTotal > 0 && $lvlCompleted === $lvlTotal;
                        $isCurrent = $currentLessonHsk == $lvl;
                    @endphp
                    <div class="relative z-10 flex flex-col items-center">
                        <div class="grid h-8 w-8 place-items-center rounded-xl border text-xs font-bold transition {{ $isPassed ? 'bg-[#991b1b] text-white border-[#991b1b] shadow-xs' : ($isCurrent ? 'bg-amber-100 text-amber-950 border-amber-400 ring-2 ring-amber-300' : 'bg-white text-slate-400 border-amber-900/20') }}">
                            @if($isPassed)
                                <span>✓</span>
                            @else
                                <span>{{ $lvl }}</span>
                            @endif
                        </div>
                        <span class="text-[10px] mt-1 font-semibold text-slate-500">
                            HSK {{ $lvl }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    </section>


    {{-- ==============================================================
         5. TIỂU KÝ HỌC TẬP (学习小记) & NHỊP TUẦN 7 NGÀY
         ============================================================== --}}
    <div class="grid gap-5 md:grid-cols-2">

        {{-- Khối 1: Nhịp Thư Phòng 7 Ngày (T2 - CN) --}}
        <div class="rounded-3xl border border-amber-900/18 bg-white/80 p-5 sm:p-6 shadow-2xs flex flex-col justify-between">
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="seal-zhusha text-xs">七日律 · NHỊP 7 NGÀY</span>
                    <span class="text-xs text-slate-500 font-medium">Thói quen bền vững</span>
                </div>

                <p class="text-xs text-slate-600 leading-relaxed">
                    Mỗi ngày một chút hương thơm, nhịp học tuần hoàn nuôi dưỡng tâm trí.
                </p>

                {{-- 7 Hạt Ngọc Nhịp Tuần --}}
                <div class="grid grid-cols-7 gap-1.5 text-center pt-2">
                    @foreach($weeklyRhythm as $point)
                    <div class="flex flex-col items-center gap-1.5">
                        <span class="text-[11px] font-bold {{ $point['is_today'] ? 'text-[#991b1b]' : 'text-slate-400' }}">
                            {{ $point['day'] }}
                        </span>

                        <div class="relative flex items-center justify-center">
                            @if($point['has_study'])
                            <div class="h-8 w-8 rounded-full bg-amber-600 text-white shadow-xs flex items-center justify-center transition"
                                title="{{ $point['day'] }}: Có học ({{ $point['minutes'] }} phút)">
                                <i data-lucide="check" class="h-4 w-4 stroke-[2.5]"></i>
                            </div>
                            @elseif($point['is_today'])
                            <div class="h-8 w-8 rounded-full border-2 border-dashed border-[#991b1b] bg-red-50/50 flex items-center justify-center text-xs font-bold text-[#991b1b]"
                                title="Hôm nay: Đang đợi thắp hương">
                                •
                            </div>
                            @else
                            <div class="h-8 w-8 rounded-full bg-slate-100 flex items-center justify-center text-xs text-slate-300"
                                title="{{ $point['day'] }}">
                                •
                            </div>
                            @endif
                        </div>

                        <span class="text-[10px] text-slate-400">
                            {{ $point['is_today'] ? 'Nay' : '' }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="mt-4 pt-3 border-t border-amber-900/10 flex items-center justify-between text-xs text-slate-500">
                <span class="flex items-center gap-1.5">
                    <span class="h-2 w-2 rounded-full bg-amber-600"></span>
                    <span>Ngày thắp hương hỏa</span>
                </span>
                <span>Học đều đặn hơn học dồn.</span>
            </div>
        </div>

        {{-- Khối 2: Tiểu Ký Thư Phòng (学习小记) --}}
        <div class="rounded-3xl border border-amber-900/18 bg-white/80 p-5 sm:p-6 shadow-2xs flex flex-col justify-between">
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="seal-zhusha text-xs">学习小记 · TIỂU KÝ</span>
                    <span class="text-xs text-slate-400 font-medium">Ghi chép gần đây</span>
                </div>

                <div class="space-y-3 pt-1">
                    @forelse($activities->take(3) as $act)
                    <div class="flex items-start gap-3 text-xs leading-relaxed">
                        <span class="tieu-ky-dot mt-1"></span>
                        <div class="min-w-0 flex-1">
                            <p class="font-bold text-slate-900 truncate">
                                {{ $act['title'] }}
                            </p>
                            <p class="text-slate-500">
                                {{ $act['time'] }} • {{ $act['description'] }}
                            </p>
                        </div>
                    </div>
                    @empty
                    <div class="py-4 text-center text-xs text-slate-500">
                        <p>Thư phòng đang đợi bạn hạ nét bút đầu tiên hôm nay.</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <div class="mt-4 pt-3 border-t border-amber-900/10 text-xs text-slate-500 flex items-center justify-between">
                <span>Ghi dấu chân trên từng trang sách</span>
                <span class="font-bold text-amber-900">Nhã Trai An Nhiên</span>
            </div>
        </div>
    </div>


    {{-- ==============================================================
         6. THỐNG KÊ CHI TIẾT (学习记录 · HỌC TRÌNH ĐẶT Ở DƯỚI CÙNG)
         ============================================================== --}}
    <section class="rounded-3xl border border-amber-900/15 bg-[#faf8f5] p-6 shadow-2xs">
        <div class="border-b border-amber-900/10 pb-3 mb-4 flex items-center justify-between">
            <div>
                <span class="seal-zhusha text-xs">学习记录</span>
                <h3 class="text-base font-black text-slate-900 mt-1">
                    Học Trình Toàn Cảnh (Dữ liệu học tập)
                </h3>
            </div>
            <p class="text-xs text-slate-500 hidden sm:block">Phần trên = Cảm xúc · Phần giữa = Hành động · Phần dưới = Dữ liệu</p>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-center">
            <div class="rounded-2xl border border-amber-900/10 bg-white/70 p-3.5">
                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Thời Lượng Hôm Nay</p>
                <p class="text-xl font-black text-slate-900 mt-0.5">{{ $todayMinutes }} <span class="text-xs font-normal text-slate-500">phút</span></p>
            </div>
            <div class="rounded-2xl border border-amber-900/10 bg-white/70 p-3.5">
                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Điểm Quiz Bình Quân</p>
                <p class="text-xl font-black text-emerald-800 mt-0.5">{{ $averageScore }}%</p>
            </div>
            <div class="rounded-2xl border border-amber-900/10 bg-white/70 p-3.5">
                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Bài Đã Tốt Nghiệp</p>
                <p class="text-xl font-black text-[#991b1b] mt-0.5">{{ $completedLessonsCount }}/{{ $totalLessonsCount }}</p>
            </div>
            <div class="rounded-2xl border border-amber-900/10 bg-white/70 p-3.5">
                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Từ Vựng Đến Hạn</p>
                <p class="text-xl font-black text-amber-900 mt-0.5">{{ $dueFlashcardsCount }} <span class="text-xs font-normal text-slate-500">từ</span></p>
            </div>
        </div>
    </section>

</div>
@endsection