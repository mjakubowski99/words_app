<?php

declare(strict_types=1);

namespace User\Infrastructure\Repositories;

use Shared\Utils\ValueObjects\UserId;
use User\Application\Repositories\ITokenRepository;
use User\Infrastructure\Entities\User;

class TokenRepository implements ITokenRepository
{
    public function __construct(
        private User $user
    ) {}

    public function create(UserId $user_id): string
    {
        $user_model = $this->user->newQuery()->find($user_id->getValue());

        return $user_model->createToken($user_model->email)->plainTextToken;
    }
}
