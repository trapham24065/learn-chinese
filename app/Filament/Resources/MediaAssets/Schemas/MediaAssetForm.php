<?php

namespace App\Filament\Resources\MediaAssets\Schemas;

use App\Models\MediaAsset;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MediaAssetForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Thông tin Cơ bản (Basic Information)')
                    ->description('Tên định danh, đường dẫn tệp tin và phân loại tư liệu.')
                    ->schema([
                        TextInput::make('name')
                            ->label('Tên tư liệu (Tiếng Việt / Song ngữ)')
                            ->placeholder('Ví dụ: Xe đạp (Bicycle)')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('slug')
                            ->label('Slug định danh duy nhất')
                            ->placeholder('Ví dụ: transport-zixingche')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->helperText('Định dạng: {danh_mục}-{tên_pinyin_hoặc_tiếng_anh}'),

                        Select::make('category')
                            ->label('Danh mục ngữ nghĩa')
                            ->options(MediaAsset::CATEGORIES)
                            ->required()
                            ->searchable(),

                        TextInput::make('file_path')
                            ->label('Đường dẫn file (trong thư mục public/)')
                            ->placeholder('Ví dụ: images/hsk/assets/transport/zixingche.svg')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Tư liệu nên được đặt trong public/images/hsk/assets/{category}/'),

                        Textarea::make('alt_text')
                            ->label('Văn bản mô tả sư phạm (Alt text)')
                            ->placeholder('Mô tả ngắn gọn nội dung bức tranh phục vụ trợ năng và phán đoán câu hỏi...')
                            ->rows(2)
                            ->columnSpanFull(),

                        Toggle::make('is_active')
                            ->label('Kích hoạt sử dụng')
                            ->default(true)
                            ->helperText('Bật để tư liệu có thể xuất hiện trong đề thi và bài học.'),
                    ])->columns(2),

                Section::make('Phân loại Cấp độ HSK & Từ khóa Ngữ nghĩa')
                    ->description('Từ khóa tìm kiếm đa ngữ và cấp độ học tập phù hợp.')
                    ->schema([
                        TagsInput::make('keywords')
                            ->label('Từ khóa tìm kiếm (Hán tự, Pinyin, Tiếng Việt, Tiếng Anh)')
                            ->placeholder('Nhập từ khóa rồi nhấn Enter...')
                            ->helperText('Ví dụ: 自行车, zìxíngchē, xe đạp, bicycle, bike, 骑车')
                            ->columnSpanFull(),

                        CheckboxList::make('metadata.hsk_levels')
                            ->label('Cấp độ HSK áp dụng')
                            ->options([
                                1 => 'HSK 1 (Sơ cấp 1)',
                                2 => 'HSK 2 (Sơ cấp 2)',
                                3 => 'HSK 3 (Trung cấp 1)',
                                4 => 'HSK 4 (Trung cấp 2)',
                                5 => 'HSK 5 (Cao cấp 1)',
                                6 => 'HSK 6 (Cao cấp 2)',
                            ])
                            ->columns(3)
                            ->default([1, 2]),

                        Select::make('metadata.style')
                            ->label('Phong cách đồ họa')
                            ->options([
                                'flat_vector'  => 'Vector phẳng (Flat Vector SVG)',
                                'illustration' => 'Minh họa sư phạm (Illustration)',
                                'scene'        => 'Bối cảnh tình huống (Contextual Scene)',
                                'photo'        => 'Hình ảnh chụp thực tế (Photo)',
                            ])
                            ->default('flat_vector'),
                    ])->columns(2),

                Section::make('Liên kết Từ vựng (Vocabulary Relations)')
                    ->description('Liên kết tư liệu này với từ vựng có sẵn trong cơ sở dữ liệu để tái sử dụng.')
                    ->schema([
                        Select::make('vocabularies')
                            ->label('Từ vựng liên kết')
                            ->relationship('vocabularies', 'hanzi')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->columnSpanFull()
                            ->helperText('Một hình ảnh có thể liên kết với nhiều từ vựng (Ví dụ: tranh xe đạp liên kết với cả 自行车 và 骑).'),
                    ]),

                Section::make('Bản quyền & Nguồn gốc (Provenance & License)')
                    ->description('Thông tin tác quyền hợp pháp và ghi nhận đóng góp.')
                    ->schema([
                        TextInput::make('source')
                            ->label('Nguồn tư liệu')
                            ->default('OpenMoji & Learn Chinese')
                            ->required(),

                        TextInput::make('license')
                            ->label('Giấy phép bản quyền')
                            ->default('CC BY-SA 4.0')
                            ->required()
                            ->helperText('Mặc định: CC BY-SA 4.0 hoặc Bản quyền nội bộ Learn Chinese.'),

                        Textarea::make('attribution')
                            ->label('Nội dung ghi nhận tác quyền (Attribution statement)')
                            ->default('Designed with OpenMoji open-source assets and Learn Chinese pedagogic illustrations. Licensed under CC BY-SA 4.0 (https://creativecommons.org/licenses/by-sa/4.0/).')
                            ->rows(2)
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }
}
