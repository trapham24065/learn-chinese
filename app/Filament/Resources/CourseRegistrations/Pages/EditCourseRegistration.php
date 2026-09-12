<?php

namespace App\Filament\Resources\CourseRegistrations\Pages;

use App\Filament\Resources\CourseRegistrations\CourseRegistrationResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditCourseRegistration extends EditRecord
{
    protected static string $resource = CourseRegistrationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('call')
                ->label('Gọi điện thoại')
                ->icon(Heroicon::OutlinedPhone)
                ->color('info')
                ->url(fn (): string => 'tel:' . $this->record->phone),

            Action::make('zalo')
                ->label('Mở chat Zalo')
                ->icon(Heroicon::OutlinedChatBubbleLeftRight)
                ->color('success')
                ->url(fn (): string => 'https://zalo.me/' . ($this->record->phone_normalized ?: $this->record->phone), shouldOpenInNewTab: true),

            DeleteAction::make()->label('Xóa đơn này'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterSave(): void
    {
        $this->record->recordActivity(
            type: 'note_added',
            description: 'Thông tin học viên được cập nhật bởi quản trị viên',
            userId: auth()->id()
        );
    }
}
