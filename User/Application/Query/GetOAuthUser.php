<?php

declare(strict_types=1);

namespace User\Application\Query;

use Shared\Enum\UserProvider;
use User\Domain\Contracts\IOAuthUser;
use User\Domain\Contracts\IOAuthLogin;

class GetOAuthUser
{
    public function __construct(
        private IOAuthLogin $login
    ) {}

    public function get(UserProvider $provider, string $access_token): IOAuthUser
    {
        return $this->login->login($provider, $access_token);
    }
}
