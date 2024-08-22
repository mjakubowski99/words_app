<?php

declare(strict_types=1);

namespace User\Application\Query;

use Shared\User\IUser;
use Shared\Utils\ValueObjects\UserId;
use Shared\Utils\ValueObjects\Uuid;
use User\Application\DTO\UserDTO;
use User\Domain\Repositories\IUserRepository;

class FindUserHandler
{
    public function __construct(
        private IUserRepository $user_repository,
    ) {}

    public function handle(UserId $id): UserDTO
    {
        $user = $this->user_repository->findById($id);

        return new UserDTO($user);
    }
}