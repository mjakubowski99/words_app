<?php

declare(strict_types=1);

namespace App\Http\OpenApi\Responses;

use OpenApi\Attributes as OAT;

#[OAT\Response(
    response: 'coupon_not_found',
    description: 'Coupon not found',
    content: new OAT\JsonContent(
        properties: [
            new OAT\Property(
                property: 'message',
                type: 'string',
                example: 'This coupon is not set to user',
            ),
        ],
        type: 'object',
    ),
)]
class CouponNotFound {}
