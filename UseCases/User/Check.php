<?php

declare(strict_types=1);

namespace UseCases\User;

use UseCases\Contracts\User\IUserService;
use UseCases\Contracts\Auth\IAuthenticable;

class Check
{
    public function __construct(
        private IUserService $user_service,
    ) {}

    public function exists(IAuthenticable $user): bool
    {
        return $this->user_service->existsByAuthenticable($user);
    }
}
