<?php

declare(strict_types=1);

namespace App\Http\OpenApi;

use OpenApi\Attributes as OAT;

#[OAT\Tag(
    name: Tags::USER,
    description: 'User part of application.'
)]
#[OAT\Tag(
    name: Tags::FLASHCARD,
    description: 'Flashcard part of application.'
)]
#[OAT\Tag(
    name: Tags::V2,
    description: 'V2 version of the api'
)]
class Tags
{
    public const USER = 'User';
    public const FLASHCARD = 'Flashcard';
    public const V2 = 'V2';
    public const EXERCISE = 'Exercise';
}
