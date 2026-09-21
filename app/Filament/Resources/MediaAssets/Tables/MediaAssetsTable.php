<?php

namespace App\Filament\Resources\MediaAssets\Tables;

use App\Models\MediaAsset;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class MediaAssetsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('file_path')
                    ->label('Ảnh')
                    ->state(fn (MediaAsset $record): string => asset($record->file_path))
                    ->height(48)
                    ->width(48)
                    ->alignCenter()
                    ->extraImgAttributes([
                        'class' => 'p-1 rounded-lg bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 object-contain shadow-xs',
                    ]),

                TextColumn::make('name')
                    ->label('Tên tư liệu')
                    ->searchable()
                    ->weight('bold')
                    ->description(fn (MediaAsset $record): string => $record->slug)
                    ->sortable(),

                TextColumn::make('category')
                    ->label('Danh mục')
                    ->badge()
                    ->formatStateUsing(fn ($state): string => MediaAsset::CATEGORIES[$state] ?? (string) $state)
                    ->color(fn ($state): string => match ($state) {
                        MediaAsset::CATEGORY_ACTIONS   => 'info',
                        MediaAsset::CATEGORY_ANIMALS   => 'warning',
                        MediaAsset::CATEGORY_TRANSPORT => 'success',
                        MediaAsset::CATEGORY_FOOD      => 'danger',
                        MediaAsset::CATEGORY_PEOPLE    => 'primary',
                        MediaAsset::CATEGORY_PLACES    => 'gray',
                        MediaAsset::CATEGORY_OBJECTS   => 'amber',
                        MediaAsset::CATEGORY_NATURE    => 'teal',
                        default                        => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('metadata.hsk_levels')
                    ->label('Cấp HSK')
                    ->badge()
                    ->separator(',')
                    ->formatStateUsing(fn ($state): string => 'HSK ' . $state)
                    ->color('warning'),

                TextColumn::make('vocabularies_count')
                    ->label('Từ vựng')
                    ->counts('vocabularies')
                    ->badge()
                    ->color('success')
                    ->alignCenter()
                    ->sortable(),

                IconColumn::make('is_in_use')
                    ->label('Đang dùng')
                    ->state(fn (MediaAsset $record): bool => $record->isInUse())
                    ->boolean()
                    ->alignCenter(),

                TextColumn::make('license')
                    ->label('Bản quyền')
                    ->badge()
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('is_active')
                    ->label('Kích hoạt')
                    ->boolean()
                    ->alignCenter()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->label('Lọc theo danh mục')
                    ->options(MediaAsset::CATEGORIES),

                TernaryFilter::make('is_active')
                    ->label('Trạng thái kích hoạt')
                    ->trueLabel('Đang kích hoạt')
                    ->falseLabel('Tạm ẩn')
                    ->placeholder('Tất cả'),
            ])
            ->recordActions([
                Action::make('preview')
                    ->label('Xem')
                    ->icon(Heroicon::OutlinedEye)
                    ->color('info')
                    ->modalHeading(fn (MediaAsset $record): string => $record->name)
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Đóng')
                    ->modalContent(fn (MediaAsset $record) => view('filament.modals.media-asset-preview', ['asset' => $record])),

                EditAction::make()->label('Sửa'),

                DeleteAction::make()
                    ->label('Xóa')
                    ->before(function (DeleteAction $action, MediaAsset $record) {
                        if ($record->isInUse()) {
                            $locations = implode(', ', $record->usageSummaryStrings());
                            Notification::make()
                                ->danger()
                                ->title('Không thể xóa tư liệu này!')
                                ->body("Tư liệu đang được sử dụng tại: {$locations}. Vui lòng gỡ bỏ liên kết trong đề thi/flashcard trước khi xóa.")
                                ->persistent()
                                ->send();

                            $action->cancel();
                        }
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Xóa đã chọn')
                        ->before(function (DeleteBulkAction $action, Collection $records) {
                            $inUseRecords = $records->filter(fn (MediaAsset $record) => $record->isInUse());
                            if ($inUseRecords->isNotEmpty()) {
                                $names = $inUseRecords->pluck('name')->implode(', ');
                                Notification::make()
                                    ->danger()
                                    ->title('Không thể xóa các tư liệu đang sử dụng!')
                                    ->body("Các tư liệu sau đang được sử dụng trong hệ thống: {$names}. Vui lòng hủy chọn những mục này.")
                                    ->persistent()
                                    ->send();

                                $action->cancel();
                            }
                        }),
                ]),
            ]);
    }
}
