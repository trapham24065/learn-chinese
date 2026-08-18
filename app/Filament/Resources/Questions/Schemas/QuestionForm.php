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
                    ->placeholder('Chọn bài học (hoặc để trống nếu là Quiz chung)')
                    ->searchable()
                    ->preload(),
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
