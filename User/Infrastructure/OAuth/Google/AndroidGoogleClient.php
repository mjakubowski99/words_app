<?php

declare(strict_types=1);

namespace User\Infrastructure\OAuth\Google;

use Shared\Enum\UserProvider;

class AndroidGoogleClient extends \Google_Client
{
    public function __construct()
    {
        $provider = UserProvider::GOOGLE;
        parent::__construct([
            'client_id' => config("services.{$provider->value}.android_client_id"),
        ]);
    }
}
