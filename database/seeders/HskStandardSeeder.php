<?php

namespace Database\Seeders;

use App\Models\HskStandard;
use Illuminate\Database\Seeder;

class HskStandardSeeder extends Seeder
{
    /**
     * Seed HSK standards.
     */
    public function run(): void
    {
        $standards = [
            [
                'code'        => HskStandard::CODE_HSK_2_0,
                'name'        => 'HSK 2.0',
                'version'     => '2010',
                'is_active'   => true,
                'description' => 'Tiêu chuẩn HSK 6 cấp độ truyền thống ban hành năm 2010 bởi Hanban. Tổng cộng ~5.000 từ vựng.',
            ],
            [
                'code'        => HskStandard::CODE_HSK_3_0_2021,
                'name'        => 'HSK 3.0 (2021)',
                'version'     => '2021',
                'is_active'   => true,
                'description' => 'Tiêu chuẩn Đánh giá Trình độ tiếng Trung Quốc tế (GF 0025-2021) công bố năm 2021. Cấu trúc 3 giai đoạn 9 cấp độ.',
            ],
            [
                'code'        => HskStandard::CODE_HSK_3_0_2026,
                'name'        => 'HSK 3.0 (2026)',
                'version'     => '2026',
                'is_active'   => true,
                'description' => 'Đợt triển khai chính thức đề thi HSK 3.0 toàn cầu từ ngày 13/12/2026 bởi CTI / Chinese Test Service.',
            ],
        ];

        foreach ($standards as $s) {
            HskStandard::updateOrCreate(
                ['code' => $s['code']],
                $s
            );
        }

        if ($this->command) {
            $this->command->info('Đã nạp 3 tiêu chuẩn HSK: ' . implode(', ', array_column($standards, 'code')));
        }
    }
}
