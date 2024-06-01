<?php

declare(strict_types=1);

namespace App\Http\OpenApi\Schemas;

use OpenApi\Attributes as OAT;

#[OAT\Schema(
    schema: 'date',
    description: '',
    type: 'string',
    format: 'date',
    example: '2022:01:01'
)]
class Date {}
