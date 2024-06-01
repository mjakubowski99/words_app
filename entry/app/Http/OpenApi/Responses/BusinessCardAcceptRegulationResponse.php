<?php

declare(strict_types=1);

namespace App\Http\OpenApi\Responses;

use OpenApi\Attributes as OAT;

#[OAT\Response(
    response: 'business_card_accept_regulation',
    description: 'Business card accept regulation response.',
    content: new OAT\JsonContent(
        properties: [
            new OAT\Property(
                property: 'message',
                type: 'string',
                example: 'Regulations have been accepted',
            ),
        ],
        type: 'object',
    ),
)]
class BusinessCardAcceptRegulationResponse {}
