<?php

declare(strict_types=1);

namespace App\Http\OpenApi\Responses;

use OpenApi\Attributes as OAT;

#[OAT\Response(
    response: 'business_card_not_accept_regulation',
    description: 'Business card not accept regulation response.',
    content: new OAT\JsonContent(
        properties: [
            new OAT\Property(
                property: 'message',
                type: 'string',
                example: 'Regulations have not been accepted',
            ),
        ],
        type: 'object',
    ),
)]
class BusinessCardNotAcceptRegulationResponse {}
