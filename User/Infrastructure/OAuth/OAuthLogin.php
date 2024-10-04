<?php

declare(strict_types=1);

namespace User\Infrastructure\OAuth;

use Shared\Enum\UserProvider;
use User\Domain\Contracts\IOAuthUser;
use User\Domain\Contracts\IOAuthLogin;
use Laravel\Socialite\Facades\Socialite;

class OAuthLogin implements IOAuthLogin
{
    public function login(UserProvider $provider, string $access_token): IOAuthUser
    {
        $driver = Socialite::driver($provider->value);

        if (!method_exists($driver, 'userFromToken')) {
            throw new \UnexpectedValueException('Unexpected problem');
        }

        $user = $driver->userFromToken($access_token);

        return new OAuthUser(
            $user->getId(),
            $provider,
            $user->getName(),
            $user->getEmail(),
            $user->getNickname(),
            $user->getAvatar()
        );
    }
}
