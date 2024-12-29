<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Shared\Enum\TicketType;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class TicketFactory extends Factory
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
            'description' => $this->faker->text,
            'context' => null,
            'type' => TicketType::DELETE_ACCOUNT,
            'email' => $this->faker->email,
        ];
    }
}
