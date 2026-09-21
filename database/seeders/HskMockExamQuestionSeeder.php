<?php

namespace Database\Seeders;

use App\Models\Question;
use Illuminate\Database\Seeder;

class HskMockExamQuestionSeeder extends Seeder
{
    public function run(): void
    {
        $questions = array (
  0 => 
  array (
    'hsk_level' => 1,
    'exam_standard' => 'hsk_2_0',
    'skill_type' => 'listening',
    'question_type' => 'picture_true_false',
    'media_type' => 'image_audio',
    'difficulty' => 'starter',
    'image' => 'images/hsk/mock/hsk1/he_cha.svg',
    'image_alt' => 'Uống trà',
    'question' => '听录音判断对错：听到的内容与图片是否一致？',
    'pinyin' => 'hē chá',
    'audio_text' => '喝茶',
    'options' => array (0 => '对', 1 => '错'),
    'correct_answer' => '对',
    'explanation' => '录音播放 "喝茶" (hē chá - uống trà), hình ảnh minh họa tách trà bốc khói nên phán đoán ĐÚNG (对).',
    'sort_order' => 1,
  ),
  1 => 
  array (
    'hsk_level' => 1,
    'exam_standard' => 'hsk_2_0',
    'skill_type' => 'listening',
    'question_type' => 'picture_true_false',
    'media_type' => 'image_audio',
    'difficulty' => 'starter',
    'image' => 'images/hsk/mock/hsk1/pingguo.svg',
    'image_alt' => 'Quả táo',
    'question' => '听录音判断对错：听到的内容与图片是否一致？',
    'pinyin' => 'kàn shū',
    'audio_text' => '看书',
    'options' => array (0 => '对', 1 => '错'),
    'correct_answer' => '错',
    'explanation' => '录音播放 "看书" (kàn shū - đọc sách), nhưng hình ảnh là quả táo (苹果 píngguǒ), chọn SAI (错).',
    'sort_order' => 2,
  ),
  2 => 
  array (
    'hsk_level' => 1,
    'exam_standard' => 'hsk_2_0',
    'skill_type' => 'listening',
    'question_type' => 'picture_true_false',
    'media_type' => 'image_audio',
    'difficulty' => 'starter',
    'image' => 'images/hsk/mock/hsk1/da_dianhua.svg',
    'image_alt' => 'Gọi điện thoại',
    'question' => '听录音判断对错：听到的内容与图片是否一致？',
    'pinyin' => 'dǎ diànhuà',
    'audio_text' => '打电话',
    'options' => array (0 => '对', 1 => '错'),
    'correct_answer' => '对',
    'explanation' => '录音播放 "打电话" (dǎ diànhuà - gọi điện thoại), hình ảnh minh họa cuộc gọi điện thoại, chọn ĐÚNG (对).',
    'sort_order' => 3,
  ),
  3 => 
  array (
    'hsk_level' => 1,
    'exam_standard' => 'hsk_2_0',
    'skill_type' => 'listening',
    'question_type' => 'picture_true_false',
    'media_type' => 'image_audio',
    'difficulty' => 'starter',
    'image' => 'images/hsk/mock/hsk1/xia_yu.svg',
    'image_alt' => 'Trời mưa',
    'question' => '听录音判断对错：听到的内容与图片是否一致？',
    'pinyin' => 'xià yǔ',
    'audio_text' => '下雨',
    'options' => array (0 => '对', 1 => '错'),
    'correct_answer' => '对',
    'explanation' => '录音播放 "下雨" (xià yǔ - trời mưa), hình ảnh đám mây đang đổ mưa, chọn ĐÚNG (对).',
    'sort_order' => 4,
  ),
  4 => 
  array (
    'hsk_level' => 1,
    'exam_standard' => 'hsk_2_0',
    'skill_type' => 'listening',
    'question_type' => 'picture_true_false',
    'media_type' => 'image_audio',
    'difficulty' => 'starter',
    'image' => 'images/hsk/mock/hsk1/feiji.svg',
    'image_alt' => 'Máy bay',
    'question' => '听录音判断对错：听到的内容与图片是否一致？',
    'pinyin' => 'chī mǐfàn',
    'audio_text' => '吃米饭',
    'options' => array (0 => '对', 1 => '错'),
    'correct_answer' => '错',
    'explanation' => '录音播放 "吃米饭" (chī mǐfàn - ăn cơm), nhưng hình ảnh là chiếc máy bay (飞机), chọn SAI (错).',
    'sort_order' => 5,
  ),
  5 => 
  array (
    'hsk_level' => 1,
    'exam_standard' => 'hsk_2_0',
    'skill_type' => 'listening',
    'question_type' => 'picture_true_false',
    'media_type' => 'image_audio',
    'difficulty' => 'starter',
    'image' => 'images/hsk/mock/hsk1/shuijiao.svg',
    'image_alt' => 'Đi ngủ',
    'question' => '听录音判断对错：听到的内容与图片是否一致？',
    'pinyin' => 'shuìjiào',
    'audio_text' => '睡觉',
    'options' => array (0 => '对', 1 => '错'),
    'correct_answer' => '对',
    'explanation' => '录音播放 "睡觉" (shuìjiào - đi ngủ), hình ảnh chiếc giường ngủ êm ái, chọn ĐÚNG (对).',
    'sort_order' => 6,
  ),
  6 => 
  array (
    'hsk_level' => 1,
    'exam_standard' => 'hsk_2_0',
    'skill_type' => 'listening',
    'question_type' => 'picture_choice',
    'media_type' => 'image_audio',
    'difficulty' => 'starter',
    'image_set' => array (
      0 => array('key' => 'A', 'image' => 'images/hsk/mock/hsk1/chuzuche.svg', 'alt' => 'Taxi'),
      1 => array('key' => 'B', 'image' => 'images/hsk/mock/hsk1/feiji.svg', 'alt' => 'Máy bay'),
      2 => array('key' => 'C', 'image' => 'images/hsk/mock/hsk1/yizi.svg', 'alt' => 'Ghế'),
    ),
    'question' => '听录音，选择与录音内容相符的图片：',
    'pinyin' => 'Tā zuò chūzūchē qù xuéxiào.',
    'audio_text' => '他坐出租车去学校。',
    'options' => array (0 => 'A', 1 => 'B', 2 => 'C'),
    'correct_answer' => 'A',
    'explanation' => '录音内容: "他坐出租车去学校" (Anh ấy đi taxi đến trường). Bức tranh A là taxi (出租车).',
    'sort_order' => 7,
  ),
  7 => 
  array (
    'hsk_level' => 1,
    'exam_standard' => 'hsk_2_0',
    'skill_type' => 'listening',
    'question_type' => 'picture_choice',
    'media_type' => 'image_audio',
    'difficulty' => 'starter',
    'image_set' => array (
      0 => array('key' => 'A', 'image' => 'images/hsk/mock/hsk1/gou.svg', 'alt' => 'Chó'),
      1 => array('key' => 'B', 'image' => 'images/hsk/mock/hsk1/mao.svg', 'alt' => 'Mèo'),
      2 => array('key' => 'C', 'image' => 'images/hsk/mock/hsk1/pingguo.svg', 'alt' => 'Táo'),
    ),
    'question' => '听录音，选择与录音内容相符的图片：',
    'pinyin' => 'Nà zhī xiǎomāo zài yǐzi xiàmian.',
    'audio_text' => '那只小猫在椅子下面。',
    'options' => array (0 => 'A', 1 => 'B', 2 => 'C'),
    'correct_answer' => 'B',
    'explanation' => '录音内容: "那只小猫在椅子下面" (Chú mèo con kia ở dưới ghế). Bức tranh B là con mèo (猫).',
    'sort_order' => 8,
  ),
  8 => 
  array (
    'hsk_level' => 1,
    'exam_standard' => 'hsk_2_0',
    'skill_type' => 'listening',
    'question_type' => 'picture_choice',
    'media_type' => 'image_audio',
    'difficulty' => 'starter',
    'image_set' => array (
      0 => array('key' => 'A', 'image' => 'images/hsk/mock/hsk1/laoshi.svg', 'alt' => 'Giáo viên'),
      1 => array('key' => 'B', 'image' => 'images/hsk/mock/hsk1/yisheng.svg', 'alt' => 'Bác sĩ'),
      2 => array('key' => 'C', 'image' => 'images/hsk/mock/hsk1/chuzuche.svg', 'alt' => 'Taxi'),
    ),
    'question' => '听录音，选择与录音内容相符的图片：',
    'pinyin' => 'Lǐ lǎoshī zài xuéxiào kàn shū.',
    'audio_text' => '李老师在学校看书。',
    'options' => array (0 => 'A', 1 => 'B', 2 => 'C'),
    'correct_answer' => 'A',
    'explanation' => '录音内容: "李老师在学校看书" (Cô giáo Lý đang đọc sách ở trường). Bức tranh A là cô giáo (老师).',
    'sort_order' => 9,
  ),
  9 => 
  array (
    'hsk_level' => 1,
    'exam_standard' => 'hsk_2_0',
    'skill_type' => 'listening',
    'question_type' => 'picture_choice',
    'media_type' => 'image_audio',
    'difficulty' => 'starter',
    'image_set' => array (
      0 => array('key' => 'A', 'image' => 'images/hsk/mock/hsk1/xia_yu.svg', 'alt' => 'Mưa'),
      1 => array('key' => 'B', 'image' => 'images/hsk/mock/hsk1/shangdian.svg', 'alt' => 'Cửa hàng'),
      2 => array('key' => 'C', 'image' => 'images/hsk/mock/hsk1/shuijiao.svg', 'alt' => 'Ngủ'),
    ),
    'question' => '听录音，选择与录音内容相符的图片：',
    'pinyin' => 'Wǒ xiǎng qù shāngdiàn mǎi dōngxi.',
    'audio_text' => '我想去商店买东西。',
    'options' => array (0 => 'A', 1 => 'B', 2 => 'C'),
    'correct_answer' => 'B',
    'explanation' => '录音内容: "我想去商店买东西" (Tôi muốn đến cửa hàng mua đồ). Bức tranh B là cửa hàng (商店).',
    'sort_order' => 10,
  ),
  10 => 
  array (
    'hsk_level' => 1,
    'exam_standard' => 'hsk_2_0',
    'skill_type' => 'listening',
    'question_type' => 'picture_choice',
    'media_type' => 'image_audio',
    'difficulty' => 'starter',
    'image_set' => array (
      0 => array('key' => 'A', 'image' => 'images/hsk/mock/hsk1/he_cha.svg', 'alt' => 'Uống trà'),
      1 => array('key' => 'B', 'image' => 'images/hsk/mock/hsk1/chi_fan.svg', 'alt' => 'Ăn cơm'),
      2 => array('key' => 'C', 'image' => 'images/hsk/mock/hsk1/pingguo.svg', 'alt' => 'Quả táo'),
    ),
    'question' => '听录音，选择与录音内容相符的图片：',
    'pinyin' => 'Zhuōzi shàng yǒu sān gè hóng píngguǒ.',
    'audio_text' => '桌子上有三个红苹果。',
    'options' => array (0 => 'A', 1 => 'B', 2 => 'C'),
    'correct_answer' => 'C',
    'explanation' => '录音内容: "桌子上有三个红苹果" (Trên bàn có ba quả táo đỏ). Bức tranh C là quả táo (苹果).',
    'sort_order' => 11,
  ),
  11 => 
  array (
    'hsk_level' => 1,
    'exam_standard' => 'hsk_2_0',
    'skill_type' => 'listening',
    'question_type' => 'picture_choice',
    'media_type' => 'image_audio',
    'difficulty' => 'starter',
    'image_set' => array (
      0 => array('key' => 'A', 'image' => 'images/hsk/mock/hsk1/yisheng.svg', 'alt' => 'Bác sĩ'),
      1 => array('key' => 'B', 'image' => 'images/hsk/mock/hsk1/xuesheng.svg', 'alt' => 'Học sinh'),
      2 => array('key' => 'C', 'image' => 'images/hsk/mock/hsk1/da_dianhua.svg', 'alt' => 'Điện thoại'),
    ),
    'question' => '听录音，选择与录音内容相符的图片：',
    'pinyin' => 'Wáng xiānsheng shì yīyuàn de yīshēng.',
    'audio_text' => '王先生是医院的医生。',
    'options' => array (0 => 'A', 1 => 'B', 2 => 'C'),
    'correct_answer' => 'A',
    'explanation' => '录音内容: "王先生是医院的医生" (Ông Vương là bác sĩ của bệnh viện). Bức tranh A là bác sĩ (医生).',
    'sort_order' => 12,
  ),
  12 => 
  array (
    'hsk_level' => 1,
    'exam_standard' => 'hsk_2_0',
    'skill_type' => 'reading',
    'question_type' => 'picture_true_false',
    'media_type' => 'image',
    'difficulty' => 'starter',
    'image' => 'images/hsk/mock/hsk1/feiji.svg',
    'image_alt' => 'Máy bay',
    'question' => '飞机',
    'pinyin' => 'fēijī',
    'audio_text' => NULL,
    'options' => array (0 => '对', 1 => '错'),
    'correct_answer' => '对',
    'explanation' => 'Từ vựng "飞机" (fēijī - máy bay) khớp hoàn toàn với hình chiếc máy bay, chọn ĐÚNG (对).',
    'sort_order' => 13,
  ),
  13 => 
  array (
    'hsk_level' => 1,
    'exam_standard' => 'hsk_2_0',
    'skill_type' => 'reading',
    'question_type' => 'picture_true_false',
    'media_type' => 'image',
    'difficulty' => 'starter',
    'image' => 'images/hsk/mock/hsk1/mao.svg',
    'image_alt' => 'Con mèo',
    'question' => '椅子',
    'pinyin' => 'yǐzi',
    'audio_text' => NULL,
    'options' => array (0 => '对', 1 => '错'),
    'correct_answer' => '错',
    'explanation' => 'Từ vựng "椅子" (yǐzi - cái ghế), nhưng hình ảnh là con mèo (猫 māo), chọn SAI (错).',
    'sort_order' => 14,
  ),
  14 => 
  array (
    'hsk_level' => 1,
    'exam_standard' => 'hsk_2_0',
    'skill_type' => 'reading',
    'question_type' => 'picture_true_false',
    'media_type' => 'image',
    'difficulty' => 'starter',
    'image' => 'images/hsk/mock/hsk1/chi_fan.svg',
    'image_alt' => 'Ăn cơm',
    'question' => '吃饭',
    'pinyin' => 'chī fàn',
    'audio_text' => NULL,
    'options' => array (0 => '对', 1 => '错'),
    'correct_answer' => '对',
    'explanation' => 'Từ vựng "吃饭" (chī fàn - ăn cơm) khớp với hình bát cơm và đôi đũa, chọn ĐÚNG (对).',
    'sort_order' => 15,
  ),
  15 => 
  array (
    'hsk_level' => 1,
    'exam_standard' => 'hsk_2_0',
    'skill_type' => 'reading',
    'question_type' => 'picture_true_false',
    'media_type' => 'image',
    'difficulty' => 'starter',
    'image' => 'images/hsk/mock/hsk1/gou.svg',
    'image_alt' => 'Con chó',
    'question' => '狗',
    'pinyin' => 'gǒu',
    'audio_text' => NULL,
    'options' => array (0 => '对', 1 => '错'),
    'correct_answer' => '对',
    'explanation' => 'Từ vựng "狗" (gǒu - con chó) khớp hoàn toàn với hình chú chó, chọn ĐÚNG (对).',
    'sort_order' => 16,
  ),
  16 => 
  array (
    'hsk_level' => 1,
    'exam_standard' => 'hsk_2_0',
    'skill_type' => 'reading',
    'question_type' => 'picture_true_false',
    'media_type' => 'image',
    'difficulty' => 'starter',
    'image' => 'images/hsk/mock/hsk1/he_cha.svg',
    'image_alt' => 'Uống trà',
    'question' => '下雨',
    'pinyin' => 'xià yǔ',
    'audio_text' => NULL,
    'options' => array (0 => '对', 1 => '错'),
    'correct_answer' => '错',
    'explanation' => 'Từ vựng "下雨" (xià yǔ - trời mưa), nhưng tranh vẽ là tách trà nóng (喝茶), chọn SAI (错).',
    'sort_order' => 17,
  ),
  17 => 
  array (
    'hsk_level' => 1,
    'exam_standard' => 'hsk_2_0',
    'skill_type' => 'reading',
    'question_type' => 'picture_true_false',
    'media_type' => 'image',
    'difficulty' => 'starter',
    'image' => 'images/hsk/mock/hsk1/kan_shu.svg',
    'image_alt' => 'Đọc sách',
    'question' => '看书',
    'pinyin' => 'kàn shū',
    'audio_text' => NULL,
    'options' => array (0 => '对', 1 => '错'),
    'correct_answer' => '对',
    'explanation' => 'Từ vựng "看书" (kàn shū - đọc sách) khớp với hình cuốn sách đang mở, chọn ĐÚNG (对).',
    'sort_order' => 18,
  ),
  18 => 
  array (
    'hsk_level' => 1,
    'exam_standard' => 'hsk_2_0',
    'skill_type' => 'reading',
    'question_type' => 'picture_choice',
    'media_type' => 'image',
    'difficulty' => 'starter',
    'image_set' => array (
      0 => array('key' => 'A', 'image' => 'images/hsk/mock/hsk1/xuesheng.svg', 'alt' => 'Học sinh'),
      1 => array('key' => 'B', 'image' => 'images/hsk/mock/hsk1/yisheng.svg', 'alt' => 'Bác sĩ'),
      2 => array('key' => 'C', 'image' => 'images/hsk/mock/hsk1/chuzuche.svg', 'alt' => 'Taxi'),
    ),
    'question' => '看句子选择相符的图片：我们都是学生。',
    'pinyin' => 'Wǒmen dōu shì xuéshēng.',
    'audio_text' => NULL,
    'options' => array (0 => 'A', 1 => 'B', 2 => 'C'),
    'correct_answer' => 'A',
    'explanation' => 'Câu văn: "我们都是学生" (Chúng tôi đều là học sinh). Bức tranh A là học sinh đeo ba lô (学生).',
    'sort_order' => 19,
  ),
  19 => 
  array (
    'hsk_level' => 1,
    'exam_standard' => 'hsk_2_0',
    'skill_type' => 'reading',
    'question_type' => 'picture_choice',
    'media_type' => 'image',
    'difficulty' => 'starter',
    'image_set' => array (
      0 => array('key' => 'A', 'image' => 'images/hsk/mock/hsk1/da_dianhua.svg', 'alt' => 'Điện thoại'),
      1 => array('key' => 'B', 'image' => 'images/hsk/mock/hsk1/shuijiao.svg', 'alt' => 'Đi ngủ'),
      2 => array('key' => 'C', 'image' => 'images/hsk/mock/hsk1/chi_fan.svg', 'alt' => 'Ăn cơm'),
    ),
    'question' => '看句子选择相符的图片：他在房间里睡觉。',
    'pinyin' => 'Tā zài fángjiān lǐ shuìjiào.',
    'audio_text' => NULL,
    'options' => array (0 => 'A', 1 => 'B', 2 => 'C'),
    'correct_answer' => 'B',
    'explanation' => 'Câu văn: "他在房间里睡觉" (Anh ấy đang ngủ trong phòng). Bức tranh B là giường ngủ (睡觉).',
    'sort_order' => 20,
  ),
  20 => 
  array (
    'hsk_level' => 1,
    'exam_standard' => 'hsk_2_0',
    'skill_type' => 'reading',
    'question_type' => 'picture_choice',
    'media_type' => 'image',
    'difficulty' => 'starter',
    'image_set' => array (
      0 => array('key' => 'A', 'image' => 'images/hsk/mock/hsk1/yizi.svg', 'alt' => 'Cái ghế'),
      1 => array('key' => 'B', 'image' => 'images/hsk/mock/hsk1/pingguo.svg', 'alt' => 'Quả táo'),
      2 => array('key' => 'C', 'image' => 'images/hsk/mock/hsk1/feiji.svg', 'alt' => 'Máy bay'),
    ),
    'question' => '看句子选择相符的图片：请坐在椅子上。',
    'pinyin' => 'Qǐng zuò zài yǐzi shàng.',
    'audio_text' => NULL,
    'options' => array (0 => 'A', 1 => 'B', 2 => 'C'),
    'correct_answer' => 'A',
    'explanation' => 'Câu văn: "请坐在椅子上" (Xin mời ngồi lên ghế). Bức tranh A là cái ghế (椅子).',
    'sort_order' => 21,
  ),
  21 => 
  array (
    'hsk_level' => 1,
    'exam_standard' => 'hsk_2_0',
    'skill_type' => 'reading',
    'question_type' => 'picture_choice',
    'media_type' => 'image',
    'difficulty' => 'starter',
    'image_set' => array (
      0 => array('key' => 'A', 'image' => 'images/hsk/mock/hsk1/xia_yu.svg', 'alt' => 'Mưa'),
      1 => array('key' => 'B', 'image' => 'images/hsk/mock/hsk1/he_cha.svg', 'alt' => 'Uống trà'),
      2 => array('key' => 'C', 'image' => 'images/hsk/mock/hsk1/shangdian.svg', 'alt' => 'Cửa hàng'),
    ),
    'question' => '看句子选择相符的图片：妈妈去商店买苹果。',
    'pinyin' => 'Māma qù shāngdiàn mǎi píngguǒ.',
    'audio_text' => NULL,
    'options' => array (0 => 'A', 1 => 'B', 2 => 'C'),
    'correct_answer' => 'C',
    'explanation' => 'Câu văn: "妈妈去商店买苹果" (Mẹ đi cửa hàng mua táo). Bức tranh C là cửa hàng (商店).',
    'sort_order' => 22,
  ),
  22 => 
  array (
    'hsk_level' => 1,
    'exam_standard' => 'hsk_2_0',
    'skill_type' => 'reading',
    'question_type' => 'picture_choice',
    'media_type' => 'image',
    'difficulty' => 'starter',
    'image_set' => array (
      0 => array('key' => 'A', 'image' => 'images/hsk/mock/hsk1/kan_shu.svg', 'alt' => 'Đọc sách'),
      1 => array('key' => 'B', 'image' => 'images/hsk/mock/hsk1/da_dianhua.svg', 'alt' => 'Điện thoại'),
      2 => array('key' => 'C', 'image' => 'images/hsk/mock/hsk1/gou.svg', 'alt' => 'Con chó'),
    ),
    'question' => '看句子选择相符的图片：他在打电话给朋友。',
    'pinyin' => 'Tā zài dǎ diànhuà gěi péngyou.',
    'audio_text' => NULL,
    'options' => array (0 => 'A', 1 => 'B', 2 => 'C'),
    'correct_answer' => 'B',
    'explanation' => 'Câu văn: "他在打电话给朋友" (Anh ấy đang gọi điện thoại cho bạn). Bức tranh B là gọi điện thoại (打电话).',
    'sort_order' => 23,
  ),
  23 => 
  array (
    'hsk_level' => 1,
    'exam_standard' => 'hsk_2_0',
    'skill_type' => 'reading',
    'question_type' => 'picture_choice',
    'media_type' => 'image',
    'difficulty' => 'starter',
    'image_set' => array (
      0 => array('key' => 'A', 'image' => 'images/hsk/mock/hsk1/feiji.svg', 'alt' => 'Máy bay'),
      1 => array('key' => 'B', 'image' => 'images/hsk/mock/hsk1/xia_yu.svg', 'alt' => 'Trời mưa'),
      2 => array('key' => 'C', 'image' => 'images/hsk/mock/hsk1/laoshi.svg', 'alt' => 'Giáo viên'),
    ),
    'question' => '看句子选择相符的图片：今天天气不好，下雨了。',
    'pinyin' => 'Jīntiān tiānqì bù hǎo, xià yǔ le.',
    'audio_text' => NULL,
    'options' => array (0 => 'A', 1 => 'B', 2 => 'C'),
    'correct_answer' => 'B',
    'explanation' => 'Câu văn: "今天天气不好，下雨了" (Hôm nay thời tiết xấu, trời mưa rồi). Bức tranh B là trời mưa (下雨).',
    'sort_order' => 24,
  ),
  24 => 
  array (
    'hsk_level' => 2,
    'skill_type' => 'listening',
    'difficulty' => 'elementary',
    'question' => 'Nghe đoạn hội thoại và cho biết hai người đang chuẩn bị làm gì:',
    'pinyin' => 'Nǐ kàn, fúwùyuán lái le, wǒmen diǎncài ba.',
    'audio_text' => '你看，服务员来了，我们点菜吧。',
    'options' => 
    array (
      0 => 'Đang gọi món ở nhà hàng',
      1 => 'Đang mua vé máy bay',
      2 => 'Đang chờ xe buýt',
      3 => 'Đang thanh toán tiền phòng',
    ),
    'correct_answer' => 'Đang gọi món ở nhà hàng',
    'explanation' => '服务员 = nhân viên phục vụ; 点菜 = gọi món ăn.',
    'sort_order' => 25,
  ),
  25 => 
  array (
    'hsk_level' => 2,
    'skill_type' => 'listening',
    'difficulty' => 'elementary',
    'question' => 'Nghe và cho biết người nói đi làm bằng phương tiện gì hàng ngày:',
    'pinyin' => 'Wǒ měitiān qídānchē qù shàngbān.',
    'audio_text' => '我每天骑单车去上班。',
    'options' => 
    array (
      0 => 'Đi xe đạp',
      1 => 'Đi tàu điện ngầm',
      2 => 'Đi xe buýt',
      3 => 'Đi bộ',
    ),
    'correct_answer' => 'Đi xe đạp',
    'explanation' => '骑单车 / 骑自行车 = đi xe đạp; 去上班 = đi làm.',
    'sort_order' => 26,
  ),
  26 => 
  array (
    'hsk_level' => 2,
    'skill_type' => 'listening',
    'difficulty' => 'elementary',
    'question' => 'Nghe và cho biết lý do người phụ nữ uống nhiều nước:',
    'pinyin' => 'Yīshēng shuō wǒ gǎnmào le, yào duō hē shuǐ, duō xiūxi.',
    'audio_text' => '医生说我感冒了，要多喝水，多休息。',
    'options' => 
    array (
      0 => 'Bị cảm nên bác sĩ dặn uống nhiều nước và nghỉ ngơi',
      1 => 'Thời tiết quá nóng',
      2 => 'Vì cô ấy vừa chạy bộ xong',
      3 => 'Vì cô ấy thích uống nước lọc',
    ),
    'correct_answer' => 'Bị cảm nên bác sĩ dặn uống nhiều nước và nghỉ ngơi',
    'explanation' => '感冒 = bị cảm sốt; 多喝水 = uống nhiều nước; 多休息 = nghỉ ngơi nhiều.',
    'sort_order' => 27,
  ),
  27 => 
  array (
    'hsk_level' => 2,
    'skill_type' => 'listening',
    'difficulty' => 'elementary',
    'question' => 'Nghe và cho biết địa điểm hai người muốn đi du lịch vào mùa hè:',
    'pinyin' => 'Jīnnián xiàtiān wǒmen xiǎng qù Běijīng lǚyóu.',
    'audio_text' => '今年夏天我们想去北京旅游。',
    'options' => 
    array (
      0 => 'Bắc Kinh',
      1 => 'Thượng Hải',
      2 => 'Quảng Châu',
      3 => 'Hải Nam',
    ),
    'correct_answer' => 'Bắc Kinh',
    'explanation' => '夏天 = mùa hè; 去北京旅游 = đi du lịch Bắc Kinh.',
    'sort_order' => 28,
  ),
  28 => 
  array (
    'hsk_level' => 2,
    'skill_type' => 'listening',
    'difficulty' => 'elementary',
    'question' => 'Nghe và xác nhận màu sắc chiếc áo người nói muốn mua:',
    'pinyin' => 'Zhè jiàn báisè de yīfu bǐ hēisè de piányi.',
    'audio_text' => '这件白色的衣服比黑色的便宜。',
    'options' => 
    array (
      0 => 'Màu trắng (白色)',
      1 => 'Màu đen (黑色)',
      2 => 'Màu đỏ (红色)',
      3 => 'Màu vàng (黄色)',
    ),
    'correct_answer' => 'Màu trắng (白色)',
    'explanation' => '白色 = màu trắng; 便宜 = rẻ; Chiếc áo màu trắng rẻ hơn chiếc màu đen.',
    'sort_order' => 29,
  ),
  29 => 
  array (
    'hsk_level' => 2,
    'skill_type' => 'listening',
    'difficulty' => 'elementary',
    'question' => 'Nghe và cho biết người nói bắt đầu học tiếng Trung từ khi nào:',
    'pinyin' => 'Wǒ shì qùnián jiǔ yuè kāishǐ xué Hànyǔ de.',
    'audio_text' => '我是去年九月开始学汉语的。',
    'options' => 
    array (
      0 => 'Tháng 9 năm ngoái',
      1 => 'Tháng 9 năm nay',
      2 => 'Tháng 8 năm ngoái',
      3 => '9 tháng trước',
    ),
    'correct_answer' => 'Tháng 9 năm ngoái',
    'explanation' => '去年 (qùnián) = năm ngoái; 九月 = tháng 9; 开始 = bắt đầu.',
    'sort_order' => 30,
  ),
  30 => 
  array (
    'hsk_level' => 2,
    'skill_type' => 'listening',
    'difficulty' => 'elementary',
    'question' => 'Nghe và xác định thời gian chuyến tàu khởi hành:',
    'pinyin' => 'Huǒchē shí diǎn shíwǔ fēn kāi.',
    'audio_text' => '火车十点十五分开。',
    'options' => 
    array (
      0 => '10 giờ 15 phút',
      1 => '10 giờ 50 phút',
      2 => '9 giờ 15 phút',
      3 => '11 giờ 15 phút',
    ),
    'correct_answer' => '10 giờ 15 phút',
    'explanation' => '十点十五分 (shí diǎn shíwǔ fēn) = 10 giờ 15 phút.',
    'sort_order' => 31,
  ),
  31 => 
  array (
    'hsk_level' => 2,
    'skill_type' => 'listening',
    'difficulty' => 'elementary',
    'question' => 'Nghe và cho biết người nói đang tìm đồ vật gì:',
    'pinyin' => 'Nǐ kàndào wǒ de shǒujī le ma? Zài zhuōzi shàng ma?',
    'audio_text' => '你看到我的手机了吗？在桌子上吗？',
    'options' => 
    array (
      0 => 'Điện thoại di động',
      1 => 'Chìa khóa nhà',
      2 => 'Ví tiền',
      3 => 'Quyển từ điển',
    ),
    'correct_answer' => 'Điện thoại di động',
    'explanation' => '手机 (shǒujī) = điện thoại di động.',
    'sort_order' => 32,
  ),
  32 => 
  array (
    'hsk_level' => 2,
    'skill_type' => 'listening',
    'difficulty' => 'elementary',
    'question' => 'Nghe và cho biết hôm nay hai người cùng đi đâu:',
    'pinyin' => 'Wǒmen yìqǐ qù diànyǐngyuàn kàn diànyǐng ba!',
    'audio_text' => '我们一起去电影院看电影吧！',
    'options' => 
    array (
      0 => 'Đi rạp chiếu phim',
      1 => 'Đi công viên dạo bộ',
      2 => 'Đi thư viện đọc sách',
      3 => 'Đi siêu thị mua thức ăn',
    ),
    'correct_answer' => 'Đi rạp chiếu phim',
    'explanation' => '电影院 = rạp chiếu phim; 看电影 = xem phim.',
    'sort_order' => 33,
  ),
  33 => 
  array (
    'hsk_level' => 2,
    'skill_type' => 'listening',
    'difficulty' => 'elementary',
    'question' => 'Nghe và cho biết người nói cảm thấy thế nào về bài thi:',
    'pinyin' => 'Jīntiān de kǎoshì bù nán, wǒ dōu huì zuò.',
    'audio_text' => '今天的考试不难，我都会做。',
    'options' => 
    array (
      0 => 'Bài thi không khó, đều biết làm',
      1 => 'Bài thi quá khó',
      2 => 'Không làm kịp giờ',
      3 => 'Chưa ôn bài nên điểm thấp',
    ),
    'correct_answer' => 'Bài thi không khó, đều biết làm',
    'explanation' => '不难 = không khó; 都会做 = đều biết làm.',
    'sort_order' => 34,
  ),
  34 => 
  array (
    'hsk_level' => 2,
    'skill_type' => 'listening',
    'difficulty' => 'elementary',
    'question' => 'Nghe và cho biết hai người hẹn gặp nhau ở đâu:',
    'pinyin' => 'Míngtiān zǎoshang bā diǎn zài xuéxiào ménkǒu jiàn.',
    'audio_text' => '明天早上八点在学校门口见。',
    'options' => 
    array (
      0 => 'Ở cổng trường học lúc 8 giờ sáng',
      1 => 'Ở trạm xe buýt lúc 8 giờ tối',
      2 => 'Ở thư viện lúc 9 giờ sáng',
      3 => 'Ở quán cà phê',
    ),
    'correct_answer' => 'Ở cổng trường học lúc 8 giờ sáng',
    'explanation' => '学校门口 = cổng trường học; 早上八点 = 8 giờ sáng.',
    'sort_order' => 35,
  ),
  35 => 
  array (
    'hsk_level' => 2,
    'skill_type' => 'listening',
    'difficulty' => 'elementary',
    'question' => 'Nghe và cho biết người này đang làm môn thể thao gì:',
    'pinyin' => 'Tā měi gè xīngqīliù dōu qù tī zúqiú.',
    'audio_text' => '他每个星期六都去踢足球。',
    'options' => 
    array (
      0 => 'Đá bóng (踢足球)',
      1 => 'Chơi bóng rổ (打篮球)',
      2 => 'Bơi lội (游泳)',
      3 => 'Chạy bộ (跑步)',
    ),
    'correct_answer' => 'Đá bóng (踢足球)',
    'explanation' => '踢足球 (tī zúqiú) = đá bóng.',
    'sort_order' => 36,
  ),
  36 => 
  array (
    'hsk_level' => 2,
    'skill_type' => 'reading',
    'difficulty' => 'elementary',
    'question' => 'Chọn liên từ thích hợp điền vào câu: "虽然天气很冷，____ 他还是去跑步了。"',
    'pinyin' => 'Suīrán tiānqì hěn lěng, ____ tā háishì qù pǎobù le.',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '但是 (dànshì)',
      1 => '因为 (yīnwèi)',
      2 => '所以 (suǒyǐ)',
      3 => '而且 (érqiě)',
    ),
    'correct_answer' => '但是 (dànshì)',
    'explanation' => 'Cặp liên từ nhượng bộ: 虽然...但是... (Tuy... nhưng...).',
    'sort_order' => 37,
  ),
  37 => 
  array (
    'hsk_level' => 2,
    'skill_type' => 'reading',
    'difficulty' => 'elementary',
    'question' => 'Chọn câu so sánh hơn ĐÚNG ngữ pháp HSK 2: "Hôm nay nóng hơn hôm qua."',
    'pinyin' => 'Jīntiān bǐ zuótiān rè.',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '今天比昨天热。',
      1 => '今天比昨天很热。',
      2 => '昨天比今天热。',
      3 => '今天更比昨天热。',
    ),
    'correct_answer' => '今天比昨天热。',
    'explanation' => 'Cấu trúc câu so sánh chữ 比: A + 比 + B + Tính từ (không dùng phó từ mức độ như 很 trước tính từ).',
    'sort_order' => 38,
  ),
  38 => 
  array (
    'hsk_level' => 2,
    'skill_type' => 'reading',
    'difficulty' => 'elementary',
    'question' => 'Trợ từ "过" (guò) trong câu "我去过一次北京。" biểu thị ý nghĩa gì?',
    'pinyin' => 'Wǒ qù guo yí cì Běijīng.',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => 'Từng trải qua kinh nghiệm trong quá khứ',
      1 => 'Hành động đang diễn ra',
      2 => 'Dự định tương lai',
      3 => 'Phủ định hành động',
    ),
    'correct_answer' => 'Từng trải qua kinh nghiệm trong quá khứ',
    'explanation' => 'Động từ + 过 biểu thị từng có kinh nghiệm/trải nghiệm làm việc gì trong quá khứ.',
    'sort_order' => 39,
  ),
  39 => 
  array (
    'hsk_level' => 2,
    'skill_type' => 'reading',
    'difficulty' => 'elementary',
    'question' => 'Chọn từ thích hợp điền vào chỗ trống: "这件衣服太贵了，便宜 ____ 吧。"',
    'pinyin' => 'Zhè jiàn yīfu tài guì le, piányi ____ ba.',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '一点儿 (yìdiǎnr)',
      1 => '有点儿 (yǒudiǎnr)',
      2 => '非常 (fēicháng)',
      3 => '特别 (tèbié)',
    ),
    'correct_answer' => '一点儿 (yìdiǎnr)',
    'explanation' => 'Tính từ + 一点儿 biểu thị mức độ nhẹ hơn khi mặc cả/yêu cầu (便宜一点儿 = rẻ hơn một chút).',
    'sort_order' => 40,
  ),
  40 => 
  array (
    'hsk_level' => 2,
    'skill_type' => 'reading',
    'difficulty' => 'elementary',
    'question' => 'Đọc đoạn văn sau và chọn câu trả lời đúng:\\n"哥哥比弟弟大三岁，今年哥哥二十岁。"\\n-> Hỏi em trai (弟弟) năm nay bao nhiêu tuổi?',
    'pinyin' => 'Dìdi jīnnián jǐ suì?',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '17 tuổi',
      1 => '23 tuổi',
      2 => '20 tuổi',
      3 => '18 tuổi',
    ),
    'correct_answer' => '17 tuổi',
    'explanation' => 'Anh trai hơn em trai 3 tuổi, anh trai 20 tuổi nên em trai 20 - 3 = 17 tuổi.',
    'sort_order' => 41,
  ),
  41 => 
  array (
    'hsk_level' => 2,
    'skill_type' => 'reading',
    'difficulty' => 'elementary',
    'question' => 'Chọn từ mang nghĩa là "chuẩn bị, sẵn sàng":',
    'pinyin' => 'zhǔn bèi',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '准备 (zhǔnbèi)',
      1 => '开始 (kāishǐ)',
      2 => '帮助 (bāngzhù)',
      3 => '介绍 (jièshào)',
    ),
    'correct_answer' => '准备 (zhǔnbèi)',
    'explanation' => '准备 = chuẩn bị; 帮助 = giúp đỡ; 介绍 = giới thiệu; 开始 = bắt đầu.',
    'sort_order' => 42,
  ),
  42 => 
  array (
    'hsk_level' => 2,
    'skill_type' => 'reading',
    'difficulty' => 'elementary',
    'question' => 'Điền từ chỉ phương hướng thích hợp: "学校在医院的 ____ 。"',
    'pinyin' => 'Xuéxiào zài yīyuàn de ____ .',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '左边 (zuǒbian)',
      1 => '晴天 (qíngtiān)',
      2 => '生病 (shēngbìng)',
      3 => '时间 (shíjiān)',
    ),
    'correct_answer' => '左边 (zuǒbian)',
    'explanation' => '左边 = bên trái (từ chỉ phương vị). Trường học ở bên trái bệnh viện.',
    'sort_order' => 43,
  ),
  43 => 
  array (
    'hsk_level' => 2,
    'skill_type' => 'reading',
    'difficulty' => 'elementary',
    'question' => 'Chọn trợ từ điền vào câu biểu thị hành động đang diễn ra: "弟弟正在看书 ____ 。"',
    'pinyin' => 'Dìdi zhèngzài kàn shū ____ .',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '呢 (ne)',
      1 => '吗 (ma)',
      2 => '了 (le)',
      3 => '的 (de)',
    ),
    'correct_answer' => '呢 (ne)',
    'explanation' => 'Cấu trúc biểu thị động tác đang tiếp diễn: 正在 + Động từ + 呢.',
    'sort_order' => 44,
  ),
  44 => 
  array (
    'hsk_level' => 2,
    'skill_type' => 'reading',
    'difficulty' => 'elementary',
    'question' => 'Từ nào dưới đây trái nghĩa với từ "慢" (màn - chậm)?',
    'pinyin' => 'màn',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '快 (kuài)',
      1 => '新 (xīn)',
      2 => '贵 (guì)',
      3 => '近 (jìn)',
    ),
    'correct_answer' => '快 (kuài)',
    'explanation' => '慢 (chậm) trái nghĩa với 快 (nhanh). 近 = gần; 贵 = đắt; 新 = mới.',
    'sort_order' => 45,
  ),
  45 => 
  array (
    'hsk_level' => 2,
    'skill_type' => 'reading',
    'difficulty' => 'elementary',
    'question' => 'Đọc câu và cho biết người nói có thích công việc mới không:\\n"虽然新工作很累，但是很有意思，我很喜欢。"',
    'pinyin' => 'Suīrán xīn gōngzuò hěn lèi, dànshì hěn yǒu yìsi, wǒ hěn xǐhuan.',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => 'Rất thích vì công việc thú vị dù mệt',
      1 => 'Không thích vì quá mệt',
      2 => 'Muốn đổi công việc khác',
      3 => 'Chưa có ý kiến',
    ),
    'correct_answer' => 'Rất thích vì công việc thú vị dù mệt',
    'explanation' => '很有意思 (rất thú vị), 我很喜欢 (tôi rất thích).',
    'sort_order' => 46,
  ),
  46 => 
  array (
    'hsk_level' => 2,
    'skill_type' => 'reading',
    'difficulty' => 'elementary',
    'question' => 'Chọn câu hỏi thời gian chính xác cho: "Buổi chiều mấy giờ chúng ta xuất phát?"',
    'pinyin' => 'Xiàwǔ jǐ diǎn wǒmen chūfā?',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '下午几点我们出发？',
      1 => '下午什么时候我们在哪儿？',
      2 => '我们怎么出发下午？',
      3 => '下午为什么出发？',
    ),
    'correct_answer' => '下午几点我们出发？',
    'explanation' => '下午几点 = buổi chiều mấy giờ; 出发 = xuất phát.',
    'sort_order' => 47,
  ),
  47 => 
  array (
    'hsk_level' => 2,
    'skill_type' => 'reading',
    'difficulty' => 'elementary',
    'question' => 'Chọn phó từ biểu thị "cũng / đều" thích hợp: "这些苹果很甜，那些苹果 ____ 很甜。"',
    'pinyin' => 'Zhèxiē píngguǒ hěn tián, nàxiē píngguǒ ____ hěn tián.',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '也 (yě)',
      1 => '就 (jiù)',
      2 => '才 (cái)',
      3 => '还 (hái)',
    ),
    'correct_answer' => '也 (yě)',
    'explanation' => '也 (yě) = cũng. Những quả táo này rất ngọt, những quả kia cũng rất ngọt.',
    'sort_order' => 48,
  ),
  48 => 
  array (
    'hsk_level' => 2,
    'skill_type' => 'reading',
    'difficulty' => 'elementary',
    'question' => 'Từ nào sau đây mang nghĩa là "giúp đỡ"?',
    'pinyin' => 'bāngzhù',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '帮助 (bāngzhù)',
      1 => '希望 (xīwàng)',
      2 => '欢迎 (huānyíng)',
      3 => '回答 (huídá)',
    ),
    'correct_answer' => '帮助 (bāngzhù)',
    'explanation' => '帮助 = giúp đỡ; 希望 = hy vọng; 欢迎 = hoan nghênh; 回答 = trả lời.',
    'sort_order' => 49,
  ),
  49 => 
  array (
    'hsk_level' => 2,
    'skill_type' => 'reading',
    'difficulty' => 'elementary',
    'question' => 'Chọn cấu trúc câu thích hợp: "从我家到学校 ____ 远。"',
    'pinyin' => 'Cóng wǒ jiā dào xuéxiào ____ yuǎn.',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '不 (bù)',
      1 => '没 (méi)',
      2 => '别 (bié)',
      3 => '无 (wú)',
    ),
    'correct_answer' => '不 (bù)',
    'explanation' => 'Phủ định tính từ 远 (xa) dùng 不 (không xa = 不远).',
    'sort_order' => 50,
  ),
  50 => 
  array (
    'hsk_level' => 2,
    'skill_type' => 'reading',
    'difficulty' => 'elementary',
    'question' => 'Tìm từ thích hợp điền vào chỗ trống: "外面下雨了，你出门别忘了带 ____ 。"',
    'pinyin' => 'Wàimiàn xià yǔ le, nǐ chūmén bié wàng le dài ____ .',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '雨伞 (yǔsǎn)',
      1 => '手表 (shǒubiǎo)',
      2 => '自行车 (zìxíngchē)',
      3 => '铅笔 (qiānbǐ)',
    ),
    'correct_answer' => '雨伞 (yǔsǎn)',
    'explanation' => '下雨 = trời mưa; 雨伞 = cái ô/dù.',
    'sort_order' => 51,
  ),
  51 => 
  array (
    'hsk_level' => 2,
    'skill_type' => 'reading',
    'difficulty' => 'elementary',
    'question' => 'Đọc hiểu mẩu tin ngắn: "小王今天生病了，不能来上班，他要去医院看医生。"\\n-> Hỏi hôm nay Tiểu Vương làm gì?',
    'pinyin' => 'Xiǎo Wáng qù yīyuàn kàn yīshēng.',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => 'Đi bệnh viện khám bác sĩ vì ốm',
      1 => 'Đi làm bình thường',
      2 => 'Đi du lịch cùng bạn bè',
      3 => 'Đi siêu thị mua sắm',
    ),
    'correct_answer' => 'Đi bệnh viện khám bác sĩ vì ốm',
    'explanation' => '生病了 = bị ốm; 去医院看医生 = đi bệnh viện khám bác sĩ.',
    'sort_order' => 52,
  ),
  52 => 
  array (
    'hsk_level' => 2,
    'skill_type' => 'reading',
    'difficulty' => 'elementary',
    'question' => 'Chọn từ thích hợp: "我听 ____ 老师说的话了。"',
    'pinyin' => 'Wǒ tīng ____ lǎoshī shuō de huà le.',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '懂 (dǒng)',
      1 => '错 (cuò)',
      2 => '走 (zǒu)',
      3 => '住 (zhù)',
    ),
    'correct_answer' => '懂 (dǒng)',
    'explanation' => 'Bổ ngữ kết quả: 听懂 = nghe hiểu.',
    'sort_order' => 53,
  ),
  53 => 
  array (
    'hsk_level' => 2,
    'skill_type' => 'reading',
    'difficulty' => 'elementary',
    'question' => 'Chọn chữ Hán có nghĩa là "sân bay":',
    'pinyin' => 'jī chǎng',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '机场',
      1 => '车站',
      2 => '饭馆',
      3 => '教室',
    ),
    'correct_answer' => '机场',
    'explanation' => '机场 = sân bay; 车站 = bến xe; 饭馆 = quán ăn; 教室 = phòng học.',
    'sort_order' => 54,
  ),
  54 => 
  array (
    'hsk_level' => 3,
    'skill_type' => 'listening',
    'difficulty' => 'intermediate',
    'question' => 'Nghe và xác định lý do nhân vật xin nghỉ phép:',
    'pinyin' => 'Duìbuqǐ, wǒ jīntiān gǎnmào fāshāo le, yào xiàng lǎobǎn qǐngjià.',
    'audio_text' => '对不起，我今天感冒发烧了，要向老板请假。',
    'options' => 
    array (
      0 => 'Bị cảm sốt xin nghỉ phép',
      1 => 'Bị trễ chuyến bay',
      2 => 'Đi công tác xa',
      3 => 'Xe bị hỏng giữa đường',
    ),
    'correct_answer' => 'Bị cảm sốt xin nghỉ phép',
    'explanation' => '感冒 (gǎnmào) = cảm cúm; 发烧 = sốt; 请假 = xin nghỉ.',
    'sort_order' => 55,
  ),
  55 => 
  array (
    'hsk_level' => 3,
    'skill_type' => 'listening',
    'difficulty' => 'intermediate',
    'question' => 'Nghe và cho biết người nói đang ở đâu:',
    'pinyin' => 'Qǐngwèn, qù túshūguǎn zěnme zǒu? Yìzhí wǎng qián zǒu jiù dào le.',
    'audio_text' => '请问，去图书馆怎么走？一直往前走就到了。',
    'options' => 
    array (
      0 => 'Đang hỏi đường đến thư viện',
      1 => 'Đang mua sách trong tiệm',
      2 => 'Đang ở trên xe buýt',
      3 => 'Đang ở nhà ăn',
    ),
    'correct_answer' => 'Đang hỏi đường đến thư viện',
    'explanation' => '图书馆 = thư viện; 怎么走 = đi thế nào; 一直往前走 = đi thẳng về phía trước.',
    'sort_order' => 56,
  ),
  56 => 
  array (
    'hsk_level' => 3,
    'skill_type' => 'listening',
    'difficulty' => 'intermediate',
    'question' => 'Nghe và cho biết hai người có quyết định đi leo núi không:',
    'pinyin' => 'Míngtiān tiānqì yùbào shuō yǒu dàyǔ, wǒmen háishì bié qù páshān le ba.',
    'audio_text' => '明天天气预报说有大雨，我们还是别去爬山了吧。',
    'options' => 
    array (
      0 => 'Không đi vì dự báo thời tiết có mưa to',
      1 => 'Vẫn đi dù trời mưa',
      2 => 'Đổi sang đi bơi',
      3 => 'Đợi tuần sau mới quyết định',
    ),
    'correct_answer' => 'Không đi vì dự báo thời tiết có mưa to',
    'explanation' => '天气预报 = dự báo thời tiết; 有大雨 = có mưa to; 别去爬山 = đừng đi leo núi.',
    'sort_order' => 57,
  ),
  57 => 
  array (
    'hsk_level' => 3,
    'skill_type' => 'listening',
    'difficulty' => 'intermediate',
    'question' => 'Nghe và cho biết cô gái cảm thấy món ăn thế nào:',
    'pinyin' => 'Zhège cài suīrán yǒudiǎnr là, dànshì fēicháng hǎochī.',
    'audio_text' => '这个菜虽然有点儿辣，但是非常好吃。',
    'options' => 
    array (
      0 => 'Hơi cay nhưng rất ngon',
      1 => 'Quá ngọt không ăn được',
      2 => 'Mặn và nguội',
      3 => 'Không ngon chút nào',
    ),
    'correct_answer' => 'Hơi cay nhưng rất ngon',
    'explanation' => '有点儿辣 = hơi cay; 非常好吃 = rất ngon miệng.',
    'sort_order' => 58,
  ),
  58 => 
  array (
    'hsk_level' => 3,
    'skill_type' => 'listening',
    'difficulty' => 'intermediate',
    'question' => 'Nghe và cho biết hai người hẹn gặp nhau vào lúc nào:',
    'pinyin' => 'Wǒmen xiàwǔ sān diǎn bàn zài kāfēiguǎn jiànmiàn ba.',
    'audio_text' => '我们下午三点半在咖啡馆见面吧。',
    'options' => 
    array (
      0 => '3 giờ 30 phút chiều',
      1 => '3 giờ đúng chiều',
      2 => '4 giờ 30 phút chiều',
      3 => '2 giờ 30 phút chiều',
    ),
    'correct_answer' => '3 giờ 30 phút chiều',
    'explanation' => '下午三点半 (sān diǎn bàn) = 3:30 chiều.',
    'sort_order' => 59,
  ),
  59 => 
  array (
    'hsk_level' => 3,
    'skill_type' => 'listening',
    'difficulty' => 'intermediate',
    'question' => 'Nghe và xác định giá trị của món đồ sau khi giảm giá:',
    'pinyin' => 'Zhè tiáo qúnzi yuánlái sānbǎi kuài, xiànzài dǎzhé zhǐ yào yībǎi wǔ.',
    'audio_text' => '这条裙子原来三百块，现在打折只要一百五。',
    'options' => 
    array (
      0 => '150 tệ',
      1 => '300 tệ',
      2 => '200 tệ',
      3 => '100 tệ',
    ),
    'correct_answer' => '150 tệ',
    'explanation' => '打折 = giảm giá; 只要一百五 = chỉ cần 150 tệ.',
    'sort_order' => 60,
  ),
  60 => 
  array (
    'hsk_level' => 3,
    'skill_type' => 'listening',
    'difficulty' => 'intermediate',
    'question' => 'Nghe và cho biết chàng trai đang tìm cái gì:',
    'pinyin' => 'Nǐ kàndào wǒ de yǎnjìng le ma? Wǒ yào kàn shū.',
    'audio_text' => '你看到我的眼镜了吗？我要看书。',
    'options' => 
    array (
      0 => 'Kính đeo mắt',
      1 => 'Cây bút',
      2 => 'Cuốn tập',
      3 => 'Ví tiền',
    ),
    'correct_answer' => 'Kính đeo mắt',
    'explanation' => '眼镜 (yǎnjìng) = kính đeo mắt (khác với 眼睛 = đôi mắt).',
    'sort_order' => 61,
  ),
  61 => 
  array (
    'hsk_level' => 3,
    'skill_type' => 'listening',
    'difficulty' => 'intermediate',
    'question' => 'Nghe và cho biết người nói dự định đi du lịch bao nhiêu ngày:',
    'pinyin' => 'Wǒ dǎsuàn zài Běijīng wánr yí gè xīngqī.',
    'audio_text' => '我打算在北京玩儿一个星期。',
    'options' => 
    array (
      0 => 'Một tuần (7 ngày)',
      1 => 'Ba ngày',
      2 => 'Hai tuần',
      3 => 'Một tháng',
    ),
    'correct_answer' => 'Một tuần (7 ngày)',
    'explanation' => '一个星期 = một tuần lễ.',
    'sort_order' => 62,
  ),
  62 => 
  array (
    'hsk_level' => 3,
    'skill_type' => 'listening',
    'difficulty' => 'intermediate',
    'question' => 'Nghe và cho biết người phụ nữ khuyên chàng trai điều gì:',
    'pinyin' => 'Nǐ tài lèi le, yīnggāi zǎodiǎnr shuìjiào, shǎoshàngwǎng.',
    'audio_text' => '你太累了，应该早点儿睡觉，少上网。',
    'options' => 
    array (
      0 => 'Nên đi ngủ sớm và ít lướt mạng',
      1 => 'Nên uống cà phê',
      2 => 'Nên đi tập gym',
      3 => 'Nên làm thêm giờ',
    ),
    'correct_answer' => 'Nên đi ngủ sớm và ít lướt mạng',
    'explanation' => '早点儿睡觉 = ngủ sớm một chút; 少上网 = ít lướt mạng.',
    'sort_order' => 63,
  ),
  63 => 
  array (
    'hsk_level' => 3,
    'skill_type' => 'listening',
    'difficulty' => 'intermediate',
    'question' => 'Nghe và cho biết người nói cảm thấy thế nào về kỳ thi này:',
    'pinyin' => 'Yīnwèi zhǔnbèi de hěn chōngfèn, suǒyǐ wǒ duì kǎoshì hěn yǒu xìnxīn.',
    'audio_text' => '因为准备得很充分，所以我对考试很有信心。',
    'options' => 
    array (
      0 => 'Rất tự tin vì đã chuẩn bị kỹ lưỡng',
      1 => 'Rất lo lắng',
      2 => 'Cảm thấy đề quá khó',
      3 => 'Không quan tâm đến kết quả',
    ),
    'correct_answer' => 'Rất tự tin vì đã chuẩn bị kỹ lưỡng',
    'explanation' => '准备充分 = chuẩn bị kỹ; 很有信心 = rất có lòng tự tin.',
    'sort_order' => 64,
  ),
  64 => 
  array (
    'hsk_level' => 3,
    'skill_type' => 'listening',
    'difficulty' => 'intermediate',
    'question' => 'Nghe và cho biết người nói làm nghề gì:',
    'pinyin' => 'Wǒ zài zhè jiā bīnguǎn dāng jīnglǐ yǐjīng sān nián le.',
    'audio_text' => '我在这家宾馆当经理已经三年了。',
    'options' => 
    array (
      0 => 'Giám đốc khách sạn',
      1 => 'Đầu bếp',
      2 => 'Lái xe taxi',
      3 => 'Bác sĩ',
    ),
    'correct_answer' => 'Giám đốc khách sạn',
    'explanation' => '宾馆 = khách sạn; 经理 = giám đốc/quản lý.',
    'sort_order' => 65,
  ),
  65 => 
  array (
    'hsk_level' => 3,
    'skill_type' => 'listening',
    'difficulty' => 'intermediate',
    'question' => 'Nghe và xác nhận phương thức thanh toán:',
    'pinyin' => 'Wǒ méiyǒu xiànjīn, kěyǐ shuākǎ ma?',
    'audio_text' => '我没有现金，可以刷卡吗？',
    'options' => 
    array (
      0 => 'Quẹt thẻ ngân hàng',
      1 => 'Trả bằng tiền mặt',
      2 => 'Chuyển khoản điện thoại',
      3 => 'Trả bằng phiếu mua hàng',
    ),
    'correct_answer' => 'Quẹt thẻ ngân hàng',
    'explanation' => '现金 = tiền mặt; 刷卡 = quẹt thẻ.',
    'sort_order' => 66,
  ),
  66 => 
  array (
    'hsk_level' => 3,
    'skill_type' => 'reading',
    'difficulty' => 'intermediate',
    'question' => 'Chọn cặp từ biểu thị nguyên nhân - kết quả: "____ 昨天睡得太晚，____ 今天早上起不来。"',
    'pinyin' => '____ zuótiān shuì de tài wǎn, ____ jīntiān zǎoshang qǐ bu lái.',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '因为...所以... (yīnwèi... suǒyǐ...)',
      1 => '不但...而且... (búdàn... érqiě...)',
      2 => '如果...就... (rúguǒ... jiù...)',
      3 => '只要...就... (zhǐyào... jiù...)',
    ),
    'correct_answer' => '因为...所以... (yīnwèi... suǒyǐ...)',
    'explanation' => '因为...所以... biểu thị mối quan hệ nguyên nhân - kết quả (Bởi vì... cho nên...).',
    'sort_order' => 67,
  ),
  67 => 
  array (
    'hsk_level' => 3,
    'skill_type' => 'reading',
    'difficulty' => 'intermediate',
    'question' => 'Chọn từ thích hợp điền vào câu: "他对中国历史非常 ____ 。"',
    'pinyin' => 'Tā duì Zhōngguó lìshǐ fēicháng ____ .',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '感兴趣 (gǎn xìngqù)',
      1 => '小心 (xiǎoxīn)',
      2 => '客气 (kèqi)',
      3 => '奇怪 (qíguài)',
    ),
    'correct_answer' => '感兴趣 (gǎn xìngqù)',
    'explanation' => 'Cấu trúc: 对...感兴趣 (có hứng thú với cái gì). Anh ấy rất có hứng thú với lịch sử Trung Quốc.',
    'sort_order' => 68,
  ),
  68 => 
  array (
    'hsk_level' => 3,
    'skill_type' => 'reading',
    'difficulty' => 'intermediate',
    'question' => 'Đọc hiểu đoạn văn: "李阿姨每天早晨都会去公园散步，这个习惯她坚持了十年。"\\n-> Điều gì giúp dì Lý có sức khỏe tốt?',
    'pinyin' => 'Lǐ āyí měitiān zǎochén sànbù...',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => 'Kiên trì thói quen đi bộ ở công viên mỗi sáng',
      1 => 'Uống nhiều thuốc bổ',
      2 => 'Ngủ nhiều trong ngày',
      3 => 'Ăn kiêng nghiêm ngặt',
    ),
    'correct_answer' => 'Kiên trì thói quen đi bộ ở công viên mỗi sáng',
    'explanation' => '去公园散步 = đi dạo ở công viên; 坚持了十年 = kiên trì suốt 10 năm.',
    'sort_order' => 69,
  ),
  69 => 
  array (
    'hsk_level' => 3,
    'skill_type' => 'reading',
    'difficulty' => 'intermediate',
    'question' => 'Chọn từ thích hợp điền vào chỗ trống: "这道数学题太难了，我想了半天也没 ____ 出来。"',
    'pinyin' => 'Zhè dào shùxué tí tài nán le, wǒ xiǎng le bàntiān yě méi ____ chūlái.',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '算 (suàn)',
      1 => '买 (mǎi)',
      2 => '写 (xiě)',
      3 => '借 (jiè)',
    ),
    'correct_answer' => '算 (suàn)',
    'explanation' => '算出来 = tính ra, giải ra (dùng cho bài toán 数学题).',
    'sort_order' => 70,
  ),
  70 => 
  array (
    'hsk_level' => 3,
    'skill_type' => 'reading',
    'difficulty' => 'intermediate',
    'question' => 'Chọn từ có nghĩa là "môi trường":',
    'pinyin' => 'huán jìng',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '环境 (huánjìng)',
      1 => '节目 (jiémù)',
      2 => '成绩 (chéngjì)',
      3 => '水平 (shuǐpíng)',
    ),
    'correct_answer' => '环境 (huánjìng)',
    'explanation' => '环境 = môi trường; 节目 = tiết mục; 成绩 = thành tích; 水平 = trình độ.',
    'sort_order' => 71,
  ),
  71 => 
  array (
    'hsk_level' => 3,
    'skill_type' => 'reading',
    'difficulty' => 'intermediate',
    'question' => 'Điền liên từ thích hợp: "____ 你明天有时间，我们 ____ 一起去看电影。"',
    'pinyin' => '____ nǐ míngtiān yǒu shíjiān, wǒmen ____ yìqǐ qù kàn diànyǐng.',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '如果...就... (rúguǒ... jiù...)',
      1 => '虽然...但是... (suīrán... dànshì...)',
      2 => '只有...才... (zhǐyǒu... cái...)',
      3 => '与其...不如... (yǔqí... bùrú...)',
    ),
    'correct_answer' => '如果...就... (rúguǒ... jiù...)',
    'explanation' => 'Nếu... thì...: 如果...就... giả thiết điều kiện.',
    'sort_order' => 72,
  ),
  72 => 
  array (
    'hsk_level' => 3,
    'skill_type' => 'reading',
    'difficulty' => 'intermediate',
    'question' => 'Từ nào đồng nghĩa với "突然" (đột nhiên, bất ngờ)?',
    'pinyin' => 'tū rán',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '忽然 (hūrán)',
      1 => '马上 (mǎshàng)',
      2 => '一直 (yìzhí)',
      3 => '经常 (jīngcháng)',
    ),
    'correct_answer' => '忽然 (hūrán)',
    'explanation' => '突然 và 忽然 đều có nghĩa là đột nhiên, bất thình lình.',
    'sort_order' => 73,
  ),
  73 => 
  array (
    'hsk_level' => 3,
    'skill_type' => 'reading',
    'difficulty' => 'intermediate',
    'question' => 'Đọc đoạn văn ngắn: "为了提高汉语水平，王强每天坚持读报纸和看中文新闻。"\\n-> Mục đích của Vương Cường là gì?',
    'pinyin' => 'Wèile tígāo Hànyǔ shuǐpíng...',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => 'Nâng cao trình độ tiếng Trung',
      1 => 'Kiếm tiền mua báo',
      2 => 'Học làm phóng viên',
      3 => 'Tìm việc làm mới',
    ),
    'correct_answer' => 'Nâng cao trình độ tiếng Trung',
    'explanation' => '为了提高汉语水平 = Để nâng cao trình độ Hán ngữ.',
    'sort_order' => 74,
  ),
  74 => 
  array (
    'hsk_level' => 3,
    'skill_type' => 'reading',
    'difficulty' => 'intermediate',
    'question' => 'Chọn từ thích hợp: "会议马上就要开始了，请大家安静 ____ 。"',
    'pinyin' => 'Huìyì mǎshàng jiù yào kāishǐ le, qǐng dàjiā ānjìng ____ .',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '下来 (xiàlai)',
      1 => '起来 (qǐlai)',
      2 => '过去 (guòqu)',
      3 => '出来 (chūlai)',
    ),
    'correct_answer' => '下来 (xiàlai)',
    'explanation' => 'Bổ ngữ xu hướng: 安静下来 = yên tĩnh trở lại, trật tự lại.',
    'sort_order' => 75,
  ),
  75 => 
  array (
    'hsk_level' => 3,
    'skill_type' => 'reading',
    'difficulty' => 'intermediate',
    'question' => 'Chọn từ biểu thị sự giải quyết: "这个问题我们必须尽快 ____ 。"',
    'pinyin' => 'Zhège wèntí wǒmen bìxū jǐnkuài ____ .',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '解决 (jiějué)',
      1 => '解释 (jiěshì)',
      2 => '决定 (juédìng)',
      3 => '离开 (líkāi)',
    ),
    'correct_answer' => '解决 (jiějué)',
    'explanation' => '解决问题 = giải quyết vấn đề. 解释 = giải thích; 决定 = quyết định.',
    'sort_order' => 76,
  ),
  76 => 
  array (
    'hsk_level' => 3,
    'skill_type' => 'reading',
    'difficulty' => 'intermediate',
    'question' => 'Từ nào chỉ cảm giác "thất vọng"?',
    'pinyin' => 'shī wàng',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '失望 (shīwàng)',
      1 => '难过 (nánguò)',
      2 => '着急 (zháojí)',
      3 => '害怕 (hàipà)',
    ),
    'correct_answer' => '失望 (shīwàng)',
    'explanation' => '失望 = thất vọng; 难过 = buồn bã; 着急 = sốt ruột; 害怕 = sợ hãi.',
    'sort_order' => 77,
  ),
  77 => 
  array (
    'hsk_level' => 3,
    'skill_type' => 'reading',
    'difficulty' => 'intermediate',
    'question' => 'Đọc đoạn thoại: "A: 你决定买哪辆车了吗？ B: 我还要再考虑一下。"\\n-> Người B có ý gì?',
    'pinyin' => 'Wǒ hái yào zài kǎolǜ yíxià.',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => 'Cần suy nghĩ, cân nhắc thêm',
      1 => 'Đã mua rồi',
      2 => 'Không mua nữa',
      3 => 'Bảo người A quyết định giúp',
    ),
    'correct_answer' => 'Cần suy nghĩ, cân nhắc thêm',
    'explanation' => '考虑 (kǎolǜ) = suy nghĩ, cân nhắc.',
    'sort_order' => 78,
  ),
  78 => 
  array (
    'hsk_level' => 3,
    'skill_type' => 'grammar',
    'difficulty' => 'intermediate',
    'question' => 'Sắp xếp các từ sau thành câu đúng ngữ pháp chữ 把: "1. 请 / 2. 交给老师 / 3. 把这本书"',
    'pinyin' => 'Qǐng bǎ zhè běn shū jiāogěi lǎoshī.',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '1 - 3 - 2 (请把这本书交给老师。)',
      1 => '3 - 1 - 2 (把这本书请交给老师。)',
      2 => '1 - 2 - 3 (请交给老师把这本书。)',
      3 => '2 - 1 - 3 (交给老师请把这本书。)',
    ),
    'correct_answer' => '1 - 3 - 2 (请把这本书交给老师。)',
    'explanation' => 'Cấu trúc câu chữ 把: Chủ ngữ (hoặc 请) + 把 + Tân ngữ + Động từ + Thành phần khác.',
    'sort_order' => 79,
  ),
  79 => 
  array (
    'hsk_level' => 3,
    'skill_type' => 'grammar',
    'difficulty' => 'intermediate',
    'question' => 'Sắp xếp các từ sau thành câu đúng ngữ pháp câu bị động chữ 被: "1. 吃了 / 2. 蛋糕 / 3. 被 / 4. 弟弟"',
    'pinyin' => 'Dàngāo bèi dìdi chī le.',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '2 - 3 - 4 - 1 (蛋糕被弟弟吃了。)',
      1 => '4 - 3 - 2 - 1 (弟弟被蛋糕吃了。)',
      2 => '2 - 1 - 3 - 4 (蛋糕吃了被弟弟。)',
      3 => '3 - 2 - 4 - 1 (被蛋糕弟弟吃了。)',
    ),
    'correct_answer' => '2 - 3 - 4 - 1 (蛋糕被弟弟吃了。)',
    'explanation' => 'Cấu trúc câu chữ 被: Đối tượng chịu tác động (Bánh kem) + 被 + Chủ thể gây tác động (Em trai) + Động từ (ăn mất rồi).',
    'sort_order' => 80,
  ),
  80 => 
  array (
    'hsk_level' => 3,
    'skill_type' => 'grammar',
    'difficulty' => 'intermediate',
    'question' => 'Chọn trợ từ kết cấu "地" (de) đặt ở vị trí chính xác: "小狗 (A) 高兴 (B) 跑 (C) 过来 (D)。"',
    'pinyin' => 'Xiǎogǒu gāoxìng de pǎo guòlai.',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => 'Vị trí (B): 小狗高兴地跑过来。',
      1 => 'Vị trí (A): 地小狗高兴跑过来。',
      2 => 'Vị trí (C): 小狗高兴跑地过来。',
      3 => 'Vị trí (D): 小狗高兴跑过来地。',
    ),
    'correct_answer' => 'Vị trí (B): 小狗高兴地跑过来。',
    'explanation' => 'Trợ từ kết cấu 地 đứng sau tính từ/trạng ngữ để bổ nghĩa cho động từ: 高兴地跑 = chạy lại một cách vui mừng.',
    'sort_order' => 81,
  ),
  81 => 
  array (
    'hsk_level' => 3,
    'skill_type' => 'grammar',
    'difficulty' => 'intermediate',
    'question' => 'Sắp xếp các từ thành câu hoàn chỉnh: "1. 越来越 / 2. 他的汉语 / 3. 好 / 4. 了"',
    'pinyin' => 'Tā de Hànyǔ yuè lái yuè hǎo le.',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '2 - 1 - 3 - 4 (他的汉语越来越好了。)',
      1 => '1 - 3 - 2 - 4 (越来越好他的汉语了。)',
      2 => '2 - 3 - 1 - 4 (他的汉语好越来越了。)',
      3 => '3 - 2 - 1 - 4 (好他的汉语越来越了。)',
    ),
    'correct_answer' => '2 - 1 - 3 - 4 (他的汉语越来越好了。)',
    'explanation' => 'Cấu trúc: 越来越 + Tính từ/Động từ tâm lý (ngày càng tốt hơn).',
    'sort_order' => 82,
  ),
  82 => 
  array (
    'hsk_level' => 3,
    'skill_type' => 'grammar',
    'difficulty' => 'intermediate',
    'question' => 'Chọn vị trí đúng của phó từ "又" biểu thị hành động lặp lại: "今天 (A) 他 (B) 迟到 (C) 了 (D)。"',
    'pinyin' => 'Jīntiān tā yòu chídào le.',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => 'Vị trí (B): 今天他又迟到了。',
      1 => 'Vị trí (A): 又今天他迟到了。',
      2 => 'Vị trí (C): 今天他迟到又了。',
      3 => 'Vị trí (D): 今天他迟到了又。',
    ),
    'correct_answer' => 'Vị trí (B): 今天他又迟到了。',
    'explanation' => '又 đứng trước động từ để biểu thị hành động đã lặp lại trong quá khứ: 又迟到 = lại đi muộn.',
    'sort_order' => 83,
  ),
  83 => 
  array (
    'hsk_level' => 3,
    'skill_type' => 'grammar',
    'difficulty' => 'intermediate',
    'question' => 'Sắp xếp câu: "1. 我 / 2. 骑车 / 3. 比 / 4. 走得快 / 5. 走路"',
    'pinyin' => 'Qíchē bǐ zǒulù zǒu de kuài.',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '2 - 3 - 5 - 4 (骑车比走路走得快。)',
      1 => '1 - 2 - 3 - 4 - 5 (我骑车比走得快走路。)',
      2 => '5 - 3 - 2 - 4 (走路比骑车走得快。)',
      3 => '2 - 5 - 3 - 4 (骑车走路比走得快。)',
    ),
    'correct_answer' => '2 - 3 - 5 - 4 (骑车比走路走得快。)',
    'explanation' => 'So sánh trạng thái: A + 比 + B + Động từ + 得 + Tính từ.',
    'sort_order' => 84,
  ),
  84 => 
  array (
    'hsk_level' => 3,
    'skill_type' => 'grammar',
    'difficulty' => 'intermediate',
    'question' => 'Chọn chữ Hán thích hợp điền vào câu: "请把空调关 ____ ，房间里太冷了。"',
    'pinyin' => 'Qǐng bǎ kōngtiáo guān ____ .',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '掉 (diào)',
      1 => '起 (qǐ)',
      2 => '开 (kāi)',
      3 => '在 (zài)',
    ),
    'correct_answer' => '掉 (diào)',
    'explanation' => '关掉 = tắt đi (关掉空调 = tắt máy điều hòa).',
    'sort_order' => 85,
  ),
  85 => 
  array (
    'hsk_level' => 3,
    'skill_type' => 'grammar',
    'difficulty' => 'intermediate',
    'question' => 'Sắp xếp câu: "1. 借了 / 2. 三本小说 / 3. 从图书馆 / 4. 他"',
    'pinyin' => 'Tā cóng túshūguǎn jiè le sān běn xiǎoshuō.',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '4 - 3 - 1 - 2 (他从图书馆借了三本小说。)',
      1 => '4 - 1 - 2 - 3 (他借了三本小说从图书馆。)',
      2 => '3 - 4 - 2 - 1 (从图书馆他三本小说借了。)',
      3 => '1 - 2 - 4 - 3 (借了三本小说他从图书馆。)',
    ),
    'correct_answer' => '4 - 3 - 1 - 2 (他从图书馆借了三本小说。)',
    'explanation' => 'Trật tự câu: Chủ ngữ (他) + Giới từ địa điểm (从图书馆) + Động từ (借了) + Tân ngữ (三本小说).',
    'sort_order' => 86,
  ),
  86 => 
  array (
    'hsk_level' => 3,
    'skill_type' => 'grammar',
    'difficulty' => 'intermediate',
    'question' => 'Điền trợ từ kết cấu "得" biểu thị mức độ: "今天大家玩儿 ____ 非常开心。"',
    'pinyin' => 'Jīntiān dàjiā wánr ____ fēicháng kāixīn.',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '得 (de)',
      1 => '的 (de)',
      2 => '地 (de)',
      3 => '了 (le)',
    ),
    'correct_answer' => '得 (de)',
    'explanation' => 'Bổ ngữ trạng thái/kết quả: Động từ (玩儿) + 得 + Mức độ (非常开心).',
    'sort_order' => 87,
  ),
  87 => 
  array (
    'hsk_level' => 3,
    'skill_type' => 'grammar',
    'difficulty' => 'intermediate',
    'question' => 'Sắp xếp câu hoàn chỉnh: "1. 完成 / 2. 这个任务 / 3. 必须在今天 / 4. 我们"',
    'pinyin' => 'Wǒmen bìxū zài jīntiān wánchéng zhège rènwu.',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '4 - 3 - 1 - 2 (我们必须在今天完成这个任务。)',
      1 => '1 - 2 - 4 - 3 (完成这个任务我们必须在今天。)',
      2 => '3 - 4 - 2 - 1 (必须在今天我们这个任务完成。)',
      3 => '4 - 1 - 2 - 3 (我们完成这个任务必须在今天。)',
    ),
    'correct_answer' => '4 - 3 - 1 - 2 (我们必须在今天完成这个任务。)',
    'explanation' => 'Chủ ngữ (我们) + Trợ động từ & Thời gian (必须在今天) + Động từ (完成) + Tân ngữ (这个任务).',
    'sort_order' => 88,
  ),
  88 => 
  array (
    'hsk_level' => 3,
    'skill_type' => 'grammar',
    'difficulty' => 'intermediate',
    'question' => 'Chọn từ thích hợp: "老师对学生要求很 ____ 。"',
    'pinyin' => 'Lǎoshī duì xuésheng yāoqiú hěn ____ .',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '严格 (yángé)',
      1 => '严肃 (yánsù)',
      2 => '紧 (jǐn)',
      3 => '硬 (yìng)',
    ),
    'correct_answer' => '严格 (yángé)',
    'explanation' => '要求严格 = yêu cầu nghiêm khắc.',
    'sort_order' => 89,
  ),
  89 => 
  array (
    'hsk_level' => 3,
    'skill_type' => 'grammar',
    'difficulty' => 'intermediate',
    'question' => 'Sắp xếp câu: "1. 放在 / 2. 请把 / 3. 桌子上 / 4. 行李箱"',
    'pinyin' => 'Qǐng bǎ xínglǐxiāng fàng zài zhuōzi shàng.',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '2 - 4 - 1 - 3 (请把行李箱放在桌子上。)',
      1 => '2 - 3 - 1 - 4 (请把桌子上放在行李箱。)',
      2 => '4 - 2 - 1 - 3 (行李箱请把放在桌子上。)',
      3 => '1 - 3 - 2 - 4 (放在桌子上请把行李箱。)',
    ),
    'correct_answer' => '2 - 4 - 1 - 3 (请把行李箱放在桌子上。)',
    'explanation' => 'Cấu trúc chữ 把 biểu thị xử lý và nơi chốn kết quả: 把 + Tân ngữ + 放在 + Địa điểm.',
    'sort_order' => 90,
  ),
  90 => 
  array (
    'hsk_level' => 4,
    'skill_type' => 'listening',
    'difficulty' => 'intermediate',
    'question' => 'Nghe và cho biết thông tin về cuộc họp:',
    'pinyin' => 'Yóuyú jīnglǐ chūchāi hái méi huílái, yuándìng de huìyì tuīchí dào xiàwǔ sān diǎn.',
    'audio_text' => '由于经理出差还没回来，原定的会议推迟到下午三点。',
    'options' => 
    array (
      0 => 'Cuộc họp bị hoãn đến 3 giờ chiều vì giám đốc chưa đi công tác về',
      1 => 'Cuộc họp bị hủy bỏ hoàn toàn',
      2 => 'Cuộc họp diễn ra lúc 9 giờ sáng',
      3 => 'Giám đốc đã về đúng giờ',
    ),
    'correct_answer' => 'Cuộc họp bị hoãn đến 3 giờ chiều vì giám đốc chưa đi công tác về',
    'explanation' => '推迟 = hoãn lại; 出差 = đi công tác; 下午三点 = 3 giờ chiều.',
    'sort_order' => 91,
  ),
  91 => 
  array (
    'hsk_level' => 4,
    'skill_type' => 'listening',
    'difficulty' => 'intermediate',
    'question' => 'Nghe và cho biết người nói đang kêu gọi hành động gì:',
    'pinyin' => 'Bǎohù huánjìng yīnggāi cóng shēnbiān de xiǎoshì zuòqǐ, bǐrú lājī fēnlèi hé jiéyuē yòngdiàn.',
    'audio_text' => '保护环境应该从身边的小事做起，比如垃圾分类和节约用电。',
    'options' => 
    array (
      0 => 'Phân loại rác và tiết kiệm điện để bảo vệ môi trường',
      1 => 'Quyên góp tiền cho quỹ môi trường',
      2 => 'Trồng cây gây rừng ở nông thôn',
      3 => 'Hạn chế đi máy bay',
    ),
    'correct_answer' => 'Phân loại rác và tiết kiệm điện để bảo vệ môi trường',
    'explanation' => '保护环境 = bảo vệ môi trường; 垃圾分类 = phân loại rác; 节约用电 = tiết kiệm điện.',
    'sort_order' => 92,
  ),
  92 => 
  array (
    'hsk_level' => 4,
    'skill_type' => 'listening',
    'difficulty' => 'intermediate',
    'question' => 'Nghe và cho biết chàng trai chuẩn bị tham gia sự kiện gì:',
    'pinyin' => 'Míngtiān zǎoshang wǒ yào qù yī jiā wàiqǐ miànshì, xīnli yǒudiǎnr jǐnzhāng.',
    'audio_text' => '明天早上我要去一家外企面试，心里有点儿紧张。',
    'options' => 
    array (
      0 => 'Đi phỏng vấn xin việc ở công ty nước ngoài',
      1 => 'Đi thi đại học',
      2 => 'Tham gia tiệc cưới đồng nghiệp',
      3 => 'Đi khám sức khỏe định kỳ',
    ),
    'correct_answer' => 'Đi phỏng vấn xin việc ở công ty nước ngoài',
    'explanation' => '外企 = doanh nghiệp nước ngoài; 面试 = phỏng vấn; 紧张 = hồi hộp/lo lắng.',
    'sort_order' => 93,
  ),
  93 => 
  array (
    'hsk_level' => 4,
    'skill_type' => 'listening',
    'difficulty' => 'intermediate',
    'question' => 'Nghe và cho biết cô gái đã thích nghi với môi trường mới chưa:',
    'pinyin' => 'Gāng dào zhèlǐ shí bù xíguàn, xiànzài wǒ yǐjīng jiéjiāo le hěn duō xīn péngyou, shìyìng de hěn hǎo.',
    'audio_text' => '刚到这里时不习惯，现在我已经结交了很多新朋友，适应得很好。',
    'options' => 
    array (
      0 => 'Đã kết bạn nhiều và thích nghi rất tốt',
      1 => 'Vẫn chưa quen môi trường sống',
      2 => 'Muốn chuyển về quê',
      3 => 'Không thích kết bạn mới',
    ),
    'correct_answer' => 'Đã kết bạn nhiều và thích nghi rất tốt',
    'explanation' => '结交新朋友 = kết bạn mới; 适应得很好 = thích nghi rất tốt.',
    'sort_order' => 94,
  ),
  94 => 
  array (
    'hsk_level' => 4,
    'skill_type' => 'listening',
    'difficulty' => 'intermediate',
    'question' => 'Nghe và cho biết người nói chia sẻ thói quen tốt nào:',
    'pinyin' => 'Měitiān shuìqián yuèdú bàn gè xiǎoshí, bùjǐn néng fàngsōng xīnqíng, hái néng zēngzhǎng zhīshi.',
    'audio_text' => '每天睡前阅读半个小时，不仅能放松心情，还能增长知识。',
    'options' => 
    array (
      0 => 'Đọc sách 30 phút trước khi ngủ',
      1 => 'Nghe nhạc cả đêm',
      2 => 'Uống trà thảo mộc',
      3 => 'Chạy bộ trước khi ngủ',
    ),
    'correct_answer' => 'Đọc sách 30 phút trước khi ngủ',
    'explanation' => '睡前阅读 = đọc sách trước khi ngủ; 放松心情 = thư giãn tâm trạng; 增长知识 = mở mang kiến thức.',
    'sort_order' => 95,
  ),
  95 => 
  array (
    'hsk_level' => 4,
    'skill_type' => 'listening',
    'difficulty' => 'intermediate',
    'question' => 'Nghe và cho biết vì sao hai người chọn đi tàu cao tốc:',
    'pinyin' => 'Zuò gāotiě yòu kuài yòu shūfu, hái bùyòng dānxīn tiānqì yuányīn wùdiǎn.',
    'audio_text' => '坐高铁又快又舒服，还不用担心天气原因误点。',
    'options' => 
    array (
      0 => 'Tàu cao tốc vừa nhanh vừa thoải mái, không lo trễ giờ do thời tiết',
      1 => 'Giá vé rẻ hơn xe buýt',
      2 => 'Vì sân bay quá xa trung tâm',
      3 => 'Vì muốn ngắm phong cảnh',
    ),
    'correct_answer' => 'Tàu cao tốc vừa nhanh vừa thoải mái, không lo trễ giờ do thời tiết',
    'explanation' => '高铁 = tàu cao tốc; 又快又舒服 = vừa nhanh vừa êm ái; 误点 = trễ giờ.',
    'sort_order' => 96,
  ),
  96 => 
  array (
    'hsk_level' => 4,
    'skill_type' => 'listening',
    'difficulty' => 'intermediate',
    'question' => 'Nghe và cho biết quan điểm về cuộc sống hôn nhân:',
    'pinyin' => 'Fūqī zhījiān zuì zhòngyào de shì hùxiāng lǐjiě hé zūnzhòng, ér bú shì bǐcǐ fùshǔ.',
    'audio_text' => '夫妻之间最重要的是互相理解和尊重，而不是彼此附属。',
    'options' => 
    array (
      0 => 'Quan trọng nhất là sự thấu hiểu và tôn trọng lẫn nhau',
      1 => 'Cần phải có nhiều của cải vật chất',
      2 => 'Phải sống chung với bố mẹ',
      3 => 'Cần có cùng sở thích',
    ),
    'correct_answer' => 'Quan trọng nhất là sự thấu hiểu và tôn trọng lẫn nhau',
    'explanation' => '互相理解 = thấu hiểu lẫn nhau; 尊重 = tôn trọng.',
    'sort_order' => 97,
  ),
  97 => 
  array (
    'hsk_level' => 4,
    'skill_type' => 'listening',
    'difficulty' => 'intermediate',
    'question' => 'Nghe và giải thích câu nói nổi tiếng:',
    'pinyin' => 'Jìhuà gǎnbushàng biànhuà, wǒmen bìxū suíshí zuòhǎo yìngbiàn de zhǔnbèi.',
    'audio_text' => '计划赶不上变化，我们必须随时做好应变的准备。',
    'options' => 
    array (
      0 => 'Kế hoạch không theo kịp biến hóa, cần chuẩn bị sẵn sàng ứng phó',
      1 => 'Kế hoạch đã lập thì không được đổi',
      2 => 'Không cần lên kế hoạch trước',
      3 => 'Thay đổi kế hoạch là sai lầm',
    ),
    'correct_answer' => 'Kế hoạch không theo kịp biến hóa, cần chuẩn bị sẵn sàng ứng phó',
    'explanation' => '计划赶不上变化 = kế hoạch không theo kịp biến hóa; 应变 = ứng biến.',
    'sort_order' => 98,
  ),
  98 => 
  array (
    'hsk_level' => 4,
    'skill_type' => 'listening',
    'difficulty' => 'intermediate',
    'question' => 'Nghe và cho biết lời khuyên của người mẹ:',
    'pinyin' => 'Tiānqì zhuǎn liáng le, chūmén jìde duō chuān jiàn wàitào, bié zháoliáng le.',
    'audio_text' => '天气转凉了，出门记得多穿件外套，别着凉了。',
    'options' => 
    array (
      0 => 'Trời trở lạnh, nhớ mặc thêm áo khoác tránh bị cảm',
      1 => 'Trời sắp mưa to nhớ mang dù',
      2 => 'Hãy ở nhà không nên ra đường',
      3 => 'Nên uống nước đá ít đi',
    ),
    'correct_answer' => 'Trời trở lạnh, nhớ mặc thêm áo khoác tránh bị cảm',
    'explanation' => '转凉 = trở lạnh; 多穿件外套 = mặc thêm áo khoác; 别着凉 = đừng để bị cảm lạnh.',
    'sort_order' => 99,
  ),
  99 => 
  array (
    'hsk_level' => 4,
    'skill_type' => 'listening',
    'difficulty' => 'intermediate',
    'question' => 'Nghe và cho biết lợi ích của việc rèn luyện thể thao:',
    'pinyin' => 'Chángqī jiānchí duànliàn shēntǐ, kěyǐ zēngqiáng miǎnyìlì, jiǎnshǎo shēngbìng de cìshù.',
    'audio_text' => '长期坚持锻炼身体，可以增强免疫力，减少生病的次数。',
    'options' => 
    array (
      0 => 'Tăng cường hệ miễn dịch và giảm nguy cơ mắc bệnh',
      1 => 'Giúp kiếm được nhiều tiền hơn',
      2 => 'Làm cho bận rộn hơn',
      3 => 'Giúp tăng cân nhanh chóng',
    ),
    'correct_answer' => 'Tăng cường hệ miễn dịch và giảm nguy cơ mắc bệnh',
    'explanation' => '锻炼身体 = rèn luyện thân thể; 增强免疫力 = tăng cường sức miễn dịch.',
    'sort_order' => 100,
  ),
  100 => 
  array (
    'hsk_level' => 4,
    'skill_type' => 'listening',
    'difficulty' => 'intermediate',
    'question' => 'Nghe và cho biết thông điệp tiết kiệm:',
    'pinyin' => 'Shuǐ shì shēngmìng zhī yuán, měi gè rén dōu yīnggāi cóng jiǎnshǎo làngfèi kāishǐ jiéshuǐ.',
    'audio_text' => '水是生命之源，每个人都应该从减少浪费开始节水。',
    'options' => 
    array (
      0 => 'Nước là nguồn sống, mọi người cần giảm lãng phí để tiết kiệm nước',
      1 => 'Cần xây thêm nhiều hồ nước',
      2 => 'Chỉ dùng nước máy đắt tiền',
      3 => 'Không nên tắm giặt thường xuyên',
    ),
    'correct_answer' => 'Nước là nguồn sống, mọi người cần giảm lãng phí để tiết kiệm nước',
    'explanation' => '生命之源 = nguồn gốc của sự sống; 节水 = tiết kiệm nước.',
    'sort_order' => 101,
  ),
  101 => 
  array (
    'hsk_level' => 4,
    'skill_type' => 'listening',
    'difficulty' => 'intermediate',
    'question' => 'Nghe và cho biết lời khuyên dành cho sinh viên mới tốt nghiệp:',
    'pinyin' => 'Gāng bìyè de dàxuéshēng búyào zhǐ kàn gōngzī, jīlěi gōngzuò jīngyàn gèng zhòngyào.',
    'audio_text' => '刚毕业的大学生不要只看工资，积累工作经验更重要。',
    'options' => 
    array (
      0 => 'Không nên chỉ chú trọng tiền lương, việc tích lũy kinh nghiệm làm việc quan trọng hơn',
      1 => 'Chỉ nên tìm việc có lương rất cao',
      2 => 'Không cần tích lũy kinh nghiệm',
      3 => 'Nên nghỉ ngơi 1 năm trước khi đi làm',
    ),
    'correct_answer' => 'Không nên chỉ chú trọng tiền lương, việc tích lũy kinh nghiệm làm việc quan trọng hơn',
    'explanation' => '积累经验 = tích lũy kinh nghiệm; 工资 = lương bổng.',
    'sort_order' => 102,
  ),
  102 => 
  array (
    'hsk_level' => 4,
    'skill_type' => 'reading',
    'difficulty' => 'intermediate',
    'question' => 'Chọn từ thích hợp điền vào câu: "这场比赛非常 ____ ，吸引了成千上万的观众。"',
    'pinyin' => 'Zhè chǎng bǐsài fēicháng ____ , xīyǐn le chéngqiānshàngwàn de guānzhòng.',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '精彩 (jīngcǎi)',
      1 => '经常 (jīngcháng)',
      2 => '经历 (jīnglì)',
      3 => '经济 (jīngjì)',
    ),
    'correct_answer' => '精彩 (jīngcǎi)',
    'explanation' => '精彩 = đặc sắc, tuyệt vời (dùng miêu tả trận đấu 比赛, tiết mục 节目).',
    'sort_order' => 103,
  ),
  103 => 
  array (
    'hsk_level' => 4,
    'skill_type' => 'reading',
    'difficulty' => 'intermediate',
    'question' => 'Chọn từ đồng nghĩa với "准确" trong ngữ cảnh phát âm: "他的发音很 ____ 。"',
    'pinyin' => 'Tā de fāyīn hěn ____ .',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '标准 (biāozhǔn)',
      1 => '随便 (suíbiàn)',
      2 => '复杂 (fùzá)',
      3 => '孤单 (gūdān)',
    ),
    'correct_answer' => '标准 (biāozhǔn)',
    'explanation' => '标准 = chuẩn, chuẩn mực, đồng nghĩa với 准确 khi nói về phát âm.',
    'sort_order' => 104,
  ),
  104 => 
  array (
    'hsk_level' => 4,
    'skill_type' => 'reading',
    'difficulty' => 'intermediate',
    'question' => 'Điền liên từ thích hợp: "哪怕困难再大，我们 ____ 要按时完成任务。"',
    'pinyin' => 'Nǎpà kùnnán zài dà, wǒmen ____ yào ànshí wánchéng rènwu.',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '也 (yě)',
      1 => '就 (jiù)',
      2 => '才 (cái)',
      3 => '都 (dōu)',
    ),
    'correct_answer' => '也 (yě)',
    'explanation' => 'Cặp liên từ nhượng bộ: 哪怕...也... (Cho dù... cũng...).',
    'sort_order' => 105,
  ),
  105 => 
  array (
    'hsk_level' => 4,
    'skill_type' => 'reading',
    'difficulty' => 'intermediate',
    'question' => 'Đọc hiểu đoạn văn: "虽然他经历过很多次失败，但他从未想过放弃。"\\n-> Nhận xét đúng nhất về người này:',
    'pinyin' => 'Suīrán tā jīnglì guò shībài...',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => 'Anh ấy rất kiên trì và giàu nghị lực',
      1 => 'Anh ấy luôn gặp may mắn',
      2 => 'Anh ấy đã từ bỏ mục tiêu',
      3 => 'Anh ấy không cần cố gắng',
    ),
    'correct_answer' => 'Anh ấy rất kiên trì và giàu nghị lực',
    'explanation' => '从未想过放弃 = chưa từng nghĩ đến việc bỏ cuộc.',
    'sort_order' => 106,
  ),
  106 => 
  array (
    'hsk_level' => 4,
    'skill_type' => 'reading',
    'difficulty' => 'intermediate',
    'question' => 'Điền từ thích hợp: "只有通过不断的努力和练习，____ 能真正掌握一门外语。"',
    'pinyin' => 'Zhǐyǒu tōngguò bùduàn de nǔlì, ____ néng zhēnzhèng zhǎngwò yìmén wàiyǔ.',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '才 (cái)',
      1 => '就 (jiù)',
      2 => '也 (yě)',
      3 => '还 (hái)',
    ),
    'correct_answer' => '才 (cái)',
    'explanation' => 'Cấu trúc điều kiện duy nhất: 只有...才... (Chỉ có... mới...).',
    'sort_order' => 107,
  ),
  107 => 
  array (
    'hsk_level' => 4,
    'skill_type' => 'reading',
    'difficulty' => 'intermediate',
    'question' => 'Từ nào mang nghĩa là "phỏng vấn" (tuyển dụng hoặc báo chí)?',
    'pinyin' => 'miàn shì',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '面试 (miànshì)',
      1 => '面对 (miànduì)',
      2 => '面积 (miànjī)',
      3 => '面貌 (miànmào)',
    ),
    'correct_answer' => '面试 (miànshì)',
    'explanation' => '面试 = phỏng vấn tuyển dụng; 面对 = đối mặt; 面积 = diện tích.',
    'sort_order' => 108,
  ),
  108 => 
  array (
    'hsk_level' => 4,
    'skill_type' => 'reading',
    'difficulty' => 'intermediate',
    'question' => 'Chọn từ thích hợp: "随着经济的飞速发展，人们的生活水平有了极大的 ____ 。"',
    'pinyin' => 'Suízhe jīngjì de fēisù fāzhǎn...',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '提高 (tígāo)',
      1 => '增加 (zēngjiā)',
      2 => '扩大 (kuòdà)',
      3 => '伸长 (shēncháng)',
    ),
    'correct_answer' => '提高 (tígāo)',
    'explanation' => '生活水平提高 = mức sống được nâng cao.',
    'sort_order' => 109,
  ),
  109 => 
  array (
    'hsk_level' => 4,
    'skill_type' => 'reading',
    'difficulty' => 'intermediate',
    'question' => 'Chọn từ điền vào câu: "这本小说非常感人，确实 ____ 一读。"',
    'pinyin' => 'Zhè běn xiǎoshuō fēicháng gǎnrén, quèshí ____ yí dú.',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '值得 (zhíde)',
      1 => '应该 (yīnggāi)',
      2 => '能够 (nénggòu)',
      3 => '必须 (bìxū)',
    ),
    'correct_answer' => '值得 (zhíde)',
    'explanation' => '值得一读 = xứng đáng / đáng để đọc một lần.',
    'sort_order' => 110,
  ),
  110 => 
  array (
    'hsk_level' => 4,
    'skill_type' => 'reading',
    'difficulty' => 'intermediate',
    'question' => 'Chọn từ biểu thị sự phong phú: "他在这家公司工作了十年，拥有 ____ 的实践经验。"',
    'pinyin' => 'Tā yōngyǒu ____ de shíjiàn jīngyàn.',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '丰富 (fēngfù)',
      1 => '负责 (fùzé)',
      2 => '复杂 (fùzá)',
      3 => '富裕 (fùyù)',
    ),
    'correct_answer' => '丰富 (fēngfù)',
    'explanation' => '经验丰富 = kinh nghiệm phong phú.',
    'sort_order' => 111,
  ),
  111 => 
  array (
    'hsk_level' => 4,
    'skill_type' => 'reading',
    'difficulty' => 'intermediate',
    'question' => 'Đọc hiểu mẩu tin: "经理要求全体员工必须在周五下班前交齐季度总结。"\\n-> Ý nghĩa câu trên là gì?',
    'pinyin' => 'Jīnglǐ yāoqiú quántǐ yuángōng...',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => 'Báo cáo tổng kết quý phải nộp đủ trước khi tan làm thứ Sáu',
      1 => 'Thứ Sáu bắt đầu viết báo cáo',
      2 => 'Giám đốc sẽ tự viết báo cáo tổng kết',
      3 => 'Báo cáo được lùi hạn sang tuần sau',
    ),
    'correct_answer' => 'Báo cáo tổng kết quý phải nộp đủ trước khi tan làm thứ Sáu',
    'explanation' => '交齐 = nộp đủ; 季度总结 = tổng kết quý; 周五下班前 = trước giờ tan sở thứ Sáu.',
    'sort_order' => 112,
  ),
  112 => 
  array (
    'hsk_level' => 4,
    'skill_type' => 'reading',
    'difficulty' => 'intermediate',
    'question' => 'Chọn cặp từ quan hệ giả thiết: "____ 明天下雨，运动会 ____ 照常举行。"',
    'pinyin' => '____ míngtiān xiàyǔ, yùndònghuì ____ zhàocháng jǔxíng.',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '即使...也... (jíshǐ... yě...)',
      1 => '因为...所以... (yīnwèi... suǒyǐ...)',
      2 => '既然...就... (jìrán... jiù...)',
      3 => '只要...才... (zhǐyào... cái...)',
    ),
    'correct_answer' => '即使...也... (jíshǐ... yě...)',
    'explanation' => '即使...也... biểu thị sự nhượng bộ giả thiết: Cho dù... cũng...',
    'sort_order' => 113,
  ),
  113 => 
  array (
    'hsk_level' => 4,
    'skill_type' => 'reading',
    'difficulty' => 'intermediate',
    'question' => 'Từ nào mang nghĩa là "chìa khóa / mấu chốt" của vấn đề?',
    'pinyin' => 'guān jiàn',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '关键 (guānjiàn)',
      1 => '关系 (guānxì)',
      2 => '观点 (guāndiǎn)',
      3 => '关注 (guānzhù)',
    ),
    'correct_answer' => '关键 (guānjiàn)',
    'explanation' => '关键 = mấu chốt, then chốt, mấu chốt giải quyết vấn đề.',
    'sort_order' => 114,
  ),
  114 => 
  array (
    'hsk_level' => 4,
    'skill_type' => 'reading',
    'difficulty' => 'intermediate',
    'question' => 'Chọn từ thích hợp: "大家都在专心听讲，教室里十分 ____ 。"',
    'pinyin' => 'Jiàoshì lǐ shífēn ____ .',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '安静 (ānjìng)',
      1 => '安全 (ānquán)',
      2 => '安心 (ānxīn)',
      3 => '平静 (píngjìng)',
    ),
    'correct_answer' => '安静 (ānjìng)',
    'explanation' => '安静 = yên tĩnh, trật tự.',
    'sort_order' => 115,
  ),
  115 => 
  array (
    'hsk_level' => 4,
    'skill_type' => 'reading',
    'difficulty' => 'intermediate',
    'question' => 'Từ "甚至" (shènzhì) dùng để biểu thị ý gì?',
    'pinyin' => 'shèn zhì',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => 'Biểu thị mức độ tăng tiến rõ rệt (Thậm chí)',
      1 => 'Biểu thị sự nhượng bộ',
      2 => 'Biểu thị nguyên nhân',
      3 => 'Biểu thị sự phủ định',
    ),
    'correct_answer' => 'Biểu thị mức độ tăng tiến rõ rệt (Thậm chí)',
    'explanation' => '甚至 biểu thị quan hệ tăng tiến vượt bậc: thậm chí.',
    'sort_order' => 116,
  ),
  116 => 
  array (
    'hsk_level' => 4,
    'skill_type' => 'reading',
    'difficulty' => 'intermediate',
    'question' => 'Chọn từ thích hợp: "这份调查报告的统计结果非常 ____ ，没有丝毫误差。"',
    'pinyin' => 'Tǒngjì jiéguǒ fēicháng ____ ...',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '准确 (zhǔnquè)',
      1 => '正常 (zhèngcháng)',
      2 => '正式 (zhèngshì)',
      3 => '正确 (zhèngquè)',
    ),
    'correct_answer' => '准确 (zhǔnquè)',
    'explanation' => '准确 = chuẩn xác, chính xác về số liệu thống kê.',
    'sort_order' => 117,
  ),
  117 => 
  array (
    'hsk_level' => 4,
    'skill_type' => 'reading',
    'difficulty' => 'intermediate',
    'question' => 'Đọc hiểu: "养成良好的作息习惯对身体健康至关重要。"\\n-> Ý của câu là gì?',
    'pinyin' => 'Yǎngchéng liánghǎo de zuòxī xíguàn...',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => 'Hình thành thói quen sinh hoạt điều độ là cực kỳ quan trọng cho sức khỏe',
      1 => 'Không cần chú ý đến giấc ngủ',
      2 => 'Chỉ cần uống thuốc là khỏe mạnh',
      3 => 'Thói quen sinh hoạt không ảnh hưởng sức khỏe',
    ),
    'correct_answer' => 'Hình thành thói quen sinh hoạt điều độ là cực kỳ quan trọng cho sức khỏe',
    'explanation' => '作息习惯 = thói quen làm việc và nghỉ ngơi; 至关重要 = vô cùng quan trọng.',
    'sort_order' => 118,
  ),
  118 => 
  array (
    'hsk_level' => 4,
    'skill_type' => 'reading',
    'difficulty' => 'intermediate',
    'question' => 'Chọn từ thích hợp: "在困难面前，我们应该相互鼓励，而不是相互 ____ 。"',
    'pinyin' => 'Zài kùnnán miànqián, wǒmen yīnggāi...',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '抱怨 (bàoyuàn)',
      1 => '保护 (bǎohù)',
      2 => '保证 (bǎozhèng)',
      3 => '抱歉 (bàoqiàn)',
    ),
    'correct_answer' => '抱怨 (bàoyuàn)',
    'explanation' => '抱怨 = phàn nàn, oán trách.',
    'sort_order' => 119,
  ),
  119 => 
  array (
    'hsk_level' => 4,
    'skill_type' => 'reading',
    'difficulty' => 'intermediate',
    'question' => 'Chọn từ biểu thị sự thành công trọn vẹn: "经过大家的齐心协力，活动举办得非常 ____ 。"',
    'pinyin' => 'Huódòng jǔbàn de fēicháng ____ .',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '成功 (chénggōng)',
      1 => '成熟 (chéngshú)',
      2 => '成立 (chénglì)',
      3 => '成年 (chéngnián)',
    ),
    'correct_answer' => '成功 (chénggōng)',
    'explanation' => '举办成功 = tổ chức thành công tốt đẹp.',
    'sort_order' => 120,
  ),
  120 => 
  array (
    'hsk_level' => 4,
    'skill_type' => 'grammar',
    'difficulty' => 'intermediate',
    'question' => 'Sắp xếp câu: "1. 引起 / 2. 这个 / 3. 广泛的 / 4. 讨论 / 5. 话题 / 6. 了"',
    'pinyin' => 'Zhège huàtí yǐnqǐ le guǎngfàn de tǎolùn.',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '2 - 5 - 1 - 6 - 3 - 4 (这个话题引起了广泛的讨论。)',
      1 => '1 - 6 - 2 - 5 - 3 - 4 (引起了这个话题广泛的讨论。)',
      2 => '3 - 4 - 1 - 6 - 2 - 5 (广泛的讨论引起了这个话题。)',
      3 => '2 - 5 - 3 - 4 - 1 - 6 (这个话题广泛的讨论引起了。)',
    ),
    'correct_answer' => '2 - 5 - 1 - 6 - 3 - 4 (这个话题引起了广泛的讨论。)',
    'explanation' => 'Chủ ngữ (这个话题) + Động từ & Trợ từ (引起了) + Định ngữ & Tân ngữ (广泛的讨论).',
    'sort_order' => 121,
  ),
  121 => 
  array (
    'hsk_level' => 4,
    'skill_type' => 'grammar',
    'difficulty' => 'intermediate',
    'question' => 'Sắp xếp câu chữ 把: "1. 钥匙 / 2. 忘在 / 3. 我 / 4. 把 / 5. 办公室了"',
    'pinyin' => 'Wǒ bǎ yàoshi wàng zài bàngōngshì le.',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '3 - 4 - 1 - 2 - 5 (我把钥匙忘在办公室了。)',
      1 => '4 - 1 - 3 - 2 - 5 (把钥匙我忘在办公室了。)',
      2 => '3 - 2 - 5 - 4 - 1 (我忘在办公室了把钥匙。)',
      3 => '1 - 4 - 3 - 2 - 5 (钥匙把我忘在办公室了。)',
    ),
    'correct_answer' => '3 - 4 - 1 - 2 - 5 (我把钥匙忘在办公室了。)',
    'explanation' => 'Chủ ngữ (我) + 把 + Tân ngữ (钥匙) + Động từ & Bổ ngữ (忘在办公室了).',
    'sort_order' => 122,
  ),
  122 => 
  array (
    'hsk_level' => 4,
    'skill_type' => 'grammar',
    'difficulty' => 'intermediate',
    'question' => 'Sắp xếp câu cấu trúc "连...都/也...": "1. 连 / 2. 这个字 / 3. 他 / 4. 认识 / 5. 都 / 6. 不"',
    'pinyin' => 'Tā lián zhège zì dōu bú rènshi.',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '3 - 1 - 2 - 5 - 6 - 4 (他连这个字都不认识。)',
      1 => '1 - 2 - 5 - 3 - 6 - 4 (连这个字都他不认识。)',
      2 => '3 - 5 - 6 - 4 - 1 - 2 (他都不认识连这个字。)',
      3 => '1 - 3 - 2 - 5 - 6 - 4 (连他这个字都不认识。)',
    ),
    'correct_answer' => '3 - 1 - 2 - 5 - 6 - 4 (他连这个字都不认识。)',
    'explanation' => 'Chủ ngữ (他) + 连 + Thành phần nhấn mạnh (这个字) + 都/也 + Phủ định & Động từ (不认识).',
    'sort_order' => 123,
  ),
  123 => 
  array (
    'hsk_level' => 4,
    'skill_type' => 'grammar',
    'difficulty' => 'intermediate',
    'question' => 'Sắp xếp câu bị động chữ 被: "1. 打破了 / 2. 那个花瓶 / 3. 被 / 4. 猫"',
    'pinyin' => 'Nà ge huāpíng bèi māo dǎpò le.',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '2 - 3 - 4 - 1 (那个花瓶被猫打破了。)',
      1 => '4 - 3 - 2 - 1 (猫被那个花瓶打破了。)',
      2 => '2 - 1 - 3 - 4 (那个花瓶打破了被猫。)',
      3 => '3 - 4 - 2 - 1 (被猫那个花瓶打破了。)',
    ),
    'correct_answer' => '2 - 3 - 4 - 1 (那个花瓶被猫打破了。)',
    'explanation' => 'Vật bị tác động (那个花瓶) + 被 + Chủ thể tác động (猫) + Động từ kết quả (打破了).',
    'sort_order' => 124,
  ),
  124 => 
  array (
    'hsk_level' => 4,
    'skill_type' => 'grammar',
    'difficulty' => 'intermediate',
    'question' => 'Sắp xếp câu cấu trúc "不仅...而且...": "1. 而且 / 2. 聪明 / 3. 善良 / 4. 她 / 5. 不仅 / 6. 非常"',
    'pinyin' => 'Tā bùjǐn cōngming, érqiě fēicháng shànliáng.',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '4 - 5 - 2 - 1 - 6 - 3 (她不仅聪明，而且非常善良。)',
      1 => '5 - 2 - 1 - 4 - 6 - 3 (不仅聪明而且她非常善良。)',
      2 => '4 - 1 - 6 - 3 - 5 - 2 (她而且非常善良不仅聪明。)',
      3 => '6 - 3 - 4 - 5 - 2 - 1 (非常善良她不仅聪明而且。)',
    ),
    'correct_answer' => '4 - 5 - 2 - 1 - 6 - 3 (她不仅聪明，而且非常善良。)',
    'explanation' => 'Chủ ngữ (她) + 不仅 + Tính từ 1 (聪明), 而且 + Tính từ 2 (非常善良).',
    'sort_order' => 125,
  ),
  125 => 
  array (
    'hsk_level' => 4,
    'skill_type' => 'grammar',
    'difficulty' => 'intermediate',
    'question' => 'Sắp xếp câu có phó từ "究竟" (rốt cuộc): "1. 是 / 2. 事情的 / 3. 究竟 / 4. 真相 / 5. 什么"',
    'pinyin' => 'Shìqíng de zhēnxiàng jiùjìng shì shénme?',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '2 - 4 - 3 - 1 - 5 (事情的真相究竟是什么？)',
      1 => '3 - 1 - 5 - 2 - 4 (究竟是什么事情的真相？)',
      2 => '2 - 4 - 1 - 5 - 3 (事情的真相是什么究竟？)',
      3 => '1 - 5 - 3 - 2 - 4 (是什么究竟事情的真相？)',
    ),
    'correct_answer' => '2 - 4 - 3 - 1 - 5 (事情的真相究竟是什么？)',
    'explanation' => 'Chủ ngữ (事情的真相) + 究竟 + Động từ (是) + Tân ngữ nghi vấn (什么).',
    'sort_order' => 126,
  ),
  126 => 
  array (
    'hsk_level' => 4,
    'skill_type' => 'grammar',
    'difficulty' => 'intermediate',
    'question' => 'Sắp xếp câu: "1. 随着 / 2. 变化 / 3. 发生了 / 4. 技术的进步 / 5. 我们的生活 / 6. 巨大"',
    'pinyin' => 'Suízhe jìshù de jìnbù, wǒmen de shēnghuó fāshēng le jùdà biànhuà.',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '1 - 4 - 5 - 3 - 6 - 2 (随着技术的进步，我们的生活发生了巨大变化。)',
      1 => '5 - 1 - 4 - 3 - 6 - 2 (我们的生活随着技术的进步发生了巨大变化。)',
      2 => '3 - 6 - 2 - 1 - 4 - 5 (发生了巨大变化随着技术的进步我们的生活。)',
      3 => '1 - 4 - 3 - 6 - 2 - 5 (随着技术的进步发生了巨大变化我们的生活。)',
    ),
    'correct_answer' => '1 - 4 - 5 - 3 - 6 - 2 (随着技术的进步，我们的生活发生了巨大变化。)',
    'explanation' => 'Trạng ngữ (随着技术的进步) + Chủ ngữ (我们的生活) + Động từ (发生了) + Tân ngữ (巨大变化).',
    'sort_order' => 127,
  ),
  127 => 
  array (
    'hsk_level' => 4,
    'skill_type' => 'grammar',
    'difficulty' => 'intermediate',
    'question' => 'Sắp xếp câu: "1. 积累了 / 2. 宝贵的 / 3. 经验 / 4. 他在实践中 / 5. 许多"',
    'pinyin' => 'Tā zài shíjiàn zhōng jīlěi le xǔduō bǎoguì de jīngyàn.',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '4 - 1 - 5 - 2 - 3 (他在实践中积累了许多宝贵的经验。)',
      1 => '1 - 5 - 2 - 3 - 4 (积累了许多宝贵的经验他在实践中。)',
      2 => '4 - 5 - 2 - 3 - 1 (他在实践中许多宝贵的经验积累了。)',
      3 => '5 - 2 - 3 - 4 - 1 (许多宝贵的经验他在实践中积累了。)',
    ),
    'correct_answer' => '4 - 1 - 5 - 2 - 3 (他在实践中积累了许多宝贵的经验。)',
    'explanation' => 'Chủ ngữ và địa điểm (他在实践中) + Động từ (积累了) + Số lượng & Định ngữ (许多宝贵的) + Danh từ (经验).',
    'sort_order' => 128,
  ),
  128 => 
  array (
    'hsk_level' => 4,
    'skill_type' => 'grammar',
    'difficulty' => 'intermediate',
    'question' => 'Sắp xếp câu có cấu trúc "只要...就...": "1. 只要 / 2. 成功 / 3. 坚持下去 / 4. 我们 / 5. 就一定能"',
    'pinyin' => 'Zhǐyào jiānchí xiàqu, wǒmen jiù yídìng néng chénggōng.',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '1 - 3 - 4 - 5 - 2 (只要坚持下去，我们就一定能成功。)',
      1 => '4 - 5 - 2 - 1 - 3 (我们一定能成功只要坚持下去。)',
      2 => '1 - 4 - 5 - 2 - 3 (只要我们一定能成功坚持下去。)',
      3 => '3 - 1 - 4 - 5 - 2 (坚持下去只要我们就一定能成功。)',
    ),
    'correct_answer' => '1 - 3 - 4 - 5 - 2 (只要坚持下去，我们就一定能成功。)',
    'explanation' => '只要 + Điều kiện (坚持下去), 主语 + 就 + Kết quả (我们就一定能成功).',
    'sort_order' => 129,
  ),
  129 => 
  array (
    'hsk_level' => 4,
    'skill_type' => 'grammar',
    'difficulty' => 'intermediate',
    'question' => 'Sắp xếp câu có bổ ngữ trạng thái "得": "1. 得 / 2. 流利 / 3. 汉语 / 4. 他 / 5. 非常 / 6. 说"',
    'pinyin' => 'Tā Hànyǔ shuō de fēicháng liúlì.',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '4 - 3 - 6 - 1 - 5 - 2 (他汉语说得非常流利。)',
      1 => '4 - 6 - 1 - 5 - 2 - 3 (他说得非常流利汉语。)',
      2 => '3 - 4 - 6 - 1 - 5 - 2 (汉语他说得非常流利。)',
      3 => '4 - 3 - 5 - 2 - 6 - 1 (他汉语非常流利说得。)',
    ),
    'correct_answer' => '4 - 3 - 6 - 1 - 5 - 2 (他汉语说得非常流利。)',
    'explanation' => 'Chủ ngữ (他) + Tân ngữ chủ đề (汉语) + Động từ (说) + 得 + Mức độ (非常流利).',
    'sort_order' => 130,
  ),
  130 => 
  array (
    'hsk_level' => 4,
    'skill_type' => 'grammar',
    'difficulty' => 'intermediate',
    'question' => 'Sắp xếp câu: "1. 无论 / 2. 都 / 3. 遇到 / 4. 放弃 / 5. 什么困难 / 6. 他不会"',
    'pinyin' => 'Wúlùn yùdào shénme kùnnán, tā dōu búhuì fàngqì.',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '1 - 3 - 5 - 6 - 2 - 4 (无论遇到什么困难，他都不会放弃。)',
      1 => '6 - 2 - 4 - 1 - 3 - 5 (他不会放弃无论遇到什么困难。)',
      2 => '1 - 5 - 3 - 6 - 2 - 4 (无论什么困难遇到他都不会放弃。)',
      3 => '3 - 5 - 1 - 6 - 2 - 4 (遇到什么困难无论他都不会放弃。)',
    ),
    'correct_answer' => '1 - 3 - 5 - 6 - 2 - 4 (无论遇到什么困难，他都不会放弃。)',
    'explanation' => '无论 + Điều kiện bất kỳ (无论遇到什么困难), 主语 + 都 + Kết quả (他都不会放弃).',
    'sort_order' => 131,
  ),
  131 => 
  array (
    'hsk_level' => 4,
    'skill_type' => 'grammar',
    'difficulty' => 'intermediate',
    'question' => 'Sắp xếp câu: "1. 专门 / 2. 为大家 / 3. 这次活动 / 4. 是 / 5. 举办的"',
    'pinyin' => 'Zhè cì huódòng shì zhuānmén wèi dàjiā jǔbàn de.',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '3 - 4 - 1 - 2 - 5 (这次活动是专门为大家举办的。)',
      1 => '1 - 2 - 3 - 4 - 5 (专门为大家这次活动是举办的。)',
      2 => '3 - 1 - 2 - 4 - 5 (这次活动专门为大家是举办的。)',
      3 => '4 - 3 - 1 - 2 - 5 (是这次活动专门为大家举办的。)',
    ),
    'correct_answer' => '3 - 4 - 1 - 2 - 5 (这次活动是专门为大家举办的。)',
    'explanation' => 'Cấu trúc nhấn mạnh 是...的: 这次活动 (Chủ ngữ) + 是专门为大家举办的 (Vị ngữ nhấn mạnh mục đích).',
    'sort_order' => 132,
  ),
  132 => 
  array (
    'hsk_level' => 5,
    'skill_type' => 'listening',
    'difficulty' => 'advanced',
    'question' => 'Nghe và cho biết trọng tâm của cuộc họp công ty:',
    'pinyin' => 'Gōngsī gāocéng zhèngzài shěndìng xīn yī lún de tóuzī fāng\'àn, zhòngdiǎn guānzhù xīn néngyuán lǐngyù.',
    'audio_text' => '公司高层正在审定新一轮的投资方案，重点关注新能源领域。',
    'options' => 
    array (
      0 => 'Thẩm định phương án đầu tư mới tập trung vào lĩnh vực năng lượng mới',
      1 => 'Cắt giảm nhân sự cuối năm',
      2 => 'Mở rộng thị trường truyền thống',
      3 => 'Thanh lý tài sản cũ',
    ),
    'correct_answer' => 'Thẩm định phương án đầu tư mới tập trung vào lĩnh vực năng lượng mới',
    'explanation' => '高层 = ban lãnh đạo; 投资方案 = phương án đầu tư; 新能源 = năng lượng mới.',
    'sort_order' => 133,
  ),
  133 => 
  array (
    'hsk_level' => 5,
    'skill_type' => 'listening',
    'difficulty' => 'advanced',
    'question' => 'Nghe và cho biết lời khuyên của chuyên gia tâm lý:',
    'pinyin' => 'Miànduì gōngzuò yālì, yīnggāi xuéhuì tōngguò yùndòng hé míngxiǎng lái huǎnjiě jiāolǜ.',
    'audio_text' => '面对工作压力，应该学会通过运动和冥想来缓解焦虑。',
    'options' => 
    array (
      0 => 'Nên học cách vận động và thiền định để giảm bớt lo âu',
      1 => 'Nên đổi việc làm ngay lập tức',
      2 => 'Nên làm việc thêm giờ để quên áp lực',
      3 => 'Không cần quan tâm đến áp lực',
    ),
    'correct_answer' => 'Nên học cách vận động và thiền định để giảm bớt lo âu',
    'explanation' => '缓解焦虑 = giảm bớt lo âu; 冥想 = thiền định.',
    'sort_order' => 134,
  ),
  134 => 
  array (
    'hsk_level' => 5,
    'skill_type' => 'listening',
    'difficulty' => 'advanced',
    'question' => 'Nghe và cho biết giải pháp bảo vệ môi trường đô thị:',
    'pinyin' => 'Chéngshì lǜsè chūxíng bùjǐn jiǎnshǎo tànpáifàng, hái néng yǒuxiào huǎnjiě jiāotōng yǒngdǔ.',
    'audio_text' => '城市绿色出行不仅减少碳排放，还能有效缓解交通拥堵。',
    'options' => 
    array (
      0 => 'Di chuyển xanh giảm phát thải carbon và giảm ùn tắc giao thông',
      1 => 'Tăng số lượng xe ô tô cá nhân',
      2 => 'Đóng cửa các trạm tàu điện ngầm',
      3 => 'Cấm người đi bộ qua đường',
    ),
    'correct_answer' => 'Di chuyển xanh giảm phát thải carbon và giảm ùn tắc giao thông',
    'explanation' => '绿色出行 = di chuyển xanh; 碳排放 = phát thải carbon; 缓解拥堵 = giảm ùn tắc.',
    'sort_order' => 135,
  ),
  135 => 
  array (
    'hsk_level' => 5,
    'skill_type' => 'listening',
    'difficulty' => 'advanced',
    'question' => 'Nghe và cho biết tiêu chí tuyển dụng nhân tài:',
    'pinyin' => 'Běn qǐyè zài zhāopìn shí, bǐqǐ xuélì, gèng kànzhòng yīngpìnzhě de tuánduì xiézuò nénglì yǔ chuàngxīn jīngshén.',
    'audio_text' => '本企业在招聘时，比起学历，更看重应聘者的团队协作能力与创新精神。',
    'options' => 
    array (
      0 => 'Xem trọng năng lực hợp tác đội nhóm và tinh thần đổi mới sáng tạo hơn học vấn',
      1 => 'Chỉ tuyển người có bằng tiến sĩ',
      2 => 'Chỉ quan tâm đến kinh nghiệm ở nước ngoài',
      3 => 'Ưu tiên người quen',
    ),
    'correct_answer' => 'Xem trọng năng lực hợp tác đội nhóm và tinh thần đổi mới sáng tạo hơn học vấn',
    'explanation' => '更看重 = xem trọng hơn; 团队协作 = hợp tác đội nhóm; 创新精神 = tinh thần sáng tạo.',
    'sort_order' => 136,
  ),
  136 => 
  array (
    'hsk_level' => 5,
    'skill_type' => 'listening',
    'difficulty' => 'advanced',
    'question' => 'Nghe và cho biết ý kiến về bảo tồn văn hóa truyền thống:',
    'pinyin' => 'Chuántǒng wénhuà bùyīng jǐnjǐn tínglúi zài bówùguǎn lǐ, ér yīnggāi róngrù xiàndài shēnghuó.',
    'audio_text' => '传统文化不应仅仅停留在博物馆里，而应该融入现代生活。',
    'options' => 
    array (
      0 => 'Văn hóa truyền thống cần hòa nhập vào đời sống hiện đại chứ không chỉ nằm trong bảo tàng',
      1 => 'Chỉ nên giữ văn hóa trong bảo tàng',
      2 => 'Nên thay thế hoàn toàn bằng văn hóa phương Tây',
      3 => 'Không cần bảo tồn văn hóa cổ',
    ),
    'correct_answer' => 'Văn hóa truyền thống cần hòa nhập vào đời sống hiện đại chứ không chỉ nằm trong bảo tàng',
    'explanation' => '融入现代生活 = hòa nhập vào đời sống hiện đại.',
    'sort_order' => 137,
  ),
  137 => 
  array (
    'hsk_level' => 5,
    'skill_type' => 'listening',
    'difficulty' => 'advanced',
    'question' => 'Nghe và cho biết thái độ cần có trước nghịch cảnh:',
    'pinyin' => 'Miànduì kùnnán yǔ cuòzhé, wǒmen bùnéng táobì, ér yīnggāi yíngnán\'érshàng, xúnzhǎo tūpòkǒu.',
    'audio_text' => '面对困难与挫折，我们不能逃避，而应该迎难而上，寻找突破口。',
    'options' => 
    array (
      0 => 'Không nên trốn tránh mà dũng cảm đương đầu tìm đột phá',
      1 => 'Nên bỏ cuộc ngay khi gặp khó',
      2 => 'Đổ lỗi cho người khác',
      3 => 'Chờ người khác giải quyết hộ',
    ),
    'correct_answer' => 'Không nên trốn tránh mà dũng cảm đương đầu tìm đột phá',
    'explanation' => '迎难而上 = dũng cảm tiến lên đối mặt khó khăn; 突破口 = điểm đột phá.',
    'sort_order' => 138,
  ),
  138 => 
  array (
    'hsk_level' => 5,
    'skill_type' => 'listening',
    'difficulty' => 'advanced',
    'question' => 'Nghe và cho biết xu hướng của ngành thương mại điện tử:',
    'pinyin' => 'Suízhe wùliú wǎngluò de wánshàn, nóngchǎnpǐn diànshāng zhèngzài kuàisù juéqǐ.',
    'audio_text' => '随着物流网络的完善，农产品电商正在快速崛起。',
    'options' => 
    array (
      0 => 'Thương mại điện tử nông sản đang trỗi dậy nhanh chóng nhờ mạng lưới logistics hoàn thiện',
      1 => 'Ngành logistics đang bị suy thoái',
      2 => 'Thương mại điện tử không bán nông sản',
      3 => 'Người tiêu dùng quay lại chợ cóc hoàn toàn',
    ),
    'correct_answer' => 'Thương mại điện tử nông sản đang trỗi dậy nhanh chóng nhờ mạng lưới logistics hoàn thiện',
    'explanation' => '物流网络 = mạng lưới vận chuyển; 快速崛起 = trỗi dậy nhanh chóng.',
    'sort_order' => 139,
  ),
  139 => 
  array (
    'hsk_level' => 5,
    'skill_type' => 'listening',
    'difficulty' => 'advanced',
    'question' => 'Nghe và cho biết ý nghĩa của việc học suốt đời:',
    'pinyin' => 'Zài xìnxī shíjiān, zhǐyǒu bǎochí zhōngshēn xuéxí de xíguàn, cáinéng bù bèi shídài suǒ táotài.',
    'audio_text' => '在信息时代，只有保持终身学习的习惯，才能不被时代所淘汰。',
    'options' => 
    array (
      0 => 'Chỉ có duy trì thói quen học tập suốt đời mới không bị thời đại đào thải',
      1 => 'Học xong đại học là đủ',
      2 => 'Thời đại số không cần học hỏi thêm',
      3 => 'Thông tin thay đổi quá nhanh không nên học',
    ),
    'correct_answer' => 'Chỉ có duy trì thói quen học tập suốt đời mới không bị thời đại đào thải',
    'explanation' => '终身学习 = học tập suốt đời; 淘汰 = bị đào thải.',
    'sort_order' => 140,
  ),
  140 => 
  array (
    'hsk_level' => 5,
    'skill_type' => 'listening',
    'difficulty' => 'advanced',
    'question' => 'Nghe và cho biết chìa khóa của giao tiếp thành công:',
    'pinyin' => 'Yǒuxiào gōutōng de mìjué bú zài yú nǐ shuō le duōshao, ér zài yú nǐ shifǒu shànyú qīngtīng.',
    'audio_text' => '有效沟通的秘诀不在于说了多少，而在于你是否善于倾听。',
    'options' => 
    array (
      0 => 'Bí quyết giao tiếp hiệu quả là giỏi lắng nghe người khác',
      1 => 'Nói càng nhiều càng tốt',
      2 => 'Tránh giao tiếp với cấp trên',
      3 => 'Luôn luôn ngắt lời người khác',
    ),
    'correct_answer' => 'Bí quyết giao tiếp hiệu quả là giỏi lắng nghe người khác',
    'explanation' => '善于倾听 = giỏi lắng nghe.',
    'sort_order' => 141,
  ),
  141 => 
  array (
    'hsk_level' => 5,
    'skill_type' => 'listening',
    'difficulty' => 'advanced',
    'question' => 'Nghe và cho biết tác động của công nghệ mới:',
    'pinyin' => 'Réngōng zhìnéng de pǔjí zhèngzài shēnkè gǎibiàn chuántǒng hángyè de shēngchǎn móshì.',
    'audio_text' => '人工智能的普及正在深刻改变传统行业的生产模式。',
    'options' => 
    array (
      0 => 'Sự phổ cập trí tuệ nhân tạo đang làm thay đổi sâu sắc mô hình sản xuất truyền thống',
      1 => 'Trí tuệ nhân tạo không có ứng dụng thực tiễn',
      2 => 'Các ngành truyền thống không chịu tác động',
      3 => 'Máy móc làm giảm hiệu suất lao động',
    ),
    'correct_answer' => 'Sự phổ cập trí tuệ nhân tạo đang làm thay đổi sâu sắc mô hình sản xuất truyền thống',
    'explanation' => '人工智能 = trí tuệ nhân tạo; 深刻改变 = thay đổi sâu sắc.',
    'sort_order' => 142,
  ),
  142 => 
  array (
    'hsk_level' => 5,
    'skill_type' => 'listening',
    'difficulty' => 'advanced',
    'question' => 'Nghe và cho biết trải nghiệm du lịch mang lại điều gì:',
    'pinyin' => 'Lǚxíng bú shì dǎkǎ pāizhào, ér shì tǐyàn bùtóng de fēngtǔ rénqíng hé wénhuà chōngjī.',
    'audio_text' => '旅行不是打卡拍照，而是体验不同的风土人情和文化冲击。',
    'options' => 
    array (
      0 => 'Du lịch là trải nghiệm phong tục tập quán và va chạm văn hóa',
      1 => 'Du lịch chỉ để chụp ảnh check-in',
      2 => 'Du lịch tốn kém không nên đi',
      3 => 'Nên ở trong khách sạn suốt kỳ nghỉ',
    ),
    'correct_answer' => 'Du lịch là trải nghiệm phong tục tập quán và va chạm văn hóa',
    'explanation' => '风土人情 = phong thổ nhân tình / phong tục tập quán; 文化冲击 = sốc văn hóa.',
    'sort_order' => 143,
  ),
  143 => 
  array (
    'hsk_level' => 5,
    'skill_type' => 'listening',
    'difficulty' => 'advanced',
    'question' => 'Nghe và cho biết yếu tố duy trì tính sáng tạo:',
    'pinyin' => 'Dàyuē suǒyǒu de chuàngxīn, dōu yuányú duì shìjiè bǎochí qiángliè de hàoqíxīn.',
    'audio_text' => '大约所有的创新，都源于对世界保持强烈的好奇心。',
    'options' => 
    array (
      0 => 'Mọi đổi mới sáng tạo đều bắt nguồn từ lòng hiếu kỳ mạnh mẽ đối với thế giới',
      1 => 'Chỉ cần bắt chước người khác',
      2 => 'Không nên tò mò',
      3 => 'Sáng tạo chỉ dựa vào may mắn',
    ),
    'correct_answer' => 'Mọi đổi mới sáng tạo đều bắt nguồn từ lòng hiếu kỳ mạnh mẽ đối với thế giới',
    'explanation' => '源于 = bắt nguồn từ; 好奇心 = lòng hiếu kỳ / tò mò.',
    'sort_order' => 144,
  ),
  144 => 
  array (
    'hsk_level' => 5,
    'skill_type' => 'reading',
    'difficulty' => 'advanced',
    'question' => 'Thành ngữ "拔苗助长" (bá miáo zhù zhǎng) mang ý nghĩa phê phán điều gì?',
    'pinyin' => 'bá miáo zhù zhǎng',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => 'Nóng vội đốt cháy giai đoạn làm hỏng việc (Nhổ lúa non giúp mau lớn)',
      1 => 'Chăm chỉ làm nông nghiệp',
      2 => 'Kiên trì từng bước một',
      3 => 'Giúp đỡ người nghèo',
    ),
    'correct_answer' => 'Nóng vội đốt cháy giai đoạn làm hỏng việc (Nhổ lúa non giúp mau lớn)',
    'explanation' => '拔苗助长 chỉ sự nôn nóng muốn thấy kết quả mà làm trái quy luật tự nhiên dẫn đến thất bại.',
    'sort_order' => 145,
  ),
  145 => 
  array (
    'hsk_level' => 5,
    'skill_type' => 'reading',
    'difficulty' => 'advanced',
    'question' => 'Thành ngữ "见义勇为" (jiàn yì yǒng wéi) chỉ đức tính gì?',
    'pinyin' => 'jiàn yì yǒng wéi',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => 'Thấy việc nghĩa dũng cảm đứng ra làm',
      1 => 'Sợ phiền phức bỏ chạy',
      2 => 'Giả vờ không biết chuyện gì',
      3 => 'Gây chuyện thị phi',
    ),
    'correct_answer' => 'Thấy việc nghĩa dũng cảm đứng ra làm',
    'explanation' => '见义勇为 là hành vi hào hiệp, thấy việc chính nghĩa thì dũng cảm làm.',
    'sort_order' => 146,
  ),
  146 => 
  array (
    'hsk_level' => 5,
    'skill_type' => 'reading',
    'difficulty' => 'advanced',
    'question' => 'Chọn từ đồng nghĩa với "偶尔" (thỉnh thoảng, ngẫu nhiên):',
    'pinyin' => 'ǒu\'ěr',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '有时 (yǒushí)',
      1 => '常常 (chángcháng)',
      2 => '从来 (cónglái)',
      3 => '总是 (zǒngshì)',
    ),
    'correct_answer' => '有时 (yǒushí)',
    'explanation' => '偶尔 = 有时 (có lúc, thỉnh thoảng).',
    'sort_order' => 147,
  ),
  147 => 
  array (
    'hsk_level' => 5,
    'skill_type' => 'reading',
    'difficulty' => 'advanced',
    'question' => 'Chọn từ kết hợp chuẩn (Collocation): "公司正在制定新的 ____ 方案。"',
    'pinyin' => 'Gōngsī zhèngzài zhìdìng xīn de ____ fāng\'àn.',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '投资 (tóuzī)',
      1 => '投币 (tóubì)',
      2 => '投票 (tóupiào)',
      3 => '投入 (tóurù)',
    ),
    'correct_answer' => '投资 (tóuzī)',
    'explanation' => '投资方案 = phương án đầu tư.',
    'sort_order' => 148,
  ),
  148 => 
  array (
    'hsk_level' => 5,
    'skill_type' => 'reading',
    'difficulty' => 'advanced',
    'question' => 'Đọc hiểu logic: "成功不是一朝一夕的事，需要长期的积累与坚持。"\\n-> Ý nghĩa câu trên là gì?',
    'pinyin' => 'Chénggōng bú shì yì zhāo yì xī de shì...',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => 'Thành công đòi hỏi quá trình tích lũy và kiên trì lâu dài, không thể nóng vội một sớm một chiều',
      1 => 'Thành công có thể đến ngẫu nhiên chỉ sau một đêm',
      2 => 'Chỉ cần có tiền là thành công',
      3 => 'Không thể nào thành công được',
    ),
    'correct_answer' => 'Thành công đòi hỏi quá trình tích lũy và kiên trì lâu dài, không thể nóng vội một sớm một chiều',
    'explanation' => '一朝一夕 = một sớm một chiều.',
    'sort_order' => 149,
  ),
  149 => 
  array (
    'hsk_level' => 5,
    'skill_type' => 'reading',
    'difficulty' => 'advanced',
    'question' => 'Từ nào sau đây gần nghĩa nhất với "改善" (gǎishàn)?',
    'pinyin' => 'gǎi shàn',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '改进 (gǎijìn)',
      1 => '改变 (gǎibiàn)',
      2 => '改正 (gǎizhèng)',
      3 => '改造 (gǎizào)',
    ),
    'correct_answer' => '改进 (gǎijìn)',
    'explanation' => '改善 (cải thiện) và 改进 (cải tiến) đều mang ý nghĩa làm cho tình trạng tốt đẹp hơn.',
    'sort_order' => 150,
  ),
  150 => 
  array (
    'hsk_level' => 5,
    'skill_type' => 'reading',
    'difficulty' => 'advanced',
    'question' => 'Thành ngữ "塞翁失马" (sài wēng shī mǎ) hàm ý điều gì trong cuộc sống?',
    'pinyin' => 'sài wēng shī mǎ',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => 'Họa phúc khôn lường, điều không may đôi khi lại mang lại điều tốt lành',
      1 => 'Mất ngựa thì phải đi tìm ngay',
      2 => 'Nên nuôi nhiều ngựa',
      3 => 'Cuộc sống chỉ toàn bất hạnh',
    ),
    'correct_answer' => 'Họa phúc khôn lường, điều không may đôi khi lại mang lại điều tốt lành',
    'explanation' => '塞翁失马，焉知非福 (Tái ông thất mã, yên tri phi phúc).',
    'sort_order' => 151,
  ),
  151 => 
  array (
    'hsk_level' => 5,
    'skill_type' => 'reading',
    'difficulty' => 'advanced',
    'question' => 'Chọn từ thích hợp: "双方在谈判中表现出了足够的诚意，彼此 ____ 对方的利益。"',
    'pinyin' => 'Shuāngfāng zài tánpàn zhōng...',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '尊重 (zūnzhòng)',
      1 => '遵守 (zūnshǒu)',
      2 => '尊敬 (zūnjìng)',
      3 => '遵从 (zūncóng)',
    ),
    'correct_answer' => '尊重 (zūnzhòng)',
    'explanation' => '尊重利益 = tôn trọng lợi ích.',
    'sort_order' => 152,
  ),
  152 => 
  array (
    'hsk_level' => 5,
    'skill_type' => 'reading',
    'difficulty' => 'advanced',
    'question' => 'Chọn từ điền vào câu: "为了 ____ 类似的错误再次发生，我们必须完善监管制度。"',
    'pinyin' => 'Wèile ____ lèisì de cuòwù zàicì fāshēng...',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '避免 (bìmiǎn)',
      1 => '逃避 (táobì)',
      2 => '躲避 (duǒbì)',
      3 => '阻止 (zǔzhǐ)',
    ),
    'correct_answer' => '避免 (bìmiǎn)',
    'explanation' => '避免发生 = tránh để xảy ra.',
    'sort_order' => 153,
  ),
  153 => 
  array (
    'hsk_level' => 5,
    'skill_type' => 'reading',
    'difficulty' => 'advanced',
    'question' => 'Từ "深刻" (shēnkè) thường đi kèm với danh từ nào sau đây?',
    'pinyin' => 'shēn kè',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '印象 (yìnxiàng)',
      1 => '速度 (sùdù)',
      2 => '面积 (miànjī)',
      3 => '价格 (jiàgé)',
    ),
    'correct_answer' => '印象 (yìnxiàng)',
    'explanation' => '留下深刻的印象 = để lại ấn tượng sâu sắc.',
    'sort_order' => 154,
  ),
  154 => 
  array (
    'hsk_level' => 5,
    'skill_type' => 'reading',
    'difficulty' => 'advanced',
    'question' => 'Liên từ "从而" (cóng\'ér) có tác dụng gì trong câu ghép?',
    'pinyin' => 'cóng\'ér',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => 'Nối vế sau biểu thị kết quả, mục đích phái sinh từ hành động trước (Từ đó...)',
      1 => 'Biểu thị sự đối lập chuyển ý',
      2 => 'Biểu thị sự lựa chọn hoặc... hoặc...',
      3 => 'Biểu thị điều kiện phủ định',
    ),
    'correct_answer' => 'Nối vế sau biểu thị kết quả, mục đích phái sinh từ hành động trước (Từ đó...)',
    'explanation' => '从而 liên kết vế câu, biểu thị từ hành động vế trước mà dẫn tới kết quả vế sau.',
    'sort_order' => 155,
  ),
  155 => 
  array (
    'hsk_level' => 5,
    'skill_type' => 'reading',
    'difficulty' => 'advanced',
    'question' => 'Thành ngữ "亡羊补牢" (wáng yáng bǔ láo) khuyên chúng ta điều gì?',
    'pinyin' => 'wáng yáng bǔ láo',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => 'Gặp tổn thất kịp thời sửa chữa, khắc phục thì vẫn chưa muộn',
      1 => 'Mất cừu rồi thì không cần làm chuồng nữa',
      2 => 'Nuôi cừu rất nguy hiểm',
      3 => 'Đợi xảy ra chuyện lớn mới lo liệu',
    ),
    'correct_answer' => 'Gặp tổn thất kịp thời sửa chữa, khắc phục thì vẫn chưa muộn',
    'explanation' => '亡羊补牢，未为迟也 (Mất bò mới lo làm chuồng, vẫn chưa muộn nếu biết sửa sai kịp thời).',
    'sort_order' => 156,
  ),
  156 => 
  array (
    'hsk_level' => 5,
    'skill_type' => 'reading',
    'difficulty' => 'advanced',
    'question' => 'Chọn từ thích hợp: "这项新政策一经公布，立刻引起了社会的 ____ 关注。"',
    'pinyin' => 'Yǐnqǐ le shèhuì de ____ guānzhù.',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '广泛 (guǎngfàn)',
      1 => '广大 (guǎngdà)',
      2 => '宽阔 (kuānkuò)',
      3 => '广阔 (guǎngkuò)',
    ),
    'correct_answer' => '广泛 (guǎngfàn)',
    'explanation' => '广泛关注 = sự quan tâm rộng rãi.',
    'sort_order' => 157,
  ),
  157 => 
  array (
    'hsk_level' => 5,
    'skill_type' => 'reading',
    'difficulty' => 'advanced',
    'question' => 'Điền từ thích hợp: "针对当前的严峻形势，政府迅速 ____ 了有效措施。"',
    'pinyin' => 'Zhèngfǔ xùnsù ____ le yǒuxiào cuòshī.',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '采取 (cǎiqǔ)',
      1 => '采用 (cǎiyòng)',
      2 => '采纳 (cǎinà)',
      3 => '采集 (cǎijí)',
    ),
    'correct_answer' => '采取 (cǎiqǔ)',
    'explanation' => '采取措施 = áp dụng biện pháp.',
    'sort_order' => 158,
  ),
  158 => 
  array (
    'hsk_level' => 5,
    'skill_type' => 'reading',
    'difficulty' => 'advanced',
    'question' => 'Đọc hiểu logic: "虽然遇到了前所未有的阻力，但他依然迎难而上。"\\n-> Đánh giá tinh thần nhân vật:',
    'pinyin' => 'Yīrán yíng nán ér shàng...',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => 'Dũng cảm đối mặt và quyết tâm vượt qua thử thách khó khăn',
      1 => 'Thấy khó khăn liền thoái lui',
      2 => 'Tìm đường tắt né tránh',
      3 => 'Chờ người khác làm thay',
    ),
    'correct_answer' => 'Dũng cảm đối mặt và quyết tâm vượt qua thử thách khó khăn',
    'explanation' => '迎难而上 biểu thị khí phách hiên ngang trước chông gai.',
    'sort_order' => 159,
  ),
  159 => 
  array (
    'hsk_level' => 5,
    'skill_type' => 'reading',
    'difficulty' => 'advanced',
    'question' => 'Từ "所谓" (suǒwèi) mang ý nghĩa gì?',
    'pinyin' => 'suǒ wèi',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => 'Cái gọi là (chỉ điều mọi người hay gọi, hoặc dùng với ý nghĩa hoài nghi/châm biếm nhẹ)',
      1 => 'Vì vậy mà',
      2 => 'Không thể nói ra',
      3 => 'Rất quan trọng',
    ),
    'correct_answer' => 'Cái gọi là (chỉ điều mọi người hay gọi, hoặc dùng với ý nghĩa hoài nghi/châm biếm nhẹ)',
    'explanation' => '所谓 = cái gọi là.',
    'sort_order' => 160,
  ),
  160 => 
  array (
    'hsk_level' => 5,
    'skill_type' => 'reading',
    'difficulty' => 'advanced',
    'question' => 'Chọn từ thích hợp: "他对待工作一贯认真负责，从不 ____ 。"',
    'pinyin' => 'Tā duìdài gōngzuò yíguàn rènzhēn fùzé, cóng bù ____ .',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '马虎 (mǎhu)',
      1 => '大方 (dàfang)',
      2 => '谦虚 (qiānxū)',
      3 => '活跃 (huóyuè)',
    ),
    'correct_answer' => '马虎 (mǎhu)',
    'explanation' => '认真负责 trái nghĩa với 马虎 (qua loa đại khái).',
    'sort_order' => 161,
  ),
  161 => 
  array (
    'hsk_level' => 5,
    'skill_type' => 'reading',
    'difficulty' => 'advanced',
    'question' => 'Đọc hiểu mẩu tin khoa học: "经常熬夜不仅会导致记忆力下降，还会削弱免疫系统的防护能力。"\\n-> Tác hại của thức khuya là gì?',
    'pinyin' => 'Àoyè bùjǐn huì dǎozhì...',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => 'Làm giảm trí nhớ và suy giảm hệ miễn dịch',
      1 => 'Tăng cường sức khỏe',
      2 => 'Giúp làm việc hiệu quả hơn',
      3 => 'Không có tác hại nào',
    ),
    'correct_answer' => 'Làm giảm trí nhớ và suy giảm hệ miễn dịch',
    'explanation' => '记忆力下降 = giảm trí nhớ; 削弱免疫 = suy giảm miễn dịch.',
    'sort_order' => 162,
  ),
  162 => 
  array (
    'hsk_level' => 5,
    'skill_type' => 'grammar',
    'difficulty' => 'advanced',
    'question' => 'Điền cặp từ liên kết thích hợp: "他 ____ 缺乏实践经验，____ 做事非常认真踏实。"',
    'pinyin' => 'Tā ____ quēfá shíjiàn jīngyàn, ____ zuòshì fēicháng rènzhēn tàshi.',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '固然...但... (gùrán... dàn...)',
      1 => '即使...也... (jíshǐ... yě...)',
      2 => '不仅...而且... (bùjǐn... érqiě...)',
      3 => '要么...要么... (yàome... yàome...)',
    ),
    'correct_answer' => '固然...但... (gùrán... dàn...)',
    'explanation' => '固然...但... biểu thị sự thừa nhận một thực tế ở vế đầu nhưng chuyển ý nhấn mạnh ưu điểm ở vế sau.',
    'sort_order' => 163,
  ),
  163 => 
  array (
    'hsk_level' => 5,
    'skill_type' => 'grammar',
    'difficulty' => 'advanced',
    'question' => 'Điền cặp từ liên kết: "我们 ____ 要注重经济增长的速度，____ 不能忽视生态环境的保护。"',
    'pinyin' => 'Wǒmen ____ yào zhùzhòng jīngjì zēngzhǎng, ____ bùnéng hūshì bǎohù.',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '既...又... (jì... yòu...)',
      1 => '宁可...也不... (nìngkě... yě bù...)',
      2 => '与其...不如... (yǔqí... bùrú...)',
      3 => '哪怕...也... (nǎpà... yě...)',
    ),
    'correct_answer' => '既...又... (jì... yòu...)',
    'explanation' => '既...又... biểu thị hai khía cạnh cùng tồn tại song song và quan trọng như nhau.',
    'sort_order' => 164,
  ),
  164 => 
  array (
    'hsk_level' => 5,
    'skill_type' => 'grammar',
    'difficulty' => 'advanced',
    'question' => 'Cấu trúc "宁可...也不..." (nìngkě... yě bù...) biểu thị:',
    'pinyin' => 'nìngkě... yě bù...',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => 'Thà chịu thiệt thòi ở vế trước chứ dứt khoát không làm điều ở vế sau',
      1 => 'Chấp nhận làm cả hai điều',
      2 => 'Không muốn làm điều gì cả',
      3 => 'Tùy duyên không lựa chọn',
    ),
    'correct_answer' => 'Thà chịu thiệt thòi ở vế trước chứ dứt khoát không làm điều ở vế sau',
    'explanation' => '宁可...也不... = Thà... cũng không...',
    'sort_order' => 165,
  ),
  165 => 
  array (
    'hsk_level' => 5,
    'skill_type' => 'grammar',
    'difficulty' => 'advanced',
    'question' => 'Sắp xếp câu chữ 把: "1. 他 / 2. 详细地 / 3. 把 / 4. 记录了下来 / 5. 整个实验过程"',
    'pinyin' => 'Tā bǎ zhènggè shíyàn guòchéng xiángxì de jìlù le xiàlai.',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '1 - 3 - 5 - 2 - 4 (他把整个实验过程详细地记录了下来。)',
      1 => '3 - 5 - 1 - 2 - 4 (把整个实验过程他详细地记录了下来。)',
      2 => '1 - 2 - 4 - 3 - 5 (他详细地记录了下来把整个实验过程。)',
      3 => '5 - 3 - 1 - 2 - 4 (整个实验过程把他详细地记录了下来。)',
    ),
    'correct_answer' => '1 - 3 - 5 - 2 - 4 (他把整个实验过程详细地记录了下来。)',
    'explanation' => 'Chủ ngữ (他) + 把 + Tân ngữ (整个实验过程) + Trạng ngữ (详细地) + Động từ và bổ ngữ kết quả (记录了下来).',
    'sort_order' => 166,
  ),
  166 => 
  array (
    'hsk_level' => 5,
    'skill_type' => 'grammar',
    'difficulty' => 'advanced',
    'question' => 'Sắp xếp câu: "1. 克服了 / 2. 种种困难 / 3. 终于取得了 / 4. 他们 / 5. 令人瞩目的成就"',
    'pinyin' => 'Tāmen kèfú le zhǒngzhǒng kùnnán, zhōngyú qǔdé le lìngrénzhǔmù de chéngjiù.',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '4 - 1 - 2 - 3 - 5 (他们克服了种种困难，终于取得了令人瞩目的成就。)',
      1 => '1 - 2 - 4 - 3 - 5 (克服了种种困难他们终于取得了令人瞩目的成就。)',
      2 => '4 - 3 - 5 - 1 - 2 (他们终于取得了令人瞩目的成就克服了种种困难。)',
      3 => '5 - 4 - 1 - 2 - 3 (令人瞩目的成就他们克服了种种困难终于取得了。)',
    ),
    'correct_answer' => '4 - 1 - 2 - 3 - 5 (他们克服了种种困难，终于取得了令人瞩目的成就。)',
    'explanation' => 'Chủ ngữ (他们) + Phân câu 1 (克服了种种困难) + Phân câu 2 (终于取得了令人瞩目的成就).',
    'sort_order' => 167,
  ),
  167 => 
  array (
    'hsk_level' => 5,
    'skill_type' => 'grammar',
    'difficulty' => 'advanced',
    'question' => 'Điền từ thích hợp: "出门前请仔细检查门窗，____ 发生意外。"',
    'pinyin' => 'Chūmén qián qǐng zǐxì jiǎnchá ménchuāng, ____ fāshēng yìwài.',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '以免 (yǐmiǎn)',
      1 => '因而 (yīn\'ér)',
      2 => '既然 (jìrán)',
      3 => '从而 (cóng\'ér)',
    ),
    'correct_answer' => '以免 (yǐmiǎn)',
    'explanation' => '以免 = để tránh khỏi, nhằm tránh xảy ra việc không mong muốn.',
    'sort_order' => 168,
  ),
  168 => 
  array (
    'hsk_level' => 5,
    'skill_type' => 'grammar',
    'difficulty' => 'advanced',
    'question' => 'Sắp xếp câu cấu trúc "与其...不如...": "1. 坐以待毙 / 2. 主动出击 / 3. 与其 / 4. 不如"',
    'pinyin' => 'Yǔqí zuòyǐdàibì, bùrú zhǔdòng chūjī.',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '3 - 1 - 4 - 2 (与其坐以待毙，不如主动出击。)',
      1 => '1 - 3 - 2 - 4 (坐以待毙与其主动出击不如。)',
      2 => '4 - 2 - 3 - 1 (不如主动出击与其坐以待毙。)',
      3 => '3 - 2 - 4 - 1 (与其主动出击，不如坐以待毙。)',
    ),
    'correct_answer' => '3 - 1 - 4 - 2 (与其坐以待毙，不如主动出击。)',
    'explanation' => '与其 A 不如 B: So với việc A (chờ chết) thì chi bằng B (chủ động tấn công).',
    'sort_order' => 169,
  ),
  169 => 
  array (
    'hsk_level' => 5,
    'skill_type' => 'grammar',
    'difficulty' => 'advanced',
    'question' => 'Điền từ nối: "深入调查研究，____ 提出切实可行的解决方案。"',
    'pinyin' => 'Shēnrù diàochá yánjiū, ____ tíchū qièshí kěxíng de jiějué fāng\'àn.',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '进而 (jìn\'ér)',
      1 => '然而 (rán\'ér)',
      2 => '反正 (fǎnzhèng)',
      3 => '偏偏 (piānpiān)',
    ),
    'correct_answer' => '进而 (jìn\'ér)',
    'explanation' => '进而 biểu thị hành động sau là bước phát triển tiếp theo sâu sắc hơn hành động trước.',
    'sort_order' => 170,
  ),
  170 => 
  array (
    'hsk_level' => 5,
    'skill_type' => 'grammar',
    'difficulty' => 'advanced',
    'question' => 'Cấu trúc "除非...否则..." mang nghĩa:',
    'pinyin' => 'chúfēi... fǒuzé...',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => 'Trừ phi... nếu không thì... (điều kiện tất yếu)',
      1 => 'Bởi vì... cho nên...',
      2 => 'Mặc dù... nhưng...',
      3 => 'Vừa... vừa...',
    ),
    'correct_answer' => 'Trừ phi... nếu không thì... (điều kiện tất yếu)',
    'explanation' => '除非...否则... biểu thị điều kiện tiền đề duy nhất để ngăn ngừa hậu quả.',
    'sort_order' => 171,
  ),
  171 => 
  array (
    'hsk_level' => 5,
    'skill_type' => 'grammar',
    'difficulty' => 'advanced',
    'question' => 'Sắp xếp câu: "1. 展现了 / 2. 独特的 / 3. 这部电影 / 4. 艺术魅力 / 5. 导演"',
    'pinyin' => 'Zhè bù diànyǐng zhǎnxiàn le dǎoyǎn dútè de yìshù mèilì.',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '3 - 1 - 5 - 2 - 4 (这部电影展现了导演独特的艺术魅力。)',
      1 => '5 - 1 - 3 - 2 - 4 (导演展现了这部电影独特的艺术魅力。)',
      2 => '3 - 2 - 4 - 1 - 5 (这部电影独特的艺术魅力展现了导演。)',
      3 => '1 - 5 - 2 - 4 - 3 (展现了导演独特的艺术魅力这部电影。)',
    ),
    'correct_answer' => '3 - 1 - 5 - 2 - 4 (这部电影展现了导演独特的艺术魅力。)',
    'explanation' => 'Chủ ngữ (这部电影) + Động từ (展现了) + Tân ngữ phức hợp (导演独特的艺术魅力).',
    'sort_order' => 172,
  ),
  172 => 
  array (
    'hsk_level' => 5,
    'skill_type' => 'grammar',
    'difficulty' => 'advanced',
    'question' => 'Sắp xếp câu bị động chữ 被: "1. 彻底消除了 / 2. 双方的误会 / 3. 被 / 4. 坦诚的沟通"',
    'pinyin' => 'Shuāngfāng de wùhuì bèi tǎnchéng de gōutōng chèdǐ xiāochú le.',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '2 - 3 - 4 - 1 (双方的误会给坦诚的沟通彻底消除了。)',
      1 => '4 - 3 - 2 - 1 (坦诚的沟通被双方的误会彻底消除了。)',
      2 => '2 - 1 - 3 - 4 (双方的误会彻底消除了被坦诚的沟通。)',
      3 => '1 - 2 - 3 - 4 (彻底消除了双方的误会被坦诚的沟通。)',
    ),
    'correct_answer' => '2 - 3 - 4 - 1 (双方的误会给坦诚的沟通彻底消除了。)',
    'explanation' => 'Vật bị tác động (双方的误会) + 被 + Chủ thể gây tác động (坦诚的沟通) + Động từ (彻底消除了).',
    'sort_order' => 173,
  ),
  173 => 
  array (
    'hsk_level' => 5,
    'skill_type' => 'grammar',
    'difficulty' => 'advanced',
    'question' => 'Chọn trợ từ ngữ khí thích hợp: "别太在意别人的议论，做好自己 ____ 。"',
    'pinyin' => 'Bié tài zàiyì biérén de yìlùn, zuò hǎo zìjǐ ____ .',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '罢了 (bàle)',
      1 => '而已 (éryǐ)',
      2 => '的话 (dehuà)',
      3 => '似的 (shìde)',
    ),
    'correct_answer' => '罢了 (bàle)',
    'explanation' => '罢了 đứng cuối câu biểu thị sắc thái nhẹ nhàng: "mà thôi / là được rồi".',
    'sort_order' => 174,
  ),
  174 => 
  array (
    'hsk_level' => 5,
    'skill_type' => 'grammar',
    'difficulty' => 'advanced',
    'question' => 'Sắp xếp câu: "1. 广泛的赞誉 / 2. 赢得了 / 3. 他的精湛技艺 / 4. 观众的"',
    'pinyin' => 'Tā de jīngzhàn jìyì yíngdé le guānzhòng de guǎngfàn zànyù.',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '3 - 2 - 4 - 1 (他的精湛技艺赢得了观众的广泛赞誉。)',
      1 => '4 - 1 - 2 - 3 (观众的广泛赞誉赢得了他的精湛技艺。)',
      2 => '3 - 4 - 1 - 2 (他的精湛技艺观众的广泛赞誉赢得了。)',
      3 => '2 - 1 - 3 - 4 (赢得了广泛赞誉他的精湛技艺观众的。)',
    ),
    'correct_answer' => '3 - 2 - 4 - 1 (他的精湛技艺赢得了观众的广泛赞誉。)',
    'explanation' => 'Chủ ngữ (他的精湛技艺) + Động từ (赢得了) + Tân ngữ (观众的广泛赞誉).',
    'sort_order' => 175,
  ),
  175 => 
  array (
    'hsk_level' => 5,
    'skill_type' => 'grammar',
    'difficulty' => 'advanced',
    'question' => 'Điền liên từ phản nghịch: "他不但没有认错，____ 变本加厉地指责别人。"',
    'pinyin' => 'Tā búdàn méiyǒu rèncuò, ____ biànběnjiālì de zhǐzé biérén.',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '反而 (fǎn\'ér)',
      1 => '反正 (fǎnzhèng)',
      2 => '反而 (fán\'ér)',
      3 => '然而 (rán\'ér)',
    ),
    'correct_answer' => '反而 (fǎn\'ér)',
    'explanation' => '不但不/没有...反而... biểu thị sự việc không theo chiều hướng bình thường mà quay ngoắt ngược lại.',
    'sort_order' => 176,
  ),
  176 => 
  array (
    'hsk_level' => 5,
    'skill_type' => 'grammar',
    'difficulty' => 'advanced',
    'question' => 'Sắp xếp câu: "1. 有效提升了 / 2. 团队的凝聚力 / 3. 这次团建活动 / 4. 员工之间的信任感与"',
    'pinyin' => 'Zhè cì tuánjiàn huódòng yǒuxiào tíshēng le...',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '3 - 1 - 4 - 2 (这次团建活动有效提升了员工之间的信任感与团队的凝聚力。)',
      1 => '1 - 2 - 3 - 4 (有效提升了团队的凝聚力这次团建活动员工之间的信任感与。)',
      2 => '4 - 2 - 1 - 3 (员工之间的信任感与团队的凝聚力有效提升了这次团建活动。)',
      3 => '3 - 4 - 2 - 1 (这次团建活动员工之间的信任感与团队的凝聚力有效提升了。)',
    ),
    'correct_answer' => '3 - 1 - 4 - 2 (这次团建活动有效提升了员工之间的信任感与团队的凝聚力。)',
    'explanation' => 'Chủ ngữ (这次团建活动) + Trạng từ và Động từ (有效提升了) + Tân ngữ song hành (员工之间的信任感与团队的凝聚力).',
    'sort_order' => 177,
  ),
  177 => 
  array (
    'hsk_level' => 5,
    'skill_type' => 'grammar',
    'difficulty' => 'advanced',
    'question' => 'Điền từ thích hợp: "任何事物的发展都是一个循序渐进的 ____ ，不可急于求成。"',
    'pinyin' => 'Dōu shì yí gè xúnxùjiànjìn de ____ ...',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '过程 (guòchéng)',
      1 => '结果 (jiéguǒ)',
      2 => '原因 (yuányīn)',
      3 => '道理 (dàolǐ)',
    ),
    'correct_answer' => '过程 (guòchéng)',
    'explanation' => '循序渐进的过程 = một quá trình tuần tự từng bước.',
    'sort_order' => 178,
  ),
  178 => 
  array (
    'hsk_level' => 5,
    'skill_type' => 'grammar',
    'difficulty' => 'advanced',
    'question' => 'Sắp xếp câu: "1. 无论 / 2. 他都 / 3. 刮风下雨 / 4. 坚持晨跑"',
    'pinyin' => 'Wúlùn guāfēng xiàyǔ, tā dōu jiānchí chénpǎo.',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '1 - 3 - 2 - 4 (无论刮风下雨，他都坚持晨跑。)',
      1 => '2 - 4 - 1 - 3 (他都坚持晨跑无论刮风下雨。)',
      2 => '1 - 2 - 3 - 4 (无论他都刮风下雨坚持晨跑。)',
      3 => '3 - 1 - 2 - 4 (刮风下雨无论他都坚持晨跑。)',
    ),
    'correct_answer' => '1 - 3 - 2 - 4 (无论刮风下雨，他都坚持晨跑。)',
    'explanation' => '无论 + Điều kiện bất kể (刮风下雨), 主语 + 都 + Kết quả kiên định (他都坚持晨跑).',
    'sort_order' => 179,
  ),
  179 => 
  array (
    'hsk_level' => 5,
    'skill_type' => 'grammar',
    'difficulty' => 'advanced',
    'question' => 'Chọn cấu trúc câu thích hợp: "____ 他不愿多谈，我 ____ 不便勉强他。"',
    'pinyin' => '____ tā bú yuàn duō tán, wǒ ____ búbiàn miǎnqiáng tā.',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '既然...也... (jìrán... yě...)',
      1 => '如果...就... (rúguǒ... jiù...)',
      2 => '只要...就... (zhǐyào... jiù...)',
      3 => '固然...但... (gùrán... dàn...)',
    ),
    'correct_answer' => '既然...也... (jìrán... yě...)',
    'explanation' => '既然...也/就... biểu thị tiền đề thực tế đã biết trước: Một khi anh ấy không muốn nói nhiều thì tôi cũng không tiện ép buộc.',
    'sort_order' => 180,
  ),
  180 => 
  array (
    'hsk_level' => 6,
    'skill_type' => 'listening',
    'difficulty' => 'advanced',
    'question' => 'Nghe và cho biết trọng tâm diễn thuyết của chuyên gia kinh tế:',
    'pinyin' => 'Zài quánqiúhuà nìliú de bèijǐng xià, gèguó yīnggāi jiānchí kāifàng bāoróng, bìmiǎn zhíxíng línghé bóyì.',
    'audio_text' => '在全球化逆流的背景下，各国应该坚持开放包容，避免执行零和博弈。',
    'options' => 
    array (
      0 => 'Kiên trì cởi mở bao dung, tránh tư duy trò chơi tổng bằng không (Zero-sum game)',
      1 => 'Đóng cửa biên giới để bảo hộ sản xuất trong nước',
      2 => 'Tăng cường cạnh tranh quân sự',
      3 => 'Từ bỏ các hiệp định kinh tế song phương',
    ),
    'correct_answer' => 'Kiên trì cởi mở bao dung, tránh tư duy trò chơi tổng bằng không (Zero-sum game)',
    'explanation' => '开放包容 = cởi mở bao dung; 零和博弈 = trò chơi tổng bằng không.',
    'sort_order' => 181,
  ),
  181 => 
  array (
    'hsk_level' => 6,
    'skill_type' => 'listening',
    'difficulty' => 'advanced',
    'question' => 'Nghe và cho biết tranh luận cốt lõi về đạo đức trí tuệ nhân tạo:',
    'pinyin' => 'Réngōng zhìnéng suànfǎ de hēixiāng xiàoyìng hé shùjù piānjiàn, zhèngzài duì shèhuì gōngpíng tíchū yánjùn tiǎozhàn.',
    'audio_text' => '人工智能算法的黑箱效应和数据偏见，正在对社会公平提出严峻挑战。',
    'options' => 
    array (
      0 => 'Hiệu ứng hộp đen và định kiến dữ liệu của thuật toán AI thách thức sự công bằng xã hội',
      1 => 'AI giúp loại bỏ hoàn toàn mọi sai lầm của con người',
      2 => 'Không cần đặt ra ranh giới pháp lý cho AI',
      3 => 'AI phát triển chậm hơn dự kiến',
    ),
    'correct_answer' => 'Hiệu ứng hộp đen và định kiến dữ liệu của thuật toán AI thách thức sự công bằng xã hội',
    'explanation' => '黑箱效应 = hiệu ứng hộp đen; 数据偏见 = định kiến dữ liệu; 社会公平 = công bằng xã hội.',
    'sort_order' => 182,
  ),
  182 => 
  array (
    'hsk_level' => 6,
    'skill_type' => 'listening',
    'difficulty' => 'advanced',
    'question' => 'Nghe và cho biết thông điệp bảo tồn đa dạng sinh học:',
    'pinyin' => 'Wùzhǒng de mièjué sùdù qiánsuǒwèiyǒu, bǎohù shēngwù duōyàngxìng jiùshì bǎohù rénlèi zìshēn de fányǎn jīchǔ.',
    'audio_text' => '物种的灭绝速度前所未有，保护生物多样性就是保护人类自身的繁衍基础。',
    'options' => 
    array (
      0 => 'Bảo vệ đa dạng sinh học là bảo vệ nền tảng sinh tồn và sinh sôi của chính loài người',
      1 => 'Tuyệt chủng loài là quy luật không cần can thiệp',
      2 => 'Chỉ bảo tồn các loài động vật có giá trị kinh tế',
      3 => 'Hệ sinh thái không ảnh hưởng đến con người',
    ),
    'correct_answer' => 'Bảo vệ đa dạng sinh học là bảo vệ nền tảng sinh tồn và sinh sôi của chính loài người',
    'explanation' => '生物多样性 = đa dạng sinh học; 繁衍基础 = nền tảng sinh sôi nảy nở.',
    'sort_order' => 183,
  ),
  183 => 
  array (
    'hsk_level' => 6,
    'skill_type' => 'listening',
    'difficulty' => 'advanced',
    'question' => 'Nghe và cho biết nhận định triết học về nhận thức:',
    'pinyin' => 'Rén de rènzhī wǎngwǎng shòudào gùyǒu sīwéi dìngshì de jùshù, xūyào bùduàn tōngguò fǎnsī lái dǎpò jiānghuà.',
    'audio_text' => '人的认知往往受到固有思维定势的拘束，需要不断通过反思来打破僵化。',
    'options' => 
    array (
      0 => 'Nhận thức của con người bị ràng buộc bởi lối mòn tư duy, cần tự phản tỉnh để phá vỡ sự xơ cứng',
      1 => 'Tư duy của con người hoàn toàn tự do',
      2 => 'Không cần phản tư lại các định kiến cũ',
      3 => 'Lối mòn tư duy luôn luôn chính xác',
    ),
    'correct_answer' => 'Nhận thức của con người bị ràng buộc bởi lối mòn tư duy, cần tự phản tỉnh để phá vỡ sự xơ cứng',
    'explanation' => '思维定势 = định kiến / lối mòn tư duy; 反思 = phản tỉnh; 打破僵化 = phá vỡ sự rập khuôn xơ cứng.',
    'sort_order' => 184,
  ),
  184 => 
  array (
    'hsk_level' => 6,
    'skill_type' => 'listening',
    'difficulty' => 'advanced',
    'question' => 'Nghe và cho biết thách thức trong phát triển đô thị hiện đại:',
    'pinyin' => 'Zài kuàisù chéngshìhuà guòchéng zhōng, rúhé pínghéng wénhuà yíchǎn bǎohù yǔ jīngjì jiànshè shì yí gè zhòngdà kètí.',
    'audio_text' => '在快速城市化过程中，如何平衡文化遗产保护与经济建设是一个重大课题。',
    'options' => 
    array (
      0 => 'Cân bằng giữa bảo tồn di sản văn hóa và xây dựng kinh tế trong quá trình đô thị hóa',
      1 => 'Phá bỏ toàn bộ di sản để xây chung cư',
      2 => 'Ngừng hoàn toàn việc phát triển kinh tế',
      3 => 'Đô thị hóa không liên quan đến văn hóa',
    ),
    'correct_answer' => 'Cân bằng giữa bảo tồn di sản văn hóa và xây dựng kinh tế trong quá trình đô thị hóa',
    'explanation' => '文化遗产 = di sản văn hóa; 平衡 = cân bằng.',
    'sort_order' => 185,
  ),
  185 => 
  array (
    'hsk_level' => 6,
    'skill_type' => 'listening',
    'difficulty' => 'advanced',
    'question' => 'Nghe và cho biết tiềm năng của điện toán lượng tử:',
    'pinyin' => 'Liàngzǐ jìsuàn de fēiyuèshì fāzhǎn, jiāng duì xiàndài mìxué hé fùzá xìtǒng de móshǐ chǎnlì dài lái gémìngxìng yǐngxiǎng.',
    'audio_text' => '量子计算的飞跃式发展，将对现代密码学和复杂系统的模拟带来革命性影响。',
    'options' => 
    array (
      0 => 'Tạo ra ảnh hưởng mang tính cách mạng cho mật mã học hiện đại và mô phỏng hệ thống phức tạp',
      1 => 'Không có khác biệt so với máy tính thông thường',
      2 => 'Chỉ ứng dụng trong trò chơi giải trí',
      3 => 'Đã đạt tới giới hạn phát triển',
    ),
    'correct_answer' => 'Tạo ra ảnh hưởng mang tính cách mạng cho mật mã học hiện đại và mô phỏng hệ thống phức tạp',
    'explanation' => '量子计算 = tính toán lượng tử; 密码学 = mật mã học; 革命性影响 = ảnh hưởng mang tính cách mạng.',
    'sort_order' => 186,
  ),
  186 => 
  array (
    'hsk_level' => 6,
    'skill_type' => 'listening',
    'difficulty' => 'advanced',
    'question' => 'Nghe và cho biết yếu tố then chốt trong ngoại giao đa phương:',
    'pinyin' => 'Duōbiān wàijiāo de shíjì zhìlǐ, xūyào tuīchū fúhé gèfāng lìyì de zhìdùxìng gòngshí.',
    'audio_text' => '多边外交的实际治理，需要推出符合各方利益的制度性共识。',
    'options' => 
    array (
      0 => 'Cần đưa ra sự đồng thuận mang tính thể chế phù hợp với lợi ích các bên',
      1 => 'Áp đặt ý chí của một nước duy nhất',
      2 => 'Bỏ qua các nguyên tắc pháp lý quốc tế',
      3 => 'Không cần đối thoại nhiều bên',
    ),
    'correct_answer' => 'Cần đưa ra sự đồng thuận mang tính thể chế phù hợp với lợi ích các bên',
    'explanation' => '多边外交 = ngoại giao đa phương; 制度性共识 = đồng thuận mang tính thể chế.',
    'sort_order' => 187,
  ),
  187 => 
  array (
    'hsk_level' => 6,
    'skill_type' => 'listening',
    'difficulty' => 'advanced',
    'question' => 'Nghe và cho biết vai trò của giáo dục thẩm mỹ:',
    'pinyin' => 'Měiyù bú jǐnjǐn shì jìfǎ de chuánshòu, gèng shì duì gèxìng línghún hé shěnměi pǐnwèi de sùzào.',
    'audio_text' => '美育不仅仅是技法的传授，更是对个性灵魂和审美品位的塑造。',
    'options' => 
    array (
      0 => 'Giáo dục thẩm mỹ nhào nặn tâm hồn và thị hiếu thưởng thức chứ không chỉ truyền thụ kỹ xảo',
      1 => 'Chỉ dạy các kỹ thuật vẽ tranh cơ bản',
      2 => 'Thẩm mỹ không cần giáo dục',
      3 => 'Chỉ dành cho nghệ sĩ chuyên nghiệp',
    ),
    'correct_answer' => 'Giáo dục thẩm mỹ nhào nặn tâm hồn và thị hiếu thưởng thức chứ không chỉ truyền thụ kỹ xảo',
    'explanation' => '美育 = giáo dục thẩm mỹ; 塑造审美品位 = nhào nặn thị hiếu thẩm mỹ.',
    'sort_order' => 188,
  ),
  188 => 
  array (
    'hsk_level' => 6,
    'skill_type' => 'listening',
    'difficulty' => 'advanced',
    'question' => 'Nghe và cho biết bước tiến của y học chính xác:',
    'pinyin' => 'Jīngzhǔn yīxué tōngguò jīyīn cèxù hé dàshùjù fēnxī, shíxiàn le duì jíbìng de gèxìnghuà dìngzhì zhìliáo.',
    'audio_text' => '精准医学通过基因测序和大数据分析，实现了对疾病的个性化定制治疗。',
    'options' => 
    array (
      0 => 'Hiện thực hóa phác đồ điều trị cá nhân hóa nhờ giải trình tự gen và phân tích dữ liệu lớn',
      1 => 'Dùng chung một loại thuốc cho tất cả bệnh nhân',
      2 => 'Không cần phân tích xét nghiệm gen',
      3 => 'Chỉ dựa vào trực giác của bác sĩ',
    ),
    'correct_answer' => 'Hiện thực hóa phác đồ điều trị cá nhân hóa nhờ giải trình tự gen và phân tích dữ liệu lớn',
    'explanation' => '精准医学 = y học chính xác; 个性化定制 = điều trị cá nhân hóa.',
    'sort_order' => 189,
  ),
  189 => 
  array (
    'hsk_level' => 6,
    'skill_type' => 'listening',
    'difficulty' => 'advanced',
    'question' => 'Nghe và cho biết nguyên lý quản trị doanh nghiệp bền vững:',
    'pinyin' => 'Qǐyè bù yīng jǐnjǐn zhuīqiú duǎnqī lìrùn zuìdàhuà, ér yīng jiāng huánbǎo yǔ shèhuì zérèn nàrù héxīn zhànlüè.',
    'audio_text' => '企业不应仅仅追求短期利润最大化，而应将环保与社会责任纳入核心战略。',
    'options' => 
    array (
      0 => 'Đưa bảo vệ môi trường và trách nhiệm xã hội vào chiến lược cốt lõi thay vì chỉ tối đa hóa lợi nhuận ngắn hạn',
      1 => 'Bất chấp mọi thủ đoạn để đạt lợi nhuận tối đa',
      2 => 'Cắt giảm toàn bộ chi phí xử lý chất thải',
      3 => 'Không cần thực hiện trách nhiệm xã hội',
    ),
    'correct_answer' => 'Đưa bảo vệ môi trường và trách nhiệm xã hội vào chiến lược cốt lõi thay vì chỉ tối đa hóa lợi nhuận ngắn hạn',
    'explanation' => '社会责任 = trách nhiệm xã hội (CSR); 核心战略 = chiến lược cốt lõi.',
    'sort_order' => 190,
  ),
  190 => 
  array (
    'hsk_level' => 6,
    'skill_type' => 'listening',
    'difficulty' => 'advanced',
    'question' => 'Nghe và cho biết mối quan hệ giữa sở hữu trí tuệ và đổi mới:',
    'pinyin' => 'Yángé de zhīshichǎnquán bǎohù shì jīlì yuánchuàngxìng yánfā hé péiyù chuàngxīn shēngtài de guānjiàn.',
    'audio_text' => '严格的知识产权保护是激励原创性研发和培育创新生态的关键。',
    'options' => 
    array (
      0 => 'Bảo hộ nghiêm ngặt quyền sở hữu trí tuệ là then chốt để khích lệ R&D nguyên bản và ươm mầm hệ sinh thái đổi mới',
      1 => 'Vi phạm bản quyền giúp phổ biến công nghệ nhanh hơn',
      2 => 'Không cần đăng ký bằng sáng chế',
      3 => 'Quyền sở hữu trí tuệ kìm hãm sự phát triển',
    ),
    'correct_answer' => 'Bảo hộ nghiêm ngặt quyền sở hữu trí tuệ là then chốt để khích lệ R&D nguyên bản và ươm mầm hệ sinh thái đổi mới',
    'explanation' => '知识产权 = sở hữu trí tuệ; 原创性研发 = R&D nguyên bản; 创新生态 = hệ sinh thái đổi mới.',
    'sort_order' => 191,
  ),
  191 => 
  array (
    'hsk_level' => 6,
    'skill_type' => 'listening',
    'difficulty' => 'advanced',
    'question' => 'Nghe và cho biết triết lý phát triển toàn cầu:',
    'pinyin' => 'Rénlèi mìngyùn gòngtóngtǐ lǐniàn zhǔzhāng bǐcǐ yīcún、hézuò gòngyíng, gòngtóng yìngduì quánqiúxìng tiǎozhàn.',
    'audio_text' => '人类命运共同体理念主张彼此依存、合作共赢，共同应对全球性挑战。',
    'options' => 
    array (
      0 => 'Chủ trương nương tựa lẫn nhau, hợp tác cùng thắng để ứng phó với các thách thức mang tính toàn cầu',
      1 => 'Chủ trương đơn phương hành động',
      2 => 'Chia rẽ các khối liên minh',
      3 => 'Các nước tự lo liệu không hợp tác',
    ),
    'correct_answer' => 'Chủ trương nương tựa lẫn nhau, hợp tác cùng thắng để ứng phó với các thách thức mang tính toàn cầu',
    'explanation' => '人类命运共同体 = cộng đồng chung vận mệnh nhân loại; 合作共赢 = hợp tác cùng thắng.',
    'sort_order' => 192,
  ),
  192 => 
  array (
    'hsk_level' => 6,
    'skill_type' => 'reading',
    'difficulty' => 'advanced',
    'question' => 'Thành ngữ "纸上谈兵" (zhǐ shàng tán bīng) dùng để phê phán thói xấu nào?',
    'pinyin' => 'zhǐ shàng tán bīng',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => 'Bàn việc quân trên giấy (Chỉ nói lý thuyết suông, không có thực tiễn)',
      1 => 'Kế hoạch tác chiến quá xuất sắc',
      2 => 'Vẽ sơ đồ chiến lược tinh xảo',
      3 => 'Lười nhác không chịu đọc sách',
    ),
    'correct_answer' => 'Bàn việc quân trên giấy (Chỉ nói lý thuyết suông, không có thực tiễn)',
    'explanation' => '纸上谈兵 bắt nguồn từ điển tích Triệu Quát đánh trận chỉ thuộc làu binh thư nhưng thực tế thất bại thảm hại.',
    'sort_order' => 193,
  ),
  193 => 
  array (
    'hsk_level' => 6,
    'skill_type' => 'reading',
    'difficulty' => 'advanced',
    'question' => 'Thành ngữ "画龙点睛" (huà lóng diǎn jīng) mang ý nghĩa ẩn dụ gì?',
    'pinyin' => 'huà lóng diǎn jīng',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => 'Họa long điểm nhãn (Thêm chi tiết then chốt làm toàn bộ tác phẩm/nội dung bừng sáng)',
      1 => 'Vẽ rồng vẽ rắn rườm rà',
      2 => 'Vẽ tranh phong cảnh cổ điển',
      3 => 'Làm việc gì cũng dở dang',
    ),
    'correct_answer' => 'Họa long điểm nhãn (Thêm chi tiết then chốt làm toàn bộ tác phẩm/nội dung bừng sáng)',
    'explanation' => '画龙点睛 chỉ việc thêm nét chấm phá đắt giá vào thời điểm quyết định.',
    'sort_order' => 194,
  ),
  194 => 
  array (
    'hsk_level' => 6,
    'skill_type' => 'reading',
    'difficulty' => 'advanced',
    'question' => 'Từ "毋庸置疑" (wú yōng zhì yí) có nghĩa là gì?',
    'pinyin' => 'wú yōng zhì yí',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => 'Không còn chút nghi ngờ nào nữa (Hiển nhiên đúng)',
      1 => 'Vô cùng đáng nghi ngờ',
      2 => 'Không nên bàn tán',
      3 => 'Hoàn toàn vô căn cứ',
    ),
    'correct_answer' => 'Không còn chút nghi ngờ nào nữa (Hiển nhiên đúng)',
    'explanation' => '毋庸 = không cần; 置疑 = hoài nghi, đặt nghi vấn.',
    'sort_order' => 195,
  ),
  195 => 
  array (
    'hsk_level' => 6,
    'skill_type' => 'reading',
    'difficulty' => 'advanced',
    'question' => 'Đọc hiểu logic: "经济全球化是一把双刃剑，既带来了前所未有的机遇，也蕴藏着深刻的风险。"\\n-> Nghĩa của "双刃剑" (thanh kiếm hai lưỡi) là gì?',
    'pinyin' => 'Shuāngrènjiàn...',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => 'Vấn đề có cả mặt lợi lẫn mặt hại song hành',
      1 => 'Vũ khí nguy hiểm trong chiến tranh',
      2 => 'Một biện pháp hoàn toàn có lợi',
      3 => 'Một tai họa hoàn toàn tiêu cực',
    ),
    'correct_answer' => 'Vấn đề có cả mặt lợi lẫn mặt hại song hành',
    'explanation' => '双刃剑 biểu thị tính hai mặt mâu thuẫn nhưng thống nhất của một hiện tượng.',
    'sort_order' => 196,
  ),
  196 => 
  array (
    'hsk_level' => 6,
    'skill_type' => 'reading',
    'difficulty' => 'advanced',
    'question' => 'Phân biệt "赋予" (fùyǔ) và "给予" (jǐyǔ):',
    'pinyin' => 'fùyǔ vs jǐyǔ',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '赋予 mang sắc thái trang trọng, thường đi với sứ mệnh (使命) hoặc ý nghĩa (意义); 给予 mang nghĩa trao tặng chung',
      1 => '赋予 chỉ dùng cho tiền bạc',
      2 => 'Không có bất kỳ sự khác biệt nào',
      3 => '给予 chỉ dùng cho người thân',
    ),
    'correct_answer' => '赋予 mang sắc thái trang trọng, thường đi với sứ mệnh (使命) hoặc ý nghĩa (意义); 给予 mang nghĩa trao tặng chung',
    'explanation' => '赋予使命 = trao ban sứ mệnh.',
    'sort_order' => 197,
  ),
  197 => 
  array (
    'hsk_level' => 6,
    'skill_type' => 'reading',
    'difficulty' => 'advanced',
    'question' => 'Chọn từ kết hợp chuẩn (Collocation): "我们必须努力 ____ 民族与文化之间的偏见。"',
    'pinyin' => 'Wǒmen bìxū nǔlì ____ piānjiàn.',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '消除 (xiāochú)',
      1 => '产生 (chǎnshēng)',
      2 => '增加 (zēngjiā)',
      3 => '扩大 (kuòdà)',
    ),
    'correct_answer' => '消除 (xiāochú)',
    'explanation' => '消除偏见 = xóa bỏ định kiến.',
    'sort_order' => 198,
  ),
  198 => 
  array (
    'hsk_level' => 6,
    'skill_type' => 'reading',
    'difficulty' => 'advanced',
    'question' => 'Đọc hiểu logic: "保护环境，人人有责，这已在国际社会达成了广泛的共识。"\\n-> Từ "共识" (gòngshí) có nghĩa là:',
    'pinyin' => 'gòng shí',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => 'Sự đồng thuận / Nhận thức chung của cộng đồng',
      1 => 'Sự tranh cãi kịch liệt',
      2 => 'Ý kiến độc đoán của một cá nhân',
      3 => 'Hiểu lầm chưa thể giải tỏa',
    ),
    'correct_answer' => 'Sự đồng thuận / Nhận thức chung của cộng đồng',
    'explanation' => '共识 = nhận thức chung, sự đồng thuận.',
    'sort_order' => 199,
  ),
  199 => 
  array (
    'hsk_level' => 6,
    'skill_type' => 'reading',
    'difficulty' => 'advanced',
    'question' => 'Thành ngữ "班门弄斧" (bān mén nòng fǔ) dùng để chỉ hành vi gì?',
    'pinyin' => 'bān mén nòng fǔ',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => 'Múa rìu qua mắt thợ (Khoe khoang tài nghệ trước bậc thầy lão luyện)',
      1 => 'Chăm chỉ học nghề mộc',
      2 => 'Rèn luyện rìu sắc bén',
      3 => 'Tôn sư trọng đạo',
    ),
    'correct_answer' => 'Múa rìu qua mắt thợ (Khoe khoang tài nghệ trước bậc thầy lão luyện)',
    'explanation' => '班 môn lộng phủ (Lỗ Ban là ông tổ nghề mộc, múa rìu trước cửa nhà Lỗ Ban).',
    'sort_order' => 200,
  ),
  200 => 
  array (
    'hsk_level' => 6,
    'skill_type' => 'reading',
    'difficulty' => 'advanced',
    'question' => 'Từ "鉴于" (jiànyú) thường được sử dụng như thế nào trong văn bản chính luận?',
    'pinyin' => 'jiàn yú',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => 'Đứng ở đầu câu nêu căn cứ, xét thấy tình hình thực tế để đưa ra quyết sách',
      1 => 'Đứng ở cuối câu cảm thán',
      2 => 'Dùng làm trợ từ nghi vấn',
      3 => 'Dùng làm phó từ phủ định',
    ),
    'correct_answer' => 'Đứng ở đầu câu nêu căn cứ, xét thấy tình hình thực tế để đưa ra quyết sách',
    'explanation' => '鉴于 = 考虑到 (Xét thấy / Căn cứ vào).',
    'sort_order' => 201,
  ),
  201 => 
  array (
    'hsk_level' => 6,
    'skill_type' => 'reading',
    'difficulty' => 'advanced',
    'question' => 'Từ "潜移默化" (qián yí mò huà) miêu tả quá trình tác động như thế nào?',
    'pinyin' => 'qián yí mò huà',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => 'Thấm nhuần ngầm dần dần, biến đổi một cách vô hình theo thời gian',
      1 => 'Tác động bạo lực đột ngột',
      2 => 'Chỉ sự thay đổi bề ngoài không thực chất',
      3 => 'Thay đổi một cách cưỡng ép',
    ),
    'correct_answer' => 'Thấm nhuần ngầm dần dần, biến đổi một cách vô hình theo thời gian',
    'explanation' => '潜移默化 = mưa dầm thấm lâu, tác động âm thầm chuyển hóa.',
    'sort_order' => 202,
  ),
  202 => 
  array (
    'hsk_level' => 6,
    'skill_type' => 'reading',
    'difficulty' => 'advanced',
    'question' => 'Thành ngữ "破釜沉舟" (pò fǔ chén zhōu) biểu thị ý chí và quyết tâm thế nào?',
    'pinyin' => 'pò fǔ chén zhōu',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => 'Đập nồi dìm thuyền (Quyết tâm tử chiến, cắt đứt đường lui để giành chiến thắng)',
      1 => 'Tiết kiệm lương thực',
      2 => 'Chế tạo thuyền chiến kiên cố',
      3 => 'Bỏ cuộc giữa chừng',
    ),
    'correct_answer' => 'Đập nồi dìm thuyền (Quyết tâm tử chiến, cắt đứt đường lui để giành chiến thắng)',
    'explanation' => 'Điển tích Hạng Vũ đánh trận Cự Lộc đập vỡ nồi niêu, dìm thuyền chìm để quân sĩ chỉ còn nước tiến công quyết thắng.',
    'sort_order' => 203,
  ),
  203 => 
  array (
    'hsk_level' => 6,
    'skill_type' => 'reading',
    'difficulty' => 'advanced',
    'question' => 'Chọn từ thích hợp: "该物种数量急剧缩减，目前已 ____ 灭绝的边缘。"',
    'pinyin' => 'Yǐ ____ mièjué de biānyuán.',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '濒临 (bīnlín)',
      1 => '面临 (miànlín)',
      2 => '到达 (dàodá)',
      3 => '位于 (wèiyú)',
    ),
    'correct_answer' => '濒临 (bīnlín)',
    'explanation' => '濒临灭绝 = cận kề bờ vực tuyệt chủng.',
    'sort_order' => 204,
  ),
  204 => 
  array (
    'hsk_level' => 6,
    'skill_type' => 'reading',
    'difficulty' => 'advanced',
    'question' => 'Thành ngữ "举足轻重" (jǔ zú qīng zhòng) miêu tả vị trí thế nào?',
    'pinyin' => 'jǔ zú qīng zhòng',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => 'Đóng vai trò quyết định, có sức ảnh hưởng to lớn đến toàn cục',
      1 => 'Vị trí nhỏ nhoi không đáng kể',
      2 => 'Bước chân nhẹ nhàng uyển chuyển',
      3 => 'Gặp khó khăn trong di chuyển',
    ),
    'correct_answer' => 'Đóng vai trò quyết định, có sức ảnh hưởng to lớn đến toàn cục',
    'explanation' => '举足轻重 = nhất cử nhất động đều làm nghiêng lệch cán cân, có vị thế then chốt.',
    'sort_order' => 205,
  ),
  205 => 
  array (
    'hsk_level' => 6,
    'skill_type' => 'reading',
    'difficulty' => 'advanced',
    'question' => 'Thành ngữ "息息相关" (xī xī xiāng guān) diễn đạt mối quan hệ gì?',
    'pinyin' => 'xī xī xiāng guān',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => 'Gắn bó mật thiết, quan hệ khăng khít như từng hơi thở',
      1 => 'Xa lạ không hề can hệ',
      2 => 'Mâu thuẫn không đội trời chung',
      3 => 'Quan hệ qua loa một thời gian',
    ),
    'correct_answer' => 'Gắn bó mật thiết, quan hệ khăng khít như từng hơi thở',
    'explanation' => '息息相关 = có liên quan mật thiết sống còn.',
    'sort_order' => 206,
  ),
  206 => 
  array (
    'hsk_level' => 6,
    'skill_type' => 'reading',
    'difficulty' => 'advanced',
    'question' => 'Chọn từ thích hợp: "我们在制定规划时，必须坚持 ____ ，统筹各方利益。"',
    'pinyin' => 'Bìxū jiānchí ____ ...',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '统筹兼顾 (tǒngchóu jiāngù)',
      1 => '拔苗助长 (bámiáo zhùzhǎng)',
      2 => '掩耳盗铃 (yǎn\'ěr dàolíng)',
      3 => '守株待兔 (shǒuzhū dàitù)',
    ),
    'correct_answer' => '统筹兼顾 (tǒngchóu jiāngù)',
    'explanation' => '统筹兼顾 = quy hoạch tổng thể, chăm lo chu toàn mọi mặt.',
    'sort_order' => 207,
  ),
  207 => 
  array (
    'hsk_level' => 6,
    'skill_type' => 'reading',
    'difficulty' => 'advanced',
    'question' => 'Thành ngữ "络绎不绝" (luò yì bù jué) dùng để miêu tả:',
    'pinyin' => 'luò yì bù jué',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => 'Người hoặc xe cộ qua lại nườm nượp, liên tục không ngớt',
      1 => 'Một nơi hoang vắng không bóng người',
      2 => 'Dòng sông bị cạn nước',
      3 => 'Sự im lặng tuyệt đối',
    ),
    'correct_answer' => 'Người hoặc xe cộ qua lại nườm nượp, liên tục không ngớt',
    'explanation' => '络绎不绝 = nườm nượp không dứt.',
    'sort_order' => 208,
  ),
  208 => 
  array (
    'hsk_level' => 6,
    'skill_type' => 'reading',
    'difficulty' => 'advanced',
    'question' => 'Từ "辩证" (biànzhèng) trong "辩证思考" có nghĩa là:',
    'pinyin' => 'biàn zhèng',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => 'Tư duy biện chứng, nhìn nhận vấn đề nhiều chiều, trong sự vận động và mối liên hệ',
      1 => 'Chỉ nhìn vào mặt tích cực',
      2 => 'Cãi nhau một cách ngoan cố',
      3 => 'Tin theo một chiều',
    ),
    'correct_answer' => 'Tư duy biện chứng, nhìn nhận vấn đề nhiều chiều, trong sự vận động và mối liên hệ',
    'explanation' => '辩证思考 = tư duy biện chứng.',
    'sort_order' => 209,
  ),
  209 => 
  array (
    'hsk_level' => 6,
    'skill_type' => 'reading',
    'difficulty' => 'advanced',
    'question' => 'Đọc hiểu phong cách nghị luận: "任何理论的生命力，都在于其与时代发展的契合度。"\\n-> Ý của câu là gì?',
    'pinyin' => 'Lǐlùn de shēngmìnglì...',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => 'Sức sống của bất kỳ lý luận nào đều nằm ở mức độ phù hợp và gắn kết với sự phát triển thời đại',
      1 => 'Lý luận đã đề ra thì vĩnh viễn không thay đổi',
      2 => 'Thời đại phát triển không cần lý luận',
      3 => 'Chỉ có lý luận cổ mới có giá trị',
    ),
    'correct_answer' => 'Sức sống của bất kỳ lý luận nào đều nằm ở mức độ phù hợp và gắn kết với sự phát triển thời đại',
    'explanation' => '契合度 = mức độ ăn khớp, hòa hợp.',
    'sort_order' => 210,
  ),
  210 => 
  array (
    'hsk_level' => 6,
    'skill_type' => 'grammar',
    'difficulty' => 'advanced',
    'question' => 'Tìm lỗi sai ngữ pháp (病句) trong các câu sau - Lỗi thiếu thành phần câu (成分残缺):',
    'pinyin' => 'Bìngjù fēnxī: Chéngfèn cánquē',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '通过这次深刻的社会实践，使同学们开阔了眼界。（Thiếu chủ ngữ do dùng sai "通过...使..."）',
      1 => '这次社会实践开阔了同学们的眼界。',
      2 => '同学们通过实践开阔了眼界。',
      3 => '实践使同学们开阔了眼界。',
    ),
    'correct_answer' => '通过这次深刻的社会实践，使同学们开阔了眼界。（Thiếu chủ ngữ do dùng sai "通过...使..."）',
    'explanation' => 'Câu này mắc lỗi kinh điển trong HSK 6: dùng cả "通过..." lẫn "使..." làm cho câu mất chủ ngữ độc lập.',
    'sort_order' => 211,
  ),
  211 => 
  array (
    'hsk_level' => 6,
    'skill_type' => 'grammar',
    'difficulty' => 'advanced',
    'question' => 'Tìm câu mắc lỗi kết hợp từ không phù hợp (搭配不当):',
    'pinyin' => 'Bìngjù fēnxī: Dāpèi bùdàng',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '随着科技的发展，人们的生活水平和文化素养得到了很大的增长。（"素养" không thể kết hợp với "增长"）',
      1 => '人们的生活水平得到了很大提高。',
      2 => '人们的文化素养得到了很大提升。',
      3 => '经济的发展带来了社会的巨大进步。',
    ),
    'correct_answer' => '随着科技的发展，人们的生活水平和文化素养得到了很大的增长。（"素养" không thể kết hợp với "增长"）',
    'explanation' => '文化素养 phải dùng 提升 hoặc 提高, không dùng 增长 (chỉ dùng cho số lượng, kinh tế).',
    'sort_order' => 212,
  ),
  212 => 
  array (
    'hsk_level' => 6,
    'skill_type' => 'grammar',
    'difficulty' => 'advanced',
    'question' => 'Tìm câu mắc lỗi lai tạp cấu trúc câu (句式杂糅):',
    'pinyin' => 'Bìngjù fēnxī: Jùshì záróu',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '本届展会之所以取得巨大成功的原因，是由全体筹备人员共同努力的结果。（Trộn lẫn "之所以...的原因" với "是由...的结果"）',
      1 => '本届展会之所以取得成功，是因为全体筹备人员共同努力。',
      2 => '本届展会的成功，是全体人员共同努力的结果。',
      3 => '全体人员的努力促成了展会的圆满成功。',
    ),
    'correct_answer' => '本届展会之所以取得巨大成功的原因，是由全体筹备人员共同努力的结果。（Trộn lẫn "之所以...的原因" với "是由...的结果"）',
    'explanation' => 'Lỗi lai tạp hai mẫu câu: 之所以...是因为... và ...是...的结果.',
    'sort_order' => 213,
  ),
  213 => 
  array (
    'hsk_level' => 6,
    'skill_type' => 'grammar',
    'difficulty' => 'advanced',
    'question' => 'Tìm câu có trật tự từ ngữ sai lệch (语序不当):',
    'pinyin' => 'Bìngjù fēnxī: Yǔxù bùdàng',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '讨论并听取了专家的学术报告。（Sai trật tự logic: phải nghe báo cáo trước rồi mới thảo luận）',
      1 => '听取并讨论了专家的学术报告。',
      2 => '专家作了精彩的学术报告。',
      3 => '大家认真听取了专家的报告。',
    ),
    'correct_answer' => '讨论并听取了专家的学术报告。（Sai trật tự logic: phải nghe báo cáo trước rồi mới thảo luận）',
    'explanation' => 'Trật tự logic tự nhiên là 听取 (nghe trước) rồi mới 讨论 (thảo luận sau).',
    'sort_order' => 214,
  ),
  214 => 
  array (
    'hsk_level' => 6,
    'skill_type' => 'grammar',
    'difficulty' => 'advanced',
    'question' => 'Điền cặp liên từ tăng tiến: "这件案子 ____ 案情错综复杂，____ 牵涉面极广。"',
    'pinyin' => 'Zhè jiàn ànzi ____ ànqíng cuòzōng fùzá, ____ qiānshèmiàn jí guǎng.',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '不仅...而且... (bùjǐn... érqiě...)',
      1 => '即使...也... (jíshǐ... yě...)',
      2 => '虽然...但是... (suīrán... dànshì...)',
      3 => '与其...不如... (yǔqí... bùrú...)',
    ),
    'correct_answer' => '不仅...而且... (bùjǐn... érqiě...)',
    'explanation' => '不仅...而且... biểu thị quan hệ tăng tiến hai khía cạnh cùng chiều.',
    'sort_order' => 215,
  ),
  215 => 
  array (
    'hsk_level' => 6,
    'skill_type' => 'grammar',
    'difficulty' => 'advanced',
    'question' => 'Cấu trúc "尚且...何况..." (shàngqiě... hékuàng...) biểu thị ý gì?',
    'pinyin' => 'shàngqiě... hékuàng...',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => 'Lấy trường hợp khó/nghiêm trọng hơn làm tiền đề để nhấn mạnh trường hợp bình thường (Ngay cả... huống chi...)',
      1 => 'Biểu thị sự nhượng bộ giả thiết',
      2 => 'Biểu thị sự đối lập tuyệt đối',
      3 => 'Biểu thị nguyên nhân khách quan',
    ),
    'correct_answer' => 'Lấy trường hợp khó/nghiêm trọng hơn làm tiền đề để nhấn mạnh trường hợp bình thường (Ngay cả... huống chi...)',
    'explanation' => '尚且...何况...: Đến người giỏi còn không làm được, huống chi là người mới học.',
    'sort_order' => 216,
  ),
  216 => 
  array (
    'hsk_level' => 6,
    'skill_type' => 'grammar',
    'difficulty' => 'advanced',
    'question' => 'Sắp xếp câu nghị luận: "1. 深入挖掘 / 2. 传统文化的精髓 / 3. 我们必须 / 4. 推动其创造性转化 / 5. 进而"',
    'pinyin' => 'Wǒmen bìxū shēnrù wājué chuántǒng wénhuà de jīngsuǐ, jìn\'ér tuīdòng qí chuàngzàoxìng zhuǎnhuà.',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '3 - 1 - 2 - 5 - 4 (我们必须深入挖掘传统文化的精髓，进而推动其创造性转化。)',
      1 => '1 - 2 - 3 - 5 - 4 (深入挖掘传统文化的精髓我们必须进而推动其创造性转化。)',
      2 => '3 - 5 - 4 - 1 - 2 (我们必须进而推动其创造性转化深入挖掘传统文化的精髓。)',
      3 => '5 - 4 - 3 - 1 - 2 (进而推动其创造性转化我们必须深入挖掘传统文化的精髓。)',
    ),
    'correct_answer' => '3 - 1 - 2 - 5 - 4 (我们必须深入挖掘传统文化的精髓，进而推动其创造性转化。)',
    'explanation' => 'Chủ ngữ (我们必须) + Hành động 1 (深入挖掘传统文化的精髓) + Từ nối tăng tiến (进而) + Hành động 2 (推动其创造性转化).',
    'sort_order' => 217,
  ),
  217 => 
  array (
    'hsk_level' => 6,
    'skill_type' => 'grammar',
    'difficulty' => 'advanced',
    'question' => 'Từ nối "旨在" (zhǐzài) thường dùng trong văn bản với ý nghĩa gì?',
    'pinyin' => 'zhǐ zài',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => 'Nhằm mục đích / Với mục tiêu là',
      1 => 'Nằm ở vị trí',
      2 => 'Tuy nhiên',
      3 => 'Mặc dù vậy',
    ),
    'correct_answer' => 'Nhằm mục đích / Với mục tiêu là',
    'explanation' => '旨在 = 目的在于 (nhằm mục đích là).',
    'sort_order' => 218,
  ),
  218 => 
  array (
    'hsk_level' => 6,
    'skill_type' => 'grammar',
    'difficulty' => 'advanced',
    'question' => 'Điền từ thích hợp: "两国的友好交往历史悠久，这已是不争的 ____ 。"',
    'pinyin' => 'Zhè yǐ shì bùzhēng de ____ .',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '事实 (shìshí)',
      1 => '原因 (yuányīn)',
      2 => '看法 (kànfǎ)',
      3 => '观点 (guāndiǎn)',
    ),
    'correct_answer' => '事实 (shìshí)',
    'explanation' => '不争的事实 = sự thật hiển nhiên không thể bàn cãi.',
    'sort_order' => 219,
  ),
  219 => 
  array (
    'hsk_level' => 6,
    'skill_type' => 'grammar',
    'difficulty' => 'advanced',
    'question' => 'Sắp xếp câu: "1. 依托强大的 / 2. 建立了 / 3. 该机构 / 4. 科技支撑体系 / 5. 完善的信息网络"',
    'pinyin' => 'Gāi jīgòu yītuō qiángdà de...',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '3 - 1 - 4 - 2 - 5 (该机构依托强大的科技支撑体系，建立了完善的信息网络。)',
      1 => '1 - 4 - 3 - 2 - 5 (依托强大的科技支撑体系该机构建立了完善的信息网络。)',
      2 => '3 - 2 - 5 - 1 - 4 (该机构建立了完善的信息网络依托强大的科技支撑体系。)',
      3 => '5 - 3 - 1 - 4 - 2 (完善的信息网络该机构依托强大的科技支撑体系建立了。)',
    ),
    'correct_answer' => '3 - 1 - 4 - 2 - 5 (该机构依托强大的科技支撑体系，建立了完善的信息网络。)',
    'explanation' => 'Chủ ngữ (该机构) + Giới từ phương thức (依托强大的科技支撑体系) + Động từ và tân ngữ (建立了完善的信息网络).',
    'sort_order' => 220,
  ),
  220 => 
  array (
    'hsk_level' => 6,
    'skill_type' => 'grammar',
    'difficulty' => 'advanced',
    'question' => 'Tìm lỗi sai logic (逻辑混乱) trong các phương án sau:',
    'pinyin' => 'Bìngjù fēnxī: Luóji hùnluàn',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '出席今天大会的有各界知名学者、专家以及老年人。（"老年人" và "学者、专家" bị giao nhau về khái niệm, phân loại logic sai）',
      1 => '出席今天大会的有各界知名学者和专家。',
      2 => '出席大会的有来自各地的代表。',
      3 => '大会吸引了众多热心人士参加。',
    ),
    'correct_answer' => '出席今天大会的有各界知名学者、专家以及老年人。（"老年人" và "学者、专家" bị giao nhau về khái niệm, phân loại logic sai）',
    'explanation' => '学者, 专家 là phân loại theo nghề nghiệp/học hàm, 老年人 là theo độ tuổi. Liệt kê song song vi phạm logic phân loại.',
    'sort_order' => 221,
  ),
  221 => 
  array (
    'hsk_level' => 6,
    'skill_type' => 'grammar',
    'difficulty' => 'advanced',
    'question' => 'Cụm từ "总而言之" (zǒng\'ér yánzhī) đặt ở đâu trong một đoạn văn bản?',
    'pinyin' => 'zǒng\'ér yánzhī',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => 'Đầu câu cuối để tóm tắt lại toàn bộ luận điểm (Nói tóm lại / Tóm lại)',
      1 => 'Mở đầu đoạn văn giới thiệu đề tài',
      2 => 'Đứng giữa câu làm định ngữ',
      3 => 'Dùng làm câu hỏi tu từ',
    ),
    'correct_answer' => 'Đầu câu cuối để tóm tắt lại toàn bộ luận điểm (Nói tóm lại / Tóm lại)',
    'explanation' => '总而言之 = tóm lại, nói tóm lại (tổng kết kết luận).',
    'sort_order' => 222,
  ),
  222 => 
  array (
    'hsk_level' => 6,
    'skill_type' => 'grammar',
    'difficulty' => 'advanced',
    'question' => 'Sắp xếp câu: "1. 提供了 / 2. 宝贵的借鉴 / 3. 这项研究成果 / 4. 为后续的理论探索"',
    'pinyin' => 'Zhè xiàng yánjiū chéngguǒ wèi hòuxù de lǐlùn tànsuǒ tígōng le bǎoguì de jièjiàn.',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '3 - 4 - 1 - 2 (这项研究成果为后续的理论探索提供了宝贵的借鉴。)',
      1 => '4 - 1 - 2 - 3 (为后续的理论探索提供了宝贵的借鉴这项研究成果。)',
      2 => '3 - 1 - 2 - 4 (这项研究成果提供了宝贵的借鉴为后续的理论探索。)',
      3 => '1 - 2 - 3 - 4 (提供了宝贵的借鉴这项研究成果为后续的理论探索。)',
    ),
    'correct_answer' => '3 - 4 - 1 - 2 (这项研究成果为后续的理论探索提供了宝贵的借鉴。)',
    'explanation' => 'Chủ ngữ (这项研究成果) + Giới từ đối tượng (为后续的理论探索) + Động từ (提供了) + Tân ngữ (宝贵的借鉴).',
    'sort_order' => 223,
  ),
  223 => 
  array (
    'hsk_level' => 6,
    'skill_type' => 'grammar',
    'difficulty' => 'advanced',
    'question' => 'Chọn từ thích hợp: "制度建设具有根本性、全局性、稳定性和 ____ 。"',
    'pinyin' => 'Zhìdù jiànshè jùyǒu gēnběnxìng, quánjúxìng...',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '长期性 (chángqīxìng)',
      1 => '短视性 (duǎnshìxìng)',
      2 => '片面性 (piànmiànxìng)',
      3 => '随意性 (suíyìxìng)',
    ),
    'correct_answer' => '长期性 (chángqīxìng)',
    'explanation' => 'Cụm thành ngữ chính luận: 根本性、全局性、稳定性和长期性.',
    'sort_order' => 224,
  ),
  224 => 
  array (
    'hsk_level' => 6,
    'skill_type' => 'grammar',
    'difficulty' => 'advanced',
    'question' => 'Sắp xếp câu: "1. 奠定了 / 2. 坚实的基础 / 3. 为两国的长远合作 / 4. 双方签署的协议"',
    'pinyin' => 'Shuāngfāng qiānshǔ de xiéyì wèi liǎng guó de chángyuǎn hézuò diàndìng le jiānshí de jīchǔ.',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '4 - 3 - 1 - 2 (双方签署的协议为两国的长远合作奠定了坚实的基础。)',
      1 => '3 - 1 - 2 - 4 (为两国的长远合作奠定了坚实的基础双方签署的协议。)',
      2 => '4 - 1 - 2 - 3 (双方签署的协议奠定了坚实的基础为两国的长远合作。)',
      3 => '1 - 2 - 4 - 3 (奠定了坚实的基础双方签署的协议为两国的长远合作。)',
    ),
    'correct_answer' => '4 - 3 - 1 - 2 (双方签署的协议为两国的长远合作奠定了坚实的基础。)',
    'explanation' => 'Chủ ngữ (双方签署的协议) + Giới từ (为两国的长远合作) + Động từ và tân ngữ (奠定了坚实的基础).',
    'sort_order' => 225,
  ),
  225 => 
  array (
    'hsk_level' => 6,
    'skill_type' => 'grammar',
    'difficulty' => 'advanced',
    'question' => 'Điền từ nối văn phong học thuật: "这表明两者之间存在着内在的必然联系，____ 并非偶然巧合。"',
    'pinyin' => 'Zhè biǎomíng liǎng zhě zhījiān cúnzài zhe...',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '而 (ér)',
      1 => '和 (hé)',
      2 => '或 (huò)',
      3 => '与 (yǔ)',
    ),
    'correct_answer' => '而 (ér)',
    'explanation' => '而 dùng nối hai phán đoán mang tính bổ sung - đối chiếu: "mà không phải là ngẫu nhiên trùng hợp".',
    'sort_order' => 226,
  ),
  226 => 
  array (
    'hsk_level' => 6,
    'skill_type' => 'grammar',
    'difficulty' => 'advanced',
    'question' => 'Tìm câu chuẩn xác nhất không mắc lỗi ngữ pháp:',
    'pinyin' => 'Xuǎnzé wú bìngjù de jùzi',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '在全体科研人员的不懈努力下，这项核心技术攻关取得了重大突破。',
      1 => '在全体科研人员的不懈努力下，使这项核心技术取得了突破。（Thiếu chủ ngữ）',
      2 => '这项技术攻关被全体人员彻底取得了突破。（Bị động lộn xộn）',
      3 => '全体科研人员努力这项技术突破。（Thiếu vị ngữ liên kết）',
    ),
    'correct_answer' => '在全体科研人员的不懈努力下，这项核心技术攻关取得了重大突破。',
    'explanation' => 'Câu này có trạng ngữ, chủ ngữ và vị ngữ hoàn chỉnh, logic chặt chẽ.',
    'sort_order' => 227,
  ),
  227 => 
  array (
    'hsk_level' => 6,
    'skill_type' => 'grammar',
    'difficulty' => 'advanced',
    'question' => 'Sắp xếp câu: "1. 赋予了 / 2. 新的时代内涵 / 3. 这项倡议 / 4. 传统丝绸之路精神"',
    'pinyin' => 'Zhè xiàng chàngyì fùyǔ le chuántǒng sīchóuzhīlù jīngshén xīn de shídài nèihán.',
    'audio_text' => NULL,
    'options' => 
    array (
      0 => '3 - 1 - 4 - 2 (这项倡议赋予了传统丝绸之路精神新的时代内涵。)',
      1 => '4 - 1 - 3 - 2 (传统丝绸之路精神赋予了这项倡议新的时代内涵。)',
      2 => '3 - 2 - 1 - 4 (这项倡议新的时代内涵赋予了传统丝绸之路精神。)',
      3 => '1 - 2 - 3 - 4 (赋予了新的时代内涵这项倡议传统丝绸之路精神。)',
    ),
    'correct_answer' => '3 - 1 - 4 - 2 (这项倡议赋予了传统丝绸之路精神新的时代内涵。)',
    'explanation' => 'Chủ ngữ (这项倡议) + Động từ (赋予了) + Tân ngữ 1 (传统丝绸之路精神) + Tân ngữ 2 (新的时代内涵).',
    'sort_order' => 228,
  ),
);

        foreach ($questions as $q) {
            Question::updateOrCreate(
                [
                    'question' => $q['question'],
                    'pinyin'   => $q['pinyin'],
                ],
                [
                    'hsk_level'      => $q['hsk_level'],
                    'exam_standard'  => $q['exam_standard'] ?? 'hsk_2_0',
                    'skill_type'     => $q['skill_type'],
                    'question_type'  => $q['question_type'] ?? 'multiple_choice',
                    'media_type'     => $q['media_type'] ?? 'text',
                    'image'          => $q['image'] ?? null,
                    'image_alt'      => $q['image_alt'] ?? null,
                    'image_set'      => $q['image_set'] ?? null,
                    'difficulty'     => $q['difficulty'],
                    'audio_text'     => $q['audio_text'],
                    'options'        => $q['options'],
                    'correct_answer' => $q['correct_answer'],
                    'explanation'    => $q['explanation'],
                    'sort_order'     => $q['sort_order'],
                    'is_active'      => true,
                ]
            );
        }
    }
}
