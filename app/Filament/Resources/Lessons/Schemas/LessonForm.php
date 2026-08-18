<?php

namespace App\Filament\Resources\Lessons\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class LessonForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('slug')
                    ->label('Slug')
                    ->required(),
                TextInput::make('title')
                    ->label('Tiêu đề')
                    ->required(),
                Textarea::make('summary')
                    ->label('Tóm tắt')
                    ->required()
                    ->columnSpanFull(),
                RichEditor::make('content')
                    ->label('Nội dung bài học')
                    ->columnSpanFull(),
                Select::make('difficulty')
                    ->label('Mức độ')
                    ->options([
                        'starter' => 'Mới bắt đầu',
                        'intermediate' => 'Trung bình',
                        'advanced' => 'Nâng cao',
                    ])
                    ->default('starter')
                    ->required(),
                Select::make('hsk_level')
                    ->label('Cấp độ HSK')
                    ->options([
                        1 => 'HSK 1 – Sơ cấp (150 từ)',
                        2 => 'HSK 2 – Sơ cấp cao (300 từ)',
                        3 => 'HSK 3 – Trung cấp thấp (600 từ)',
                        4 => 'HSK 4 – Trung cấp (1200 từ)',
                        5 => 'HSK 5 – Cao cấp (2500 từ)',
                        6 => 'HSK 6 – Thành thạo (5000+ từ)',
                    ])
                    ->nullable()
                    ->placeholder('Chưa phân loại HSK'),
                TextInput::make('sort_order')
                    ->label('Thứ tự hiển thị')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('estimated_minutes')
                    ->label('Thời lượng (phút)')
                    ->required()
                    ->numeric()
                    ->default(10),
                TextInput::make('accent_color')
                    ->label('Màu nhấn')
                    ->required()
                    ->default('#991b1b'),
                Toggle::make('is_published')
                    ->label('Đã xuất bản')
                    ->required(),
            ]);
    }
}
