<?php

namespace Database\Seeders;

use App\Models\Flashcard;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HskFullVocabularySeeder extends Seeder
{
    /**
     * Run the database seeds to import the complete 5,000 HSK 1-6 vocabulary.
     */
    public function run(): void
    {
        $jsonPath = __DIR__ . '/data/hsk_vocabulary_5000.json';
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
            $this->command->info("Đang nạp " . count($items) . " từ vựng HSK 1-6...");
        }

        // Get all existing flashcards keyed by hanzi
        $existing = Flashcard::query()->pluck('id', 'hanzi')->toArray();

        $toInsert = [];
        $now = now();
        $updatedCount = 0;
        $insertedCount = 0;

        foreach ($items as $index => $item) {
            $hanzi = trim($item['hanzi']);
            if (empty($hanzi)) continue;

            if (isset($existing[$hanzi])) {
                // Word already exists - ensure hsk_level is set if missing
                Flashcard::where('id', $existing[$hanzi])
                    ->whereNull('hsk_level')
                    ->update(['hsk_level' => $item['hsk_level']]);
                $updatedCount++;
                continue;
            }

            // New word to insert
            $toInsert[] = [
                'lesson_id'       => null,
                'hsk_level'       => $item['hsk_level'],
                'hanzi'           => $hanzi,
                'pinyin'          => $item['pinyin'] ?? '',
                'meaning'         => $item['meaning'] ?? '',
                'example'         => null,
                'example_pinyin'  => null,
                'example_meaning' => null,
                'tags'            => json_encode(["hsk{$item['hsk_level']}", 'hsk_full']),
                'sort_order'      => $item['hsk_level'] * 1000 + ($index % 1000),
                'is_active'       => true,
                'created_at'      => $now,
                'updated_at'      => $now,
            ];

            $existing[$hanzi] = true;
            $insertedCount++;

            // Batch insert in chunks of 500 for optimal memory and speed
            if (count($toInsert) >= 500) {
                DB::table('flashcards')->insert($toInsert);
                $toInsert = [];
            }
        }

        if (!empty($toInsert)) {
            DB::table('flashcards')->insert($toInsert);
        }

        if ($this->command) {
            $this->command->info("Hoàn tất: Đã thêm mới {$insertedCount} từ vựng và đồng bộ {$updatedCount} thẻ hiện có.");
            $this->command->info("Tổng số flashcard hiện tại trong hệ thống: " . Flashcard::count());
        }
    }
}
