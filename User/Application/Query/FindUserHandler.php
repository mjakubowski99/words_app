<?php

declare(strict_types=1);

namespace User\Application\Query;

use User\Application\DTO\UserDTO;
use Shared\Utils\ValueObjects\UserId;
use User\Application\Repositories\IUserRepository;

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
