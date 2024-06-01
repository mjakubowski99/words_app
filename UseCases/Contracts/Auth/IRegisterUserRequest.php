<?php

declare(strict_types=1);

namespace UseCases\Contracts\Auth;

interface IRegisterUserRequest
{
    public function getEmail(): string;

    public function getUserPassword(): string;
}
