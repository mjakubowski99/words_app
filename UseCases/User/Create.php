<?php

declare(strict_types=1);

namespace UseCases\User;

use UseCases\Contracts\User\IUser;
use UseCases\Contracts\User\IExternalUserService;
use UseCases\Contracts\Auth\IExternalAuthenticable;

readonly class Create
{
    public function __construct(
        private IExternalUserService $external_user_service,
    ) {}

    public function createByExternal(IExternalAuthenticable $user): IUser
    {
        if (!$this->external_user_service->exists($user)) {
            return $this->external_user_service->create($user);
        }

        return $this->external_user_service->find($user);
    }
}
