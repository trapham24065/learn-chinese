<?php

namespace Database\Seeders;

use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\StudySession;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $student = User::query()->updateOrCreate(
            ['email' => 'linh@example.com'],
            [
                'name' => 'Linh Nguyen',
                'password' => Hash::make('password'),
                'role' => User::ROLE_STUDENT,
            ]
        );

        $lessons = collect([
            [
                'slug' => 'pinyin-co-ban',
                'title' => 'Pinyin cơ bản',
                'summary' => 'Làm quen thanh điệu và cách phát âm chuẩn.',
                'difficulty' => 'starter',
                'hsk_level' => 1,
                'sort_order' => 1,
                'estimated_minutes' => 20,
                'accent_color' => '#991b1b',
            ],
            [
                'slug' => 'chao-hoi-gioi-thieu',
                'title' => 'Chào hỏi & giới thiệu',
                'summary' => 'Mẫu câu đầu tiên để chào và tự giới thiệu.',
                'difficulty' => 'starter',
                'hsk_level' => 1,
                'sort_order' => 2,
                'estimated_minutes' => 18,
                'accent_color' => '#b45309',
            ],
            [
                'slug' => 'tu-vung-gia-dinh',
                'title' => 'Từ vựng gia đình',
                'summary' => 'Nhóm từ cơ bản về thành viên gia đình và quan hệ.',
                'difficulty' => 'starter',
                'hsk_level' => 2,
                'sort_order' => 3,
                'estimated_minutes' => 15,
                'accent_color' => '#1f2937',
            ],
            [
                'slug' => 'so-dem-va-thoi-gian',
                'title' => 'Số đếm và thời gian',
                'summary' => 'Học cách nói số, giờ, ngày tháng và lịch trình.',
                'difficulty' => 'intermediate',
                'hsk_level' => 2,
                'sort_order' => 4,
                'estimated_minutes' => 25,
                'accent_color' => '#047857',
            ],
        ])->map(fn(array $lesson) => Lesson::query()->updateOrCreate(
            ['slug' => $lesson['slug']],
            $lesson + ['is_published' => true]
        ));

        $progressRows = [
            ['lesson' => 'pinyin-co-ban', 'status' => 'completed', 'progress_percent' => 100, 'started_at' => now()->subDays(10), 'last_accessed_at' => now()->subDay()->setTime(8, 15), 'completed_at' => now()->subDay()->setTime(8, 40)],
            ['lesson' => 'chao-hoi-gioi-thieu', 'status' => 'in_progress', 'progress_percent' => 78, 'started_at' => now()->subDays(4), 'last_accessed_at' => now()->setTime(8, 10), 'completed_at' => null],
            ['lesson' => 'tu-vung-gia-dinh', 'status' => 'in_progress', 'progress_percent' => 54, 'started_at' => now()->subDays(3), 'last_accessed_at' => now()->subHours(4), 'completed_at' => null],
            ['lesson' => 'so-dem-va-thoi-gian', 'status' => 'not_started', 'progress_percent' => 31, 'started_at' => now()->subDays(1), 'last_accessed_at' => now()->subHours(10), 'completed_at' => null],
        ];

        foreach ($progressRows as $row) {
            LessonProgress::query()->updateOrCreate(
                ['user_id' => $student->id, 'lesson_id' => $lessons->firstWhere('slug', $row['lesson'])->id],
                [
                    'status' => $row['status'],
                    'progress_percent' => $row['progress_percent'],
                    'started_at' => $row['started_at'],
                    'last_accessed_at' => $row['last_accessed_at'],
                    'completed_at' => $row['completed_at'],
                ]
            );
        }

        $sessionRows = [
            ['lesson' => 'pinyin-co-ban', 'session_type' => 'lesson', 'duration_minutes' => 20, 'score' => null, 'started_at' => now()->subDays(6)->setTime(8, 30), 'completed_at' => now()->subDays(6)->setTime(8, 52)],
            ['lesson' => 'pinyin-co-ban', 'session_type' => 'quiz', 'duration_minutes' => 12, 'score' => 84, 'started_at' => now()->subDays(5)->setTime(8, 40), 'completed_at' => now()->subDays(5)->setTime(8, 55)],
            ['lesson' => 'chao-hoi-gioi-thieu', 'session_type' => 'flashcard', 'duration_minutes' => 15, 'score' => 90, 'started_at' => now()->subDays(4)->setTime(9, 5), 'completed_at' => now()->subDays(4)->setTime(9, 20)],
            ['lesson' => 'tu-vung-gia-dinh', 'session_type' => 'lesson', 'duration_minutes' => 18, 'score' => null, 'started_at' => now()->subDays(3)->setTime(8, 10), 'completed_at' => now()->subDays(3)->setTime(8, 35)],
            ['lesson' => 'tu-vung-gia-dinh', 'session_type' => 'quiz', 'duration_minutes' => 10, 'score' => 86, 'started_at' => now()->subDays(2)->setTime(8, 15), 'completed_at' => now()->subDays(2)->setTime(8, 28)],
            ['lesson' => 'so-dem-va-thoi-gian', 'session_type' => 'flashcard', 'duration_minutes' => 14, 'score' => 92, 'started_at' => now()->subDay()->setTime(7, 50), 'completed_at' => now()->subDay()->setTime(8, 5)],
            ['lesson' => 'so-dem-va-thoi-gian', 'session_type' => 'quiz', 'duration_minutes' => 9, 'score' => 88, 'started_at' => now()->setTime(8, 10), 'completed_at' => now()->setTime(8, 22)],
        ];

        foreach ($sessionRows as $row) {
            StudySession::query()->updateOrCreate(
                [
                    'user_id' => $student->id,
                    'lesson_id' => $lessons->firstWhere('slug', $row['lesson'])->id,
                    'session_type' => $row['session_type'],
                    'started_at' => $row['started_at'],
                ],
                [
                    'duration_minutes' => $row['duration_minutes'],
                    'score' => $row['score'],
                    'completed_at' => $row['completed_at'],
                ]
            );
        }

        User::query()->updateOrCreate(
            ['email' => 'admin-local@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'role' => User::ROLE_ADMIN,
            ]
        );

        $questionRows = [
            [
                'lesson' => 'pinyin-co-ban',
                'question' => 'Thanh 1 trong tiếng Trung có đặc điểm phát âm như thế nào?',
                'pinyin' => 'shēngdiào',
                'options' => ['Cao và bằng phẳng', 'Đi lên từ thấp đến cao', 'Xuống rồi lên', 'Từ cao rơi nhanh xuống'],
                'correct_answer' => 'Cao và bằng phẳng',
                'explanation' => 'Thanh 1 (âm bổng) phát âm cao đều, ngang và kéo dài nhẹ (ví dụ: mā).',
                'difficulty' => 'starter',
                'sort_order' => 1,
            ],
            [
                'lesson' => 'pinyin-co-ban',
                'question' => 'Khi hai thanh 3 đi liền nhau (ví dụ: 你好 nǐ hǎo), quy tắc biến điệu là gì?',
                'pinyin' => 'nǐ hǎo',
                'options' => ['Thanh 3 đầu đổi thành thanh 2', 'Thanh 3 sau đổi thành thanh 1', 'Cả hai đổi thành thanh 4', 'Giữ nguyên không đổi'],
                'correct_answer' => 'Thanh 3 đầu đổi thành thanh 2',
                'explanation' => 'Quy tắc biến âm 2 thanh 3: từ đầu tiên đọc thành thanh 2 (ní hǎo) nhưng khi viết pinyin vẫn giữ nguyên dấu.',
                'difficulty' => 'starter',
                'sort_order' => 2,
            ],
            [
                'lesson' => 'chao-hoi-gioi-thieu',
                'question' => '你好 (nǐ hǎo) có nghĩa là gì?',
                'pinyin' => 'nǐ hǎo',
                'options' => ['Cảm ơn', 'Xin chào', 'Tạm biệt', 'Xin lỗi'],
                'correct_answer' => 'Xin chào',
                'explanation' => '你好 (nǐ hǎo) là câu chào hỏi cơ bản và thông dụng nhất trong tiếng Trung.',
                'difficulty' => 'starter',
                'sort_order' => 1,
            ],
            [
                'lesson' => 'chao-hoi-gioi-thieu',
                'question' => 'Mẫu câu nào dùng để giới thiệu tên mình?',
                'pinyin' => 'wǒ jiào...',
                'options' => ['我叫 Linh。', '他是老师。', '我很高兴。', '再见。'],
                'correct_answer' => '我叫 Linh。',
                'explanation' => 'Cấu trúc: 我叫 + [Tên] dùng để nói "Tôi tên là...".',
                'difficulty' => 'starter',
                'sort_order' => 2,
            ],
            [
                'lesson' => 'chao-hoi-gioi-thieu',
                'question' => '再见 (zài jiàn) thường dùng trong hoàn cảnh nào?',
                'pinyin' => 'zài jiàn',
                'options' => ['Khi tạm biệt', 'Khi chào buổi sáng', 'Khi giới thiệu bản thân', 'Khi hỏi tuổi'],
                'correct_answer' => 'Khi tạm biệt',
                'explanation' => '再见 (zài jiàn) nghĩa đen là "hẹn gặp lại", dùng khi chia tay hoặc tạm biệt.',
                'difficulty' => 'starter',
                'sort_order' => 3,
            ],
            [
                'lesson' => 'chao-hoi-gioi-thieu',
                'question' => '谢谢 (xièxie) dùng để làm gì?',
                'pinyin' => 'xiè xie',
                'options' => ['Bày tỏ sự cảm ơn', 'Nói lời xin lỗi', 'Chào khi gặp mặt', 'Hỏi đường'],
                'correct_answer' => 'Bày tỏ sự cảm ơn',
                'explanation' => '谢谢 (xièxie) là từ dùng để cảm ơn người khác.',
                'difficulty' => 'starter',
                'sort_order' => 4,
            ],
            [
                'lesson' => 'tu-vung-gia-dinh',
                'question' => 'Từ "老师" trong tiếng Trung có nghĩa là gì?',
                'pinyin' => 'lǎo shī',
                'options' => ['Giáo viên', 'Bác sĩ', 'Học sinh', 'Bạn bè'],
                'correct_answer' => 'Giáo viên',
                'explanation' => '老师 (lǎoshī) nghĩa là thầy giáo, cô giáo.',
                'difficulty' => 'starter',
                'sort_order' => 1,
            ],
            [
                'lesson' => 'tu-vung-gia-dinh',
                'question' => 'Từ nào có nghĩa là "Mẹ"?',
                'pinyin' => 'mā ma',
                'options' => ['妈妈', '爸爸', '姐姐', '弟弟'],
                'correct_answer' => '妈妈',
                'explanation' => '妈妈 (māma) nghĩa là mẹ; 爸爸 (bàba) là bố; 姐姐 (jiějie) là chị gái; 弟弟 (dìdi) là em trai.',
                'difficulty' => 'starter',
                'sort_order' => 2,
            ],
            [
                'lesson' => 'tu-vung-gia-dinh',
                'question' => 'Từ "朋友" mang ý nghĩa gì?',
                'pinyin' => 'péng you',
                'options' => ['Bạn bè', 'Thành viên', 'Đồng nghiệp', 'Hàng xóm'],
                'correct_answer' => 'Bạn bè',
                'explanation' => '朋友 (péngyou) là bạn bè.',
                'difficulty' => 'starter',
                'sort_order' => 3,
            ],
            [
                'lesson' => 'so-dem-va-thoi-gian',
                'question' => 'Số 8 trong tiếng Trung được viết bằng chữ Hán nào?',
                'pinyin' => 'bā',
                'options' => ['八', '六', '七', '九'],
                'correct_answer' => '八',
                'explanation' => '八 (bā) là số 8. 六 (liù) = 6, 七 (qī) = 7, 九 (jiǔ) = 9.',
                'difficulty' => 'intermediate',
                'sort_order' => 1,
            ],
            [
                'lesson' => 'so-dem-va-thoi-gian',
                'question' => 'Câu "现在几点？" có ý nghĩa là gì?',
                'pinyin' => 'Xiànzài jǐ diǎn?',
                'options' => ['Bây giờ là mấy giờ?', 'Hôm nay ngày mấy?', 'Bạn đi đâu đấy?', 'Món này bao nhiêu tiền?'],
                'correct_answer' => 'Bây giờ là mấy giờ?',
                'explanation' => '现在 (xiànzài) = bây giờ, 几点 (jǐ diǎn) = mấy giờ.',
                'difficulty' => 'intermediate',
                'sort_order' => 2,
            ],
            [
                'lesson' => 'so-dem-va-thoi-gian',
                'question' => 'Từ "学习" trong câu "我每天学习中文。" có nghĩa là gì?',
                'pinyin' => 'xué xí',
                'options' => ['Học tập', 'Nói chuyện', 'Đọc sách', 'Viết chữ'],
                'correct_answer' => 'Học tập',
                'explanation' => '学习 (xuéxí) nghĩa là học tập. Câu trên có nghĩa: "Tôi học tiếng Trung mỗi ngày."',
                'difficulty' => 'intermediate',
                'sort_order' => 3,
            ],
        ];

        foreach ($questionRows as $q) {
            $lessonId = isset($q['lesson']) ? $lessons->firstWhere('slug', $q['lesson'])?->id : null;

            \App\Models\Question::query()->updateOrCreate(
                [
                    'question' => $q['question'],
                ],
                [
                    'lesson_id'      => $lessonId,
                    'pinyin'         => $q['pinyin'],
                    'options'        => $q['options'],
                    'correct_answer' => $q['correct_answer'],
                    'explanation'    => $q['explanation'],
                    'difficulty'     => $q['difficulty'],
                    'sort_order'     => $q['sort_order'],
                    'is_active'      => true,
                ]
            );
        }

        // ─── Flashcard seed data ──────────────────────────────────────────────
        $flashcardRows = [
            // HSK 1 – Pinyin cơ bản
            ['lesson' => 'pinyin-co-ban', 'hsk' => 1, 'hanzi' => 'ā', 'pinyin' => 'ā', 'meaning' => 'Thanh 1 – Cao bằng', 'example' => '妈妈 māmā', 'example_pinyin' => 'māmā', 'example_meaning' => 'Mẹ (ví dụ thanh 1)', 'sort_order' => 1],
            ['lesson' => 'pinyin-co-ban', 'hsk' => 1, 'hanzi' => 'á', 'pinyin' => 'á', 'meaning' => 'Thanh 2 – Lên cao', 'example' => '麻 má', 'example_pinyin' => 'má', 'example_meaning' => 'Tê/cây gai (ví dụ thanh 2)', 'sort_order' => 2],
            ['lesson' => 'pinyin-co-ban', 'hsk' => 1, 'hanzi' => 'ǎ', 'pinyin' => 'ǎ', 'meaning' => 'Thanh 3 – Xuống rồi lên', 'example' => '马 mǎ', 'example_pinyin' => 'mǎ', 'example_meaning' => 'Con ngựa (ví dụ thanh 3)', 'sort_order' => 3],
            ['lesson' => 'pinyin-co-ban', 'hsk' => 1, 'hanzi' => 'à', 'pinyin' => 'à', 'meaning' => 'Thanh 4 – Xuống nhanh', 'example' => '骂 mà', 'example_pinyin' => 'mà', 'example_meaning' => 'Mắng chửi (ví dụ thanh 4)', 'sort_order' => 4],

            // HSK 1 – Chào hỏi & giới thiệu
            ['lesson' => 'chao-hoi-gioi-thieu', 'hsk' => 1, 'hanzi' => '你好', 'pinyin' => 'nǐ hǎo', 'meaning' => 'Xin chào', 'example' => '你好，我叫 Linh。', 'example_pinyin' => 'Nǐ hǎo, wǒ jiào Linh.', 'example_meaning' => 'Xin chào, tôi tên là Linh.', 'sort_order' => 1],
            ['lesson' => 'chao-hoi-gioi-thieu', 'hsk' => 1, 'hanzi' => '谢谢', 'pinyin' => 'xiè xie', 'meaning' => 'Cảm ơn', 'example' => '谢谢你的帮助。', 'example_pinyin' => 'Xièxie nǐ de bāngzhù.', 'example_meaning' => 'Cảm ơn sự giúp đỡ của bạn.', 'sort_order' => 2],
            ['lesson' => 'chao-hoi-gioi-thieu', 'hsk' => 1, 'hanzi' => '再见', 'pinyin' => 'zài jiàn', 'meaning' => 'Tạm biệt', 'example' => '我们明天再见。', 'example_pinyin' => 'Wǒmen míngtiān zàijiàn.', 'example_meaning' => 'Hẹn gặp lại ngày mai.', 'sort_order' => 3],
            ['lesson' => 'chao-hoi-gioi-thieu', 'hsk' => 1, 'hanzi' => '对不起', 'pinyin' => 'duì bu qǐ', 'meaning' => 'Xin lỗi', 'example' => '对不起，我来晚了。', 'example_pinyin' => 'Duìbuqǐ, wǒ lái wǎn le.', 'example_meaning' => 'Xin lỗi, tôi đến muộn.', 'sort_order' => 4],

            // HSK 2 – Từ vựng gia đình
            ['lesson' => 'tu-vung-gia-dinh', 'hsk' => 2, 'hanzi' => '学习', 'pinyin' => 'xué xí', 'meaning' => 'Học tập', 'example' => '我每天学习中文。', 'example_pinyin' => 'Wǒ měitiān xuéxí Zhōngwén.', 'example_meaning' => 'Tôi học tiếng Trung mỗi ngày.', 'sort_order' => 1],
            ['lesson' => 'tu-vung-gia-dinh', 'hsk' => 2, 'hanzi' => '朋友', 'pinyin' => 'péng you', 'meaning' => 'Bạn bè', 'example' => '他是我的朋友。', 'example_pinyin' => 'Tā shì wǒ de péngyou.', 'example_meaning' => 'Anh ấy là bạn của tôi.', 'sort_order' => 2],
            ['lesson' => 'tu-vung-gia-dinh', 'hsk' => 2, 'hanzi' => '老师', 'pinyin' => 'lǎo shī', 'meaning' => 'Giáo viên', 'example' => '老师今天很忙。', 'example_pinyin' => 'Lǎoshī jīntiān hěn máng.', 'example_meaning' => 'Hôm nay thầy giáo rất bận.', 'sort_order' => 3],
            ['lesson' => 'tu-vung-gia-dinh', 'hsk' => 2, 'hanzi' => '妈妈', 'pinyin' => 'māma', 'meaning' => 'Mẹ', 'example' => '我妈妈做饭很好吃。', 'example_pinyin' => 'Wǒ māma zuòfàn hěn hǎochī.', 'example_meaning' => 'Mẹ tôi nấu ăn rất ngon.', 'sort_order' => 4],

            // HSK 2 – Số đếm và thời gian
            ['lesson' => 'so-dem-va-thoi-gian', 'hsk' => 2, 'hanzi' => '一二三', 'pinyin' => 'yī èr sān', 'meaning' => 'Một, Hai, Ba', 'example' => '一二三四五。', 'example_pinyin' => 'Yī èr sān sì wǔ.', 'example_meaning' => 'Một hai ba bốn năm.', 'sort_order' => 1],
            ['lesson' => 'so-dem-va-thoi-gian', 'hsk' => 2, 'hanzi' => '现在', 'pinyin' => 'xiàn zài', 'meaning' => 'Bây giờ', 'example' => '现在几点？', 'example_pinyin' => 'Xiànzài jǐ diǎn?', 'example_meaning' => 'Bây giờ là mấy giờ?', 'sort_order' => 2],
            ['lesson' => 'so-dem-va-thoi-gian', 'hsk' => 2, 'hanzi' => '今天', 'pinyin' => 'jīn tiān', 'meaning' => 'Hôm nay', 'example' => '今天天气很好。', 'example_pinyin' => 'Jīntiān tiānqì hěn hǎo.', 'example_meaning' => 'Hôm nay thời tiết rất đẹp.', 'sort_order' => 3],
            ['lesson' => 'so-dem-va-thoi-gian', 'hsk' => 2, 'hanzi' => '明天', 'pinyin' => 'míng tiān', 'meaning' => 'Ngày mai', 'example' => '明天我们去学校。', 'example_pinyin' => 'Míngtiān wǒmen qù xuéxiào.', 'example_meaning' => 'Ngày mai chúng ta đến trường.', 'sort_order' => 4],
        ];

        foreach ($flashcardRows as $row) {
            $lessonId = $lessons->firstWhere('slug', $row['lesson'])?->id;
            \App\Models\Flashcard::query()->updateOrCreate(
                ['hanzi' => $row['hanzi'], 'lesson_id' => $lessonId],
                [
                    'lesson_id'       => $lessonId,
                    'pinyin'          => $row['pinyin'],
                    'meaning'         => $row['meaning'],
                    'example'         => $row['example'] ?? null,
                    'example_pinyin'  => $row['example_pinyin'] ?? null,
                    'example_meaning' => $row['example_meaning'] ?? null,
                    'sort_order'      => $row['sort_order'],
                    'hsk_level'       => $row['hsk'] ?? null,
                    'is_active'       => true,
                ]
            );
        }
    }
}
