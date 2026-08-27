<?php

namespace App\Filament\Resources\Flashcards\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class FlashcardsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('hanzi')
                    ->label('Chữ Hán')
                    ->searchable()
                    ->weight('bold')
                    ->size('lg'),

                TextColumn::make('pinyin')
                    ->label('Pinyin')
                    ->searchable()
                    ->color('warning'),

                TextColumn::make('meaning')
                    ->label('Nghĩa')
                    ->searchable()
                    ->limit(40),

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

                TextColumn::make('lesson.title')
                    ->label('Bài học')
                    ->badge()
                    ->placeholder('Chung')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('tags')
                    ->label('Tags')
                    ->badge()
                    ->separator(',')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('example')
                    ->label('Ví dụ')
                    ->limit(40)
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('is_active')
                    ->label('Hiển thị')
                    ->boolean()
                    ->sortable()
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
            ])
            ->filters([
                SelectFilter::make('hsk_level')
                    ->options([
                        1 => 'HSK 1 – Sơ cấp',
                        2 => 'HSK 2 – Sơ cấp cao',
                        3 => 'HSK 3 – Trung cấp thấp',
                        4 => 'HSK 4 – Trung cấp',
                        5 => 'HSK 5 – Cao cấp',
                        6 => 'HSK 6 – Thành thạo',
                    ])
                    ->label('Lọc theo HSK'),

                SelectFilter::make('lesson_id')
                    ->relationship('lesson', 'title')
                    ->searchable()
                    ->preload()
                    ->label('Lọc theo bài học'),

                TernaryFilter::make('is_active')
                    ->label('Trạng thái hiển thị')
                    ->trueLabel('Đang hiển thị')
                    ->falseLabel('Đang ẩn')
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

