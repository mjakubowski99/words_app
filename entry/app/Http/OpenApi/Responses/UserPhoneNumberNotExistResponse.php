<?php

declare(strict_types=1);

namespace App\Http\OpenApi\Responses;

use OpenApi\Attributes as OAT;

#[OAT\Response(
    response: 'user_phone_number_not_exist',
    description: 'User number does not exist',
    content: new OAT\JsonContent(
        properties: [
            new OAT\Property(
                property: 'message',
                type: 'string',
                example: 'The given phone number does not exist',
            ),
        ],
        type: 'object',
    ),
)]
class UserPhoneNumberNotExistResponse {}
