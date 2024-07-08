<?php

declare(strict_types=1);

namespace UseCases\Contracts\User;

use UseCases\Contracts\Auth\IAuthenticable;

interface IUserService
{
    public function existsByAuthenticable(IAuthenticable $user): bool;

    public function findByAuthenticable(IAuthenticable $user): IUser;

    public function createFromAuthenticable(IAuthenticable $user): IUser;
}
