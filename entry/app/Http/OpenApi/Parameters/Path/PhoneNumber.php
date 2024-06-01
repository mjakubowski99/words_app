<?php

declare(strict_types=1);

namespace App\Http\OpenApi\Parameters\Path;

use OpenApi\Attributes as OAT;

#[OAT\Parameter(
    parameter: 'Path\PhoneNumber',
    name: 'phone_number',
    description: 'Phone Number',
    in: 'path',
    required: true,
    schema: new OAT\Schema(
        type: 'string',
    ),
)]
class PhoneNumber {}
