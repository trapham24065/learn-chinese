<x-filament-panels::page>

@php
    $summary      = $this->getLevelSummary();
    $cm           = $summary[$activeLevel];          // current meta
    $lessons      = $this->getLessons();
    $flashcards   = $this->getFlashcards();
    $unassigned   = $this->getUnassignedLessons();
    $unassignedFc = $this->getUnassignedFlashcards();
    $hanNums      = ['一','二','三','四','五','六'];
    $color        = $cm['color'];
@endphp

{{-- ══ LEVEL TABS ══ --}}
<div style="display:grid;grid-template-columns:repeat(6,1fr);gap:.75rem;margin-bottom:1.5rem;">
    @foreach($summary as $lvl => $meta)
    @php $active = ($activeLevel === $lvl); @endphp
    <button wire:click="setLevel({{ $lvl }})"
            style="
                position:relative;overflow:hidden;border-radius:1rem;padding:.875rem .75rem;text-align:left;
                border: 2px solid {{ $active ? $meta['color'] : '#e5e7eb' }};
                background: {{ $active ? $meta['color'] : '#fafafa' }};
                box-shadow: {{ $active ? '0 4px 16px -4px '.$meta['color'].'66' : 'none' }};
                transition: all .2s;cursor:pointer;
            ">
        <span style="position:absolute;right:6px;top:2px;font-size:2.8rem;font-weight:900;opacity:.1;line-height:1;color:{{ $active ? 'white' : $meta['color'] }};pointer-events:none;">{{ $hanNums[$lvl-1] }}</span>
        <div style="position:relative;">
            <div style="font-size:.6rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:{{ $active ? 'rgba(255,255,255,.65)' : '#9ca3af' }};">Cấp {{ $lvl }}</div>
            <div style="font-size:.95rem;font-weight:900;margin-top:2px;color:{{ $active ? 'white' : $meta['color'] }};">{{ $meta['label'] }}</div>
            <div style="margin-top:.5rem;display:flex;gap:.35rem;flex-wrap:wrap;">
                <span style="font-size:.6rem;font-weight:700;padding:2px 7px;border-radius:6px;background:{{ $active ? 'rgba(255,255,255,.2)' : '#f3f4f6' }};color:{{ $active ? 'white' : '#6b7280' }};">{{ $meta['lesson_count'] }} bài</span>
                <span style="font-size:.6rem;font-weight:700;padding:2px 7px;border-radius:6px;background:{{ $active ? 'rgba(255,255,255,.2)' : '#f3f4f6' }};color:{{ $active ? 'white' : '#6b7280' }};">{{ $meta['flashcard_count'] }} thẻ</span>
            </div>
        </div>
    </button>
    @endforeach
</div>

{{-- ══ HERO BAR ══ --}}
<div style="border-radius:1.25rem;padding:1.25rem 1.5rem;margin-bottom:1.75rem;background:{{ $color }}0f;border:1.5px solid {{ $color }}22;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
    <div>
        <div style="font-size:.65rem;font-weight:700;letter-spacing:.18em;text-transform:uppercase;color:{{ $color }};">{{ $cm['label'] }}</div>
        <div style="font-size:1.1rem;font-weight:900;color:#111827;margin-top:3px;">{{ $cm['desc'] }}</div>
    </div>
    <div style="display:flex;gap:.75rem;">
        @foreach([['count'=>$lessons->count(),'label'=>'Bài học'],['count'=>$flashcards->count(),'label'=>'Flashcard'],['count'=>$unassigned->count()+$unassignedFc->count(),'label'=>'Chưa gán']] as $stat)
        <div style="text-align:center;padding:.5rem 1.1rem;border-radius:.875rem;background:white;border:1px solid {{ $color }}22;box-shadow:0 1px 4px rgba(0,0,0,.04);">
            <div style="font-size:1.35rem;font-weight:900;color:{{ $color }};">{{ $stat['count'] }}</div>
            <div style="font-size:.6rem;font-weight:600;text-transform:uppercase;letter-spacing:.1em;color:#9ca3af;margin-top:1px;">{{ $stat['label'] }}</div>
        </div>
        @endforeach
    </div>
</div>

