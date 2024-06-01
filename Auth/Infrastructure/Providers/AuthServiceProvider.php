<?php

declare(strict_types=1);

namespace Auth\Infrastructure\Providers;

use Auth\Domain\Services\LoginService;
use Illuminate\Support\ServiceProvider;
use Auth\Domain\Services\RegisterService;
use UseCases\Contracts\Auth\ILoginService;
use UseCases\Contracts\Auth\IRegisterService;
use Auth\Domain\Repositories\ITokenRepository;
use Auth\Infrastructure\Repositories\TokenRepository;

class AuthServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ITokenRepository::class, TokenRepository::class);
        $this->app->bind(ILoginService::class, LoginService::class);
        $this->app->bind(IRegisterService::class, RegisterService::class);
    }
}
