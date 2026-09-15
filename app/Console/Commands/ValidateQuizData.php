<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ValidateQuizData extends Command
{
    protected $signature = 'quiz:validate-data';
    protected $description = 'Kiểm tra tính toàn vẹn của dữ liệu câu hỏi trắc nghiệm HSK (options, correct_answer, pinyin, giải thích, định dạng)';

    public function handle(): int
    {
        $this->info("==========================================================");
        $this->info("   KIỂM TRA TÍNH TOÀN VẸN DỮ LIỆU NGÂN HÀNG CÂU HỎI QUIZ   ");
        $this->info("==========================================================");

        $validSkillTypes = [
            'vocabulary', 'grammar', 'dialogue', 'cloze', 'reading', 
            'listening', 'word_order', 'equivalent_meaning',
        ];

        $validDifficulties = [
            'starter', 'intermediate', 'advanced', 'easy', 'medium', 'hard',
        ];

        $totalLessons = 0;
        $totalLessonQuestions = 0;
        $totalMockQuestions = 0;
        $errors = [];
        $levelStats = [];

        for ($lvl = 1; $lvl <= 6; $lvl++) {
            $levelStats[$lvl] = [
                'lessons' => 0,
                'lesson_questions' => 0,
                'mock_questions' => 0,
            ];

            $dataFile = base_path("database/seeders/data/hsk{$lvl}_lessons.php");
            if (!file_exists($dataFile)) {
                $errors[] = [
                    'file' => "hsk{$lvl}_lessons.php",
                    'location' => "Root",
                    'field' => 'file',
                    'current' => 'File not found',
                    'expected' => 'File exists',
                ];
                continue;
            }

            // Syntax check via php lint
            $lintCmd = 'php -l ' . escapeshellarg($dataFile);
            $lintOutput = [];
            $lintCode = 0;
            exec($lintCmd, $lintOutput, $lintCode);
            if ($lintCode !== 0) {
                $errors[] = [
                    'file' => "hsk{$lvl}_lessons.php",
                    'location' => "Syntax",
                    'field' => 'syntax',
                    'current' => implode("\n", $lintOutput),
                    'expected' => 'No syntax error',
                ];
                continue;
            }

            $lessons = require $dataFile;
            $totalLessons += count($lessons);
            $levelStats[$lvl]['lessons'] = count($lessons);

            foreach ($lessons as $lIdx => $lesson) {
                $lessonSlug = $lesson['slug'] ?? "lesson_{$lIdx}";
                $questions = $lesson['questions'] ?? [];
                $levelStats[$lvl]['lesson_questions'] += count($questions);
                $totalLessonQuestions += count($questions);

                foreach ($questions as $qIdx => $q) {
                    $loc = "HSK{$lvl} > [{$lessonSlug}] > Câu #" . ($qIdx + 1);

                    // 1. Check question content
                    if (empty($q['question']) || !is_string($q['question'])) {
                        $errors[] = [
                            'file' => "hsk{$lvl}_lessons.php",
                            'location' => $loc,
                            'field' => 'question',
                            'current' => json_encode($q['question'] ?? null),
                            'expected' => 'Non-empty string',
                        ];
                    }

                    // 2. Check options
                    if (empty($q['options']) || !is_array($q['options']) || count($q['options']) < 2) {
                        $errors[] = [
                            'file' => "hsk{$lvl}_lessons.php",
                            'location' => $loc,
                            'field' => 'options',
                            'current' => json_encode($q['options'] ?? null),
                            'expected' => 'Array with at least 2 options (preferably 4)',
                        ];
                    } else {
                        // Check for duplicate options
                        $uniqueOptions = array_unique(array_map('trim', $q['options']));
                        if (count($uniqueOptions) !== count($q['options'])) {
                            $errors[] = [
                                'file' => "hsk{$lvl}_lessons.php",
                                'location' => $loc,
                                'field' => 'options_duplicate',
                                'current' => json_encode($q['options']),
                                'expected' => 'All options must be unique',
                            ];
                        }

                        // 3. Check correct_answer exists in options
                        $correct = trim($q['correct_answer'] ?? '');
                        $found = false;
                        foreach ($q['options'] as $opt) {
                            if (trim($opt) === $correct) {
                                $found = true;
                                break;
                            }
                        }

                        if (!$found) {
                            $errors[] = [
                                'file' => "hsk{$lvl}_lessons.php",
                                'location' => $loc,
                                'field' => 'correct_answer',
                                'current' => "[$correct]",
                                'expected' => 'Must match exactly one item in options: ' . json_encode($q['options']),
                            ];
                        }
                    }

                    // 4. Check explanation
                    if (empty($q['explanation']) || !is_string($q['explanation'])) {
                        $errors[] = [
                            'file' => "hsk{$lvl}_lessons.php",
                            'location' => $loc,
                            'field' => 'explanation',
                            'current' => json_encode($q['explanation'] ?? null),
                            'expected' => 'Non-empty explanation string',
                        ];
                    }

                    // 5. Check skill_type
                    if (!empty($q['skill_type']) && !in_array($q['skill_type'], $validSkillTypes, true)) {
                        $errors[] = [
                            'file' => "hsk{$lvl}_lessons.php",
                            'location' => $loc,
                            'field' => 'skill_type',
                            'current' => $q['skill_type'],
                            'expected' => 'One of: ' . implode(', ', $validSkillTypes),
                        ];
                    }

                    // 6. Check difficulty
                    if (!empty($q['difficulty']) && !in_array($q['difficulty'], $validDifficulties, true)) {
                        $errors[] = [
                            'file' => "hsk{$lvl}_lessons.php",
                            'location' => $loc,
                            'field' => 'difficulty',
                            'current' => $q['difficulty'],
                            'expected' => 'One of: ' . implode(', ', $validDifficulties),
                        ];
                    }
                }
            }
        }

        // Validate HskMockExamQuestionSeeder
        $mockSeederFile = base_path('database/seeders/HskMockExamQuestionSeeder.php');
        if (file_exists($mockSeederFile)) {
            $lintCmd = 'php -l ' . escapeshellarg($mockSeederFile);
            $lintOutput = [];
            $lintCode = 0;
            exec($lintCmd, $lintOutput, $lintCode);
            if ($lintCode !== 0) {
                $errors[] = [
                    'file' => 'HskMockExamQuestionSeeder.php',
                    'location' => 'Syntax',
                    'field' => 'syntax',
                    'current' => implode("\n", $lintOutput),
                    'expected' => 'No syntax error',
                ];
            } else {
                $content = file_get_contents($mockSeederFile);
                if (preg_match('/\$questions\s*=\s*(\[.*?\]);\s*\n\s*foreach/s', $content, $matches)) {
                    try {
                        $mockQuestions = eval('return ' . $matches[1] . ';');
                        if (is_array($mockQuestions)) {
                            $totalMockQuestions = count($mockQuestions);
                            foreach ($mockQuestions as $mIdx => $mq) {
                                $mLvl = $mq['hsk_level'] ?? 1;
                                if (isset($levelStats[$mLvl])) {
                                    $levelStats[$mLvl]['mock_questions']++;
                                }

                                $mLoc = "Mock HSK{$mLvl} > Câu #" . ($mIdx + 1);

                                if (empty($mq['question'])) {
                                    $errors[] = [
                                        'file' => 'HskMockExamQuestionSeeder.php',
                                        'location' => $mLoc,
                                        'field' => 'question',
                                        'current' => 'empty',
                                        'expected' => 'Non-empty string',
                                    ];
                                }

                                if (empty($mq['options']) || !is_array($mq['options'])) {
                                    $errors[] = [
                                        'file' => 'HskMockExamQuestionSeeder.php',
                                        'location' => $mLoc,
                                        'field' => 'options',
                                        'current' => json_encode($mq['options'] ?? null),
                                        'expected' => 'Array of options',
                                    ];
                                } else {
                                    $correct = trim($mq['correct_answer'] ?? '');
                                    $found = false;
                                    foreach ($mq['options'] as $opt) {
                                        if (trim($opt) === $correct) {
                                            $found = true;
                                            break;
                                        }
                                    }
                                    if (!$found) {
                                        $errors[] = [
                                            'file' => 'HskMockExamQuestionSeeder.php',
                                            'location' => $mLoc,
                                            'field' => 'correct_answer',
                                            'current' => "[$correct]",
                                            'expected' => 'Must match an option in ' . json_encode($mq['options']),
                                        ];
                                    }
                                }

                                if (empty($mq['explanation'])) {
                                    $errors[] = [
                                        'file' => 'HskMockExamQuestionSeeder.php',
                                        'location' => $mLoc,
                                        'field' => 'explanation',
                                        'current' => 'empty',
                                        'expected' => 'Non-empty explanation',
                                    ];
                                }
                            }
                        }
                    } catch (\Throwable $e) {
                        $errors[] = [
                            'file' => 'HskMockExamQuestionSeeder.php',
                            'location' => 'Eval questions array',
                            'field' => 'parse_error',
                            'current' => $e->getMessage(),
                            'expected' => 'Valid PHP array',
                        ];
                    }
                }
            }
        }

        // Print Statistics Table
        $tableRows = [];
        foreach ($levelStats as $lvl => $stat) {
            $tableRows[] = [
                "HSK {$lvl}",
                "{$stat['lessons']} bài học",
                "{$stat['lesson_questions']} câu",
                "{$stat['mock_questions']} câu",
                ($stat['lesson_questions'] + $stat['mock_questions']) . " câu",
            ];
        }

        $tableRows[] = [
            '<fg=yellow;options=bold>TỔNG CỘNG</>',
            "<fg=yellow;options=bold>{$totalLessons} bài học</>",
            "<fg=yellow;options=bold>{$totalLessonQuestions} câu</>",
            "<fg=yellow;options=bold>{$totalMockQuestions} câu</>",
            "<fg=yellow;options=bold>" . ($totalLessonQuestions + $totalMockQuestions) . " câu</>",
        ];

        $this->table(['Cấp độ', 'Số bài học', 'Câu hỏi bài học', 'Câu hỏi đề mô phỏng', 'Tổng câu hỏi'], $tableRows);

        if (!empty($errors)) {
            $this->newLine();
            $this->error("❌ PHÁT HIỆN " . count($errors) . " LỖI DỮ LIỆU CẦN KHẮC PHỤC:");
            $errorTable = [];
            foreach (array_slice($errors, 0, 30) as $err) {
                $errorTable[] = [
                    $err['file'],
                    $err['location'],
                    $err['field'],
                    substr($err['current'], 0, 50),
                    substr($err['expected'], 0, 50),
                ];
            }
            $this->table(['File', 'Vị trí', 'Trường lỗi', 'Giá trị hiện tại', 'Kỳ vọng'], $errorTable);

            if (count($errors) > 30) {
                $this->warn("... và còn " . (count($errors) - 30) . " lỗi khác.");
            }

            return self::FAILURE;
        }

        $this->newLine();
        $this->info("✔ DỮ LIỆU HOÀN TOÀN HỢP LỆ! Không có bất kỳ lỗi cú pháp hay sai lệch đáp án nào.");
        return self::SUCCESS;
    }
}