{{-- ══ TWO COLUMNS ══ --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;">

    {{-- ── LESSONS ── --}}
    <div>
        {{-- Section header --}}
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.875rem;">
            <div style="display:flex;align-items:center;gap:.5rem;">
                <div style="width:28px;height:28px;border-radius:.625rem;background:{{ $color }};display:grid;place-items:center;font-size:.8rem;">📚</div>
                <span style="font-size:.7rem;font-weight:800;letter-spacing:.14em;text-transform:uppercase;color:#374151;">Bài học</span>
                <span style="font-size:.65rem;font-weight:700;padding:1px 8px;border-radius:99px;background:#f3f4f6;color:#6b7280;">{{ $lessons->count() }}</span>
            </div>
            <a href="{{ route('filament.admin.resources.lessons.create') }}"
               style="display:inline-flex;align-items:center;gap:.375rem;padding:.375rem .875rem;border-radius:.625rem;border:1px solid #e5e7eb;background:white;font-size:.7rem;font-weight:700;color:#374151;text-decoration:none;box-shadow:0 1px 3px rgba(0,0,0,.05);transition:all .15s;">
                + Tạo bài học
            </a>
        </div>

        {{-- Lesson cards --}}
        <div style="display:flex;flex-direction:column;gap:.625rem;">
            @forelse($lessons as $lesson)
            <div style="border-radius:1rem;border:1px solid #f1f5f9;background:white;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,.04);">
                {{-- Left accent --}}
                <div style="display:flex;align-items:stretch;">
                    <div style="width:4px;background:{{ $color }};flex-shrink:0;border-radius:1rem 0 0 1rem;"></div>
                    <div style="padding:.875rem 1rem;flex:1;min-width:0;">
                        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:.5rem;">
                            <div style="min-width:0;">
                                <div style="display:flex;align-items:center;gap:.5rem;flex-wrap:wrap;">
                                    <span style="font-size:.85rem;font-weight:800;color:#111827;">{{ $lesson->title }}</span>
                                    @if($lesson->is_published)
                                    <span style="font-size:.6rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;padding:2px 7px;border-radius:99px;background:#ecfdf5;color:#059669;border:1px solid #a7f3d0;">Live</span>
                                    @else
                                    <span style="font-size:.6rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;padding:2px 7px;border-radius:99px;background:#fffbeb;color:#d97706;border:1px solid #fde68a;">Draft</span>
                                    @endif
                                </div>
                                <p style="font-size:.72rem;color:#9ca3af;margin-top:3px;overflow:hidden;white-space:nowrap;text-overflow:ellipsis;">{{ $lesson->summary }}</p>
                            </div>
                        </div>
                        <div style="margin-top:.625rem;display:flex;flex-wrap:wrap;gap:.375rem;">
                            @foreach([['⏱',''. $lesson->estimated_minutes.'m'],['❓',$lesson->questions_count.' câu'],['🃏',$lesson->flashcards_count.' thẻ']] as $chip)
                            <span style="font-size:.65rem;font-weight:600;padding:2px 8px;border-radius:6px;background:#f8fafc;border:1px solid #e2e8f0;color:#64748b;">{{ $chip[0] }} {{ $chip[1] }}</span>
                            @endforeach
                            <span style="font-size:.65rem;font-weight:600;padding:2px 8px;border-radius:6px;background:{{ $color }}10;border:1px solid {{ $color }}25;color:{{ $color }};text-transform:capitalize;">{{ $lesson->difficulty }}</span>
                        </div>
                        <div style="margin-top:.75rem;padding-top:.625rem;border-top:1px solid #f8fafc;display:flex;flex-wrap:wrap;gap:.5rem;">
                            {{ ($this->editLessonAction)(['id' => $lesson->id]) }}
                            {{ ($this->moveLessonAction)(['id' => $lesson->id, 'current_level' => $activeLevel]) }}
                            {{ ($this->deleteLessonAction)(['id' => $lesson->id]) }}
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div style="border-radius:1rem;border:2px dashed #e5e7eb;padding:2.5rem 1rem;text-align:center;color:#9ca3af;">
                <div style="font-size:2rem;margin-bottom:.5rem;">📭</div>
                <p style="font-size:.8rem;font-weight:600;">Chưa có bài học nào</p>
                <p style="font-size:.7rem;margin-top:.25rem;">Gán từ mục bên dưới hoặc tạo mới.</p>
            </div>
            @endforelse
        </div>

        {{-- Unassigned lessons --}}
        @if($unassigned->isNotEmpty())
        <div style="margin-top:1.25rem;">
            <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:.75rem;">
                <div style="flex:1;height:1px;background:#e5e7eb;"></div>
                <span style="font-size:.6rem;font-weight:700;letter-spacing:.16em;text-transform:uppercase;color:#9ca3af;white-space:nowrap;">Chưa gán HSK ({{ $unassigned->count() }})</span>
                <div style="flex:1;height:1px;background:#e5e7eb;"></div>
            </div>
            <div style="display:flex;flex-direction:column;gap:.375rem;">
                @foreach($unassigned as $lesson)
                <div style="display:flex;align-items:center;justify-content:space-between;border-radius:.75rem;border:1.5px dashed #e5e7eb;background:#fafafa;padding:.625rem 1rem;gap:.75rem;">
                    <div style="min-width:0;flex:1;">
                        <p style="font-size:.78rem;font-weight:700;color:#374151;overflow:hidden;white-space:nowrap;text-overflow:ellipsis;">{{ $lesson->title }}</p>
                        <p style="font-size:.65rem;color:#9ca3af;overflow:hidden;white-space:nowrap;text-overflow:ellipsis;">{{ $lesson->summary }}</p>
                    </div>
                    {{ ($this->assignLessonAction)(['id' => $lesson->id]) }}
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    {{-- ── FLASHCARDS ── --}}
    <div>
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.875rem;">
            <div style="display:flex;align-items:center;gap:.5rem;">
                <div style="width:28px;height:28px;border-radius:.625rem;background:{{ $color }};display:grid;place-items:center;font-size:.8rem;">🗂️</div>
                <span style="font-size:.7rem;font-weight:800;letter-spacing:.14em;text-transform:uppercase;color:#374151;">Flashcard</span>
                <span style="font-size:.65rem;font-weight:700;padding:1px 8px;border-radius:99px;background:#f3f4f6;color:#6b7280;">{{ $flashcards->count() }}</span>
            </div>
            <a href="{{ route('filament.admin.resources.flashcards.create') }}"
               style="display:inline-flex;align-items:center;gap:.375rem;padding:.375rem .875rem;border-radius:.625rem;border:1px solid #e5e7eb;background:white;font-size:.7rem;font-weight:700;color:#374151;text-decoration:none;box-shadow:0 1px 3px rgba(0,0,0,.05);">
                + Tạo flashcard
            </a>
        </div>


</div>
</x-filament-panels::page>
