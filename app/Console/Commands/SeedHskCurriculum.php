<?php

namespace App\Console\Commands;

use App\Models\Flashcard;
use App\Models\Lesson;
use App\Models\Question;
use Database\Seeders\HskCurriculumSeeder;
use Illuminate\Console\Command;

class SeedHskCurriculum extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:seed-hsk-curriculum {--level= : Cấp độ HSK cụ thể cần nạp (1, 2, hoặc 3)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Khởi tạo và nạp dữ liệu chuẩn Giáo trình HSK (HSK Standard Course) gồm bài khóa, ngữ pháp, flashcard và quiz.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $levelOpt = $this->option('level');
        $specificLevel = null;

        if ($levelOpt !== null) {
            $specificLevel = (int) $levelOpt;
            if (!in_array($specificLevel, [1, 2, 3], true)) {
                $this->error("Cấp độ không hợp lệ. Vui lòng chọn --level=1, 2 hoặc 3.");
                return self::FAILURE;
            }
        }

        $this->info("==========================================================");
        $this->info("  BẮT ĐẦU NẠP DỮ LIỆU CHUẨN GIÁO TRÌNH HSK (BLCU / HANBAN) ");
        $this->info("==========================================================");

        $seeder = new HskCurriculumSeeder();
        $seeder->setCommand($this);
        $seeder->run($specificLevel);

        $this->newLine();
        $this->info("==========================================================");
        $this->info("                  THỐNG KÊ DỮ LIỆU ĐÃ NẠP                 ");
        $this->info("==========================================================");

        $levelsToCheck = $specificLevel ? [$specificLevel] : [1, 2, 3];
        $tableRows = [];

        foreach ($levelsToCheck as $lvl) {
            $lessonCount = Lesson::where('hsk_level', $lvl)->where('is_published', true)->count();
            $flashcardCount = Flashcard::where('hsk_level', $lvl)->whereNotNull('lesson_id')->where('is_active', true)->count();
            $questionCount = Question::where('hsk_level', $lvl)->whereNotNull('lesson_id')->where('is_active', true)->count();

            $tableRows[] = [
                "HSK {$lvl}",
                "{$lessonCount} bài học",
                "{$flashcardCount} thẻ vựng",
                "{$questionCount} câu hỏi Quiz",
            ];
        }

        $this->table(['Cấp độ', 'Số bài học', 'Từ vựng gắn bài', 'Ngân hàng trắc nghiệm'], $tableRows);

        $this->newLine();
        $this->info("✔ Dữ liệu Giáo trình HSK đã được đồng bộ chuẩn xác vào hệ thống!");

        return self::SUCCESS;
    }
}
