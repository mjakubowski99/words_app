<?php

declare(strict_types=1);

namespace User\Infrastructure\OAuth\Strategies;

use User\Domain\Contracts\IOAuthUser;

interface IOAuthLoginStrategy
{
    public function userFromToken(string $access_token): IOAuthUser;
}
