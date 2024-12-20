<?php

declare(strict_types=1);

namespace Flashcard\Application\Query;

use Flashcard\Domain\Models\Owner;
use Flashcard\Application\ReadModels\UserFlashcardsRead;
use Flashcard\Application\Repository\IFlashcardReadRepository;

class GetUserFlashcards
{
    public function __construct(
        private IFlashcardReadRepository $repository
    ) {}

    public function get(Owner $owner, ?string $search, int $page, int $per_page): UserFlashcardsRead
    {
        return $this->repository->findByUser($owner, $search, $page, $per_page);
    }
}
