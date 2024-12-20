<?php

declare(strict_types=1);

namespace Flashcard\Application\Repository;

use Flashcard\Domain\Models\Owner;
use Flashcard\Application\ReadModels\UserFlashcardsRead;

interface IFlashcardReadRepository
{
    public function findByUser(Owner $owner, ?string $search, int $page, int $per_page): UserFlashcardsRead;
}
