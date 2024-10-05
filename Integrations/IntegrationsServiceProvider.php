<?php

declare(strict_types=1);

namespace Integrations;

use App\Providers\AppServiceProvider;
use Integrations\Gemini\GeminiApiClient;
use Shared\Integrations\Gemini\IGeminiApiClient;

class IntegrationsServiceProvider extends AppServiceProvider
{
    public function register(): void
    {
        $this->app->bind(IGeminiApiClient::class, GeminiApiClient::class);
        $this->app->bind(\Google_Client::class, function ($app) {
            return new \Google_Client(['client_id' => config('services.google.android_client_id')]);
        });
    }
}