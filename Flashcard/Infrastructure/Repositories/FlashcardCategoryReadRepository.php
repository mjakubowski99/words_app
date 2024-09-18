<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Repositories;

use Flashcard\Domain\Models\Owner;
use Flashcard\Domain\ValueObjects\CategoryId;
use Flashcard\Application\ReadModels\CategoryDetailsRead;
use Flashcard\Infrastructure\Mappers\FlashcardCategoryReadMapper;
use Flashcard\Application\Repository\IFlashcardCategoryReadRepository;

class FlashcardCategoryReadRepository implements IFlashcardCategoryReadRepository
{
    public function __construct(
        private readonly FlashcardCategoryReadMapper $mapper
    ) {}

    public function findDetails(CategoryId $id): CategoryDetailsRead
    {
        return $this->mapper->findDetails($id);
    }

    public function getByOwner(Owner $owner, int $page, int $per_page): array
    {
        return $this->mapper->getByOwner($owner, $page, $per_page);
    }
}
