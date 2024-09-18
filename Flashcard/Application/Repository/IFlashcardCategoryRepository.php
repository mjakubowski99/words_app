<?php

declare(strict_types=1);

namespace Flashcard\Application\Repository;

use Flashcard\Domain\Models\Owner;
use Flashcard\Domain\Models\Category;
use Flashcard\Domain\Contracts\ICategory;
use Flashcard\Domain\ValueObjects\CategoryId;

interface IFlashcardCategoryRepository
{
    public function findById(CategoryId $id): ICategory;

    /** @return Category[] */
    public function getByOwner(Owner $owner, int $page, int $per_page): array;

    public function createCategory(ICategory $category): ICategory;

    public function removeCategory(ICategory $category): void;
}
