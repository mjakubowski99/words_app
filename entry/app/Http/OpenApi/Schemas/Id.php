<?php

declare(strict_types=1);

namespace App\Http\OpenApi\Schemas;

use OpenApi\Attributes as OAT;

#[OAT\Schema(
    schema: 'id',
    description: '',
    type: 'integer',
    example: 1
)]
class Id {}
