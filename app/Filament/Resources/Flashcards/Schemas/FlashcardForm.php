<?php

namespace App\Filament\Resources\Flashcards\Schemas;

use App\Models\Lesson;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class FlashcardForm
{
    public static function make(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('lesson_id')
                ->label('Bài học')
                ->options(Lesson::query()->where('is_published', true)->orderBy('sort_order')->pluck('title', 'id'))
                ->searchable()
                ->nullable(),

            TextInput::make('hanzi')
                ->label('Chữ Hán (Hanzi)')
                ->required()
                ->maxLength(50),

            TextInput::make('pinyin')
                ->label('Phiên âm (Pinyin)')
                ->required()
                ->maxLength(100),

            TextInput::make('meaning')
                ->label('Nghĩa tiếng Việt')
                ->required()
                ->maxLength(255),

            Textarea::make('example')
                ->label('Câu ví dụ (Hanzi)')
                ->rows(2)
                ->nullable(),

            TextInput::make('example_pinyin')
                ->label('Phiên âm câu ví dụ')
                ->maxLength(255)
                ->nullable(),

            TextInput::make('example_meaning')
                ->label('Dịch nghĩa câu ví dụ')
                ->maxLength(255)
                ->nullable(),

            TagsInput::make('tags')
                ->label('Thẻ / Chủ đề (Tags)')
                ->placeholder('Ví dụ: Chào hỏi, Mua sắm, Gia đình...')
                ->helperText('Nhập từ khóa rồi nhấn Enter để thêm tag.')
                ->nullable(),

            TextInput::make('sort_order')
                ->label('Thứ tự')
                ->numeric()
                ->default(0),

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

            Toggle::make('is_active')
                ->label('Hiển thị')
                ->default(true),
        ]);
    }
}
