<?php

namespace App\Filament\Resources\Students\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StudentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Họ và tên')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                // Eager-loaded via withCount() in getEloquentQuery() — zero extra queries, sortable
                TextColumn::make('completed_lessons')
                    ->label('Bài hoàn thành')
                    ->numeric()
                    ->sortable()
                    ->alignCenter()
                    ->badge()
                    ->color('success'),

                // Eager-loaded via withSum() in getEloquentQuery() — zero extra queries, sortable
                TextColumn::make('total_minutes')
                    ->label('Phút học')
                    ->numeric()
                    ->sortable()
                    ->suffix(' phút')
                    ->alignCenter(),

                // Eager-loaded via withAvg() in getEloquentQuery() — zero extra queries, sortable
                TextColumn::make('avg_score')
                    ->label('Điểm TB Quiz')
                    ->formatStateUsing(fn ($state): string => $state !== null ? number_format((float) $state, 1) . ' đ' : '—')
                    ->sortable()
                    ->alignCenter()
                    ->badge()
                    ->color(fn ($state): string => match (true) {
                        $state === null  => 'gray',
                        $state >= 80     => 'success',
                        $state >= 60     => 'warning',
                        default          => 'danger',
                    }),

                // Streak requires PHP calculation — acceptable since it's the only non-subquery column
                TextColumn::make('streak')
                    ->label('Streak')
                    ->getStateUsing(fn ($record): string => $record->calculateStreak() . ' ngày')
                    ->badge()
                    ->color('warning'),

                TextColumn::make('created_at')
                    ->label('Ngày đăng ký')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->color('gray'),
            ])
            ->defaultSort('created_at', 'desc')
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
