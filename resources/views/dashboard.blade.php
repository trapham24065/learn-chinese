@extends('layouts.app')

@section('title', 'Trang chủ | Learn Chinese')

@section('content')
<div class="max-w-4xl mx-auto space-y-8 py-2 sm:py-4">

    {{-- Header: Nhẹ nhàng, cảm xúc --}}
    <header class="space-y-2">
        <p class="text-xs font-bold uppercase tracking-[0.2em] text-[#991b1b]">
            Chào mừng trở lại{{ $student ? ', ' . $student->name : '' }}!
        </p>
        <h1 class="text-3xl sm:text-4xl font-black tracking-tight text-slate-900">
            <span class="brush-underline">Hôm nay mình học gì nhỉ?</span>
        </h1>
        <p class="text-sm sm:text-base text-slate-600">
            Tiếp tục từ nơi bạn đã dừng lại. Học một chút mỗi ngày để tích lũy dài lâu.
        </p>

        {{-- Daily Goal & XP Indicators (Minimalist) --}}
        <div class="flex flex-wrap items-center gap-3 pt-1 text-xs text-slate-500">
            <span class="inline-flex items-center gap-1.5 font-semibold text-slate-700">
                <i data-lucide="target" class="h-3.5 w-3.5 text-[#991b1b]"></i>
                <span>Mục tiêu hôm nay: <strong class="text-slate-900">{{ $dailyGoal['progress_percent'] ?? 0 }}%</strong></span>
            </span>
            <span class="text-slate-300">•</span>
            <span class="inline-flex items-center gap-1.5 font-semibold text-slate-700">
                <i data-lucide="sparkles" class="h-3.5 w-3.5 text-amber-500"></i>
                <span>Tích lũy XP: <strong class="text-slate-900">{{ number_format($totalXp) }} XP</strong></span>
            </span>
        </div>
    </header>

    {{-- Chuỗi Nhịp Học 3 Bước (Tam Bộ Học Tập 三步学): Liên kết chặt chẽ Học - Luyện - Khảo --}}
    <section class="relative overflow-hidden rounded-[2.5rem] border border-amber-900/15 bg-gradient-to-br from-white via-[#fcf8f2] to-amber-50/50 p-6 sm:p-7 shadow-sm chinese-frame-corners">
        <span class="chinese-watermark right-6 top-1/2 -translate-y-1/2 text-8xl text-amber-900/5 select-none pointer-events-none">道</span>

        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-5">
            <div class="flex items-center gap-2">
                <span class="chinese-seal px-2 py-0.5 text-xs font-bold">【三步学】</span>
                <div>
                    <h2 class="text-base font-black text-slate-900 tracking-tight">Vòng lặp học tập hôm nay</h2>
                    <p class="text-xs text-slate-500">Hoàn thành 3 bước để tiếp thu trọn vẹn và tích lũy thức ăn cho Pet</p>
                </div>
            </div>
            <span class="rounded-full bg-amber-100/80 border border-amber-200 px-3 py-1 text-[11px] font-bold text-amber-900 flex items-center gap-1.5">
                <i data-lucide="sparkles" class="h-3.5 w-3.5 text-amber-600"></i>
                <span>Chuỗi học tập khép kín</span>
            </span>
        </div>

        <div class="grid gap-3 sm:grid-cols-3">
            {{-- Bước 1: Khám phá bài học --}}
            <a href="{{ $currentLesson ? route('lesson.show', ['slug' => $currentLesson['slug']]) : route('hsk.overview') }}"
               class="group relative flex flex-col justify-between rounded-2xl border border-red-200/80 bg-white/95 p-4 transition-all hover:shadow-md hover:border-[#991b1b] hover:-translate-y-0.5">
                <div class="flex items-center justify-between">
                    <span class="inline-flex items-center gap-1 text-[11px] font-black uppercase tracking-wider text-[#991b1b]">
                        <span class="chinese-seal px-1 py-0.2 text-[9px]">【学】</span>
                        <span>1. Khám phá</span>
                    </span>
                    <i data-lucide="arrow-right" class="h-3.5 w-3.5 text-slate-400 group-hover:text-[#991b1b] group-hover:translate-x-0.5 transition"></i>
                </div>
                <div class="mt-2.5">
                    <p class="text-xs text-slate-400 font-semibold">Bài học hiện tại</p>
                    <p class="text-sm font-black text-slate-900 truncate mt-0.5 group-hover:text-[#991b1b] transition">
                        {{ $currentLesson['title'] ?? 'Chọn bài trong Lộ trình HSK' }}
                    </p>
                    <p class="text-[11px] text-slate-500 mt-1">Đọc bài khóa & ngữ pháp</p>
                </div>
            </a>

            {{-- Bước 2: Khắc sâu từ vựng --}}
            <a href="{{ route('flashcards', $currentLesson ? ['lesson' => $currentLesson['slug']] : []) }}"
               class="group relative flex flex-col justify-between rounded-2xl border border-amber-200/80 bg-white/95 p-4 transition-all hover:shadow-md hover:border-amber-500 hover:-translate-y-0.5">
                <div class="flex items-center justify-between">
                    <span class="inline-flex items-center gap-1 text-[11px] font-black uppercase tracking-wider text-amber-800">
                        <span class="chinese-seal-gold px-1 py-0.2 text-[9px]">【记】</span>
                        <span>2. Khắc sâu</span>
                    </span>
                    <i data-lucide="arrow-right" class="h-3.5 w-3.5 text-slate-400 group-hover:text-amber-600 group-hover:translate-x-0.5 transition"></i>
                </div>
                <div class="mt-2.5">
                    <p class="text-xs text-slate-400 font-semibold">Flashcard 3D</p>
                    <p class="text-sm font-black text-slate-900 truncate mt-0.5 group-hover:text-amber-700 transition">
                        {{ $dueFlashcardsCount > 0 ? $dueFlashcardsCount . ' từ đến hạn ôn' : 'Luyện thẻ bài học' }}
                    </p>
                    <p class="text-[11px] text-slate-500 mt-1">Lật thẻ & trau chuốt phát âm</p>
                </div>
            </a>

            {{-- Bước 3: Thử thách Quiz & Thưởng Pet --}}
            <a href="{{ route('quiz', $currentLesson ? ['lesson' => $currentLesson['slug']] : []) }}"
               class="group relative flex flex-col justify-between rounded-2xl border border-rose-200/80 bg-white/95 p-4 transition-all hover:shadow-md hover:border-rose-500 hover:-translate-y-0.5">
                <div class="flex items-center justify-between">
                    <span class="inline-flex items-center gap-1 text-[11px] font-black uppercase tracking-wider text-rose-800">
                        <span class="chinese-seal px-1 py-0.2 text-[9px]">【考】</span>
                        <span>3. Phản xạ & Pet</span>
                    </span>
                    <i data-lucide="arrow-right" class="h-3.5 w-3.5 text-slate-400 group-hover:text-rose-600 group-hover:translate-x-0.5 transition"></i>
                </div>
                <div class="mt-2.5">
                    <p class="text-xs text-slate-400 font-semibold">Quiz Đố vui</p>
                    <p class="text-sm font-black text-slate-900 truncate mt-0.5 group-hover:text-rose-700 transition">
                        Làm Quiz kiểm tra
                    </p>
                    <p class="text-[11px] text-rose-600 font-semibold mt-1">🎁 Nhận +15 EXP & thức ăn cho Pet</p>
                </div>
            </a>
        </div>
    </section>

    {{-- Main 4-Card Minimalist Grid --}}
    <div class="grid gap-5 sm:grid-cols-2">

        {{-- Card 1: HỌC TIẾP --}}
        <div class="relative overflow-hidden rounded-3xl border border-slate-200/90 bg-white p-6 shadow-sm hover:shadow-md transition flex flex-col justify-between chinese-frame-corners">
            <span class="chinese-watermark right-4 bottom-4 text-8xl text-red-950/5 select-none pointer-events-none">学</span>

            <div class="space-y-3 z-10">
                <div class="flex items-center justify-between">
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-red-50 border border-red-200/70 px-3 py-1 text-xs font-bold uppercase tracking-wider text-[#991b1b]">
                        <span class="chinese-seal px-1 py-0.2 text-[9px] font-bold">【学】</span>
                        <span>Học tiếp</span>
                    </span>
                    @if($currentLesson && !empty($currentLesson['hsk_level']))
                    <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-bold text-slate-600">
                        HSK {{ $currentLesson['hsk_level'] }}
                    </span>
                    @endif
                </div>

                <div>
                    @if($currentLesson)
                    <h2 class="text-xl font-black text-slate-900 leading-snug">
                        {{ $currentLesson['title'] }}
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1 line-clamp-2">
                        {{ $currentLesson['summary'] ?: 'Tiếp tục bài học của bạn và hoàn thành các câu đố vui.' }}
                    </p>

                    {{-- Progress Bar --}}
                    <div class="mt-4">
                        <div class="flex justify-between text-xs text-slate-500 font-semibold mb-1">
                            <span>Tiến độ bài học</span>
                            <span class="text-[#991b1b] font-bold">{{ $currentLesson['progress_percent'] ?? 0 }}%</span>
                        </div>
                        <div class="h-2 w-full overflow-hidden rounded-full bg-slate-100">
                            <div class="h-full rounded-full bg-gradient-to-r from-amber-400 to-[#991b1b] transition-all duration-500"
                                style="width: {{ $currentLesson['progress_percent'] ?? 0 }}%"></div>
                        </div>
                    </div>
                    @else
                    <h2 class="text-xl font-black text-slate-900 leading-snug">
                        Bắt đầu bài học đầu tiên
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1">
                        Khám phá lộ trình học tiếng Trung từ số 0 cùng chúng mình.
                    </p>
                    @endif
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-slate-100">
                @if($currentLesson)
                <a href="{{ route('lesson.show', ['slug' => $currentLesson['slug']]) }}"
                    class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-[#991b1b] px-5 py-3 text-sm font-bold text-white shadow-md shadow-red-950/15 hover:bg-red-800 transition active:scale-[0.98]">
                    <span>Tiếp tục học</span>
                    <i data-lucide="arrow-right" class="h-4 w-4"></i>
                </a>
                @else
                <a href="{{ route('hsk.overview') }}"
                    class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-[#991b1b] px-5 py-3 text-sm font-bold text-white shadow-md shadow-red-950/15 hover:bg-red-800 transition active:scale-[0.98]">
                    <span>Xem lộ trình HSK</span>
                    <i data-lucide="arrow-right" class="h-4 w-4"></i>
                </a>
                @endif
            </div>
        </div>

        {{-- Card 2: MỘT CHÚT ÔN TẬP --}}
        <div class="relative overflow-hidden rounded-3xl border border-slate-200/90 bg-white p-6 shadow-sm hover:shadow-md transition flex flex-col justify-between chinese-frame-corners">
            <span class="chinese-watermark right-4 bottom-4 text-8xl text-amber-900/5 select-none pointer-events-none">知</span>

            <div class="space-y-3 z-10">
                <div class="flex items-center justify-between">
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 border border-amber-200/70 px-3 py-1 text-xs font-bold uppercase tracking-wider text-amber-800">
                        <span class="chinese-seal-gold px-1 py-0.2 text-[9px] font-bold">【温】</span>
                        <span>Một chút ôn tập</span>
                    </span>
                    <span class="text-xs font-semibold text-slate-400">Hôm nay</span>
                </div>

                <div>
                    @if($dueFlashcardsCount > 0)
                    <div class="flex items-baseline gap-2">
                        <span class="text-4xl font-black text-slate-900">{{ $dueFlashcardsCount }}</span>
                        <span class="text-sm font-bold text-slate-600">từ vựng</span>
                    </div>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1">
                        Đang chờ bạn ôn lại theo chu kỳ trí nhớ ngắt quãng (SRS).
                    </p>
                    @else
                    <div class="flex items-center gap-2 text-emerald-600">
                        <i data-lucide="circle-check-big" class="h-6 w-6"></i>
                        <span class="text-2xl font-black">Tuyệt vời!</span>
                    </div>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1">
                        Bạn đã hoàn thành toàn bộ từ vựng cần ôn hôm nay. Bạn có thể tự do xem lại các thẻ đã lưu bất cứ lúc nào.
                    </p>
                    @endif

                    @if(isset($starredCount) && $starredCount > 0)
                    <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-600">
                        <a href="{{ route('flashcards', ['filter' => 'starred']) }}" class="inline-flex items-center gap-1 hover:text-[#991b1b] transition font-medium">
                            <i data-lucide="star" class="h-3.5 w-3.5 text-amber-500 fill-amber-400"></i>
                            <span>Sổ từ vựng đã lưu: <strong class="text-slate-900">{{ $starredCount }} từ</strong></span>
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-slate-100 z-10">
                <a href="{{ route('flashcards') }}"
                    class="inline-flex w-full items-center justify-center gap-2 rounded-2xl border border-amber-300 bg-amber-50/80 px-5 py-3 text-sm font-bold text-amber-900 hover:bg-amber-100 transition active:scale-[0.98]">
                    <span>{{ $dueFlashcardsCount > 0 ? 'Bắt đầu ôn tập' : 'Xem thẻ từ vựng' }}</span>
                    <i data-lucide="arrow-right" class="h-4 w-4"></i>
                </a>
            </div>
        </div>

        {{-- Card 3: NHỊP HỌC CỦA BẠN (7 Chấm hoạt động T2-CN) --}}
        <div class="relative overflow-hidden rounded-3xl border border-slate-200/90 bg-white p-6 shadow-sm hover:shadow-md transition flex flex-col justify-between chinese-frame-corners">
            <span class="chinese-watermark right-4 bottom-4 text-8xl text-slate-900/5 select-none pointer-events-none">志</span>

            <div class="space-y-4 z-10">
                <div class="flex items-center justify-between">
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-3 py-1 text-xs font-bold uppercase tracking-wider text-slate-700">
                        <span class="chinese-seal px-1 py-0.2 text-[9px] font-bold">【恒】</span>
                        <span>Nhịp học của bạn</span>
                    </span>
                    @if($streakDays > 0)
                    <span class="inline-flex items-center gap-1 text-xs font-black text-amber-600">
                        <i data-lucide="flame" class="h-3.5 w-3.5 fill-current"></i>
                        <span>{{ $streakDays }} ngày</span>
                    </span>
                    @endif
                </div>

                {{-- 7 Chấm Hoạt Động Tuần --}}
                <div class="grid grid-cols-7 gap-1.5 text-center pt-2">
                    @foreach($weeklyRhythm as $point)
                    <div class="flex flex-col items-center gap-2">
                        <span class="text-[11px] font-bold {{ $point['is_today'] ? 'text-[#991b1b]' : 'text-slate-400' }}">
                            {{ $point['day'] }}
                        </span>

                        {{-- Dot status --}}
                        <div class="relative flex items-center justify-center">
                            @if($point['has_study'])
                            <div class="h-8 w-8 rounded-full bg-amber-500 text-white shadow-sm flex items-center justify-center transition"
                                title="{{ $point['day'] }} ({{ $point['date'] }}): Có học ({{ $point['minutes'] }} phút)">
                                <i data-lucide="check" class="h-4 w-4 stroke-[3]"></i>
                            </div>
                            @elseif($point['is_today'])
                            <div class="h-8 w-8 rounded-full border-2 border-dashed border-[#991b1b] bg-red-50/50 flex items-center justify-center text-xs font-bold text-[#991b1b]"
                                title="Hôm nay: Chưa học">
                                •
                            </div>
                            @else
                            <div class="h-8 w-8 rounded-full bg-slate-100 flex items-center justify-center text-xs text-slate-300"
                                title="{{ $point['day'] }} ({{ $point['date'] }})">
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

            <div class="mt-4 pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500 z-10">
                <span class="flex items-center gap-1.5">
                    <span class="h-2 w-2 rounded-full bg-amber-500"></span>
                    <span>Ngày có học</span>
                </span>
                <span>Học đều mỗi ngày tạo nên thói quen bền vững.</span>
            </div>
        </div>

        {{-- Card 4: PET ĐANG Ở ĐÂY --}}
        <div class="relative overflow-hidden rounded-3xl border border-amber-200/80 bg-gradient-to-br from-amber-50/80 via-white to-orange-50/50 p-6 shadow-sm hover:shadow-md transition flex flex-col justify-between chinese-frame-corners"
            x-data="{
                dialogue: {{ Js::from($petData['randomDialogue'] ?? 'Hôm nay chúng ta cùng học tiếng Trung thật vui nhé!') }},
                speakDialogue() {
                    if (window.playChineseVoice) {
                        window.playChineseVoice(this.dialogue);
                    }
                }
            }">
            <span class="chinese-watermark right-4 bottom-4 text-8xl text-amber-900/5 select-none pointer-events-none">龙</span>

            <div class="space-y-3 z-10">
                <div class="flex items-center justify-between">
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-100 border border-amber-300 px-3 py-1 text-xs font-bold uppercase tracking-wider text-amber-900">
                        <span class="chinese-seal px-1 py-0.2 text-[9px] font-bold">【伴】</span>
                        <span>Pet đang ở đây</span>
                    </span>
                    @if(isset($petData) && $petData['userPet'])
                    <span class="text-xs font-semibold text-slate-500">
                        {{ match($petData['userPet']->getHungerState()) {
                            'happy' => '😊 Đang rất vui',
                            'hungry' => '🙂 Hơi đói',
                            'very_hungry' => '😟 Cần ăn',
                            'dormant' => '💤 Đang ngủ',
                            default => '✨ Bạn đồng hành'
                        } }}
                    </span>
                    @endif
                </div>

                <div class="flex items-start gap-4 pt-1">
                    {{-- Pet Avatar nhỏ xinh --}}
                    <a href="{{ route('pet.index') }}" class="shrink-0 group">
                        <div class="grid h-16 w-16 place-items-center rounded-2xl bg-white shadow-sm border border-amber-200 group-hover:scale-105 group-hover:shadow transition p-1">
                            @if(isset($petData) && $petData['userPet'])
                            <x-pet-avatar :stage="$petData['userPet']->stage" :mood="$petData['userPet']->getHungerState()" size="sm" :interactive="false" />
                            @else
                            <span class="text-3xl select-none">🥚</span>
                            @endif
                        </div>
                    </a>

                    {{-- Pet Speech Bubble --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-sm font-black text-slate-900 truncate">
                                    {{ $petData['userPet']->name ?? 'Bạn đồng hành nhỏ' }}
                                </h3>
                                @if(isset($petData) && $petData['userPet'])
                                <p class="text-[11px] text-slate-500 font-medium">
                                    Giai đoạn {{ $petData['userPet']->stage ?? 0 }}: {{ optional(optional($petData['userPet']->pet)->stages->where('stage', $petData['userPet']->stage ?? 0)->first())->name ?? 'Trứng' }}
                                </p>
                                @endif
                            </div>
                            <button type="button" @click="speakDialogue()"
                                class="rounded-lg p-1 text-slate-400 hover:text-amber-700 hover:bg-amber-100 transition"
                                title="Nghe Pet nói">
                                <i data-lucide="volume-2" class="h-3.5 w-3.5"></i>
                            </button>
                        </div>
                        <p class="mt-1 text-xs text-slate-600 leading-relaxed italic line-clamp-2 cursor-pointer hover:text-amber-900"
                            @click="speakDialogue()">
                            "<span x-text="dialogue"></span>"
                        </p>
                    </div>
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-amber-200/70 z-10">
                <a href="{{ route('pet.index') }}"
                    class="inline-flex w-full items-center justify-center gap-2 rounded-2xl border border-amber-300 bg-white px-5 py-3 text-sm font-bold text-amber-900 hover:bg-amber-50 transition active:scale-[0.98]">
                    <span>Thăm phòng Pet</span>
                    <i data-lucide="arrow-right" class="h-4 w-4"></i>
                </a>
            </div>
        </div>

    </div>

</div>
@endsection