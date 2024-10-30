<?php

declare(strict_types=1);

namespace User\Infrastructure\OAuth\Google;

class IosGoogleClient extends \Google_Client
{
    public function __construct()
    {
        parent::__construct([
            'client_id' => config('services.google.ios_client_id'),
        ]);
    }
}
