<?php

declare(strict_types=1);

namespace Flashcard\Application\ReadModels;

use Shared\Enum\GeneralRatingType;

class RatingStatsRead
{
    public function __construct(
        private GeneralRating $rating,
        private float $rating_percentage
    ) {
        if ($this->rating->getValue() === GeneralRatingType::NEW) {
            throw new \UnexpectedValueException("{$this->rating->getValue()->value} is not allowed enum value for this object");
        }
    }

    public function getRating(): GeneralRating
    {
        return $this->rating;
    }

    public function getRatingPercentage(): float
    {
        return $this->rating_percentage;
    }
}
