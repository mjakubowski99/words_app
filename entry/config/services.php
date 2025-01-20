<?php

declare(strict_types=1);

use Shared\Enum\UserProvider;

return [
    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    UserProvider::GOOGLE->value => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URL'),

        'android_client_id' => env('ANDROID_GOOGLE_CLIENT_ID', 'android'),
        'ios_client_id' => env('IOS_GOOGLE_CLIENT_ID', 'ios'),
    ],

    UserProvider::APPLE->value => [
        'client_id' => env('APPLE_CLIENT_ID'),
        'client_secret' => '',
        'redirect' => env('APPLE_REDIRECT_URL'),
        'android_client_id' => env('ANDROID_APPLE_CLIENT_ID'),
        'ios_client_id' => env('IOS_APPLE_CLIENT_ID'),
    ],
];
