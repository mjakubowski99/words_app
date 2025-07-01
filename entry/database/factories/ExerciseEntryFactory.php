<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Exercise;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ExerciseEntry>
 */
class ExerciseEntryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'exercise_id' => fn () => Exercise::factory()->create(),
            'correct_answer' => 'ans',
            'last_answer' => null,
            'last_answer_correct' => null,
            'score' => 0,
            'answers_count' => 0,
            'order' => random_int(0,10),
        ];
    }
}
