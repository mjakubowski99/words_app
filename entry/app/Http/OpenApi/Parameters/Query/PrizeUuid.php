<?php

declare(strict_types=1);

namespace App\Http\OpenApi\Parameters\Query;

use OpenApi\Attributes as OAT;

#[OAT\Parameter(
    parameter: 'prize_uuid_query',
    name: 'prize_uuid',
    description: 'Prize uuid',
    in: 'query',
    required: true,
    schema: new OAT\Schema(
        type: 'string',
    ),
)]
class PrizeUuid {}
