<?php

declare(strict_types=1);

namespace App\Http\OpenApi\Responses;

use OpenApi\Attributes as OAT;

#[OAT\Response(
    response: 'validation_error',
    description: 'Validation error response.',
    content: new OAT\JsonContent(
        properties: [
            new OAT\Property(
                property: 'message',
                type: 'string',
                example: 'The given data was invalid.',
            ),
            new OAT\Property(
                property: 'errors',
                properties: [
                    new OAT\Property(
                        property: 'foo.0.bar',
                        type: 'array',
                        items: new OAT\Items(
                            type: 'string',
                            example: 'The foo.0.bar format is invalid.',
                        ),
                    ),
                ],
            ),
        ],
        type: 'object',
    ),
)]
class ValidationErrorResponse {}
