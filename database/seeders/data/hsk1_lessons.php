<?php

return [
    [
        'slug' => 'pinyin-co-ban',
        'title' => 'Bài mở đầu: Pinyin cơ bản & Ngữ âm chuẩn HSK 1',
        'summary' => 'Làm quen toàn diện hệ thống Bính âm Hán ngữ: 21 Thanh mẫu, 36 Vận mẫu, 4 Thanh điệu, Quy tắc đánh dấu và Biến điệu quan trọng.',
        'hsk_level' => 1,
        'sort_order' => 0,
        'estimated_minutes' => 30,
        'accent_color' => '#991b1b',
        'difficulty' => 'starter',
        'content' => '
<div class="space-y-8">
    <div class="rounded-2xl bg-gradient-to-br from-rose-50 to-red-50/60 p-6 border border-rose-200/80">
        <h3 class="text-xl font-black text-rose-900 flex items-center gap-2 mb-3">
            <span class="text-2xl">🎯</span> 1. Bính âm Hán ngữ (Pinyin) là gì?
        </h3>
        <p class="text-slate-700 leading-relaxed text-base">
            <strong>Bính âm Hán ngữ (Hànyǔ Pīnyīn - 汉语拼音)</strong> là hệ thống phiên âm chữ Hán bằng chữ cái Latinh tiêu chuẩn quốc tế. Đây là chìa khóa vàng giúp bạn phát âm chuẩn xác mọi chữ Hán ngay từ bài học đầu tiên.
        </p>
        <div class="mt-4 p-4 bg-white rounded-xl border border-rose-100 shadow-xs">
            <p class="text-sm font-bold text-slate-800 uppercase tracking-wide mb-3">Cấu tạo của một âm tiết tiếng Trung:</p>
            <div class="flex flex-wrap items-center justify-center gap-3 py-2 text-center font-bold">
                <div class="px-4 py-2 bg-red-100 text-red-800 rounded-lg">Thanh mẫu (Phụ âm)<br><span class="text-xl font-mono text-red-600">m</span></div>
                <span class="text-2xl text-slate-400">+</span>
                <div class="px-4 py-2 bg-amber-100 text-amber-800 rounded-lg">Vận mẫu (Nguyên âm)<br><span class="text-xl font-mono text-amber-600">a</span></div>
                <span class="text-2xl text-slate-400">+</span>
                <div class="px-4 py-2 bg-emerald-100 text-emerald-800 rounded-lg">Thanh điệu (Dấu thanh)<br><span class="text-xl font-mono text-emerald-600">ˇ (thanh 3)</span></div>
                <span class="text-2xl text-slate-400">=</span>
                <div class="px-5 py-2 bg-slate-900 text-white rounded-lg">Âm đọc hoàn chỉnh<br><span class="text-2xl font-bold text-yellow-400">mǎ (马 - con ngựa)</span></div>
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-slate-50/60 p-6 space-y-4">
        <h3 class="text-xl font-black text-slate-900 flex items-center gap-2">
            <span class="text-2xl">🎵</span> 2. Bốn thanh điệu chuẩn trong tiếng Trung (四声)
        </h3>
        <p class="text-slate-700 text-sm leading-relaxed">
            Tiếng Trung có 4 thanh điệu chính và 1 thanh nhẹ (khinh thanh). Độ cao và hướng đi của giọng quyết định hoàn toàn ý nghĩa của từ:
        </p>
        <div class="grid gap-4 sm:grid-cols-2">
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="font-black text-red-600 text-lg">Thanh 1 (Âm bổng - 55)</span>
                    <span class="text-2xl font-mono font-bold text-slate-900">mā (妈)</span>
                </div>
                <p class="text-xs text-slate-500 mt-2">Cao độ 5-5. Phát âm cao và bằng phẳng, ngân dài đều (như hát nốt son): <em>māma (mẹ)</em>.</p>
            </div>
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="font-black text-amber-600 text-lg">Thanh 2 (Âm thăng - 35)</span>
                    <span class="text-2xl font-mono font-bold text-slate-900">má (麻)</span>
                </div>
                <p class="text-xs text-slate-500 mt-2">Cao độ 3-5. Giọng đi từ trung bình vút lên cao, tương tự dấu sắc tiếng Việt: <em>má (cây gai, tê)</em>.</p>
            </div>
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="font-black text-blue-600 text-lg">Thanh 3 (Âm khúc - 214)</span>
                    <span class="text-2xl font-mono font-bold text-slate-900">mǎ (马)</span>
                </div>
                <p class="text-xs text-slate-500 mt-2">Cao độ 2-1-4. Giọng hạ xuống mức thấp nhất rồi hơi nhấc lên, tương tự dấu hỏi: <em>mǎ (con ngựa)</em>.</p>
            </div>
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="font-black text-purple-600 text-lg">Thanh 4 (Âm giáng - 51)</span>
                    <span class="text-2xl font-mono font-bold text-slate-900">mà (骂)</span>
                </div>
                <p class="text-xs text-slate-500 mt-2">Cao độ 5-1. Từ độ cao nhất rơi nhanh, dứt khoát xuống mức thấp nhất (như ra lệnh): <em>mà (mắng mỏ)</em>.</p>
            </div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
            <span class="font-bold text-emerald-700">★ Thanh nhẹ (Khinh thanh):</span>
            <span class="text-sm text-slate-700 ml-2">Không đánh dấu trên đầu chữ cái, đọc ngắn, nhẹ và lướt qua (ví dụ chữ thứ 2 trong: <strong>māma 妈妈</strong>, <strong>bàba 爸爸</strong>, <strong>de 的</strong>, <strong>ma 吗</strong>).</span>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-slate-50/60 p-6 space-y-4">
        <h3 class="text-xl font-black text-slate-900 flex items-center gap-2">
            <span class="text-2xl">🗣️</span> 3. Hệ thống 21 Thanh mẫu (Phụ âm đầu)
        </h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 bg-white rounded-xl shadow-xs text-sm">
                <thead class="bg-slate-100 text-slate-700 font-bold">
                    <tr>
                        <th class="px-4 py-3 text-left">Nhóm âm</th>
                        <th class="px-4 py-3 text-left">Thanh mẫu</th>
                        <th class="px-4 py-3 text-left">Mẹo phát âm chuẩn</th>
                        <th class="px-4 py-3 text-left">Ví dụ thực tế</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    <tr>
                        <td class="px-4 py-3 font-semibold text-slate-900">Âm hai môi & môi răng</td>
                        <td class="px-4 py-3 font-mono font-bold text-red-600 text-base">b, p, m, f</td>
                        <td class="px-4 py-3"><strong>b</strong> đọc như "p" tiếng Việt; <strong>p</strong> bật hơi mạnh từ khoang miệng; <strong>m</strong> đọc như "m"; <strong>f</strong> như "ph".</td>
                        <td class="px-4 py-3 font-mono">bàba (bố), péngyou (bạn)</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 font-semibold text-slate-900">Âm đầu lưỡi giữa</td>
                        <td class="px-4 py-3 font-mono font-bold text-red-600 text-base">d, t, n, l</td>
                        <td class="px-4 py-3"><strong>d</strong> đọc như "t" tiếng Việt; <strong>t</strong> đọc như "th" bật hơi; <strong>n, l</strong> như tiếng Việt.</td>
                        <td class="px-4 py-3 font-mono">dà (lớn), tóngxué (bạn học)</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 font-semibold text-slate-900">Âm gốc lưỡi</td>
                        <td class="px-4 py-3 font-mono font-bold text-red-600 text-base">g, k, h</td>
                        <td class="px-4 py-3"><strong>g</strong> đọc như "c/k"; <strong>k</strong> đọc như "kh" bật hơi mạnh; <strong>h</strong> nằm giữa "h" và "kh" (hơi thở sâu).</td>
                        <td class="px-4 py-3 font-mono">gāo (cao), kàn (xem), hǎo (tốt)</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 font-semibold text-slate-900">Âm mặt lưỡi</td>
                        <td class="px-4 py-3 font-mono font-bold text-red-600 text-base">j, q, x</td>
                        <td class="px-4 py-3"><strong>j</strong> đọc như "chi" nhẹ; <strong>q</strong> đọc như "chi" bật hơi mạnh; <strong>x</strong> đọc như "xi" mặt lưỡi dẹt.</td>
                        <td class="px-4 py-3 font-mono">jiào (tên), qǐng (mời), xièxie (cảm ơn)</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 font-semibold text-slate-900">Âm uốn lưỡi (cuộn lưỡi)</td>
                        <td class="px-4 py-3 font-mono font-bold text-red-600 text-base">zh, ch, sh, r</td>
                        <td class="px-4 py-3">Đầu lưỡi uốn cong chạm ngạc cứng: <strong>zh</strong> đọc như "tr"; <strong>ch</strong> như "tr" bật hơi; <strong>sh</strong> như "s" uốn lưỡi; <strong>r</strong> rung nhẹ.</td>
                        <td class="px-4 py-3 font-mono">Zhōngguó (TQ), chī (ăn), shì (là), rén (người)</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 font-semibold text-slate-900">Âm đầu lưỡi trước</td>
                        <td class="px-4 py-3 font-mono font-bold text-red-600 text-base">z, c, s</td>
                        <td class="px-4 py-3">Đầu lưỡi thẳng chạm mặt sau răng trên: <strong>z</strong> đọc "ch/tz" dẹt miệng; <strong>c</strong> bật hơi ma sát mạnh; <strong>s</strong> đọc như "x" thẳng lưỡi.</td>
                        <td class="px-4 py-3 font-mono">zàijiàn (tạm biệt), cài (món ăn), sān (ba)</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="rounded-2xl border border-amber-200 bg-amber-50/70 p-6 space-y-3">
        <h3 class="text-xl font-black text-amber-900 flex items-center gap-2">
            <span class="text-2xl">💡</span> 4. Ba quy tắc biến điệu bắt buộc phải thuộc lòng
        </h3>
        <ul class="space-y-3 text-sm text-slate-800">
            <li class="p-3 bg-white rounded-xl border border-amber-200">
                <strong>1. Biến điệu hai thanh 3:</strong> Khi 2 thanh 3 đi liền nhau, thanh 3 thứ nhất đọc thành thanh 2:<br>
                <span class="font-mono text-emerald-700 font-bold">nǐ hǎo ➔ đọc là: ní hǎo</span> (nhưng pinyin viết vẫn giữ nǐ hǎo).
            </li>
            <li class="p-3 bg-white rounded-xl border border-amber-200">
                <strong>2. Biến điệu của chữ 不 (bù):</strong> Khi đứng trước một âm tiết mang thanh 4, chữ 不 đọc thành thanh 2 (bú):<br>
                <span class="font-mono text-emerald-700 font-bold">bù + shì ➔ bú shì (không phải)</span>, <span class="font-mono text-emerald-700 font-bold">bù + kèqi ➔ bú kèqi (đừng khách sáo)</span>.
            </li>
            <li class="p-3 bg-white rounded-xl border border-amber-200">
                <strong>3. Quy tắc bỏ dấu 2 chấm của chữ "ü":</strong> Khi kết hợp với <strong>j, q, x, y</strong>, chữ <strong>ü</strong> bỏ 2 chấm viết thành <strong>u</strong> nhưng vẫn đọc tròn môi là "ü":<br>
                <span class="font-mono text-emerald-700 font-bold">j + ü ➔ ju</span>, <span class="font-mono text-emerald-700 font-bold">q + ü ➔ qu</span>, <span class="font-mono text-emerald-700 font-bold">x + ü ➔ xu</span>, <span class="font-mono text-emerald-700 font-bold">y + ü ➔ yu</span>.
            </li>
        </ul>
    </div>
</div>',
        'flashcards' => [
            ['hanzi' => '妈妈', 'pinyin' => 'māma', 'meaning' => 'Mẹ (thanh 1 + thanh nhẹ)', 'example' => '我妈妈是老师。', 'example_pinyin' => 'Wǒ māma shì lǎoshī.', 'example_meaning' => 'Mẹ tôi là giáo viên.'],
            ['hanzi' => '爸爸', 'pinyin' => 'bàba', 'meaning' => 'Bố, ba (thanh 4 + thanh nhẹ)', 'example' => '我爸爸在医院工作。', 'example_pinyin' => 'Wǒ bàba zài yīyuàn gōngzuò.', 'example_meaning' => 'Bố tôi làm việc ở bệnh viện.'],
            ['hanzi' => '哥哥', 'pinyin' => 'gēge', 'meaning' => 'Anh trai', 'example' => '他是我哥哥。', 'example_pinyin' => 'Tā shì wǒ gēge.', 'example_meaning' => 'Anh ấy là anh trai tôi.'],
            ['hanzi' => '弟弟', 'pinyin' => 'dìdi', 'meaning' => 'Em trai', 'example' => '弟弟今年八岁。', 'example_pinyin' => 'Dìdi jīnnián bā suì.', 'example_meaning' => 'Em trai năm nay 8 tuổi.'],
            ['hanzi' => '妹妹', 'pinyin' => 'mèimei', 'meaning' => 'Em gái', 'example' => '妹妹很漂亮。', 'example_pinyin' => 'Mèimei hěn piàoliang.', 'example_meaning' => 'Em gái rất xinh đẹp.'],
            ['hanzi' => '你好', 'pinyin' => 'nǐ hǎo', 'meaning' => 'Xin chào (biến điệu 2 thanh 3: ní hǎo)', 'example' => '你好！很高兴认识你。', 'example_pinyin' => 'Nǐ hǎo! Hěn gāoxìng rènshi nǐ.', 'example_meaning' => 'Xin chào! Rất vui được quen bạn.'],
            ['hanzi' => '谢谢', 'pinyin' => 'xièxie', 'meaning' => 'Cảm ơn (thanh 4 + thanh nhẹ)', 'example' => '谢谢你的帮助。', 'example_pinyin' => 'Xièxie nǐ de bāngzhù.', 'example_meaning' => 'Cảm ơn sự giúp đỡ của bạn.'],
            ['hanzi' => '再见', 'pinyin' => 'zàijiàn', 'meaning' => 'Tạm biệt, hẹn gặp lại (hai thanh 4)', 'example' => '我们明天再见。', 'example_pinyin' => 'Wǒmen míngtiān zàijiàn.', 'example_meaning' => 'Ngày mai gặp lại nhé.'],
            ['hanzi' => '很好', 'pinyin' => 'hěn hǎo', 'meaning' => 'Rất tốt, rất khỏe (biến điệu: hén hǎo)', 'example' => '我很好，谢谢你。', 'example_pinyin' => 'Wǒ hěn hǎo, xièxie nǐ.', 'example_meaning' => 'Tôi rất khỏe, cảm ơn bạn.'],
            ['hanzi' => '不客气', 'pinyin' => 'bú kèqi', 'meaning' => 'Đừng khách sáo (biến điệu chữ 不: bú kèqi)', 'example' => '不客气，请坐。', 'example_pinyin' => 'Bú kèqi, qǐng zuò.', 'example_meaning' => 'Đừng khách sáo, mời ngồi.'],
        ],
        'questions' => [
            [
                'question' => 'Thanh 1 trong tiếng Trung có cao độ và đặc điểm phát âm như thế nào?',
                'pinyin' => 'shēngdiào',
                'options' => ['Cao độ 5-5, cao và bằng phẳng ngân đều', 'Cao độ 5-1, rơi nhanh dứt khoát', 'Cao độ 2-1-4, xuống rồi lên', 'Cao độ 3-5, vút lên cao'],
                'correct_answer' => 'Cao độ 5-5, cao và bằng phẳng ngân đều',
                'explanation' => 'Thanh 1 (âm bổng) có cao độ 5-5, phát âm cao ngang, bằng phẳng và kéo dài nhẹ (ví dụ: mā, sān).',
                'difficulty' => 'starter',
                'skill_type' => 'listening',
            ],
            [
                'question' => 'Khi hai thanh 3 đi liền nhau (ví dụ: 你好 nǐ hǎo), quy tắc biến điệu chuẩn là gì?',
                'pinyin' => 'nǐ hǎo biàndiào',
                'options' => ['Thanh 3 đầu đọc thành thanh 2 (ní hǎo)', 'Thanh 3 sau đọc thành thanh 1', 'Cả hai cùng đọc thành thanh 4', 'Giữ nguyên không thay đổi'],
                'correct_answer' => 'Thanh 3 đầu đọc thành thanh 2 (ní hǎo)',
                'explanation' => 'Khi 2 thanh 3 đi liền nhau, thanh 3 thứ nhất chuyển sang đọc thành thanh 2, nhưng khi viết pinyin vẫn giữ nguyên dấu.',
                'difficulty' => 'starter',
                'skill_type' => 'grammar',
            ],
            [
                'question' => 'Khi các phụ âm j, q, x, y kết hợp với nguyên âm "ü", chữ viết pinyin sẽ thay đổi như thế nào?',
                'pinyin' => 'j, q, x + ü',
                'options' => ['Bỏ dấu 2 chấm trên đầu chữ ü, viết thành u (ju, qu, xu, yu)', 'Giữ nguyên dấu 2 chấm', 'Đổi chữ ü thành chữ i', 'Thêm chữ e vào sau'],
                'correct_answer' => 'Bỏ dấu 2 chấm trên đầu chữ ü, viết thành u (ju, qu, xu, yu)',
                'explanation' => 'Quy tắc chính tả Pinyin: j, q, x, y khi đi với ü thì bỏ 2 chấm (ju, qu, xu, yu), nhưng vẫn phát âm tròn môi là ü.',
                'difficulty' => 'starter',
                'skill_type' => 'grammar',
            ],
            [
                'question' => 'Trong từ "妈妈" (māma - mẹ), chữ "ma" thứ hai mang thanh gì?',
                'pinyin' => 'qīngshēng',
                'options' => ['Thanh nhẹ (Khinh thanh), đọc ngắn và nhẹ', 'Thanh 1', 'Thanh 3', 'Thanh 4'],
                'correct_answer' => 'Thanh nhẹ (Khinh thanh), đọc ngắn và nhẹ',
                'explanation' => 'Chữ thứ hai trong các từ xưng hô lặp lại (māma, bàba, gēge, mèimei) đều mang thanh nhẹ (khinh thanh).',
                'difficulty' => 'starter',
                'skill_type' => 'listening',
            ],
            [
                'question' => 'Chữ "不" (bù) đọc thành thanh 2 (bú) khi nào?',
                'pinyin' => 'bú shì',
                'options' => ['Khi đứng trước âm tiết mang thanh 4 (như 不是 bú shì, 不客气 bú kèqi)', 'Khi đứng trước thanh 1', 'Khi đứng ở cuối câu', 'Khi đứng trước thanh 2'],
                'correct_answer' => 'Khi đứng trước âm tiết mang thanh 4 (như 不是 bú shì, 不客气 bú kèqi)',
                'explanation' => 'Chữ 不 (bù) mang thanh 4, khi đứng trước một âm tiết thanh 4 khác sẽ biến điệu thành thanh 2 (bú).',
                'difficulty' => 'starter',
                'skill_type' => 'grammar',
            ],
        ],
    ],
    [
        'slug' => 'chao-hoi-gioi-thieu',
        'title' => 'Bài thực hành: Chào hỏi & Tự giới thiệu bản thân',
        'summary' => 'Mẫu câu giao tiếp cơ bản: Chào hỏi các đối tượng, giới thiệu họ tên, quốc tịch, nghề nghiệp và hỏi thăm người khác.',
        'hsk_level' => 1,
        'sort_order' => 0,
        'estimated_minutes' => 25,
        'accent_color' => '#b45309',
        'difficulty' => 'starter',
        'content' => '
<div class="space-y-8">
    <div class="rounded-2xl bg-amber-50/70 p-6 border border-amber-200/80">
        <h3 class="text-lg font-black text-amber-900 flex items-center gap-2 mb-4">
            <span class="text-xl">💬</span> Bài khóa 1: Chào hỏi lần đầu gặp gỡ (初次见面)
        </h3>
        <div class="space-y-3">
            <div class="p-4 bg-white rounded-xl shadow-xs border border-amber-100">
                <p class="text-xl font-bold text-slate-900">A: 你好！</p>
                <p class="text-sm font-sans text-amber-700 font-medium">Nǐ hǎo!</p>
                <p class="text-sm text-slate-600 mt-1">Xin chào bạn!</p>
            </div>
            <div class="p-4 bg-white rounded-xl shadow-xs border border-amber-100">
                <p class="text-xl font-bold text-slate-900">B: 你好！很高兴认识你！</p>
                <p class="text-sm font-sans text-amber-700 font-medium">Nǐ hǎo! Hěn gāoxìng rènshi nǐ!</p>
                <p class="text-sm text-slate-600 mt-1">Xin chào! Rất vui được quen biết bạn!</p>
            </div>
            <div class="p-4 bg-white rounded-xl shadow-xs border border-amber-100">
                <p class="text-xl font-bold text-slate-900">A: 认识你我也很高兴！</p>
                <p class="text-sm font-sans text-amber-700 font-medium">Rènshi nǐ wǒ yě hěn gāoxìng!</p>
                <p class="text-sm text-slate-600 mt-1">Được quen bạn tôi cũng rất vui!</p>
            </div>
        </div>
    </div>

    <div class="rounded-2xl bg-orange-50/70 p-6 border border-orange-200/80">
        <h3 class="text-lg font-black text-orange-900 flex items-center gap-2 mb-4">
            <span class="text-xl">💬</span> Bài khóa 2: Hỏi tên, quốc tịch và nghề nghiệp (自我介绍)
        </h3>
        <div class="space-y-3">
            <div class="p-4 bg-white rounded-xl shadow-xs border border-orange-100">
                <p class="text-xl font-bold text-slate-900">A: 请问，你叫什么名字？</p>
                <p class="text-sm font-sans text-orange-700 font-medium">Qǐngwèn, nǐ jiào shénme míngzi?</p>
                <p class="text-sm text-slate-600 mt-1">Xin hỏi, bạn tên là gì?</p>
            </div>
            <div class="p-4 bg-white rounded-xl shadow-xs border border-orange-100">
                <p class="text-xl font-bold text-slate-900">B: 我叫 Minh。你是哪国人？</p>
                <p class="text-sm font-sans text-orange-700 font-medium">Wǒ jiào Minh. Nǐ shì nǎ guó rén?</p>
                <p class="text-sm text-slate-600 mt-1">Tôi tên là Minh. Bạn là người nước nào?</p>
            </div>
            <div class="p-4 bg-white rounded-xl shadow-xs border border-orange-100">
                <p class="text-xl font-bold text-slate-900">A: 我是中国人，我是汉语老师。你呢？</p>
                <p class="text-sm font-sans text-orange-700 font-medium">Wǒ shì Zhōngguó rén, wǒ shì Hànyǔ lǎoshī. Nǐ ne?</p>
                <p class="text-sm text-slate-600 mt-1">Tôi là người Trung Quốc, tôi là giáo viên tiếng Trung. Còn bạn thì sao?</p>
            </div>
            <div class="p-4 bg-white rounded-xl shadow-xs border border-orange-100">
                <p class="text-xl font-bold text-slate-900">B: 我是越南人，我是学生。</p>
                <p class="text-sm font-sans text-orange-700 font-medium">Wǒ shì Yuènán rén, wǒ shì xuésheng.</p>
                <p class="text-sm text-slate-600 mt-1">Tôi là người Việt Nam, tôi là học sinh sinh viên.</p>
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <h3 class="text-xl font-black text-slate-900 flex items-center gap-2">
            <span class="text-xl">📖</span> Ngữ pháp trọng điểm & Mẫu câu ứng dụng
        </h3>

        <div class="rounded-2xl border border-slate-200 bg-slate-50/60 p-6 space-y-3">
            <h4 class="font-bold text-slate-900 text-base">1. Mẫu câu tự giới thiệu tên mình</h4>
            <p class="text-slate-700 text-sm leading-relaxed">
                - Cấu trúc: <strong>我叫 + [Tên]</strong> (Tôi tên là...): <strong>我叫李月 (Tôi tên là Lý Nguyệt)</strong>.<br>
                - Cách hỏi tên người khác: <strong>你叫什么名字？ (Bạn tên là gì?)</strong>.
            </p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-slate-50/60 p-6 space-y-3">
            <h4 class="font-bold text-slate-900 text-base">2. Cấu trúc định danh với "是" (shì - là)</h4>
            <p class="text-slate-700 text-sm leading-relaxed">
                - Khẳng định: <strong>Chủ ngữ + 是 + Danh từ</strong>: <strong>我是越南人 (Tôi là người Việt Nam)</strong>.<br>
                - Phủ định: <strong>Chủ ngữ + 不是 + Danh từ</strong>: <strong>我不是老师 (Tôi không phải giáo viên)</strong>.<br>
                - Câu hỏi Yes/No: <strong>Chủ ngữ + 是 + Danh từ + 吗？</strong>: <strong>你是学生吗？ (Bạn là học sinh phải không?)</strong>.
            </p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-slate-50/60 p-6 space-y-3">
            <h4 class="font-bold text-slate-900 text-base">3. Câu hỏi tỉnh lược với "呢" (ne)</h4>
            <p class="text-slate-700 text-sm leading-relaxed">
                Dùng khi người nói muốn hỏi lại đối phương về nội dung vừa đề cập trước đó để tránh lặp lại nguyên câu:<br>
                <strong>我是学生，你呢？ (Tôi là học sinh, còn bạn thì sao?)</strong>.
            </p>
        </div>
    </div>
</div>',
        'flashcards' => [
            ['hanzi' => '你好', 'pinyin' => 'nǐ hǎo', 'meaning' => 'Xin chào (chào bạn bè, người ngang hàng)', 'example' => '你好，很高兴认识你。', 'example_pinyin' => 'Nǐ hǎo, hěn gāoxìng rènshi nǐ.', 'example_meaning' => 'Xin chào, rất vui được quen bạn.'],
            ['hanzi' => '您好', 'pinyin' => 'nín hǎo', 'meaning' => 'Xin chào (kính ngữ tôn trọng dành cho thầy cô, bề trên)', 'example' => '老师，您好！', 'example_pinyin' => 'Lǎoshī, nín hǎo!', 'example_meaning' => 'Em chào thầy ạ!'],
            ['hanzi' => '叫', 'pinyin' => 'jiào', 'meaning' => 'Gọi là, tên là', 'example' => '我叫王方。', 'example_pinyin' => 'Wǒ jiào Wáng Fāng.', 'example_meaning' => 'Tôi tên là Vương Phương.'],
            ['hanzi' => '什么', 'pinyin' => 'shénme', 'meaning' => 'Cái gì, gì (đại từ nghi vấn)', 'example' => '你叫什么名字？', 'example_pinyin' => 'Nǐ jiào shénme míngzi?', 'example_meaning' => 'Bạn tên là gì?'],
            ['hanzi' => '名字', 'pinyin' => 'míngzi', 'meaning' => 'Tên, họ tên', 'example' => '好名字。', 'example_pinyin' => 'Hǎo míngzi.', 'example_meaning' => 'Tên rất hay.'],
            ['hanzi' => '是', 'pinyin' => 'shì', 'meaning' => 'Là, phải, đúng', 'example' => '我是学生。', 'example_pinyin' => 'Wǒ shì xuésheng.', 'example_meaning' => 'Tôi là học sinh.'],
            ['hanzi' => '越南', 'pinyin' => 'Yuènán', 'meaning' => 'Việt Nam', 'example' => '我是越南人。', 'example_pinyin' => 'Wǒ shì Yuènán rén.', 'example_meaning' => 'Tôi là người Việt Nam.'],
            ['hanzi' => '中国', 'pinyin' => 'Zhōngguó', 'meaning' => 'Trung Quốc', 'example' => '中国很大。', 'example_pinyin' => 'Zhōngguó hěn dà.', 'example_meaning' => 'Trung Quốc rất rộng lớn.'],
            ['hanzi' => '老师', 'pinyin' => 'lǎoshī', 'meaning' => 'Giáo viên, thầy cô giáo', 'example' => '李老师是我的汉语老师。', 'example_pinyin' => 'Lǐ lǎoshī shì wǒ de Hànyǔ lǎoshī.', 'example_meaning' => 'Cô Lý là giáo viên tiếng Hán của tôi.'],
            ['hanzi' => '学生', 'pinyin' => 'xuésheng', 'meaning' => 'Học sinh, sinh viên', 'example' => '我们都是学生。', 'example_pinyin' => 'Wǒmen dōu shì xuésheng.', 'example_meaning' => 'Chúng tôi đều là học sinh.'],
            ['hanzi' => '高兴', 'pinyin' => 'gāoxìng', 'meaning' => 'Vui vẻ, hân hoan', 'example' => '今天我很高兴。', 'example_pinyin' => 'Jīntiān wǒ hěn gāoxìng.', 'example_meaning' => 'Hôm nay tôi rất vui.'],
            ['hanzi' => '认识', 'pinyin' => 'rènshi', 'meaning' => 'Quen biết, nhận biết', 'example' => '很高兴认识你！', 'example_pinyin' => 'Hěn gāoxìng rènshi nǐ!', 'example_meaning' => 'Rất vui được quen biết bạn!'],
        ],
        'questions' => [
            [
                'question' => 'Mẫu câu chuẩn dùng để hỏi tên đối phương trong tiếng Trung là gì?',
                'pinyin' => 'Nǐ jiào shénme míngzi?',
                'options' => ['你叫什么名字？', '你是哪国人？', '你是谁？', '你好吗？'],
                'correct_answer' => '你叫什么名字？',
                'explanation' => 'Cấu trúc hỏi tên thông dụng: 你叫什么名字？ (Bạn tên là gì?).',
                'difficulty' => 'starter',
                'skill_type' => 'grammar',
            ],
            [
                'question' => 'Để nói "Tôi là người Việt Nam", câu nói chuẩn xác là:',
                'pinyin' => 'Wǒ shì Yuènán rén',
                'options' => ['我是越南人。', '我叫越南人。', '我没有越南人。', '我是人越南。'],
                'correct_answer' => '我是越南人。',
                'explanation' => 'Cấu trúc nói quốc tịch: [Chủ ngữ] + 是 + [Tên quốc gia] + 人 -> 我是越南人。',
                'difficulty' => 'starter',
                'skill_type' => 'grammar',
            ],
            [
                'question' => 'Khi đối phương nói "很高兴认识你！", câu đáp lại lịch sự và tự nhiên nhất là gì?',
                'pinyin' => 'Rènshi nǐ wǒ yě hěn gāoxìng',
                'options' => ['认识你我也很高兴！', '对不起！', '不用谢！', '再见！'],
                'correct_answer' => '认识你我也很高兴！',
                'explanation' => 'Đáp lại "Rất vui được quen bạn" là "Được quen bạn tôi cũng rất vui" (认识你我也很高兴).',
                'difficulty' => 'starter',
                'skill_type' => 'vocabulary',
            ],
            [
                'question' => 'Dạng phủ định của câu "他是老师" (Anh ấy là giáo viên) là gì?',
                'pinyin' => 'Tā bú shì lǎoshī',
                'options' => ['他不是老师。', '他不老师。', '他没是老师。', '他没有老师。'],
                'correct_answer' => '他不是老师。',
                'explanation' => 'Phủ định của động từ "是" là "不是" (bú shì).',
                'difficulty' => 'starter',
                'skill_type' => 'grammar',
            ],
            [
                'question' => 'Từ "您" (nín) khác với "你" (nǐ) ở điểm nào?',
                'pinyin' => 'nín vs nǐ',
                'options' => ['"您" là dạng kính ngữ, thể hiện sự tôn trọng với người lớn tuổi, thầy cô', '"您" dùng cho trẻ em', '"您" là số nhiều của "你"', '"您" chỉ dùng khi tức giận'],
                'correct_answer' => '"您" là dạng kính ngữ, thể hiện sự tôn trọng với người lớn tuổi, thầy cô',
                'explanation' => '"您" (nín) chiết tự gồm chữ 你 ở trên và bộ Tâm 心 ở dưới, thể hiện sự tôn kính từ đáy lòng.',
                'difficulty' => 'starter',
                'skill_type' => 'vocabulary',
            ],
        ],
    ],
    [
        'slug' => 'hsk1-bai-01-ni-hao',
        'title' => 'Bài 1: 你好 - Xin chào',
        'summary' => 'Học các đại từ nhân xưng cơ bản, câu chào hỏi thông dụng và quy tắc biến điệu hai thanh 3.',
        'hsk_level' => 1,
        'sort_order' => 1,
        'estimated_minutes' => 25,
        'accent_color' => '#16a34a',
        'difficulty' => 'starter',
        'content' => '
<div class="space-y-8">
    <div class="rounded-2xl bg-emerald-50/70 p-6 border border-emerald-200/80">
        <h3 class="text-lg font-black text-emerald-900 flex items-center gap-2 mb-4">
            <span class="text-xl">💬</span> Bài khóa 1: Chào hỏi thông thường (在教室 - Trong lớp học)
        </h3>
        <div class="space-y-3">
            <div class="p-4 bg-white rounded-xl shadow-xs border border-emerald-100">
                <p class="text-xl font-bold text-slate-900">A: 你好！</p>
                <p class="text-sm font-sans text-emerald-700 font-medium">Nǐ hǎo!</p>
                <p class="text-sm text-slate-600 mt-1">Xin chào!</p>
            </div>
            <div class="p-4 bg-white rounded-xl shadow-xs border border-emerald-100">
                <p class="text-xl font-bold text-slate-900">B: 你好！</p>
                <p class="text-sm font-sans text-emerald-700 font-medium">Nǐ hǎo!</p>
                <p class="text-sm text-slate-600 mt-1">Xin chào!</p>
            </div>
        </div>
    </div>

    <div class="rounded-2xl bg-teal-50/70 p-6 border border-teal-200/80">
        <h3 class="text-lg font-black text-teal-900 flex items-center gap-2 mb-4">
            <span class="text-xl">💬</span> Bài khóa 2: Chào hỏi tôn kính và chào nhiều người
        </h3>
        <div class="space-y-3">
            <div class="p-4 bg-white rounded-xl shadow-xs border border-teal-100">
                <p class="text-xl font-bold text-slate-900">A: 您好！</p>
                <p class="text-sm font-sans text-teal-700 font-medium">Nín hǎo!</p>
                <p class="text-sm text-slate-600 mt-1">Xin chào ngài/thầy/cô! (Kính ngữ)</p>
            </div>
            <div class="p-4 bg-white rounded-xl shadow-xs border border-teal-100">
                <p class="text-xl font-bold text-slate-900">B: 你们好！</p>
                <p class="text-sm font-sans text-teal-700 font-medium">Nǐmen hǎo!</p>
                <p class="text-sm text-slate-600 mt-1">Chào các bạn!</p>
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <h3 class="text-xl font-black text-slate-900 flex items-center gap-2">
            <span class="text-xl">📖</span> Ngữ pháp trọng điểm (Grammar Notes)
        </h3>

        <div class="rounded-2xl border border-slate-200 bg-slate-50/60 p-6 space-y-3">
            <h4 class="font-bold text-slate-900 text-base">1. Đại từ nhân xưng trong tiếng Trung</h4>
            <p class="text-slate-700 text-sm leading-relaxed">
                Đại từ nhân xưng ngôi thứ nhất là <strong>我 (wǒ - tôi)</strong>, ngôi thứ hai là <strong>你 (nǐ - bạn)</strong> hoặc <strong>您 (nín - ngài, thể hiện sự kính trọng)</strong>, ngôi thứ ba là <strong>他 (tā - anh ấy)</strong> hoặc <strong>她 (tā - cô ấy)</strong>.
            </p>
            <p class="text-slate-700 text-sm leading-relaxed">
                Khi muốn biểu đạt số nhiều, thêm hậu tố <strong>们 (men)</strong> vào sau đại từ: <strong>我们 (wǒmen - chúng tôi)</strong>, <strong>你们 (nǐmen - các bạn)</strong>, <strong>他们 (tāmen - bọn họ)</strong>.
            </p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-slate-50/60 p-6 space-y-3">
            <h4 class="font-bold text-slate-900 text-base">2. Công thức chào hỏi cơ bản: [Đại từ] + 好</h4>
            <p class="text-slate-700 text-sm leading-relaxed">
                Để chào một người hoặc nhóm người, ghép đại từ xưng hô với chữ <strong>好 (hǎo)</strong>:
            </p>
            <ul class="list-disc list-inside text-sm text-slate-700 space-y-1.5 pl-2">
                <li><strong>你好 (Nǐ hǎo)</strong>: Chào bạn (dùng phổ biến nhất).</li>
                <li><strong>您好 (Nín hǎo)</strong>: Chào ngài/thầy cô (dành cho người lớn tuổi, bề trên).</li>
                <li><strong>你们好 (Nǐmen hǎo)</strong>: Chào các bạn (từ 2 người trở lên).</li>
                <li><strong>老师好 (Lǎoshī hǎo)</strong>: Em chào thầy/cô!</li>
            </ul>
        </div>

        <div class="rounded-2xl border border-amber-200 bg-amber-50/60 p-6 space-y-3">
            <h4 class="font-bold text-amber-900 text-base flex items-center gap-1.5">
                <span>💡</span> Quy tắc ngữ âm: Biến điệu hai thanh 3
            </h4>
            <p class="text-amber-950 text-sm leading-relaxed">
                Khi hai âm tiết mang thanh 3 đi liền nhau (như <strong>你 nǐ</strong> và <strong>好 hǎo</strong>), âm tiết thứ nhất sẽ biến điệu phát âm thành <strong>thanh 2</strong>:
            </p>
            <div class="bg-white p-3 rounded-xl border border-amber-200 text-sm font-medium text-amber-900">
                nǐ + hǎo ➔ phát âm là: <span class="font-bold text-red-600">ní hǎo</span> (nhưng chữ viết phiên âm pinyin vẫn giữ nguyên ký hiệu thanh 3: nǐ hǎo).
            </div>
        </div>
    </div>
</div>',
        'flashcards' => [
            ['hanzi' => '你', 'pinyin' => 'nǐ', 'meaning' => 'Bạn, anh, chị (ngôi thứ hai số ít)', 'example' => '你好！', 'example_pinyin' => 'Nǐ hǎo!', 'example_meaning' => 'Xin chào bạn!'],
            ['hanzi' => '好', 'pinyin' => 'hǎo', 'meaning' => 'Tốt, khỏe, hay, đẹp', 'example' => '很好。', 'example_pinyin' => 'Hěn hǎo.', 'example_meaning' => 'Rất tốt.'],
            ['hanzi' => '您', 'pinyin' => 'nín', 'meaning' => 'Ngài, ông, bà (kính ngữ của 你)', 'example' => '老师，您好！', 'example_pinyin' => 'Lǎoshī, nín hǎo!', 'example_meaning' => 'Em chào thầy ạ!'],
            ['hanzi' => '你们', 'pinyin' => 'nǐmen', 'meaning' => 'Các bạn, các anh, các chị (số nhiều)', 'example' => '你们好！', 'example_pinyin' => 'Nǐmen hǎo!', 'example_meaning' => 'Chào các bạn!'],
            ['hanzi' => '对不起', 'pinyin' => 'duìbuqǐ', 'meaning' => 'Xin lỗi', 'example' => '对不起，我来晚了。', 'example_pinyin' => 'Duìbuqǐ, wǒ lái wǎn le.', 'example_meaning' => 'Xin lỗi, tôi đến muộn.'],
            ['hanzi' => '没关系', 'pinyin' => 'méi guānxi', 'meaning' => 'Không sao đâu, không có gì', 'example' => '没关系，请进。', 'example_pinyin' => 'Méi guānxi, qǐng jìn.', 'example_meaning' => 'Không sao đâu, xin mời vào.'],
        ],
        'questions' => [
            [
                'question' => 'Khi chào một người lớn tuổi hoặc thầy cô giáo, ta nên dùng câu chào nào để thể hiện sự tôn trọng?',
                'pinyin' => 'Nín hǎo',
                'options' => ['您好！', '你好！', '你们好！', '对不起！'],
                'correct_answer' => '您好！',
                'explanation' => 'Chữ "您" (nín) là dạng tôn kính của "你", dùng để chào người lớn tuổi, cấp trên hoặc thầy cô.',
                'difficulty' => 'starter',
                'skill_type' => 'grammar',
            ],
            [
                'question' => 'Khi hai âm tiết thanh 3 đi liền nhau (ví dụ: 你好 nǐ hǎo), quy tắc phát âm biến điệu là gì?',
                'pinyin' => 'Biàndiào',
                'options' => ['Thanh 3 thứ nhất đọc thành thanh 2', 'Thanh 3 thứ hai đọc thành thanh 1', 'Cả hai đọc thành thanh 4', 'Giữ nguyên không thay đổi'],
                'correct_answer' => 'Thanh 3 thứ nhất đọc thành thanh 2',
                'explanation' => 'Khi 2 thanh 3 đi liền nhau, thanh 3 thứ nhất chuyển sang đọc thành thanh 2 (ní hǎo), pinyin viết vẫn giữ nguyên.',
                'difficulty' => 'starter',
                'skill_type' => 'listening',
            ],
            [
                'question' => 'Khi đối phương nói "对不起！" (Duìbuqǐ - Xin lỗi), bạn đáp lại như thế nào là lịch sự nhất?',
                'pinyin' => 'Méi guānxi',
                'options' => ['没关系！', '不用谢！', '您好！', '再见！'],
                'correct_answer' => '没关系！',
                'explanation' => '"没关系" (méi guānxi) có nghĩa là "Không sao đâu / Không có gì", là câu đáp lại chuẩn xác khi được xin lỗi.',
                'difficulty' => 'starter',
                'skill_type' => 'vocabulary',
            ],
            [
                'question' => 'Từ "你们" (nǐmen) trong tiếng Trung có ý nghĩa là gì?',
                'pinyin' => 'nǐmen',
                'options' => ['Các bạn (số nhiều)', 'Chúng tôi', 'Bọn họ', 'Một mình bạn'],
                'correct_answer' => 'Các bạn (số nhiều)',
                'explanation' => 'Hậu tố "们" (men) biểu thị số nhiều của đại từ nhân xưng: 你们 = các bạn.',
                'difficulty' => 'starter',
                'skill_type' => 'vocabulary',
            ],
        ],
    ],
    [
        'slug' => 'hsk1-bai-02-xie-xie-ni',
        'title' => 'Bài 2: 谢谢你 - Cảm ơn bạn',
        'summary' => 'Học cách nói cảm ơn, xin lỗi, cách đáp lại lịch sự và quy tắc biến điệu quan trọng của chữ 不.',
        'hsk_level' => 1,
        'sort_order' => 2,
        'estimated_minutes' => 25,
        'accent_color' => '#16a34a',
        'difficulty' => 'starter',
        'content' => '
<div class="space-y-8">
    <div class="rounded-2xl bg-emerald-50/70 p-6 border border-emerald-200/80">
        <h3 class="text-lg font-black text-emerald-900 flex items-center gap-2 mb-4">
            <span class="text-xl">💬</span> Bài khóa 1: Bày tỏ lời cảm ơn (谢谢)
        </h3>
        <div class="space-y-3">
            <div class="p-4 bg-white rounded-xl shadow-xs border border-emerald-100">
                <p class="text-xl font-bold text-slate-900">A: 谢谢！</p>
                <p class="text-sm font-sans text-emerald-700 font-medium">Xièxie!</p>
                <p class="text-sm text-slate-600 mt-1">Cảm ơn!</p>
            </div>
            <div class="p-4 bg-white rounded-xl shadow-xs border border-emerald-100">
                <p class="text-xl font-bold text-slate-900">B: 不客气！</p>
                <p class="text-sm font-sans text-emerald-700 font-medium">Bú kèqi!</p>
                <p class="text-sm text-slate-600 mt-1">Đừng khách sáo! (Không có chi!)</p>
            </div>
        </div>
    </div>

    <div class="rounded-2xl bg-teal-50/70 p-6 border border-teal-200/80">
        <h3 class="text-lg font-black text-teal-900 flex items-center gap-2 mb-4">
            <span class="text-xl">💬</span> Bài khóa 2: Tạm biệt (再见)
        </h3>
        <div class="space-y-3">
            <div class="p-4 bg-white rounded-xl shadow-xs border border-teal-100">
                <p class="text-xl font-bold text-slate-900">A: 再见！</p>
                <p class="text-sm font-sans text-teal-700 font-medium">Zàijiàn!</p>
                <p class="text-sm text-slate-600 mt-1">Tạm biệt!</p>
            </div>
            <div class="p-4 bg-white rounded-xl shadow-xs border border-teal-100">
                <p class="text-xl font-bold text-slate-900">B: 再见！</p>
                <p class="text-sm font-sans text-teal-700 font-medium">Zàijiàn!</p>
                <p class="text-sm text-slate-600 mt-1">Hẹn gặp lại!</p>
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <h3 class="text-xl font-black text-slate-900 flex items-center gap-2">
            <span class="text-xl">📖</span> Ngữ pháp trọng điểm (Grammar Notes)
        </h3>

        <div class="rounded-2xl border border-slate-200 bg-slate-50/60 p-6 space-y-3">
            <h4 class="font-bold text-slate-900 text-base">1. Cách cảm ơn và đáp lại</h4>
            <p class="text-slate-700 text-sm leading-relaxed">
                Khi nhận được sự giúp đỡ, ta nói <strong>谢谢 (Xièxie)</strong> hoặc <strong>谢谢你 (Xièxie nǐ)</strong>.
            </p>
            <p class="text-slate-700 text-sm leading-relaxed">
                Cách đáp lại lời cảm ơn:
            </p>
            <ul class="list-disc list-inside text-sm text-slate-700 space-y-1 pl-2">
                <li><strong>不客气 (Bú kèqi)</strong>: Đừng khách sáo / Không có gì (thông dụng nhất).</li>
                <li><strong>不用谢 (Bú yòng xiè)</strong>: Không cần cảm ơn đâu.</li>
            </ul>
        </div>

        <div class="rounded-2xl border border-amber-200 bg-amber-50/60 p-6 space-y-3">
            <h4 class="font-bold text-amber-900 text-base flex items-center gap-1.5">
                <span>💡</span> Biến điệu của chữ "不" (bù)
            </h4>
            <p class="text-amber-950 text-sm leading-relaxed">
                Chữ <strong>不</strong> phát âm gốc là thanh 4 (<strong>bù</strong>). Tuy nhiên:
            </p>
            <ul class="list-disc list-inside text-sm text-amber-900 space-y-1.5 pl-2">
                <li>Khi đứng trước một âm tiết mang <strong>thanh 4</strong>, chữ 不 biến điệu thành <strong>thanh 2 (bú)</strong>: ví dụ <strong>不客气 bú kèqi</strong>, <strong>不是 bú shì</strong>.</li>
                <li>Khi đứng trước các thanh 1, 2, 3, chữ 不 vẫn giữ nguyên <strong>thanh 4 (bù)</strong>: ví dụ <strong>不好 bù hǎo</strong>, <strong>不吃 bù chī</strong>.</li>
            </ul>
        </div>
    </div>
</div>',
        'flashcards' => [
            ['hanzi' => '谢谢', 'pinyin' => 'xièxie', 'meaning' => 'Cảm ơn', 'example' => '谢谢你的帮助。', 'example_pinyin' => 'Xièxie nǐ de bāngzhù.', 'example_meaning' => 'Cảm ơn sự giúp đỡ của bạn.'],
            ['hanzi' => '不客气', 'pinyin' => 'bú kèqi', 'meaning' => 'Đừng khách sáo, không có gì', 'example' => '不客气，这是我应该做的。', 'example_pinyin' => 'Bú kèqi, zhè shì wǒ yīnggāi zuò de.', 'example_meaning' => 'Đừng khách sáo, đây là việc tôi nên làm.'],
            ['hanzi' => '不', 'pinyin' => 'bù', 'meaning' => 'Không, chẳng (phủ định)', 'example' => '不好。', 'example_pinyin' => 'Bù hǎo.', 'example_meaning' => 'Không tốt.'],
            ['hanzi' => '再见', 'pinyin' => 'zàijiàn', 'meaning' => 'Tạm biệt, hẹn gặp lại', 'example' => '明天再见！', 'example_pinyin' => 'Míngtiān zàijiàn!', 'example_meaning' => 'Ngày mai gặp lại nhé!'],
        ],
        'questions' => [
            [
                'question' => 'Khi đối phương nói "谢谢你！", câu trả lời nào sau đây là chuẩn mực nhất?',
                'pinyin' => 'Bú kèqi',
                'options' => ['不客气！', '没关系！', '再见！', '很好！'],
                'correct_answer' => '不客气！',
                'explanation' => 'Khi được cảm ơn, dùng "不客气" (hoặc 不用谢) để đáp lại.',
                'difficulty' => 'starter',
                'skill_type' => 'vocabulary',
            ],
            [
                'question' => 'Chữ "不" đọc thành thanh 2 (bú) trong trường hợp nào sau đây?',
                'pinyin' => 'bù biàndiào',
                'options' => ['Khi đứng trước âm tiết mang thanh 4 (như 客 qi, 是 shì)', 'Khi đứng trước âm tiết mang thanh 1', 'Khi đứng ở cuối câu', 'Khi đứng trước thanh 3'],
                'correct_answer' => 'Khi đứng trước âm tiết mang thanh 4 (như 客 qi, 是 shì)',
                'explanation' => 'Chữ 不 (bù) khi đứng trước âm tiết thanh 4 sẽ biến điệu thành thanh 2 (bú kèqi, bú shì).',
                'difficulty' => 'starter',
                'skill_type' => 'grammar',
            ],
            [
                'question' => 'Từ "再见" (zàijiàn) có nghĩa gốc chiết tự là gì?',
                'pinyin' => 'zàijiàn',
                'options' => ['Lại gặp lại (Hẹn gặp lại)', 'Đi về cẩn thận', 'Chào buổi sáng', 'Chúc ngủ ngon'],
                'correct_answer' => 'Lại gặp lại (Hẹn gặp lại)',
                'explanation' => '再 (zài) là lại/nữa, 见 (jiàn) là gặp/nhìn thấy. 再见 nghĩa là hẹn gặp lại.',
                'difficulty' => 'starter',
                'skill_type' => 'vocabulary',
            ],
        ],
    ],
    [
        'slug' => 'hsk1-bai-03-ni-jiao-shen-me-ming-zi',
        'title' => 'Bài 3: 你叫什么名字 - Bạn tên là gì?',
        'summary' => 'Hỏi và trả lời về họ tên, làm quen, câu chữ "是" và cách đặt câu hỏi Có/Không với trợ từ "吗".',
        'hsk_level' => 1,
        'sort_order' => 3,
        'estimated_minutes' => 25,
        'accent_color' => '#16a34a',
        'difficulty' => 'starter',
        'content' => '
<div class="space-y-8">
    <div class="rounded-2xl bg-emerald-50/70 p-6 border border-emerald-200/80">
        <h3 class="text-lg font-black text-emerald-900 flex items-center gap-2 mb-4">
            <span class="text-xl">💬</span> Bài khóa 1: Hỏi tên bạn (在学校 - Ở trường học)
        </h3>
        <div class="space-y-3">
            <div class="p-4 bg-white rounded-xl shadow-xs border border-emerald-100">
                <p class="text-xl font-bold text-slate-900">A: 你叫什么名字？</p>
                <p class="text-sm font-sans text-emerald-700 font-medium">Nǐ jiào shénme míngzi?</p>
                <p class="text-sm text-slate-600 mt-1">Bạn tên là gì?</p>
            </div>
            <div class="p-4 bg-white rounded-xl shadow-xs border border-emerald-100">
                <p class="text-xl font-bold text-slate-900">B: 我叫李月。</p>
                <p class="text-sm font-sans text-emerald-700 font-medium">Wǒ jiào Lǐ Yuè.</p>
                <p class="text-sm text-slate-600 mt-1">Tôi tên là Lý Nguyệt.</p>
            </div>
        </div>
    </div>

    <div class="rounded-2xl bg-teal-50/70 p-6 border border-teal-200/80">
        <h3 class="text-lg font-black text-teal-900 flex items-center gap-2 mb-4">
            <span class="text-xl">💬</span> Bài khóa 2: Hỏi nghề nghiệp và quốc tịch
        </h3>
        <div class="space-y-3">
            <div class="p-4 bg-white rounded-xl shadow-xs border border-teal-100">
                <p class="text-xl font-bold text-slate-900">A: 你是老师吗？</p>
                <p class="text-sm font-sans text-teal-700 font-medium">Nǐ shì lǎoshī ma?</p>
                <p class="text-sm text-slate-600 mt-1">Bạn là giáo viên phải không?</p>
            </div>
            <div class="p-4 bg-white rounded-xl shadow-xs border border-teal-100">
                <p class="text-xl font-bold text-slate-900">B: 我不是老师，我是学生。</p>
                <p class="text-sm font-sans text-teal-700 font-medium">Wǒ bú shì lǎoshī, wǒ shì xuésheng.</p>
                <p class="text-sm text-slate-600 mt-1">Tôi không phải là giáo viên, tôi là học sinh.</p>
            </div>
            <div class="p-4 bg-white rounded-xl shadow-xs border border-teal-100">
                <p class="text-xl font-bold text-slate-900">A: 你是中国人吗？</p>
                <p class="text-sm font-sans text-teal-700 font-medium">Nǐ shì Zhōngguó rén ma?</p>
                <p class="text-sm text-slate-600 mt-1">Bạn là người Trung Quốc à?</p>
            </div>
            <div class="p-4 bg-white rounded-xl shadow-xs border border-teal-100">
                <p class="text-xl font-bold text-slate-900">B: 我不是中国人，我是美国人。</p>
                <p class="text-sm font-sans text-teal-700 font-medium">Wǒ bú shì Zhōngguó rén, wǒ shì Měiguó rén.</p>
                <p class="text-sm text-slate-600 mt-1">Tôi không phải người Trung Quốc, tôi là người Mỹ.</p>
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <h3 class="text-xl font-black text-slate-900 flex items-center gap-2">
            <span class="text-xl">📖</span> Ngữ pháp trọng điểm (Grammar Notes)
        </h3>

        <div class="rounded-2xl border border-slate-200 bg-slate-50/60 p-6 space-y-3">
            <h4 class="font-bold text-slate-900 text-base">1. Đại từ nghi vấn "什么" (shénme - cái gì)</h4>
            <p class="text-slate-700 text-sm leading-relaxed">
                Được dùng để hỏi vật hoặc thông tin: <strong>叫什么名字 (tên là gì)</strong>, <strong>什么书 (sách gì)</strong>.
            </p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-slate-50/60 p-6 space-y-3">
            <h4 class="font-bold text-slate-900 text-base">2. Câu chữ "是" (shì - là)</h4>
            <p class="text-slate-700 text-sm leading-relaxed">
                Dùng để xác định danh tính hoặc định danh: <strong>Chủ ngữ + 是 + Tân ngữ</strong>.<br>
                Phủ định thêm <strong>不</strong> trước 是 (đọc là <strong>bú shì</strong>): <strong>我不是老师 (Tôi không phải là giáo viên)</strong>.
            </p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-slate-50/60 p-6 space-y-3">
            <h4 class="font-bold text-slate-900 text-base">3. Câu hỏi Yes/No với trợ từ nghi vấn "吗" (ma)</h4>
            <p class="text-slate-700 text-sm leading-relaxed">
                Thêm <strong>吗</strong> vào cuối câu khẳng định để tạo câu hỏi: <strong>你是中国人吗？</strong> (Bạn là người Trung Quốc phải không?)
            </p>
        </div>
    </div>
</div>',
        'flashcards' => [
            ['hanzi' => '叫', 'pinyin' => 'jiào', 'meaning' => 'Kêu, gọi là, tên là', 'example' => '我叫王方。', 'example_pinyin' => 'Wǒ jiào Wáng Fāng.', 'example_meaning' => 'Tôi tên là Vương Phương.'],
            ['hanzi' => '什么', 'pinyin' => 'shénme', 'meaning' => 'Cái gì, gì', 'example' => '这是什么？', 'example_pinyin' => 'Zhè shì shénme?', 'example_meaning' => 'Đây là cái gì?'],
            ['hanzi' => '名字', 'pinyin' => 'míngzi', 'meaning' => 'Tên, họ tên', 'example' => '你的名字。', 'example_pinyin' => 'Nǐ de míngzi.', 'example_meaning' => 'Tên của bạn.'],
            ['hanzi' => '是', 'pinyin' => 'shì', 'meaning' => 'Là, phải, đúng', 'example' => '我是学生。', 'example_pinyin' => 'Wǒ shì xuésheng.', 'example_meaning' => 'Tôi là học sinh.'],
            ['hanzi' => '老师', 'pinyin' => 'lǎoshī', 'meaning' => 'Giáo viên, thầy cô giáo', 'example' => '他是汉语老师。', 'example_pinyin' => 'Tā shì Hànyǔ lǎoshī.', 'example_meaning' => 'Thầy ấy là giáo viên tiếng Trung.'],
            ['hanzi' => '学生', 'pinyin' => 'xuésheng', 'meaning' => 'Học sinh, sinh viên', 'example' => '我们都是学生。', 'example_pinyin' => 'Wǒmen dōu shì xuésheng.', 'example_meaning' => 'Chúng tôi đều là học sinh.'],
            ['hanzi' => '人', 'pinyin' => 'rén', 'meaning' => 'Người', 'example' => '中国人。', 'example_pinyin' => 'Zhōngguó rén.', 'example_meaning' => 'Người Trung Quốc.'],
            ['hanzi' => '中国', 'pinyin' => 'Zhōngguó', 'meaning' => 'Trung Quốc', 'example' => '我在中国。', 'example_pinyin' => 'Wǒ zài Zhōngguó.', 'example_meaning' => 'Tôi ở Trung Quốc.'],
            ['hanzi' => '美国', 'pinyin' => 'Měiguó', 'meaning' => 'Nước Mỹ, Hoa Kỳ', 'example' => '美国人。', 'example_pinyin' => 'Měiguó rén.', 'example_meaning' => 'Người Mỹ.'],
            ['hanzi' => '吗', 'pinyin' => 'ma', 'meaning' => 'Trợ từ nghi vấn (... không? ... à?)', 'example' => '你好吗？', 'example_pinyin' => 'Nǐ hǎo ma?', 'example_meaning' => 'Bạn có khỏe không?'],
        ],
        'questions' => [
            [
                'question' => 'Để hỏi "Bạn tên là gì?", ta dùng câu nào sau đây?',
                'pinyin' => 'Nǐ jiào shénme míngzi?',
                'options' => ['你叫什么名字？', '你是哪国人？', '他是谁？', '你好吗？'],
                'correct_answer' => '你叫什么名字？',
                'explanation' => 'Cấu trúc hỏi tên thông dụng nhất trong tiếng Trung là: 你叫什么名字？',
                'difficulty' => 'starter',
                'skill_type' => 'grammar',
            ],
            [
                'question' => 'Dạng phủ định của câu "我是学生" (Tôi là học sinh) là gì?',
                'pinyin' => 'Wǒ bú shì xuésheng',
                'options' => ['我不是学生。', '我不学生。', '我没有学生。', '我没是学生。'],
                'correct_answer' => '我不是学生。',
                'explanation' => 'Phủ định của động từ "是" luôn dùng phó từ "不" đứng trước -> 不是 (bú shì).',
                'difficulty' => 'starter',
                'skill_type' => 'grammar',
            ],
            [
                'question' => 'Để biến câu "她是老师" thành câu hỏi "Cô ấy là giáo viên phải không?", ta làm thế nào?',
                'pinyin' => 'ma',
                'options' => ['Thêm "吗" vào cuối câu: 她是老师吗？', 'Thêm "什么" vào đầu câu', 'Đổi "是" thành "不"', 'Bỏ chủ ngữ'],
                'correct_answer' => 'Thêm "吗" vào cuối câu: 她是老师吗？',
                'explanation' => 'Trợ từ nghi vấn "吗" đặt ở cuối câu trần thuật để biến câu đó thành câu hỏi Có/Không.',
                'difficulty' => 'starter',
                'skill_type' => 'grammar',
            ],
        ],
    ],
    [
        'slug' => 'hsk1-bai-04-ta-shi-wo-de-han-yu-lao-shi',
        'title' => 'Bài 4: 她是我的汉语老师 - Cô ấy là giáo viên tiếng Trung của tôi',
        'summary' => 'Học đại từ nghi vấn "谁" (ai), "哪" (nào), trợ từ kết cấu sở hữu "的" và câu hỏi tỉnh lược với "呢".',
        'hsk_level' => 1,
        'sort_order' => 4,
        'estimated_minutes' => 30,
        'accent_color' => '#16a34a',
        'difficulty' => 'starter',
        'content' => '
<div class="space-y-8">
    <div class="rounded-2xl bg-emerald-50/70 p-6 border border-emerald-200/80">
        <h3 class="text-lg font-black text-emerald-900 flex items-center gap-2 mb-4">
            <span class="text-xl">💬</span> Bài khóa 1: Cô ấy là ai? (在教室 - Trong lớp học)
        </h3>
        <div class="space-y-3">
            <div class="p-4 bg-white rounded-xl shadow-xs border border-emerald-100">
                <p class="text-xl font-bold text-slate-900">A: 她是谁？</p>
                <p class="text-sm font-sans text-emerald-700 font-medium">Tā shì shéi?</p>
                <p class="text-sm text-slate-600 mt-1">Cô ấy là ai?</p>
            </div>
            <div class="p-4 bg-white rounded-xl shadow-xs border border-emerald-100">
                <p class="text-xl font-bold text-slate-900">B: 她是我的汉语老师，她叫李月。</p>
                <p class="text-sm font-sans text-emerald-700 font-medium">Tā shì wǒ de Hànyǔ lǎoshī, tā jiào Lǐ Yuè.</p>
                <p class="text-sm text-slate-600 mt-1">Cô ấy là giáo viên tiếng Trung của tôi, cô ấy tên là Lý Nguyệt.</p>
            </div>
        </div>
    </div>

    <div class="rounded-2xl bg-teal-50/70 p-6 border border-teal-200/80">
        <h3 class="text-lg font-black text-teal-900 flex items-center gap-2 mb-4">
            <span class="text-xl">💬</span> Bài khóa 2: Bạn là người nước nào?
        </h3>
        <div class="space-y-3">
            <div class="p-4 bg-white rounded-xl shadow-xs border border-teal-100">
                <p class="text-xl font-bold text-slate-900">A: 你是哪国人？</p>
                <p class="text-sm font-sans text-teal-700 font-medium">Nǐ shì nǎ guó rén?</p>
                <p class="text-sm text-slate-600 mt-1">Bạn là người nước nào?</p>
            </div>
            <div class="p-4 bg-white rounded-xl shadow-xs border border-teal-100">
                <p class="text-xl font-bold text-slate-900">B: 我是美国人。你呢？</p>
                <p class="text-sm font-sans text-teal-700 font-medium">Wǒ shì Měiguó rén. Nǐ ne?</p>
                <p class="text-sm text-slate-600 mt-1">Tôi là người Mỹ. Còn bạn thì sao?</p>
            </div>
            <div class="p-4 bg-white rounded-xl shadow-xs border border-teal-100">
                <p class="text-xl font-bold text-slate-900">A: 我是中国人。</p>
                <p class="text-sm font-sans text-teal-700 font-medium">Wǒ shì Zhōngguó rén.</p>
                <p class="text-sm text-slate-600 mt-1">Tôi là người Trung Quốc.</p>
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <h3 class="text-xl font-black text-slate-900 flex items-center gap-2">
            <span class="text-xl">📖</span> Ngữ pháp trọng điểm (Grammar Notes)
        </h3>

        <div class="rounded-2xl border border-slate-200 bg-slate-50/60 p-6 space-y-3">
            <h4 class="font-bold text-slate-900 text-base">1. Trợ từ kết cấu "的" (de - của)</h4>
            <p class="text-slate-700 text-sm leading-relaxed">
                Dùng để biểu thị quan hệ sở hữu: <strong>Định ngữ (người sở hữu) + 的 + Danh từ trung tâm</strong>.<br>
                Ví dụ: <strong>我的汉语老师 (giáo viên tiếng Trung của tôi)</strong>, <strong>他的书 (sách của anh ấy)</strong>.
            </p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-slate-50/60 p-6 space-y-3">
            <h4 class="font-bold text-slate-900 text-base">2. Đại từ nghi vấn "谁" (shéi / shuí - ai) và "哪" (nǎ - nào)</h4>
            <p class="text-slate-700 text-sm leading-relaxed">
                - <strong>谁 (shéi)</strong>: dùng hỏi người: <strong>他是谁？</strong> (Anh ấy là ai?)<br>
                - <strong>哪 (nǎ)</strong>: dùng để hỏi lựa chọn: <strong>哪国人 (người nước nào)</strong>, <strong>哪本书 (cuốn sách nào)</strong>.
            </p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-slate-50/60 p-6 space-y-3">
            <h4 class="font-bold text-slate-900 text-base">3. Câu hỏi tỉnh lược với trợ từ "呢" (ne - còn... thì sao?)</h4>
            <p class="text-slate-700 text-sm leading-relaxed">
                Được đặt sau danh từ hoặc đại từ để hỏi lặp lại nội dung vừa đề cập: <strong>我很好，你呢？</strong> (Tôi rất khỏe, còn bạn thì sao?)
            </p>
        </div>
    </div>
</div>',
        'flashcards' => [
            ['hanzi' => '谁', 'pinyin' => 'shéi', 'meaning' => 'Ai', 'example' => '他是谁？', 'example_pinyin' => 'Tā shì shéi?', 'example_meaning' => 'Anh ấy là ai?'],
            ['hanzi' => '的', 'pinyin' => 'de', 'meaning' => 'Của (trợ từ kết cấu sở hữu)', 'example' => '我的老师。', 'example_pinyin' => 'Wǒ de lǎoshī.', 'example_meaning' => 'Thầy giáo của tôi.'],
            ['hanzi' => '汉语', 'pinyin' => 'Hànyǔ', 'meaning' => 'Tiếng Hán, tiếng Trung', 'example' => '我学汉语。', 'example_pinyin' => 'Wǒ xué Hànyǔ.', 'example_meaning' => 'Tôi học tiếng Hán.'],
            ['hanzi' => '哪', 'pinyin' => 'nǎ', 'meaning' => 'Nào, cái nào', 'example' => '你是哪国人？', 'example_pinyin' => 'Nǐ shì nǎ guó rén?', 'example_meaning' => 'Bạn là người nước nào?'],
            ['hanzi' => '国', 'pinyin' => 'guó', 'meaning' => 'Nước, quốc gia', 'example' => '国家。', 'example_pinyin' => 'Guójiā.', 'example_meaning' => 'Quốc gia.'],
            ['hanzi' => '呢', 'pinyin' => 'ne', 'meaning' => 'Còn... thì sao? (trợ từ nghi vấn tỉnh lược)', 'example' => '你呢？', 'example_pinyin' => 'Nǐ ne?', 'example_meaning' => 'Còn bạn thì sao?'],
            ['hanzi' => '同学', 'pinyin' => 'tóngxué', 'meaning' => 'Bạn cùng học, bạn cùng lớp', 'example' => '他是我的同学。', 'example_pinyin' => 'Tā shì wǒ de tóngxué.', 'example_meaning' => 'Cậu ấy là bạn cùng lớp của tôi.'],
            ['hanzi' => '朋友', 'pinyin' => 'péngyou', 'meaning' => 'Bạn bè', 'example' => '我们是好朋友。', 'example_pinyin' => 'Wǒmen shì hǎo péngyou.', 'example_meaning' => 'Chúng tôi là bạn tốt.'],
        ],
        'questions' => [
            [
                'question' => 'Để hỏi quốc tịch một người "Bạn là người nước nào?", câu nói chuẩn xác là gì?',
                'pinyin' => 'Nǐ shì nǎ guó rén?',
                'options' => ['你是哪国人？', '你叫什么名字？', '你是谁？', '你家在哪儿？'],
                'correct_answer' => '你是哪国人？',
                'explanation' => 'Cấu trúc hỏi quốc tịch: 你是哪国人？ (哪 = nào, 国 = quốc gia, 人 = người).',
                'difficulty' => 'starter',
                'skill_type' => 'grammar',
            ],
            [
                'question' => 'Trong câu "她是我的汉语老师", từ "的" đóng vai trò gì?',
                'pinyin' => 'de',
                'options' => ['Trợ từ kết cấu chỉ quan hệ sở hữu', 'Động từ chính', 'Trợ từ nghi vấn', 'Phó từ phủ định'],
                'correct_answer' => 'Trợ từ kết cấu chỉ quan hệ sở hữu',
                'explanation' => '"的" là trợ từ kết cấu sở hữu, đứng giữa định ngữ và danh từ trung tâm (người sở hữu + 的 + danh từ).',
                'difficulty' => 'starter',
                'skill_type' => 'grammar',
            ],
            [
                'question' => 'Khi một người nói "我是中国人。你呢？", từ "你呢" có nghĩa là gì?',
                'pinyin' => 'Nǐ ne?',
                'options' => ['Còn bạn thì sao?', 'Bạn tên gì?', 'Bạn đi đâu đấy?', 'Bạn có khỏe không?'],
                'correct_answer' => 'Còn bạn thì sao?',
                'explanation' => 'Cấu trúc [Danh từ/Đại từ] + 呢 dùng để hỏi lại thông tin vừa đề cập (câu hỏi tỉnh lược).',
                'difficulty' => 'starter',
                'skill_type' => 'vocabulary',
            ],
        ],
    ],
    [
        'slug' => 'hsk1-bai-05-ta-nv-er-jin-nian-er-shi-sui',
        'title' => 'Bài 5: 她女儿今年二十岁 - Con gái cô ấy năm nay 20 tuổi',
        'summary' => 'Học cách hỏi số lượng thành viên gia đình với lượng từ "口", hỏi tuổi tác bằng "几岁" và "多大", trợ từ "了".',
        'hsk_level' => 1,
        'sort_order' => 5,
        'estimated_minutes' => 30,
        'accent_color' => '#16a34a',
        'difficulty' => 'starter',
        'content' => '
<div class="space-y-8">
    <div class="rounded-2xl bg-emerald-50/70 p-6 border border-emerald-200/80">
        <h3 class="text-lg font-black text-emerald-900 flex items-center gap-2 mb-4">
            <span class="text-xl">💬</span> Bài khóa 1: Gia đình bạn có mấy người?
        </h3>
        <div class="space-y-3">
            <div class="p-4 bg-white rounded-xl shadow-xs border border-emerald-100">
                <p class="text-xl font-bold text-slate-900">A: 你家有几口人？</p>
                <p class="text-sm font-sans text-emerald-700 font-medium">Nǐ jiā yǒu jǐ kǒu rén?</p>
                <p class="text-sm text-slate-600 mt-1">Nhà bạn có mấy người?</p>
            </div>
            <div class="p-4 bg-white rounded-xl shadow-xs border border-emerald-100">
                <p class="text-xl font-bold text-slate-900">B: 我家有三口人。</p>
                <p class="text-sm font-sans text-emerald-700 font-medium">Wǒ jiā yǒu sān kǒu rén.</p>
                <p class="text-sm text-slate-600 mt-1">Nhà tôi có ba người.</p>
            </div>
        </div>
    </div>

    <div class="rounded-2xl bg-teal-50/70 p-6 border border-teal-200/80">
        <h3 class="text-lg font-black text-teal-900 flex items-center gap-2 mb-4">
            <span class="text-xl">💬</span> Bài khóa 2: Hỏi tuổi tác con cái và người lớn
        </h3>
        <div class="space-y-3">
            <div class="p-4 bg-white rounded-xl shadow-xs border border-teal-100">
                <p class="text-xl font-bold text-slate-900">A: 你女儿几岁了？</p>
                <p class="text-sm font-sans text-teal-700 font-medium">Nǐ nǚ\'ér jǐ suì le?</p>
                <p class="text-sm text-slate-600 mt-1">Con gái bạn mấy tuổi rồi? (Dưới 10 tuổi)</p>
            </div>
            <div class="p-4 bg-white rounded-xl shadow-xs border border-teal-100">
                <p class="text-xl font-bold text-slate-900">B: 她四岁了。</p>
                <p class="text-sm font-sans text-teal-700 font-medium">Tā sì suì le.</p>
                <p class="text-sm text-slate-600 mt-1">Cháu 4 tuổi rồi.</p>
            </div>
            <div class="p-4 bg-white rounded-xl shadow-xs border border-teal-100">
                <p class="text-xl font-bold text-slate-900">A: 李老师多大了？</p>
                <p class="text-sm font-sans text-teal-700 font-medium">Lǐ lǎoshī duō dà le?</p>
                <p class="text-sm text-slate-600 mt-1">Cô Lý bao nhiêu tuổi rồi?</p>
            </div>
            <div class="p-4 bg-white rounded-xl shadow-xs border border-teal-100">
                <p class="text-xl font-bold text-slate-900">B: 她今年五十岁了。她女儿今年二十岁。</p>
                <p class="text-sm font-sans text-teal-700 font-medium">Tā jīnnián wǔshí suì le. Tā nǚ\'ér jīnnián èrshí suì.</p>
                <p class="text-sm text-slate-600 mt-1">Năm nay cô ấy 50 tuổi rồi. Con gái cô ấy năm nay 20 tuổi.</p>
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <h3 class="text-xl font-black text-slate-900 flex items-center gap-2">
            <span class="text-xl">📖</span> Ngữ pháp trọng điểm (Grammar Notes)
        </h3>

        <div class="rounded-2xl border border-slate-200 bg-slate-50/60 p-6 space-y-3">
            <h4 class="font-bold text-slate-900 text-base">1. Đại từ nghi vấn "几" (jǐ - mấy) và lượng từ "口" (kǒu)</h4>
            <p class="text-slate-700 text-sm leading-relaxed">
                - <strong>几 (jǐ)</strong> dùng hỏi số lượng nhỏ dưới 10: <strong>几口人 (mấy người)</strong>, <strong>几岁 (mấy tuổi)</strong>.<br>
                - <strong>口 (kǒu)</strong> là lượng từ chuyên dùng để đếm số thành viên trong gia đình: <strong>三口人 (3 người)</strong>.
            </p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-slate-50/60 p-6 space-y-3">
            <h4 class="font-bold text-slate-900 text-base">2. Phân biệt cách hỏi tuổi: "几岁" vs "多大"</h4>
            <p class="text-slate-700 text-sm leading-relaxed">
                - Trẻ em (dưới 10 tuổi): dùng <strong>几岁 (jǐ suì)</strong>: <strong>你女儿几岁了？</strong><br>
                - Người lớn hoặc thanh thiếu niên: dùng <strong>多大 (duō dà)</strong>: <strong>你多大了？</strong> / <strong>李老师多大了？</strong>
            </p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-slate-50/60 p-6 space-y-3">
            <h4 class="font-bold text-slate-900 text-base">3. Trợ từ ngữ khí "了" (le) biểu thị sự thay đổi</h4>
            <p class="text-slate-700 text-sm leading-relaxed">
                Đặt ở cuối câu chỉ tuổi tác để thể hiện một tình huống mới hoặc tuổi mới đạt tới: <strong>她二十岁了 (Cô ấy đã 20 tuổi rồi)</strong>.
            </p>
        </div>
    </div>
</div>',
        'flashcards' => [
            ['hanzi' => '家', 'pinyin' => 'jiā', 'meaning' => 'Nhà, gia đình', 'example' => '我家很大。', 'example_pinyin' => 'Wǒ jiā hěn dà.', 'example_meaning' => 'Nhà tôi rất to.'],
            ['hanzi' => '有', 'pinyin' => 'yǒu', 'meaning' => 'Có', 'example' => '我有三本书。', 'example_pinyin' => 'Wǒ yǒu sān běn shū.', 'example_meaning' => 'Tôi có ba cuốn sách.'],
            ['hanzi' => '口', 'pinyin' => 'kǒu', 'meaning' => 'Miệng, lượng từ thành viên gia đình', 'example' => '四口人。', 'example_pinyin' => 'Sì kǒu rén.', 'example_meaning' => 'Bốn người trong gia đình.'],
            ['hanzi' => '女儿', 'pinyin' => 'nǚ\'ér', 'meaning' => 'Con gái', 'example' => '我女儿很漂亮。', 'example_pinyin' => 'Wǒ nǚ\'ér hěn piàoliang.', 'example_meaning' => 'Con gái tôi rất xinh đẹp.'],
            ['hanzi' => '几', 'pinyin' => 'jǐ', 'meaning' => 'Mấy (dưới 10)', 'example' => '几个人？', 'example_pinyin' => 'Jǐ gè rén?', 'example_meaning' => 'Mấy người?'],
            ['hanzi' => '岁', 'pinyin' => 'suì', 'meaning' => 'Tuổi', 'example' => '我二十岁。', 'example_pinyin' => 'Wǒ èrshí suì.', 'example_meaning' => 'Tôi hai mươi tuổi.'],
            ['hanzi' => '今年', 'pinyin' => 'jīnnián', 'meaning' => 'Năm nay', 'example' => '今年是2026年。', 'example_pinyin' => 'Jīnnián shì 2026 nián.', 'example_meaning' => 'Năm nay là năm 2026.'],
            ['hanzi' => '多大', 'pinyin' => 'duō dà', 'meaning' => 'Bao nhiêu tuổi (hỏi người lớn)', 'example' => '你多大了？', 'example_pinyin' => 'Nǐ duō dà le?', 'example_meaning' => 'Bạn bao nhiêu tuổi rồi?'],
        ],
        'questions' => [
            [
                'question' => 'Khi hỏi tuổi một em bé dưới 10 tuổi, câu nào chuẩn xác nhất?',
                'pinyin' => 'jǐ suì',
                'options' => ['你几岁了？', '你多大了？', '你是谁？', '你有几口人？'],
                'correct_answer' => '你几岁了？',
                'explanation' => 'Đối với trẻ em dưới 10 tuổi, người Trung Quốc sử dụng "几岁了？".',
                'difficulty' => 'starter',
                'skill_type' => 'grammar',
            ],
            [
                'question' => 'Lượng từ nào chuyên dùng để đếm số nhân khẩu/thành viên trong gia đình?',
                'pinyin' => 'kǒu',
                'options' => ['口 (kǒu)', '个 (gè)', '只 (zhī)', '本 (běn)'],
                'correct_answer' => '口 (kǒu)',
                'explanation' => 'Trong cấu trúc hỏi và nói về số thành viên gia đình, người ta dùng lượng từ "口" (ví dụ: 三口人).',
                'difficulty' => 'starter',
                'skill_type' => 'grammar',
            ],
            [
                'question' => 'Dạng phủ định của động từ "有" (có) là gì?',
                'pinyin' => 'méiyǒu',
                'options' => ['没有 (méiyǒu)', '不有 (bù yǒu)', '别有 (bié yǒu)', '无有 (wú yǒu)'],
                'correct_answer' => '没有 (méiyǒu)',
                'explanation' => 'Động từ 有 chỉ đi với phó từ phủ định 没 -> 没有 (không có), tuyệt đối không nói "不有".',
                'difficulty' => 'starter',
                'skill_type' => 'grammar',
            ],
        ],
    ],
    [
        'slug' => 'hsk1-bai-06-wo-hui-shuo-han-yu',
        'title' => 'Bài 6: 我会说汉语 - Tôi biết nói tiếng Trung',
        'summary' => 'Động từ năng nguyện "会" biểu thị kỹ năng qua rèn luyện, câu vị ngữ tính từ với "很" và cách dùng "怎么".',
        'hsk_level' => 1,
        'sort_order' => 6,
        'estimated_minutes' => 30,
        'accent_color' => '#16a34a',
        'difficulty' => 'starter',
        'content' => '
<div class="space-y-8">
    <div class="rounded-2xl bg-emerald-50/70 p-6 border border-emerald-200/80">
        <h3 class="text-lg font-black text-emerald-900 flex items-center gap-2 mb-4">
            <span class="text-xl">💬</span> Bài khóa 1: Bạn biết nói tiếng Trung không?
        </h3>
        <div class="space-y-3">
            <div class="p-4 bg-white rounded-xl shadow-xs border border-emerald-100">
                <p class="text-xl font-bold text-slate-900">A: 你会说汉语吗？</p>
                <p class="text-sm font-sans text-emerald-700 font-medium">Nǐ huì shuō Hànyǔ ma?</p>
                <p class="text-sm text-slate-600 mt-1">Bạn biết nói tiếng Trung không?</p>
            </div>
            <div class="p-4 bg-white rounded-xl shadow-xs border border-emerald-100">
                <p class="text-xl font-bold text-slate-900">B: 我会说汉语。</p>
                <p class="text-sm font-sans text-emerald-700 font-medium">Wǒ huì shuō Hànyǔ.</p>
                <p class="text-sm text-slate-600 mt-1">Tôi biết nói tiếng Trung.</p>
            </div>
            <div class="p-4 bg-white rounded-xl shadow-xs border border-emerald-100">
                <p class="text-xl font-bold text-slate-900">A: 你妈妈会说汉语吗？</p>
                <p class="text-sm font-sans text-emerald-700 font-medium">Nǐ māma huì shuō Hànyǔ ma?</p>
                <p class="text-sm text-slate-600 mt-1">Mẹ bạn biết nói tiếng Trung không?</p>
            </div>
            <div class="p-4 bg-white rounded-xl shadow-xs border border-emerald-100">
                <p class="text-xl font-bold text-slate-900">B: 她不会说。</p>
                <p class="text-sm font-sans text-emerald-700 font-medium">Tā bú huì shuō.</p>
                <p class="text-sm text-slate-600 mt-1">Mẹ tôi không biết nói.</p>
            </div>
        </div>
    </div>

    <div class="rounded-2xl bg-teal-50/70 p-6 border border-teal-200/80">
        <h3 class="text-lg font-black text-teal-900 flex items-center gap-2 mb-4">
            <span class="text-xl">💬</span> Bài khóa 2: Món ăn Trung Quốc & Viết chữ Hán
        </h3>
        <div class="space-y-3">
            <div class="p-4 bg-white rounded-xl shadow-xs border border-teal-100">
                <p class="text-xl font-bold text-slate-900">A: 中国菜好吃吗？</p>
                <p class="text-sm font-sans text-teal-700 font-medium">Zhōngguó cài hǎochī ma?</p>
                <p class="text-sm text-slate-600 mt-1">Món ăn Trung Quốc có ngon không?</p>
            </div>
            <div class="p-4 bg-white rounded-xl shadow-xs border border-teal-100">
                <p class="text-xl font-bold text-slate-900">B: 中国菜很好吃。你会做中国菜吗？</p>
                <p class="text-sm font-sans text-teal-700 font-medium">Zhōngguó cài hěn hǎochī. Nǐ huì zuò Zhōngguó cài ma?</p>
                <p class="text-sm text-slate-600 mt-1">Món ăn Trung Quốc rất ngon. Bạn biết nấu món Trung Quốc không?</p>
            </div>
            <div class="p-4 bg-white rounded-xl shadow-xs border border-teal-100">
                <p class="text-xl font-bold text-slate-900">A: 这个汉字怎么写？</p>
                <p class="text-sm font-sans text-teal-700 font-medium">Zhè ge Hànzì zěnme xiě?</p>
                <p class="text-sm text-slate-600 mt-1">Chữ Hán này viết như thế nào?</p>
            </div>
            <div class="p-4 bg-white rounded-xl shadow-xs border border-teal-100">
                <p class="text-xl font-bold text-slate-900">B: 对不起，这个字我会读，不会写。</p>
                <p class="text-sm font-sans text-teal-700 font-medium">Duìbuqǐ, zhè ge zì wǒ huì dú, bú huì xiě.</p>
                <p class="text-sm text-slate-600 mt-1">Xin lỗi, chữ này tôi biết đọc, không biết viết.</p>
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <h3 class="text-xl font-black text-slate-900 flex items-center gap-2">
            <span class="text-xl">📖</span> Ngữ pháp trọng điểm (Grammar Notes)
        </h3>

        <div class="rounded-2xl border border-slate-200 bg-slate-50/60 p-6 space-y-3">
            <h4 class="font-bold text-slate-900 text-base">1. Động từ năng nguyện "会" (huì - biết)</h4>
            <p class="text-slate-700 text-sm leading-relaxed">
                Biểu thị một khả năng đạt được thông qua học tập, rèn luyện: <strong>Chủ ngữ + 会 + Động từ</strong>.<br>
                Phủ định dùng <strong>不会 (bú huì)</strong>: <strong>我不会做中国菜 (Tôi không biết nấu món Trung Quốc)</strong>.
            </p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-slate-50/60 p-6 space-y-3">
            <h4 class="font-bold text-slate-900 text-base">2. Câu có vị ngữ là tính từ</h4>
            <p class="text-slate-700 text-sm leading-relaxed">
                Trong tiếng Trung, câu vị ngữ tính từ không dùng động từ 是 mà thường có phó từ <strong>很 (hěn)</strong> đứng trước tính từ: <strong>Chủ ngữ + 很 + Tính từ</strong>.<br>
                Ví dụ: <strong>中国菜很好吃</strong> (Món Trung Quốc ngon - từ 很 ở đây đóng vai trò liên kết ngữ pháp, không nhất thiết mang sắc thái quá mức).
            </p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-slate-50/60 p-6 space-y-3">
            <h4 class="font-bold text-slate-900 text-base">3. Đại từ nghi vấn "怎么" (zěnme - như thế nào)</h4>
            <p class="text-slate-700 text-sm leading-relaxed">
                Cấu trúc: <strong>怎么 + Động từ</strong> dùng để hỏi phương thức thực hiện hành động:<br>
                - <strong>怎么写？</strong> (Viết thế nào?)<br>
                - <strong>怎么读？</strong> (Đọc như thế nào?)<br>
                - <strong>怎么说？</strong> (Nói thế nào?)
            </p>
        </div>
    </div>
</div>',
        'flashcards' => [
            ['hanzi' => '会', 'pinyin' => 'huì', 'meaning' => 'Biết (thông qua học tập)', 'example' => '我会说汉语。', 'example_pinyin' => 'Wǒ huì shuō Hànyǔ.', 'example_meaning' => 'Tôi biết nói tiếng Hán.'],
            ['hanzi' => '说', 'pinyin' => 'shuō', 'meaning' => 'Nói', 'example' => '请你说。', 'example_pinyin' => 'Qǐng nǐ shuō.', 'example_meaning' => 'Xin mời bạn nói.'],
            ['hanzi' => '菜', 'pinyin' => 'cài', 'meaning' => 'Món ăn, rau', 'example' => '中国菜。', 'example_pinyin' => 'Zhōngguó cài.', 'example_meaning' => 'Món ăn Trung Quốc.'],
            ['hanzi' => '很', 'pinyin' => 'hěn', 'meaning' => 'Rất', 'example' => '很好。', 'example_pinyin' => 'Hěn hǎo.', 'example_meaning' => 'Rất tốt.'],
            ['hanzi' => '好吃', 'pinyin' => 'hǎochī', 'meaning' => 'Ngon miệng', 'example' => '米饭很好吃。', 'example_pinyin' => 'Mǐfàn hěn hǎochī.', 'example_meaning' => 'Cơm rất ngon.'],
            ['hanzi' => '做', 'pinyin' => 'zuò', 'meaning' => 'Làm, nấu', 'example' => '做菜。', 'example_pinyin' => 'Zuò cài.', 'example_meaning' => 'Nấu ăn.'],
            ['hanzi' => '写', 'pinyin' => 'xiě', 'meaning' => 'Viết', 'example' => '写汉字。', 'example_pinyin' => 'Xiě Hànzì.', 'example_meaning' => 'Viết chữ Hán.'],
            ['hanzi' => '汉字', 'pinyin' => 'Hànzì', 'meaning' => 'Chữ Hán', 'example' => '汉字很有意思。', 'example_pinyin' => 'Hànzì hěn yǒu yìsi.', 'example_meaning' => 'Chữ Hán rất thú vị.'],
            ['hanzi' => '字', 'pinyin' => 'zì', 'meaning' => 'Chữ, từ', 'example' => '这个字。', 'example_pinyin' => 'Zhè ge zì.', 'example_meaning' => 'Chữ này.'],
            ['hanzi' => '怎么', 'pinyin' => 'zěnme', 'meaning' => 'Làm sao, thế nào', 'example' => '怎么读？', 'example_pinyin' => 'Zěnme dú?', 'example_meaning' => 'Đọc như thế nào?'],
            ['hanzi' => '读', 'pinyin' => 'dú', 'meaning' => 'Đọc', 'example' => '读课文。', 'example_pinyin' => 'Dú kèwén.', 'example_meaning' => 'Đọc bài khóa.'],
        ],
        'questions' => [
            [
                'question' => 'Động từ năng nguyện "会" (huì) biểu thị điều gì?',
                'pinyin' => 'huì',
                'options' => ['Biết làm điều gì đó nhờ rèn luyện, học tập', 'Muốn làm điều gì đó', 'Có thể do điều kiện cho phép', 'Bắt buộc phải làm'],
                'correct_answer' => 'Biết làm điều gì đó nhờ rèn luyện, học tập',
                'explanation' => '"会" nhấn mạnh kỹ năng có được qua quá trình học tập (như nói ngoại ngữ, nấu ăn, lái xe).',
                'difficulty' => 'starter',
                'skill_type' => 'grammar',
            ],
            [
                'question' => 'Để hỏi "Chữ này đọc như thế nào?", câu tiếng Trung chuẩn xác là:',
                'pinyin' => 'Zhè ge zì zěnme dú?',
                'options' => ['这个字怎么读？', '这个字什么读？', '这个字谁读？', '这个字在哪儿读？'],
                'correct_answer' => '这个字怎么读？',
                'explanation' => 'Cấu trúc "怎么 + Động từ" dùng để hỏi cách thức tiến hành hành động (怎么读 = đọc thế nào).',
                'difficulty' => 'starter',
                'skill_type' => 'grammar',
            ],
        ],
    ],
    [
        'slug' => 'hsk1-bai-07-jin-tian-ji-hao',
        'title' => 'Bài 7: 今天几号 - Hôm nay ngày mấy?',
        'summary' => 'Học cách nói ngày, tháng, thứ trong tuần theo quy tắc từ lớn đến bé và câu liên động 去...做...',
        'hsk_level' => 1,
        'sort_order' => 7,
        'estimated_minutes' => 30,
        'accent_color' => '#16a34a',
        'difficulty' => 'starter',
        'content' => '
<div class="space-y-8">
    <div class="rounded-2xl bg-emerald-50/70 p-6 border border-emerald-200/80">
        <h3 class="text-lg font-black text-emerald-900 flex items-center gap-2 mb-4">
            <span class="text-xl">💬</span> Bài khóa 1: Hỏi ngày tháng (在看日历 - Xem lịch)
        </h3>
        <div class="space-y-3">
            <div class="p-4 bg-white rounded-xl shadow-xs border border-emerald-100">
                <p class="text-xl font-bold text-slate-900">A: 请问，今天几号？</p>
                <p class="text-sm font-sans text-emerald-700 font-medium">Qǐngwèn, jīntiān jǐ hào?</p>
                <p class="text-sm text-slate-600 mt-1">Xin hỏi, hôm nay ngày mấy?</p>
            </div>
            <div class="p-4 bg-white rounded-xl shadow-xs border border-emerald-100">
                <p class="text-xl font-bold text-slate-900">B: 今天9月1号。</p>
                <p class="text-sm font-sans text-emerald-700 font-medium">Jīntiān jiǔ yuè yī hào.</p>
                <p class="text-sm text-slate-600 mt-1">Hôm nay ngày 1 tháng 9.</p>
            </div>
            <div class="p-4 bg-white rounded-xl shadow-xs border border-emerald-100">
                <p class="text-xl font-bold text-slate-900">A: 今天星期几？</p>
                <p class="text-sm font-sans text-emerald-700 font-medium">Jīntiān xīngqī jǐ?</p>
                <p class="text-sm text-slate-600 mt-1">Hôm nay thứ mấy?</p>
            </div>
            <div class="p-4 bg-white rounded-xl shadow-xs border border-emerald-100">
                <p class="text-xl font-bold text-slate-900">B: 星期三。</p>
                <p class="text-sm font-sans text-emerald-700 font-medium">Xīngqīsān.</p>
                <p class="text-sm text-slate-600 mt-1">Thứ tư.</p>
            </div>
        </div>
    </div>

    <div class="rounded-2xl bg-teal-50/70 p-6 border border-teal-200/80">
        <h3 class="text-lg font-black text-teal-900 flex items-center gap-2 mb-4">
            <span class="text-xl">💬</span> Bài khóa 2: Kế hoạch ngày mai đi đâu làm gì
        </h3>
        <div class="space-y-3">
            <div class="p-4 bg-white rounded-xl shadow-xs border border-teal-100">
                <p class="text-xl font-bold text-slate-900">A: 明天是星期六，你去学校吗？</p>
                <p class="text-sm font-sans text-teal-700 font-medium">Míngtiān shì xīngqīliù, nǐ qù xuéxiào ma?</p>
                <p class="text-sm text-slate-600 mt-1">Ngày mai là thứ bảy, bạn có đến trường không?</p>
            </div>
            <div class="p-4 bg-white rounded-xl shadow-xs border border-teal-100">
                <p class="text-xl font-bold text-slate-900">B: 我去学校。</p>
                <p class="text-sm font-sans text-teal-700 font-medium">Wǒ qù xuéxiào.</p>
                <p class="text-sm text-slate-600 mt-1">Tôi đến trường.</p>
            </div>
            <div class="p-4 bg-white rounded-xl shadow-xs border border-teal-100">
                <p class="text-xl font-bold text-slate-900">A: 你去学校做什么？</p>
                <p class="text-sm font-sans text-teal-700 font-medium">Nǐ qù xuéxiào zuò shénme?</p>
                <p class="text-sm text-slate-600 mt-1">Bạn đến trường làm gì?</p>
            </div>
            <div class="p-4 bg-white rounded-xl shadow-xs border border-teal-100">
                <p class="text-xl font-bold text-slate-900">B: 我去学校看书。</p>
                <p class="text-sm font-sans text-teal-700 font-medium">Wǒ qù xuéxiào kàn shū.</p>
                <p class="text-sm text-slate-600 mt-1">Tôi đến trường đọc sách.</p>
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <h3 class="text-xl font-black text-slate-900 flex items-center gap-2">
            <span class="text-xl">📖</span> Ngữ pháp trọng điểm (Grammar Notes)
        </h3>

        <div class="rounded-2xl border border-slate-200 bg-slate-50/60 p-6 space-y-3">
            <h4 class="font-bold text-slate-900 text-base">1. Cách diễn đạt ngày tháng năm trong tiếng Trung</h4>
            <p class="text-slate-700 text-sm leading-relaxed">
                Quy tắc bất biến: <strong>Từ đơn vị lớn đến đơn vị nhỏ</strong>: Năm ➔ Tháng ➔ Ngày (号/日) ➔ Thứ.<br>
                Ví dụ: <strong>2026年9月1号，星期三</strong>.
            </p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-slate-50/60 p-6 space-y-3">
            <h4 class="font-bold text-slate-900 text-base">2. Câu liên động: [去 + Địa điểm + Làm gì]</h4>
            <p class="text-slate-700 text-sm leading-relaxed">
                Biểu thị mục đích đi đến một nơi nào đó để thực hiện một hành động: <strong>Chủ ngữ + 去 + Địa điểm + Động từ hành động</strong>.<br>
                Ví dụ: <strong>我去学校看书</strong> (Tôi đi đến trường đọc sách).
            </p>
        </div>
    </div>
</div>',
        'flashcards' => [
            ['hanzi' => '请', 'pinyin' => 'qǐng', 'meaning' => 'Xin, mời', 'example' => '请进。', 'example_pinyin' => 'Qǐng jìn.', 'example_meaning' => 'Xin mời vào.'],
            ['hanzi' => '问', 'pinyin' => 'wèn', 'meaning' => 'Hỏi', 'example' => '请问。', 'example_pinyin' => 'Qǐngwèn.', 'example_meaning' => 'Xin hỏi.'],
            ['hanzi' => '今天', 'pinyin' => 'jīntiān', 'meaning' => 'Hôm nay', 'example' => '今天星期几？', 'example_pinyin' => 'Jīntiān xīngqī jǐ?', 'example_meaning' => 'Hôm nay thứ mấy?'],
            ['hanzi' => '号', 'pinyin' => 'hào', 'meaning' => 'Ngày, mùng', 'example' => '5号。', 'example_pinyin' => 'Wǔ hào.', 'example_meaning' => 'Ngày mùng 5.'],
            ['hanzi' => '月', 'pinyin' => 'yuè', 'meaning' => 'Tháng, mặt trăng', 'example' => '十月。', 'example_pinyin' => 'Shí yuè.', 'example_meaning' => 'Tháng mười.'],
            ['hanzi' => '星期', 'pinyin' => 'xīngqī', 'meaning' => 'Tuần, thứ', 'example' => '星期日。', 'example_pinyin' => 'Xīngqīrì.', 'example_meaning' => 'Chủ nhật.'],
            ['hanzi' => '昨天', 'pinyin' => 'zuótiān', 'meaning' => 'Hôm qua', 'example' => '昨天很好。', 'example_pinyin' => 'Zuótiān hěn hǎo.', 'example_meaning' => 'Hôm qua rất tốt.'],
            ['hanzi' => '明天', 'pinyin' => 'míngtiān', 'meaning' => 'Ngày mai', 'example' => '明天见。', 'example_pinyin' => 'Míngtiān jiàn.', 'example_meaning' => 'Ngày mai gặp lại.'],
            ['hanzi' => '去', 'pinyin' => 'qù', 'meaning' => 'Đi', 'example' => '你去哪儿？', 'example_pinyin' => 'Nǐ qù nǎr?', 'example_meaning' => 'Bạn đi đâu đấy?'],
            ['hanzi' => '学校', 'pinyin' => 'xuéxiào', 'meaning' => 'Trường học', 'example' => '我们的学校。', 'example_pinyin' => 'Wǒmen de xuéxiào.', 'example_meaning' => 'Trường học của chúng tôi.'],
            ['hanzi' => '看', 'pinyin' => 'kàn', 'meaning' => 'Xem, nhìn, đọc', 'example' => '看电影。', 'example_pinyin' => 'Kàn diànyǐng.', 'example_meaning' => 'Xem phim.'],
            ['hanzi' => '书', 'pinyin' => 'shū', 'meaning' => 'Sách', 'example' => '汉语书。', 'example_pinyin' => 'Hànyǔ shū.', 'example_meaning' => 'Sách tiếng Hán.'],
        ],
        'questions' => [
            [
                'question' => 'Thứ tự sắp xếp ngày tháng năm chuẩn trong tiếng Trung là gì?',
                'pinyin' => 'nián yuè hào',
                'options' => ['Năm 年 -> Tháng 月 -> Ngày 号', 'Ngày 号 -> Tháng 月 -> Năm 年', 'Tháng 月 -> Ngày 号 -> Năm 年', 'Thứ -> Ngày -> Tháng'],
                'correct_answer' => 'Năm 年 -> Tháng 月 -> Ngày 号',
                'explanation' => 'Tiếng Trung luôn đi từ đơn vị thời gian lớn nhất đến nhỏ nhất: Năm (年) -> Tháng (月) -> Ngày (号/日).',
                'difficulty' => 'starter',
                'skill_type' => 'grammar',
            ],
            [
                'question' => '"星期三" trong tiếng Việt tương ứng với thứ mấy?',
                'pinyin' => 'Xīngqīsān',
                'options' => ['Thứ tư', 'Thứ ba', 'Thứ năm', 'Chủ nhật'],
                'correct_answer' => 'Thứ tư',
                'explanation' => 'Trong tiếng Trung: 星期一 = Thứ 2, 星期二 = Thứ 3, 星期三 = Thứ 4.',
                'difficulty' => 'starter',
                'skill_type' => 'vocabulary',
            ],
        ],
    ],
    [
        'slug' => 'hsk1-bai-08-wo-xiang-he-cha',
        'title' => 'Bài 8: 我想喝茶 - Tôi muốn uống trà',
        'summary' => 'Học động từ năng nguyện "想" (muốn), hỏi giá cả với "多少钱" và cấu trúc Số từ + Lượng từ + Danh từ.',
        'hsk_level' => 1,
        'sort_order' => 8,
        'estimated_minutes' => 30,
        'accent_color' => '#16a34a',
        'difficulty' => 'starter',
        'content' => '
<div class="space-y-8">
    <div class="rounded-2xl bg-emerald-50/70 p-6 border border-emerald-200/80">
        <h3 class="text-lg font-black text-emerald-900 flex items-center gap-2 mb-4">
            <span class="text-xl">💬</span> Bài khóa 1: Gọi đồ uống và đồ ăn (在餐馆 - Ở nhà hàng)
        </h3>
        <div class="space-y-3">
            <div class="p-4 bg-white rounded-xl shadow-xs border border-emerald-100">
                <p class="text-xl font-bold text-slate-900">A: 你想喝什么？</p>
                <p class="text-sm font-sans text-emerald-700 font-medium">Nǐ xiǎng hē shénme?</p>
                <p class="text-sm text-slate-600 mt-1">Bạn muốn uống gì?</p>
            </div>
            <div class="p-4 bg-white rounded-xl shadow-xs border border-emerald-100">
                <p class="text-xl font-bold text-slate-900">B: 我想喝茶。</p>
                <p class="text-sm font-sans text-emerald-700 font-medium">Wǒ xiǎng hē chá.</p>
                <p class="text-sm text-slate-600 mt-1">Tôi muốn uống trà.</p>
            </div>
            <div class="p-4 bg-white rounded-xl shadow-xs border border-emerald-100">
                <p class="text-xl font-bold text-slate-900">A: 你想吃什么？</p>
                <p class="text-sm font-sans text-emerald-700 font-medium">Nǐ xiǎng chī shénme?</p>
                <p class="text-sm text-slate-600 mt-1">Bạn muốn ăn gì?</p>
            </div>
            <div class="p-4 bg-white rounded-xl shadow-xs border border-emerald-100">
                <p class="text-xl font-bold text-slate-900">B: 我想吃米饭。</p>
                <p class="text-sm font-sans text-emerald-700 font-medium">Wǒ xiǎng chī mǐfàn.</p>
                <p class="text-sm text-slate-600 mt-1">Tôi muốn ăn cơm.</p>
            </div>
        </div>
    </div>

    <div class="rounded-2xl bg-teal-50/70 p-6 border border-teal-200/80">
        <h3 class="text-lg font-black text-teal-900 flex items-center gap-2 mb-4">
            <span class="text-xl">💬</span> Bài khóa 2: Mua sắm và hỏi giá tiền (在商店 - Ở cửa hàng)
        </h3>
        <div class="space-y-3">
            <div class="p-4 bg-white rounded-xl shadow-xs border border-teal-100">
                <p class="text-xl font-bold text-slate-900">A: 你好！这个杯子多少钱？</p>
                <p class="text-sm font-sans text-teal-700 font-medium">Nǐ hǎo! Zhè ge bēizi duōshao qián?</p>
                <p class="text-sm text-slate-600 mt-1">Xin chào! Cái cốc này bao nhiêu tiền?</p>
            </div>
            <div class="p-4 bg-white rounded-xl shadow-xs border border-teal-100">
                <p class="text-xl font-bold text-slate-900">B: 28块。</p>
                <p class="text-sm font-sans text-teal-700 font-medium">Èrshíbā kuài.</p>
                <p class="text-sm text-slate-600 mt-1">28 tệ (đồng).</p>
            </div>
            <div class="p-4 bg-white rounded-xl shadow-xs border border-teal-100">
                <p class="text-xl font-bold text-slate-900">A: 那个杯子多少钱？</p>
                <p class="text-sm font-sans text-teal-700 font-medium">Nà ge bēizi duōshao qián?</p>
                <p class="text-sm text-slate-600 mt-1">Cái cốc kia bao nhiêu tiền?</p>
            </div>
            <div class="p-4 bg-white rounded-xl shadow-xs border border-teal-100">
                <p class="text-xl font-bold text-slate-900">B: 那个杯子18块钱。</p>
                <p class="text-sm font-sans text-teal-700 font-medium">Nà ge bēizi shíbā kuài qián.</p>
                <p class="text-sm text-slate-600 mt-1">Cái cốc kia 18 tệ.</p>
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <h3 class="text-xl font-black text-slate-900 flex items-center gap-2">
            <span class="text-xl">📖</span> Ngữ pháp trọng điểm (Grammar Notes)
        </h3>

        <div class="rounded-2xl border border-slate-200 bg-slate-50/60 p-6 space-y-3">
            <h4 class="font-bold text-slate-900 text-base">1. Động từ năng nguyện "想" (xiǎng - muốn, dự định)</h4>
            <p class="text-slate-700 text-sm leading-relaxed">
                Cấu trúc: <strong>Chủ ngữ + 想 + Động từ</strong> biểu thị mong muốn hoặc kế hoạch làm việc gì đó: <strong>我想喝茶 (Tôi muốn uống trà)</strong>, <strong>我想去商店 (Tôi muốn đi cửa hàng)</strong>.
            </p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-slate-50/60 p-6 space-y-3">
            <h4 class="font-bold text-slate-900 text-base">2. Hỏi số lượng và giá cả với "多少" (duōshao - bao nhiêu)</h4>
            <p class="text-slate-700 text-sm leading-relaxed">
                - <strong>几 (jǐ)</strong> dùng hỏi số lượng nhỏ dưới 10.<br>
                - <strong>多少 (duōshao)</strong> dùng hỏi số lượng trên 10 hoặc hỏi giá cả: <strong>多少钱？ (Bao nhiêu tiền?)</strong>.
            </p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-slate-50/60 p-6 space-y-3">
            <h4 class="font-bold text-slate-900 text-base">3. Lượng từ và đơn vị tiền tệ</h4>
            <p class="text-slate-700 text-sm leading-relaxed">
                - Cấu trúc số lượng: <strong>Số từ + Lượng từ + Danh từ</strong>: <strong>一个杯子 (một cái cốc)</strong>.<br>
                - Đơn vị tiền tệ khẩu ngữ dùng <strong>块 (kuài)</strong> (văn viết dùng 元 yuán): <strong>28块 (28 đồng/tệ)</strong>.
            </p>
        </div>
    </div>
</div>',
        'flashcards' => [
            ['hanzi' => '想', 'pinyin' => 'xiǎng', 'meaning' => 'Muốn, nghĩ, nhớ', 'example' => '我想买车。', 'example_pinyin' => 'Wǒ xiǎng mǎi chē.', 'example_meaning' => 'Tôi muốn mua xe.'],
            ['hanzi' => '喝', 'pinyin' => 'hē', 'meaning' => 'Uống', 'example' => '喝水。', 'example_pinyin' => 'Hē shuǐ.', 'example_meaning' => 'Uống nước.'],
            ['hanzi' => '茶', 'pinyin' => 'chá', 'meaning' => 'Trà, chè', 'example' => '中国茶。', 'example_pinyin' => 'Zhōngguó chá.', 'example_meaning' => 'Trà Trung Quốc.'],
            ['hanzi' => '吃', 'pinyin' => 'chī', 'meaning' => 'Ăn', 'example' => '吃饭。', 'example_pinyin' => 'Chī fàn.', 'example_meaning' => 'Ăn cơm.'],
            ['hanzi' => '米饭', 'pinyin' => 'mǐfàn', 'meaning' => 'Cơm', 'example' => '一碗米饭。', 'example_pinyin' => 'Yī wǎn mǐfàn.', 'example_meaning' => 'Một bát cơm.'],
            ['hanzi' => '下午', 'pinyin' => 'xiàwǔ', 'meaning' => 'Buổi chiều', 'example' => '今天下午。', 'example_pinyin' => 'Jīntiān xiàwǔ.', 'example_meaning' => 'Chiều hôm nay.'],
            ['hanzi' => '商店', 'pinyin' => 'shāngdiàn', 'meaning' => 'Cửa hàng, tiệm', 'example' => '我去商店。', 'example_pinyin' => 'Wǒ qù shāngdiàn.', 'example_meaning' => 'Tôi đi cửa hàng.'],
            ['hanzi' => '买', 'pinyin' => 'mǎi', 'meaning' => 'Mua', 'example' => '买东西。', 'example_pinyin' => 'Mǎi dōngxi.', 'example_meaning' => 'Mua sắm đồ đạc.'],
            ['hanzi' => '个', 'pinyin' => 'gè', 'meaning' => 'Cái, chiếc (lượng từ phổ biến nhất)', 'example' => '一个人。', 'example_pinyin' => 'Yī gè rén.', 'example_meaning' => 'Một người.'],
            ['hanzi' => '杯子', 'pinyin' => 'bēizi', 'meaning' => 'Cái chén, ly, cốc', 'example' => '茶杯。', 'example_pinyin' => 'Chábēi.', 'example_meaning' => 'Chén trà.'],
            ['hanzi' => '多少', 'pinyin' => 'duōshao', 'meaning' => 'Bao nhiêu', 'example' => '多少人？', 'example_pinyin' => 'Duōshao rén?', 'example_meaning' => 'Bao nhiêu người?'],
            ['hanzi' => '钱', 'pinyin' => 'qián', 'meaning' => 'Tiền', 'example' => '多少钱？', 'example_pinyin' => 'Duōshao qián?', 'example_meaning' => 'Bao nhiêu tiền?'],
            ['hanzi' => '块', 'pinyin' => 'kuài', 'meaning' => 'Đồng, tệ (đơn vị tiền khẩu ngữ)', 'example' => '十块钱。', 'example_pinyin' => 'Shí kuài qián.', 'example_meaning' => '10 đồng (tệ).'],
        ],
        'questions' => [
            [
                'question' => 'Mẫu câu nào sau đây dùng để hỏi giá một món hàng?',
                'pinyin' => 'duōshao qián',
                'options' => ['这个杯子多少钱？', '这个杯子几钱？', '这个杯子什么钱？', '这个杯子是谁的？'],
                'correct_answer' => '这个杯子多少钱？',
                'explanation' => 'Hỏi giá tiền trong tiếng Trung dùng cụm "多少钱？".',
                'difficulty' => 'starter',
                'skill_type' => 'grammar',
            ],
            [
                'question' => 'Trong giao tiếp hàng ngày, đơn vị tiền tệ thường được nói là gì?',
                'pinyin' => 'kuài',
                'options' => ['块 (kuài)', '元 (yuán)', '分 (fēn)', '斤 (jīn)'],
                'correct_answer' => '块 (kuài)',
                'explanation' => 'Trong khẩu ngữ, người Trung Quốc thường dùng "块" (kuài) để nói về đơn vị tiền thay cho "元" (yuán).',
                'difficulty' => 'starter',
                'skill_type' => 'vocabulary',
            ],
        ],
    ],
    [
        'slug' => 'hsk1-bai-09-ni-er-zi-zai-nar-gong-zuo',
        'title' => 'Bài 9: 你儿子在哪儿工作 - Con trai bạn làm việc ở đâu?',
        'summary' => 'Giới từ "在" chỉ vị trí/nơi chốn, phương vị từ "上面/下面" và cách hỏi nơi làm việc.',
        'hsk_level' => 1,
        'sort_order' => 9,
        'estimated_minutes' => 30,
        'accent_color' => '#16a34a',
        'difficulty' => 'starter',
        'content' => '
<div class="space-y-8">
    <div class="rounded-2xl bg-emerald-50/70 p-6 border border-emerald-200/80">
        <h3 class="text-lg font-black text-emerald-900 flex items-center gap-2 mb-4">
            <span class="text-xl">💬</span> Bài khóa 1: Vị trí thú cưng (在家里 - Ở nhà)
        </h3>
        <div class="space-y-3">
            <div class="p-4 bg-white rounded-xl shadow-xs border border-emerald-100">
                <p class="text-xl font-bold text-slate-900">A: 小猫在哪儿？</p>
                <p class="text-sm font-sans text-emerald-700 font-medium">Xiǎo māo zài nǎr?</p>
                <p class="text-sm text-slate-600 mt-1">Con mèo con ở đâu thế?</p>
            </div>
            <div class="p-4 bg-white rounded-xl shadow-xs border border-emerald-100">
                <p class="text-xl font-bold text-slate-900">B: 小猫在那儿。小狗在椅子下面。</p>
                <p class="text-sm font-sans text-emerald-700 font-medium">Xiǎo māo zài nàr. Xiǎo gǒu zài yǐzi xiàmiàn.</p>
                <p class="text-sm text-slate-600 mt-1">Con mèo ở đằng kia. Con chó con ở dưới gầm ghế.</p>
            </div>
        </div>
    </div>

    <div class="rounded-2xl bg-teal-50/70 p-6 border border-teal-200/80">
        <h3 class="text-lg font-black text-teal-900 flex items-center gap-2 mb-4">
            <span class="text-xl">💬</span> Bài khóa 2: Con trai bạn làm việc ở đâu?
        </h3>
        <div class="space-y-3">
            <div class="p-4 bg-white rounded-xl shadow-xs border border-teal-100">
                <p class="text-xl font-bold text-slate-900">A: 你在哪儿工作？</p>
                <p class="text-sm font-sans text-teal-700 font-medium">Nǐ zài nǎr gōngzuò?</p>
                <p class="text-sm text-slate-600 mt-1">Bạn làm việc ở đâu?</p>
            </div>
            <div class="p-4 bg-white rounded-xl shadow-xs border border-teal-100">
                <p class="text-xl font-bold text-slate-900">B: 我在学校工作。你儿子在哪儿工作？</p>
                <p class="text-sm font-sans text-teal-700 font-medium">Wǒ zài xuéxiào gōngzuò. Nǐ érzi zài nǎr gōngzuò?</p>
                <p class="text-sm text-slate-600 mt-1">Tôi làm việc ở trường học. Con trai bạn làm việc ở đâu?</p>
            </div>
            <div class="p-4 bg-white rounded-xl shadow-xs border border-teal-100">
                <p class="text-xl font-bold text-slate-900">A: 我儿子在医院工作，他是医生。</p>
                <p class="text-sm font-sans text-teal-700 font-medium">Wǒ érzi zài yīyuàn gōngzuò, tā shì yīshēng.</p>
                <p class="text-sm text-slate-600 mt-1">Con trai tôi làm việc ở bệnh viện, nó là bác sĩ.</p>
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <h3 class="text-xl font-black text-slate-900 flex items-center gap-2">
            <span class="text-xl">📖</span> Ngữ pháp trọng điểm (Grammar Notes)
        </h3>

        <div class="rounded-2xl border border-slate-200 bg-slate-50/60 p-6 space-y-3">
            <h4 class="font-bold text-slate-900 text-base">1. Động từ "在" (zài - ở, tại)</h4>
            <p class="text-slate-700 text-sm leading-relaxed">
                Biểu thị sự tồn tại ở một vị trí: <strong>Chủ ngữ + 在 + Nơi chốn</strong>: <strong>小狗在椅子下面 (Chó con ở dưới ghế)</strong>.
            </p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-slate-50/60 p-6 space-y-3">
            <h4 class="font-bold text-slate-900 text-base">2. Cấu trúc giới từ: [在 + Nơi chốn + Làm gì]</h4>
            <p class="text-slate-700 text-sm leading-relaxed">
                Trong tiếng Trung, trạng ngữ chỉ nơi chốn đứng TRƯỚC động từ hành động:<br>
                <strong>Chủ ngữ + 在 + Nơi chốn + Động từ</strong>.<br>
                Ví dụ: <strong>我在学校工作 (Tôi làm việc ở trường)</strong>, KHÔNG nói "Tôi làm việc ở trường" theo trật tự tiếng Việt.
            </p>
        </div>
    </div>
</div>',
        'flashcards' => [
            ['hanzi' => '小', 'pinyin' => 'xiǎo', 'meaning' => 'Nhỏ, bé', 'example' => '小狗。', 'example_pinyin' => 'Xiǎo gǒu.', 'example_meaning' => 'Con chó nhỏ.'],
            ['hanzi' => '猫', 'pinyin' => 'māo', 'meaning' => 'Mèo', 'example' => '一只猫。', 'example_pinyin' => 'Yī zhī māo.', 'example_meaning' => 'Một con mèo.'],
            ['hanzi' => '在', 'pinyin' => 'zài', 'meaning' => 'Ở, tại', 'example' => '我在家。', 'example_pinyin' => 'Wǒ zài jiā.', 'example_meaning' => 'Tôi ở nhà.'],
            ['hanzi' => '那儿', 'pinyin' => 'nàr', 'meaning' => 'Ở đằng kia, ở đó', 'example' => '他在那儿。', 'example_pinyin' => 'Tā zài nàr.', 'example_meaning' => 'Anh ấy ở đằng kia.'],
            ['hanzi' => '狗', 'pinyin' => 'gǒu', 'meaning' => 'Chó', 'example' => '我家有一只狗。', 'example_pinyin' => 'Wǒ jiā yǒu yī zhī gǒu.', 'example_meaning' => 'Nhà tôi có một con chó.'],
            ['hanzi' => '椅子', 'pinyin' => 'yǐzi', 'meaning' => 'Cái ghế', 'example' => '一把椅子。', 'example_pinyin' => 'Yī bǎ yǐzi.', 'example_meaning' => 'Một cái ghế.'],
            ['hanzi' => '下面', 'pinyin' => 'xiàmiàn', 'meaning' => 'Phía dưới, bên dưới', 'example' => '桌子下面。', 'example_pinyin' => 'Zhuōzi xiàmiàn.', 'example_meaning' => 'Dưới gầm bàn.'],
            ['hanzi' => '哪儿', 'pinyin' => 'nǎr', 'meaning' => 'Ở đâu', 'example' => '你在哪儿？', 'example_pinyin' => 'Nǐ zài nǎr?', 'example_meaning' => 'Bạn ở đâu?'],
            ['hanzi' => '工作', 'pinyin' => 'gōngzuò', 'meaning' => 'Làm việc, công việc', 'example' => '我爱工作。', 'example_pinyin' => 'Wǒ ài gōngzuò.', 'example_meaning' => 'Tôi yêu công việc.'],
            ['hanzi' => '儿子', 'pinyin' => 'érzi', 'meaning' => 'Con trai', 'example' => '他儿子十岁。', 'example_pinyin' => 'Tā érzi shí suì.', 'example_meaning' => 'Con trai anh ấy 10 tuổi.'],
            ['hanzi' => '医院', 'pinyin' => 'yīyuàn', 'meaning' => 'Bệnh viện', 'example' => '大医院。', 'example_pinyin' => 'Dà yīyuàn.', 'example_meaning' => 'Bệnh viện lớn.'],
            ['hanzi' => '医生', 'pinyin' => 'yīshēng', 'meaning' => 'Bác sĩ', 'example' => '我爸爸是医生。', 'example_pinyin' => 'Wǒ bàba shì yīshēng.', 'example_meaning' => 'Bố tôi là bác sĩ.'],
            ['hanzi' => '爸爸', 'pinyin' => 'bàba', 'meaning' => 'Bố, cha', 'example' => '爸爸妈妈。', 'example_pinyin' => 'Bàba māma.', 'example_meaning' => 'Bố mẹ.'],
        ],
        'questions' => [
            [
                'question' => 'Trật tự câu nào sau đây là chuẩn ngữ pháp tiếng Trung khi diễn đạt "Tôi làm việc ở bệnh viện"?',
                'pinyin' => 'Wǒ zài yīyuàn gōngzuò',
                'options' => ['我在医院工作。', '我工作在医院。', '在医院我做工作。', '我医院在工作。'],
                'correct_answer' => '我在医院工作。',
                'explanation' => 'Ngữ pháp tiếng Trung quy định trạng ngữ chỉ nơi chốn đứng trước động từ: [Chủ ngữ] + [在 + Nơi chốn] + [Động từ].',
                'difficulty' => 'starter',
                'skill_type' => 'grammar',
            ],
            [
                'question' => '"椅子下面" có nghĩa là gì?',
                'pinyin' => 'yǐzi xiàmiàn',
                'options' => ['Dưới cái ghế', 'Trên cái ghế', 'Trước cái ghế', 'Sau cái ghế'],
                'correct_answer' => 'Dưới cái ghế',
                'explanation' => '椅子 (yǐzi) = cái ghế, 下面 (xiàmiàn) = phía dưới.',
                'difficulty' => 'starter',
                'skill_type' => 'vocabulary',
            ],
        ],
    ],
    [
        'slug' => 'hsk1-bai-10-wo-neng-zuo-zhe-er-ma',
        'title' => 'Bài 10: 我能坐这儿吗 - Tôi có thể ngồi đây không?',
        'summary' => 'Động từ năng nguyện "能" (có thể), câu tồn hiện vị trí với "桌子上" và liên từ "和".',
        'hsk_level' => 1,
        'sort_order' => 10,
        'estimated_minutes' => 30,
        'accent_color' => '#16a34a',
        'difficulty' => 'starter',
        'content' => '
<div class="space-y-8">
    <div class="rounded-2xl bg-emerald-50/70 p-6 border border-emerald-200/80">
        <h3 class="text-lg font-black text-emerald-900 flex items-center gap-2 mb-4">
            <span class="text-xl">💬</span> Bài khóa 1: Đồ vật trên bàn (在教室 - Trong lớp học)
        </h3>
        <div class="space-y-3">
            <div class="p-4 bg-white rounded-xl shadow-xs border border-emerald-100">
                <p class="text-xl font-bold text-slate-900">A: 桌子上有什么？</p>
                <p class="text-sm font-sans text-emerald-700 font-medium">Zhuōzi shang yǒu shénme?</p>
                <p class="text-sm text-slate-600 mt-1">Trên bàn có cái gì?</p>
            </div>
            <div class="p-4 bg-white rounded-xl shadow-xs border border-emerald-100">
                <p class="text-xl font-bold text-slate-900">B: 桌子上有一个电脑和一本书。</p>
                <p class="text-sm font-sans text-emerald-700 font-medium">Zhuōzi shang yǒu yí gè diànnǎo hé yì běn shū.</p>
                <p class="text-sm text-slate-600 mt-1">Trên bàn có một chiếc máy vi tính và một cuốn sách.</p>
            </div>
        </div>
    </div>

    <div class="rounded-2xl bg-teal-50/70 p-6 border border-teal-200/80">
        <h3 class="text-lg font-black text-teal-900 flex items-center gap-2 mb-4">
            <span class="text-xl">💬</span> Bài khóa 2: Xin phép ngồi (在图书馆 - Ở thư viện)
        </h3>
        <div class="space-y-3">
            <div class="p-4 bg-white rounded-xl shadow-xs border border-teal-100">
                <p class="text-xl font-bold text-slate-900">A: 这儿有人吗？</p>
                <p class="text-sm font-sans text-teal-700 font-medium">Zhèr yǒu rén ma?</p>
                <p class="text-sm text-slate-600 mt-1">Chỗ này có ai ngồi chưa?</p>
            </div>
            <div class="p-4 bg-white rounded-xl shadow-xs border border-teal-100">
                <p class="text-xl font-bold text-slate-900">B: 没有。</p>
                <p class="text-sm font-sans text-teal-700 font-medium">Méiyǒu.</p>
                <p class="text-sm text-slate-600 mt-1">Không có.</p>
            </div>
            <div class="p-4 bg-white rounded-xl shadow-xs border border-teal-100">
                <p class="text-xl font-bold text-slate-900">A: 我能坐这儿吗？</p>
                <p class="text-sm font-sans text-teal-700 font-medium">Wǒ néng zuò zhèr ma?</p>
                <p class="text-sm text-slate-600 mt-1">Tôi có thể ngồi ở đây không?</p>
            </div>
            <div class="p-4 bg-white rounded-xl shadow-xs border border-teal-100">
                <p class="text-xl font-bold text-slate-900">B: 请坐。</p>
                <p class="text-sm font-sans text-teal-700 font-medium">Qǐng zuò.</p>
                <p class="text-sm text-slate-600 mt-1">Xin mời ngồi.</p>
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <h3 class="text-xl font-black text-slate-900 flex items-center gap-2">
            <span class="text-xl">📖</span> Ngữ pháp trọng điểm (Grammar Notes)
        </h3>

        <div class="rounded-2xl border border-slate-200 bg-slate-50/60 p-6 space-y-3">
            <h4 class="font-bold text-slate-900 text-base">1. Câu tồn hiện vị trí: [Nơi chốn + 有 + Danh từ]</h4>
            <p class="text-slate-700 text-sm leading-relaxed">
                Biểu đạt tại một địa điểm tồn tại người hoặc vật nào đó: <strong>桌子上有一个电脑</strong> (Trên bàn có một chiếc máy tính).
            </p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-slate-50/60 p-6 space-y-3">
            <h4 class="font-bold text-slate-900 text-base">2. Động từ năng nguyện "能" (néng - có thể)</h4>
            <p class="text-slate-700 text-sm leading-relaxed">
                Dùng để hỏi xin phép hoặc biểu thị khả năng: <strong>我能坐这儿吗？ (Tôi có thể ngồi đây không?)</strong>.<br>
                Phủ định là <strong>不能 (bù néng - không thể)</strong>.
            </p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-slate-50/60 p-6 space-y-3">
            <h4 class="font-bold text-slate-900 text-base">3. Liên từ "和" (hé - và)</h4>
            <p class="text-slate-700 text-sm leading-relaxed">
                Chuyên dùng để liên kết hai danh từ hoặc cụm danh từ: <strong>电脑和书 (máy tính và sách)</strong>.<br>
                Lưu ý: Không dùng 和 để nối 2 vế câu!
            </p>
        </div>
    </div>
</div>',
        'flashcards' => [
            ['hanzi' => '桌子', 'pinyin' => 'zhuōzi', 'meaning' => 'Cái bàn', 'example' => '一张桌子。', 'example_pinyin' => 'Yī zhāng zhuōzi.', 'example_meaning' => 'Một cái bàn.'],
            ['hanzi' => '上', 'pinyin' => 'shàng', 'meaning' => 'Trên, lên', 'example' => '桌子上。', 'example_pinyin' => 'Zhuōzi shang.', 'example_meaning' => 'Trên mặt bàn.'],
            ['hanzi' => '电脑', 'pinyin' => 'diànnǎo', 'meaning' => 'Máy vi tính', 'example' => '买电脑。', 'example_pinyin' => 'Mǎi diànnǎo.', 'example_meaning' => 'Mua máy tính.'],
            ['hanzi' => '和', 'pinyin' => 'hé', 'meaning' => 'Và, cùng (nối danh từ)', 'example' => '我和你。', 'example_pinyin' => 'Wǒ hé nǐ.', 'example_meaning' => 'Tôi và bạn.'],
            ['hanzi' => '本', 'pinyin' => 'běn', 'meaning' => 'Quyển, cuốn (lượng từ sách)', 'example' => '一本书。', 'example_pinyin' => 'Yī běn shū.', 'example_meaning' => 'Một cuốn sách.'],
            ['hanzi' => '里', 'pinyin' => 'lǐ', 'meaning' => 'Trong, bên trong', 'example' => '家里。', 'example_pinyin' => 'Jiā lǐ.', 'example_meaning' => 'Ở trong nhà.'],
            ['hanzi' => '前面', 'pinyin' => 'qiánmiàn', 'meaning' => 'Phía trước', 'example' => '学校前面。', 'example_pinyin' => 'Xuéxiào qiánmiàn.', 'example_meaning' => 'Phía trước trường học.'],
            ['hanzi' => '后面', 'pinyin' => 'hòumiàn', 'meaning' => 'Phía sau', 'example' => '医院后面。', 'example_pinyin' => 'Yīyuàn hòumiàn.', 'example_meaning' => 'Phía sau bệnh viện.'],
            ['hanzi' => '这儿', 'pinyin' => 'zhèr', 'meaning' => 'Ở đây, chỗ này', 'example' => '这儿很好。', 'example_pinyin' => 'Zhèr hěn hǎo.', 'example_meaning' => 'Ở đây rất tốt.'],
            ['hanzi' => '能', 'pinyin' => 'néng', 'meaning' => 'Có thể (cho phép hoặc năng lực)', 'example' => '我能去。', 'example_pinyin' => 'Wǒ néng qù.', 'example_meaning' => 'Tôi có thể đi.'],
            ['hanzi' => '坐', 'pinyin' => 'zuò', 'meaning' => 'Ngồi, đi (tàu xe)', 'example' => '请坐。', 'example_pinyin' => 'Qǐng zuò.', 'example_meaning' => 'Mời ngồi.'],
        ],
        'questions' => [
            [
                'question' => 'Để hỏi xin phép lịch sự "Tôi có thể ngồi ở đây không?", câu nói đúng là:',
                'pinyin' => 'Wǒ néng zuò zhèr ma?',
                'options' => ['我能坐这儿吗？', '我会坐这儿吗？', '我想坐哪儿？', '我在坐这儿吗？'],
                'correct_answer' => '我能坐这儿吗？',
                'explanation' => 'Động từ năng nguyện "能" biểu thị sự xin phép hoặc khả năng được phép làm việc gì.',
                'difficulty' => 'starter',
                'skill_type' => 'grammar',
            ],
            [
                'question' => 'Liên từ "和" (hé) trong tiếng Trung dùng để nối những thành phần nào?',
                'pinyin' => 'hé',
                'options' => ['Hai danh từ hoặc đại từ tương đương', 'Hai câu độc lập hoàn chỉnh', 'Hai động từ hành động khác nhau', 'Đặt ở đầu câu làm liên từ nối'],
                'correct_answer' => 'Hai danh từ hoặc đại từ tương đương',
                'explanation' => '"和" chỉ dùng để liên kết các từ hoặc cụm từ tương đương (ví dụ: 我和他, 电脑和书), không nối 2 phân câu.',
                'difficulty' => 'starter',
                'skill_type' => 'grammar',
            ],
        ],
    ],
    [
        'slug' => 'hsk1-bai-11-xian-zai-ji-dian',
        'title' => 'Bài 11: 现在几点 - Bây giờ là mấy giờ?',
        'summary' => 'Cách nói giờ và phút, trật tự thời gian trong câu và từ chỉ mốc thời gian "前".',
        'hsk_level' => 1,
        'sort_order' => 11,
        'estimated_minutes' => 30,
        'accent_color' => '#16a34a',
        'difficulty' => 'starter',
        'content' => '
<div class="space-y-8">
    <div class="rounded-2xl bg-emerald-50/70 p-6 border border-emerald-200/80">
        <h3 class="text-lg font-black text-emerald-900 flex items-center gap-2 mb-4">
            <span class="text-xl">💬</span> Bài khóa 1: Bây giờ là mấy giờ? (看手表 - Xem đồng hồ)
        </h3>
        <div class="space-y-3">
            <div class="p-4 bg-white rounded-xl shadow-xs border border-emerald-100">
                <p class="text-xl font-bold text-slate-900">A: 现在几点？</p>
                <p class="text-sm font-sans text-emerald-700 font-medium">Xiànzài jǐ diǎn?</p>
                <p class="text-sm text-slate-600 mt-1">Bây giờ là mấy giờ?</p>
            </div>
            <div class="p-4 bg-white rounded-xl shadow-xs border border-emerald-100">
                <p class="text-xl font-bold text-slate-900">B: 现在十点十分。</p>
                <p class="text-sm font-sans text-emerald-700 font-medium">Xiànzài shí diǎn shí fēn.</p>
                <p class="text-sm text-slate-600 mt-1">Bây giờ là 10 giờ 10 phút.</p>
            </div>
            <div class="p-4 bg-white rounded-xl shadow-xs border border-emerald-100">
                <p class="text-xl font-bold text-slate-900">A: 中午几点吃饭？</p>
                <p class="text-sm font-sans text-emerald-700 font-medium">Zhōngwǔ jǐ diǎn chīfàn?</p>
                <p class="text-sm text-slate-600 mt-1">Trưa nay mấy giờ ăn cơm?</p>
            </div>
            <div class="p-4 bg-white rounded-xl shadow-xs border border-emerald-100">
                <p class="text-xl font-bold text-slate-900">B: 十二点吃饭。</p>
                <p class="text-sm font-sans text-emerald-700 font-medium">Shí\'èr diǎn chīfàn.</p>
                <p class="text-sm text-slate-600 mt-1">12 giờ ăn cơm.</p>
            </div>
        </div>
    </div>

    <div class="rounded-2xl bg-teal-50/70 p-6 border border-teal-200/80">
        <h3 class="text-lg font-black text-teal-900 flex items-center gap-2 mb-4">
            <span class="text-xl">💬</span> Bài khóa 2: Khi nào về nhà?
        </h3>
        <div class="space-y-3">
            <div class="p-4 bg-white rounded-xl shadow-xs border border-teal-100">
                <p class="text-xl font-bold text-slate-900">A: 爸爸什么时候回家？</p>
                <p class="text-sm font-sans text-teal-700 font-medium">Bàba shénme shíhou huí jiā?</p>
                <p class="text-sm text-slate-600 mt-1">Khi nào thì bố về nhà?</p>
            </div>
            <div class="p-4 bg-white rounded-xl shadow-xs border border-teal-100">
                <p class="text-xl font-bold text-slate-900">B: 下午五点。</p>
                <p class="text-sm font-sans text-teal-700 font-medium">Xiàwǔ wǔ diǎn.</p>
                <p class="text-sm text-slate-600 mt-1">5 giờ chiều.</p>
            </div>
            <div class="p-4 bg-white rounded-xl shadow-xs border border-teal-100">
                <p class="text-xl font-bold text-slate-900">A: 我们什么时候去看电影？</p>
                <p class="text-sm font-sans text-teal-700 font-medium">Wǒmen shénme shíhou qù kàn diànyǐng?</p>
                <p class="text-sm text-slate-600 mt-1">Chúng ta khi nào đi xem phim?</p>
            </div>
            <div class="p-4 bg-white rounded-xl shadow-xs border border-teal-100">
                <p class="text-xl font-bold text-slate-900">B: 六点三十分。</p>
                <p class="text-sm font-sans text-teal-700 font-medium">Liù diǎn sānshí fēn.</p>
                <p class="text-sm text-slate-600 mt-1">6 giờ 30 phút.</p>
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <h3 class="text-xl font-black text-slate-900 flex items-center gap-2">
            <span class="text-xl">📖</span> Ngữ pháp trọng điểm (Grammar Notes)
        </h3>

        <div class="rounded-2xl border border-slate-200 bg-slate-50/60 p-6 space-y-3">
            <h4 class="font-bold text-slate-900 text-base">1. Cách nói giờ và phút</h4>
            <p class="text-slate-700 text-sm leading-relaxed">
                Công thức: <strong>[Số giờ] + 点 (diǎn) + [Số phút] + 分 (fēn)</strong>.<br>
                Ví dụ: <strong>10点10分 (10 giờ 10 phút)</strong>, <strong>6点30分 (6 giờ 30 phút)</strong>.
            </p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-slate-50/60 p-6 space-y-3">
            <h4 class="font-bold text-slate-900 text-base">2. Vị trí trạng ngữ thời gian trong câu</h4>
            <p class="text-slate-700 text-sm leading-relaxed">
                Trạng ngữ chỉ thời gian phải đặt <strong>trước động từ</strong> (có thể đứng trước hoặc sau chủ ngữ):<br>
                - <strong>爸爸下午五点回家。</strong> (Bố 5 giờ chiều về nhà).<br>
                - Hoặc: <strong>下午五点爸爸回家。</strong><br>
                Tuyệt đối không đặt thời gian ở cuối câu như tiếng Việt hay tiếng Anh!
            </p>
        </div>
    </div>
</div>',
        'flashcards' => [
            ['hanzi' => '现在', 'pinyin' => 'xiànzài', 'meaning' => 'Bây giờ, hiện tại', 'example' => '现在几点？', 'example_pinyin' => 'Xiànzài jǐ diǎn?', 'example_meaning' => 'Bây giờ là mấy giờ?'],
            ['hanzi' => '点', 'pinyin' => 'diǎn', 'meaning' => 'Giờ, điểm', 'example' => '八点。', 'example_pinyin' => 'Bā diǎn.', 'example_meaning' => '8 giờ.'],
            ['hanzi' => '分', 'pinyin' => 'fēn', 'meaning' => 'Phút', 'example' => '十分钟。', 'example_pinyin' => 'Shí fēnzhōng.', 'example_meaning' => '10 phút.'],
            ['hanzi' => '中午', 'pinyin' => 'zhōngwǔ', 'meaning' => 'Buổi trưa', 'example' => '中午好。', 'example_pinyin' => 'Zhōngwǔ hǎo.', 'example_meaning' => 'Chào buổi trưa.'],
            ['hanzi' => '吃饭', 'pinyin' => 'chīfàn', 'meaning' => 'Ăn cơm, dùng bữa', 'example' => '去吃饭。', 'example_pinyin' => 'Qù chīfàn.', 'example_meaning' => 'Đi ăn cơm.'],
            ['hanzi' => '时候', 'pinyin' => 'shíhou', 'meaning' => 'Lúc, khi, thời điểm', 'example' => '什么时候？', 'example_pinyin' => 'Shénme shíhou?', 'example_meaning' => 'Khi nào? Bao giờ?'],
            ['hanzi' => '回', 'pinyin' => 'huí', 'meaning' => 'Về, quay lại', 'example' => '回家。', 'example_pinyin' => 'Huí jiā.', 'example_meaning' => 'Về nhà.'],
            ['hanzi' => '我们', 'pinyin' => 'wǒmen', 'meaning' => 'Chúng tôi, chúng ta', 'example' => '我们走吧。', 'example_pinyin' => 'Wǒmen zǒu ba.', 'example_meaning' => 'Chúng ta đi thôi.'],
            ['hanzi' => '电影', 'pinyin' => 'diànyǐng', 'meaning' => 'Phim ảnh, điện ảnh', 'example' => '看电影。', 'example_pinyin' => 'Kàn diànyǐng.', 'example_meaning' => 'Xem phim.'],
            ['hanzi' => '住', 'pinyin' => 'zhù', 'meaning' => 'Ở, trú ngụ', 'example' => '住在北京。', 'example_pinyin' => 'Zhù zài Běijīng.', 'example_meaning' => 'Sống ở Bắc Kinh.'],
            ['hanzi' => '前', 'pinyin' => 'qián', 'meaning' => 'Trước, phía trước', 'example' => '三天前。', 'example_pinyin' => 'Sān tiān qián.', 'example_meaning' => 'Ba ngày trước.'],
        ],
        'questions' => [
            [
                'question' => 'Để nói "9 giờ 15 phút" trong tiếng Trung, ta viết:',
                'pinyin' => 'Jiǔ diǎn shíwǔ fēn',
                'options' => ['九点十五分', '九分十五点', '九点十五号', '九月十五分'],
                'correct_answer' => '九点十五分',
                'explanation' => 'Cách nói giờ phút: [Số giờ] + 点 + [Số phút] + 分 -> 九点十五分.',
                'difficulty' => 'starter',
                'skill_type' => 'grammar',
            ],
            [
                'question' => 'Vị trí của trạng ngữ thời gian trong câu "Chúng tôi 7 giờ đi xem phim" là ở đâu?',
                'pinyin' => 'shíjiān zhuàngyǔ',
                'options' => ['Trước động từ: 我们七点去看电影。', 'Cuối câu: 我们去看电影七点。', 'Sau tân ngữ', 'Bất kỳ vị trí nào'],
                'correct_answer' => 'Trước động từ: 我们七点去看电影。',
                'explanation' => 'Trạng từ chỉ thời gian trong tiếng Trung bắt buộc phải đứng trước vị ngữ/động từ.',
                'difficulty' => 'starter',
                'skill_type' => 'grammar',
            ],
        ],
    ],
    [
        'slug' => 'hsk1-bai-12-ming-tian-tian-qi-zen-me-yang',
        'title' => 'Bài 12: 明天天气怎么样 - Thời tiết ngày mai thế nào?',
        'summary' => 'Đại từ nghi vấn "怎么样" (thế nào), cấu trúc cảm thán "太...了" và động từ năng nguyện dự đoán "会".',
        'hsk_level' => 1,
        'sort_order' => 12,
        'estimated_minutes' => 30,
        'accent_color' => '#16a34a',
        'difficulty' => 'starter',
        'content' => '
<div class="space-y-8">
    <div class="rounded-2xl bg-emerald-50/70 p-6 border border-emerald-200/80">
        <h3 class="text-lg font-black text-emerald-900 flex items-center gap-2 mb-4">
            <span class="text-xl">💬</span> Bài khóa 1: Hỏi về thời tiết
        </h3>
        <div class="space-y-3">
            <div class="p-4 bg-white rounded-xl shadow-xs border border-emerald-100">
                <p class="text-xl font-bold text-slate-900">A: 昨天北京的天气怎么样？</p>
                <p class="text-sm font-sans text-emerald-700 font-medium">Zuótiān Běijīng de tiānqì zěnmeyàng?</p>
                <p class="text-sm text-slate-600 mt-1">Thời tiết Bắc Kinh hôm qua thế nào?</p>
            </div>
            <div class="p-4 bg-white rounded-xl shadow-xs border border-emerald-100">
                <p class="text-xl font-bold text-slate-900">B: 太热了。</p>
                <p class="text-sm font-sans text-emerald-700 font-medium">Tài rè le.</p>
                <p class="text-sm text-slate-600 mt-1">Nóng quá.</p>
            </div>
            <div class="p-4 bg-white rounded-xl shadow-xs border border-emerald-100">
                <p class="text-xl font-bold text-slate-900">A: 明天呢？明天天气怎么样？</p>
                <p class="text-sm font-sans text-emerald-700 font-medium">Míngtiān ne? Míngtiān tiānqì zěnmeyàng?</p>
                <p class="text-sm text-slate-600 mt-1">Còn ngày mai thì sao? Thời tiết ngày mai thế nào?</p>
            </div>
            <div class="p-4 bg-white rounded-xl shadow-xs border border-emerald-100">
                <p class="text-xl font-bold text-slate-900">B: 明天天气很好，不冷不热。</p>
                <p class="text-sm font-sans text-emerald-700 font-medium">Míngtiān tiānqì hěn hǎo, bù lěng bú rè.</p>
                <p class="text-sm text-slate-600 mt-1">Ngày mai thời tiết rất đẹp, không lạnh cũng không nóng.</p>
            </div>
        </div>
    </div>

    <div class="rounded-2xl bg-teal-50/70 p-6 border border-teal-200/80">
        <h3 class="text-lg font-black text-teal-900 flex items-center gap-2 mb-4">
            <span class="text-xl">💬</span> Bài khóa 2: Khả năng mưa và chăm sóc sức khỏe
        </h3>
        <div class="space-y-3">
            <div class="p-4 bg-white rounded-xl shadow-xs border border-teal-100">
                <p class="text-xl font-bold text-slate-900">A: 今天会下雨吗？</p>
                <p class="text-sm font-sans text-teal-700 font-medium">Jīntiān huì xiàyǔ ma?</p>
                <p class="text-sm text-slate-600 mt-1">Hôm nay trời có mưa không?</p>
            </div>
            <div class="p-4 bg-white rounded-xl shadow-xs border border-teal-100">
                <p class="text-xl font-bold text-slate-900">B: 今天不会下雨。</p>
                <p class="text-sm font-sans text-teal-700 font-medium">Jīntiān bú huì xiàyǔ.</p>
                <p class="text-sm text-slate-600 mt-1">Hôm nay trời sẽ không mưa.</p>
            </div>
            <div class="p-4 bg-white rounded-xl shadow-xs border border-teal-100">
                <p class="text-xl font-bold text-slate-900">A: 你身体怎么样？</p>
                <p class="text-sm font-sans text-teal-700 font-medium">Nǐ shēntǐ zěnmeyàng?</p>
                <p class="text-sm text-slate-600 mt-1">Sức khỏe của bạn dạo này thế nào?</p>
            </div>
            <div class="p-4 bg-white rounded-xl shadow-xs border border-teal-100">
                <p class="text-xl font-bold text-slate-900">B: 我身体很好。天气热，你多吃水果，多喝水。</p>
                <p class="text-sm font-sans text-teal-700 font-medium">Wǒ shēntǐ hěn hǎo. Tiānqì rè, nǐ duō chī shuǐguǒ, duō hē shuǐ.</p>
                <p class="text-sm text-slate-600 mt-1">Sức khỏe tôi rất tốt. Trời nóng, bạn hãy ăn nhiều hoa quả, uống nhiều nước nhé.</p>
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <h3 class="text-xl font-black text-slate-900 flex items-center gap-2">
            <span class="text-xl">📖</span> Ngữ pháp trọng điểm (Grammar Notes)
        </h3>

        <div class="rounded-2xl border border-slate-200 bg-slate-50/60 p-6 space-y-3">
            <h4 class="font-bold text-slate-900 text-base">1. Đại từ nghi vấn "怎么样" (zěnmeyàng - thế nào)</h4>
            <p class="text-slate-700 text-sm leading-relaxed">
                Dùng để hỏi về tình hình, tính chất hoặc trạng thái của một người/sự việc: <strong>天气怎么样？ (Thời tiết thế nào?)</strong>, <strong>身体怎么样？ (Sức khỏe thế nào?)</strong>.
            </p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-slate-50/60 p-6 space-y-3">
            <h4 class="font-bold text-slate-900 text-base">2. Cấu trúc mức độ cao: "太 + Tính từ + 了"</h4>
            <p class="text-slate-700 text-sm leading-relaxed">
                Dùng biểu thị cảm thán hoặc mức độ quá mức: <strong>太热了 (Nóng quá!)</strong>, <strong>太好了 (Tuyệt vời quá!)</strong>.<br>
                Khi ở thể phủ định dùng <strong>不太 + Tính từ</strong> (không có 了): <strong>不太热 (Không nóng lắm)</strong>.
            </p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-slate-50/60 p-6 space-y-3">
            <h4 class="font-bold text-slate-900 text-base">3. Động từ năng nguyện "会" (huì) chỉ khả năng xảy ra</h4>
            <p class="text-slate-700 text-sm leading-relaxed">
                Ngoài biểu thị kỹ năng đã học, "会" còn dùng để dự đoán một sự việc có khả năng xảy ra trong tương lai: <strong>今天会下雨吗？ (Hôm nay trời có mưa không?)</strong>.
            </p>
        </div>
    </div>
</div>',
        'flashcards' => [
            ['hanzi' => '天气', 'pinyin' => 'tiānqì', 'meaning' => 'Thời tiết', 'example' => '好天气。', 'example_pinyin' => 'Hǎo tiānqì.', 'example_meaning' => 'Thời tiết đẹp.'],
            ['hanzi' => '怎么样', 'pinyin' => 'zěnmeyàng', 'meaning' => 'Thế nào, ra sao', 'example' => '你怎么样？', 'example_pinyin' => 'Nǐ zěnmeyàng?', 'example_meaning' => 'Bạn thế nào rồi?'],
            ['hanzi' => '太', 'pinyin' => 'tài', 'meaning' => 'Quá, lắm', 'example' => '太大了。', 'example_pinyin' => 'Tài dà le.', 'example_meaning' => 'To quá.'],
            ['hanzi' => '热', 'pinyin' => 'rè', 'meaning' => 'Nóng', 'example' => '天气很热。', 'example_pinyin' => 'Tiānqì hěn rè.', 'example_meaning' => 'Thời tiết rất nóng.'],
            ['hanzi' => '冷', 'pinyin' => 'lěng', 'meaning' => 'Lạnh', 'example' => '太冷了。', 'example_pinyin' => 'Tài lěng le.', 'example_meaning' => 'Lạnh quá.'],
            ['hanzi' => '下雨', 'pinyin' => 'xiàyǔ', 'meaning' => 'Mưa, trời mưa', 'example' => '明天会下雨。', 'example_pinyin' => 'Míngtiān huì xiàyǔ.', 'example_meaning' => 'Ngày mai sẽ mưa.'],
            ['hanzi' => '雨', 'pinyin' => 'yǔ', 'meaning' => 'Mưa', 'example' => '大雨。', 'example_pinyin' => 'Dà yǔ.', 'example_meaning' => 'Mưa to.'],
            ['hanzi' => '小姐', 'pinyin' => 'xiǎojiě', 'meaning' => 'Cô, tiểu thư', 'example' => '张小姐。', 'example_pinyin' => 'Zhāng xiǎojiě.', 'example_meaning' => 'Cô Trương.'],
            ['hanzi' => '来', 'pinyin' => 'lái', 'meaning' => 'Đến, tới', 'example' => '他来了。', 'example_pinyin' => 'Tā lái le.', 'example_meaning' => 'Anh ấy đến rồi.'],
            ['hanzi' => '身体', 'pinyin' => 'shēntǐ', 'meaning' => 'Thân thể, sức khỏe', 'example' => '身体健康。', 'example_pinyin' => 'Shēntǐ jiànkāng.', 'example_meaning' => 'Thân thể khỏe mạnh.'],
            ['hanzi' => '爱', 'pinyin' => 'ài', 'meaning' => 'Yêu, thích', 'example' => '我爱你。', 'example_pinyin' => 'Wǒ ài nǐ.', 'example_meaning' => 'Tôi yêu bạn.'],
            ['hanzi' => '水果', 'pinyin' => 'shuǐguǒ', 'meaning' => 'Trái cây, hoa quả', 'example' => '买水果。', 'example_pinyin' => 'Mǎi shuǐguǒ.', 'example_meaning' => 'Mua hoa quả.'],
            ['hanzi' => '水', 'pinyin' => 'shuǐ', 'meaning' => 'Nước', 'example' => '喝水。', 'example_pinyin' => 'Hē shuǐ.', 'example_meaning' => 'Uống nước.'],
        ],
        'questions' => [
            [
                'question' => 'Để hỏi "Thời tiết ngày mai thế nào?", ta nói câu gì?',
                'pinyin' => 'Míngtiān tiānqì zěnmeyàng?',
                'options' => ['明天天气怎么样？', '明天天气怎么？', '明天天气什么？', '明天天气在哪儿？'],
                'correct_answer' => '明天天气怎么样？',
                'explanation' => 'Đại từ nghi vấn 怎么样 dùng hỏi về tính chất hoặc tình trạng.',
                'difficulty' => 'starter',
                'skill_type' => 'grammar',
            ],
            [
                'question' => 'Cấu trúc "太...了" khi chuyển sang phủ định (Không... lắm) sẽ được viết như thế nào?',
                'pinyin' => 'bú tài',
                'options' => ['不太 + Tính từ (bỏ 了)', '不太 + Tính từ + 了', '太不 + Tính từ + 了', '没太 + Tính từ'],
                'correct_answer' => '不太 + Tính từ (bỏ 了)',
                'explanation' => 'Phủ định của "太...了" là "不太 + Tính từ" (ví dụ: 不太热 - không nóng lắm).',
                'difficulty' => 'starter',
                'skill_type' => 'grammar',
            ],
        ],
    ],
    [
        'slug' => 'hsk1-bai-13-ta-zai-xue-zuo-zhong-guo-cai-ne',
        'title' => 'Bài 13: 他在学做中国菜呢 - Anh ấy đang học nấu món Trung Quốc đấy',
        'summary' => 'Hành động đang tiếp diễn với "在...呢", phủ định với "没" và cách bắt máy điện thoại "喂".',
        'hsk_level' => 1,
        'sort_order' => 13,
        'estimated_minutes' => 30,
        'accent_color' => '#16a34a',
        'difficulty' => 'starter',
        'content' => '
<div class="space-y-8">
    <div class="rounded-2xl bg-emerald-50/70 p-6 border border-emerald-200/80">
        <h3 class="text-lg font-black text-emerald-900 flex items-center gap-2 mb-4">
            <span class="text-xl">💬</span> Bài khóa 1: Nghe điện thoại (打电话)
        </h3>
        <div class="space-y-3">
            <div class="p-4 bg-white rounded-xl shadow-xs border border-emerald-100">
                <p class="text-xl font-bold text-slate-900">A: 喂，你在做什么呢？</p>
                <p class="text-sm font-sans text-emerald-700 font-medium">Wèi, nǐ zài zuò shénme ne?</p>
                <p class="text-sm text-slate-600 mt-1">A-lô, bạn đang làm gì đấy?</p>
            </div>
            <div class="p-4 bg-white rounded-xl shadow-xs border border-emerald-100">
                <p class="text-xl font-bold text-slate-900">B: 我在看书呢。</p>
                <p class="text-sm font-sans text-emerald-700 font-medium">Wǒ zài kàn shū ne.</p>
                <p class="text-sm text-slate-600 mt-1">Tôi đang đọc sách nè.</p>
            </div>
            <div class="p-4 bg-white rounded-xl shadow-xs border border-emerald-100">
                <p class="text-xl font-bold text-slate-900">A: 大卫也在看书吗？</p>
                <p class="text-sm font-sans text-emerald-700 font-medium">Dàwèi yě zài kàn shū ma?</p>
                <p class="text-sm text-slate-600 mt-1">David cũng đang đọc sách à?</p>
            </div>
            <div class="p-4 bg-white rounded-xl shadow-xs border border-emerald-100">
                <p class="text-xl font-bold text-slate-900">B: 他没看书，他在学做中国菜呢。</p>
                <p class="text-sm font-sans text-teal-700 font-medium">Tā méi kàn shū, tā zài xué zuò Zhōngguó cài ne.</p>
                <p class="text-sm text-slate-600 mt-1">Cậu ấy không đọc sách, cậu ấy đang học nấu món ăn Trung Quốc đấy.</p>
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <h3 class="text-xl font-black text-slate-900 flex items-center gap-2">
            <span class="text-xl">📖</span> Ngữ pháp trọng điểm (Grammar Notes)
        </h3>

        <div class="rounded-2xl border border-slate-200 bg-slate-50/60 p-6 space-y-3">
            <h4 class="font-bold text-slate-900 text-base">1. Cấu trúc biểu thị hành động đang tiếp diễn: [在 + Động từ + 呢]</h4>
            <p class="text-slate-700 text-sm leading-relaxed">
                Diễn tả một hành động đang xảy ra tại thời điểm nói:<br>
                <strong>Chủ ngữ + 在 + Động từ + (呢)</strong>.<br>
                Ví dụ: <strong>我在看书呢 (Tôi đang đọc sách nè)</strong>.<br>
                <em>Chú ý phủ định:</em> Dùng <strong>没 (méi)</strong> hoặc <strong>没在</strong> trước động từ (KHÔNG dùng 不): <strong>他没看书 (Cậu ấy không đọc sách)</strong>.
            </p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-slate-50/60 p-6 space-y-3">
            <h4 class="font-bold text-slate-900 text-base">2. Cách bắt máy điện thoại "喂" (wèi / wéi)</h4>
            <p class="text-slate-700 text-sm leading-relaxed">
                Tương đương với từ "A-lô" trong tiếng Việt, dùng khi nghe điện thoại hoặc gọi ai đó chú ý.
            </p>
        </div>
    </div>
</div>',
        'flashcards' => [
            ['hanzi' => '喂', 'pinyin' => 'wèi', 'meaning' => 'A-lô (khi nghe điện thoại)', 'example' => '喂，你好！', 'example_pinyin' => 'Wèi, nǐ hǎo!', 'example_meaning' => 'A-lô, xin chào!'],
            ['hanzi' => '也', 'pinyin' => 'yě', 'meaning' => 'Cũng', 'example' => '我也去。', 'example_pinyin' => 'Wǒ yě qù.', 'example_meaning' => 'Tôi cũng đi.'],
            ['hanzi' => '学习', 'pinyin' => 'xuéxí', 'meaning' => 'Học tập, học', 'example' => '学习汉语。', 'example_pinyin' => 'Xuéxí Hànyǔ.', 'example_meaning' => 'Học tiếng Hán.'],
            ['hanzi' => '上午', 'pinyin' => 'shàngwǔ', 'meaning' => 'Buổi sáng (từ 8h đến 11h)', 'example' => '明天上午。', 'example_pinyin' => 'Míngtiān shàngwǔ.', 'example_meaning' => 'Sáng ngày mai.'],
            ['hanzi' => '睡觉', 'pinyin' => 'shuìjiào', 'meaning' => 'Ngủ', 'example' => '我想睡觉。', 'example_pinyin' => 'Wǒ xiǎng shuìjiào.', 'example_meaning' => 'Tôi muốn đi ngủ.'],
            ['hanzi' => '电视', 'pinyin' => 'diànshì', 'meaning' => 'Vô tuyến, truyền hình, tivi', 'example' => '看电视。', 'example_pinyin' => 'Kàn diànshì.', 'example_meaning' => 'Xem tivi.'],
            ['hanzi' => '喜欢', 'pinyin' => 'xǐhuan', 'meaning' => 'Thích, ưa thích', 'example' => '我很喜欢你。', 'example_pinyin' => 'Wǒ hěn xǐhuan nǐ.', 'example_meaning' => 'Tôi rất thích bạn.'],
            ['hanzi' => '打电话', 'pinyin' => 'dǎ diànhuà', 'meaning' => 'Gọi điện thoại', 'example' => '给我打电话。', 'example_pinyin' => 'Gěi wǒ dǎ diànhuà.', 'example_meaning' => 'Gọi điện thoại cho tôi.'],
            ['hanzi' => '吧', 'pinyin' => 'ba', 'meaning' => 'Nhé, đi (trợ từ ngữ khí đề nghị)', 'example' => '走吧。', 'example_pinyin' => 'Zǒu ba.', 'example_meaning' => 'Đi thôi nào.'],
        ],
        'questions' => [
            [
                'question' => 'Dạng phủ định của câu hành động đang tiếp diễn "他在看书呢" là gì?',
                'pinyin' => 'Tā méi kàn shū',
                'options' => ['他没看书。', '他不看书。', '他不是看书。', '他没有看书呢。'],
                'correct_answer' => '他没看书。',
                'explanation' => 'Phủ định của hành động đang diễn ra dùng "没" hoặc "没在" trước động từ, bỏ trợ từ 呢.',
                'difficulty' => 'starter',
                'skill_type' => 'grammar',
            ],
            [
                'question' => 'Khi nhấc máy nghe điện thoại, người Trung Quốc nói từ nào đầu tiên?',
                'pinyin' => 'wèi',
                'options' => ['喂 (wèi)', '你好 (nǐ hǎo)', '请问 (qǐngwèn)', '谁呀 (shéi ya)'],
                'correct_answer' => '喂 (wèi)',
                'explanation' => 'Từ "喂" (wèi / wéi) là từ bắt máy điện thoại chuẩn xác nhất trong tiếng Trung.',
                'difficulty' => 'starter',
                'skill_type' => 'vocabulary',
            ],
        ],
    ],
    [
        'slug' => 'hsk1-bai-14-ta-mai-le-bu-shao-yi-fu',
        'title' => 'Bài 14: 她买了不少衣服 - Cô ấy đã mua không ít quần áo',
        'summary' => 'Trợ từ động thái "了" biểu thị hành động đã hoàn thành, từ chỉ số lượng "一点儿" và "不少".',
        'hsk_level' => 1,
        'sort_order' => 14,
        'estimated_minutes' => 30,
        'accent_color' => '#16a34a',
        'difficulty' => 'starter',
        'content' => '
<div class="space-y-8">
    <div class="rounded-2xl bg-emerald-50/70 p-6 border border-emerald-200/80">
        <h3 class="text-lg font-black text-emerald-900 flex items-center gap-2 mb-4">
            <span class="text-xl">💬</span> Bài khóa 1: Mua sắm quần áo (在商场 - Ở trung tâm thương mại)
        </h3>
        <div class="space-y-3">
            <div class="p-4 bg-white rounded-xl shadow-xs border border-emerald-100">
                <p class="text-xl font-bold text-slate-900">A: 王方的衣服太漂亮了！</p>
                <p class="text-sm font-sans text-emerald-700 font-medium">Wáng Fāng de yīfu tài piàoliang le!</p>
                <p class="text-sm text-slate-600 mt-1">Quần áo của Vương Phương đẹp quá!</p>
            </div>
            <div class="p-4 bg-white rounded-xl shadow-xs border border-emerald-100">
                <p class="text-xl font-bold text-slate-900">B: 是啊，她买了不少衣服。</p>
                <p class="text-sm font-sans text-emerald-700 font-medium">Shì a, tā mǎi le bù shǎo yīfu.</p>
                <p class="text-sm text-slate-600 mt-1">Đúng vậy, cô ấy đã mua không ít quần áo đâu.</p>
            </div>
            <div class="p-4 bg-white rounded-xl shadow-xs border border-emerald-100">
                <p class="text-xl font-bold text-slate-900">A: 你买什么了？</p>
                <p class="text-sm font-sans text-emerald-700 font-medium">Nǐ mǎi shénme le?</p>
                <p class="text-sm text-slate-600 mt-1">Bạn đã mua cái gì rồi?</p>
            </div>
            <div class="p-4 bg-white rounded-xl shadow-xs border border-emerald-100">
                <p class="text-xl font-bold text-slate-900">B: 我没买，这些都是王方的东西。</p>
                <p class="text-sm font-sans text-emerald-700 font-medium">Wǒ méi mǎi, zhèxiē dōu shì Wáng Fāng de dōngxi.</p>
                <p class="text-sm text-slate-600 mt-1">Tôi không mua gì cả, những thứ này đều là đồ của Vương Phương.</p>
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <h3 class="text-xl font-black text-slate-900 flex items-center gap-2">
            <span class="text-xl">📖</span> Ngữ pháp trọng điểm (Grammar Notes)
        </h3>

        <div class="rounded-2xl border border-slate-200 bg-slate-50/60 p-6 space-y-3">
            <h4 class="font-bold text-slate-900 text-base">1. Trợ từ động thái "了" (le) chỉ hành động hoàn thành</h4>
            <p class="text-slate-700 text-sm leading-relaxed">
                Khi đặt ngay sau động từ: <strong>Động từ + 了 + Tân ngữ</strong>, biểu thị hành động đó đã diễn ra và hoàn thành:<br>
                <strong>她买了不少衣服 (Cô ấy đã mua rất nhiều quần áo)</strong>.<br>
                <em>Phủ định:</em> Dùng <strong>没 + Động từ</strong> và <strong>bỏ 了</strong>: <strong>我没买 (Tôi chưa/không mua)</strong>.
            </p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-slate-50/60 p-6 space-y-3">
            <h4 class="font-bold text-slate-900 text-base">2. Cụm từ "一点儿" (yìdiǎnr - một ít, một chút)</h4>
            <p class="text-slate-700 text-sm leading-relaxed">
                Đứng trước danh từ để bổ nghĩa chỉ số lượng ít: <strong>我买了一点儿苹果 (Tôi mua một ít táo)</strong>.
            </p>
        </div>
    </div>
</div>',
        'flashcards' => [
            ['hanzi' => '东西', 'pinyin' => 'dōngxi', 'meaning' => 'Đồ đạc, đồ vật', 'example' => '买东西。', 'example_pinyin' => 'Mǎi dōngxi.', 'example_meaning' => 'Mua đồ.'],
            ['hanzi' => '一点儿', 'pinyin' => 'yìdiǎnr', 'meaning' => 'Một chút, một ít', 'example' => '吃一点儿。', 'example_pinyin' => 'Chī yìdiǎnr.', 'example_meaning' => 'Ăn một chút.'],
            ['hanzi' => '苹果', 'pinyin' => 'píngguǒ', 'meaning' => 'Quả táo', 'example' => '红苹果。', 'example_pinyin' => 'Hóng píngguǒ.', 'example_meaning' => 'Táo đỏ.'],
            ['hanzi' => '看见', 'pinyin' => 'kànjiàn', 'meaning' => 'Nhìn thấy, trông thấy', 'example' => '我看见他了。', 'example_pinyin' => 'Wǒ kànjiàn tā le.', 'example_meaning' => 'Tôi nhìn thấy anh ấy rồi.'],
            ['hanzi' => '先生', 'pinyin' => 'xiānsheng', 'meaning' => 'Ông, ngài, tiên sinh', 'example' => '王先生。', 'example_pinyin' => 'Wáng xiānsheng.', 'example_meaning' => 'Ông Vương.'],
            ['hanzi' => '开', 'pinyin' => 'kāi', 'meaning' => 'Mở, lái (xe)', 'example' => '开车。', 'example_pinyin' => 'Kāi chē.', 'example_meaning' => 'Lái xe.'],
            ['hanzi' => '车', 'pinyin' => 'chē', 'meaning' => 'Xe cộ', 'example' => '坐车。', 'example_pinyin' => 'Zuò chē.', 'example_meaning' => 'Đi xe.'],
            ['hanzi' => '回来', 'pinyin' => 'huílái', 'meaning' => 'Trở về, quay về', 'example' => '他回来了。', 'example_pinyin' => 'Tā huílái le.', 'example_meaning' => 'Anh ấy quay về rồi.'],
            ['hanzi' => '分钟', 'pinyin' => 'fēnzhōng', 'meaning' => 'Phút (khoảng thời gian)', 'example' => '五分钟。', 'example_pinyin' => 'Wǔ fēnzhōng.', 'example_meaning' => '5 phút.'],
            ['hanzi' => '后', 'pinyin' => 'hòu', 'meaning' => 'Sau, sau khi', 'example' => '三天后。', 'example_pinyin' => 'Sān tiān hòu.', 'example_meaning' => 'Sau 3 ngày.'],
            ['hanzi' => '衣服', 'pinyin' => 'yīfu', 'meaning' => 'Quần áo', 'example' => '新衣服。', 'example_pinyin' => 'Xīn yīfu.', 'example_meaning' => 'Quần áo mới.'],
            ['hanzi' => '漂亮', 'pinyin' => 'piàoliang', 'meaning' => 'Xinh đẹp, đẹp đẽ', 'example' => '真漂亮！', 'example_pinyin' => 'Zhēn piàoliang!', 'example_meaning' => 'Thật là đẹp!'],
            ['hanzi' => '不少', 'pinyin' => 'bù shǎo', 'meaning' => 'Không ít, nhiều', 'example' => '不少人。', 'example_pinyin' => 'Bù shǎo rén.', 'example_meaning' => 'Khá nhiều người.'],
        ],
        'questions' => [
            [
                'question' => 'Dạng phủ định của câu "我买了衣服" (Tôi đã mua quần áo) là gì?',
                'pinyin' => 'Wǒ méi mǎi yīfu',
                'options' => ['我没买衣服。', '我不买衣服了。', '我不买了衣服。', '我没有买了衣服。'],
                'correct_answer' => '我没买衣服。',
                'explanation' => 'Phủ định của hành động đã hoàn thành dùng "没 + Động từ" và bắt buộc phải bỏ trợ từ "了".',
                'difficulty' => 'starter',
                'skill_type' => 'grammar',
            ],
            [
                'question' => 'Từ "漂亮" (piàoliang) có nghĩa là gì?',
                'pinyin' => 'piàoliang',
                'options' => ['Xinh đẹp', 'Ngon miệng', 'Đắt đỏ', 'Rẻ tiền'],
                'correct_answer' => 'Xinh đẹp',
                'explanation' => '漂亮 (piàoliang) nghĩa là đẹp đẽ, xinh đẹp.',
                'difficulty' => 'starter',
                'skill_type' => 'vocabulary',
            ],
        ],
    ],
    [
        'slug' => 'hsk1-bai-15-wo-shi-zuo-fei-ji-lai-de',
        'title' => 'Bài 15: 我是坐飞机来的 - Tôi đến bằng máy bay',
        'summary' => 'Cấu trúc nhấn mạnh "是...的" về thời gian, địa điểm, phương thức và tổng kết HSK 1.',
        'hsk_level' => 1,
        'sort_order' => 15,
        'estimated_minutes' => 35,
        'accent_color' => '#16a34a',
        'difficulty' => 'starter',
        'content' => '
<div class="space-y-8">
    <div class="rounded-2xl bg-emerald-50/70 p-6 border border-emerald-200/80">
        <h3 class="text-lg font-black text-emerald-900 flex items-center gap-2 mb-4">
            <span class="text-xl">💬</span> Bài khóa 1: Quen biết nhau như thế nào? (在饭店 - Ở nhà hàng)
        </h3>
        <div class="space-y-3">
            <div class="p-4 bg-white rounded-xl shadow-xs border border-emerald-100">
                <p class="text-xl font-bold text-slate-900">A: 你认识李小姐吗？</p>
                <p class="text-sm font-sans text-emerald-700 font-medium">Nǐ rènshi Lǐ xiǎojiě ma?</p>
                <p class="text-sm text-slate-600 mt-1">Bạn có quen biết cô Lý không?</p>
            </div>
            <div class="p-4 bg-white rounded-xl shadow-xs border border-emerald-100">
                <p class="text-xl font-bold text-slate-900">B: 认识，我们是大学同学。</p>
                <p class="text-sm font-sans text-emerald-700 font-medium">Rènshi, wǒmen shì dàxué tóngxué.</p>
                <p class="text-sm text-slate-600 mt-1">Quen chứ, chúng tôi là bạn học đại học.</p>
            </div>
            <div class="p-4 bg-white rounded-xl shadow-xs border border-emerald-100">
                <p class="text-xl font-bold text-slate-900">A: 你们是怎么认识的？</p>
                <p class="text-sm font-sans text-emerald-700 font-medium">Nǐmen shì zěnme rènshi de?</p>
                <p class="text-sm text-slate-600 mt-1">Hai bạn quen biết nhau như thế nào thế?</p>
            </div>
            <div class="p-4 bg-white rounded-xl shadow-xs border border-emerald-100">
                <p class="text-xl font-bold text-slate-900">B: 我们是在北京认识的。</p>
                <p class="text-sm font-sans text-emerald-700 font-medium">Wǒmen shì zài Běijīng rènshi de.</p>
                <p class="text-sm text-slate-600 mt-1">Chúng tôi quen nhau ở Bắc Kinh (Nhấn mạnh địa điểm).</p>
            </div>
        </div>
    </div>

    <div class="rounded-2xl bg-teal-50/70 p-6 border border-teal-200/80">
        <h3 class="text-lg font-black text-teal-900 flex items-center gap-2 mb-4">
            <span class="text-xl">💬</span> Bài khóa 2: Đến đây bằng phương tiện gì?
        </h3>
        <div class="space-y-3">
            <div class="p-4 bg-white rounded-xl shadow-xs border border-teal-100">
                <p class="text-xl font-bold text-slate-900">A: 你们是怎么来饭店的？</p>
                <p class="text-sm font-sans text-teal-700 font-medium">Nǐmen shì zěnme lái fàndiàn de?</p>
                <p class="text-sm text-slate-600 mt-1">Các bạn đến khách sạn bằng cách nào?</p>
            </div>
            <div class="p-4 bg-white rounded-xl shadow-xs border border-teal-100">
                <p class="text-xl font-bold text-slate-900">B: 我们是坐出租车来的。李先生是坐飞机来的。</p>
                <p class="text-sm font-sans text-teal-700 font-medium">Wǒmen shì zuò chūzūchē lái de. Lǐ xiānsheng shì zuò fēijī lái de.</p>
                <p class="text-sm text-slate-600 mt-1">Chúng tôi đi taxi đến. Còn ông Lý thì đi máy bay đến.</p>
            </div>
            <div class="p-4 bg-white rounded-xl shadow-xs border border-teal-100">
                <p class="text-xl font-bold text-slate-900">A: 很高兴认识你！</p>
                <p class="text-sm font-sans text-teal-700 font-medium">Hěn gāoxìng rènshi nǐ!</p>
                <p class="text-sm text-slate-600 mt-1">Rất vui được làm quen với bạn!</p>
            </div>
            <div class="p-4 bg-white rounded-xl shadow-xs border border-teal-100">
                <p class="text-xl font-bold text-slate-900">B: 认识你我也很高兴！</p>
                <p class="text-sm font-sans text-teal-700 font-medium">Rènshi nǐ wǒ yě hěn gāoxìng!</p>
                <p class="text-sm text-slate-600 mt-1">Được quen bạn tôi cũng rất vui!</p>
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <h3 class="text-xl font-black text-slate-900 flex items-center gap-2">
            <span class="text-xl">📖</span> Ngữ pháp trọng điểm (Grammar Notes)
        </h3>

        <div class="rounded-2xl border border-slate-200 bg-slate-50/60 p-6 space-y-3">
            <h4 class="font-bold text-slate-900 text-base">1. Cấu trúc nhấn mạnh: "是...的" (shì... de)</h4>
            <p class="text-slate-700 text-sm leading-relaxed">
                Khi hành động <strong>đã xảy ra trong quá khứ</strong>, ta dùng cấu trúc <strong>是...的</strong> để nhấn mạnh:
            </p>
            <ul class="list-disc list-inside text-sm text-slate-700 space-y-1.5 pl-2">
                <li><strong>Nhấn mạnh phương thức:</strong> 我<strong>是</strong>坐飞机来<strong>的</strong> (Tôi đến bằng máy bay).</li>
                <li><strong>Nhấn mạnh địa điểm:</strong> 我们<strong>是</strong>在北京认识<strong>的</strong> (Chúng tôi quen nhau ở Bắc Kinh).</li>
                <li><strong>Nhấn mạnh thời gian:</strong> 他<strong>是</strong>昨天来<strong>的</strong> (Anh ấy đến vào ngày hôm qua).</li>
            </ul>
            <p class="text-slate-700 text-sm leading-relaxed">
                Phủ định đặt <strong>不是</strong> trước phần muốn phủ định: <strong>我们不是坐出租车来的 (Chúng tôi không phải đến bằng taxi)</strong>.
            </p>
        </div>
    </div>
</div>',
        'flashcards' => [
            ['hanzi' => '认识', 'pinyin' => 'rènshi', 'meaning' => 'Quen biết, nhận biết', 'example' => '很高兴认识你。', 'example_pinyin' => 'Hěn gāoxìng rènshi nǐ.', 'example_meaning' => 'Rất vui được quen bạn.'],
            ['hanzi' => '年', 'pinyin' => 'nián', 'meaning' => 'Năm', 'example' => '一年。', 'example_pinyin' => 'Yī nián.', 'example_meaning' => 'Một năm.'],
            ['hanzi' => '大学', 'pinyin' => 'dàxué', 'meaning' => 'Đại học, trường đại học', 'example' => '大学同学。', 'example_pinyin' => 'Dàxué tóngxué.', 'example_meaning' => 'Bạn học đại học.'],
            ['hanzi' => '饭店', 'pinyin' => 'fàndiàn', 'meaning' => 'Nhà hàng, quán ăn, khách sạn', 'example' => '去饭店吃饭。', 'example_pinyin' => 'Qù fàndiàn chīfàn.', 'example_meaning' => 'Đến nhà hàng ăn cơm.'],
            ['hanzi' => '出租车', 'pinyin' => 'chūzūchē', 'meaning' => 'Xe taxi', 'example' => '坐出租车。', 'example_pinyin' => 'Zuò chūzūchē.', 'example_meaning' => 'Đi xe taxi.'],
            ['hanzi' => '一起', 'pinyin' => 'yìqǐ', 'meaning' => 'Cùng nhau', 'example' => '我们一起去。', 'example_pinyin' => 'Wǒmen yìqǐ qù.', 'example_meaning' => 'Chúng ta cùng đi.'],
            ['hanzi' => '高兴', 'pinyin' => 'gāoxìng', 'meaning' => 'Vui vẻ, phấn khởi', 'example' => '我很高兴。', 'example_pinyin' => 'Wǒ hěn gāoxìng.', 'example_meaning' => 'Tôi rất vui.'],
            ['hanzi' => '听', 'pinyin' => 'tīng', 'meaning' => 'Nghe', 'example' => '听音乐。', 'example_pinyin' => 'Tīng yīnyuè.', 'example_meaning' => 'Nghe âm nhạc.'],
            ['hanzi' => '飞机', 'pinyin' => 'fēijī', 'meaning' => 'Máy bay', 'example' => '坐飞机。', 'example_pinyin' => 'Zuò fēijī.', 'example_meaning' => 'Đi máy bay.'],
        ],
        'questions' => [
            [
                'question' => 'Cấu trúc "是...的" dùng trong trường hợp nào?',
                'pinyin' => 'shì... de',
                'options' => ['Nhấn mạnh thời gian, địa điểm, phương thức của hành động đã xảy ra', 'Dự đoán hành động trong tương lai', 'Biểu thị hành động đang tiếp diễn', 'Hỏi nguyên nhân lý do'],
                'correct_answer' => 'Nhấn mạnh thời gian, địa điểm, phương thức của hành động đã xảy ra',
                'explanation' => 'Cấu trúc 是...的 dùng để nhấn mạnh chi tiết (thời gian, nơi chốn, cách thức) của một hành động đã hoàn tất trong quá khứ.',
                'difficulty' => 'starter',
                'skill_type' => 'grammar',
            ],
            [
                'question' => 'Câu "Chúng tôi đến bằng taxi" được nói bằng tiếng Trung là:',
                'pinyin' => 'Wǒmen shì zuò chūzūchē lái de',
                'options' => ['我们是坐出租车来的。', 'Chúng tôi đi taxi đến', '我们来是出租车。', '我们要在出租车来。'],
                'correct_answer' => '我们是坐出租车来的。',
                'explanation' => 'Nhấn mạnh phương tiện đến bằng taxi: [Chủ ngữ] + 是 + 坐出租车 + 来的.',
                'difficulty' => 'starter',
                'skill_type' => 'grammar',
            ],
        ],
    ],
];
