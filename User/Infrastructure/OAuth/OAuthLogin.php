<?php

declare(strict_types=1);

namespace User\Infrastructure\OAuth;

use Shared\Enum\Platform;
use Shared\Enum\UserProvider;
use User\Domain\Contracts\IOAuthUser;
use User\Domain\Contracts\IOAuthLogin;

class OAuthLogin implements IOAuthLogin
{
    public function __construct(private OAuthLoginStrategyFactory $factory) {}

    public function login(UserProvider $provider, string $access_token, Platform $platform): IOAuthUser
    {
        $login_strategy = $this->factory->make($provider, $platform);

        return $login_strategy->userFromToken($access_token);
    }
}
