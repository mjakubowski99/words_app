<?php

declare(strict_types=1);

namespace Shared\Enum;

use OpenApi\Attributes as OAT;

#[OAT\Schema(
    schema: 'FlashcardOwnerType',
    description: 'Flashcard owner type',
    enum: [
        FlashcardOwnerType::ADMIN->value,
        FlashcardOwnerType::USER->value,
    ],
    example: FlashcardOwnerType::USER->value,
    nullable: true
)]
enum FlashcardOwnerType: string
{
    case USER = 'user';
    case ADMIN = 'admin';
}
