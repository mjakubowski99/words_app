<?php

declare(strict_types=1);

namespace Tests\Integration\User\Application\Command;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use User\Application\Command\CreateUser;
use Shared\Exceptions\BadRequestException;
use User\Application\Command\CreateUserHandler;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CreateUserHandlerTest extends TestCase
{
    use DatabaseTransactions;

    private CreateUserHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();
        $this->handler = $this->app->make(CreateUserHandler::class);
    }

    public function test__handle_WhenUserDoesNotExist_createsUserSuccessfully(): void
    {
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
        $this->assertTrue(
            Hash::check('securepassword123', User::whereEmail('newuser@email.com')->first()->password)
        );
    }

    public function test__handle_WhenUserAlreadyExists_fail(): void
    {
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
    }
}
