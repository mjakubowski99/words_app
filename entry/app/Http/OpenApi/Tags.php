<?php

declare(strict_types=1);

namespace App\Http\OpenApi;

use OpenApi\Attributes as OAT;

#[OAT\Tag(
    name: Tags::AUTH,
    description: 'Auth part of application.'
)]
class Tags
{
    public const AUTH = 'Auth';
}
