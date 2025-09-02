<?php

declare(strict_types=1);

namespace Flashcard\Application\ReadModels;

use Shared\Enum\GeneralRatingType;
use Flashcard\Domain\Models\Rating;

class GeneralRating
{
    public const MAP = [
        Rating::GOOD->value => GeneralRatingType::GOOD,
        Rating::VERY_GOOD->value => GeneralRatingType::VERY_GOOD,
        Rating::UNKNOWN->value => GeneralRatingType::UNKNOWN,
        Rating::WEAK->value => GeneralRatingType::WEAK,
    ];

    private GeneralRatingType $value;

    public function __construct(?int $value)
    {
        if (is_null($value)) {
            $this->value = GeneralRatingType::NEW;
        } else {
            $this->value = self::MAP[$value];
        }
    }

    public function getValue(): GeneralRatingType
    {
        return $this->value;
    }

    public function toScore(): float
    {
        return match ($this->value) {
            GeneralRatingType::NEW => 0.0,
            GeneralRatingType::WEAK => 25.0,
            GeneralRatingType::UNKNOWN => 50.0,
            GeneralRatingType::GOOD => 75.0,
            GeneralRatingType::VERY_GOOD => 100.0,
        };
    }
}
