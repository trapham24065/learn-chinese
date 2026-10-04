<?php

namespace App\Console\Commands;

use App\Models\Question;
use Illuminate\Console\Command;

class ShuffleQuizOptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'quiz:shuffle-options {--level= : Chỉ xáo trộn cấp độ HSK cụ thể (1-6)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Xáo trộn ngẫu nhiên thứ tự các đáp án trắc nghiệm trong database để loại bỏ hiện tượng đáp án đúng luôn nằm ở vị trí A';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info("==========================================================");
        $this->info("   XÁO TRỘN NGẪU NHIÊN VỊ TRÍ ĐÁP ÁN TRẮC NGHIỆM (A-B-C-D)   ");
        $this->info("==========================================================");

        $query = Question::query();
        if ($level = $this->option('level')) {
            $query->where('hsk_level', (int) $level);
        }

        $questions = $query->get();
        if ($questions->isEmpty()) {
            $this->warn("Không tìm thấy câu hỏi nào để xử lý.");
            return Command::SUCCESS;
        }

        $shuffledCount = 0;
        $skippedCount = 0;

        foreach ($questions as $q) {
            $options = is_array($q->options) ? $q->options : json_decode($q->options ?? '[]', true);
            if (!is_array($options) || count($options) <= 1) {
                $skippedCount++;
                continue;
            }

            // Giữ nguyên câu Đúng / Sai định dạng 2 lựa chọn (True / False)
            $isTrueFalse = count($options) === 2 && (
                (in_array('对', $options) && in_array('错', $options)) ||
                (in_array('Đúng', $options) && in_array('Sai', $options))
            );

            if ($isTrueFalse) {
                $skippedCount++;
                continue;
            }

            // Xáo trộn ngẫu nhiên
            shuffle($options);

            // Kiểm tra tính toàn vẹn: đáp án đúng vẫn nằm trong mảng options
            $correct = trim((string) $q->correct_answer);
            $hasCorrect = false;
            foreach ($options as $opt) {
                if (trim((string) $opt) === $correct) {
                    $hasCorrect = true;
                    break;
                }
            }

            if ($hasCorrect) {
                $q->options = $options;
                $q->save();
                $shuffledCount++;
            } else {
                $this->warn("Bỏ qua câu #{$q->id} vì không tìm thấy correct_answer trong options.");
                $skippedCount++;
            }
        }

        $this->info("✓ Đã xáo trộn ngẫu nhiên thành công: {$shuffledCount} câu hỏi.");
        if ($skippedCount > 0) {
            $this->comment("i Đã giữ nguyên (câu Đúng/Sai hoặc 1 lựa chọn): {$skippedCount} câu.");
        }

        return Command::SUCCESS;
    }
}
