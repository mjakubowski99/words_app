<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use User\Application\Command\CreateUser;
use User\Application\Command\CreateUserHandler;
use Symfony\Component\Console\Command\Command as CommandAlias;

class CreateUserCommand extends Command
{
    protected $signature = 'app:create-user';

    protected $description = 'This command creates an application user, for example, for a reviewer';

    public function handle(CreateUserHandler $handler): int
    {
        $email = $this->ask('Please enter the email');

        $password = $this->secret('Please enter the password');

        $confirmPassword = $this->secret('Please confirm the password');

        if ($password !== $confirmPassword) {
            $this->error('Passwords do not match. Please try again.');

            return CommandAlias::FAILURE;
        }

        $command = new CreateUser(
            $email,
            null,
            $email,
            $password,
            null
        );

        $handler->handle($command);

        $this->info('User created successfully!');

        return CommandAlias::SUCCESS;
    }
}
