<?php

declare(strict_types=1);

namespace Flashcard\Application\Repository;

use Flashcard\Domain\Models\Owner;
use Flashcard\Domain\Models\Category;
use Flashcard\Domain\ValueObjects\CategoryId;

interface IFlashcardCategoryRepository
{
    public function findById(CategoryId $id): Category;

    public function searchByName(Owner $owner, string $name): ?Category;

    /** @return Category[] */
    public function getByOwner(Owner $owner, int $page, int $per_page): array;

    public function createCategory(Category $category): Category;

    public function removeCategory(Category $category): void;
}
