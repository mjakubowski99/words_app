<?php

declare(strict_types=1);

namespace User\Application\Command;

use Shared\Utils\Str\IStr;
use Shared\Utils\Hash\IHash;
use User\Domain\Repositories\IUserRepository;

class CreateExternalUserHandler
{
    public function __construct(
        private IUserRepository $repository,
        private IHash $hash,
        private IStr $str
    ) {}

    public function handle(CreateExternalUser $command): void
    {
        if (!$this->repository->existsByProvider($command->getProviderId(), $command->getProviderType())) {
            $this->repository->create([
                'name' => $command->getName(),
                'email' => $command->getEmail(),
                'email_verified_at' => now(),
                'password' => $this->hash->make($this->str->random(16)),
                'picture' => $command->getPicture(),
                'provider_id' => $command->getProviderId(),
                'provider_type' => $command->getProviderType(),
            ]);
        }
    }
}
