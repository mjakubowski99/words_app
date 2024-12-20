<?php

declare(strict_types=1);

namespace Flashcard\Application\ReadModels;

class RatingStatsReadCollection
{
    public function __construct(
        private array $rating_stats_read
    ) {
        $this->validate();
    }

    /** @return RatingStatsRead[] */
    public function getRatingStats(): array
    {
        return $this->rating_stats_read;
    }

    private function validate(): void
    {
        foreach ($this->rating_stats_read as $rating_stats) {
            if (!$rating_stats instanceof RatingStatsRead) {
                throw new \UnexpectedValueException('Rating stats array must be array of: ' . RatingStatsRead::class);
            }
        }
    }
}
