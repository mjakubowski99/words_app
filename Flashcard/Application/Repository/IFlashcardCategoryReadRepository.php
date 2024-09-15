<?php

namespace Flashcard\Application\Repository;

use Flashcard\Application\ReadModels\CategoryDetailsRead;
use Flashcard\Application\ReadModels\FlashcardRead;
use Flashcard\Domain\Models\Owner;
use Flashcard\Domain\ValueObjects\CategoryId;

interface IFlashcardCategoryReadRepository
{
    public function find(CategoryId $id): CategoryDetailsRead;

    /** @return FlashcardRead[] */
    public function getByOwner(Owner $owner, int $page, int $per_page): array;
}