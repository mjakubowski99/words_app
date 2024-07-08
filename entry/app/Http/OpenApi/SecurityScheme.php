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
class SecurityScheme {}
