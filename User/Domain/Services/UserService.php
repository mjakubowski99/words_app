<?php

declare(strict_types=1);

namespace User\Domain\Services;

use UseCases\Contracts\User\IUser;
use Shared\Utils\ValueObjects\Uuid;
use User\Domain\Models\DTO\UserDTO;
use UseCases\Contracts\User\IUserService;
use User\Domain\Repositories\IUserRepository;

class UserService implements IUserService
{
    public function __construct(private readonly IUserRepository $repository) {}

    public function findById(Uuid $id): IUser
    {
        return new UserDTO(
            $this->repository->findById($id)
        );
    }
}
