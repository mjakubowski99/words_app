<?php

declare(strict_types=1);

namespace Shared\Enum;

enum FlashcardOwnerType: string
{
    case USER = 'user';
    case ADMIN = 'admin';
}
