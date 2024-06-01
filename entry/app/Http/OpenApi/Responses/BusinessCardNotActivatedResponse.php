<?php

declare(strict_types=1);

namespace App\Http\OpenApi\Responses;

use OpenApi\Attributes as OAT;

#[OAT\Response(
    response: 'business_card_not_activate',
    description: 'Business card not activated response.',
    content: new OAT\JsonContent(
        properties: [
            new OAT\Property(
                property: 'message',
                type: 'string',
                example: 'The given code does not exist.',
            ),
        ],
        type: 'object',
    ),
)]
class BusinessCardNotActivatedResponse {}
