<?php

declare(strict_types=1);

namespace User\Domain\Repositories;

use Shared\Utils\ValueObjects\UserId;

interface ITokenRepository
{
    public function create(UserId $user_id): string;
}
