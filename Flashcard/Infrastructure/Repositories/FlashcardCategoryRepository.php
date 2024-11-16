<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Repositories;

use Flashcard\Domain\Models\Owner;
use Flashcard\Domain\Models\Category;
use Flashcard\Domain\ValueObjects\CategoryId;
use Flashcard\Infrastructure\Mappers\FlashcardCategoryMapper;
use Flashcard\Application\Repository\IFlashcardCategoryRepository;

class FlashcardCategoryRepository implements IFlashcardCategoryRepository
{
    public function __construct(
        private readonly FlashcardCategoryMapper $mapper
    ) {}

    public function findById(CategoryId $id): Category
    {
        return $this->mapper->findById($id);
    }

    public function searchByName(Owner $owner, string $name): ?Category
    {
        return $this->mapper->searchByName($owner, $name);
    }

    public function createCategory(Category $category): Category
    {
        $category_id = $this->mapper->create($category);

        return $this->findById($category_id);
    }

    public function updateCategory(Category $category): void
    {
        $this->mapper->update($category);
    }

    /** @return Category[] */
    public function getByOwner(Owner $owner, int $page, int $per_page): array
    {
        return $this->mapper->getByOwner($owner, $page, $per_page);
    }

    public function removeCategory(Category $category): void
    {
        $this->mapper->remove($category->getId());
    }
}
