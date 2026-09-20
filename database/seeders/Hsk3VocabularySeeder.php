<?php

namespace Database\Seeders;

use App\Models\HskStandard;
use App\Models\Vocabulary;
use App\Models\VocabularyHskLevel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Hsk3VocabularySeeder extends Seeder
{
    /**
     * Seed HSK 3.0 vocabulary mapping (Levels 1 to 6).
     */
    public function run(): void
    {
        $std2021 = HskStandard::findByCode(HskStandard::CODE_HSK_3_0_2021);
        $std2026 = HskStandard::findByCode(HskStandard::CODE_HSK_3_0_2026);

        if (!$std2021 || !$std2026) {
            $this->call(HskStandardSeeder::class);
            $std2021 = HskStandard::findByCode(HskStandard::CODE_HSK_3_0_2021);
            $std2026 = HskStandard::findByCode(HskStandard::CODE_HSK_3_0_2026);
        }

        $jsonPath = __DIR__ . '/data/hsk3_vocabulary_data.json';
        if (!file_exists($jsonPath)) {
            if ($this->command) {
                $this->command->error("Data file not found: {$jsonPath}");
            }
            return;
        }

        $items = json_decode(file_get_contents($jsonPath), true);
        if (!$items) {
            return;
        }

        if ($this->command) {
            $this->command->info("Đang nạp và ánh xạ " . count($items) . " từ vựng HSK 3.0 (Cấp 1-6)...");
        }

        // Preload existing vocabularies keyed by hanzi (or hanzi|pinyin)
        $vocabMap = [];
        Vocabulary::query()->select(['id', 'hanzi', 'pinyin'])->each(function ($v) use (&$vocabMap) {
            $hanzi = trim($v->hanzi);
            $pinyin = trim($v->pinyin);
            $vocabMap["{$hanzi}|{$pinyin}"] = $v->id;
            if (!isset($vocabMap[$hanzi])) {
                $vocabMap[$hanzi] = $v->id;
            }
        });

        // Preload existing mappings for 2021 & 2026 to avoid duplicate keys
        $existing2021 = VocabularyHskLevel::where('hsk_standard_id', $std2021->id)
            ->pluck('level', 'vocabulary_id')
            ->toArray();

        $existing2026 = VocabularyHskLevel::where('hsk_standard_id', $std2026->id)
            ->pluck('level', 'vocabulary_id')
            ->toArray();

        $now = now();
        $mappingsToInsert = [];
        $newVocabsCount = 0;
        $mappedCount = 0;

        foreach ($items as $index => $item) {
            $hanzi = trim($item['hanzi']);
            $pinyin = trim($item['pinyin'] ?? '');
            if (empty($hanzi)) continue;

            $vocabId = $vocabMap["{$hanzi}|{$pinyin}"] ?? ($vocabMap[$hanzi] ?? null);

            if (!$vocabId) {
                // New word introduced in HSK 3.0
                $vocab = Vocabulary::create([
                    'hanzi'       => $hanzi,
                    'pinyin'      => $pinyin,
                    'meaning'     => $item['meaning'] ?? '',
                    'simplified'  => $hanzi,
                    'traditional' => $item['traditional'] ?? null,
                    'is_active'   => true,
                ]);

                $vocabId = $vocab->id;
                $vocabMap["{$hanzi}|{$pinyin}"] = $vocabId;
                if (!isset($vocabMap[$hanzi])) {
                    $vocabMap[$hanzi] = $vocabId;
                }
                $newVocabsCount++;
            }

            $level = (int)$item['level'];
            $source = $item['source'] ?? 'GF 0025-2021 / CTI Syllabus';

            // Map for 2021 standard
            if (!isset($existing2021[$vocabId])) {
                $mappingsToInsert[] = [
                    'vocabulary_id'   => $vocabId,
                    'hsk_standard_id' => $std2021->id,
                    'level'           => $level,
                    'source'          => $source,
                    'topic'           => null,
                    'sort_order'      => $level * 1000 + ($index % 1000),
                    'created_at'      => $now,
                    'updated_at'      => $now,
                ];
                $existing2021[$vocabId] = $level;
            }

            // Map for 2026 standard (pre-official deployment baseline)
            if (!isset($existing2026[$vocabId])) {
                $mappingsToInsert[] = [
                    'vocabulary_id'   => $vocabId,
                    'hsk_standard_id' => $std2026->id,
                    'level'           => $level,
                    'source'          => 'CTI Syllabus 2026 Baseline',
                    'topic'           => null,
                    'sort_order'      => $level * 1000 + ($index % 1000),
                    'created_at'      => $now,
                    'updated_at'      => $now,
                ];
                $existing2026[$vocabId] = $level;
            }

            $mappedCount++;

            if (count($mappingsToInsert) >= 500) {
                DB::table('vocabulary_hsk_levels')->insert($mappingsToInsert);
                $mappingsToInsert = [];
            }
        }

        if (!empty($mappingsToInsert)) {
            DB::table('vocabulary_hsk_levels')->insert($mappingsToInsert);
        }

        if ($this->command) {
            $this->command->info("Hoàn tất: Đã thêm mới {$newVocabsCount} từ vựng mới và ánh xạ {$mappedCount} từ sang HSK 3.0.");
            $this->command->info("Tổng số từ vựng trong kho Vocabulary: " . Vocabulary::count());
        }
    }
}
