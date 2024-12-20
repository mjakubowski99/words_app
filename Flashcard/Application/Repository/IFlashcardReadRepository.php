<?php

declare(strict_types=1);

namespace Flashcard\Application\Repository;

use Flashcard\Domain\Models\Owner;
use Flashcard\Application\ReadModels\UserFlashcardsRead;
use Flashcard\Application\ReadModels\RatingStatsReadCollection;

interface IFlashcardReadRepository
{
    public function findStatsByUser(Owner $owner): RatingStatsReadCollection;

    public function findByUser(Owner $owner, ?string $search, int $page, int $per_page): UserFlashcardsRead;
}
