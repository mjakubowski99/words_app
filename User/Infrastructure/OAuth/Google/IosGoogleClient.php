<?php

declare(strict_types=1);

namespace User\Infrastructure\OAuth\Google;

use Shared\Enum\UserProvider;

class IosGoogleClient extends \Google_Client
{
    public function __construct()
    {
        $provider = UserProvider::GOOGLE;
        parent::__construct([
            'client_id' => config("services.{$provider->value}.ios_client_id"),
        ]);
    }
}
