<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\User;
use App\Models\Flashcard;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SmTwoFlashcard>
 */
class SmTwoFlashcardFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->create()->id,
            'flashcard_id' => Flashcard::factory()->create()->id,
            'repetition_count' => random_int(0, 10),
            'repetition_interval' => (float) (random_int(5, 100) / random_int(4, 60)),
            'repetition_ratio' => (float) (random_int(5, 100) / random_int(4, 60)),
        ];
    }
}
