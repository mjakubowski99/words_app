<?php

declare(strict_types=1);

namespace User\Domain\Contracts;

use Shared\Enum\UserProvider;

interface IOAuthLogin
{
    public function login(UserProvider $provider, string $access_token);
}
