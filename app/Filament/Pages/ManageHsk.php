<?php

namespace App\Filament\Pages;

use App\Models\Flashcard;
use App\Models\Lesson;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\RichEditor;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;

class ManageHsk extends Page
{
    // Instance property (not static) – required by Filament 5
    protected string $view = 'filament.pages.manage-hsk';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAcademicCap;

    protected static ?string $navigationLabel = 'Quản lý HSK';

    protected static ?string $title = 'Quản lý nội dung theo cấp HSK';

    protected static ?int $navigationSort = 10;

    public int $activeLevel = 1;
    public string $searchFlashcard = '';

    public static array $levelMeta = [
        1 => ['label' => 'HSK 1', 'color' => '#16a34a', 'desc' => 'Sơ cấp – 150 từ'],
        2 => ['label' => 'HSK 2', 'color' => '#2563eb', 'desc' => 'Sơ cấp cao – 300 từ'],
        3 => ['label' => 'HSK 3', 'color' => '#d97706', 'desc' => 'Trung cấp thấp – 600 từ'],
        4 => ['label' => 'HSK 4', 'color' => '#ea580c', 'desc' => 'Trung cấp – 1200 từ'],
        5 => ['label' => 'HSK 5', 'color' => '#9333ea', 'desc' => 'Cao cấp – 2500 từ'],
        6 => ['label' => 'HSK 6', 'color' => '#be123c', 'desc' => 'Thành thạo – 5000+ từ'],
    ];

    /** Summary counts for all 6 levels */
    public function getLevelSummary(): array
    {
        $summary = [];
        foreach (self::$levelMeta as $lvl => $meta) {
            $summary[$lvl] = $meta;
            $summary[$lvl]['lesson_count'] = Lesson::where('hsk_level', $lvl)->count();
            $summary[$lvl]['flashcard_count'] = Flashcard::where('hsk_level', $lvl)->count();
        }
        return $summary;
    }

    /** Lessons for the active level */
    public function getLessons(): Collection
    {
        return Lesson::where('hsk_level', $this->activeLevel)
            ->orderBy('sort_order')
            ->withCount(['questions', 'flashcards'])
            ->get();
    }

    /** Unassigned lessons (hsk_level = null) */
    public function getUnassignedLessons(): Collection
    {
        return Lesson::whereNull('hsk_level')->orderBy('created_at', 'desc')->get();
    }

    /** Flashcards for the active level */
    public function getFlashcards(): Collection
    {
        return Flashcard::where('hsk_level', $this->activeLevel)
            ->when($this->searchFlashcard, function ($query) {
                $query->where(function ($q) {
                    $q->where('hanzi', 'like', '%' . $this->searchFlashcard . '%')
                      ->orWhere('pinyin', 'like', '%' . $this->searchFlashcard . '%')
                      ->orWhere('meaning', 'like', '%' . $this->searchFlashcard . '%');
                });
            })
            ->orderBy('sort_order')
            ->with('lesson')
            ->get();
    }

    /** Unassigned flashcards (hsk_level = null) */
    public function getUnassignedFlashcards(): Collection
    {
        return Flashcard::whereNull('hsk_level')->orderBy('created_at', 'desc')->limit(50)->get();
    }

    /** Switch active tab */
    public function setLevel(int $level): void
    {
        $this->activeLevel = $level;
        $this->searchFlashcard = '';
    }

    // ── Lesson Actions ──────────────────────────────────────────────────────────

