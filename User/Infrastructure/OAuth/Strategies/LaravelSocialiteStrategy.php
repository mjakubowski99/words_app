<?php

declare(strict_types=1);

namespace User\Infrastructure\OAuth\Strategies;

use Shared\Enum\UserProvider;
use User\Domain\Contracts\IOAuthUser;
use Laravel\Socialite\Facades\Socialite;
use User\Infrastructure\OAuth\OAuthUser;

class LaravelSocialiteStrategy implements IOAuthLoginStrategy
{
    public function __construct(private readonly UserProvider $provider) {}

    public function userFromToken(string $access_token): IOAuthUser
    {
        $driver = Socialite::driver($this->provider->value);

        /** @phpstan-ignore-next-line */
        $user = $driver->userFromToken($access_token);

        return new OAuthUser(
            $user->getId(),
            $this->provider,
            $user->getName(),
            $user->getEmail(),
            $user->getNickname(),
            $user->getAvatar()
        );
    }
}
