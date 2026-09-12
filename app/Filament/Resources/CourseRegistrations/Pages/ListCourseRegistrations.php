<?php

namespace App\Filament\Resources\CourseRegistrations\Pages;

use App\Filament\Resources\CourseRegistrations\CourseRegistrationResource;
use App\Filament\Widgets\CourseRegistrationStats;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCourseRegistrations extends ListRecords
{
    protected static string $resource = CourseRegistrationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Thêm đơn đăng ký mới'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            CourseRegistrationStats::class,
        ];
    }
}
