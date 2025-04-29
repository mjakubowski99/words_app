<?php

namespace Database\Factories;

use App\Models\Exercise;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UnscrambleWordExercise>
 */
class UnscrambleWordExerciseFactory extends Factory
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
            'word' => 'word',
            'context_sentence' => 'sentence',
            'word_translation' => 'translation',
            'scrambled_word' => 'scrambled',
            'emoji' => ';)',
        ];
    }
}
