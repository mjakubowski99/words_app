<?php

declare(strict_types=1);

namespace User\Infrastructure\Providers;

use Shared\User\IUserFacade;
use User\Domain\Contracts\IOAuthLogin;
use Illuminate\Support\ServiceProvider;
use User\Application\Facades\UserFacade;
use User\Infrastructure\OAuth\OAuthLogin;
use User\Domain\Repositories\IUserRepository;
use User\Domain\Repositories\ITokenRepository;
use User\Infrastructure\Repositories\UserRepository;
use User\Infrastructure\Repositories\TokenRepository;

class UserServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(IUserRepository::class, UserRepository::class);
        $this->app->bind(IUserFacade::class, UserFacade::class);
        $this->app->bind(ITokenRepository::class, TokenRepository::class);
        $this->app->bind(IOAuthLogin::class, OAuthLogin::class);
    }
}
