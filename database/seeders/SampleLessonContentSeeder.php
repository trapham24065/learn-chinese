<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Lesson;

class SampleLessonContentSeeder extends Seeder
{
    public function run()
    {
        $lessons = Lesson::take(3)->get();
        if ($lessons->isEmpty()) return;

        $content1 = '
        <h2>1. Giới thiệu (Introduction)</h2>
        <p>Chào mừng bạn đến với bài học đầu tiên! Trong bài này, chúng ta sẽ làm quen với các đại từ nhân xưng cơ bản và cách chào hỏi trong tiếng Trung.</p>
        
        <h3>Từ vựng trọng tâm (Vocabulary)</h3>
        <table border="1" cellpadding="10" style="border-collapse: collapse; width: 100%;">
            <thead>
                <tr>
                    <th>Chữ Hán</th>
                    <th>Pinyin</th>
                    <th>Nghĩa</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>我</td>
                    <td>wǒ</td>
                    <td>Tôi, tớ, mình</td>
                </tr>
                <tr>
                    <td>你</td>
                    <td>nǐ</td>
                    <td>Bạn, cậu</td>
                </tr>
                <tr>
                    <td>好</td>
                    <td>hǎo</td>
                    <td>Tốt, khỏe, hay</td>
                </tr>
            </tbody>
        </table>

        <h3>Ngữ pháp cơ bản (Grammar)</h3>
        <p>Để chào một người, chúng ta ghép đại từ với chữ <strong>好</strong>:</p>
        <ul>
            <li><strong>你好! (Nǐ hǎo!)</strong> - Xin chào! (Dùng cho người ngang hàng hoặc nhỏ tuổi hơn)</li>
            <li><strong>您好! (Nín hǎo!)</strong> - Xin chào! (Dùng cho người lớn tuổi, cấp trên, thể hiện sự tôn trọng)</li>
        </ul>
        
        <p><em>Mẹo nhỏ:</em> Khi hai thanh 3 đi liền nhau (như nǐ hǎo), thanh 3 thứ nhất sẽ biến điệu thành thanh 2. Vì vậy, ta đọc là "ní hǎo".</p>
        ';

        $content2 = '
        <h2>1. Số đếm từ 1 đến 10</h2>
        <p>Học đếm là kỹ năng cơ bản nhất. Tiếng Trung có hệ thống số đếm rất logic và dễ nhớ.</p>
        <ul>
            <li><strong>一 (yī)</strong>: 1</li>
            <li><strong>二 (èr)</strong>: 2</li>
            <li><strong>三 (sān)</strong>: 3</li>
            <li><strong>四 (sì)</strong>: 4</li>
            <li><strong>五 (wǔ)</strong>: 5</li>
            <li><strong>六 (liù)</strong>: 6</li>
            <li><strong>七 (qī)</strong>: 7</li>
            <li><strong>八 (bā)</strong>: 8</li>
            <li><strong>九 (jiǔ)</strong>: 9</li>
            <li><strong>十 (shí)</strong>: 10</li>
        </ul>

        <h2>2. Cấu trúc câu hỏi với "吗" (ma)</h2>
        <p>Trợ từ nghi vấn <strong>吗 (ma)</strong> được đặt ở cuối câu trần thuật để biến nó thành câu hỏi Có/Không.</p>
        <p><strong>Cấu trúc:</strong> Chủ ngữ + Vị ngữ + 吗?</p>
        <p><strong>Ví dụ:</strong></p>
        <blockquote>
            <p>你好吗？(Nǐ hǎo ma?)</p>
            <p>Dịch: Bạn có khỏe không?</p>
        </blockquote>
        ';

        foreach ($lessons as $index => $lesson) {
            $lesson->content = ($index % 2 == 0) ? $content1 : $content2;
            $lesson->save();
        }
    }
}
