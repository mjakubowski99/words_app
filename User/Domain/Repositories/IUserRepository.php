<?php

declare(strict_types=1);

namespace User\Domain\Repositories;

use User\Domain\Models\Entities\IUser;
use UseCases\Contracts\User\ICreateUserRequest;

interface IUserRepository
{
    public function findByEmail(string $email): ?IUser;

    public function create(ICreateUserRequest $request): IUser;
}
