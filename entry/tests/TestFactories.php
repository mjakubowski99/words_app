<?php

declare(strict_types=1);

namespace Tests;

use App\Models\User;
use Shared\Utils\Hash\IHash;

trait TestFactories
{
    public function createUser(array $attributes = [], ?string $password = null): User
    {
        if ($password) {
            $hash = $this->app->make(IHash::class);

            return User::factory()->create(
                array_merge($attributes, ['password' => $hash->make($password)])
            );
        }

        return User::factory()->create($attributes);
    }
}
