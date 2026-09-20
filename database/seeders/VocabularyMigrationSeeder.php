<?php

namespace Database\Seeders;

use App\Models\Flashcard;
use App\Models\HskStandard;
use App\Models\Vocabulary;
use App\Models\VocabularyHskLevel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VocabularyMigrationSeeder extends Seeder
{
    /**
     * Migrate existing flashcards into the centralized vocabularies and vocabulary_hsk_levels tables.
     */
    public function run(): void
    {
        $hsk2Standard = HskStandard::findByCode(HskStandard::CODE_HSK_2_0);
        if (!$hsk2Standard) {
            $this->call(HskStandardSeeder::class);
            $hsk2Standard = HskStandard::findByCode(HskStandard::CODE_HSK_2_0);
        }

        if ($this->command) {
            $this->command->info('Bắt đầu chuyển dịch Flashcards sang bảng Vocabulary trung tâm...');
        }

        // Get all flashcards
        $flashcards = Flashcard::query()->orderBy('id')->get();
        if ($flashcards->isEmpty()) {
            if ($this->command) {
                $this->command->warn('Không có flashcard nào để chuyển dịch.');
            }
            return;
        }

        // Existing vocabularies keyed by hanzi (or hanzi|pinyin)
        $vocabMap = [];
        Vocabulary::query()->each(function (Vocabulary $v) use (&$vocabMap) {
            $key = $v->hanzi . '|' . trim($v->pinyin);
            $vocabMap[$key] = $v->id;
            // Also key by hanzi alone if first occurrence
            if (!isset($vocabMap[$v->hanzi])) {
                $vocabMap[$v->hanzi] = $v->id;
            }
        });

        $now = now();
        $newVocabs = [];
        $vocabHskLevelsToInsert = [];
        $flashcardUpdates = [];
        $existingLevelKeys = [];

        // Preload existing vocabulary_hsk_levels for hsk2
        VocabularyHskLevel::where('hsk_standard_id', $hsk2Standard->id)
            ->pluck('vocabulary_id')
            ->each(function ($vocId) use (&$existingLevelKeys) {
                $existingLevelKeys[$vocId] = true;
            });

        foreach ($flashcards as $fc) {
            $hanzi = trim($fc->hanzi);
            $pinyin = trim($fc->pinyin);
            $fullKey = $hanzi . '|' . $pinyin;

            $vocabId = $vocabMap[$fullKey] ?? ($vocabMap[$hanzi] ?? null);

            if (!$vocabId) {
                // Insert vocabulary entry
                $vocab = Vocabulary::create([
                    'hanzi'           => $hanzi,
                    'pinyin'          => $fc->pinyin,
                    'meaning'         => $fc->meaning,
                    'simplified'      => $hanzi,
                    'example'         => $fc->example,
                    'example_pinyin'  => $fc->example_pinyin,
                    'example_meaning' => $fc->example_meaning,
                    'is_active'       => $fc->is_active ?? true,
                ]);

                $vocabId = $vocab->id;
                $vocabMap[$fullKey] = $vocabId;
                if (!isset($vocabMap[$hanzi])) {
                    $vocabMap[$hanzi] = $vocabId;
                }
            }

            // Link flashcard
            if ($fc->vocabulary_id !== $vocabId) {
                $fc->vocabulary_id = $vocabId;
                $fc->saveQuietly();
            }

            // Map HSK 2.0 level if present
            if ($fc->hsk_level && !isset($existingLevelKeys[$vocabId])) {
                $vocabHskLevelsToInsert[] = [
                    'vocabulary_id'   => $vocabId,
                    'hsk_standard_id' => $hsk2Standard->id,
                    'level'           => $fc->hsk_level,
                    'source'          => 'Hanban 2010 Syllabus',
                    'sort_order'      => $fc->sort_order ?? 0,
                    'created_at'      => $now,
                    'updated_at'      => $now,
                ];
                $existingLevelKeys[$vocabId] = true;

                if (count($vocabHskLevelsToInsert) >= 500) {
                    DB::table('vocabulary_hsk_levels')->insert($vocabHskLevelsToInsert);
                    $vocabHskLevelsToInsert = [];
                }
            }
        }

        if (!empty($vocabHskLevelsToInsert)) {
            DB::table('vocabulary_hsk_levels')->insert($vocabHskLevelsToInsert);
        }

        if ($this->command) {
            $totalVocabs = Vocabulary::count();
            $totalMapped = VocabularyHskLevel::where('hsk_standard_id', $hsk2Standard->id)->count();
            $this->command->info("Hoàn tất chuyển dịch: {$totalVocabs} từ vựng và {$totalMapped} ánh xạ HSK 2.0.");
        }
    }
}
