<?php

declare(strict_types=1);

namespace App\Http\OpenApi\Responses;

use OpenApi\Attributes as OAT;

#[OAT\Response(
    response: 'user_account_banned_error',
    description: 'User Account Banned error response.',
    content: new OAT\JsonContent(
        properties: [
            new OAT\Property(
                property: 'message',
                type: 'string',
                example: 'User Account Banned.',
            ),
        ],
        type: 'object',
    ),
)]
class UserAccountBannedErrorResponse {}
