<?php

declare(strict_types=1);

namespace User\Infrastructure\Repositories;

use Illuminate\Support\Str;
use Shared\Utils\Hash\IHash;
use User\Domain\Models\Entities\IUser;
use User\Infrastructure\Entities\User;
use User\Domain\Repositories\IUserRepository;
use UseCases\Contracts\User\ICreateUserRequest;

readonly class UserRepository implements IUserRepository
{
    public function __construct(private User $user, private IHash $hash) {}

    public function findByEmail(string $email): ?IUser
    {
        return $this->user
            ->newQuery()
            ->where('email', $email)
            ->first();
    }

    public function create(ICreateUserRequest $request): IUser
    {
        return $this->user
            ->newQuery()
            ->create([
                'email' => $request->getEmail(),
                'password' => $this->hash->make($request->getPassword()),
                'name' => Str::random(16),
            ]);
    }
}
