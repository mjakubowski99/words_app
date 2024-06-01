<?php

declare(strict_types=1);

namespace App\Http\OpenApi\Schemas;

use OpenApi\Attributes as OAT;

#[OAT\Schema(
    schema: 'uuid',
    description: 'UUID4',
    type: 'string',
    format: 'uuid',
)]
class Uuid {}
