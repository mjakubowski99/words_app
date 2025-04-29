<?php

namespace Database\Factories;

use App\Models\Exercise;
use App\Models\LearningSessionFlashcard;
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
            'exercise_id' => fn() => Exercise::factory()->create(),
            'session_flashcard_id' => fn() => LearningSessionFlashcard::factory()->create(),
            'correct_answer' => 'ans',
            'last_answer' => null,
            'last_answer_correct' => null,
            'score' => 0,
            'answers_count' => 0,
        ];
    }
}
