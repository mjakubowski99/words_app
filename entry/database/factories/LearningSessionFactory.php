<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\User;
use Shared\Enum\SessionStatus;
use App\Models\FlashcardCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class LearningSessionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'cards_per_session' => random_int(5, 10),
            'device' => $this->faker->name,
            'user_id' => fn () => User::factory()->create(),
            'flashcard_category_id' => fn () => FlashcardCategory::factory()->create(),
            'status' => SessionStatus::STARTED->value,
        ];
    }
}
