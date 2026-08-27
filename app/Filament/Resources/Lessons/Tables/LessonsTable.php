<?php

namespace App\Filament\Resources\Lessons\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class LessonsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Tiêu đề bài học')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->limit(50),

                TextColumn::make('hsk_level')
                    ->label('Cấp HSK')
                    ->badge()
                    ->formatStateUsing(fn ($state): string => $state ? 'HSK ' . $state : '—')
                    ->color(fn ($state): string => match ($state) {
                        1 => 'success',
                        2 => 'info',
                        3 => 'warning',
                        4 => 'danger',
                        5 => 'primary',
                        6 => 'gray',
                        default => 'gray',
                    })
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('difficulty')
                    ->label('Mức độ')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'starter'      => 'Mới bắt đầu',
                        'intermediate' => 'Trung bình',
                        'advanced'     => 'Nâng cao',
                        default        => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'starter'      => 'success',
                        'intermediate' => 'warning',
                        'advanced'     => 'danger',
                        default        => 'gray',
                    }),

                TextColumn::make('estimated_minutes')
                    ->label('Phút')
                    ->numeric()
                    ->sortable()
                    ->suffix(' phút')
                    ->alignCenter(),

                ColorColumn::make('accent_color')
                    ->label('Màu nhấn')
                    ->alignCenter(),

                IconColumn::make('is_published')
                    ->label('Xuất bản')
                    ->boolean()
                    ->alignCenter(),

                TextColumn::make('sort_order')
                    ->label('Thứ tự')
                    ->numeric()
                    ->sortable()
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Ngày cập nhật')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('hsk_level')
                    ->label('Cấp HSK')
                    ->options([
                        1 => 'HSK 1 – Sơ cấp',
                        2 => 'HSK 2 – Sơ cấp cao',
                        3 => 'HSK 3 – Trung cấp thấp',
                        4 => 'HSK 4 – Trung cấp',
                        5 => 'HSK 5 – Cao cấp',
                        6 => 'HSK 6 – Thành thạo',
                    ])
                    ->placeholder('Tất cả cấp HSK'),

                SelectFilter::make('difficulty')
                    ->label('Độ khó')
                    ->options([
                        'starter'      => 'Mới bắt đầu',
                        'intermediate' => 'Trung bình',
                        'advanced'     => 'Nâng cao',
                    ])
                    ->placeholder('Tất cả độ khó'),

                TernaryFilter::make('is_published')
                    ->label('Trạng thái xuất bản')
                    ->trueLabel('Đã xuất bản')
                    ->falseLabel('Chưa xuất bản')
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
