<?php

declare(strict_types=1);

namespace User\Infrastructure\OAuth;

use Shared\Enum\Platform;
use Shared\Enum\UserProvider;
use Illuminate\Container\Container;
use User\Infrastructure\OAuth\Google\IosGoogleClient;
use User\Infrastructure\OAuth\Google\AndroidGoogleClient;
use User\Infrastructure\OAuth\Strategies\IOAuthLoginStrategy;
use User\Infrastructure\OAuth\Strategies\GoogleClientStrategy;
use User\Infrastructure\OAuth\Strategies\LaravelSocialiteStrategy;

class OAuthLoginStrategyFactory
{
    public function __construct(
        private readonly Container $app
    ) {}

    public function make(UserProvider $provider, Platform $platform): IOAuthLoginStrategy
    {
        if ($provider === UserProvider::GOOGLE && $platform === Platform::ANDROID) {
            return new GoogleClientStrategy(
                $this->app->make(AndroidGoogleClient::class)
            );
        }
        if ($provider === UserProvider::GOOGLE && $platform === Platform::IOS) {
            return new GoogleClientStrategy(
                $this->app->make(IosGoogleClient::class)
            );
        }
        if ($provider === UserProvider::GOOGLE && $platform === Platform::WEB) {
            return new LaravelSocialiteStrategy(UserProvider::GOOGLE);
        }

        throw new \UnexpectedValueException("OAuth login for  provider {$provider->value} for {$platform->value} is not supported");
    }
}
