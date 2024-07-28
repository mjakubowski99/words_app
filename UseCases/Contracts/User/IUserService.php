<?php

declare(strict_types=1);

namespace UseCases\Contracts\User;

use Shared\Utils\ValueObjects\Uuid;

interface IUserService
{
    public function findById(Uuid $id): IUser;
}
