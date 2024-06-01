<?php

declare(strict_types=1);

namespace App\Http\OpenApi\Responses;

use OpenApi\Attributes as OAT;

#[OAT\Response(
    response: 'not_acceptable',
    description: 'Not acceptable response.',
    content: new OAT\JsonContent(
        properties: [
            new OAT\Property(
                property: 'message',
                type: 'string',
                example: 'The user is using an obsolete version of the application'
            ),
        ],
        type: 'object',
    ),
)]
class NotAcceptableResponse {}
