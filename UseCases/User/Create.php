<?php

declare(strict_types=1);

namespace UseCases\User;

use UseCases\Contracts\User\IUser;
use UseCases\Contracts\User\IUserService;
use UseCases\Contracts\Auth\IAuthenticable;

readonly class Create
{
    public function __construct(
        private IUserService $user_service,
    ) {}

    public function findOrCreate(IAuthenticable $user): IUser
    {
        if ($this->user_service->existsByAuthenticable($user)) {
            return $this->user_service->findByAuthenticable($user);
        }

        return $this->user_service->createFromAuthenticable($user);
    }
}
