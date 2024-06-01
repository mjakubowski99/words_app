<?php

declare(strict_types=1);

namespace App\Http\OpenApi\Responses;

use OpenApi\Attributes as OAT;

#[OAT\Response(
    response: 'email_verification_required',
    description: 'Email verification required response.',
    content: new OAT\JsonContent(
        properties: [
            new OAT\Property(
                property: 'message',
                type: 'string',
                example: 'Please verify your email address before',
            ),
        ],
        type: 'object',
    ),
)]
class EmailVerificationRequiredResponse {}
