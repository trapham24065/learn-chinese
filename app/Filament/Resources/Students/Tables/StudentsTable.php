<?php

namespace App\Filament\Resources\Students\Tables;

use App\Models\LessonProgress;
use App\Models\StudySession;
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

                TextColumn::make('streak')
                    ->label('🔥 Streak')
                    ->getStateUsing(fn ($record): string => $record->calculateStreak() . ' ngày')
                    ->badge()
                    ->color('warning'),

                TextColumn::make('completed_lessons')
                    ->label('✅ Bài hoàn thành')
                    ->getStateUsing(fn ($record): int => LessonProgress::where('user_id', $record->id)
                        ->where('status', 'completed')->count())
                    ->numeric()
                    ->alignCenter(),

                TextColumn::make('total_minutes')
                    ->label('⏱ Tổng phút học')
                    ->getStateUsing(fn ($record): int => StudySession::where('user_id', $record->id)
                        ->sum('duration_minutes'))
                    ->numeric()
                    ->suffix(' phút')
                    ->alignCenter(),

                TextColumn::make('avg_score')
                    ->label('📊 Điểm TB Quiz')
                    ->getStateUsing(function ($record): string {
                        $avg = StudySession::where('user_id', $record->id)
                            ->whereNotNull('score')
                            ->avg('score');
                        return $avg ? number_format($avg, 1) . ' đ' : '—';
                    })
                    ->alignCenter(),

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
