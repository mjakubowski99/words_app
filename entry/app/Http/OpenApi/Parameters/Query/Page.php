<?php

declare(strict_types=1);

namespace App\Http\OpenApi\Parameters\Query;

use OpenApi\Attributes as OAT;

#[OAT\Parameter(
    parameter: 'Query\Page',
    name: 'page',
    description: 'Requested page.',
    in: 'query',
    required: false,
    schema: new OAT\Schema(
        type: 'integer',
        default: 1,
        minimum: 1,
    ),
)]
class Page {}
