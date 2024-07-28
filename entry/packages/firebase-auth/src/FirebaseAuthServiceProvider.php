<?php

declare(strict_types=1);

namespace Mjakubowski\FirebaseAuth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Kreait\Firebase\JWT\IdTokenVerifier;

class FirebaseAuthServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Auth::viaRequest('firebase', function ($request) {
            return app(FirebaseResolver::class)->fromRequest($request);
        });
    }

    public function register(): void
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
