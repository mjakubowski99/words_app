<?php

declare(strict_types=1);

namespace App\Http\OpenApi\Parameters\Header;

use OpenApi\Attributes as OAT;

#[OAT\Parameter(
    parameter: 'autorization_header',
    name: 'Authorization Header',
    description: 'Authorization Header',
    in: 'header',
    required: true,
)]
class Authorization {}
