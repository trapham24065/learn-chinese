<?php

namespace App\Console\Commands;

use App\Models\Flashcard;
use App\Models\HskStandard;
use App\Models\Vocabulary;
use App\Models\VocabularyHskLevel;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ValidateHskData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hsk:validate {--shifts : Hiển thị chi tiết danh sách chuyển dịch cấp độ}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kiểm tra và báo cáo tính toàn vẹn dữ liệu Đa chuẩn HSK (HSK 2.0 & HSK 3.0)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('===========================================================');
        $this->info('       KIỂM TRA DỮ LIỆU ĐA CHUẨN HSK 2.0 & HSK 3.0        ');
        $this->info('===========================================================');

        $standards = HskStandard::all();
        if ($standards->isEmpty()) {
            $this->error('Chưa có tiêu chuẩn HSK nào trong database. Hãy chạy db:seed trước!');
            return self::FAILURE;
        }

        // 1. Tiêu chuẩn hiện có
        $this->newLine();
        $this->comment('1. Danh sách Tiêu chuẩn HSK:');
        $stdRows = [];
        foreach ($standards as $s) {
            $stdRows[] = [
                $s->id,
                $s->code,
                $s->name,
                $s->version,
                $s->is_active ? '✅ Hoạt động' : '❌ Vô hiệu',
                $s->vocabularyLevels()->count() . ' từ',
            ];
        }
        $this->table(['ID', 'Mã', 'Tên', 'Phiên bản', 'Trạng thái', 'Số từ mapped'], $stdRows);

        // 2. Thống kê cấp độ theo từng chuẩn
        $this->newLine();
        $this->comment('2. Phân bố số lượng từ vựng theo từng cấp độ:');
        $hsk2 = $standards->firstWhere('code', HskStandard::CODE_HSK_2_0);
        $hsk3 = $standards->firstWhere('code', HskStandard::CODE_HSK_3_0_2026) 
            ?? $standards->firstWhere('code', HskStandard::CODE_HSK_3_0_2021);

        $hsk2Counts = $hsk2 ? VocabularyHskLevel::where('hsk_standard_id', $hsk2->id)
            ->select('level', DB::raw('count(*) as total'))
            ->groupBy('level')
            ->pluck('total', 'level') : collect();

        $hsk3Counts = $hsk3 ? VocabularyHskLevel::where('hsk_standard_id', $hsk3->id)
            ->select('level', DB::raw('count(*) as total'))
            ->groupBy('level')
            ->pluck('total', 'level') : collect();

        $levelRows = [];
        $totalHsk2 = 0;
        $totalHsk3 = 0;
        for ($lvl = 1; $lvl <= 9; $lvl++) {
            $c2 = $hsk2Counts[$lvl] ?? 0;
            $c3 = $hsk3Counts[$lvl] ?? 0;
            $totalHsk2 += $c2;
            $totalHsk3 += $c3;
            if ($c2 > 0 || $c3 > 0) {
                $levelRows[] = [
                    "Cấp {$lvl}",
                    $c2 ? number_format($c2) . ' từ' : '—',
                    $c3 ? number_format($c3) . ' từ' : '—',
                ];
            }
        }
        $levelRows[] = [
            '<fg=yellow>Tổng cộng</>',
            '<fg=yellow>' . number_format($totalHsk2) . ' từ</>',
            '<fg=yellow>' . number_format($totalHsk3) . ' từ</>',
        ];
        $this->table(['Cấp độ', 'HSK 2.0 (2010)', 'HSK 3.0 (2026)'], $levelRows);

        // 3. Phân tích Dịch chuyển cấp độ (Level Shifts)
        $this->newLine();
        $this->comment('3. Phân tích Dịch chuyển Cấp độ giữa HSK 2.0 và HSK 3.0:');

        if ($hsk2 && $hsk3) {
            $shiftsQuery = DB::table('vocabulary_hsk_levels as l2')
                ->join('vocabulary_hsk_levels as l3', function ($join) use ($hsk2, $hsk3) {
                    $join->on('l2.vocabulary_id', '=', 'l3.vocabulary_id')
                         ->where('l2.hsk_standard_id', '=', $hsk2->id)
                         ->where('l3.hsk_standard_id', '=', $hsk3->id);
                })
                ->join('vocabularies as v', 'v.id', '=', 'l2.vocabulary_id')
                ->whereColumn('l2.level', '!=', 'l3.level')
                ->select([
                    'v.hanzi',
                    'v.pinyin',
                    'v.meaning',
                    'l2.level as old_level',
                    'l3.level as new_level',
                ]);

            $totalShifts = $shiftsQuery->count();
            $this->info("✓ Có {$totalShifts} từ vựng thay đổi cấp độ giữa HSK 2.0 và HSK 3.0.");

            $samples = $shiftsQuery->limit(10)->get();
            $shiftRows = [];
            foreach ($samples as $s) {
                $direction = $s->new_level < $s->old_level ? '🔻 Hạ cấp (Học sớm hơn)' : '🔺 Nâng cấp (Học muộn hơn)';
                $shiftRows[] = [
                    $s->hanzi,
                    $s->pinyin,
                    mb_substr($s->meaning, 0, 30) . '...',
                    "Cấp {$s->old_level}",
                    "Cấp {$s->new_level}",
                    $direction,
                ];
            }
            $this->table(['Chữ Hán', 'Pinyin', 'Nghĩa', 'HSK 2.0', 'HSK 3.0', 'Xu hướng'], $shiftRows);
        }

        // 4. Kiểm tra toàn vẹn liên kết (Integrity Checks)
        $this->newLine();
        $this->comment('4. Kiểm tra Toàn vẹn Dữ liệu (Data Integrity):');
        
        $totalVocabs = Vocabulary::count();
        $totalCards = Flashcard::count();
        $unlinkedCards = Flashcard::whereNull('vocabulary_id')->count();
        $orphanLevels = VocabularyHskLevel::whereNotExists(function ($q) {
            $q->select(DB::raw(1))->from('vocabularies')->whereColumn('vocabularies.id', 'vocabulary_hsk_levels.vocabulary_id');
        })->count();

        $this->line("• Tổng số từ vựng (Vocabulary Core): <fg=green>{$totalVocabs}</>");
        $this->line("• Tổng số Flashcards: <fg=green>{$totalCards}</>");
        
        if ($unlinkedCards === 0) {
            $this->line('• Liên kết Flashcard ➔ Vocabulary: <fg=green>100% hoàn hảo (0 thẻ chưa gán)</>');
        } else {
            $this->warn("• Cảnh báo: Còn {$unlinkedCards} flashcard chưa gán vocabulary_id!");
        }

        if ($orphanLevels === 0) {
            $this->line('• Kiểm tra Orphan Mapping: <fg=green>Không có bản ghi mồ côi</>');
        } else {
            $this->error("• Lỗi: Có {$orphanLevels} bản ghi mapping không liên kết được với từ vựng!");
        }

        $this->newLine();
        $this->info('✓ Quá trình kiểm tra hoàn tất thành công!');
        return self::SUCCESS;
    }
}
