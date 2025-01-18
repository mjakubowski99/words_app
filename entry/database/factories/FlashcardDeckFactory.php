<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Admin;
use App\Models\User;
use Shared\Enum\LanguageLevel;
use Illuminate\Database\Eloquent\Factories\Factory;

class FlashcardDeckFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => fn () => User::factory()->create(),
            'tag' => $this->faker->name,
            'name' => $this->faker->name,
            'default_language_level' => LanguageLevel::A1->value,
        ];
    }

    public function byAdmin(Admin $admin): Factory
    {
        return $this->state(function (array $attributes) use ($admin) {
            return [
                'user_id' => null,
                'admin_id' => $admin->id,
            ];
        });
    }

    public function byUser(User $user): Factory
    {
        return $this->state(function (array $attributes) use ($user) {
            return [
                'user_id' => $user->id,
                'admin_id' => null,
            ];
        });
    }
}
