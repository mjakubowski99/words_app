<?php

declare(strict_types=1);

namespace User\Infrastructure\Repositories;

use Shared\Enum\UserProvider;
use Shared\Utils\ValueObjects\UserId;
use User\Domain\Models\Entities\IUser;
use User\Infrastructure\Entities\User;
use User\Application\Repositories\IUserRepository;

readonly class UserRepository implements IUserRepository
{
    public function __construct(private User $user) {}

    public function findById(UserId $id): IUser
    {
        return $this->user
            ->newQuery()
            ->findOrFail($id->getValue());
    }

    public function findByEmail(string $email): ?IUser
    {
        return $this->user
            ->newQuery()
            ->where('email', $email)
            ->first();
    }

    /** @param array<string, mixed> $attributes */
    public function create(array $attributes): IUser
    {
        return $this->user
            ->newQuery()
            ->create($attributes);
    }

    public function delete(UserId $user_id): void
    {
        $this->user
            ->newQuery()
            ->where('id', $user_id->getValue())
            ->delete();
    }

    public function existsByProvider(string $provider_id, UserProvider $provider): bool
    {
        return $this->user
            ->newQuery()
            ->where('provider_id', $provider_id)
            ->where('provider_type', $provider->value)
            ->exists();
    }

    public function findByProvider(string $provider_id, UserProvider $provider): IUser
    {
        return $this->user
            ->newQuery()
            ->where('provider_id', $provider_id)
            ->where('provider_type', $provider->value)
            ->firstOrFail();
    }

    public function update(IUser $user): void
    {
        $this->user
            ->newQuery()
            ->where('id', $user->getId()->getValue())
            ->update([
                'email' => $user->getEmail(),
                'name' => $user->getName(),
                'password' => $user->getPassword(),
                'user_language' => $user->getUserLanguage()->getValue(),
                'learning_language' => $user->getLearningLanguage()->getValue(),
                'profile_completed' => $user->profileCompleted(),
            ]);
    }
}
