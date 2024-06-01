<?php

declare(strict_types=1);

namespace Auth\Domain\Models\DTO;

use UseCases\Contracts\User\IUser;
use UseCases\Contracts\Auth\IUserToken;

class UserToken implements IUserToken
{
    public function __construct(private readonly IUser $user, private readonly string $token) {}

    public function getUser(): IUser
    {
        return $this->user;
    }

    public function getToken(): string
    {
        return $this->token;
    }
}
