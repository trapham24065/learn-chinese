<?php

namespace App\Filament\Resources\Flashcards\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
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

                TextColumn::make('lesson.title')
                    ->label('Bài học')
                    ->badge()
                    ->placeholder('Chung')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('example')
                    ->label('Ví dụ')
                    ->limit(40)
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('is_active')
                    ->label('Hiển thị')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('sort_order')
                    ->label('Thứ tự')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('hsk_level')
                    ->options([
                        1 => 'HSK 1',
                        2 => 'HSK 2',
                        3 => 'HSK 3',
                        4 => 'HSK 4',
                        5 => 'HSK 5',
                        6 => 'HSK 6',
                    ])
                    ->label('Lọc theo HSK'),
                SelectFilter::make('lesson_id')
                    ->relationship('lesson', 'title')
                    ->label('Lọc theo bài học'),
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
