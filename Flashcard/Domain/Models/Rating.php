<?php

declare(strict_types=1);

namespace Flashcard\Domain\Models;

enum Rating: int
{
    case UNKNOWN = 0;
    case WEAK = 1;
    case GOOD = 2;
    case VERY_GOOD = 3;

    public static function maxRating(): int
    {
        return max(array_map(fn (Rating $rating) => $rating->value, self::cases()));
    }
}
