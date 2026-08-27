<?php

namespace App\Filament\Resources\Flashcards\Pages;

use App\Filament\Resources\Flashcards\FlashcardResource;
use App\Models\Lesson;
use App\Services\FlashcardImportService;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;

class ListFlashcards extends ListRecords
{
    protected static string $resource = FlashcardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Tạo Flashcard mới'),

            Action::make('importCsv')
                ->label('Import CSV / Excel')
                ->icon(Heroicon::OutlinedArrowUpTray)
                ->color('success')
                ->modalHeading('📥 Import Flashcard hàng loạt từ CSV / Excel')
                ->modalDescription('Nhập nhanh hàng chục, hàng trăm thẻ từ vựng vào hệ thống từ tệp CSV hoặc dán trực tiếp dữ liệu.')
                ->modalSubmitActionLabel('Bắt đầu Import')
                ->schema([
                    FileUpload::make('file')
                        ->label('Chọn tệp CSV / TXT / TSV')
                        ->helperText('Định dạng cột: hanzi, pinyin, meaning, example, example_pinyin, example_meaning, hsk_level, tags')
                        ->acceptedFileTypes([
                            'text/csv',
                            'text/plain',
                            'text/tab-separated-values',
                            'application/vnd.ms-excel',
                            'application/csv',
                        ])
                        ->disk('local')
                        ->directory('temp-imports')
                        ->nullable(),

                    Textarea::make('raw_content')
                        ->label('Hoặc dán trực tiếp dữ liệu từ Excel / Google Sheets')
                        ->placeholder("hanzi,pinyin,meaning,example,example_pinyin,example_meaning,hsk_level,tags\n你好,nǐ hǎo,xin chào,你好吗？,Nǐ hǎo ma?,Bạn khỏe không?,1,\"chào hỏi, cơ bản\"\n谢谢,xièxie,cảm ơn,非常感谢你！,Fēicháng gǎnxiè nǐ!,Rất cảm ơn bạn!,1,\"lịch sự\"")
                        ->rows(5)
                        ->helperText('Có thể copy trực tiếp các cột từ Excel rồi paste vào đây (tự động nhận diện dấu phẩy hoặc phím Tab).')
                        ->nullable(),

                    Select::make('default_hsk_level')
                        ->label('Cấp HSK mặc định')
                        ->helperText('Áp dụng nếu dòng trong file không có thông tin cấp HSK.')
                        ->options([
                            1 => 'HSK 1 – Sơ cấp (150 từ)',
                            2 => 'HSK 2 – Sơ cấp cao (300 từ)',
                            3 => 'HSK 3 – Trung cấp thấp (600 từ)',
                            4 => 'HSK 4 – Trung cấp (1200 từ)',
                            5 => 'HSK 5 – Cao cấp (2500 từ)',
                            6 => 'HSK 6 – Thành thạo (5000+ từ)',
                        ])
                        ->placeholder('Tự nhận diện từ file (nếu có)')
                        ->nullable(),

                    Select::make('default_lesson_id')
                        ->label('Gán vào bài học (tùy chọn)')
                        ->options(fn () => Lesson::query()->where('is_published', true)->orderBy('sort_order')->pluck('title', 'id'))
                        ->searchable()
                        ->placeholder('Không gán bài học')
                        ->nullable(),

                    Select::make('duplicate_mode')
                        ->label('Xử lý khi từ Hán (Hanzi) đã tồn tại')
                        ->options([
                            'update' => 'Cập nhật nội dung mới (Update - Khuyên dùng)',
                            'skip'   => 'Bỏ qua dòng đó (Skip)',
                            'create' => 'Tạo thêm bản ghi mới (Create duplicate)',
                        ])
                        ->default('update')
                        ->required(),
                ])
                ->action(function (array $data) {
                    $importService = new FlashcardImportService();
                    $filePath = $data['file'] ?? null;
                    $rawContent = $data['raw_content'] ?? null;
                    $defaultHskLevel = !empty($data['default_hsk_level']) ? (int) $data['default_hsk_level'] : null;
                    $defaultLessonId = !empty($data['default_lesson_id']) ? (int) $data['default_lesson_id'] : null;
                    $duplicateMode = $data['duplicate_mode'] ?? 'update';

                    if (empty($filePath) && empty(trim((string) $rawContent))) {
                        Notification::make()
                            ->title('Vui lòng chọn file tải lên hoặc dán nội dung CSV!')
                            ->danger()
                            ->send();
                        return;
                    }

                    $result = $importService->import(
                        filePath: $filePath,
                        rawContent: $rawContent,
                        defaultHskLevel: $defaultHskLevel,
                        defaultLessonId: $defaultLessonId,
                        duplicateMode: $duplicateMode
                    );

                    if ($result['errors'] > 0 && $result['created'] === 0 && $result['updated'] === 0) {
                        Notification::make()
                            ->title('Import thất bại!')
                            ->body(implode("\n", $result['error_messages']))
                            ->danger()
                            ->persistent()
                            ->send();
                    } else {
                        $msg = "Tổng xử lý: {$result['total']} dòng | Tạo mới: {$result['created']} | Cập nhật: {$result['updated']}";
                        if ($result['skipped'] > 0) {
                            $msg .= " | Bỏ qua: {$result['skipped']}";
                        }
                        if ($result['errors'] > 0) {
                            $msg .= " | Lỗi: {$result['errors']} dòng";
                        }

                        Notification::make()
                            ->title('Import Flashcard thành công!')
                            ->body($msg)
                            ->success()
                            ->send();
                    }
                }),

            Action::make('downloadSample')
                ->label('Tải CSV mẫu')
                ->icon(Heroicon::OutlinedArrowDownTray)
                ->color('gray')
                ->action(function () {
                    $csv = FlashcardImportService::getSampleCsv();
                    return response()->streamDownload(function () use ($csv) {
                        echo "\xEF\xBB\xBF"; // UTF-8 BOM for Excel compatibility
                        echo $csv;
                    }, 'mau_flashcard_import.csv', [
                        'Content-Type' => 'text/csv; charset=UTF-8',
                    ]);
                }),
        ];
    }
}

