<?php

declare(strict_types=1);

namespace User\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;
use Shared\User\IUserFacade;
use User\Application\Facades\UserFacade;
use User\Application\Repositories\ITicketRepository;
use User\Application\Repositories\ITokenRepository;
use User\Application\Repositories\IUserRepository;
use User\Domain\Contracts\IOAuthLogin;
use User\Infrastructure\OAuth\OAuthLogin;
use User\Infrastructure\Repositories\TicketRepository;
use User\Infrastructure\Repositories\TokenRepository;
use User\Infrastructure\Repositories\UserRepository;

class UserServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(IUserRepository::class, UserRepository::class);
        $this->app->bind(IUserFacade::class, UserFacade::class);
        $this->app->bind(ITokenRepository::class, TokenRepository::class);
        $this->app->bind(IOAuthLogin::class, OAuthLogin::class);
        $this->app->bind(ITicketRepository::class, TicketRepository::class);
    }
}
