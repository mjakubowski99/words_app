<?php

declare(strict_types=1);

namespace App\Http\OpenApi\Parameters\Query;

use OpenApi\Attributes as OAT;

#[OAT\Parameter(
    parameter: 'Query\PerPage',
    name: 'per_page',
    description: 'Requested item count.',
    in: 'query',
    required: false,
    schema: new OAT\Schema(
        type: 'integer',
        // @see entry/config/talently.php
        // config variable: default_pagination
        default: 25,
        maximum: 100,
        minimum: 1,
    ),
)]
class PerPage {}
