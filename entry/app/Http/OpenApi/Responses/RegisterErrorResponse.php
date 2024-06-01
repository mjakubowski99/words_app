<?php

declare(strict_types=1);

namespace App\Http\OpenApi\Responses;

use OpenApi\Attributes as OAT;

#[OAT\Response(
    response: 'register_error_response',
    description: 'Register error response',
    content: new OAT\JsonContent(
        properties: [
            new OAT\Property(
                property: 'message',
                type: 'string',
                example: 'A similar user already exists.',
            ),
        ],
        type: 'object',
    ),
)]
class RegisterErrorResponse {}
