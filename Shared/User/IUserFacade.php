<?php

namespace Shared\User;

use Shared\Auth\IExternalAuthenticable;
use Shared\Enum\UserProvider;
use Shared\Utils\ValueObjects\UserId;
use Shared\Utils\ValueObjects\Uuid;

interface IUserFacade
{
    public function findByExternal(string $provider_id, UserProvider $provider): IUser;

    public function findById(UserId $id): IUser;
}