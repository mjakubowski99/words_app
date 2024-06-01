<?php

declare(strict_types=1);

namespace App\Http\OpenApi\Responses;

use OpenApi\Attributes as OAT;

#[OAT\Response(
    response: 'missing_properties',
    description: 'Missing properties',
    content: new OAT\JsonContent(
        properties: [
            new OAT\Property(
                property: 'message',
                type: 'string',
                example: 'total'
            ),
        ],
        type: 'object',
    ),
)]
class MissingProperties {}
