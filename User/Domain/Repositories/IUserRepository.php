<?php

declare(strict_types=1);

namespace User\Domain\Repositories;

use Shared\Enum\UserProvider;
use Shared\Utils\ValueObjects\Uuid;
use User\Domain\Models\Entities\IUser;

interface IUserRepository
{
    public function findById(Uuid $id): IUser;

    public function findByProvider(string $provider_id, UserProvider $provider): IUser;

    public function findByEmail(string $email): ?IUser;

    public function existsByProvider(string $provider_id, UserProvider $provider): bool;

    public function create(array $attributes): IUser;
}
