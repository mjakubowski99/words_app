<?php

declare(strict_types=1);

namespace User\Domain\Services;

use Shared\Utils\Str\IStr;
use Shared\Utils\Hash\IHash;
use UseCases\Contracts\User\IUser;
use User\Domain\Models\DTO\UserDTO;
use User\Domain\Repositories\IUserRepository;
use UseCases\Contracts\User\IExternalUserService;
use UseCases\Contracts\Auth\IExternalAuthenticable;

readonly class ExternalUserService implements IExternalUserService
{
    public function __construct(private IUserRepository $repository, private IHash $hash, private IStr $str) {}

    public function exists(IExternalAuthenticable $user): bool
    {
        return $this->repository->existsByProvider($user->getProviderId(), $user->getProviderType());
    }

    public function find(IExternalAuthenticable $user): IUser
    {
        $user = $this->repository->findByProvider($user->getProviderId(), $user->getProviderType());

        return new UserDTO($user);
    }

    public function create(IExternalAuthenticable $user): IUser
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
