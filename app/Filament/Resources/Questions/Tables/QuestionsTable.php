<?php

namespace App\Filament\Resources\Questions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class QuestionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('question')
                    ->label('Câu hỏi')
                    ->searchable()
                    ->limit(45)
                    ->tooltip(fn ($record): string => $record->question),
                TextColumn::make('lesson.title')
                    ->label('Bài học')
                    ->badge()
                    ->placeholder('Quiz chung')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('correct_answer')
                    ->label('Đáp án đúng')
                    ->color('success')
                    ->searchable(),
                TextColumn::make('difficulty')
                    ->label('Mức độ')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'starter' => 'Mới bắt đầu',
                        'intermediate' => 'Trung bình',
                        'advanced' => 'Nâng cao',
                        default => $state ?? '',
                    }),
                IconColumn::make('is_active')
                    ->label('Kích hoạt')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('sort_order')
                    ->label('Thứ tự')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('lesson_id')
                    ->relationship('lesson', 'title')
                    ->label('Lọc theo bài học'),
                SelectFilter::make('difficulty')
                    ->label('Lọc theo mức độ')
                    ->options([
                        'starter' => 'Mới bắt đầu',
                        'intermediate' => 'Trung bình',
                        'advanced' => 'Nâng cao',
                    ]),
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
