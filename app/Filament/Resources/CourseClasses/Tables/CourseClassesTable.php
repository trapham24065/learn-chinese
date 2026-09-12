<?php

namespace App\Filament\Resources\CourseClasses\Tables;

use App\Models\CourseClass;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CourseClassesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Mã lớp')
                    ->badge()
                    ->color('primary')
                    ->copyable()
                    ->sortable()
                    ->searchable(),

                TextColumn::make('name')
                    ->label('Tên lớp / Kỳ mở')
                    ->weight('bold')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('course.title')
                    ->label('Khóa học')
                    ->limit(30)
                    ->sortable()
                    ->searchable(),

                TextColumn::make('start_date')
                    ->label('Khai giảng')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('schedule')
                    ->label('Lịch học')
                    ->state(fn (CourseClass $record): string => "{$record->schedule_days} ({$record->schedule_time})"),

                TextColumn::make('students_count')
                    ->label('Sĩ số (Đã cọc/học)')
                    ->state(fn (CourseClass $record): string => "{$record->enrolled_count} / {$record->max_students}")
                    ->badge()
                    ->color(fn (CourseClass $record): string => $record->is_full ? 'warning' : 'success')
                    ->alignCenter(),

                TextColumn::make('status')
                    ->label('Trạng thái')
                    ->badge()
                    ->formatStateUsing(fn (CourseClass $record): string => $record->status_label)
                    ->color(fn (CourseClass $record): string => $record->status_color)
                    ->alignCenter(),
            ])
            ->filters([
                SelectFilter::make('course_id')
                    ->label('Lọc theo khóa học')
                    ->relationship('course', 'title'),

                SelectFilter::make('status')
                    ->label('Trạng thái')
                    ->options([
                        'draft' => 'Bản nháp',
                        'open' => 'Đang mở tuyển sinh',
                        'full' => 'Đã đủ học viên',
                        'ongoing' => 'Đang học',
                        'completed' => 'Đã kết thúc',
                        'cancelled' => 'Đã hủy',
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