    public function editLessonAction(): Action
    {
        return Action::make('editLesson')
            ->label('Sửa bài học')
            ->icon('heroicon-o-pencil-square')
            ->form([
                TextInput::make('slug')->label('Slug')->required(),
                TextInput::make('title')->label('Tiêu đề')->required(),
                Textarea::make('summary')->label('Tóm tắt')->rows(2)->columnSpanFull(),
                RichEditor::make('content')->label('Nội dung bài học')->columnSpanFull(),
                Select::make('difficulty')->label('Mức độ')->options([
                    'starter'      => 'Mới bắt đầu',
                    'intermediate' => 'Trung bình',
                    'advanced'     => 'Nâng cao',
                ])->required(),
                Select::make('hsk_level')->label('Cấp độ HSK')->options(
                    collect(self::$levelMeta)->map(fn($m) => $m['label'] . ' – ' . $m['desc'])
                )->nullable()->placeholder('Chưa phân loại'),
                TextInput::make('estimated_minutes')->label('Thời lượng (phút)')->numeric(),
                TextInput::make('sort_order')->label('Thứ tự hiển thị')->numeric(),
                TextInput::make('accent_color')->label('Màu nhấn'),
                Toggle::make('is_published')->label('Đã xuất bản'),
            ])
            ->fillForm(fn(array $arguments) => Lesson::find($arguments['id'])?->toArray() ?? [])
            ->action(function (array $arguments, array $data): void {
                $lesson = Lesson::find($arguments['id']);
                if ($lesson) {
                    $lesson->update($data);
                    Notification::make()->title('Đã cập nhật bài học "' . $lesson->title . '"')->success()->send();
                }
            })
            ->modalHeading('Chỉnh sửa bài học')
            ->modalSubmitActionLabel('Lưu thay đổi');
    }

    public function moveLessonAction(): Action
    {
        return Action::make('moveLesson')
            ->label('Chuyển cấp HSK')
            ->icon('heroicon-o-arrows-right-left')
            ->form([
                Select::make('hsk_level')
                    ->label('Chuyển sang cấp HSK')
                    ->options(collect(self::$levelMeta)->map(fn($m) => $m['label'] . ' – ' . $m['desc']))
                    ->nullable()
                    ->placeholder('Bỏ phân loại (unset)'),
            ])
            ->fillForm(fn(array $arguments) => ['hsk_level' => $arguments['current_level']])
            ->action(function (array $arguments, array $data): void {
                $lesson = Lesson::find($arguments['id']);
                if ($lesson) {
                    $lesson->update(['hsk_level' => $data['hsk_level'] ?: null]);
                    Notification::make()->title('Đã chuyển "' . $lesson->title . '" sang ' . ($data['hsk_level'] ? 'HSK ' . $data['hsk_level'] : 'chưa phân loại'))->success()->send();
                }
            })
            ->modalHeading('Chuyển bài học sang cấp HSK khác')
            ->modalSubmitActionLabel('Xác nhận chuyển');
    }

    public function assignLessonAction(): Action
    {
        return Action::make('assignLesson')
            ->label('Gán vào HSK ' . $this->activeLevel)
            ->icon('heroicon-o-plus-circle')
            ->action(function (array $arguments): void {
                $lesson = Lesson::find($arguments['id']);
                if ($lesson) {
                    $lesson->update(['hsk_level' => $this->activeLevel]);
                    Notification::make()->title('Đã gán "' . $lesson->title . '" vào HSK ' . $this->activeLevel)->success()->send();
                }
            });
    }

    // ── Flashcard Actions ───────────────────────────────────────────────────────

