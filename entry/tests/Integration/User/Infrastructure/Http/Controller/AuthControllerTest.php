<?php

declare(strict_types=1);

namespace Tests\Integration\User\Infrastructure\Http\Controller;

use Tests\TestCase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AuthControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function test__login_WhenCredentialsCorrect_success(): void
    {
        $this->createUser([
            'email' => 'email@email.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->json('POST', route('user.login'), [
            'username' => 'email@email.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'token',
                'user' => [
                    'id',
                    'email',
                    'name',
                    'has_any_session',
                ],
            ],
        ]);
    }

    public function test__login_WhenPasswordIncorrect_fail(): void
    {
        $this->createUser([
            'email' => 'email@email.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson(route('user.login'), [
            'username' => 'email@email.com',
            'password' => 'password1234',
        ]);

        $response->assertStatus(400);
        $response->assertJsonStructure([
            'message',
        ]);
    }

    public function test__login_WhenEmailIncorrect_fail(): void
    {
        $this->createUser([
            'email' => 'email@email1.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson(route('user.login'), [
            'username' => 'email@email.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(400);
        $response->assertJsonStructure([
            'message',
        ]);
    }
}
