<?php

declare(strict_types=1);

namespace Flashcard\Domain\Models;

enum Rating: int
{
    case UNKNOWN = 0;
    case WEAK = 1;
    case GOOD = 2;
    case VERY_GOOD = 3;

    public static function maxLeitnerLevel(): int
    {
        return self::maxRating();
    }

    public static function maxRating(): int
    {
        return max(array_map(fn (Rating $rating) => $rating->value, self::cases()));
    }

    public function leitnerLevel(): int
    {
        return $this->value;
    }

    public function fromScore(float $score): self
    {
        if ($score <= 0.3) {
            return self::UNKNOWN;
        }
        if ($score <= 0.5) {
            return self::WEAK;
        } elseif ($score <= 0.85) {
            return self::GOOD;
        } else {
            return self::VERY_GOOD;
        }
    }
}
