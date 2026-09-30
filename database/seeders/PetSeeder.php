<?php

namespace Database\Seeders;

use App\Models\Pet;
use App\Models\PetStage;
use Illuminate\Database\Seeder;

class PetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Idempotent — safe to run multiple times.
     */
    public function run(): void
    {
        $pet = Pet::updateOrCreate(
            ['slug' => 'dragon'],
            [
                'name'        => 'Rồng Con',
                'description' => 'Một chú rồng nhỏ đang cần được chăm sóc và nuôi dưỡng bằng kiến thức.',
                'image_data'  => null,
                'is_active'   => true,
            ]
        );

        $stages = [
            [
                'stage'                         => 0,
                'name'                          => 'Trứng',
                'emoji'                         => '🥚',
                'required_exp'                  => 0,
                'required_mastered_vocabulary'  => 0,
                'required_used_vocabulary'      => 0,
                'required_reading_activities'   => 0,
                'required_listening_activities' => 0,
                'dialogue_level'                => 0,
                'sort_order'                    => 0,
            ],
            [
                'stage'                         => 1,
                'name'                          => 'Sơ sinh',
                'emoji'                         => '🐣',
                'required_exp'                  => 100,
                'required_mastered_vocabulary'  => 0,
                'required_used_vocabulary'      => 0,
                'required_reading_activities'   => 0,
                'required_listening_activities' => 0,
                'dialogue_level'                => 0,
                'sort_order'                    => 1,
            ],
            [
                'stage'                         => 2,
                'name'                          => 'Bé',
                'emoji'                         => '🐥',
                'required_exp'                  => 250,
                'required_mastered_vocabulary'  => 0,
                'required_used_vocabulary'      => 0,
                'required_reading_activities'   => 0,
                'required_listening_activities' => 0,
                'dialogue_level'                => 0,
                'sort_order'                    => 2,
            ],
            [
                'stage'                         => 3,
                'name'                          => 'Nhỏ',
                'emoji'                         => '🦊',
                'required_exp'                  => 500,
                'required_mastered_vocabulary'  => 10,
                'required_used_vocabulary'      => 0,
                'required_reading_activities'   => 0,
                'required_listening_activities' => 0,
                'dialogue_level'                => 0,
                'sort_order'                    => 3,
            ],
            [
                'stage'                         => 4,
                'name'                          => 'Trưởng thành',
                'emoji'                         => '🐺',
                'required_exp'                  => 1000,
                'required_mastered_vocabulary'  => 30,
                'required_used_vocabulary'      => 10,
                'required_reading_activities'   => 0,
                'required_listening_activities' => 0,
                'dialogue_level'                => 0,
                'sort_order'                    => 4,
            ],
            [
                'stage'                         => 5,
                'name'                          => 'Rồng thần',
                'emoji'                         => '🐉',
                'required_exp'                  => 2000,
                'required_mastered_vocabulary'  => 60,
                'required_used_vocabulary'      => 0,
                'required_reading_activities'   => 10,
                'required_listening_activities' => 10,
                'dialogue_level'                => 0,
                'sort_order'                    => 5,
            ],
        ];

        foreach ($stages as $stageData) {
            PetStage::updateOrCreate(
                ['pet_id' => $pet->id, 'stage' => $stageData['stage']],
                $stageData
            );
        }
    }
}
