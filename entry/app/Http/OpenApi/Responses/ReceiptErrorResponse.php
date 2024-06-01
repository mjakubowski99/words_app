<?php

declare(strict_types=1);

namespace App\Http\OpenApi\Responses;

use OpenApi\Attributes as OAT;

#[OAT\Response(
    response: 'receipt_error',
    description: 'Receipt error response.',
    content: new OAT\JsonContent(
        properties: [
            new OAT\Property(
                property: 'message',
                type: 'string',
                example: 'Photo scanning failed',
            ),
        ],
        type: 'object',
    ),
)]
class ReceiptErrorResponse {}