    public function editFlashcardAction(): Action
    {
        return Action::make('editFlashcard')
            ->label('Sửa flashcard')
            ->icon('heroicon-o-pencil-square')
            ->form([
                TextInput::make('hanzi')->label('Chữ Hán')->required(),
                TextInput::make('pinyin')->label('Pinyin')->required(),
                TextInput::make('meaning')->label('Nghĩa')->required(),
                TextInput::make('example')->label('Câu ví dụ'),
                TextInput::make('example_pinyin')->label('Pinyin ví dụ'),
                TextInput::make('example_meaning')->label('Dịch nghĩa'),
                Select::make('hsk_level')->label('Cấp độ HSK')->options(
                    collect(self::$levelMeta)->map(fn($m) => $m['label'] . ' – ' . $m['desc'])
                )->nullable()->placeholder('Chưa phân loại'),
                Select::make('lesson_id')->label('Bài học')->options(
                    Lesson::orderBy('sort_order')->pluck('title', 'id')
                )->nullable()->placeholder('Không gán bài học'),
                TextInput::make('sort_order')->label('Thứ tự')->numeric(),
                Toggle::make('is_active')->label('Hiển thị'),
            ])
            ->fillForm(fn(array $arguments) => Flashcard::find($arguments['id'])?->toArray() ?? [])
            ->action(function (array $arguments, array $data): void {
                $fc = Flashcard::find($arguments['id']);
                if ($fc) {
                    $fc->update($data);
                    Notification::make()->title('Đã cập nhật flashcard "' . $fc->hanzi . '"')->success()->send();
                }
            })
            ->modalHeading('Chỉnh sửa Flashcard')
            ->modalSubmitActionLabel('Lưu thay đổi');
    }

    public function moveFlashcardAction(): Action
    {
        return Action::make('moveFlashcard')
            ->label('Chuyển cấp')
            ->icon('heroicon-o-arrows-right-left')
            ->form([
                Select::make('hsk_level')
                    ->label('Chuyển sang cấp HSK')
                    ->options(collect(self::$levelMeta)->map(fn($m) => $m['label'] . ' – ' . $m['desc']))
                    ->nullable()
                    ->placeholder('Bỏ phân loại'),
            ])
            ->fillForm(fn(array $arguments) => ['hsk_level' => $arguments['current_level']])
            ->action(function (array $arguments, array $data): void {
                $fc = Flashcard::find($arguments['id']);
                if ($fc) {
                    $fc->update(['hsk_level' => $data['hsk_level'] ?: null]);
                    Notification::make()->title('Đã chuyển "' . $fc->hanzi . '" sang ' . ($data['hsk_level'] ? 'HSK ' . $data['hsk_level'] : 'chưa phân loại'))->success()->send();
                }
            })
            ->modalHeading('Chuyển Flashcard sang cấp HSK khác')
            ->modalSubmitActionLabel('Xác nhận');
    }

    public function assignFlashcardAction(): Action
    {
        return Action::make('assignFlashcard')
            ->label('Gán vào HSK ' . $this->activeLevel)
            ->icon('heroicon-o-plus-circle')
            ->action(function (array $arguments): void {
                $fc = Flashcard::find($arguments['id']);
                if ($fc) {
                    $fc->update(['hsk_level' => $this->activeLevel]);
                    Notification::make()->title('Đã gán "' . $fc->hanzi . '" vào HSK ' . $this->activeLevel)->success()->send();
                }
            });
    }

    public function deleteFlashcardAction(): Action
    {
        return Action::make('deleteFlashcard')
            ->label('Xóa')
            ->icon('heroicon-o-trash')
            ->color('danger')
            ->requiresConfirmation()
            ->modalHeading('Xóa flashcard này?')
            ->action(function (array $arguments): void {
                $fc = Flashcard::find($arguments['id']);
                if ($fc) {
                    $hanzi = $fc->hanzi;
                    $fc->delete();
                    Notification::make()->title('Đã xóa flashcard "' . $hanzi . '"')->warning()->send();
                }
            });
    }

    public function deleteLessonAction(): Action
    {
        return Action::make('deleteLesson')
            ->label('Xóa')
            ->icon('heroicon-o-trash')
            ->color('danger')
            ->requiresConfirmation()
            ->modalHeading('Xóa bài học này?')
            ->action(function (array $arguments): void {
                $lesson = Lesson::find($arguments['id']);
                if ($lesson) {
                    $title = $lesson->title;
                    $lesson->delete();
                    Notification::make()->title('Đã xóa bài học "' . $title . '"')->warning()->send();
                }
            });
    }
}
