<?php

declare(strict_types=1);

namespace Shared\Enum;

enum GeneralRatingType: string
{
    case NEW = 'NEW';
    case GOOD = 'GOOD';
    case VERY_GOOD = 'VERY_GOOD';
    case WEAK = 'WEAK';
    case UNKNOWN = 'UNKNOWN';
}
