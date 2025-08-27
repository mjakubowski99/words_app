<?php

declare(strict_types=1);
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\DatabaseTransactions;

uses(DatabaseTransactions::class);

test('login when credentials correct success', function () {
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
});
test('login when password incorrect fail', function () {
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
});
test('login when email incorrect fail', function () {
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
});
