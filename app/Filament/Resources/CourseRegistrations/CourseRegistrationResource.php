<?php

namespace App\Filament\Resources\CourseRegistrations;

use App\Filament\Resources\CourseRegistrations\Pages\CreateCourseRegistration;
use App\Filament\Resources\CourseRegistrations\Pages\EditCourseRegistration;
use App\Filament\Resources\CourseRegistrations\Pages\ListCourseRegistrations;
use App\Filament\Resources\CourseRegistrations\Schemas\CourseRegistrationForm;
use App\Filament\Resources\CourseRegistrations\Tables\CourseRegistrationsTable;
use App\Models\CourseRegistration;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CourseRegistrationResource extends Resource
{
    protected static ?string $model = CourseRegistration::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserPlus;

    protected static string|UnitEnum|null $navigationGroup = 'Khóa học Trực tuyến';

    protected static ?string $navigationLabel = 'Đơn đăng ký & CRM';

    protected static ?string $modelLabel = 'đơn đăng ký';

    protected static ?string $pluralModelLabel = 'đơn đăng ký';

    protected static ?string $recordTitleAttribute = 'full_name';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return CourseRegistrationForm::make($schema);
    }

    public static function table(Table $table): Table
    {
        return CourseRegistrationsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListCourseRegistrations::route('/'),
            'create' => CreateCourseRegistration::route('/create'),
            'edit'   => EditCourseRegistration::route('/{record}/edit'),
        ];
    }
}
