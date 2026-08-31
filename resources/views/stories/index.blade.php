@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- ══ 1. HERO HEADER BANNER ══ --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-900 via-indigo-950 to-red-950 text-white p-6 sm:p-10 shadow-2xl border border-white/10 mb-8">
        {{-- Background Hanzi Watermark --}}
        <div class="absolute -right-6 top-1/2 -translate-y-1/2 text-9xl sm:text-[14rem] font-black text-white/5 pointer-events-none select-none">
            读
        </div>

        <div class="relative z-10 max-w-3xl">
            <div class="inline-flex items-center gap-2 bg-amber-400/20 border border-amber-400/30 px-3 py-1 rounded-full text-xs font-bold text-amber-300 uppercase tracking-widest mb-4">
                <i data-lucide="book-open-check" class="h-3.5 w-3.5"></i>
                <span>Phòng Luyện Đọc Hiểu Phân Cấp · Graded Reader</span>
            </div>

            <h1 class="text-2xl sm:text-4xl font-black tracking-tight leading-tight">
                Luyện Đọc Tiếng Trung Thực Chiến
            </h1>

            <p class="mt-3 text-sm sm:text-base text-slate-300 leading-relaxed max-w-2xl">
                Nâng cao phản xạ ngữ cảm tự nhiên qua các mẩu truyện ngắn và hội thoại đời sống phân cấp HSK 1 – 6. 
                Tích hợp công nghệ <strong>Tra từ 1-Click</strong>, <strong>Bật/Tắt Pinyin thông minh</strong> và <strong>Audio giọng đọc bản xứ</strong>.
            </p>

            {{-- User Reading Stats Mini-Bar --}}
            <div class="mt-6 grid grid-cols-2 sm:grid-cols-4 gap-3 pt-6 border-t border-white/10">
                <div class="bg-white/5 backdrop-blur-sm rounded-xl p-3 border border-white/10">
                    <div class="text-xs text-slate-400 font-semibold">Tổng bài đọc</div>
                    <div class="text-xl font-black text-white mt-0.5">{{ $stats['total_stories'] }} <span class="text-xs font-normal text-slate-400">bài</span></div>
                </div>

                <div class="bg-white/5 backdrop-blur-sm rounded-xl p-3 border border-white/10">
                    <div class="text-xs text-slate-400 font-semibold">Đã hoàn thành</div>
                    <div class="text-xl font-black text-emerald-400 mt-0.5">{{ $stats['completed_count'] }} <span class="text-xs font-normal text-slate-400">bài</span></div>
                </div>

                <div class="bg-white/5 backdrop-blur-sm rounded-xl p-3 border border-white/10">
                    <div class="text-xs text-slate-400 font-semibold">Từ vựng đã đọc</div>
                    <div class="text-xl font-black text-amber-300 mt-0.5">{{ number_format($stats['total_words_read']) }} <span class="text-xs font-normal text-slate-400">từ</span></div>
                </div>

                <div class="bg-white/5 backdrop-blur-sm rounded-xl p-3 border border-white/10">
                    <div class="text-xs text-slate-400 font-semibold">Thời gian đọc</div>
                    <div class="text-xl font-black text-indigo-300 mt-0.5">{{ $stats['total_read_mins'] }} <span class="text-xs font-normal text-slate-400">phút</span></div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══ 2. FILTER TABS & SEARCH BAR ══ --}}
    <div class="bg-white rounded-2xl p-4 sm:p-5 shadow-sm border border-slate-200 mb-8 space-y-4">
        
        {{-- Level Tabs --}}
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex flex-wrap items-center gap-1.5 sm:gap-2">
                <a href="{{ route('stories.index', array_merge(request()->query(), ['level' => ''])) }}"
                   class="px-3.5 py-1.5 rounded-xl text-xs sm:text-sm font-bold transition-all {{ empty($selectedLevel) ? 'bg-slate-900 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    Tất cả cấp độ
                </a>
                @foreach([1, 2, 3, 4, 5, 6] as $lvl)
                    <a href="{{ route('stories.index', array_merge(request()->query(), ['level' => $lvl])) }}"
                       class="px-3.5 py-1.5 rounded-xl text-xs sm:text-sm font-bold transition-all {{ $selectedLevel == $lvl ? 'bg-red-600 text-white shadow-sm shadow-red-200' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        HSK {{ $lvl }}
                    </a>
                @endforeach
            </div>

            {{-- Search Bar --}}
            <form method="GET" action="{{ route('stories.index') }}" class="w-full sm:w-72">
                @if($selectedLevel)
                    <input type="hidden" name="level" value="{{ $selectedLevel }}">
                @endif
                @if($selectedCategory)
                    <input type="hidden" name="category" value="{{ $selectedCategory }}">
                @endif
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Tìm kiếm bài đọc..."
                           class="w-full pl-9 pr-4 py-2 text-xs sm:text-sm rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent bg-slate-50 transition">
                    <i data-lucide="search" class="absolute left-3 top-2.5 h-4 w-4 text-slate-400"></i>
                </div>
            </form>
        </div>

        {{-- Categories Pills --}}
        @if($categories->isNotEmpty())
        <div class="flex flex-wrap items-center gap-1.5 pt-3 border-t border-slate-100 text-xs">
            <span class="text-slate-400 font-bold mr-1">Chủ đề:</span>
            <a href="{{ route('stories.index', array_merge(request()->query(), ['category' => 'all'])) }}"
               class="px-2.5 py-1 rounded-lg font-semibold transition {{ empty($selectedCategory) || $selectedCategory === 'all' ? 'bg-red-100 text-red-700 font-bold' : 'text-slate-500 hover:bg-slate-100' }}">
                Tất cả
            </a>
            @foreach($categories as $cat)
                <a href="{{ route('stories.index', array_merge(request()->query(), ['category' => $cat])) }}"
                   class="px-2.5 py-1 rounded-lg font-semibold transition {{ $selectedCategory === $cat ? 'bg-red-100 text-red-700 font-bold' : 'text-slate-500 hover:bg-slate-100' }}">
                    {{ $cat }}
                </a>
            @endforeach
        </div>
        @endif
    </div>

    {{-- ══ 3. STORIES CARD GRID ══ --}}
    @if($stories->isEmpty())
        <div class="bg-white rounded-2xl p-12 text-center border border-slate-200 shadow-sm max-w-lg mx-auto my-8">
            <div class="h-16 w-16 bg-slate-100 text-slate-400 rounded-full flex items-center justify-center mx-auto mb-4">
                <i data-lucide="book-open" class="h-8 w-8"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-800">Không tìm thấy bài đọc phù hợp</h3>
            <p class="text-sm text-slate-500 mt-1">Hãy thử xóa bộ lọc hoặc chọn cấp độ HSK khác.</p>
            <a href="{{ route('stories.index') }}" class="inline-flex items-center gap-2 mt-4 px-4 py-2 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-slate-800 transition">
                <i data-lucide="rotate-ccw" class="h-3.5 w-3.5"></i>
                <span>Xem tất cả bài đọc</span>
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($stories as $story)
                @php
                    $isCompleted = $completedStoryIds->contains($story->id);
                @endphp
                <div class="bg-white rounded-2xl overflow-hidden border border-slate-200 hover:border-slate-300 hover:shadow-xl transition-all duration-300 flex flex-col group">
                    
                    {{-- Card Banner --}}
                    <div class="relative p-5 text-white overflow-hidden"
                         style="background: linear-gradient(135deg, {{ $story->cover_color }} 0%, #0f172a 100%);">
                        
                        {{-- Watermark Hanzi --}}
                        <div class="absolute -right-2 -bottom-4 text-7xl font-black text-white/10 pointer-events-none select-none">
                            {{ mb_substr($story->title, 0, 2) }}
                        </div>

                        <div class="relative z-10 flex items-start justify-between gap-2">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-black bg-white/20 backdrop-blur-md text-white border border-white/30">
                                HSK {{ $story->hsk_level }}
                            </span>

                            <div class="flex items-center gap-2">
                                <span class="text-xs font-medium px-2.5 py-0.5 rounded-full bg-black/30 backdrop-blur-md text-slate-200 border border-white/10">
                                    {{ $story->category }}
                                </span>
                                @if($isCompleted)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-500/90 text-white text-xs font-bold shadow-sm" title="Bạn đã hoàn thành bài này">
                                        <i data-lucide="check" class="h-3 w-3"></i>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="relative z-10 mt-4">
                            <h2 class="text-xl font-bold text-white group-hover:text-amber-300 transition-colors line-clamp-1">
                                {{ $story->title }}
                            </h2>
                            <p class="text-xs font-medium text-amber-200/90 tracking-wide mt-0.5">
                                {{ $story->title_pinyin }}
                            </p>
                        </div>
                    </div>

                    {{-- Card Body --}}
                    <div class="p-5 flex-1 flex flex-col justify-between space-y-4">
                        <div>
                            <div class="text-sm font-bold text-slate-800 mb-2">
                                {{ $story->title_vi }}
                            </div>
                            <p class="text-xs text-slate-500 line-clamp-2 leading-relaxed">
                                {{ $story->summary ?? 'Luyện đọc hiểu tiếng Trung thực chiến kèm tra từ điển và nghe phát âm.' }}
                            </p>
                        </div>

                        {{-- Metadata Info --}}
                        <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-400 font-semibold">
                            <span class="inline-flex items-center gap-1">
                                <i data-lucide="file-text" class="h-3.5 w-3.5 text-slate-400"></i>
                                {{ $story->word_count }} từ
                            </span>

                            <span class="inline-flex items-center gap-1">
                                <i data-lucide="clock" class="h-3.5 w-3.5 text-slate-400"></i>
                                ~{{ $story->estimated_reading_minutes }} phút đọc
                            </span>

                            <a href="{{ route('stories.show', $story->slug) }}"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold {{ $isCompleted ? 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' : 'bg-red-50 text-red-700 hover:bg-red-100 group-hover:bg-red-600 group-hover:text-white' }} transition-all">
                                <span>{{ $isCompleted ? 'Đọc lại' : 'Đọc bài' }}</span>
                                <i data-lucide="arrow-right" class="h-3.5 w-3.5"></i>
                            </a>
                        </div>
                    </div>

                </div>
            @endforeach
        </div>
    @endif

</div>
@endsection
