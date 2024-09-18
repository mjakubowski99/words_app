<?php

declare(strict_types=1);

return [
    'api_key' => env('GEMINI_API_KEY'),
    'api_url' => env('GEMINI_API_URL'),

    'endpoints' => [
        'generate_text' => 'v1beta/models/gemini-1.5-flash:generateContent',
    ],
];
