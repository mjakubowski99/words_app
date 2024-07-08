<?php

declare(strict_types=1);

namespace App\Http\OpenApi;

use OpenApi\Attributes as OAT;

#[OAT\Tag(
    name: Tags::USER,
    description: 'User part of application.'
)]
class Tags
{
    public const USER = 'User';
}
