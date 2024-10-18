<?php

declare(strict_types=1);

namespace Shared\Providers;

use Shared\Utils\Str\Str;
use Shared\Utils\Str\IStr;
use Shared\Utils\Hash\Hash;
use Shared\Utils\Hash\IHash;
use Shared\Utils\Config\Config;
use Shared\Utils\Config\IConfig;
use Shared\Utils\Storage\Storage;
use Shared\Utils\Storage\IStorage;
use Shared\Utils\Container\Container;
use Shared\Utils\Container\IContainer;
use Illuminate\Support\ServiceProvider;
use Shared\Database\ITransactionManager;
use Shared\Database\LaravelTransactionManager;

class SharedServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(IHash::class, Hash::class);
        $this->app->bind(IStorage::class, Storage::class);
        $this->app->bind(IConfig::class, Config::class);
        $this->app->bind(IStr::class, Str::class);
        $this->app->bind(IContainer::class, Container::class);
        $this->app->bind(ITransactionManager::class, LaravelTransactionManager::class);
    }
}
