<?php

declare(strict_types=1);

namespace Auth\Domain\Repositories;

use UseCases\Contracts\User\IUser;
use Auth\Domain\Models\Entities\IPersonalAccessToken;

interface ITokenRepository
{
    public function findToken(string $token): ?IPersonalAccessToken;

    public function createUserToken(IUser $user, string $plain_text_token): IPersonalAccessToken;

    public function removeToken(string $token): void;
}
