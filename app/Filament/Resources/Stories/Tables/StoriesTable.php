<?php

namespace App\Filament\Resources\Stories\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class StoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Tiêu đề tiếng Trung')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('title_vi')
                    ->label('Tiêu đề tiếng Việt')
                    ->searchable()
                    ->limit(40),

                TextColumn::make('hsk_level')
                    ->label('Cấp HSK')
                    ->badge()
                    ->formatStateUsing(fn ($state): string => $state ? 'HSK ' . $state : '—')
                    ->color(fn ($state): string => match ($state) {
                        1 => 'danger',
                        2 => 'warning',
                        3 => 'info',
                        4 => 'primary',
                        5 => 'success',
                        default => 'gray',
                    })
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('category')
                    ->label('Chủ đề')
                    ->badge()
                    ->color('gray')
                    ->sortable(),

                TextColumn::make('word_count')
                    ->label('Số từ')
                    ->numeric()
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('estimated_reading_minutes')
                    ->label('Thời lượng')
                    ->formatStateUsing(fn ($state) => $state . ' phút')
                    ->alignCenter(),

                IconColumn::make('is_published')
                    ->label('Xuất bản')
                    ->boolean()
                    ->alignCenter(),

                TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('hsk_level')
                    ->label('Lọc theo HSK')
                    ->options([
                        1 => 'HSK 1',
                        2 => 'HSK 2',
                        3 => 'HSK 3',
                        4 => 'HSK 4',
                        5 => 'HSK 5',
                        6 => 'HSK 6',
                    ]),

                SelectFilter::make('category')
                    ->label('Lọc theo chủ đề')
                    ->options([
                        'Đời sống'   => 'Đời sống',
                        'Ẩm thực'    => 'Ẩm thực',
                        'Giao tiếp'  => 'Giao tiếp',
                        'Mua sắm'    => 'Mua sắm',
                        'Du lịch'    => 'Du lịch',
                        'Giao thông' => 'Giao thông',
                        'Công sở'    => 'Công sở',
                        'Văn hóa'    => 'Văn hóa',
                    ]),

                TernaryFilter::make('is_published')
                    ->label('Trạng thái xuất bản'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
