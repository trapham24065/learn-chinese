<?php

namespace Database\Factories;

use App\Models\Flashcard;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Flashcard>
 */
class FlashcardFactory extends Factory
{
    protected $model = Flashcard::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'hanzi'            => fake()->randomElement(['你好', '谢谢', '再见', '苹果', '学校', '朋友', '咖啡']),
            'pinyin'           => fake()->randomElement(['nǐ hǎo', 'xièxie', 'zàijiàn', 'píngguǒ', 'xuéxiào', 'péngyou', 'kāfēi']),
            'meaning'          => fake()->words(2, true),
            'example'          => '这是例子',
            'example_pinyin'   => 'Zhè shì lìzi',
            'example_meaning'  => 'Đây là ví dụ',
            'hsk_level'        => fake()->numberBetween(1, 6),
            'sort_order'       => 0,
            'is_active'        => true,
        ];
    }
}
