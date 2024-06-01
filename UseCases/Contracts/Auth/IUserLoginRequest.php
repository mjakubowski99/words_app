<?php

declare(strict_types=1);

namespace UseCases\Contracts\Auth;

interface IUserLoginRequest
{
    public function getEmail(): string;

    public function getPassword(): string;
}
