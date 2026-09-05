<?php

namespace Database\Seeders;

use App\Models\Flashcard;
use App\Models\Lesson;
use App\Models\Question;
use Illuminate\Database\Seeder;

class HskCurriculumSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(?int $specificLevel = null): void
    {
        // Clean up obsolete rough/demo phonetic flashcards
        Flashcard::whereIn('hanzi', ['ā', 'á', 'ǎ', 'à'])->delete();

        $levels = $specificLevel ? [$specificLevel] : [1, 2, 3, 4];

        foreach ($levels as $level) {
            $dataFile = __DIR__ . "/data/hsk{$level}_lessons.php";
            if (!file_exists($dataFile)) {
                if ($this->command) {
                    $this->command->warn("Không tìm thấy dữ liệu cho HSK {$level} tại {$dataFile}");
                }
                continue;
            }

            $lessonsData = require $dataFile;
            if ($this->command) {
                $this->command->info("Đang nạp " . count($lessonsData) . " bài học chuẩn cho HSK {$level}...");
            }

            foreach ($lessonsData as $item) {
                $lesson = Lesson::query()->updateOrCreate(
                    ['slug' => $item['slug']],
                    [
                        'title' => $item['title'],
                        'summary' => $item['summary'],
                        'content' => $item['content'] ?? null,
                        'hsk_level' => $item['hsk_level'],
                        'sort_order' => $item['sort_order'],
                        'estimated_minutes' => $item['estimated_minutes'] ?? 30,
                        'accent_color' => $item['accent_color'] ?? '#16a34a',
                        'difficulty' => $item['difficulty'] ?? 'starter',
                        'is_published' => true,
                    ]
                );

                // Seed Flashcards for this lesson
                if (!empty($item['flashcards'])) {
                    foreach ($item['flashcards'] as $cardOrder => $card) {
                        Flashcard::query()->updateOrCreate(
                            [
                                'lesson_id' => $lesson->id,
                                'hanzi'     => $card['hanzi'],
                            ],
                            [
                                'pinyin'          => $card['pinyin'],
                                'meaning'         => $card['meaning'],
                                'example'         => $card['example'] ?? null,
                                'example_pinyin'  => $card['example_pinyin'] ?? null,
                                'example_meaning' => $card['example_meaning'] ?? null,
                                'hsk_level'       => $item['hsk_level'],
                                'sort_order'      => $cardOrder + 1,
                                'is_active'       => true,
                            ]
                        );
                    }
                }

                // Seed Questions for this lesson
                if (!empty($item['questions'])) {
                    foreach ($item['questions'] as $qOrder => $q) {
                        Question::query()->updateOrCreate(
                            [
                                'lesson_id' => $lesson->id,
                                'question'  => $q['question'],
                            ],
                            [
                                'pinyin'         => $q['pinyin'] ?? null,
                                'audio_text'     => $q['audio_text'] ?? null,
                                'options'        => $q['options'],
                                'correct_answer' => $q['correct_answer'],
                                'explanation'    => $q['explanation'] ?? null,
                                'difficulty'     => $q['difficulty'] ?? 'starter',
                                'skill_type'     => $q['skill_type'] ?? 'grammar',
                                'sort_order'     => $qOrder + 1,
                                'hsk_level'      => $item['hsk_level'],
                                'is_active'      => true,
                            ]
                        );
                    }
                }
            }

            if ($this->command) {
                $this->command->info("Đã hoàn tất nạp bài học HSK {$level} thành công!");
            }
        }
    }
}
