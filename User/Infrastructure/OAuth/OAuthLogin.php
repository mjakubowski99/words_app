<?php

declare(strict_types=1);

namespace User\Infrastructure\OAuth;

use Shared\Enum\Platform;
use Shared\Enum\UserProvider;
use User\Domain\Contracts\IOAuthUser;
use User\Domain\Contracts\IOAuthLogin;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Config;

class OAuthLogin implements IOAuthLogin
{
    public function login(UserProvider $provider, string $access_token, Platform $platform): IOAuthUser
    {
        $this->adjustConfigToPlatform($provider, $platform);

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

    private function adjustConfigToPlatform(UserProvider $provider, Platform $platform): void
    {
        if ($platform === Platform::WEB) {
            //default platform
            return;
        }

        Config::set("services.{$provider->value}.client_id", config("services.{$provider->value}.android_client_id"));
        Config::set("services.{$provider->value}.client_secret", config("services.{$provider->value}.android_client_secret"));
    }
}
