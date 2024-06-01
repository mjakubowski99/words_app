<?php

declare(strict_types=1);

namespace Tests\Smoke\App\Http\Controllers\Auth\AuthController;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;
    use AuthControllerTrait;

    public function test_loginUser_shouldLoginUser(): void
    {
        // GIVEN
        $user = $this->createUser(['email' => 'email@email.com'], 'password123');

        // WHEN
        $response = $this->json('POST', route('auth.login'), [
            'email' => 'email@email.com',
            'password' => 'password123',
        ]);

        // THEN
        $this->assertAuthResponseSuccessful($response);
    }

    public function test_RegisterUser_WhenValidCredentials_shouldLoginUser(): void
    {
        $user = $this->createUser(['email' => 'email@email.com'], 'password123');

        $response = $this->json('POST', route('auth.login'), [
            'email' => 'email@email.com',
            'password' => 'password123',
        ]);

        $this->assertAuthResponseSuccessful($response);
    }
}
