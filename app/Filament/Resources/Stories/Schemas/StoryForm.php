<?php

namespace App\Filament\Resources\Stories\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class StoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Tiêu đề tiếng Trung')
                    ->placeholder('VD: 去咖啡馆喝咖啡')
                    ->required(),

                TextInput::make('title_pinyin')
                    ->label('Tiêu đề Pinyin')
                    ->placeholder('VD: Qù kāfēiguǎn hē kāfēi'),

                TextInput::make('title_vi')
                    ->label('Tiêu đề tiếng Việt')
                    ->placeholder('VD: Đi quán cà phê uống cà phê')
                    ->required(),

                TextInput::make('slug')
                    ->label('Slug đường dẫn')
                    ->placeholder('VD: di-quan-ca-phe-hsk-1')
                    ->required()
                    ->unique(ignoreRecord: true),

                Select::make('hsk_level')
                    ->label('Cấp độ HSK')
                    ->options([
                        1 => 'HSK 1 – Sơ cấp 1',
                        2 => 'HSK 2 – Sơ cấp 2',
                        3 => 'HSK 3 – Trung cấp 1',
                        4 => 'HSK 4 – Trung cấp 2',
                        5 => 'HSK 5 – Cao cấp 1',
                        6 => 'HSK 6 – Thành thạo',
                    ])
                    ->default(1)
                    ->required(),

                Select::make('category')
                    ->label('Chủ đề')
                    ->options([
                        'Đời sống'  => 'Đời sống',
                        'Ẩm thực'   => 'Ẩm thực',
                        'Giao tiếp' => 'Giao tiếp',
                        'Mua sắm'   => 'Mua sắm',
                        'Du lịch'   => 'Du lịch',
                        'Giao thông'=> 'Giao thông',
                        'Công sở'   => 'Công sở',
                        'Văn hóa'   => 'Văn hóa',
                    ])
                    ->default('Đời sống')
                    ->required(),

                ColorPicker::make('cover_color')
                    ->label('Màu thẻ đại diện')
                    ->default('#991b1b'),

                TextInput::make('word_count')
                    ->label('Số chữ Hán')
                    ->numeric()
                    ->default(0),

                TextInput::make('estimated_reading_minutes')
                    ->label('Thời lượng ước tính (phút)')
                    ->numeric()
                    ->default(3)
                    ->required(),

                Toggle::make('is_published')
                    ->label('Công khai cho học viên')
                    ->default(true),

                Textarea::make('summary')
                    ->label('Tóm tắt nội dung')
                    ->rows(2)
                    ->columnSpanFull(),

                Textarea::make('content_json')
                    ->label('Nội dung bài đọc (JSON)')
                    ->helperText('Định dạng mảng JSON gồm các câu, pinyin, dịch nghĩa và danh sách từ vựng.')
                    ->rows(8)
                    ->formatStateUsing(fn ($state) => is_array($state) ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $state)
                    ->dehydrateStateUsing(fn ($state) => is_string($state) ? json_decode($state, true) : $state)
                    ->columnSpanFull()
                    ->required(),

                Textarea::make('quiz_json')
                    ->label('Câu hỏi kiểm tra đọc hiểu (JSON)')
                    ->helperText('Mảng JSON câu hỏi trắc nghiệm, các lựa chọn và đáp án đúng.')
                    ->rows(6)
                    ->formatStateUsing(fn ($state) => is_array($state) ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $state)
                    ->dehydrateStateUsing(fn ($state) => is_string($state) ? json_decode($state, true) : $state)
                    ->columnSpanFull(),
            ]);
    }
}
