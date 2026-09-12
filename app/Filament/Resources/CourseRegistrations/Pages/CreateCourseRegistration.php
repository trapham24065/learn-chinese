<?php

namespace App\Filament\Resources\CourseRegistrations\Pages;

use App\Filament\Resources\CourseRegistrations\CourseRegistrationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCourseRegistration extends CreateRecord
{
    protected static string $resource = CourseRegistrationResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterCreate(): void
    {
        $this->record->recordActivity(
            type: 'created',
            description: 'Được tạo thủ công bởi quản trị viên qua Admin Panel',
            userId: auth()->id()
        );
    }
}
