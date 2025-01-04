<?php

declare(strict_types=1);

namespace Flashcard\Application\Query;

use Shared\Utils\ValueObjects\UserId;
use Flashcard\Application\Repository\IFlashcardReadRepository;
use Flashcard\Application\ReadModels\RatingStatsReadCollection;

class GetUserRatingStats
{
    public function __construct(private readonly IFlashcardReadRepository $repository) {}

    public function get(UserId $user_id): RatingStatsReadCollection
    {
        return $this->repository->findStatsByUser($user_id);
    }
}
