<?php

declare(strict_types=1);

namespace Flashcard\Application\Repository;

use Shared\Utils\ValueObjects\UserId;
use Flashcard\Application\ReadModels\UserFlashcardsRead;
use Flashcard\Application\ReadModels\RatingStatsReadCollection;

interface IFlashcardReadRepository
{
    public function findStatsByUser(UserId $user_id): RatingStatsReadCollection;

    public function findByUser(UserId $user_id, ?string $search, int $page, int $per_page): UserFlashcardsRead;
}
