<?php

declare(strict_types=1);

namespace User\Application\Query;

use Shared\Enum\UserProvider;
use User\Application\DTO\UserDTO;
use User\Domain\Repositories\IUserRepository;

class FindExternalUserHandler
{
    public function __construct(
        private IUserRepository $user_repository,
    ) {}

    public function handle(string $provider_id, UserProvider $provider): UserDTO
    {
        $user = $this->user_repository->findByProvider($provider_id, $provider);

        return new UserDTO($user);
    }
}
