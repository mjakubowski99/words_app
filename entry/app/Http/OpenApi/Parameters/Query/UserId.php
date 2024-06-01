<?php

declare(strict_types=1);

namespace App\Http\OpenApi\Parameters\Query;

use OpenApi\Attributes as OAT;

#[OAT\Parameter(
    parameter: 'user_id_query',
    name: 'user_id',
    description: 'User identifier',
    in: 'query',
    required: true,
    schema: new OAT\Schema(
        type: 'integer',
    ),
)]
class UserId {}
