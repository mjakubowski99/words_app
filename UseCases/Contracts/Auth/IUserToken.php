<?php

declare(strict_types=1);

namespace UseCases\Contracts\Auth;

use UseCases\Contracts\User\IUser;

interface IUserToken
{
    public function getUser(): IUser;

    public function getToken(): string;
}
