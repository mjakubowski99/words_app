<?php

declare(strict_types=1);

namespace UseCases\Contracts\User;

interface ICreateUserRequest
{
    public function getEmail(): string;

    public function getPassword(): string;
}
