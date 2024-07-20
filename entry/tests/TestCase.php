<?php

declare(strict_types=1);

namespace Tests;

use Mockery\MockInterface;
use Shared\Enum\UserProvider;
use Mjakubowski\FirebaseAuth\FirebaseAuthenticable;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use TestFactories;

    /**
     * @template T
     *
     * @param class-string<T> $class
     *
     * @return MockInterface|T
     */
    public function mockery(string $class): mixed
    {
        return \Mockery::mock($class);
    }

    public function createFirebaseUser(string $id, UserProvider $provider): FirebaseAuthenticable
    {
        $user = new FirebaseAuthenticable();
        $user->updateOrCreateUser($id, [
            'email' => fake()->email(),
            'name' => fake()->name,
            'picture' => fake()->imageUrl,
            'sign_in_provider' => $provider === UserProvider::GOOGLE ? 'google.com' : null,
        ]);

        return $user;
    }
}
