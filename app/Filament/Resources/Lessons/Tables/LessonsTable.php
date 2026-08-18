<?php

namespace App\Filament\Resources\Lessons\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LessonsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable(),
                TextColumn::make('title')
                    ->label('Tiêu đề')
                    ->searchable(),
                TextColumn::make('difficulty')
                    ->label('Mức độ')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'starter' => 'Mới bắt đầu',
                        'intermediate' => 'Trung bình',
                        'advanced' => 'Nâng cao',
                        default => $state,
                    }),
                TextColumn::make('sort_order')
                    ->label('Thứ tự')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('estimated_minutes')
                    ->label('Phút')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('accent_color')
                    ->label('Màu nhấn')
                    ->searchable(),
                IconColumn::make('is_published')
                    ->label('Xuất bản')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Ngày cập nhật')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
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
