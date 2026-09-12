<?php

namespace App\Filament\Resources\CourseClasses\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CourseClassForm
{
    public static function make(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('course_id')
                    ->label('Thuộc khóa học')
                    ->relationship('course', 'title')
                    ->searchable()
                    ->preload()
                    ->required(),

                TextInput::make('name')
                    ->label('Tên lớp / Kỳ mở')
                    ->placeholder('Ví dụ: Lớp T10 - Tối 2-4-6 (19:30)')
                    ->required()
                    ->maxLength(255),

                TextInput::make('code')
                    ->label('Mã lớp (Code duy nhất)')
                    ->placeholder('Ví dụ: HSK12-2610A')
                    ->required()
                    ->maxLength(50)
                    ->unique('course_classes', 'code', ignoreRecord: true),

                Select::make('status')
                    ->label('Trạng thái lớp')
                    ->options([
                        'draft' => 'Bản nháp (Chưa công khai)',
                        'open' => 'Đang mở tuyển sinh',
                        'full' => 'Đã đủ học viên (Tạm dừng nhận)',
                        'ongoing' => 'Đang diễn ra khóa học',
                        'completed' => 'Đã bế giảng / Kết thúc',
                        'cancelled' => 'Đã hủy lớp',
                    ])
                    ->default('open')
                    ->required(),

                DatePicker::make('start_date')
                    ->label('Ngày khai giảng')
                    ->required(),

                DatePicker::make('end_date')
                    ->label('Ngày bế giảng (dự kiến)')
                    ->nullable(),

                TextInput::make('schedule_days')
                    ->label('Các buổi học')
                    ->placeholder('Ví dụ: Thứ 2 - 4 - 6')
                    ->required(),

                TextInput::make('schedule_time')
                    ->label('Khung giờ học')
                    ->placeholder('Ví dụ: 19:30 - 21:00')
                    ->required(),

                TextInput::make('max_students')
                    ->label('Sĩ số tối đa')
                    ->numeric()
                    ->default(12)
                    ->required(),

                TextInput::make('meet_url')
                    ->label('Link Google Meet lớp học (Bảo mật)')
                    ->placeholder('https://meet.google.com/xxx-yyyy-zzz')
                    ->helperText('🔒 Link này được bảo mật, tuyệt đối KHÔNG hiển thị công khai ở ngoài trang web. Chỉ admin/giáo viên thấy và gửi cho học viên đã thanh toán/vào lớp.')
                    ->columnSpanFull(),

                Textarea::make('notes')
                    ->label('Ghi chú nội bộ')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }
}
