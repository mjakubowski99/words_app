<?php

declare(strict_types=1);

namespace App\Http\OpenApi\Responses;

use OpenApi\Attributes as OAT;

#[OAT\Response(
    response: 'user_points_response',
    description: 'User points response',
    content: new OAT\JsonContent(
        properties: [
            new OAT\Property(
                property: 'value',
                type: 'integer',
                example: 50,
            ),
        ],
        type: 'object',
    ),
)]
class UserPointsResponse {}
