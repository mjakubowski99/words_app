<?php

declare(strict_types=1);

namespace User\Application\Command;

use Shared\Utils\Hash\IHash;
use Shared\Exceptions\BadRequestException;
use User\Application\Repositories\IUserRepository;

class CreateUserHandler
{
    public function __construct(
        private IUserRepository $repository,
        private IHash $hash,
    ) {}

    public function handle(CreateUser $command): void
    {
        if ($this->repository->findByEmail($command->getEmail())) {
            throw new BadRequestException('User with this email already exists!');
        }

        $this->repository->create([
            'name' => $command->getName(),
            'email' => $command->getEmail(),
            'email_verified_at' => $command->getEmailVerifiedAt(),
            'password' => $this->hash->make($command->getPassword()),
            'picture' => $command->getPicture(),
            'provider_id' => null,
            'provider_type' => null,
        ]);
    }
}
