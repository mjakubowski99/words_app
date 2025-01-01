<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Admin;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Admin>
 */
class AdminFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => Uuid::uuid4(),
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ];
    }
}
