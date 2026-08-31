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
        ];

        foreach ($stories as $item) {
            Story::updateOrCreate(
                ['slug' => $item['slug']],
                $item
            );
        }
    }
}
