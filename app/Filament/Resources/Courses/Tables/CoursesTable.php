<?php

namespace App\Filament\Resources\Courses\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class CoursesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Tên khóa học')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->limit(45),

                TextColumn::make('category')
                    ->label('Phân loại')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'hsk_starter' => 'HSK Sơ cấp',
                        'hsk_intermediate' => 'HSK Trung cấp',
                        'hsk_advanced' => 'HSK Cao cấp',
                        'conversation' => 'Giao tiếp',
                        'business' => 'Thương mại',
                        'kids' => 'Trẻ em',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'hsk_starter' => 'success',
                        'hsk_intermediate' => 'warning',
                        'hsk_advanced' => 'danger',
                        'conversation' => 'info',
                        'business' => 'primary',
                        default => 'gray',
                    }),

                TextColumn::make('price')
                    ->label('Học phí')
                    ->formatStateUsing(fn ($state) => number_format((float) $state, 0, ',', '.') . ' ₫')
                    ->sortable()
                    ->alignEnd(),

                TextColumn::make('duration_weeks')
                    ->label('Thời lượng')
                    ->suffix(' tuần')
                    ->alignCenter(),

                TextColumn::make('total_sessions')
                    ->label('Số buổi')
                    ->suffix(' buổi')
                    ->alignCenter(),

                TextColumn::make('classes_count')
                    ->label('Lớp mở')
                    ->counts('classes')
                    ->badge()
                    ->color('primary')
                    ->alignCenter(),

                IconColumn::make('is_active')
                    ->label('Kích hoạt')
                    ->boolean()
                    ->alignCenter(),

                TextColumn::make('sort_order')
                    ->label('Thứ tự')
                    ->numeric()
                    ->sortable()
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->label('Phân loại')
                    ->options([
                        'hsk_starter' => 'HSK Sơ cấp (1-2)',
                        'hsk_intermediate' => 'HSK Trung cấp (3-4)',
                        'hsk_advanced' => 'HSK Cao cấp (5-6)',
                        'conversation' => 'Giao tiếp thực chiến',
                        'business' => 'Tiếng Trung Thương mại',
                    ]),

                TernaryFilter::make('is_active')
                    ->label('Trạng thái kích hoạt')
                    ->trueLabel('Đang kích hoạt')
                    ->falseLabel('Tạm ẩn')
                    ->placeholder('Tất cả'),
            ])
            ->recordActions([
                EditAction::make()->label('Sửa'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('Xóa đã chọn'),
                ]),
            ]);
    }
}
