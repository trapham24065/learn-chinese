<?php

namespace Database\Factories;

use App\Models\Story;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Story>
 */
class StoryFactory extends Factory
{
    protected $model = Story::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence(3);

        return [
            'title'                     => '故事 ' . fake()->word(),
            'title_pinyin'              => 'Gùshì',
            'title_vi'                  => 'Câu chuyện ' . fake()->word(),
            'slug'                      => Str::slug($title) . '-' . fake()->unique()->numberBetween(100, 999),
            'hsk_level'                 => fake()->numberBetween(1, 6),
            'category'                  => fake()->randomElement(['Đời sống', 'Ẩm thực', 'Giao tiếp', 'Mua sắm']),
            'cover_color'               => '#991b1b',
            'summary'                   => fake()->paragraph(),
            'content_json'              => [
                [
                    'chinese'    => '今天 天气 很 好 。',
                    'pinyin'     => 'Jīntiān tiānqì hěn hǎo.',
                    'vietnamese' => 'Hôm nay thời tiết rất tốt.',
                    'words'      => [
                        ['hanzi' => '今天', 'pinyin' => 'jīntiān', 'meaning' => 'hôm nay'],
                        ['hanzi' => '天气', 'pinyin' => 'tiānqì', 'meaning' => 'thời tiết'],
                    ],
                ],
            ],
            'quiz_json'                 => [
                [
                    'question'       => '今天 天气 怎么样 ？',
                    'options'        => ['很 好', '不 好', '下 雨', '很 冷'],
                    'correct_answer' => '很 好',
                ],
            ],
            'word_count'                => 50,
            'estimated_reading_minutes' => 2,
            'is_published'              => true,
        ];
    }
}
