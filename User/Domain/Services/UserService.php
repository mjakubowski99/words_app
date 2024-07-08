<?php

declare(strict_types=1);

namespace User\Domain\Services;

use Shared\Utils\Str\IStr;
use Shared\Utils\Hash\IHash;
use UseCases\Contracts\User\IUser;
use User\Domain\Models\DTO\UserDTO;
use UseCases\Contracts\User\IUserService;
use UseCases\Contracts\Auth\IAuthenticable;
use User\Domain\Repositories\IUserRepository;

readonly class UserService implements IUserService
{
    public function __construct(private IUserRepository $repository, private IHash $hash, private IStr $str) {}

    public function existsByAuthenticable(IAuthenticable $user): bool
    {
        return $this->repository->existsByProvider($user->getProviderId(), $user->getProviderType());
    }

    public function findByAuthenticable(IAuthenticable $user): IUser
    {
        $user = $this->repository->findByProvider($user->getProviderId(), $user->getProviderType());

        return new UserDTO($user);
    }

    public function createFromAuthenticable(IAuthenticable $user): IUser
    {
        $user = $this->repository->create([
            'name' => $user->getName(),
            'email' => $user->getEmail(),
            'email_verified_at' => now(),
            'password' => $this->hash->make($this->str->random(16)),
            'picture' => $user->getPicture(),
            'provider_id' => $user->getProviderId(),
            'provider_type' => $user->getProviderType(),
        ]);

        return new UserDTO($user);
    }
}
