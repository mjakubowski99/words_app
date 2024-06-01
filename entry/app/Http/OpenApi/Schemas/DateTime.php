<?php

declare(strict_types=1);

namespace App\Http\OpenApi\Schemas;

use OpenApi\Attributes as OAT;

#[OAT\Schema(
    schema: 'date_time',
    description: '',
    type: 'string',
    format: 'date-time',
    example: '2021:01:01 00:00:000'
)]
class DateTime {}
