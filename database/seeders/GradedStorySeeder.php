<?php

namespace Database\Seeders;

use App\Models\Story;
use Illuminate\Database\Seeder;

class GradedStorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stories = [
            // ══════════════════════════════════════════════
            // HSK 1 STORIES
            // ══════════════════════════════════════════════
            [
                'title'         => '去咖啡馆喝咖啡',
                'title_pinyin'  => 'Qù kāfēiguǎn hē kāfēi',
                'title_vi'      => 'Đi quán cà phê uống cà phê',
                'slug'          => 'di-quan-ca-phe-uong-ca-phe-hsk-1',
                'hsk_level'     => 1,
                'category'      => 'Đời sống',
                'cover_color'   => '#78350f',
                'summary'       => 'Một buổi chiều thư thả tại quán cà phê gần nhà, gặp gỡ bạn bè và gọi đồ uống yêu thích.',
                'word_count'    => 65,
                'estimated_reading_minutes' => 2,
                'is_published'  => true,
                'content_json'  => [
                    [
                        'chinese'    => '今天 是 星期六 ， 天气 很 好 。',
                        'pinyin'     => 'Jīntiān shì xīngqīliù, tiānqì hěn hǎo.',
                        'vietnamese' => 'Hôm nay là thứ Bảy, thời tiết rất đẹp.',
                        'words'      => [
                            ['hanzi' => '今天', 'pinyin' => 'jīntiān', 'meaning' => 'hôm nay'],
                            ['hanzi' => '是', 'pinyin' => 'shì', 'meaning' => 'là'],
                            ['hanzi' => '星期六', 'pinyin' => 'xīngqīliù', 'meaning' => 'thứ Bảy'],
                            ['hanzi' => '天气', 'pinyin' => 'tiānqì', 'meaning' => 'thời tiết'],
                            ['hanzi' => '很', 'pinyin' => 'hěn', 'meaning' => 'rất'],
                            ['hanzi' => '好', 'pinyin' => 'hǎo', 'meaning' => 'tốt, đẹp'],
                        ],
                    ],
                    [
                        'chinese'    => '我 想 和 朋友 去 咖啡馆 。',
                        'pinyin'     => 'Wǒ xiǎng hé péngyou qù kāfēiguǎn.',
                        'vietnamese' => 'Tôi muốn cùng bạn đi quán cà phê.',
                        'words'      => [
                            ['hanzi' => '我', 'pinyin' => 'wǒ', 'meaning' => 'tôi'],
                            ['hanzi' => '想', 'pinyin' => 'xiǎng', 'meaning' => 'muốn, nghĩ'],
                            ['hanzi' => '和', 'pinyin' => 'hé', 'meaning' => 'và, cùng với'],
                            ['hanzi' => '朋友', 'pinyin' => 'péngyou', 'meaning' => 'bạn bè'],
                            ['hanzi' => '去', 'pinyin' => 'qù', 'meaning' => 'đi'],
                            ['hanzi' => '咖啡馆', 'pinyin' => 'kāfēiguǎn', 'meaning' => 'quán cà phê'],
                        ],
                    ],
                    [
                        'chinese'    => '我们 坐 在 窗户 旁边 。',
                        'pinyin'     => 'Wǒmen zuò zài chuānghu pángbiān.',
                        'vietnamese' => 'Chúng tôi ngồi ở bên cạnh cửa sổ.',
                        'words'      => [
                            ['hanzi' => '我们', 'pinyin' => 'wǒmen', 'meaning' => 'chúng tôi'],
                            ['hanzi' => '坐', 'pinyin' => 'zuò', 'meaning' => 'ngồi'],
                            ['hanzi' => '在', 'pinyin' => 'zài', 'meaning' => 'ở, tại'],
                            ['hanzi' => '窗户', 'pinyin' => 'chuānghu', 'meaning' => 'cửa sổ'],
                            ['hanzi' => '旁边', 'pinyin' => 'pángbiān', 'meaning' => 'bên cạnh'],
                        ],
                    ],
                    [
                        'chinese'    => '服务员 问 ：“ 你们 喝 什么 ？”',
                        'pinyin'     => 'Fúwùyuán wèn: "Nǐmen hē shénme?"',
                        'vietnamese' => 'Nhân viên phục vụ hỏi: "Các bạn uống gì ạ?"',
                        'words'      => [
                            ['hanzi' => '服务员', 'pinyin' => 'fúwùyuán', 'meaning' => 'nhân viên phục vụ'],
                            ['hanzi' => '问', 'pinyin' => 'wèn', 'meaning' => 'hỏi'],
                            ['hanzi' => '你们', 'pinyin' => 'nǐmen', 'meaning' => 'các bạn'],
                            ['hanzi' => '喝', 'pinyin' => 'hē', 'meaning' => 'uống'],
                            ['hanzi' => '什么', 'pinyin' => 'shénme', 'meaning' => 'cái gì'],
                        ],
                    ],
                    [
                        'chinese'    => '我 说 ：“ 我 要 一杯 热 咖啡 ， 谢谢 。”',
                        'pinyin'     => 'Wǒ shuō: "Wǒ yào yì bēi rè kāfēi, xièxie."',
                        'vietnamese' => 'Tôi nói: "Cho tôi một ly cà phê nóng, cảm ơn."',
                        'words'      => [
                            ['hanzi' => '说', 'pinyin' => 'shuō', 'meaning' => 'nói'],
                            ['hanzi' => '要', 'pinyin' => 'yào', 'meaning' => 'muốn, cần'],
                            ['hanzi' => '一杯', 'pinyin' => 'yì bēi', 'meaning' => 'một ly / cốc'],
                            ['hanzi' => '热', 'pinyin' => 'rè', 'meaning' => 'nóng'],
                            ['hanzi' => '咖啡', 'pinyin' => 'kāfēi', 'meaning' => 'cà phê'],
                            ['hanzi' => '谢谢', 'pinyin' => 'xièxie', 'meaning' => 'cảm ơn'],
                        ],
                    ],
                    [
                        'chinese'    => '我的 朋友 喜欢 喝 中国 茶 。',
                        'pinyin'     => 'Wǒ de péngyou xǐhuan hē Zhōngguó chá.',
                        'vietnamese' => 'Bạn của tôi thích uống trà Trung Quốc.',
                        'words'      => [
                            ['hanzi' => '喜欢', 'pinyin' => 'xǐhuan', 'meaning' => 'thích'],
                            ['hanzi' => '中国', 'pinyin' => 'Zhōngguó', 'meaning' => 'Trung Quốc'],
                            ['hanzi' => '茶', 'pinyin' => 'chá', 'meaning' => 'trà'],
                        ],
                    ],
                    [
                        'chinese'    => '我们 在 咖啡馆 聊 了 两个 小时 ， 非常 高兴 。',
                        'pinyin'     => 'Wǒmen zài kāfēiguǎn liáo le liǎng gè xiǎoshí, fēicháng gāoxìng.',
                        'vietnamese' => 'Chúng tôi nói chuyện ở quán cà phê suốt hai tiếng đồng hồ, rất vui vẻ.',
                        'words'      => [
                            ['hanzi' => '聊', 'pinyin' => 'liáo', 'meaning' => 'trò chuyện'],
                            ['hanzi' => '两个', 'pinyin' => 'liǎng gè', 'meaning' => 'hai cái'],
                            ['hanzi' => '小时', 'pinyin' => 'xiǎoshí', 'meaning' => 'tiếng đồng hồ'],
                            ['hanzi' => '非常', 'pinyin' => 'fēicháng', 'meaning' => 'vô cùng, rất'],
                            ['hanzi' => '高兴', 'pinyin' => 'gāoxìng', 'meaning' => 'vui vẻ'],
                        ],
                    ],
                ],
                'quiz_json'     => [
                    [
                        'question'       => '今天 是 星期 几 ？',
                        'pinyin'         => 'Jīntiān shì xīngqī jǐ?',
                        'options'        => ['星期五', '星期六', '星期日', '星期一'],
                        'correct_answer' => '星期六',
                        'explanation'    => 'Trong bài viết: "今天 是 星期六 ， 天气 很 好 。"',
                    ],
                    [
                        'question'       => '主角 想 喝 什么 ？',
                        'pinyin'         => 'Zhǔjué xiǎng hē shénme?',
                        'options'        => ['冷 水', '中国 茶', '热 咖啡', '牛奶'],
                        'correct_answer' => '热 咖啡',
                        'explanation'    => 'Trong bài: "我 说 ：“ 我 要 一杯 热 咖啡 ， 谢谢 。”"',
                    ],
                    [
                        'question'       => '朋友 喜欢 喝 什么 ？',
                        'pinyin'         => 'Péngyou xǐhuan hē shénme?',
                        'options'        => ['中国 茶', '果汁', '啤酒', '可乐'],
                        'correct_answer' => '中国 茶',
                        'explanation'    => 'Trong bài: "我的 朋友 喜欢 喝 中国 茶 。"',
                    ],
                ],
            ],

            [
                'title'         => '在水果店买苹果',
                'title_pinyin'  => 'Zài shuǐguǒdiàn mǎi píngguǒ',
                'title_vi'      => 'Mua táo ở tiệm hoa quả',
                'slug'          => 'mua-tao-o-tiem-hoa-qua-hsk-1',
                'hsk_level'     => 1,
                'category'      => 'Mua sắm',
                'cover_color'   => '#dc2626',
                'summary'       => 'Cách hỏi giá, mặc cả và mua hoa quả tươi ngon bằng tiếng Trung đơn giản.',
                'word_count'    => 70,
                'estimated_reading_minutes' => 2,
                'is_published'  => true,
                'content_json'  => [
                    [
                        'chinese'    => '学校 旁边 有 一 个 水果店 。',
                        'pinyin'     => 'Xuéxiào pángbiān yǒu yí gè shuǐguǒdiàn.',
                        'vietnamese' => 'Bên cạnh trường học có một tiệm bán hoa quả.',
                        'words'      => [
                            ['hanzi' => '学校', 'pinyin' => 'xuéxiào', 'meaning' => 'trường học'],
                            ['hanzi' => '有', 'pinyin' => 'yǒu', 'meaning' => 'có'],
                            ['hanzi' => '水果店', 'pinyin' => 'shuǐguǒdiàn', 'meaning' => 'tiệm hoa quả'],
                        ],
                    ],
                    [
                        'chinese'    => '店 里的 水果 很 新鲜 ， 也 很 便宜 。',
                        'pinyin'     => 'Diàn lǐ de shuǐguǒ hěn xīnxiān, yě hěn piányi.',
                        'vietnamese' => 'Hoa quả trong tiệm rất tươi mới, và cũng rất rẻ.',
                        'words'      => [
                            ['hanzi' => '店', 'pinyin' => 'diàn', 'meaning' => 'cửa tiệm'],
                            ['hanzi' => '新鲜', 'pinyin' => 'xīnxiān', 'meaning' => 'tươi mới'],
                            ['hanzi' => '也', 'pinyin' => 'yě', 'meaning' => 'cũng'],
                            ['hanzi' => '便宜', 'pinyin' => 'piányi', 'meaning' => 'rẻ'],
                        ],
                    ],
                    [
                        'chinese'    => '我 问 老板 ：“ 苹果 多少 钱 一斤 ？”',
                        'pinyin'     => 'Wǒ wèn lǎobǎn: "Píngguǒ duōshao qián yì jīn?"',
                        'vietnamese' => 'Tôi hỏi ông chủ: "Táo bao nhiêu tiền một cân (500g) ạ?"',
                        'words'      => [
                            ['hanzi' => '老板', 'pinyin' => 'lǎobǎn', 'meaning' => 'ông chủ'],
                            ['hanzi' => '苹果', 'pinyin' => 'píngguǒ', 'meaning' => 'quả táo'],
                            ['hanzi' => '多少', 'pinyin' => 'duōshao', 'meaning' => 'bao nhiêu'],
                            ['hanzi' => '钱', 'pinyin' => 'qián', 'meaning' => 'tiền'],
                            ['hanzi' => '一斤', 'pinyin' => 'yì jīn', 'meaning' => '1 cân Trung Quốc (500g)'],
                        ],
                    ],
                    [
                        'chinese'    => '老板 笑着 说 ：“ 五 块 钱 一斤 ， 很 甜 的 ！”',
                        'pinyin'     => 'Lǎobǎn xiàozhe shuō: "Wǔ kuài qián yì jīn, hěn tián de!"',
                        'vietnamese' => 'Ông chủ cười nói: "Năm tệ một cân, ngọt lắm đấy!"',
                        'words'      => [
                            ['hanzi' => '笑着', 'pinyin' => 'xiàozhe', 'meaning' => 'cười nói'],
                            ['hanzi' => '五', 'pinyin' => 'wǔ', 'meaning' => 'số 5'],
                            ['hanzi' => '块', 'pinyin' => 'kuài', 'meaning' => 'đồng/tệ'],
                            ['hanzi' => '甜', 'pinyin' => 'tián', 'meaning' => 'ngọt'],
                        ],
                    ],
                    [
                        'chinese'    => '我 买了 三斤 苹果 和 两 个 西瓜 。',
                        'pinyin'     => 'Wǒ mǎile sān jīn píngguǒ hé liǎng gè xīguā.',
                        'vietnamese' => 'Tôi đã mua 3 cân táo và 2 quả dưa hấu.',
                        'words'      => [
                            ['hanzi' => '买', 'pinyin' => 'mǎi', 'meaning' => 'mua'],
                            ['hanzi' => '三斤', 'pinyin' => 'sān jīn', 'meaning' => '3 cân (1.5kg)'],
                            ['hanzi' => '西瓜', 'pinyin' => 'xīguā', 'meaning' => 'dưa hấu'],
                        ],
                    ],
                ],
                'quiz_json'     => [
                    [
                        'question'       => '水果店 在 哪里 ？',
                        'pinyin'         => 'Shuǐguǒdiàn zài nǎlǐ?',
                        'options'        => ['学校 旁边', '医院 后面', '公园 里', '家 里面'],
                        'correct_answer' => '学校 旁边',
                        'explanation'    => 'Trong bài: "学校 旁边 有 一 个 水果店 。"',
                    ],
                    [
                        'question'       => '苹果 多少 钱 一斤 ？',
                        'pinyin'         => 'Píngguǒ duōshao qián yì jīn?',
                        'options'        => ['三 块 钱', '五 块 钱', '十 块 钱', '八 块 钱'],
                        'correct_answer' => '五 块 钱',
                        'explanation'    => 'Trong bài: "五 块 钱 一斤 ， 很 甜 的 ！"',
                    ],
                ],
            ],

            [
                'title'         => '介绍我的中国朋友',
                'title_pinyin'  => 'Jièshào wǒ de Zhōngguó péngyou',
                'title_vi'      => 'Giới thiệu người bạn Trung Quốc của tôi',
                'slug'          => 'gioi-thieu-nguoi-ban-trung-quoc-hsk-1',
                'hsk_level'     => 1,
                'category'      => 'Giao tiếp',
                'cover_color'   => '#2563eb',
                'summary'       => 'Làm quen với bạn học mới người Bắc Kinh, sở thích học tiếng Việt và ăn phở.',
                'word_count'    => 80,
                'estimated_reading_minutes' => 2,
                'is_published'  => true,
                'content_json'  => [
                    [
                        'chinese'    => '我 有 一 个 中国 朋友 ， 他 叫 王明 。',
                        'pinyin'     => 'Wǒ yǒu yí gè Zhōngguó péngyou, tā jiào Wáng Míng.',
                        'vietnamese' => 'Tôi có một người bạn Trung Quốc, anh ấy tên là Vương Minh.',
                        'words'      => [
                            ['hanzi' => '他', 'pinyin' => 'tā', 'meaning' => 'anh ấy'],
                            ['hanzi' => '叫', 'pinyin' => 'jiào', 'meaning' => 'tên là, gọi là'],
                            ['hanzi' => '王明', 'pinyin' => 'Wáng Míng', 'meaning' => 'Vương Minh (tên riêng)'],
                        ],
                    ],
                    [
                        'chinese'    => '王明 今年 二十 岁 ， 是 北京 人 。',
                        'pinyin'     => 'Wáng Míng jīnnián èrshí suì, shì Běijīng rén.',
                        'vietnamese' => 'Vương Minh năm nay hai mươi tuổi, là người Bắc Kinh.',
                        'words'      => [
                            ['hanzi' => '今年', 'pinyin' => 'jīnnián', 'meaning' => 'năm nay'],
                            ['hanzi' => '二十', 'pinyin' => 'èrshí', 'meaning' => 'hai mươi (20)'],
                            ['hanzi' => '岁', 'pinyin' => 'suì', 'meaning' => 'tuổi'],
                            ['hanzi' => '北京', 'pinyin' => 'Běijīng', 'meaning' => 'Bắc Kinh'],
                            ['hanzi' => '人', 'pinyin' => 'rén', 'meaning' => 'người'],
                        ],
                    ],
                    [
                        'chinese'    => '他 会 说 汉语 和 英语 ， 现在 在 学习 越南语 。',
                        'pinyin'     => 'Tā huì shuō Hànyǔ hé Yīngyǔ, xiànzài zài xuéxí Yuènányǔ.',
                        'vietnamese' => 'Anh ấy biết nói tiếng Hán và tiếng Anh, bây giờ đang học tiếng Việt.',
                        'words'      => [
                            ['hanzi' => '会', 'pinyin' => 'huì', 'meaning' => 'biết (kỹ năng)'],
                            ['hanzi' => '汉语', 'pinyin' => 'Hànyǔ', 'meaning' => 'tiếng Hán / Trung'],
                            ['hanzi' => '英语', 'pinyin' => 'Yīngyǔ', 'meaning' => 'tiếng Anh'],
                            ['hanzi' => '现在', 'pinyin' => 'xiànzài', 'meaning' => 'bây giờ, hiện tại'],
                            ['hanzi' => '学习', 'pinyin' => 'xuéxí', 'meaning' => 'học tập'],
                            ['hanzi' => '越南语', 'pinyin' => 'Yuènányǔ', 'meaning' => 'tiếng Việt'],
                        ],
                    ],
                    [
                        'chinese'    => '他 很 喜欢 越南 的 咖啡 和 米粉 。',
                        'pinyin'     => 'Tā hěn xǐhuan Yuènán de kāfēi hé mǐfěn.',
                        'vietnamese' => 'Anh ấy rất thích cà phê và phở / bún của Việt Nam.',
                        'words'      => [
                            ['hanzi' => '越南', 'pinyin' => 'Yuènán', 'meaning' => 'Việt Nam'],
                            ['hanzi' => '米粉', 'pinyin' => 'mǐfěn', 'meaning' => 'phở / bún gạo'],
                        ],
                    ],
                ],
                'quiz_json'     => [
                    [
                        'question'       => '王明 今年 多大 了 ？',
                        'pinyin'         => 'Wáng Míng jīnnián duō dà le?',
                        'options'        => ['十八 岁', '二十 岁', '二十五 岁', '三十 岁'],
                        'correct_answer' => '二十 岁',
                        'explanation'    => 'Trong bài: "王明 今年 二十 岁 ， 是 北京 人 。"',
                    ],
                    [
                        'question'       => '王明 现在 在 学习 什么 语言 ？',
                        'pinyin'         => 'Wáng Míng xiànzài zài xuéxí shénme yǔyán?',
                        'options'        => ['法语', '日语', '越南语', '韩语'],
                        'correct_answer' => '越南语',
                        'explanation'    => 'Trong bài: "现在 在 学习 越南语 。"',
                    ],
                ],
            ],

            [
                'title'         => '今天天气真好',
                'title_pinyin'  => 'Jīntiān tiānqì zhēn hǎo',
                'title_vi'      => 'Hôm nay thời tiết thật đẹp',
                'slug'          => 'hom-nay-thoi-tiet-that-dep-hsk-1',
                'hsk_level'     => 1,
                'category'      => 'Đời sống',
                'cover_color'   => '#059669',
                'summary'       => 'Một ngày đẹp trời đi dạo công viên, ngắm hoa và tận hưởng không khí trong lành.',
                'word_count'    => 60,
                'estimated_reading_minutes' => 2,
                'is_published'  => true,
                'content_json'  => [
                    [
                        'chinese'    => '今天 天气 真 好 ， 不 冷 也 不 热 。',
                        'pinyin'     => 'Jīntiān tiānqì zhēn hǎo, bù lěng yě bù rè.',
                        'vietnamese' => 'Hôm nay thời tiết thật đẹp, không lạnh cũng không nóng.',
                        'words'      => [
                            ['hanzi' => '真', 'pinyin' => 'zhēn', 'meaning' => 'thật là, quả thực'],
                            ['hanzi' => '不', 'pinyin' => 'bù', 'meaning' => 'không'],
                            ['hanzi' => '冷', 'pinyin' => 'lěng', 'meaning' => 'lạnh'],
                        ],
                    ],
                    [
                        'chinese'    => '太阳 出来了 ， 天空 很 蓝 。',
                        'pinyin'     => 'Tàiyáng chūlái le, tiānkōng hěn lán.',
                        'vietnamese' => 'Mặt trời đã lên rồi, bầu trời rất xanh.',
                        'words'      => [
                            ['hanzi' => '太阳', 'pinyin' => 'tàiyáng', 'meaning' => 'mặt trời'],
                            ['hanzi' => '天空', 'pinyin' => 'tiānkōng', 'meaning' => 'bầu trời'],
                            ['hanzi' => '蓝', 'pinyin' => 'lán', 'meaning' => 'màu xanh lam'],
                        ],
                    ],
                    [
                        'chinese'    => '我和 爸爸 妈妈 一起 去 公园 散步 。',
                        'pinyin'     => 'Wǒ hé bàba māma yìqǐ qù gōngyuán sànbù.',
                        'vietnamese' => 'Tôi cùng bố mẹ đi bộ dạo ở công viên.',
                        'words'      => [
                            ['hanzi' => '爸爸', 'pinyin' => 'bàba', 'meaning' => 'bố, cha'],
                            ['hanzi' => '妈妈', 'pinyin' => 'māma', 'meaning' => 'mẹ'],
                            ['hanzi' => '一起', 'pinyin' => 'yìqǐ', 'meaning' => 'cùng nhau'],
                            ['hanzi' => '公园', 'pinyin' => 'gōngyuán', 'meaning' => 'công viên'],
                            ['hanzi' => '散步', 'pinyin' => 'sànbù', 'meaning' => 'đi dạo'],
                        ],
                    ],
                ],
                'quiz_json'     => [
                    [
                        'question'       => '今天 的 天气 怎么样 ？',
                        'pinyin'         => 'Jīntiān de tiānqì zěnmeyàng?',
                        'options'        => ['很 冷', '很 热', '真 好', '下 雨'],
                        'correct_answer' => '真 好',
                        'explanation'    => 'Trong bài: "今天 天气 真 好 ， 不 冷 也 不 热 。"',
                    ],
                ],
            ],

            // ══════════════════════════════════════════════
            // HSK 2 STORIES
            // ══════════════════════════════════════════════
            [
                'title'         => '在饭馆点中国菜',
                'title_pinyin'  => 'Zài fànguǎn diǎn Zhōngguó cài',
                'title_vi'      => 'Gọi món ở nhà hàng Trung Quốc',
                'slug'          => 'goi-mon-o-nha-hang-trung-quoc-hsk-2',
                'hsk_level'     => 2,
                'category'      => 'Ẩm thực',
                'cover_color'   => '#b91c1c',
                'summary'       => 'Trải nghiệm thưởng thức các món ăn nổi tiếng như Thịt kho Đông Pha và Đậu phụ Ma Bà.',
                'word_count'    => 105,
                'estimated_reading_minutes' => 3,
                'is_published'  => true,
                'content_json'  => [
                    [
                        'chinese'    => '今天 晚上 ， 我 和 两个 同事 去 中国 饭馆 吃饭 。',
                        'pinyin'     => 'Jīntiān wǎnshang, wǒ hé liǎng gè tóngshì qù Zhōngguó fànguǎn chīfàn.',
                        'vietnamese' => 'Tối hôm nay, tôi cùng hai đồng nghiệp đi ăn cơm ở nhà hàng Trung Quốc.',
                        'words'      => [
                            ['hanzi' => '晚上', 'pinyin' => 'wǎnshang', 'meaning' => 'buổi tối'],
                            ['hanzi' => '同事', 'pinyin' => 'tóngshì', 'meaning' => 'đồng nghiệp'],
                            ['hanzi' => '饭馆', 'pinyin' => 'fànguǎn', 'meaning' => 'nhà hàng, quán ăn'],
                            ['hanzi' => '吃饭', 'pinyin' => 'chīfàn', 'meaning' => 'ăn cơm'],
                        ],
                    ],
                    [
                        'chinese'    => '这家 饭馆 的 菜 非常 有名 ， 人 很多 。',
                        'pinyin'     => 'Zhè jiā fànguǎn de cài fēicháng yǒumíng, rén hěn duō.',
                        'vietnamese' => 'Món ăn của nhà hàng này vô cùng nổi tiếng, khách rất đông.',
                        'words'      => [
                            ['hanzi' => '这家', 'pinyin' => 'zhè jiā', 'meaning' => 'nhà hàng này (lượng từ gia đình/quán)'],
                            ['hanzi' => '菜', 'pinyin' => 'cài', 'meaning' => 'món ăn, thức ăn'],
                            ['hanzi' => '有名', 'pinyin' => 'yǒumíng', 'meaning' => 'nổi tiếng'],
                        ],
                    ],
                    [
                        'chinese'    => '我们 点了 麻婆豆腐 、 宫保鸡丁 和 一 碗 鸡蛋汤 。',
                        'pinyin'     => 'Wǒmen diǎnle mápó dòufu, gōngbǎo jīdīng hé yì wǎn jīdàntāng.',
                        'vietnamese' => 'Chúng tôi đã gọi món Đậu phụ Ma Bà, Gà Kung Pao và một tô canh trứng gà.',
                        'words'      => [
                            ['hanzi' => '点了', 'pinyin' => 'diǎnle', 'meaning' => 'đã gọi món'],
                            ['hanzi' => '麻婆豆腐', 'pinyin' => 'mápó dòufu', 'meaning' => 'Đậu phụ Ma Bà'],
                            ['hanzi' => '宫保鸡丁', 'pinyin' => 'gōngbǎo jīdīng', 'meaning' => 'Gà xào Cung Bảo'],
                            ['hanzi' => '一碗', 'pinyin' => 'yì wǎn', 'meaning' => 'một bát / tô'],
                            ['hanzi' => '鸡蛋汤', 'pinyin' => 'jīdàntāng', 'meaning' => 'canh trứng gà'],
                        ],
                    ],
                    [
                        'chinese'    => '服务员 问 ：“ 你们 能 吃 辣 吗 ？”',
                        'pinyin'     => 'Fúwùyuán wèn: "Nǐmen néng chī là ma?"',
                        'vietnamese' => 'Nhân viên hỏi: "Các bạn có ăn được cay không?"',
                        'words'      => [
                            ['hanzi' => '能', 'pinyin' => 'néng', 'meaning' => 'có thể, được'],
                            ['hanzi' => '吃', 'pinyin' => 'chī', 'meaning' => 'ăn'],
                            ['hanzi' => '辣', 'pinyin' => 'là', 'meaning' => 'cay'],
                        ],
                    ],
                    [
                        'chinese'    => '我 回答 ：“ 请 少 放 一点 辣椒 ， 谢谢 ！”',
                        'pinyin'     => 'Wǒ huídá: "Qǐng shǎo fàng yìdiǎn làjiāo, xièxie!"',
                        'vietnamese' => 'Tôi trả lời: "Xin vui lòng cho ít ớt một chút, cảm ơn ạ!"',
                        'words'      => [
                            ['hanzi' => '回答', 'pinyin' => 'huídá', 'meaning' => 'trả lời'],
                            ['hanzi' => '少', 'pinyin' => 'shǎo', 'meaning' => 'ít'],
                            ['hanzi' => '放', 'pinyin' => 'fàng', 'meaning' => 'bỏ vào, để'],
                            ['hanzi' => '一点', 'pinyin' => 'yìdiǎn', 'meaning' => 'một chút'],
                            ['hanzi' => '辣椒', 'pinyin' => 'làjiāo', 'meaning' => 'ớt'],
                        ],
                    ],
                ],
                'quiz_json'     => [
                    [
                        'question'       => '他们 一共 点了 几 样 菜 和 汤 ？',
                        'pinyin'         => 'Tāmen yígòng diǎnle jǐ yàng cài hé tāng?',
                        'options'        => ['两 样', '三 样', '四 样', '五 样'],
                        'correct_answer' => '三 样',
                        'explanation'    => 'Họ đã gọi 3 món: 麻婆豆腐 (Đậu phụ Ma Bà), 宫保鸡丁 (Gà Kung Pao) và 鸡蛋汤 (Canh trứng).',
                    ],
                    [
                        'question'       => '主角 为什么 让 服务员 少 放 辣椒 ？',
                        'pinyin'         => 'Zhǔjué wèishénme ràng fúwùyuán shǎo fàng làjiāo?',
                        'options'        => ['因为 不 能 吃 太 辣', '因为 没有 钱', '因为 菜 太多', '因为 喜欢 甜'],
                        'correct_answer' => '因为 不 能 吃 太 辣',
                        'explanation'    => 'Học viên dặn: "请 少 放 一点 辣椒" nghĩa là xin cho ít ớt một chút.',
                    ],
                ],
            ],

            [
                'title'         => '坐出租车去机场',
                'title_pinyin'  => 'Zuò chūzūchē qù jīchǎng',
                'title_vi'      => 'Đi taxi ra sân bay',
                'slug'          => 'di-taxi-ra-san-bay-hsk-2',
                'hsk_level'     => 2,
                'category'      => 'Giao thông',
                'cover_color'   => '#d97706',
                'summary'       => 'Học các mẫu câu hội thoại thường dùng nhất khi đi taxi, chỉ đường và thanh toán.',
                'word_count'    => 95,
                'estimated_reading_minutes' => 3,
                'is_published'  => true,
                'content_json'  => [
                    [
                        'chinese'    => '早上 八点 ， 我 带着 行李 箱 走出 宾馆 。',
                        'pinyin'     => 'Zǎoshang bā diǎn, wǒ dàizhe xínglixiāng zǒuchū bīnguǎn.',
                        'vietnamese' => 'Tám giờ sáng, tôi mang theo va li hành lý bước ra khỏi khách sạn.',
                        'words'      => [
                            ['hanzi' => '早上', 'pinyin' => 'zǎoshang', 'meaning' => 'buổi sáng'],
                            ['hanzi' => '行李箱', 'pinyin' => 'xínglixiāng', 'meaning' => 'va li hành lý'],
                            ['hanzi' => '宾馆', 'pinyin' => 'bīnguǎn', 'meaning' => 'khách sạn'],
                        ],
                    ],
                    [
                        'chinese'    => '我 在 路 边 叫了 一 辆 出租车 。',
                        'pinyin'     => 'Wǒ zài lùbiān jiàole yí liàng chūzūchē.',
                        'vietnamese' => 'Tôi gọi một chiếc xe taxi ở ven đường.',
                        'words'      => [
                            ['hanzi' => '路边', 'pinyin' => 'lùbiān', 'meaning' => 'ven đường, lề đường'],
                            ['hanzi' => '辆', 'pinyin' => 'liàng', 'meaning' => 'chiếc (lượng từ xe cộ)'],
                            ['hanzi' => '出租车', 'pinyin' => 'chūzūchē', 'meaning' => 'xe taxi'],
                        ],
                    ],
                    [
                        'chinese'    => '司机 师傅 问 ：“ 您 去 哪儿 ？”',
                        'pinyin'     => 'Sījī shīfu wèn: "Nín qù nǎr?"',
                        'vietnamese' => 'Bác tài xế hỏi: "Bác / anh đi đâu thế ạ?"',
                        'words'      => [
                            ['hanzi' => '司机', 'pinyin' => 'sījī', 'meaning' => 'tài xế'],
                            ['hanzi' => '师傅', 'pinyin' => 'shīfu', 'meaning' => 'sư phụ / bác tài'],
                            ['hanzi' => '哪儿', 'pinyin' => 'nǎr', 'meaning' => 'ở đâu, đâu'],
                        ],
                    ],
                    [
                        'chinese'    => '我 说 ：“ 去 首都 机场 ， 我的 飞机 是 十点半 的 。”',
                        'pinyin'     => 'Wǒ shuō: "Qù Shǒudū Jīchǎng, wǒ de fēijī shì shí diǎn bàn de."',
                        'vietnamese' => 'Tôi nói: "Đi sân bay Thủ Đô, chuyến bay của tôi lúc mười rưỡi."',
                        'words'      => [
                            ['hanzi' => '首都', 'pinyin' => 'shǒudū', 'meaning' => 'thủ đô'],
                            ['hanzi' => '机场', 'pinyin' => 'jīchǎng', 'meaning' => 'sân bay'],
                            ['hanzi' => '飞机', 'pinyin' => 'fēijī', 'meaning' => 'máy bay'],
                            ['hanzi' => '十点半', 'pinyin' => 'shí diǎn bàn', 'meaning' => '10 giờ rưỡi'],
                        ],
                    ],
                ],
                'quiz_json'     => [
                    [
                        'question'       => '主角 要 去 哪里 ？',
                        'pinyin'         => 'Zhǔjué yào qù nǎlǐ?',
                        'options'        => ['火车站', '首都 机场', '学校', '医院'],
                        'correct_answer' => '首都 机场',
                        'explanation'    => 'Trong bài: "去 首都 机场 ， 我的 飞机 是 十点半 的 。"',
                    ],
                ],
            ],

            // ══════════════════════════════════════════════
            // HSK 3 STORIES
            // ══════════════════════════════════════════════
            [
                'title'         => '在上海租公寓',
                'title_pinyin'  => 'Zài Shànghǎi zū gōngyù',
                'title_vi'      => 'Thuê căn hộ ở Thượng Hải',
                'slug'          => 'thue-can-ho-o-thuong-hai-hsk-3',
                'hsk_level'     => 3,
                'category'      => 'Đời sống',
                'cover_color'   => '#4338ca',
                'summary'       => 'Hành trình tìm nhà trọ tại khu trung tâm, xem phòng và ký hợp đồng thuê nhà.',
                'word_count'    => 130,
                'estimated_reading_minutes' => 4,
                'is_published'  => true,
                'content_json'  => [
                    [
                        'chinese'    => '下 个 月 我 要 去 上海 工作 ， 所以 最近 在 找 房子 。',
                        'pinyin'     => 'Xià gè yuè wǒ yào qù Shànghǎi gōngzuò, suǒyǐ zuìjìn zài zhǎo fángzi.',
                        'vietnamese' => 'Tháng sau tôi phải đi Thượng Hải làm việc, nên dạo gần đây đang tìm nhà.',
                        'words'      => [
                            ['hanzi' => '下个月', 'pinyin' => 'xià gè yuè', 'meaning' => 'tháng sau'],
                            ['hanzi' => '上海', 'pinyin' => 'Shànghǎi', 'meaning' => 'Thượng Hải'],
                            ['hanzi' => '工作', 'pinyin' => 'gōngzuò', 'meaning' => 'làm việc'],
                            ['hanzi' => '所以', 'pinyin' => 'suǒyǐ', 'meaning' => 'cho nên, vì vậy'],
                            ['hanzi' => '最近', 'pinyin' => 'zuìjìn', 'meaning' => 'gần đây, dạo này'],
                            ['hanzi' => '找', 'pinyin' => 'zhǎo', 'meaning' => 'tìm kiếm'],
                            ['hanzi' => '房子', 'pinyin' => 'fángzi', 'meaning' => 'căn nhà / phòng'],
                        ],
                    ],
                    [
                        'chinese'    => '昨天 中介 经理 带 我 看 了 一 套 离 地铁站 很 近 的 公寓 。',
                        'pinyin'     => 'Zuótiān zhōngjiè jīnglǐ dài wǒ kàn le yí tào lí dìtiězhàn hěn jìn de gōngyù.',
                        'vietnamese' => 'Hôm qua người quản lý môi giới đã dẫn tôi đi xem một căn hộ cách ga tàu điện ngầm rất gần.',
                        'words'      => [
                            ['hanzi' => '中介', 'pinyin' => 'zhōngjiè', 'meaning' => 'môi giới trung gian'],
                            ['hanzi' => '经理', 'pinyin' => 'jīnglǐ', 'meaning' => 'giám đốc / quản lý'],
                            ['hanzi' => '带', 'pinyin' => 'dài', 'meaning' => 'dẫn dắt, mang theo'],
                            ['hanzi' => '一套', 'pinyin' => 'yí tào', 'meaning' => 'một căn / bộ'],
                            ['hanzi' => '离', 'pinyin' => 'lí', 'meaning' => 'cách (khoảng cách)'],
                            ['hanzi' => '地铁站', 'pinyin' => 'dìtiězhàn', 'meaning' => 'ga tàu điện ngầm'],
                            ['hanzi' => '近', 'pinyin' => 'jìn', 'meaning' => 'gần'],
                            ['hanzi' => '公寓', 'pinyin' => 'gōngyù', 'meaning' => 'căn hộ chung cư'],
                        ],
                    ],
                    [
                        'chinese'    => '房间 采光 很 好 ， 家具 和 家电 都 很 齐全 ， 还有 一 个 大 阳台 。',
                        'pinyin'     => 'Fángjiān cǎiguāng hěn hǎo, jiājù hé jiādiàn dōu hěn qíquán, hái yǒu yí gè dà yángtái.',
                        'vietnamese' => 'Ánh sáng trong phòng rất tốt, đồ nội thất và đồ điện gia dụng đều rất đầy đủ, còn có một ban công lớn.',
                        'words'      => [
                            ['hanzi' => '采光', 'pinyin' => 'cǎiguāng', 'meaning' => 'ánh sáng tự nhiên'],
                            ['hanzi' => '家具', 'pinyin' => 'jiājù', 'meaning' => 'đồ nội thất gia đình'],
                            ['hanzi' => '齐全', 'pinyin' => 'qíquán', 'meaning' => 'đầy đủ tiện nghi'],
                            ['hanzi' => '阳台', 'pinyin' => 'yángtái', 'meaning' => 'ban công'],
                        ],
                    ],
                ],
                'quiz_json'     => [
                    [
                        'question'       => '这 套 公寓 离 哪里 很 近 ？',
                        'pinyin'         => 'Zhè tào gōngyù lí nǎlǐ hěn jìn?',
                        'options'        => ['飞机场', '地铁站', '公园', '医院'],
                        'correct_answer' => '地铁站',
                        'explanation'    => 'Trong bài: "离 地铁站 很 近 的 公寓 。"',
                    ],
                ],
            ],

            [
                'title'         => '第一次参加工作面试',
                'title_pinyin'  => 'Dì yī cì cānjiā gōngzuò miànshì',
                'title_vi'      => 'Lần đầu tham gia phỏng vấn xin việc',
                'slug'          => 'lan-dau-tham-gia-phong-van-xin-viec-hsk-3',
                'hsk_level'     => 3,
                'category'      => 'Công sở',
                'cover_color'   => '#0f766e',
                'summary'       => 'Kinh nghiệm chuẩn bị hồ sơ, trang phục và trả lời câu hỏi tự tin trước hội đồng phỏng vấn.',
                'word_count'    => 140,
                'estimated_reading_minutes' => 4,
                'is_published'  => true,
                'content_json'  => [
                    [
                        'chinese'    => '今天 早上 ， 我 穿上 正式 的 西装 去 一家 跨国 公司 面试 。',
                        'pinyin'     => 'Jīntiān zǎoshang, wǒ chuānshang zhèngshì de xīzhuāng qù yì jiā kuàguó gōngsī miànshì.',
                        'vietnamese' => 'Sáng hôm nay, tôi mặc bộ vest trang trọng đến một công ty đa quốc gia để phỏng vấn.',
                        'words'      => [
                            ['hanzi' => '穿上', 'pinyin' => 'chuānshang', 'meaning' => 'mặc vào'],
                            ['hanzi' => '正式', 'pinyin' => 'zhèngshì', 'meaning' => 'trang trọng, chính thức'],
                            ['hanzi' => '西装', 'pinyin' => 'xīzhuāng', 'meaning' => 'bộ âu phục / vest'],
                            ['hanzi' => '跨国', 'pinyin' => 'kuàguó', 'meaning' => 'đa quốc gia'],
                            ['hanzi' => '公司', 'pinyin' => 'gōngsī', 'meaning' => 'công ty'],
                            ['hanzi' => '面试', 'pinyin' => 'miànshì', 'meaning' => 'phỏng vấn'],
                        ],
                    ],
                    [
                        'chinese'    => '面试官 首先 让 我 用 中文 做 自我 介绍 。',
                        'pinyin'     => 'Miànshìguān shǒuxiān ràng wǒ yòng Zhōngwén zuò zìwǒ jièshào.',
                        'vietnamese' => 'Người phỏng vấn trước tiên bảo tôi dùng tiếng Trung để tự giới thiệu bản thân.',
                        'words'      => [
                            ['hanzi' => '面试官', 'pinyin' => 'miànshìguān', 'meaning' => 'người phỏng vấn'],
                            ['hanzi' => '首先', 'pinyin' => 'shǒuxiān', 'meaning' => 'đầu tiên, trước hết'],
                            ['hanzi' => '自我介绍', 'pinyin' => 'zìwǒ jièshào', 'meaning' => 'tự giới thiệu bản thân'],
                        ],
                    ],
                    [
                        'chinese'    => '虽然 有点 紧张 ， 但 我 回答 得 非常 流利 ， 他们 对 我 很 满意 。',
                        'pinyin'     => 'Suīrán yǒudiǎn jǐnzhāng, dàn wǒ huídá de fēicháng liúlì, tāmen duì wǒ hěn mǎnyì.',
                        'vietnamese' => 'Tuy có chút căng thẳng, nhưng tôi đã trả lời rất trôi chảy, họ vô cùng hài lòng về tôi.',
                        'words'      => [
                            ['hanzi' => '虽然', 'pinyin' => 'suīrán', 'meaning' => 'tuy rằng, mặc dù'],
                            ['hanzi' => '紧张', 'pinyin' => 'jǐnzhāng', 'meaning' => 'căng thẳng, hồi hộp'],
                            ['hanzi' => '流利', 'pinyin' => 'liúlì', 'meaning' => 'trôi chảy, lưu loát'],
                            ['hanzi' => '满意', 'pinyin' => 'mǎnyì', 'meaning' => 'hài lòng, vừa ý'],
                        ],
                    ],
                ],
                'quiz_json'     => [
                    [
                        'question'       => '面试官 让 主角 做 什么 ？',
                        'pinyin'         => 'Miànshìguān ràng zhǔjué zuò shénme?',
                        'options'        => ['唱歌', '用 中文 做 自我 介绍', '写 汉字', '喝 咖啡'],
                        'correct_answer' => '用 中文 做 自我 介绍',
                        'explanation'    => 'Trong bài: "面试官 首先 让 我 用 中文 做 自我 介绍 。"',
                    ],
                ],
            ],

            // ══════════════════════════════════════════════
            // THÊM MỚI: HSK 1 HỘI THOẠI THỰC TẾ
            // ══════════════════════════════════════════════
            [
                'title'         => '去图书馆借书',
                'title_pinyin'  => 'Qù túshūguǎn jiè shū',
                'title_vi'      => 'Đi thư viện mượn sách',
                'slug'          => 'di-thu-vien-muon-sach-hsk-1',
                'hsk_level'     => 1,
                'category'      => 'Đời sống',
                'cover_color'   => '#065f46',
                'summary'       => 'Hội thoại ngắn giữa sinh viên và thủ thư khi đến mượn sách học tiếng Trung tại thư viện trường.',
                'word_count'    => 75,
                'estimated_reading_minutes' => 2,
                'is_published'  => true,
                'content_json'  => [
                    [
                        'chinese'    => '王明 ： 你好 ， 请问 这里 有 汉语 书 吗 ？',
                        'pinyin'     => 'Wáng Míng: Nǐ hǎo, qǐngwèn zhèlǐ yǒu hànyǔ shū ma?',
                        'vietnamese' => 'Vương Minh: Xin chào, xin hỏi ở đây có sách tiếng Trung không?',
                    ],
                    [
                        'chinese'    => '管理员 ： 有 的 ， 汉语 书 都 在 二 楼 。',
                        'pinyin'     => 'Guǎnlǐyuán: Yǒu de, hànyǔ shū dōu zài èr lóu.',
                        'vietnamese' => 'Thủ thư: Có chứ, sách tiếng Trung đều ở trên tầng hai.',
                    ],
                    [
                        'chinese'    => '王明 ： 我 想 借 两 本 汉语 词典 ， 可以 吗 ？',
                        'pinyin'     => 'Wáng Míng: Wǒ xiǎng jiè liǎng běn hànyǔ cídiǎn, kěyǐ ma?',
                        'vietnamese' => 'Vương Minh: Tôi muốn mượn hai quyển từ điển tiếng Trung, có được không?',
                    ],
                    [
                        'chinese'    => '管理员 ： 可以 ， 请 给 我 你 的 学生证 。',
                        'pinyin'     => 'Guǎnlǐyuán: Kěyǐ, qǐng gěi wǒ nǐ de xuéshengzhèng.',
                        'vietnamese' => 'Thủ thư: Được chứ, xin vui lòng đưa thẻ sinh viên của bạn cho tôi.',
                    ],
                    [
                        'chinese'    => '王明 ： 给 您 ， 谢谢 老师 ！',
                        'pinyin'     => 'Wáng Míng: Gěi nín, xièxie lǎoshī!',
                        'vietnamese' => 'Vương Minh: Gửi cô ạ, cảm ơn cô!',
                    ],
                ],
                'quiz_json'     => [
                    [
                        'question'       => '汉语 书 在 几 楼 ？',
                        'pinyin'         => 'Hànyǔ shū zài jǐ lóu?',
                        'options'        => ['一 楼', '二 楼', '三 楼', '四 楼'],
                        'correct_answer' => '二 楼',
                        'explanation'    => 'Người thủ thư nói: "汉语 书 都 在 二 楼 。"',
                    ],
                    [
                        'question'       => '王明 想 借 几 本 词典 ？',
                        'pinyin'         => 'Wáng Míng xiǎng jiè jǐ běn cídiǎn?',
                        'options'        => ['一 本', '两 本', '三 本', '五 本'],
                        'correct_answer' => '两 本',
                        'explanation'    => 'Vương Minh nói: "我 想 借 两 本 汉语 词典 。"',
                    ],
                ],
            ],
            [
                'title'         => '问路：去地铁站怎么走？',
                'title_pinyin'  => 'Wènlù: Qù dìtiězhàn zěnme zǒu?',
                'title_vi'      => 'Hỏi đường: Đi đến ga tàu điện ngầm thế nào?',
                'slug'          => 'hoi-duong-di-tau-dien-ngam-hsk-1',
                'hsk_level'     => 1,
                'category'      => 'Giao thông',
                'cover_color'   => '#1e40af',
                'summary'       => 'Hội thoại hỏi đường kinh điển khi đi bộ ở Trung Quốc, cách hỏi hướng đi, rẽ trái, rẽ phải và đi thẳng.',
                'word_count'    => 80,
                'estimated_reading_minutes' => 2,
                'is_published'  => true,
                'content_json'  => [
                    [
                        'chinese'    => '大卫 ： 请问 ， 去 地铁站 怎么 走 ？',
                        'pinyin'     => 'Dàwèi: Qǐngwèn, qù dìtiězhàn zěnme zǒu?',
                        'vietnamese' => 'David: Xin hỏi, đi đến trạm tàu điện ngầm đi đường nào ạ?',
                    ],
                    [
                        'chinese'    => '路人 ： 你 直 走 ， 在 第一个 路口 向 右 转 。',
                        'pinyin'     => 'Lùrén: Nǐ zhí zǒu, zài dì-yī gè lùkǒu xiàng yòu zhuǎn.',
                        'vietnamese' => 'Người qua đường: Bạn đi thẳng, ở ngã tư đầu tiên thì rẽ phải.',
                    ],
                    [
                        'chinese'    => '大卫 ： 离 这里 远 不 远 ？',
                        'pinyin'     => 'Dàwèi: Lí zhèlǐ yuǎn bù yuǎn?',
                        'vietnamese' => 'David: Cách đây có xa không ạ?',
                    ],
                    [
                        'chinese'    => '路人 ： 不 远 ， 走 路 五 分钟 就 到 了 。',
                        'pinyin'     => 'Lùrén: Bù yuǎn, zǒu lù wǔ fēnzhōng jiù dào le.',
                        'vietnamese' => 'Người qua đường: Không xa đâu, đi bộ 5 phút là tới rồi.',
                    ],
                    [
                        'chinese'    => '大卫 ： 太 好 了 ， 非常 感谢 您 ！',
                        'pinyin'     => 'Dàwèi: Tài hǎo le, fēicháng gǎnxiè nín!',
                        'vietnamese' => 'David: Tốt quá, vô cùng cảm ơn bác ạ!',
                    ],
                ],
                'quiz_json'     => [
                    [
                        'question'       => '在 第一个 路口 怎么 走 ？',
                        'pinyin'         => 'Zài dì-yī gè lùkǒu zěnme zǒu?',
                        'options'        => ['向 左 转', '向 右 转', '向 后 走', '坐 出租车'],
                        'correct_answer' => '向 右 转',
                        'explanation'    => 'Người qua đường hướng dẫn: "在 第一个 路口 向 右 转 。"',
                    ],
                    [
                        'question'       => '走 路 去 地铁站 要 多长 时间 ？',
                        'pinyin'         => 'Zǒu lù qù dìtiězhàn yào duō cháng shíjiān?',
                        'options'        => ['五 分钟', '十 分钟', '半 个 小时', '一 个 小时'],
                        'correct_answer' => '五 分钟',
                        'explanation'    => 'Trong bài: "走 路 五 分钟 就 到 了 。"',
                    ],
                ],
            ],
            [
                'title'         => '在服装店买衣服',
                'title_pinyin'  => 'Zài fúzhuāngdiàn mǎi yīfu',
                'title_vi'      => 'Mua quần áo ở cửa hàng thời trang',
                'slug'          => 'mua-quan-ao-o-cua-hang-hsk-1',
                'hsk_level'     => 1,
                'category'      => 'Mua sắm',
                'cover_color'   => '#9d174d',
                'summary'       => 'Cuộc đối thoại mua sắm thú vị: hỏi kích cỡ quần áo, thử đồ và thanh toán.',
                'word_count'    => 85,
                'estimated_reading_minutes' => 2,
                'is_published'  => true,
                'content_json'  => [
                    [
                        'chinese'    => '服务员 ： 你好 ， 欢迎 光临 ！ 你 想 买 什么 ？',
                        'pinyin'     => 'Fúwùyuán: Nǐ hǎo, huānyíng guānglín! Nǐ xiǎng mǎi shénme?',
                        'vietnamese' => 'Nhân viên: Xin chào, hoan nghênh quý khách! Bạn muốn mua gì ạ?',
                    ],
                    [
                        'chinese'    => '玛丽 ： 我 想 看 一下 这 件 白色 的 衬衫 。',
                        'pinyin'     => 'Mǎlì: Wǒ xiǎng kàn yíxià zhè jiàn báisè de chènshān.',
                        'vietnamese' => 'Mary: Tôi muốn xem chiếc áo sơ mi màu trắng này một chút.',
                    ],
                    [
                        'chinese'    => '服务员 ： 您 穿 多大 号 ？ 我们 有 中号 和 大号 。',
                        'pinyin'     => 'Fúwùyuán: Nín chuān duō dà hào? Wǒmen yǒu zhōnghào hé dàhào.',
                        'vietnamese' => 'Nhân viên: Bạn mặc cỡ bao nhiêu ạ? Chúng tôi có cỡ M và cỡ L.',
                    ],
                    [
                        'chinese'    => '玛丽 ： 我 穿 中号 。 我 可以 试 一下 吗 ？',
                        'pinyin'     => 'Mǎlì: Wǒ chuān zhōnghào. Wǒ kěyǐ shì yíxià ma?',
                        'vietnamese' => 'Mary: Tôi mặc cỡ M. Tôi có thể mặc thử một chút được không?',
                    ],
                    [
                        'chinese'    => '服务员 ： 当然 可以 ， 试衣间 在 那边 。',
                        'pinyin'     => 'Fúwùyuán: Dāngrán kěyǐ, shìyījiān zài nàbiān.',
                        'vietnamese' => 'Nhân viên: Đương nhiên được ạ, phòng thay đồ ở đằng kia.',
                    ],
                ],
                'quiz_json'     => [
                    [
                        'question'       => '玛丽 想 买 什么 颜色 的 衬衫 ？',
                        'pinyin'         => 'Mǎlì xiǎng mǎi shénme yánsè de chènshān?',
                        'options'        => ['红色', '黑色', '白色', '黄色'],
                        'correct_answer' => '白色',
                        'explanation'    => 'Mary nói: "我 想 看 一下 这 件 白色 的 衬衫 。"',
                    ],
                ],
            ],
            [
                'title'         => '祝你生日快乐',
                'title_pinyin'  => 'Zhù nǐ shēngrì kuàilè',
                'title_vi'      => 'Chúc bạn sinh nhật vui vẻ',
                'slug'          => 'chuc-mung-sinh-nhat-ban-hsk-1',
                'hsk_level'     => 1,
                'category'      => 'Đời sống',
                'cover_color'   => '#ea580c',
                'summary'       => 'Bữa tiệc sinh nhật ấm áp cùng bạn học, cùng nhau thổi nến, ăn bánh kem và tặng quà.',
                'word_count'    => 70,
                'estimated_reading_minutes' => 2,
                'is_published'  => true,
                'content_json'  => [
                    [
                        'chinese'    => '今天 是 小明 的 生日 ， 我们 去 他 家 玩 。',
                        'pinyin'     => 'Jīntiān shì Xiǎomíng de shēngrì, wǒmen qù tā jiā wán.',
                        'vietnamese' => 'Hôm nay là sinh nhật của Tiểu Minh, chúng tôi đến nhà cậu ấy chơi.',
                    ],
                    [
                        'chinese'    => '我 送 给 他 一 本 漂亮 的 书 。',
                        'pinyin'     => 'Wǒ sòng gěi tā yì běn piàoliang de shū.',
                        'vietnamese' => 'Tôi tặng cậu ấy một cuốn sách rất đẹp.',
                    ],
                    [
                        'chinese'    => '大家 一起 唱 生日 歌 ， 吃 了 甜甜 的 蛋糕 。',
                        'pinyin'     => 'Dàjiā yìqǐ chàng shēngrì gē, chī le tiántián de dàngāo.',
                        'vietnamese' => 'Mọi người cùng nhau hát bài hát chúc mừng sinh nhật và ăn bánh kem ngọt ngào.',
                    ],
                    [
                        'chinese'    => '小明 很 开心 ， 对 大家 说 ： “ 谢谢 你们 ！ ”',
                        'pinyin'     => 'Xiǎomíng hěn kāixīn, duì dàjiā shuō: "Xièxie nǐmen!"',
                        'vietnamese' => 'Tiểu Minh rất vui mừng, nói với mọi người: "Cảm ơn các bạn!"',
                    ],
                ],
                'quiz_json'     => [
                    [
                        'question'       => '主角 送 给 小明 什么 礼物 ？',
                        'pinyin'         => 'Zhǔjué sòng gěi Xiǎomíng shénme lǐwù?',
                        'options'        => ['一 辆 车', '一 本 书', '一 件 衣服', '一 部 手机'],
                        'correct_answer' => '一 本 书',
                        'explanation'    => 'Trong bài: "我 送 给 他 一 本 漂亮 的 书 。"',
                    ],
                ],
            ],

            // ══════════════════════════════════════════════
            // THÊM MỚI: HSK 2 HỘI THOẠI & ĐỜI SỐNG
            // ══════════════════════════════════════════════
            [
                'title'         => '去医院看医生',
                'title_pinyin'  => 'Qù yīyuàn kàn yīshēng',
                'title_vi'      => 'Đi bệnh viện khám bác sĩ',
                'slug'          => 'di-benh-vien-kham-bac-si-hsk-2',
                'hsk_level'     => 2,
                'category'      => 'Đời sống',
                'cover_color'   => '#0891b2',
                'summary'       => 'Hội thoại thực tế khi bị cảm sốt, miêu tả triệu chứng sức khỏe với bác sĩ và nghe lời dặn uống thuốc.',
                'word_count'    => 95,
                'estimated_reading_minutes' => 3,
                'is_published'  => true,
                'content_json'  => [
                    [
                        'chinese'    => '医生 ： 你 哪里 不 舒服 ？',
                        'pinyin'     => 'Yīshēng: Nǐ nǎlǐ bù shūfu?',
                        'vietnamese' => 'Bác sĩ: Bạn cảm thấy không thoải mái ở đâu?',
                    ],
                    [
                        'chinese'    => '病人 ： 医生 ， 我 头疼 ， 嗓子 也 疼 ， 昨天 晚上 开始 发烧 。',
                        'pinyin'     => 'Bìngrén: Yīshēng, wǒ tóuténg, sǎngzi yě téng, zuótiān wǎnshang kāishǐ fāshāo.',
                        'vietnamese' => 'Bệnh nhân: Bác sĩ, tôi bị đau đầu, họng cũng đau, tối hôm qua bắt đầu bị sốt.',
                    ],
                    [
                        'chinese'    => '医生 ： 量 一下 体温 吧 。 三十八 度 五 ， 你 感冒 了 。',
                        'pinyin'     => 'Yīshēng: Liáng yíxià tǐwēn ba. Sānshíbā dù wǔ, nǐ gǎnmào le.',
                        'vietnamese' => 'Bác sĩ: Đo nhiệt độ cơ thể một chút nhé. 38 độ 5, bạn bị cảm cúm rồi.',
                    ],
                    [
                        'chinese'    => '医生 ： 这 种 药 一 天 吃 三 次 ， 每次 吃 两 片 。 多 喝 热水 ， 多 休息 。',
                        'pinyin'     => 'Yīshēng: Zhè zhǒng yào yì tiān chī sān cì, měi cì chī liǎng piàn. Duō hē rèshuǐ, duō xiūxi.',
                        'vietnamese' => 'Bác sĩ: Loại thuốc này ngày uống 3 lần, mỗi lần 2 viên. Hãy uống nhiều nước ấm và nghỉ ngơi nhiều nhé.',
                    ],
                ],
                'quiz_json'     => [
                    [
                        'question'       => '病人的 体温 是 多少 ？',
                        'pinyin'         => 'Bìngrén de tǐwēn shì duōshao?',
                        'options'        => ['三十七 度', '三十八 度 五', '三十九 度', '四十 度'],
                        'correct_answer' => '三十八 度 五',
                        'explanation'    => 'Bác sĩ nói: "三十八 度 五 ， 你 感冒 了 。"',
                    ],
                    [
                        'question'       => '医生 让 病人 每天 吃 几 次 药 ？',
                        'pinyin'         => 'Yīshēng ràng bìngrén měitiān chī jǐ cì yào?',
                        'options'        => ['一 次', '两 次', '三 次', '四 次'],
                        'correct_answer' => '三 次',
                        'explanation'    => 'Bác sĩ dặn: "这 种 药 一 天 吃 三 次 。"',
                    ],
                ],
            ],
            [
                'title'         => '在酒店办理入住',
                'title_pinyin'  => 'Zài jiǔdiàn bànlǐ rùzhù',
                'title_vi'      => 'Làm thủ tục nhận phòng ở khách sạn',
                'slug'          => 'nhan-phong-o-khach-san-hsk-2',
                'hsk_level'     => 2,
                'category'      => 'Du lịch',
                'cover_color'   => '#4f46e5',
                'summary'       => 'Thủ tục check-in tại quầy lễ tân khách sạn, nhận thẻ phòng, hỏi giờ ăn sáng và mật khẩu Wi-Fi.',
                'word_count'    => 105,
                'estimated_reading_minutes' => 3,
                'is_published'  => true,
                'content_json'  => [
                    [
                        'chinese'    => '前台 ： 您好 ， 欢迎 光临 阳光 酒店 ， 请问 有 预订 吗 ？',
                        'pinyin'     => 'Qiántái: Nín hǎo, huānyíng guānglín Yángguāng Jiǔdiàn, qǐngwèn yǒu yùdìng ma?',
                        'vietnamese' => 'Lễ tân: Xin chào, hoan nghênh quý khách đến với khách sạn Ánh Dương, xin hỏi quý khách có đặt phòng trước không ạ?',
                    ],
                    [
                        'chinese'    => '张伟 ： 有 的 ， 我 在 网上 订 了 一 间 大床房 ， 我 叫 张伟 。',
                        'pinyin'     => 'Zhāng Wěi: Yǒu de, wǒ zài wǎngshang dìng le yì jiān dàchuángfáng, wǒ jiào Zhāng Wěi.',
                        'vietnamese' => 'Trương Vĩ: Có chứ, tôi đã đặt trên mạng một phòng giường đôi, tôi tên là Trương Vĩ.',
                    ],
                    [
                        'chinese'    => '前台 ： 查到 了 ， 请 出示 您 的 护照 或 身份证 。 这是 您 的 房卡 ， 房间 在 六 楼 608 号 。',
                        'pinyin'     => 'Qiántái: Chádào le, qǐng chūshì nín de hùzhào huò shēnfènzhèng. Zhè shì nín de fángkǎ, fángjiān zài liù lóu 608 hào.',
                        'vietnamese' => 'Lễ tân: Đã tìm thấy rồi ạ, xin vui lòng xuất trình hộ chiếu hoặc CCCD của quý khách. Đây là thẻ phòng của quý khách, phòng ở tầng 6 số 608.',
                    ],
                    [
                        'chinese'    => '张伟 ： 请问 早上 几点 可以 吃 早餐 ？',
                        'pinyin'     => 'Zhāng Wěi: Qǐngwèn zǎoshang jǐdiǎn kěyǐ chī zǎocān?',
                        'vietnamese' => 'Trương Vĩ: Xin hỏi buổi sáng mấy giờ có thể dùng bữa sáng ạ?',
                    ],
                    [
                        'chinese'    => '前台 ： 早餐 从 七 点 到 九 点 半 ， 在 一 楼 餐厅 。 房间 里 有 免费 Wi-Fi 。',
                        'pinyin'     => 'Qiántái: Zǎocān cóng qī diǎn dào jiǔ diǎn bàn, zài yī lóu cāntīng. Fángjiān lǐ yǒu miǎnfèi Wi-Fi.',
                        'vietnamese' => 'Lễ tân: Bữa sáng từ 7 giờ đến 9 giờ rưỡi, ở nhà hàng tầng 1 ạ. Trong phòng có Wi-Fi miễn phí.',
                    ],
                ],
                'quiz_json'     => [
                    [
                        'question'       => '张伟 的 房间 在 几 楼 ？',
                        'pinyin'         => 'Zhāng Wěi de fángjiān zài jǐ lóu?',
                        'options'        => ['三 楼', '六 楼', '八 楼', '十 楼'],
                        'correct_answer' => '六 楼',
                        'explanation'    => 'Lễ tân thông báo: "房间 在 六 楼 608 号 。"',
                    ],
                ],
            ],
            [
                'title'         => '周末去看电影',
                'title_pinyin'  => 'Zhōumò qù kàn diànyǐng',
                'title_vi'      => 'Cuối tuần rủ nhau đi xem phim',
                'slug'          => 'cuoi-tuan-di-xem-phim-hsk-2',
                'hsk_level'     => 2,
                'category'      => 'Đời sống',
                'cover_color'   => '#7c3aed',
                'summary'       => 'Cuộc hẹn hò cuối tuần cùng bạn bè: chọn phim hài kịch, mua bỏng ngô và thảo luận về nội dung phim.',
                'word_count'    => 90,
                'estimated_reading_minutes' => 3,
                'is_published'  => true,
                'content_json'  => [
                    [
                        'chinese'    => '李丽 ： 这个 星期六 晚上 你 有 空 吗 ？ 我们 一起 去 看 电影 吧 。',
                        'pinyin'     => 'Lǐ Lì: Zhège xīngqīliù wǎnshang nǐ yǒu kòng ma? Wǒmen yìqǐ qù kàn diànyǐng ba.',
                        'vietnamese' => 'Lý Lệ: Tối thứ Bảy tuần này cậu có rảnh không? Chúng mình cùng đi xem phim đi.',
                    ],
                    [
                        'chinese'    => '王明 ： 好 啊 ， 最近 有 什么 好看 的 电影 吗 ？',
                        'pinyin'     => 'Wáng Míng: Hǎo a, zuìjìn yǒu shénme hǎokàn de diànyǐng ma?',
                        'vietnamese' => 'Vương Minh: Được chứ, dạo này có phim gì hay không nhỉ?',
                    ],
                    [
                        'chinese'    => '李丽 ： 有 一 部 新 的 喜剧 电影 ， 听说 非常 搞笑 。 七 点 半 开始 。',
                        'pinyin'     => 'Lǐ Lì: Yǒu yí bù xīn de xǐjù diànyǐng, tīngshuō fēicháng gǎoxiào. Qī diǎn bàn kāishǐ.',
                        'vietnamese' => 'Lý Lệ: Có một bộ phim hài mới ra, nghe nói buồn cười lắm. Bắt đầu lúc 7 giờ rưỡi.',
                    ],
                    [
                        'chinese'    => '王明 ： 太 好 了 ， 我 提前 在 手机 上 买 票 和 爆米花 ， 到 时候 电影院 门口 见 ！',
                        'pinyin'     => 'Wáng Míng: Tài hǎo le, wǒ tíqián zài shǒujī shang mǎi piào hé bàomǐhuā, dào shíhou diànyǐngyuàn ménkǒu jiàn!',
                        'vietnamese' => 'Vương Minh: Tuyệt quá, tớ sẽ mua vé và bỏng ngô trước trên điện thoại, đến lúc đó hẹn gặp ở cửa rạp chiếu phim nhé!',
                    ],
                ],
                'quiz_json'     => [
                    [
                        'question'       => '他们 打算 去 看 什么 类型 的 电影 ？',
                        'pinyin'         => 'Tāmen dǎsuàn qù kàn shénme lèixíng de diànyǐng?',
                        'options'        => ['动作片', '喜剧 电影', '恐怖片', '纪录片'],
                        'correct_answer' => '喜剧 电影',
                        'explanation'    => 'Lý Lệ nói: "有 一 部 新 的 喜剧 电影 。"',
                    ],
                ],
            ],
            [
                'title'         => '点外卖叫奶茶',
                'title_pinyin'  => 'Diǎn wàimài jiào nǎichá',
                'title_vi'      => 'Đặt đồ ăn ngoài và gọi trà sữa',
                'slug'          => 'dat-do-an-ngoai-goi-tra-sua-hsk-2',
                'hsk_level'     => 2,
                'category'      => 'Ẩm thực',
                'cover_color'   => '#c2410c',
                'summary'       => 'Trải nghiệm nét văn hóa đặt đồ ăn và trà sữa qua app tại Trung Quốc: chọn mức ngọt, mức đá và trân châu.',
                'word_count'    => 100,
                'estimated_reading_minutes' => 3,
                'is_published'  => true,
                'content_json'  => [
                    [
                        'chinese'    => '下午 三 点 ， 办公室 的 同事 们 想 喝 下午茶 。',
                        'pinyin'     => 'Xiàwǔ sān diǎn, bàngōngshì de tóngshì men xiǎng hē xiàwǔchá.',
                        'vietnamese' => 'Ba giờ chiều, các đồng nghiệp trong văn phòng muốn uống trà chiều.',
                    ],
                    [
                        'chinese'    => '小华 打开 手机 外卖 软件 ， 问 大家 ： “ 你们 想 喝 什么 奶茶 ？ ”',
                        'pinyin'     => 'Xiǎohuá dǎkāi shǒujī wàimài ruǎnjiàn, wèn dàjiā: "Nǐmen xiǎng hē shénme nǎichá?"',
                        'vietnamese' => 'Tiểu Hoa mở ứng dụng gọi đồ ăn ngoài trên điện thoại, hỏi mọi người: "Các bạn muốn uống trà sữa gì nào?"',
                    ],
                    [
                        'chinese'    => '我 说 ： “ 我 要 一 杯 珍珠 奶茶 ， 少 糖 ， 少 冰 。 ”',
                        'pinyin'     => 'Wǒ shuō: "Wǒ yào yì bēi zhēnzhū nǎichá, shǎo táng, shǎo bīng."',
                        'vietnamese' => 'Tôi nói: "Cho tớ một cốc trà sữa trân châu, ít đường, ít đá nhé."',
                    ],
                    [
                        'chinese'    => '外卖 员 的 速度 非常 快 ， 半 个 小时 后 就 送到 了 公司 楼下 。',
                        'pinyin'     => 'Wàimàiyuán de sùdù fēicháng kuài, bàn gè xiǎoshí hòu jiù sòngdào le gōngsī lóuxià.',
                        'vietnamese' => 'Tốc độ của anh shipper rất nhanh, nửa tiếng sau đã giao đến tận dưới sảnh công ty.',
                    ],
                ],
                'quiz_json'     => [
                    [
                        'question'       => '主角 想 喝 什么 奶茶 ？',
                        'pinyin'         => 'Zhǔjué xiǎng hē shénme nǎichá?',
                        'options'        => ['绿茶', '珍珠 奶茶', '咖啡', '柠檬水'],
                        'correct_answer' => '珍珠 奶茶',
                        'explanation'    => 'Trong bài: "我 要 一 杯 珍珠 奶茶 ， 少 糖 ， 少 冰 。"',
                    ],
                ],
            ],
            [
                'title'         => '去银行换钱',
                'title_pinyin'  => 'Qù yínháng huàn qián',
                'title_vi'      => 'Đến ngân hàng đổi tiền',
                'slug'          => 'den-ngan-hang-doi-tien-hsk-2',
                'hsk_level'     => 2,
                'category'      => 'Giao tiếp',
                'cover_color'   => '#047857',
                'summary'       => 'Cuộc đối thoại tại quầy giao dịch ngân hàng: hỏi tỷ giá hối đoái, đổi tiền Đô la sang Nhân dân tệ.',
                'word_count'    => 90,
                'estimated_reading_minutes' => 3,
                'is_published'  => true,
                'content_json'  => [
                    [
                        'chinese'    => '保罗 ： 您好 ， 我 想 把 美元 换成 人民币 。',
                        'pinyin'     => 'Bǎoluó: Nǐn hǎo, wǒ xiǎng bǎ měiyuán huànchéng rénmínbì.',
                        'vietnamese' => 'Paul: Xin chào, tôi muốn đổi tiền Đô la Mỹ sang đồng Nhân dân tệ.',
                    ],
                    [
                        'chinese'    => '工作人员 ： 好的 ， 请问 今天 的 汇率 是 多少 ？ 一 百 美元 可以 换 七百 二十 元 人民币 。',
                        'pinyin'     => 'Gōngzuò rényuán: Hǎode, jīntiān de huìlǜ shì yì bǎi měiyuán kěyǐ huàn qībǎi èrshí yuán rénmínbì.',
                        'vietnamese' => 'Nhân viên: Vâng, tỷ giá hôm nay là 100 Đô la Mỹ đổi được 720 Nhân dân tệ ạ.',
                    ],
                    [
                        'chinese'    => '保罗 ： 我 想 换 五百 美元 ， 这是 我 的 护照 。',
                        'pinyin'     => 'Bǎoluó: Wǒ xiǎng huàn wǔbǎi měiyuán, zhè shì wǒ de hùzhào.',
                        'vietnamese' => 'Paul: Tôi muốn đổi 500 Đô la, đây là hộ chiếu của tôi.',
                    ],
                    [
                        'chinese'    => '工作人员 ： 请 在 这里 签 一下 您 的 名字 ， 请 点 一下 现金 。',
                        'pinyin'     => 'Gōngzuò rényuán: Qǐng zài zhèlǐ qiān yíxià nín de míngzi, qǐng diǎn yíxià xiànjīn.',
                        'vietnamese' => 'Nhân viên: Xin vui lòng ký tên vào đây ạ, mời quý khách đếm lại tiền mặt.',
                    ],
                ],
                'quiz_json'     => [
                    [
                        'question'       => '保罗 想 换 多少 美元 ？',
                        'pinyin'         => 'Bǎoluó xiǎng huàn duōshao měiyuán?',
                        'options'        => ['一百 美元', '三 百 美元', '五百 美元', '一千 美元'],
                        'correct_answer' => '五百 美元',
                        'explanation'    => 'Paul nói: "我 想 换 五百 美元 。"',
                    ],
                ],
            ],

            // ══════════════════════════════════════════════
            // THÊM MỚI: HSK 3 HỘI THOẠI & ĐỜI SỐNG NÂNG CAO
            // ══════════════════════════════════════════════
            [
                'title'         => '去理发店剪头发',
                'title_pinyin'  => 'Qù lǐfàdiàn jiǎn tóufa',
                'title_vi'      => 'Đi tiệm cắt tóc',
                'slug'          => 'di-tiem-cat-toc-hsk-3',
                'hsk_level'     => 3,
                'category'      => 'Đời sống',
                'cover_color'   => '#be185d',
                'summary'       => 'Cách trao đổi chi tiết với thợ cắt tóc bằng tiếng Trung: cắt ngắn hai bên, tỉa bớt mái và sấy tạo kiểu.',
                'word_count'    => 110,
                'estimated_reading_minutes' => 3,
                'is_published'  => true,
                'content_json'  => [
                    [
                        'chinese'    => '理发师 ： 您好 ， 帅哥 ， 今天 想 怎么 剪 ？',
                        'pinyin'     => 'Lǐfàshī: Nín hǎo, shuàigē, jīntiān xiǎng zěnme jiǎn?',
                        'vietnamese' => 'Thợ cắt tóc: Xin chào anh đẹp trai, hôm nay anh muốn cắt thế nào ạ?',
                    ],
                    [
                        'chinese'    => '顾客 ： 两 边 剪 短 一点 ， 上面 稍微 修 一 修 就 行 了 。',
                        'pinyin'     => 'Gùkè: Liǎng biān jiǎn duǎn yìdiǎn, shàngmiàn shāowēi xiū yì xiū jiù xíng le.',
                        'vietnamese' => 'Khách hàng: Hai bên cắt ngắn một chút, bên trên tỉa bớt một tí là được rồi.',
                    ],
                    [
                        'chinese'    => '理发师 ： 需要 洗 头 和 吹风 吗 ？ 我们 店 现在 有 优惠 活动 。',
                        'pinyin'     => 'Lǐfàshī: Xūyào xǐ tóu hé chuīfēng ma? Wǒmen diàn xiànzài yǒu yōuhuì huódòng.',
                        'vietnamese' => 'Thợ cắt tóc: Anh có cần gội đầu và sấy tạo kiểu không ạ? Tiệm chúng em hiện đang có chương trình khuyến mãi.',
                    ],
                    [
                        'chinese'    => '顾客 ： 好 的 ， 帮 我 洗 个 头 吧 ， 谢谢 ！ 剪 完 感觉 整个人 精神 多 了 。',
                        'pinyin'     => 'Gùkè: Hǎo de, bāng wǒ xǐ gè tóu ba, xièxie! Jiǎn wán gǎnjué zhěng gè rén jīngshen duō le.',
                        'vietnamese' => 'Khách hàng: Được, gội đầu giúp tôi nhé, cảm ơn! Cắt xong cảm thấy cả người sảng khoái, tràn đầy năng lượng hơn hẳn.',
                    ],
                ],
                'quiz_json'     => [
                    [
                        'question'       => '顾客 要求 两 边 怎么 剪 ？',
                        'pinyin'         => 'Gùkè yāoqiú liǎng biān zěnme jiǎn?',
                        'options'        => ['留 长', '剪 短 一点', '染 颜色', '烫 发'],
                        'correct_answer' => '剪 短 一点',
                        'explanation'    => 'Khách hàng nói: "两 边 剪 短 一点 。"',
                    ],
                ],
            ],
            [
                'title'         => '计划去古城西安旅游',
                'title_pinyin'  => 'Jìhuà qù gǔchéng Xī\'ān lǚyóu',
                'title_vi'      => 'Kế hoạch đi du lịch cổ thành Tây An',
                'slug'          => 'ke-hoach-du-lich-tay-an-hsk-3',
                'hsk_level'     => 3,
                'category'      => 'Du lịch',
                'cover_color'   => '#b45309',
                'summary'       => 'Kế hoạch du lịch cố đô Tây An: ghé thăm Binh Mã Dũng nghìn năm, đạp xe trên tường thành cổ và thưởng thức thịt cừu hầm bánh mì kẹp.',
                'word_count'    => 120,
                'estimated_reading_minutes' => 4,
                'is_published'  => true,
                'content_json'  => [
                    [
                        'chinese'    => '下 个 月 有 五 天 假期 ， 我 和 朋友 决定 去 西安 旅游 。',
                        'pinyin'     => 'Xià gè yuè yǒu wǔ tiān jiàqī, wǒ hé péngyou juédìng qù Xī\'ān lǚyóu.',
                        'vietnamese' => 'Tháng sau có kỳ nghỉ 5 ngày, tôi và bạn bè quyết định đi du lịch Tây An.',
                    ],
                    [
                        'chinese'    => '西安 是 中国 历史 上 非常 著名 的 古都 。',
                        'pinyin'     => 'Xī\'ān shì Zhōngguó lìshǐ shang fēicháng zhùmíng de gǔdū.',
                        'vietnamese' => 'Tây An là một cố đô vô cùng nổi tiếng trong lịch sử Trung Quốc.',
                    ],
                    [
                        'chinese'    => '我们 计划 第一天 去 看 兵马俑 ， 第二天 在 古城墙 上 骑 自行车 。',
                        'pinyin'     => 'Wǒmen jìhuà dì-yī tiān qù kàn bīngmǎyǒng, dì-èr tiān zài gǔchéngqiáng shang qí zìxíngchē.',
                        'vietnamese' => 'Chúng tôi lên kế hoạch ngày đầu tiên đi xem Binh Mã Dũng, ngày thứ hai đạp xe đạp trên tường thành cổ.',
                    ],
                    [
                        'chinese'    => '当然 ， 我们 还 要 去 回民街 品尝 当地 著名 的 肉夹馍 和 羊肉泡馍 。',
                        'pinyin'     => 'Dāngrán, wǒmen hái yào qù Huímín Jiē pǐncháng dāngdì zhùmíng de ròujiāmó hé yángròupàomó.',
                        'vietnamese' => 'Đương nhiên, chúng tôi còn sẽ đến Phố người Hồi để nếm thử món bánh mì kẹp thịt (Roujiamo) và canh thịt cừu hầm bánh nổi tiếng nơi đây.',
                    ],
                ],
                'quiz_json'     => [
                    [
                        'question'       => '他们 计划 第一天 去 哪里 ？',
                        'pinyin'         => 'Tāmen jìhuà dì-yī tiān qù nǎlǐ?',
                        'options'        => ['回民街', '兵马俑', '故宫', '长城'],
                        'correct_answer' => '兵马俑',
                        'explanation'    => 'Trong bài: "我们 计划 第一天 去 看 兵马俑 。"',
                    ],
                ],
            ],
            [
                'title'         => '在中国开银行账户和绑定微信支付',
                'title_pinyin'  => 'Zài Zhōngguó kāi yínháng zhànghù hé bǎngdìng Wēixìn Zhīfù',
                'title_vi'      => 'Mở tài khoản ngân hàng và liên kết WeChat Pay tại Trung Quốc',
                'slug'          => 'mo-tai-khoan-ngan-hang-lien-ket-wechat-hsk-3',
                'hsk_level'     => 3,
                'category'      => 'Đời sống',
                'cover_color'   => '#15803d',
                'summary'       => 'Hướng dẫn thực tế từng bước mở thẻ ngân hàng và quét mã thanh toán điện tử không dùng tiền mặt ở Trung Quốc.',
                'word_count'    => 130,
                'estimated_reading_minutes' => 4,
                'is_published'  => true,
                'content_json'  => [
                    [
                        'chinese'    => '刚 到 中国 留学 的 时候 ， 老师 建议 我们 尽快 办 一 张 中国 的 银行卡 。',
                        'pinyin'     => 'Gāng dào Zhōngguó liúxué de shíhou, lǎoshī jiànyì wǒmen jìnkuài bàn yì zhāng Zhōngguó de yínhángkǎ.',
                        'vietnamese' => 'Khi mới đến Trung Quốc du học, thầy cô khuyên chúng tôi nên nhanh chóng làm một chiếc thẻ ngân hàng Trung Quốc.',
                    ],
                    [
                        'chinese'    => '因为 在 中国 生活 ， 几乎 所有 地方 都 可以 用 手机 扫 码 支付 。',
                        'pinyin'     => 'Yīnwèi zài Zhōngguó shēnghuó, jīhū suǒyǒu dìfang dōu kěyǐ yòng shǒujī sǎo mǎ zhīfù.',
                        'vietnamese' => 'Bởi vì sống ở Trung Quốc, hầu như tất cả mọi nơi đều có thể dùng điện thoại để quét mã thanh toán.',
                    ],
                    [
                        'chinese'    => '我 带 着 护照 和 录取 通知书 去 了 工商 银行 ， 顺利 开通 了 账户 。',
                        'pinyin'     => 'Wǒ dài zhe hùzhào hé lùqǔ tōngzhīshū qù le Gōngshāng Yínháng, shùnlì kāitōng le zhànghù.',
                        'vietnamese' => 'Tôi mang theo hộ chiếu và giấy báo trúng tuyển đến Ngân hàng Công Thương (ICBC), thuận lợi mở được tài khoản.',
                    ],
                    [
                        'chinese'    => '把 银行卡 绑定 到 微信 和 支付宝 以后 ， 买 东西 、 坐 地铁 都 变得 特别 方便 ！',
                        'pinyin'     => 'Bǎ yínhángkǎ bǎngdìng dào Wēixìn hé Zhīfùbǎo yǐhòu, mǎi dōngxi, zuò dìtiě dōu biànde tèbié fāngbiàn!',
                        'vietnamese' => 'Sau khi liên kết thẻ ngân hàng vào WeChat và Alipay, việc mua sắm đồ đạc, đi tàu điện ngầm đều trở nên vô cùng tiện lợi!',
                    ],
                ],
                'quiz_json'     => [
                    [
                        'question'       => '在 中国 生活 ， 人们 习惯 怎么 支付 ？',
                        'pinyin'         => 'Zài Zhōngguó shēnghuó, rénmen xíguàn zěnme zhīfù?',
                        'options'        => ['只 用 现金', '手机 扫 码 支付', '写 支票', '借 钱'],
                        'correct_answer' => '手机 扫 码 支付',
                        'explanation'    => 'Trong bài: "几乎 所有 地方 都 可以 用 手机 扫 码 支付 。"',
                    ],
                ],
            ],

            // ══════════════════════════════════════════════
            // THÊM MỚI: HSK 4 VĂN HÓA & TRẢI NGHIỆM THỰC CHIẾN
            // ══════════════════════════════════════════════
            [
                'title'         => '游览北京故宫博物院',
                'title_pinyin'  => 'Yóulǎn Běijīng Gùgōng Bówùyuàn',
                'title_vi'      => 'Tham quan Bảo tàng Cố Cung Bắc Kinh (Tử Cấm Thành)',
                'slug'          => 'tham-quan-co-cung-bac-kinh-hsk-4',
                'hsk_level'     => 4,
                'category'      => 'Văn hóa',
                'cover_color'   => '#991b1b',
                'summary'       => 'Hành trình khám phá vẻ đẹp nguy nga tráng lệ của Tử Cấm Thành với hơn 600 năm lịch sử triều Minh và triều Thanh.',
                'word_count'    => 140,
                'estimated_reading_minutes' => 4,
                'is_published'  => true,
                'content_json'  => [
                    [
                        'chinese'    => '北京 故宫 ， 又 称 紫禁城 ， 是 明 清 两 代 的 皇家 宫殿 。',
                        'pinyin'     => 'Běijīng Gùgōng, yòu chēng Zǐjìnchéng, shì Míng Qīng liǎng dài de huángjiā gōngdiàn.',
                        'vietnamese' => 'Cố Cung Bắc Kinh, còn gọi là Tử Cấm Thành, là cung điện hoàng gia của hai triều đại Minh và Thanh.',
                    ],
                    [
                        'chinese'    => '走进 午门 ， 宏伟 的 建筑 群 和 红 墙 黄 瓦 映入 眼帘 ， 令人 叹为观止 。',
                        'pinyin'     => 'Zǒu jìn Wǔmén, hóngwěi de jiànzhù qún hé hóng qiáng huáng wǎ yìngrù yǎnlián, lìngrén tànwéiguānzhǐ.',
                        'vietnamese' => 'Bước vào Ngọ Môn, quần thể kiến trúc hùng vĩ cùng những bức tường đỏ ngói vàng đập vào mắt, khiến người ta phải trầm trồ thán phục.',
                    ],
                    [
                        'chinese'    => '太和殿 是 故宫 中 最大 的 殿宇 ， 皇帝 登基 等 重大 典礼 都 在 这里 举行 。',
                        'pinyin'     => 'Tàihédiàn shì Gùgōng zhōng zuì dà de diànyǔ, huángdì dēngjī děng zhòngdà diǎnlǐ dōu zài zhèlǐ jǔxíng.',
                        'vietnamese' => 'Điện Thái Hòa là điện lớn nhất trong Cố Cung, các đại lễ trọng đại như lễ đăng cơ của Hoàng đế đều được tổ chức tại nơi đây.',
                    ],
                    [
                        'chinese'    => '故宫 不仅 展现 了 中国 古代 建筑 的 智慧 ， 更 是 中华 优秀 传统 文化 的 象征 。',
                        'pinyin'     => 'Gùgōng bùjǐn zhǎnxiàn le Zhōngguó gǔdài jiànzhù de zhìhuì, gèng shì Zhōnghuá yōuxiù chuántǒng wénhuà de xiàngzhēng.',
                        'vietnamese' => 'Cố Cung không chỉ phô diễn trí tuệ của kiến trúc cổ đại Trung Hoa, mà còn là biểu tượng của nền văn hóa truyền thống ưu tú.',
                    ],
                ],
                'quiz_json'     => [
                    [
                        'question'       => '故宫 中 最大 的 殿宇 是 哪 一个 ？',
                        'pinyin'         => 'Gùgōng zhōng zuì dà de diànyǔ shì nǎ yí gè?',
                        'options'        => ['乾清宫', '太和殿', '保和殿', '中和殿'],
                        'correct_answer' => '太和殿',
                        'explanation'    => 'Trong bài: "太和殿 是 故宫 中 最大 的 殿宇 。"',
                    ],
                ],
            ],
            [
                'title'         => '中国茶文化与功夫茶',
                'title_pinyin'  => 'Zhōngguó chá wénhuà yǔ gōngfuchá',
                'title_vi'      => 'Văn hóa Trà đạo Trung Hoa và Trà Công Phu',
                'slug'          => 'van-hoa-tra-dao-va-tra-cong-phu-hsk-4',
                'hsk_level'     => 4,
                'category'      => 'Văn hóa',
                'cover_color'   => '#365314',
                'summary'       => 'Nghệ thuật thưởng trà thanh tao của người Trung Quốc: từ trà xanh, trà ô long đến quy trình pha trà công phu tinh tế.',
                'word_count'    => 145,
                'estimated_reading_minutes' => 4,
                'is_published'  => true,
                'content_json'  => [
                    [
                        'chinese'    => '茶 是 中国 的 国饮 ， 中国 人 喝 茶 已经 有 几千年 的 历史 了 。',
                        'pinyin'     => 'Chá shì Zhōngguó de guóyǐn, Zhōngguó rén hē chá yǐjīng yǒu jǐ qiān nián de lìshǐ le.',
                        'vietnamese' => 'Trà là thức uống quốc hồn quốc túy của Trung Quốc, người Trung Quốc uống trà đã có lịch sử mấy ngàn năm rồi.',
                    ],
                    [
                        'chinese'    => '中国 茶 主要 分为 绿茶 、 红茶 、 乌龙茶 、 白茶 、 黑茶 和 黄茶 六 大 类 。',
                        'pinyin'     => 'Zhōngguó chá zhǔyào fēnwéi lǜchá, hóngchá, wūlóngchá, báichá, hēichá hé huángchá liù dà lèi.',
                        'vietnamese' => 'Trà Trung Quốc chủ yếu được chia thành 6 loại lớn: lục trà, hồng trà, ô long trà, bạch trà, hắc trà và hoàng trà.',
                    ],
                    [
                        'chinese'    => '在 广东 和 福建 一带 ， 人们 喜欢 泡 功夫茶 ， 讲究 水温 、 茶具 和 泡 茶 的 步骤 。',
                        'pinyin'     => 'Zài Guǎngdōng hé Fújiàn yídài, rénmen xǐhuan pào gōngfuchá, jiǎngjiu shuǐwēn, chájù hé pào chá de bùzhòu.',
                        'vietnamese' => 'Ở vùng Quảng Đông và Phúc Kiến, mọi người rất thích pha trà Công Phu, vô cùng tỉ mỉ về nhiệt độ nước, ấm chén và các bước hãm trà.',
                    ],
                    [
                        'chinese'    => '品 茶 不仅 能 提神 醒脑 ， 更 能 让 人 在 忙碌 的 生活 中 感受 内心 的 平静 。',
                        'pinyin'     => 'Pǐn chá bùjǐn néng tíshén xǐngnǎo, gèng néng ràng rén zài mánglù de shēnghuó zhōng gǎnshòu nèixīn de píngjìng.',
                        'vietnamese' => 'Thưởng thức trà không chỉ giúp tỉnh táo tinh thần, mà còn giúp con người cảm nhận sự tĩnh lặng trong tâm hồn giữa cuộc sống bận rộn.',
                    ],
                ],
                'quiz_json'     => [
                    [
                        'question'       => '中国 茶 主要 分为 几 大 类 ？',
                        'pinyin'         => 'Zhōngguó chá zhǔyào fēnwéi jǐ dà lèi?',
                        'options'        => ['三 类', '四 类', '六 大 类', '八 类'],
                        'correct_answer' => '六 大 类',
                        'explanation'    => 'Trong bài: "中国 茶 主要 分为 ... 六 大 类 。"',
                    ],
                ],
            ],
            [
                'title'         => '中秋节赏月与吃月饼',
                'title_pinyin'  => 'Zhōngqiūjié shǎng yuè yǔ chī yuèbing',
                'title_vi'      => 'Tết Trung Thu ngắm trăng tròn và ăn bánh Trung Thu',
                'slug'          => 'tet-trung-thu-ngam-trang-va-an-banh-hsk-4',
                'hsk_level'     => 4,
                'category'      => 'Văn hóa',
                'cover_color'   => '#d97706',
                'summary'       => 'Ý nghĩa đoàn viên ngày Tết Trung Thu nông lịch ngày 15 tháng 8, sự tích Hằng Nga bôn nguyệt và các loại bánh nướng truyền thống.',
                'word_count'    => 135,
                'estimated_reading_minutes' => 4,
                'is_published'  => true,
                'content_json'  => [
                    [
                        'chinese'    => '每年 农历 八月 十五 是 中国 的 传统 节日 —— 中秋节 。',
                        'pinyin'     => 'Měinián nónglì bāyuè shíwǔ shì Zhōngguó de chuántǒng jiérì —— Zhōngqiūjié.',
                        'vietnamese' => 'Hàng năm vào ngày 15 tháng 8 Âm lịch là ngày lễ truyền thống của Trung Quốc —— Tết Trung Thu.',
                    ],
                    [
                        'chinese'    => '在 这 一 天 ， 月亮 最 圆 最 亮 ， 象征 着 亲人 团圆 和 美满 。',
                        'pinyin'     => 'Zài zhè yì tiān, yuèliang zuì yuán zuì liàng, xiàngzhēng zhe qīnrén tuányuán hé měimǎn.',
                        'vietnamese' => 'Vào ngày này, mặt trăng tròn nhất và sáng nhất, tượng trưng cho sự đoàn tụ và viên mãn của người thân trong gia đình.',
                    ],
                    [
                        'chinese'    => '家人 们 会 聚 在 一起 吃 团圆饭 ， 一边 赏月 ， 一边 吃 各种 口味 的 月饼 。',
                        'pinyin'     => 'Jiārén men huì jù zài yìqǐ chī tuányuánfàn, yìbiān shǎng yuè, yìbiān chī gè zhǒng kǒuwèi de yuèbing.',
                        'vietnamese' => 'Các thành viên trong gia đình sẽ quây quần bên nhau ăn bữa cơm đoàn viên, vừa ngắm trăng vừa thưởng thức đủ loại hương vị bánh Trung Thu.',
                    ],
                    [
                        'chinese'    => '“ 但愿 人 长久 ， 千里 共 婵娟 ” ， 这 句 诗 表达 了 对 远方 亲友 最 美好 的 祝福 。',
                        'pinyin'     => '"Dànyuàn rén chángjiǔ, qiānlǐ gòng chánjuān", zhè jù shī biǎodá le duì yuǎnfāng qīnyǒu zuì měihǎo de zhùfú.',
                        'vietnamese' => '"Đãn nguyện nhân trường cửu, thiên lý cộng thiền quyên", câu thơ này thể hiện lời chúc phúc tốt đẹp nhất gửi đến người thân nơi phương xa.',
                    ],
                ],
                'quiz_json'     => [
                    [
                        'question'       => '中秋节 是 农历 的 哪 一 天 ？',
                        'pinyin'         => 'Zhōngqiūjié shì nónglì de nǎ yì tiān?',
                        'options'        => ['正月 十五', '五月 初五', '八月 十五', '九月 初九'],
                        'correct_answer' => '八月 十五',
                        'explanation'    => 'Trong bài: "每年 农历 八月 十五 是 ... 中秋节 。"',
                    ],
                ],
            ],
        ];

        foreach ($stories as $item) {
            Story::updateOrCreate(
                ['slug' => $item['slug']],
                $item
            );
        }
    }
}

