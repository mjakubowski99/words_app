<?php

declare(strict_types=1);

namespace Tests\Integration\UseCases\Auth\LoginUser;

use Tests\TestCase;
use Tests\TestFactories;
use UseCases\Auth\LoginUser;
use UseCases\Contracts\Auth\IUserToken;
use UseCases\Contracts\Auth\IUserLoginRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginUserTest extends TestCase
{
    use RefreshDatabase;
    use TestFactories;

    private LoginUser $use_case;

    protected function setUp(): void
    {
        parent::setUp();
        $this->use_case = $this->app->make(LoginUser::class);
    }

    public function test_login_CredentialsValid_loginUser(): void
    {
        // GIVEN
        $user = $this->createUser([], 'password123');
        $request = \Mockery::mock(IUserLoginRequest::class);
        $request->allows([
            'getEmail' => $user->email,
            'getPassword' => 'password123',
        ]);

        // WHEN
        $result = $this->use_case->login($request);

        // THEN
        $this->assertTrue($result->success());
        $this->assertInstanceOf(IUserToken::class, $result->getUserToken());
    }

    public function test_login_invalidCredentials_fail(): void
    {
        // GIVEN
        $user = $this->createUser([], 'password123');
        $request = \Mockery::mock(IUserLoginRequest::class);
        $request->allows([
            'getEmail' => $user->email,
            'getPassword' => 'password1234',
        ]);

        // WHEN
        $result = $this->use_case->login($request);

        // THEN
        $this->assertFalse($result->success());
        $this->expectException(\TypeError::class);
        $result->getUserToken();
    }
}
