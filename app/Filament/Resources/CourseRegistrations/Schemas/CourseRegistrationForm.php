<?php

namespace App\Filament\Resources\CourseRegistrations\Schemas;

use App\Models\CourseClass;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CourseRegistrationForm
{
    public static function make(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('registration_code')
                    ->label('Mã đăng ký')
                    ->disabled()
                    ->dehydrated(false),

                TextInput::make('full_name')
                    ->label('Họ và tên học viên')
                    ->required()
                    ->maxLength(255),

                TextInput::make('phone')
                    ->label('Số điện thoại')
                    ->tel()
                    ->required()
                    ->maxLength(20),

                TextInput::make('zalo')
                    ->label('Zalo (nếu khác SĐT)')
                    ->maxLength(20),

                TextInput::make('email')
                    ->label('Địa chỉ Email')
                    ->email()
                    ->maxLength(255),

                Select::make('current_level')
                    ->label('Trình độ hiện tại')
                    ->options([
                        'chua_biet_gi' => 'Chưa biết gì (Người mới bắt đầu)',
                        'co_ban_phat_am' => 'Biết phát âm cơ bản (Pinyin)',
                        'hsk1_2' => 'Đã học qua HSK 1 - 2',
                        'hsk3_4' => 'Đã học qua HSK 3 - 4',
                        'giao_tiep' => 'Muốn tập trung phản xạ giao tiếp',
                    ])
                    ->default('chua_biet_gi')
                    ->required(),

                TextInput::make('preferred_schedule')
                    ->label('Khung giờ mong muốn')
                    ->placeholder('Ví dụ: Tối 2-4-6 sau 19h')
                    ->maxLength(255),

                Select::make('course_id')
                    ->label('Khóa học quan tâm')
                    ->relationship('course', 'title')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->live(),

                Select::make('course_class_id')
                    ->label('Xếp vào lớp cụ thể')
                    ->options(function ($get) {
                        $courseId = $get('course_id');
                        if (! $courseId) {
                            return CourseClass::pluck('name', 'id');
                        }

                        return CourseClass::where('course_id', $courseId)->pluck('name', 'id');
                    })
                    ->searchable()
                    ->nullable(),

                Select::make('status')
                    ->label('Trạng thái tiếp cận (CRM)')
                    ->options([
                        'pending' => 'Mới đăng ký (Chờ liên hệ)',
                        'contacted' => 'Đã liên hệ cuộc gọi đầu',
                        'consulted' => 'Đã tư vấn lộ trình',
                        'deposit_paid' => 'Đã đặt cọc giữ chỗ',
                        'paid' => 'Đã hoàn tất thanh toán',
                        'enrolled' => 'Đã thêm vào lớp học Meet',
                        'cancelled' => 'Đã hủy / Từ chối',
                    ])
                    ->default('pending')
                    ->required(),

                Select::make('payment_status')
                    ->label('Trạng thái học phí')
                    ->options([
                        'unpaid' => 'Chưa thanh toán',
                        'deposit' => 'Đã đặt cọc',
                        'paid' => 'Đã thanh toán đủ 100%',
                        'refunded' => 'Đã hoàn tiền',
                    ])
                    ->default('unpaid')
                    ->required(),

                TextInput::make('payment_amount')
                    ->label('Số tiền đã đóng (VNĐ)')
                    ->numeric()
                    ->prefix('₫')
                    ->default(0),

                Select::make('payment_method')
                    ->label('Hình thức thanh toán')
                    ->options([
                        'bank_transfer' => 'Chuyển khoản Ngân hàng (VietQR)',
                        'momo' => 'Ví điện tử MoMo',
                        'cash' => 'Tiền mặt',
                        'other' => 'Khác',
                    ])
                    ->default('bank_transfer'),

                DateTimePicker::make('paid_at')
                    ->label('Thời gian đóng tiền')
                    ->nullable(),

                TextInput::make('payment_reference')
                    ->label('Mã tham chiếu / Nội dung CK')
                    ->maxLength(100),

                Textarea::make('learning_goal')
                    ->label('Mục tiêu học tập của học viên')
                    ->rows(2)
                    ->columnSpanFull(),

                Textarea::make('admin_notes')
                    ->label('Ghi chú nội bộ giáo viên / Tư vấn viên')
                    ->rows(3)
                    ->columnSpanFull()
                    ->helperText('Ghi chép lịch sử trao đổi, thời gian hẹn gọi lại, yêu cầu riêng của học viên...'),
            ]);
    }
}
