<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Test;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Test::class, function (Application $application) {
            return new Test($application['request']);
        });
    }

    public function boot(): void {}
}
