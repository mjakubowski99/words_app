<?php

declare(strict_types=1);

namespace User\Application\Facades;

use Shared\User\IUser;
use Shared\User\IUserFacade;
use Shared\Enum\UserProvider;
use User\Application\DTO\UserDTO;
use Shared\Utils\ValueObjects\UserId;
use User\Application\Query\FindUserHandler;
use User\Application\Command\CreateTokenHandler;
use User\Application\Repositories\IUserRepository;
use User\Application\Query\FindExternalUserHandler;

class UserFacade implements IUserFacade
{
    public function __construct(
        private FindExternalUserHandler $external_user_handler,
        private FindUserHandler $user_handler,
        private CreateTokenHandler $create_token_handler,
        private IUserRepository $repository,
    ) {}

    public function findByExternal(string $provider_id, UserProvider $provider): IUser
    {
        return $this->external_user_handler->handle($provider_id, $provider);
    }

    public function findById(UserId $id): IUser
    {
        return $this->user_handler->handle($id);
    }

    public function findByEmail(string $email): IUser
    {
        $user = $this->repository->findByEmail($email);

        return new UserDTO($user);
    }

    public function issueToken(UserId $id): string
    {
        return $this->create_token_handler->handle($id);
    }
}
