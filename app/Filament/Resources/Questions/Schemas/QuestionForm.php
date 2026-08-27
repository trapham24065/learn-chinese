<?php

namespace App\Filament\Resources\Questions\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class QuestionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('lesson_id')
                    ->relationship('lesson', 'title')
                    ->label('Bài học liên kết')
                    ->placeholder('Chọn bài học (hoặc để trống nếu là Quiz/HSK chung)')
                    ->searchable()
                    ->preload(),
                Select::make('hsk_level')
                    ->label('Cấp độ HSK')
                    ->options([
                        1 => 'HSK 1',
                        2 => 'HSK 2',
                        3 => 'HSK 3',
                        4 => 'HSK 4',
                        5 => 'HSK 5',
                        6 => 'HSK 6',
                    ])
                    ->default(1)
                    ->required(),
                Select::make('skill_type')
                    ->label('Kỹ năng thi')
                    ->options([
                        'listening' => '🎧 Nghe hiểu (Listening)',
                        'reading'   => '📖 Đọc hiểu (Reading)',
                        'grammar'   => '✍️ Ngữ pháp (Grammar)',
                    ])
                    ->default('reading')
                    ->required(),
                Select::make('difficulty')
                    ->label('Mức độ')
                    ->options([
                        'starter' => 'Mới bắt đầu',
                        'intermediate' => 'Trung bình',
                        'advanced' => 'Nâng cao',
                    ])
                    ->default('starter')
                    ->required(),
                TextInput::make('question')
                    ->label('Nội dung câu hỏi')
                    ->placeholder('VD: 你好 có nghĩa là gì?')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('pinyin')
                    ->label('Phiên âm / Pinyin')
                    ->placeholder('VD: nǐ hǎo'),
                TextInput::make('audio_text')
                    ->label('Nội dung giọng đọc Audio (Cho phần Nghe)')
                    ->placeholder('VD: 你好，我是中国人。 (Để trống nếu không phải câu hỏi Nghe)'),
                TextInput::make('correct_answer')
                    ->label('Đáp án đúng')
                    ->placeholder('VD: Xin chào')
                    ->helperText('Nhập chính xác nội dung của đáp án đúng.')
                    ->required(),
                TagsInput::make('options')
                    ->label('Danh sách các lựa chọn đáp án')
                    ->placeholder('Gõ đáp án rồi nhấn Enter (Thêm ít nhất 2 lựa chọn)')
                    ->required()
                    ->columnSpanFull(),
                Textarea::make('explanation')
                    ->label('Giải thích đáp án')
                    ->placeholder('Giải thích chi tiết vì sao đáp án đúng để học viên củng cố kiến thức...')
                    ->rows(3)
                    ->columnSpanFull(),
                TextInput::make('sort_order')
                    ->label('Thứ tự sắp xếp')
                    ->numeric()
                    ->default(0)
                    ->required(),
                Toggle::make('is_active')
                    ->label('Kích hoạt câu hỏi')
                    ->default(true)
                    ->required(),
            ]);
    }
}
