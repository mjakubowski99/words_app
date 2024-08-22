<?php

namespace User\Application\Facades;

use Shared\Enum\UserProvider;
use Shared\User\IUser;
use Shared\User\IUserFacade;
use Shared\Utils\ValueObjects\UserId;
use User\Application\Query\FindExternalUserHandler;
use User\Application\Query\FindUserHandler;

class UserFacade implements IUserFacade
{
    public function __construct(
        private FindExternalUserHandler $external_user_handler,
        private FindUserHandler $user_handler,
    ) {}

    public function findByExternal(string $provider_id, UserProvider $provider): IUser
    {
        return $this->external_user_handler->handle($provider_id, $provider);
    }

    public function findById(UserId $id): IUser
    {
        return $this->user_handler->handle($id);
    }
}