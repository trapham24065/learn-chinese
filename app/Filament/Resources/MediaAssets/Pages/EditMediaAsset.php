<?php

namespace App\Filament\Resources\MediaAssets\Pages;

use App\Filament\Resources\MediaAssets\MediaAssetResource;
use App\Models\MediaAsset;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditMediaAsset extends EditRecord
{
    protected static string $resource = MediaAssetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->label('Xóa tư liệu này')
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
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
