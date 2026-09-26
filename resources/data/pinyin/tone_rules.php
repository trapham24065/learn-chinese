<?php

return [
    'tones' => [
        [
            'tone' => 1,
            'name' => 'Thanh 1 (Âm bình - 阴平)',
            'contour' => '55',
            'symbol' => '─',
            'curve_shape' => 'high_flat',
            'desc' => 'Cao độ cao và bằng phẳng (5-5), giữ đều âm lượng từ đầu đến cuối như khi ngân dài nốt nhạc cao.',
            'pitch_graph' => [5, 5],
            'sample_syllable' => 'mā',
            'sample_char' => '妈',
            'meaning' => 'mẹ',
        ],
        [
            'tone' => 2,
            'name' => 'Thanh 2 (Dương bình - 阳平)',
            'contour' => '35',
            'symbol' => '╱',
            'curve_shape' => 'rising',
            'desc' => 'Bắt đầu ở mức trung bình (3) rồi vút lên cao (5), tương tự như dấu sắc trong tiếng Việt hoặc khi hỏi "Hả?".',
            'pitch_graph' => [3, 5],
            'sample_syllable' => 'má',
            'sample_char' => '麻',
            'meaning' => 'cây gai, tê',
        ],
        [
            'tone' => 3,
            'name' => 'Thanh 3 (Thượng thanh - 上声)',
            'contour' => '214',
            'symbol' => '╲╱',
            'curve_shape' => 'dipping',
            'desc' => 'Bắt đầu ở mức thấp vừa (2), hạ xuống tận đáy (1) rồi uốn vút lên cao (4). Khi nói nhanh thường đọc thanh 3 nửa (chỉ hạ 21).',
            'pitch_graph' => [2, 1, 4],
            'sample_syllable' => 'mǎ',
            'sample_char' => '马',
            'meaning' => 'con ngựa',
        ],
        [
            'tone' => 4,
            'name' => 'Thanh 4 (Khứ thanh - 去声)',
            'contour' => '51',
            'symbol' => '╲',
            'curve_shape' => 'falling',
            'desc' => 'Bắt đầu từ đỉnh cao nhất (5) rơi thẳng dứt khoát xuống đáy (1). Dứt khoát, mạnh mẽ, tương tự như tiếng ra lệnh trong tiếng Việt.',
            'pitch_graph' => [5, 1],
            'sample_syllable' => 'mà',
            'sample_char' => '骂',
            'meaning' => 'mắng, chửi',
        ],
        [
            'tone' => 0,
            'name' => 'Thanh nhẹ (Khinh thanh - 轻声)',
            'contour' => '•',
            'symbol' => '•',
            'curve_shape' => 'neutral',
            'desc' => 'Không mang dấu thanh điệu, phát âm ngắn, nhẹ và rơi tự nhiên vào âm đứng trước nó.',
            'pitch_graph' => [2],
            'sample_syllable' => 'ma',
            'sample_char' => '吗',
            'meaning' => 'chăng, à (trợ từ)',
        ],
    ],

    'sandhi_rules' => [
        [
            'id' => 'two_third_tones',
            'title' => 'Biến điệu của hai Thanh 3 (三声变调)',
            'tag' => 'Phổ biến nhất',
            'rule_summary' => 'Khi 2 thanh 3 đi liền nhau: Thanh 3 phía trước ĐỌC THÀNH THANH 2, chính tả chữ viết giữ nguyên.',
            'explanation' => 'Trong tiếng Trung, hai âm điệu thấp (214) đi liền nhau sẽ rất khó phát âm trôi chảy. Vì vậy âm thanh phía trước tự động trượt lên thanh 2 (35) để tạo nhịp điệu tự nhiên.',
            'examples' => [
                [
                    'orthography' => 'nǐ hǎo',
                    'pronunciation' => 'ní hǎo',
                    'characters' => '你好',
                    'meaning' => 'Xin chào',
                ],
                [
                    'orthography' => 'kě yǐ',
                    'pronunciation' => 'ké yǐ',
                    'characters' => '可以',
                    'meaning' => 'Có thể, được',
                ],
                [
                    'orthography' => 'yǔ fǎ',
                    'pronunciation' => 'yú fǎ',
                    'characters' => '语法',
                    'meaning' => 'Ngữ pháp',
                ],
                [
                    'orthography' => 'shǒu biǎo',
                    'pronunciation' => 'shóu biǎo',
                    'characters' => '手表',
                    'meaning' => 'Đồng hồ đeo tay',
                ],
            ],
        ],

        [
            'id' => 'yi_sandhi',
            'title' => 'Quy tắc Biến điệu chữ 一 (yī - Số 1)',
            'tag' => 'Cực kỳ quan trọng',
            'rule_summary' => 'Đọc "yī" khi đếm số; đọc "yì" trước thanh 1, 2, 3; đọc "yí" trước thanh 4.',
            'explanation' => 'Chữ "一" nguyên bản mang thanh 1 (yī). Nhưng trong văn cảnh tự nhiên, nó sẽ thay đổi linh hoạt theo thanh điệu của từ đi liền sau.',
            'cases' => [
                [
                    'condition' => 'Đứng độc lập hoặc số đếm / thứ tự: Giữ nguyên Thanh 1 (yī)',
                    'examples' => [
                        ['orthography' => 'yī, èr, sān', 'pronunciation' => 'yī, èr, sān', 'characters' => '一二三', 'meaning' => '1, 2, 3'],
                        ['orthography' => 'dì yī', 'pronunciation' => 'dì yī', 'characters' => '第一', 'meaning' => 'thứ nhất, vị trí số 1'],
                    ],
                ],
                [
                    'condition' => 'Đứng trước từ mang Thanh 1, 2, 3: Đọc thành Thanh 4 (yì)',
                    'examples' => [
                        ['orthography' => 'yī tiān', 'pronunciation' => 'yì tiān', 'characters' => '一天', 'meaning' => 'một ngày (trước thanh 1)'],
                        ['orthography' => 'yī nián', 'pronunciation' => 'yì nián', 'characters' => '一年', 'meaning' => 'một năm (trước thanh 2)'],
                        ['orthography' => 'yī qǐ', 'pronunciation' => 'yì qǐ', 'characters' => '一起', 'meaning' => 'cùng nhau (trước thanh 3)'],
                    ],
                ],
                [
                    'condition' => 'Đứng trước từ mang Thanh 4: Đọc thành Thanh 2 (yí)',
                    'examples' => [
                        ['orthography' => 'yī gè', 'pronunciation' => 'yí gè', 'characters' => '一个', 'meaning' => 'một cái (trước thanh 4)'],
                        ['orthography' => 'yī dìng', 'pronunciation' => 'yí dìng', 'characters' => '一定', 'meaning' => 'nhất định (trước thanh 4)'],
                        ['orthography' => 'yī yàng', 'pronunciation' => 'yí yàng', 'characters' => '一样', 'meaning' => 'giống nhau (trước thanh 4)'],
                    ],
                ],
            ],
        ],

        [
            'id' => 'bu_sandhi',
            'title' => 'Quy tắc Biến điệu chữ 不 (bù - Không)',
            'tag' => 'Cực kỳ quan trọng',
            'rule_summary' => 'Bình thường đọc "bù" (thanh 4); Khi đứng trước một từ Thanh 4 khác thì đọc thành "bú" (thanh 2).',
            'explanation' => 'Hai thanh 4 (51) mạnh đi liền nhau sẽ tạo cảm giác giật cục, gắt tai. Do đó chữ "不" đi trước được làm mềm biến thành thanh 2 (35).',
            'cases' => [
                [
                    'condition' => 'Đứng trước Thanh 1, 2, 3: Giữ nguyên Thanh 4 (bù)',
                    'examples' => [
                        ['orthography' => 'bù hē', 'pronunciation' => 'bù hē', 'characters' => '不喝', 'meaning' => 'không uống (trước thanh 1)'],
                        ['orthography' => 'bù lái', 'pronunciation' => 'bù lái', 'characters' => '不来', 'meaning' => 'không đến (trước thanh 2)'],
                        ['orthography' => 'bù hǎo', 'pronunciation' => 'bù hǎo', 'characters' => '不好', 'meaning' => 'không tốt (trước thanh 3)'],
                    ],
                ],
                [
                    'condition' => 'Đứng trước từ mang Thanh 4: Đọc thành Thanh 2 (bú)',
                    'examples' => [
                        ['orthography' => 'bù shì', 'pronunciation' => 'bú shì', 'characters' => '不是', 'meaning' => 'không phải (trước thanh 4)'],
                        ['orthography' => 'bù duì', 'pronunciation' => 'bú duì', 'characters' => '不对', 'meaning' => 'không đúng (trước thanh 4)'],
                        ['orthography' => 'bù qù', 'pronunciation' => 'bú qù', 'characters' => '不去', 'meaning' => 'không đi (trước thanh 4)'],
                    ],
                ],
            ],
        ],

        [
            'id' => 'u_dots_rule',
            'title' => 'Quy tắc bỏ hai dấu chấm của ü (j, q, x, y)',
            'tag' => 'Quy tắc chính tả',
            'rule_summary' => 'Khi j, q, x, y đi với "ü": Bỏ 2 dấu chấm (viết ju, qu, xu, yu) nhưng VẪN PHÁT ÂM LÀ [ü].',
            'explanation' => 'Vì j, q, x không bao giờ kết hợp với nguyên âm "u" thông thường, nên để tiết kiệm và tiện viết chính tả, người ta bỏ 2 dấu chấm mà không sợ bị nhầm lẫn. Riêng n và l vì đi được với cả u và ü nên phải giữ nguyên: nü vs nu, lü vs lu.',
            'examples' => [
                ['orthography' => 'j + ü -> ju', 'pronunciation' => '[tɕy]', 'characters' => '句 (jù)', 'meaning' => 'câu văn (vẫn đọc tròn môi ü)'],
                ['orthography' => 'q + ü -> qu', 'pronunciation' => '[tɕʰy]', 'characters' => '去 (qù)', 'meaning' => 'đi (vẫn đọc tròn môi ü)'],
                ['orthography' => 'x + ü -> xu', 'pronunciation' => '[ɕy]', 'characters' => '雪 (xuě)', 'meaning' => 'tuyết (vẫn đọc tròn môi ü)'],
                ['orthography' => 'y + ü -> yu', 'pronunciation' => '[y]', 'characters' => '雨 (yǔ)', 'meaning' => 'mưa (vẫn đọc tròn môi ü)'],
            ],
        ],

        [
            'id' => 'tone_mark_position',
            'title' => 'Quy tắc đặt dấu thanh điệu trên nguyên âm',
            'tag' => 'Mẹo ghi nhớ chính tả',
            'rule_summary' => 'Ưu tiên số 1: Luôn đánh trên "a". Nếu không có "a", tìm "o" hoặc "e". Nếu "i" và "u" đi cùng nhau: chữ nào sau cùng thì đánh dấu chữ đó.',
            'explanation' => 'Khẩu quyết: "Có a thì đánh trên a / Không a tìm đến o, e / i, u nếu đứng liền nhau / Dấu thanh rơi ở phía sau cùng".',
            'examples' => [
                ['orthography' => 'hǎo', 'characters' => '好', 'meaning' => 'Có "a" nên đánh trên "a"'],
                ['orthography' => 'hěn', 'characters' => '很', 'meaning' => 'Không có "a", có "e" nên đánh trên "e"'],
                ['orthography' => 'xiǎo', 'characters' => '小', 'meaning' => 'Có "a" nên đánh trên "a" thay vì "i" hay "o"'],
                ['orthography' => 'liù', 'characters' => '六', 'meaning' => 'i đứng trước, u đứng sau -> đánh trên "u"'],
                ['orthography' => 'guì', 'characters' => '贵', 'meaning' => 'u đứng trước, i đứng sau -> đánh trên "i"'],
            ],
        ],
    ],
];
