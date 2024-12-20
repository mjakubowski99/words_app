<?php

declare(strict_types=1);

namespace Flashcard\Application\Query;

use Flashcard\Domain\Models\Owner;
use Flashcard\Application\Repository\IFlashcardReadRepository;
use Flashcard\Application\ReadModels\RatingStatsReadCollection;

class GetUserRatingStats
{
    public function __construct(private readonly IFlashcardReadRepository $repository) {}

    public function get(Owner $owner): RatingStatsReadCollection
    {
        return $this->repository->findStatsByUser($owner);
    }
}
