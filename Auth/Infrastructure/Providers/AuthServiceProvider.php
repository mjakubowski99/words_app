<?php

declare(strict_types=1);

namespace Auth\Infrastructure\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Kreait\Firebase\JWT\IdTokenVerifier;
use Auth\Infrastructure\Guards\FirebaseGuard;

class AuthServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Auth::viaRequest('firebase', function ($request) {
            return app(FirebaseGuard::class)->user($request);
        });
    }

    public function register(): void
    {
        $this->registerIdTokenVerifier();
    }

    private function registerIdTokenVerifier(): void
    {
        $this->app->singleton(IdTokenVerifier::class, function ($app) {
            $project = config('firebase.default');

            if (empty($project)) {
                throw new \Exception('Missing firebase project id .env variable.');
            }

            return IdTokenVerifier::createWithProjectId($project);
        });
    }
}
