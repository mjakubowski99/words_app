<?php

namespace Database\Factories;

use App\Models\User;
use Exercise\Domain\Models\ExerciseStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Shared\Enum\ExerciseType;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Exercise>
 */
class ExerciseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => fn() => User::factory()->create(),
            'status' => ExerciseStatus::IN_PROGRESS,
            'exercise_type' => ExerciseType::UNSCRAMBLE_WORDS->value,
        ];
    }
}
