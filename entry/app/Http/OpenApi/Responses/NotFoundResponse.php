<?php

declare(strict_types=1);

namespace App\Http\OpenApi\Responses;

use OpenApi\Attributes as OAT;

#[OAT\Response(
    response: 'not_found',
    description: 'Not found response.',
    content: new OAT\JsonContent(
        properties: [
            new OAT\Property(
                property: 'message',
                type: 'string',
                example: 'Not found.'
            ),
        ],
        type: 'object',
    ),
)]
class NotFoundResponse {}
