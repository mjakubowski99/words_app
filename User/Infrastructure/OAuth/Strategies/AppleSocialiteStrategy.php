<?php

declare(strict_types=1);

namespace User\Infrastructure\OAuth\Strategies;

use Firebase\JWT\JWT;
use Shared\Enum\Platform;
use Shared\Enum\UserProvider;
use User\Domain\Contracts\IOAuthUser;
use Illuminate\Support\Facades\Config;
use Laravel\Socialite\Facades\Socialite;
use User\Infrastructure\OAuth\OAuthUser;

class AppleSocialiteStrategy implements IOAuthLoginStrategy
{
    public function __construct(private readonly Platform $platform) {}

    public function userFromToken(string $access_token): IOAuthUser
    {
        $this->setCredentials($this->platform);

        $driver = Socialite::driver(UserProvider::APPLE->value);

        /** @phpstan-ignore-next-line */
        $user = $driver->userFromToken($access_token);

        return new OAuthUser(
            $user->getId(),
            UserProvider::APPLE,
            $user->getName(),
            $user->getEmail(),
            $user->getNickname(),
            $user->getAvatar()
        );
    }

    private function setCredentials(Platform $platform): void
    {
        $credentials = [
            'iss' => config('services.apple.team_id'),
            'iat' => time(),
            'exp' => time() + 3600,
            'aud' => 'https://appleid.apple.com',
            'sub' => $this->resolveClientId($platform),
        ];

        $secret = JWT::encode(
            $credentials,
            config('services.apple.private_key'),
            'ES256',
            config('services.apple.key_id')
        );

        Config::set('services.apple.client_id', $this->resolveClientId($platform));
        Config::set('services.apple.client_secret', $secret);
    }

    private function resolveClientId(Platform $platform): string
    {
        switch ($platform) {
            case Platform::WEB:
                return config('services.apple.client_id');

            case Platform::ANDROID:
                return config('services.apple.android_client_id');

            case Platform::IOS:
                return config('services.apple.ios_client_id');

            default:
                throw new \UnexpectedValueException('Unsupported platform for apple');
        }
    }
}
