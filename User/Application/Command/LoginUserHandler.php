<?php

declare(strict_types=1);

namespace User\Application\Command;

use Shared\User\IUser;
use Shared\Utils\Hash\IHash;
use User\Application\DTO\UserDTO;
use User\Application\Repositories\IUserRepository;

class LoginUserHandler
{
    public function __construct(
        private IUserRepository $repository,
        private IHash $hash,
    ) {}

    public function handle(string $username, string $password): ?IUser
    {
        $user = $this->repository->findByEmail($username);

        if (!$user) {
            return null;
        }

        if (!$this->hash->check($password, $user->getPassword())) {
            return null;
        }

        return new UserDTO($user);
    }
}
