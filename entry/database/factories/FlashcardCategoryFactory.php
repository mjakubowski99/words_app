<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FlashcardCategoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => fn() => User::factory()->create(),
            'tag' => $this->faker->name,
            'name' => $this->faker->name,
        ];
    }
}
