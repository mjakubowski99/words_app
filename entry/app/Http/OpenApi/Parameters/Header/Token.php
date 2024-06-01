<?php

declare(strict_types=1);

namespace App\Http\OpenApi\Parameters\Header;

use OpenApi\Attributes as OAT;

#[OAT\Parameter(
    parameter: 'token_header',
    name: 'token',
    description: 'Logged user token',
    in: 'header',
    required: true,
    schema: new OAT\Schema(
        type: 'string',
    ),
)]
class Token {}
