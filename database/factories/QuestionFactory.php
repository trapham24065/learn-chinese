<?php

namespace Database\Factories;

use App\Models\Lesson;
use App\Models\Question;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Question>
 */
class QuestionFactory extends Factory
{
    protected $model = Question::class;

    public function definition(): array
    {
        return [
            'lesson_id' => Lesson::factory(),
            'question' => fake()->sentence() . ' nghĩa là gì?',
            'pinyin' => 'nǐ hǎo',
            'options' => ['Xin chào', 'Cảm ơn', 'Tạm biệt', 'Xin lỗi'],
            'correct_answer' => 'Xin chào',
            'explanation' => fake()->sentence(),
            'difficulty' => 'starter',
            'sort_order' => 1,
            'is_active' => true,
        ];
    }
}
