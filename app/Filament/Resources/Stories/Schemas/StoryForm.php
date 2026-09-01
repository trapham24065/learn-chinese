<?php

namespace App\Filament\Resources\Stories\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
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
                        'Đời sống'   => 'Đời sống',
                        'Ẩm thực'    => 'Ẩm thực',
                        'Giao tiếp'  => 'Giao tiếp',
                        'Mua sắm'    => 'Mua sắm',
                        'Du lịch'    => 'Du lịch',
                        'Giao thông' => 'Giao thông',
                        'Công sở'    => 'Công sở',
                        'Văn hóa'    => 'Văn hóa',
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
                    ->placeholder('Tóm tắt ngắn gọn nội dung bài đọc để hiển thị ở danh sách bài...')
                    ->rows(2)
                    ->columnSpanFull(),

                Repeater::make('content_json')
                    ->label('Nội dung các câu trong bài đọc')
                    ->helperText('Bấm "+ Thêm câu mới" để thêm từng câu tiếng Trung, Pinyin và nghĩa tiếng Việt một cách trực quan.')
                    ->schema([
                        TextInput::make('chinese')
                            ->label('Câu tiếng Trung (Chữ Hán)')
                            ->placeholder('VD: 今天是星期六，天气很好。 (hoặc có khoảng trắng: 今天 是 星期六)')
                            ->required(),

                        TextInput::make('pinyin')
                            ->label('Phiên âm Pinyin (Tùy chọn)')
                            ->placeholder('VD: Jīntiān shì xīngqīliù, tiānqì hěn hǎo.'),

                        TextInput::make('vietnamese')
                            ->label('Dịch nghĩa tiếng Việt')
                            ->placeholder('VD: Hôm nay là thứ Bảy, thời tiết rất đẹp.')
                            ->required(),
                    ])
                    ->itemLabel(fn (array $state): ?string => !empty($state['chinese']) ? $state['chinese'] : 'Câu mới')
                    ->addActionLabel('+ Thêm câu mới')
                    ->collapsible()
                    ->defaultItems(1)
                    ->columnSpanFull()
                    ->required(),

                Repeater::make('quiz_json')
                    ->label('Câu hỏi kiểm tra đọc hiểu (Comprehension Quiz)')
                    ->helperText('Thêm các câu hỏi trắc nghiệm kiểm tra độ hiểu bài của học viên.')
                    ->schema([
                        TextInput::make('question')
                            ->label('Nội dung câu hỏi tiếng Trung')
                            ->placeholder('VD: 今天是星期几？')
                            ->required(),

                        TextInput::make('pinyin')
                            ->label('Pinyin câu hỏi (Tùy chọn)')
                            ->placeholder('VD: Jīntiān shì xīngqī jǐ?'),

                        TagsInput::make('options')
                            ->label('Các lựa chọn đáp án (Gõ từng đáp án rồi nhấn Enter)')
                            ->placeholder('VD: 星期五 (Enter), 星期六 (Enter)...')
                            ->required(),

                        TextInput::make('correct_answer')
                            ->label('Đáp án đúng')
                            ->placeholder('VD: 星期六 (Phải trùng khớp với 1 trong các đáp án ở trên)')
                            ->required(),

                        TextInput::make('explanation')
                            ->label('Giải thích đáp án (Tùy chọn)')
                            ->placeholder('VD: Trong bài viết: "今天是星期六"'),
                    ])
                    ->itemLabel(fn (array $state): ?string => !empty($state['question']) ? $state['question'] : 'Câu hỏi mới')
                    ->addActionLabel('+ Thêm câu hỏi kiểm tra')
                    ->collapsible()
                    ->columnSpanFull(),
            ]);
    }
}

