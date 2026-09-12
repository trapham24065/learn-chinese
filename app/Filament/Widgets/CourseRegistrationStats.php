<?php

namespace App\Filament\Widgets;

use App\Models\CourseRegistration;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CourseRegistrationStats extends StatsOverviewWidget
{
    protected ?string $pollingInterval = '30s';

    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        $pendingCount = CourseRegistration::query()->where('status', 'pending')->count();
        $totalCount = CourseRegistration::query()->count();
        $confirmedCount = CourseRegistration::query()->whereIn('status', ['deposit_paid', 'paid', 'enrolled'])->count();
        $totalRevenue = CourseRegistration::query()->sum('payment_amount');

        return [
            Stat::make('Đơn mới chờ liên hệ', $pendingCount)
                ->description($pendingCount > 0 ? 'Cần gọi điện tư vấn ngay' : 'Không có đơn tồn')
                ->color($pendingCount > 0 ? 'warning' : 'success'),

            Stat::make('Tổng đơn đăng ký', $totalCount)
                ->description('Tất cả các khóa học')
                ->color('primary'),

            Stat::make('Đã cọc & Vào lớp', $confirmedCount)
                ->description('Học viên đã xác nhận học')
                ->color('success'),

            Stat::make('Tổng học phí đã thu', number_format((float) $totalRevenue, 0, ',', '.') . ' ₫')
                ->description('Thực thu qua VietQR & tiền mặt')
                ->color('info'),
        ];
    }
}
