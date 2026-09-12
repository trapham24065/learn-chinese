<?php

namespace App\Filament\Resources\CourseRegistrations\Tables;

use App\Models\CourseRegistration;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CourseRegistrationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('registration_code')
                    ->label('Mã ĐK')
                    ->badge()
                    ->color('primary')
                    ->copyable()
                    ->sortable()
                    ->searchable(),

                TextColumn::make('full_name')
                    ->label('Họ và tên')
                    ->weight('bold')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('phone')
                    ->label('Số điện thoại')
                    ->searchable()
                    ->copyable(),

                TextColumn::make('course.title')
                    ->label('Khóa học')
                    ->limit(24)
                    ->sortable()
                    ->searchable(),

                TextColumn::make('courseClass.name')
                    ->label('Lớp học')
                    ->limit(20)
                    ->placeholder('Chưa xếp lớp')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('status')
                    ->label('Trạng thái CRM')
                    ->badge()
                    ->formatStateUsing(fn (CourseRegistration $record): string => $record->status_label)
                    ->color(fn (CourseRegistration $record): string => $record->status_color)
                    ->alignCenter(),

                TextColumn::make('payment_status')
                    ->label('Học phí')
                    ->badge()
                    ->formatStateUsing(fn (CourseRegistration $record): string => $record->payment_status_label)
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'deposit' => 'warning',
                        'unpaid' => 'danger',
                        'refunded' => 'gray',
                        default => 'gray',
                    })
                    ->alignCenter(),

                TextColumn::make('payment_amount')
                    ->label('Đã đóng')
                    ->formatStateUsing(fn ($state) => number_format((float) $state, 0, ',', '.') . ' ₫')
                    ->alignEnd()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Ngày đăng ký')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->color('gray'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('course_id')
                    ->label('Khóa học')
                    ->relationship('course', 'title'),

                SelectFilter::make('status')
                    ->label('Trạng thái CRM')
                    ->options([
                        'pending' => 'Mới đăng ký (Chờ liên hệ)',
                        'contacted' => 'Đã liên hệ',
                        'consulted' => 'Đã tư vấn',
                        'deposit_paid' => 'Đã đặt cọc',
                        'paid' => 'Đã thanh toán',
                        'enrolled' => 'Đã vào lớp Meet',
                        'cancelled' => 'Đã hủy',
                    ]),

                SelectFilter::make('payment_status')
                    ->label('Học phí')
                    ->options([
                        'unpaid' => 'Chưa thanh toán',
                        'deposit' => 'Đã đặt cọc',
                        'paid' => 'Đã thanh toán',
                        'refunded' => 'Đã hoàn tiền',
                    ]),
            ])
            ->recordActions([
                Action::make('call')
                    ->label('Gọi')
                    ->icon(Heroicon::OutlinedPhone)
                    ->color('info')
                    ->url(fn (CourseRegistration $record): string => 'tel:' . $record->phone),

                Action::make('zalo')
                    ->label('Zalo')
                    ->icon(Heroicon::OutlinedChatBubbleLeftRight)
                    ->color('success')
                    ->url(fn (CourseRegistration $record): string => 'https://zalo.me/' . ($record->phone_normalized ?: $record->phone), shouldOpenInNewTab: true),

                Action::make('logActivity')
                    ->label('CRM')
                    ->icon(Heroicon::OutlinedClock)
                    ->color('warning')
                    ->modalHeading('Ghi nhận chăm sóc học viên & Đổi trạng thái')
                    ->schema([
                        Select::make('type')
                            ->label('Loại hoạt động')
                            ->options([
                                'call' => '📞 Cuộc gọi điện thoại',
                                'zalo_sent' => '💬 Nhắn tin trao đổi qua Zalo',
                                'status_changed' => '🔄 Chuyển trạng thái tuyển sinh',
                                'payment_updated' => '💰 Cập nhật thông tin thanh toán/cọc',
                                'meet_sent' => '📹 Đã gửi link Google Meet & Hướng dẫn',
                                'note_added' => '📝 Ghi chú thông tin cá nhân',
                            ])
                            ->default('call')
                            ->required(),

                        Select::make('new_status')
                            ->label('Cập nhật trạng thái CRM (tùy chọn)')
                            ->options([
                                'pending' => 'Mới đăng ký (Chờ liên hệ)',
                                'contacted' => 'Đã liên hệ cuộc gọi đầu',
                                'consulted' => 'Đã tư vấn lộ trình',
                                'deposit_paid' => 'Đã đặt cọc',
                                'paid' => 'Đã thanh toán 100%',
                                'enrolled' => 'Đã thêm vào lớp học',
                                'cancelled' => 'Đã hủy / Bỏ cuộc',
                            ])
                            ->nullable(),

                        Textarea::make('description')
                            ->label('Nội dung trao đổi / Ghi chú')
                            ->placeholder('Ví dụ: Đã gọi điện tư vấn lộ trình HSK 1-2, học viên hẹn chuyển cọc trước ngày 20...')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function (CourseRegistration $record, array $data) {
                        if (! empty($data['new_status'])) {
                            $record->update(['status' => $data['new_status']]);
                        }

                        $record->recordActivity(
                            type: $data['type'],
                            description: $data['description'],
                            userId: auth()->id()
                        );

                        Notification::make()
                            ->title('Đã lưu hoạt động CRM thành công!')
                            ->success()
                            ->send();
                    }),

                EditAction::make()->label('Sửa'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('Xóa đã chọn'),
                ]),
            ]);
    }
}
