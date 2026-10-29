<?php

namespace Database\Seeders;

use App\Models\Question;
use Illuminate\Database\Seeder;

class HskMockExamQuestionSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Clean existing mock exam questions (where lesson_id is null)
        Question::whereNull('lesson_id')->delete();

        // 2. Load modular question banks for HSK 1 to HSK 6
        $levels = [1, 2, 3, 4, 5, 6];
        $totalSeeded = 0;

        foreach ($levels as $level) {
            $filePath = __DIR__ . "/data/mock/hsk{$level}_mock_questions.php";
            if (! file_exists($filePath)) {
                $this->command?->warn("Warning: Mock questions file not found: {$filePath}");
                continue;
            }

            $questions = include $filePath;
            if (! is_array($questions)) {
                $this->command?->warn("Warning: Invalid question format in: {$filePath}");
                continue;
            }

            foreach ($questions as $q) {
                Question::create([
                    'lesson_id'      => null,
                    'hsk_level'      => $q['hsk_level'] ?? $level,
                    'exam_standard'  => $q['exam_standard'] ?? 'hsk_2_0',
                    'skill_type'     => $q['skill_type'],
                    'question_type'  => $q['question_type'] ?? 'multiple_choice',
                    'media_type'     => $q['media_type'] ?? 'text',
                    'image'          => $q['image'] ?? null,
                    'image_alt'      => $q['image_alt'] ?? null,
                    'image_set'      => $q['image_set'] ?? null,
                    'difficulty'     => $q['difficulty'] ?? 'intermediate',
                    'question'       => $q['question'],
                    'pinyin'         => $q['pinyin'] ?? null,
                    'audio_text'     => $q['audio_text'] ?? null,
                    'options'        => $q['options'],
                    'correct_answer' => $q['correct_answer'],
                    'explanation'    => $q['explanation'] ?? null,
                    'sort_order'     => $q['sort_order'] ?? 0,
                    'is_active'      => true,
                ]);
                $totalSeeded++;
            }
        }

        $this->command?->info("Successfully seeded {$totalSeeded} authentic HSK mock questions across levels 1-6.");
    }
}
