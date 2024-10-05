<?php

declare(strict_types=1);

namespace User\Infrastructure\OAuth;

use Shared\Enum\Platform;
use Shared\Enum\UserProvider;
use User\Domain\Contracts\IOAuthUser;
use User\Domain\Contracts\IOAuthLogin;
use Laravel\Socialite\Facades\Socialite;

class OAuthLogin implements IOAuthLogin
{
    public function __construct(private \Google_Client $google_Client) {}

    public function login(UserProvider $provider, string $access_token, Platform $platform): IOAuthUser
    {
        if ($provider === UserProvider::GOOGLE && $platform === Platform::ANDROID) {
            $payload = $this->google_Client->verifyIdToken($access_token);

            if (!$payload) {
                throw new \Exception("Failed to verify google id token: {$access_token}");
            }

            return new OAuthUser(
                $payload['sub'],
                $provider,
                $payload['name'],
                $payload['email'],
                $payload['email'],
                $payload['picture'],
            );
        }

        $driver = Socialite::driver($provider->value);

        /** @phpstan-ignore-next-line */
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
