<?php

declare(strict_types=1);

namespace User\Infrastructure\OAuth\Google;

class AndroidGoogleClient extends \Google_Client
{
    public function __construct()
    {
        parent::__construct([
            'client_id' => config('services.google.android_client_id'),
        ]);
    }
}
