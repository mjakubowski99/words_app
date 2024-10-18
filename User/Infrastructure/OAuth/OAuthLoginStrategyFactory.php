<?php

declare(strict_types=1);

namespace User\Infrastructure\OAuth;

use Shared\Enum\Platform;
use Shared\Enum\UserProvider;
use Illuminate\Support\Facades\App;
use User\Infrastructure\OAuth\Strategies\IOAuthLoginStrategy;
use User\Infrastructure\OAuth\Strategies\GoogleAndroidStrategy;
use User\Infrastructure\OAuth\Strategies\LaravelSocialiteStrategy;

class OAuthLoginStrategyFactory
{
    public function __construct(
        private readonly App $app
    ) {}

    public function make(UserProvider $provider, Platform $platform): IOAuthLoginStrategy
    {
        if ($provider === UserProvider::GOOGLE && $platform === Platform::ANDROID) {
            return $this->app::make(GoogleAndroidStrategy::class);
        }
        if ($provider === UserProvider::GOOGLE && $platform === Platform::WEB) {
            return new LaravelSocialiteStrategy(UserProvider::GOOGLE);
        }

        throw new \UnexpectedValueException("OAuth login for  provider {$provider->value} for {$platform->value} is not supported");
    }
}
