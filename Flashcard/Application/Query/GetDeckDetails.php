<?php

declare(strict_types=1);

namespace Flashcard\Application\Query;

use Flashcard\Domain\ValueObjects\FlashcardDeckId;
use Flashcard\Application\ReadModels\DeckDetailsRead;
use Flashcard\Application\Repository\IFlashcardDeckReadRepository;

class GetDeckDetails
{
    public function __construct(
        private IFlashcardDeckReadRepository $repository,
    ) {}

    public function get(FlashcardDeckId $id, ?string $search, int $page, int $per_page): DeckDetailsRead
    {
        return $this->repository->findDetails($id, $search, $page, $per_page);
    }
}
