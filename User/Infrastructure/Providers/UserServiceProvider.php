<?php

declare(strict_types=1);

namespace User\Infrastructure\Providers;

use User\Domain\Services\UserService;
use Illuminate\Support\ServiceProvider;
use UseCases\Contracts\User\IUserService;
use User\Domain\Repositories\IUserRepository;
use User\Domain\Services\ExternalUserService;
use UseCases\Contracts\User\IExternalUserService;
use User\Infrastructure\Repositories\UserRepository;

class UserServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(IUserRepository::class, UserRepository::class);
        $this->app->bind(IExternalUserService::class, ExternalUserService::class);
        $this->app->bind(IUserService::class, UserService::class);
    }
}
