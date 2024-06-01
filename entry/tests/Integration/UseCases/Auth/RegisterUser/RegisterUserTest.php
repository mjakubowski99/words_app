<?php

declare(strict_types=1);

namespace Tests\Integration\UseCases\Auth\RegisterUser;

use Tests\TestCase;
use Tests\TestFactories;
use UseCases\Auth\RegisterUser;
use UseCases\Contracts\Auth\IRegisterUserRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegisterUserTest extends TestCase
{
    use RefreshDatabase;
    use TestFactories;

    private RegisterUser $use_case;

    protected function setUp(): void
    {
        parent::setUp();
        $this->use_case = $this->app->make(RegisterUser::class);
    }

    public function test_register_WhenEmailUnique_success(): void
    {
        // GIVEN
        $request = $this->mockery(IRegisterUserRequest::class, [
            'getEmail' => 'email@email.com',
            'getPassword' => 'password123',
        ]);

        // WHEN
        $result = $this->use_case->registerUser($request);

        // THEN
        $this->assertTrue($result->success());
        $this->assertDatabaseHas('users', [
            'email' => 'email@email.com',
        ]);
    }
}
