<?php

declare(strict_types=1);

namespace App\Http\OpenApi\Responses;

use OpenApi\Attributes as OAT;

#[OAT\Response(
    response: 'profile_already_exists_error',
    description: 'Profile already exists error response.',
    content: new OAT\JsonContent(
        properties: [
            new OAT\Property(
                property: 'message',
                type: 'string',
                example: 'The Profile for given user already exists.',
            ),
        ],
        type: 'object',
    ),
)]
class ProfileAlreadyExistsErrorResponse {}
