<?php

declare(strict_types=1);

namespace UseCases\Contracts\User;

interface IUserService
{
    public function validateCredentials(string $email, string $password): ?IUser;

    public function createUser(ICreateUserRequest $request): IUser;
}
