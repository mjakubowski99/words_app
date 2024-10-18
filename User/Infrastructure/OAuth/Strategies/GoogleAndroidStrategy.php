<?php

declare(strict_types=1);

namespace User\Infrastructure\OAuth\Strategies;

use Illuminate\Support\Arr;
use Shared\Enum\UserProvider;
use User\Domain\Contracts\IOAuthUser;
use User\Infrastructure\OAuth\OAuthUser;

class GoogleAndroidStrategy implements IOAuthLoginStrategy
{
    public function __construct(private \Google_Client $google_Client) {}

    public function userFromToken(string $access_token): IOAuthUser
    {
        $payload = $this->google_Client->verifyIdToken($access_token);

        if (!$payload) {
            throw new \Exception("Failed to verify google id token: {$access_token}");
        }

        return new OAuthUser(
            $payload['sub'],
            UserProvider::GOOGLE,
            Arr::get($payload, 'name'),
            $payload['email'],
            $payload['email'],
            Arr::get($payload, 'picture'),
        );
    }
}
