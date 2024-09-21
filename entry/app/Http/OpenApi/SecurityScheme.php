<?php

declare(strict_types=1);

namespace App\Http\OpenApi;

use OpenApi\Attributes as OAT;

#[OAT\SecurityScheme(
    securityScheme: 'firebase',
    type: 'http',
    description: 'Enter authorization token from firebase',
    name: 'Firebase Authorization',
    scheme: 'bearer'
)]
#[OAT\SecurityScheme(
    securityScheme: 'sanctum',
    type: 'http',
    description: 'Laravel sanctum tokens',
    name: 'Authorization token for app',
    scheme: 'bearer'
)]
class SecurityScheme {}
