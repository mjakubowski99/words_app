<?php

declare(strict_types=1);

namespace App\Http\OpenApi\Responses;

use OpenApi\Attributes as OAT;

#[OAT\Response(
    response: 'verification_pin_error',
    description: 'Verification PIN error response.',
    content: new OAT\JsonContent(
        properties: [
            new OAT\Property(
                property: 'message',
                type: 'string',
                example: 'Pin is not correct.',
            ),
        ],
        type: 'object',
    ),
)]
class VerifyPinErrorResponse {}
