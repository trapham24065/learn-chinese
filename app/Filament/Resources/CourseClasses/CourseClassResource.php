<?php

namespace App\Filament\Resources\CourseClasses;

use App\Filament\Resources\CourseClasses\Pages\CreateCourseClass;
use App\Filament\Resources\CourseClasses\Pages\EditCourseClass;
use App\Filament\Resources\CourseClasses\Pages\ListCourseClasses;
use App\Filament\Resources\CourseClasses\Schemas\CourseClassForm;
use App\Filament\Resources\CourseClasses\Tables\CourseClassesTable;
use App\Models\CourseClass;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CourseClassResource extends Resource
{
    protected static ?string $model = CourseClass::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static string|UnitEnum|null $navigationGroup = 'Khóa học Trực tuyến';

    protected static ?string $navigationLabel = 'Lớp học & Kỳ mở';

    protected static ?string $modelLabel = 'lớp học';

    protected static ?string $pluralModelLabel = 'lớp học';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return CourseClassForm::make($schema);
    }

    public static function table(Table $table): Table
    {
        return CourseClassesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListCourseClasses::route('/'),
            'create' => CreateCourseClass::route('/create'),
            'edit'   => EditCourseClass::route('/{record}/edit'),
        ];
    }
}
