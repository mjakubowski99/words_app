<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Repositories\Postgres;

use Flashcard\Domain\Models\Owner;
use Flashcard\Domain\ValueObjects\FlashcardDeckId;
use Flashcard\Application\ReadModels\DeckDetailsRead;
use Flashcard\Application\Repository\IFlashcardDeckReadRepository;
use Flashcard\Infrastructure\Mappers\Postgres\FlashcardCategoryReadMapper;

class FlashcardDeckReadRepository implements IFlashcardDeckReadRepository
{
    public function __construct(
        private readonly FlashcardCategoryReadMapper $mapper
    ) {}

    public function findDetails(FlashcardDeckId $id, ?string $search, int $page, int $per_page): DeckDetailsRead
    {
        return $this->mapper->findDetails($id, $search, $page, $per_page);
    }

    public function getByOwner(Owner $owner, ?string $search, int $page, int $per_page): array
    {
        return $this->mapper->getByOwner($owner, $search, $page, $per_page);
    }
}
