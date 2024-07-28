<?php

declare(strict_types=1);

namespace UseCases\Contracts\User;

use UseCases\Contracts\Auth\IExternalAuthenticable;

interface IExternalUserService
{
    public function exists(IExternalAuthenticable $user): bool;

    public function find(IExternalAuthenticable $user): IUser;

    public function create(IExternalAuthenticable $user): IUser;
}
