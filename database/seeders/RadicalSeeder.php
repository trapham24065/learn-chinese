<?php

namespace Database\Seeders;

use App\Models\Flashcard;
use App\Models\Radical;
use App\Models\RadicalCharacter;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RadicalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $radicalsFile = __DIR__ . '/data/kangxi_radicals.php';
        $charactersFile = __DIR__ . '/data/radical_characters.php';

        if (! file_exists($radicalsFile)) {
            $this->command?->error("File {$radicalsFile} not found!");
            return;
        }

        $radicalsData = require $radicalsFile;

        $this->command?->info("Seeding " . count($radicalsData) . " Kangxi Radicals...");

        DB::transaction(function () use ($radicalsData, $charactersFile) {
            $radicalIdMap = []; // radical_number => id

            foreach ($radicalsData as $data) {
                $radical = Radical::updateOrCreate(
                    ['radical_number' => $data['radical_number']],
                    [
                        'character'         => $data['character'],
                        'variants'          => $data['variants'],
                        'display_character' => $data['display_character'],
                        'slug'              => $data['slug'],
                        'name_vi'           => $data['name_vi'],
                        'pinyin'            => $data['pinyin'],
                        'meaning_vi'        => $data['meaning_vi'],
                        'stroke_count'      => $data['stroke_count'],
                        'position'          => $data['position'],
                        'position_desc'     => $data['position_desc'],
                        'description'       => $data['description'] ?? null,
                        'mnemonic'          => $data['mnemonic'] ?? null,
                        'is_common'         => (bool) ($data['is_common'] ?? false),
                        'common_rank'       => $data['common_rank'] ?? null,
                        'sort_order'        => $data['sort_order'] ?? $data['radical_number'],
                    ]
                );

                $radicalIdMap[$radical->radical_number] = $radical->id;
            }

            if (file_exists($charactersFile)) {
                $charactersData = require $charactersFile;
                $this->command?->info("Seeding " . count($charactersData) . " Radical Characters...");

                // Pre-index flashcards for fast ID linking
                $flashcardsMap = Flashcard::query()
                    ->where('is_active', true)
                    ->pluck('id', 'hanzi')
                    ->toArray();

                foreach ($charactersData as $c) {
                    $radicalId = $radicalIdMap[$c['radical_number']] ?? null;
                    if (! $radicalId) {
                        continue;
                    }

                    $matchedFlashcardId = $flashcardsMap[$c['character']] ?? null;

                    RadicalCharacter::updateOrCreate(
                        [
                            'radical_id' => $radicalId,
                            'character'  => $c['character'],
                        ],
                        [
                            'pinyin'       => $c['pinyin'],
                            'meaning_vi'   => $c['meaning_vi'],
                            'hsk_level'    => $c['hsk_level'] ?? null,
                            'flashcard_id' => $matchedFlashcardId,
                            'is_featured'  => (bool) ($c['is_featured'] ?? false),
                            'sort_order'   => $c['sort_order'] ?? 0,
                        ]
                    );
                }
            }
        });

        $this->command?->info("Radicals and characters seeded successfully!");
    }
}
