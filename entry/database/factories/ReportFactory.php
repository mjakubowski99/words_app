<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Flashcard;
use App\Models\User;
use Shared\Enum\ReportableType;
use Shared\Enum\ReportType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class ReportFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => fn () => User::factory()->create(),
            'description' => $this->faker->text,
            'type' => ReportType::DELETE_ACCOUNT,
            'email' => $this->faker->email,
            'reportable_id' => Flashcard::factory()->create()->id,
            'reportable_type' => ReportableType::FLASHCARD->value,
        ];
    }
}
