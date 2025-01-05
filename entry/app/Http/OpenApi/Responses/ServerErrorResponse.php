<?php

declare(strict_types=1);

namespace App\Http\OpenApi\Responses;

use OpenApi\Attributes as OAT;

#[OAT\Response(
    response: 'server_error',
    description: 'Server error response.',
    content: new OAT\JsonContent(
        properties: [
            new OAT\Property(
                property: 'message',
                type: 'string',
                example: 'Server error.'
            ),
        ],
        type: 'object',
    ),
)]
class ServerErrorResponse {}
