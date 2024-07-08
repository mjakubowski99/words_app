<?php

declare(strict_types=1);

namespace UseCases\User;

use UseCases\Contracts\User\IUser;
use UseCases\Contracts\User\IUserService;
use UseCases\Contracts\Auth\IAuthenticable;

class Find
{
    public function __construct(
        private IUserService $user_service,
    ) {}

    public function findByAuthenticable(IAuthenticable $user): IUser
    {
        return $this->user_service->findByAuthenticable($user);
    }
}
