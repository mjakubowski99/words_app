<?php

declare(strict_types=1);

namespace App\Http\OpenApi\Responses;

use OpenApi\Attributes as OAT;

#[OAT\Response(
    response: 'bad_gateway',
    description: 'Bad gateway response.',
    content: new OAT\JsonContent(
        properties: [
            new OAT\Property(
                property: 'message',
                type: 'string',
                example: 'Bad gateway.'
            ),
        ],
        type: 'object',
    ),
)]
class BadGatewayResponse {}
