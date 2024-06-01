<?php

declare(strict_types=1);

namespace App\Http\OpenApi\Responses;

use OpenApi\Attributes as OAT;

#[OAT\Response(
    response: 'business_card_activate',
    description: 'Business card activate response.',
    content: new OAT\JsonContent(
        properties: [
            new OAT\Property(
                property: 'message',
                type: 'string',
                example: 'The business card has been activated.',
            ),
        ],
        type: 'object',
    ),
)]
class BusinessCardActivatedResponse {}
