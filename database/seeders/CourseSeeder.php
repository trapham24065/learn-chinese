<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\CourseClass;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $coursesData = [
            [
                'title' => 'Tiếng Trung Toàn Diện HSK 1 - 2 (Nhập môn & Cơ bản)',
                'slug' => 'tieng-trung-hsk-1-2-nhap-mon',
                'category' => 'hsk_starter',
                'summary' => 'Khóa học nền tảng toàn diện dành cho người mới bắt đầu hoặc mất gốc. Chuẩn hóa ngữ âm Pinyin, nắm vững 300 từ vựng và tự tin giao tiếp căn bản.',
                'description' => "Khóa học **Tiếng Trung Toàn Diện HSK 1 - 2** được thiết kế bài bản, hiện đại, giúp người học từ con số 0 xây dựng nền móng vững chắc cả 4 kỹ năng Nghe - Nói - Đọc - Viết.\n\n### Điểm nổi bật của khóa học:\n- **Chuẩn hóa phát âm Pinyin từ đầu**: Giáo viên chỉnh sửa khẩu hình và thanh điệu trực tiếp từng học viên qua Google Meet.\n- **Học từ vựng thông minh**: Ứng dụng phương pháp chiết tự chữ Hán, câu chuyện chữ và sơ đồ tư duy giúp nhớ lâu, không bị áp lực.\n- **Tập trung phản xạ giao tiếp**: Không học vẹt ngữ pháp, 60% thời lượng buổi học dành cho luyện tập đối thoại thực tế.\n- **Cam kết đầu ra**: Học viên hoàn thành khóa học tự tin đạt chứng chỉ HSK 2 quốc tế và giao tiếp lưu loát các chủ đề đời sống hàng ngày.",
                'original_price' => 2800000,
                'price' => 1950000,
                'duration_weeks' => 8,
                'total_sessions' => 24,
                'highlights' => [
                    'Chuẩn hóa ngữ âm Bắc Kinh từ gốc, sửa phát âm 1:1 qua Google Meet',
                    'Nắm vững 300+ từ vựng & 50 cấu trúc ngữ pháp HSK 1-2 cốt lõi',
                    'Tự tin giao tiếp các chủ đề: mua sắm, hỏi đường, ẩm thực, gia đình',
                    'Cung cấp bản ghi video buổi học xem lại trọn đời khóa học',
                    'Nhóm Zalo hỗ trợ giải đáp bài tập 24/7 cùng giáo viên',
                    'Tặng bộ flashcard độc quyền và tài liệu PDF luyện thi',
                ],
                'curriculum' => [
                    [
                        'phase' => 'Chặng 1: Nền tảng Ngữ âm & Giao tiếp Sơ cấp',
                        'sessions' => 'Buổi 1 - 8',
                        'content' => 'Bảng chữ cái Pinyin, Thanh mẫu, Vận mẫu, 4 Thanh điệu và quy tắc biến âm. Quy tắc viết chữ Hán, bộ thủ cơ bản. Giao tiếp: Chào hỏi, xưng hô, quốc tịch, nghề nghiệp.',
                    ],
                    [
                        'phase' => 'Chặng 2: Đời sống & Phản xạ Thường nhật',
                        'sessions' => 'Buổi 9 - 16',
                        'content' => 'Con số, ngày tháng năm, xem giờ giấc. Mua sắm hàng hóa, hỏi giá tiền, đổi tiền tệ. Giới thiệu thành viên gia đình, bạn bè và sở thích cá nhân.',
                    ],
                    [
                        'phase' => 'Chặng 3: Định hướng Không gian & Tương tác Xã hội',
                        'sessions' => 'Buổi 17 - 20',
                        'content' => 'Phương hướng, vị trí, gọi xe taxi, hỏi đường đi. Ăn uống tại nhà hàng, gọi món ăn Trung Hoa, thói quen sinh hoạt và lịch trình một ngày.',
                    ],
                    [
                        'phase' => 'Chặng 4: Tổng kết & Luyện thi HSK 2',
                        'sessions' => 'Buổi 21 - 24',
                        'content' => 'Tổng hợp toàn bộ ngữ pháp HSK 1-2. Kỹ thuật làm bài thi Nghe và Đọc hiểu HSK 2. Thi thử mô phỏng phòng thi thật và chữa bài chi tiết.',
                    ],
                ],
                'is_active' => true,
                'sort_order' => 1,
                'classes' => [
                    [
                        'name' => 'Lớp HSK12 - Tối 2-4-6 (19:30 - 21:00)',
                        'code' => 'HSK12-2610A',
                        'start_date' => '2026-10-05',
                        'end_date' => '2026-11-27',
                        'schedule_days' => 'Thứ 2 - 4 - 6',
                        'schedule_time' => '19:30 - 21:00',
                        'max_students' => 12,
                        'status' => 'open',
                        'notes' => 'Lớp học trực tuyến tương tác cao qua Google Meet. Tối đa 12 học viên để giáo viên theo sát từng bạn.',
                    ],
                    [
                        'name' => 'Lớp HSK12 - Tối 3-5-7 (20:00 - 21:30)',
                        'code' => 'HSK12-2610B',
                        'start_date' => '2026-10-15',
                        'end_date' => '2026-12-08',
                        'schedule_days' => 'Thứ 3 - 5 - 7',
                        'schedule_time' => '20:00 - 21:30',
                        'max_students' => 12,
                        'status' => 'open',
                        'notes' => 'Lịch học phù hợp cho người đi làm và sinh viên bận rộn.',
                    ],
                ],
            ],
            [
                'title' => 'Chinh phục HSK 3 - 4 Chuyên Sâu (Trung cấp & Phản xạ)',
                'slug' => 'chinh-phuc-hsk-3-4-chuyen-sau',
                'category' => 'hsk_intermediate',
                'summary' => 'Bứt phá vốn từ lên 1.200 từ vựng, làm chủ các cấu trúc ngữ pháp phức tạp, nâng cao tốc độ đọc hiểu và phản xạ nói tự nhiên.',
                'description' => "Dành cho học viên đã có nền tảng HSK 2 hoặc tương đương muốn nâng cấp năng lực tiếng Trung lên trình độ Trung cấp, phục vụ du học, làm việc tại công ty Trung Quốc hoặc apply học bổng CIS/CSC.\n\nKhóa học tập trung rèn luyện tư duy ngôn ngữ bằng tiếng Trung, loại bỏ thói quen dịch nhẩm tiếng Việt trong đầu.",
                'original_price' => 3800000,
                'price' => 2850000,
                'duration_weeks' => 10,
                'total_sessions' => 30,
                'highlights' => [
                    'Mở rộng vốn từ vựng lên 1.200+ từ và thành ngữ thông dụng',
                    'Thành thạo cấu trúc câu chữ 把, câu chữ 被, bổ ngữ kết quả & bổ ngữ xu hướng',
                    'Luyện kỹ năng đọc hiểu nhanh và trích xuất thông tin đoạn văn dài',
                    'Luyện kỹ năng viết đoạn văn và mô tả biểu cảm linh hoạt',
                    'Cam kết đầu ra chuẩn HSK 4 quốc tế (tối thiểu 210+ điểm)',
                    'Bản ghi video từng buổi học kèm tài liệu ôn luyện độc quyền',
                ],
                'curriculum' => [
                    [
                        'phase' => 'Chặng 1: Nâng cấp Ngữ pháp Trung cấp & Mở rộng Từ vựng',
                        'sessions' => 'Buổi 1 - 8',
                        'content' => 'Bổ ngữ trạng thái, bổ ngữ khả năng, bổ ngữ xu hướng phức tạp. Phân biệt các phó từ gần nghĩa: 又 / 再, 刚 / 刚才. Viết câu phức biểu thị nguyên nhân - kết quả, giả thiết.',
                    ],
                    [
                        'phase' => 'Chặng 2: Cấu trúc Đặc thù & Đọc hiểu Tốc độ cao',
                        'sessions' => 'Buổi 9 - 18',
                        'content' => 'Câu chữ 把 và câu chữ 被 ứng dụng trong giao tiếp và văn bản. Kỹ thuật scanning/skimming đoạn văn ngắn, nắm bắt ý chính trong bài thi HSK 4.',
                    ],
                    [
                        'phase' => 'Chặng 3: Phản xạ Nghe nói Đa chủ đề & Văn phong Đời sống',
                        'sessions' => 'Buổi 19 - 24',
                        'content' => 'Giao tiếp chủ đề công việc, quan hệ xã hội, môi trường, du lịch trải nghiệm. Nghe bắt từ khóa trong các đoạn hội thoại dài của người bản ngữ.',
                    ],
                    [
                        'phase' => 'Chặng 4: Luyện đề Chuyên sâu & Chiến thuật Phòng thi',
                        'sessions' => 'Buổi 25 - 30',
                        'content' => 'Giải bộ đề thi thật HSK 4 các năm gần nhất. Mẹo sắp xếp từ thành câu chuẩn xác và viết đoạn văn miêu tả tranh đạt điểm tối đa.',
                    ],
                ],
                'is_active' => true,
                'sort_order' => 2,
                'classes' => [
                    [
                        'name' => 'Lớp HSK34 - Tối 3-5-7 (19:30 - 21:00)',
                        'code' => 'HSK34-2610A',
                        'start_date' => '2026-10-06',
                        'end_date' => '2026-12-15',
                        'schedule_days' => 'Thứ 3 - 5 - 7',
                        'schedule_time' => '19:30 - 21:00',
                        'max_students' => 10,
                        'status' => 'open',
                        'notes' => 'Lớp giới hạn tối đa 10 học viên để đảm bảo chất lượng phản xạ và chữa bài tập chi tiết.',
                    ],
                ],
            ],
            [
                'title' => 'Tiếng Trung Giao Tiếp Thực Chiến (Phản xạ & Đời sống)',
                'slug' => 'tieng-trung-giao-tiep-thuc-chien',
                'category' => 'conversation',
                'summary' => '100% thời lượng tương tác nói trực tiếp qua Google Meet. Sửa ngữ điệu tự nhiên, tự tin bắt chuyện, du lịch và order hàng Taobao/1688.',
                'description' => "Bạn đã học tiếng Trung nhưng ngại mở miệng nói? Bạn hiểu ngữ pháp nhưng không thể bật ra thành câu khi gặp người Trung Quốc?\n\nKhóa học **Giao Tiếp Thực Chiến** sẽ giúp bạn phá vỡ rào cản tâm lý, tự tin bật phản xạ nói tự nhiên chỉ sau 6 tuần tập trung.",
                'original_price' => 3200000,
                'price' => 2450000,
                'duration_weeks' => 6,
                'total_sessions' => 18,
                'highlights' => [
                    'Lớp học sĩ số nhỏ (tối đa 8 bạn) - mỗi buổi nói tối thiểu 20-30 phút',
                    'Sửa ngữ điệu và khẩu ngữ tự nhiên như người bản xứ',
                    'Tình huống thực tế: mua sắm, trả giá, gọi món, đặt vé máy bay, du lịch tự túc',
                    'Học cách chat và trao đổi mặc cả với chủ shop trên Taobao, 1688',
                    'Cung cấp bản ghi buổi học để xem lại và luyện phát âm theo',
                ],
                'curriculum' => [
                    [
                        'phase' => 'Phần 1: Bật phản xạ & Phá vỡ nỗi sợ nói tiếng Trung',
                        'sessions' => 'Buổi 1 - 6',
                        'content' => 'Ngữ điệu tự nhiên trong giao tiếp hàng ngày. Các thán từ, trợ từ ngữ khí thường dùng. Bắt chuyện với người lạ, kết bạn và chia sẻ cảm xúc.',
                    ],
                    [
                        'phase' => 'Phần 2: Cẩm nang Giao tiếp Du lịch & Đời sống Thực tế',
                        'sessions' => 'Buổi 7 - 12',
                        'content' => 'Check-in sân bay, đặt phòng khách sạn, hỏi đường, bắt xe công nghệ. Trải nghiệm ẩm thực, gọi món theo khẩu vị, cách trả giá thông minh khi mua sắm.',
                    ],
                    [
                        'phase' => 'Phần 3: Giao tiếp Đặt hàng Online & Xử lý Tình huống Bất ngờ',
                        'sessions' => 'Buổi 13 - 18',
                        'content' => 'Giao dịch online với shop Taobao/1688, khiếu nại đổi trả, hỏi kích cỡ. Xử lý sự cố khi lạc đường, ốm đau hoặc cần trợ giúp khẩn cấp.',
                    ],
                ],
                'is_active' => true,
                'sort_order' => 3,
                'classes' => [
                    [
                        'name' => 'Lớp Giao Tiếp Cuối Tuần (T7 & Chủ Nhật)',
                        'code' => 'GT-2610A',
                        'start_date' => '2026-10-10',
                        'end_date' => '2026-11-22',
                        'schedule_days' => 'Thứ 7 & Chủ Nhật',
                        'schedule_time' => '09:00 - 10:30',
                        'max_students' => 8,
                        'status' => 'open',
                        'notes' => 'Lịch học cuối tuần thư thái, nhiều thời lượng thực hành đóng vai và trò chuyện.',
                    ],
                ],
            ],
            [
                'title' => 'Tiếng Trung Thương Mại & Đàm Phán Hợp Đồng (Business Chinese)',
                'slug' => 'tieng-trung-thuong-mai-dam-phan',
                'category' => 'business',
                'summary' => 'Chuyên ngành xuất nhập khẩu, kỹ năng soạn email công việc chuẩn mực, đàm phán điều khoản hợp đồng và văn hóa ứng xử kinh doanh Trung Hoa.',
                'description' => "Chương trình chuyên biệt cho người đi làm tại các doanh nghiệp FDI, công ty xuất nhập khẩu, thương mại điện tử hoặc chủ shop đánh hàng Quảng Châu.\n\nHọc viên được trang bị thuật ngữ chuẩn xác, văn phong giao dịch trang trọng và kỹ năng mềm đàm phán thực tiễn.",
                'original_price' => 4500000,
                'price' => 3500000,
                'duration_weeks' => 8,
                'total_sessions' => 24,
                'highlights' => [
                    'Hệ thống thuật ngữ xuất nhập khẩu, logistics và thương mại quốc tế',
                    'Kỹ năng viết thư điện tử (Email) và văn bản công vụ chuẩn xác',
                    'Đàm phán giá cả, chiết khấu, phương thức thanh toán L/C, T/T',
                    'Soạn thảo và thẩm định các điều khoản hợp đồng kinh tế bằng tiếng Trung',
                    'Bí quyết văn hóa bàn tiệc và xã giao với đối tác doanh nghiệp Trung Quốc',
                ],
                'curriculum' => [
                    [
                        'phase' => 'Chặng 1: Giao tiếp Doanh nghiệp & Nghi thức Thương mại',
                        'sessions' => 'Buổi 1 - 6',
                        'content' => 'Văn hóa công sở, chức danh và xưng hô chuẩn mực. Tiếp đón đối tác nước ngoài, thăm nhà máy, tham quan triển lãm hội chợ thương mại.',
                    ],
                    [
                        'phase' => 'Chặng 2: Thư tín Thương mại & Hỏi giá - Báo giá',
                        'sessions' => 'Buổi 7 - 12',
                        'content' => 'Quy chuẩn soạn thảo email trao đổi công việc. Mẫu thư hỏi hàng (Inquiry), báo giá (Quotation), mặc cả giảm giá và yêu cầu gửi mẫu thử.',
                    ],
                    [
                        'phase' => 'Chặng 3: Đàm phán Điều khoản & Thanh toán Quốc tế',
                        'sessions' => 'Buổi 13 - 18',
                        'content' => 'Điều kiện giao hàng Incoterms (FOB, CIF, EXW). Phương thức thanh toán quốc tế (T/T, L/C, D/P). Ký kết hợp đồng nguyên tắc và đơn đặt hàng PO.',
                    ],
                    [
                        'phase' => 'Chặng 4: Xử lý Khiếu nại, Tranh chấp & Tổng kết',
                        'sessions' => 'Buổi 19 - 24',
                        'content' => 'Quy trình giải quyết khiếu nại chất lượng hàng hóa, chậm trễ giao hàng, bồi thường thiệt hại. Thực hành đàm phán hợp đồng theo nhóm giả lập.',
                    ],
                ],
                'is_active' => true,
                'sort_order' => 4,
                'classes' => [
                    [
                        'name' => 'Lớp Thương Mại - Tối 2-4-6 (20:00 - 21:30)',
                        'code' => 'BIZ-2611A',
                        'start_date' => '2026-11-02',
                        'end_date' => '2026-12-25',
                        'schedule_days' => 'Thứ 2 - 4 - 6',
                        'schedule_time' => '20:00 - 21:30',
                        'max_students' => 8,
                        'status' => 'open',
                        'notes' => 'Yêu cầu đầu vào tối thiểu tương đương HSK 3 để tiếp thu bài học hiệu quả nhất.',
                    ],
                ],
            ],
        ];

        foreach ($coursesData as $cData) {
            $classes = $cData['classes'] ?? [];
            unset($cData['classes']);

            $course = Course::updateOrCreate(
                ['slug' => $cData['slug']],
                $cData
            );

            foreach ($classes as $classData) {
                $classData['course_id'] = $course->id;
                CourseClass::updateOrCreate(
                    ['code' => $classData['code']],
                    $classData
                );
            }
        }
    }
}
