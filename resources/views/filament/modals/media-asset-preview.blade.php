<div class="space-y-6 text-sm">
    {{-- Image Showcase --}}
    <div class="flex flex-col items-center justify-center p-6 bg-gray-50 dark:bg-gray-800/60 rounded-xl border border-gray-200 dark:border-gray-700">
        <div class="relative p-4 bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-100 dark:border-gray-800 flex items-center justify-center">
            <img 
                src="{{ asset($asset->file_path) }}" 
                alt="{{ $asset->alt_text }}"
                class="w-32 h-32 object-contain"
            />
        </div>
        <div class="mt-3 text-center">
            <h4 class="font-bold text-gray-900 dark:text-gray-100 text-base">{{ $asset->name }}</h4>
            <code class="text-xs text-gray-500 dark:text-gray-400">{{ $asset->slug }}</code>
        </div>
    </div>

    {{-- Meta Badges Grid --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="p-3 bg-gray-50 dark:bg-gray-800/40 rounded-lg border border-gray-100 dark:border-gray-800 text-center">
            <div class="text-xs text-gray-500 font-medium">Danh mục</div>
            <div class="mt-1 font-semibold text-gray-800 dark:text-gray-200">
                {{ \App\Models\MediaAsset::CATEGORIES[$asset->category] ?? $asset->category }}
            </div>
        </div>
        <div class="p-3 bg-gray-50 dark:bg-gray-800/40 rounded-lg border border-gray-100 dark:border-gray-800 text-center">
            <div class="text-xs text-gray-500 font-medium">Định dạng</div>
            <div class="mt-1 font-semibold text-gray-800 dark:text-gray-200 uppercase">
                {{ $asset->metadata['format'] ?? 'SVG' }}
            </div>
        </div>
        <div class="p-3 bg-gray-50 dark:bg-gray-800/40 rounded-lg border border-gray-100 dark:border-gray-800 text-center">
            <div class="text-xs text-gray-500 font-medium">Cấp HSK</div>
            <div class="mt-1 flex flex-wrap gap-1 justify-center">
                @forelse($asset->metadata['hsk_levels'] ?? [] as $lvl)
                    <span class="px-2 py-0.5 text-xs font-semibold rounded bg-amber-100 dark:bg-amber-900/40 text-amber-800 dark:text-amber-300">
                        HSK {{ $lvl }}
                    </span>
                @empty
                    <span class="text-xs text-gray-400">Tất cả</span>
                @endforelse
            </div>
        </div>
        <div class="p-3 bg-gray-50 dark:bg-gray-800/40 rounded-lg border border-gray-100 dark:border-gray-800 text-center">
            <div class="text-xs text-gray-500 font-medium">Sử dụng</div>
            <div class="mt-1">
                @if($asset->isInUse())
                    <span class="px-2 py-0.5 text-xs font-semibold rounded bg-emerald-100 dark:bg-emerald-900/40 text-emerald-800 dark:text-emerald-300">
                        ✓ Đang dùng
                    </span>
                @else
                    <span class="px-2 py-0.5 text-xs font-semibold rounded bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
                        Chưa liên kết
                    </span>
                @endif
            </div>
        </div>
    </div>

    {{-- Details Sections --}}
    <div class="space-y-3">
        <div>
            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Mô tả (Alt text)</span>
            <p class="mt-1 text-gray-800 dark:text-gray-200">{{ $asset->alt_text }}</p>
        </div>

        <div>
            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Đường dẫn file</span>
            <p class="mt-1 font-mono text-xs text-gray-600 dark:text-gray-300 bg-gray-50 dark:bg-gray-800/60 p-2 rounded border border-gray-100 dark:border-gray-800">
                {{ $asset->file_path }}
            </p>
        </div>

        <div>
            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Từ khóa tìm kiếm</span>
            <div class="mt-1.5 flex flex-wrap gap-1.5">
                @foreach($asset->keywords ?? [] as $kw)
                    <span class="px-2 py-0.5 text-xs rounded-full bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700">
                        {{ $kw }}
                    </span>
                @endforeach
            </div>
        </div>

        {{-- Linked Vocabularies --}}
        <div>
            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Từ vựng liên kết ({{ $asset->vocabularies->count() }})</span>
            @if($asset->vocabularies->isNotEmpty())
                <div class="mt-2 grid grid-cols-1 sm:grid-cols-2 gap-2">
                    @foreach($asset->vocabularies as $vocab)
                        <div class="flex items-center justify-between p-2 rounded-lg bg-gray-50 dark:bg-gray-800/50 border border-gray-100 dark:border-gray-800">
                            <div>
                                <span class="font-bold text-gray-900 dark:text-gray-100">{{ $vocab->hanzi }}</span>
                                <span class="text-xs text-amber-600 dark:text-amber-400 ml-1">({{ $vocab->pinyin }})</span>
                                <div class="text-xs text-gray-500">{{ $vocab->meaning }}</div>
                            </div>
                            <span class="text-[10px] font-medium px-1.5 py-0.5 rounded {{ $vocab->pivot->relation_type === 'primary' ? 'bg-primary-100 text-primary-700 dark:bg-primary-900/50' : 'bg-gray-100 text-gray-600 dark:bg-gray-700' }}">
                                {{ $vocab->pivot->relation_type === 'primary' ? 'Chính' : 'Phụ' }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="mt-1 text-xs text-gray-400 italic">Chưa có từ vựng nào được liên kết trực tiếp trong bảng vocabularies.</p>
            @endif
        </div>

        {{-- Usage Locations if in use --}}
        @if($asset->isInUse())
            <div class="p-3 bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800 rounded-lg">
                <span class="text-xs font-bold text-amber-900 dark:text-amber-200 uppercase tracking-wider">Vị trí đang sử dụng trong hệ thống:</span>
                <ul class="mt-1.5 list-disc list-inside text-xs text-amber-800 dark:text-amber-300 space-y-1">
                    @foreach($asset->usageSummaryStrings() as $loc)
                        <li>{{ $loc }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- License and Attribution --}}
        <div class="pt-3 border-t border-gray-200 dark:border-gray-800 text-xs text-gray-500 dark:text-gray-400 space-y-1">
            <div class="flex items-center justify-between">
                <span><strong>Giấy phép:</strong> {{ $asset->license }}</span>
                <span><strong>Nguồn:</strong> {{ $asset->source }}</span>
            </div>
            @if($asset->attribution)
                <p class="text-[11px] leading-relaxed text-gray-400 dark:text-gray-500">
                    {{ $asset->attribution }}
                </p>
            @endif
        </div>
    </div>
</div>
