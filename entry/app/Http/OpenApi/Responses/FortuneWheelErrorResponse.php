<?php

declare(strict_types=1);

namespace App\Http\OpenApi\Responses;

use OpenApi\Attributes as OAT;

#[OAT\Response(
    response: 'fortune_wheel_error',
    description: 'Fortune Wheel error response.',
    content: new OAT\JsonContent(
        properties: [
            new OAT\Property(
                property: 'message',
                type: 'string',
                example: 'Fortune Wheel was already turned.',
            ),
        ],
        type: 'object',
    ),
)]
class FortuneWheelErrorResponse {}
