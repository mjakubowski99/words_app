<?php

declare(strict_types=1);

namespace App\Http\OpenApi;

use OpenApi\Attributes as OAT;

#[OAT\SecurityScheme(
    securityScheme: 'sanctum',
    type: 'http',
    description: 'Enter authorization token from /api/login endpoint.',
    name: 'Sanctum Authorization',
    scheme: 'bearer'
)]
class SecurityScheme {}
