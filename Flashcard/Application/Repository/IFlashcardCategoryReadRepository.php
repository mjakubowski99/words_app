<?php

declare(strict_types=1);

namespace Flashcard\Application\Repository;

use Flashcard\Domain\Models\Owner;
use Flashcard\Domain\ValueObjects\CategoryId;
use Flashcard\Application\ReadModels\OwnerCategoryRead;
use Flashcard\Application\ReadModels\CategoryDetailsRead;

interface IFlashcardCategoryReadRepository
{
    public function findDetails(CategoryId $id, ?string $search, int $page, int $per_page): CategoryDetailsRead;

    /** @return OwnerCategoryRead[] */
    public function getByOwner(Owner $owner, ?string $search, int $page, int $per_page): array;
}
