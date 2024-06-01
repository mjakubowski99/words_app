<?php

declare(strict_types=1);

namespace App\Http\OpenApi\Parameters\Path;

use OpenApi\Attributes as OAT;

#[OAT\Parameter(
    parameter: 'user_id_in_path',
    name: 'user_id',
    description: 'User identifier',
    in: 'path',
    required: true,
    schema: new OAT\Schema(
        type: 'integer',
    ),
)]
class UserId {}
