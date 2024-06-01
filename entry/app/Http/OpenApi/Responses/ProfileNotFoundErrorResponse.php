<?php

declare(strict_types=1);

namespace App\Http\OpenApi\Responses;

use OpenApi\Attributes as OAT;

#[OAT\Response(
    response: 'profile_not_found_error',
    description: 'Profile not found error response.',
    content: new OAT\JsonContent(
        properties: [
            new OAT\Property(
                property: 'message',
                type: 'string',
                example: 'The Profile for given user was not found.',
            ),
            new OAT\Property(
                property: 'errors',
                properties: [
                    new OAT\Property(
                        property: 'foo.0.bar',
                        type: 'array',
                        items: new OAT\Items(
                            type: 'string',
                            example: 'The Profile for given user was not found.',
                        ),
                    ),
                ],
            ),
        ],
        type: 'object',
    ),
)]
class ProfileNotFoundErrorResponse {}
