<?php

declare(strict_types=1);

namespace App\Http\OpenApi\Responses;

use OpenApi\Attributes as OAT;

#[OAT\Response(
    response: 'success',
    description: 'Success response.',
    content: new OAT\JsonContent(
        properties: [
            new OAT\Property(
                property: 'success',
                type: 'bool',
                example: true
            ),
        ],
        type: 'object',
    ),
)]
class SuccessResponse {}
