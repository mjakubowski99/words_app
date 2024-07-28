<?php

declare(strict_types=1);

namespace UseCases\User;

use UseCases\Contracts\User\IUser;
use Shared\Utils\ValueObjects\Uuid;
use UseCases\Contracts\User\IUserService;
use UseCases\Contracts\User\IExternalUserService;
use UseCases\Contracts\Auth\IExternalAuthenticable;

class Find
{
    public function __construct(
        private readonly IExternalUserService $external_user_service,
        private readonly IUserService $user_service,
    ) {}

    public function findByExternal(IExternalAuthenticable $user): IUser
    {
        return $this->external_user_service->find($user);
    }

    public function findById(Uuid $id): IUser
    {
        return $this->user_service->findById($id);
    }
}
