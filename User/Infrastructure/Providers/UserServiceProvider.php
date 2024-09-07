<?php

declare(strict_types=1);

namespace User\Infrastructure\Providers;

use Shared\User\IUserFacade;
use Illuminate\Support\ServiceProvider;
use User\Application\Facades\UserFacade;
use User\Domain\Repositories\IUserRepository;
use User\Infrastructure\Repositories\UserRepository;

class UserServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(IUserRepository::class, UserRepository::class);
        $this->app->bind(IUserFacade::class, UserFacade::class);
    }
}
