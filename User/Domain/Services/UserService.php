<?php

declare(strict_types=1);

namespace User\Domain\Services;

use Shared\Utils\Hash\IHash;
use UseCases\Contracts\User\IUser;
use User\Domain\Models\DTO\UserDTO;
use UseCases\Contracts\User\IUserService;
use User\Domain\Repositories\IUserRepository;
use UseCases\Contracts\User\ICreateUserRequest;

readonly class UserService implements IUserService
{
    public function __construct(private IUserRepository $repository, private IHash $hash) {}

    public function validateCredentials(string $email, string $password): ?IUser
    {
        $user = $this->repository->findByEmail($email);

        if (!$user) {
            return null;
        }
        if (!$this->hash->check($password, $user->getPassword())) {
            return null;
        }

        return new UserDTO($user);
    }

    public function createUser(ICreateUserRequest $request): IUser
    {
        return new UserDTO(
            $this->repository->create($request)
        );
    }
}
