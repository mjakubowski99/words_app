<?php

declare(strict_types=1);

namespace App\Http\OpenApi\Parameters\Query;

use OpenApi\Attributes as OAT;

#[OAT\Parameter(
    parameter: 'Query\Search',
    name: 'search',
    description: 'Search phrase.',
    in: 'query',
    required: false,
    schema: new OAT\Schema(
        type: 'string',
        default: null,
        minLength: 2,
    ),
)]
class Search {}
