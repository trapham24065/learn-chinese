<?php

namespace Database\Factories;

use App\Models\Lesson;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Lesson>
 */
class LessonFactory extends Factory
{
    protected $model = Lesson::class;

    public function definition(): array
    {
        $title = fake()->unique()->words(3, true);

        return [
            'slug' => Str::slug($title) . '-' . fake()->unique()->numberBetween(100, 999),
            'title' => ucfirst($title),
            'summary' => fake()->sentence(),
            'difficulty' => fake()->randomElement(['starter', 'intermediate', 'advanced']),
            'sort_order' => fake()->numberBetween(1, 100),
            'estimated_minutes' => fake()->numberBetween(10, 30),
            'accent_color' => '#991b1b',
            'is_published' => true,
        ];
    }
}
