<?php

declare(strict_types=1);

namespace App\Http\OpenApi\Responses;

use OpenApi\Attributes as OAT;

#[OAT\Response(
    response: 'unauthenticated',
    description: 'Unauthenticated response.',
    content: new OAT\JsonContent(
        properties: [
            new OAT\Property(
                property: 'message',
                type: 'string',
                example: 'Unauthenticated.'
            ),
        ],
        type: 'object',
    ),
)]
class UnauthenticatedResponse {}
