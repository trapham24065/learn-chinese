<?php

namespace App\Filament\Resources\Courses\Schemas;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class CourseForm
{
    public static function make(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Tên khóa học')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (string $operation, $state, callable $set) {
                        if ($operation === 'create' && filled($state)) {
                            $set('slug', Str::slug($state));
                        }
                    }),

                TextInput::make('slug')
                    ->label('Đường dẫn tĩnh (Slug)')
                    ->required()
                    ->maxLength(255)
                    ->unique('courses', 'slug', ignoreRecord: true),

                Select::make('category')
                    ->label('Phân loại')
                    ->options([
                        'hsk_starter' => 'HSK Sơ cấp (1-2)',
                        'hsk_intermediate' => 'HSK Trung cấp (3-4)',
                        'hsk_advanced' => 'HSK Cao cấp (5-6)',
                        'conversation' => 'Giao tiếp thực chiến',
                        'business' => 'Tiếng Trung Thương mại',
                        'kids' => 'Tiếng Trung Trẻ em',
                    ])
                    ->default('hsk_starter')
                    ->required(),

                TextInput::make('price')
                    ->label('Học phí ưu đãi (VNĐ)')
                    ->numeric()
                    ->prefix('₫')
                    ->required(),

                TextInput::make('original_price')
                    ->label('Học phí gốc (VNĐ)')
                    ->numeric()
                    ->prefix('₫')
                    ->nullable()
                    ->helperText('Để trống nếu không có giá niêm yết cũ'),

                TextInput::make('duration_weeks')
                    ->label('Thời lượng (Tuần)')
                    ->numeric()
                    ->default(8)
                    ->suffix('tuần')
                    ->required(),

                TextInput::make('total_sessions')
                    ->label('Tổng số buổi')
                    ->numeric()
                    ->default(24)
                    ->suffix('buổi')
                    ->required(),

                TextInput::make('sort_order')
                    ->label('Thứ tự hiển thị')
                    ->numeric()
                    ->default(0),

                Toggle::make('is_active')
                    ->label('Kích hoạt hiển thị công khai')
                    ->default(true),

                Textarea::make('summary')
                    ->label('Tóm tắt ngắn (1-2 câu)')
                    ->rows(3)
                    ->columnSpanFull(),

                TagsInput::make('highlights')
                    ->label('Điểm nổi bật (Nhấn Enter sau mỗi ý)')
                    ->placeholder('Nhập điểm nổi bật...')
                    ->columnSpanFull(),

                RichEditor::make('description')
                    ->label('Nội dung chi tiết khóa học')
                    ->columnSpanFull(),
            ]);
    }
}
