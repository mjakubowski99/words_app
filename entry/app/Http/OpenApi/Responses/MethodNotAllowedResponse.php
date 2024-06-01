<?php

declare(strict_types=1);

namespace App\Http\OpenApi\Responses;

use OpenApi\Attributes as OAT;

#[OAT\Response(
    response: 'method_not_allowed',
    description: 'Method not allowed response.',
    content: new OAT\JsonContent(
        properties: [
            new OAT\Property(
                property: 'message',
                type: 'string',
                example: 'No route found for "GET /api": Method Not Allowed (Allow: POST)'
            ),
        ],
        type: 'object',
    ),
)]
class MethodNotAllowedResponse {}
