<?php

declare(strict_types=1);

namespace Shared\User;

use Shared\Enum\UserProvider;
use Shared\Utils\ValueObjects\UserId;

interface IUserFacade
{
    public function findByExternal(string $provider_id, UserProvider $provider): IUser;

    public function findById(UserId $id): IUser;

    public function findByEmail(string $email): IUser;

    public function issueToken(UserId $id): string;
}
