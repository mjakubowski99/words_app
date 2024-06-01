<?php

declare(strict_types=1);

namespace App\Http\OpenApi\Schemas;

use OpenApi\Attributes as OAT;

#[OAT\Schema(
    schema: 'string_item',
    description: '',
    type: 'string',
    example: 'some value'
)]
class StringOA {}
