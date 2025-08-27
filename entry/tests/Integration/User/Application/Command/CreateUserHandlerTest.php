<?php

declare(strict_types=1);
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use User\Application\Command\CreateUser;
use Shared\Exceptions\BadRequestException;
use User\Application\Command\CreateUserHandler;
use Illuminate\Foundation\Testing\DatabaseTransactions;

uses(DatabaseTransactions::class);

beforeEach(function () {
    $this->handler = $this->app->make(CreateUserHandler::class);
});
test('handle when user does not exist creates user successfully', function () {
    // GIVEN
    $command = new CreateUser(
        'newuser@email.com',
        null,
        'newuser',
        'securepassword123',
        null,
    );

    // WHEN
    $this->handler->handle($command);

    // THEN
    $this->assertDatabaseHas('users', [
        'email' => 'newuser@email.com',
        'name' => 'newuser',
        'email_verified_at' => null,
    ]);
    expect(Hash::check('securepassword123', User::whereEmail('newuser@email.com')->first()->password))->toBeTrue();
});
test('handle when user already exists fail', function () {
    // GIVEN
    $user = $this->createUser([
        'email' => 'email@email.com',
    ]);
    $command = new CreateUser(
        'email@email.com',
        null,
        'email',
        'password123',
        null,
    );

    // THEN
    $this->expectException(BadRequestException::class);

    $this->handler->handle($command);
});
