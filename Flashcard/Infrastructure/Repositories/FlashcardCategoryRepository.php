<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Repositories;

use Flashcard\Domain\Models\Owner;
use Shared\Enum\FlashcardCategoryType;
use Flashcard\Domain\Contracts\ICategory;
use Flashcard\Domain\Models\MainCategory;
use Flashcard\Domain\ValueObjects\CategoryId;
use Flashcard\Infrastructure\Mappers\FlashcardCategoryMapper;
use Flashcard\Domain\Exceptions\CannotCreateCategoryException;
use Flashcard\Application\Repository\IFlashcardCategoryRepository;

class FlashcardCategoryRepository implements IFlashcardCategoryRepository
{
    public function __construct(
        private readonly FlashcardCategoryMapper $mapper
    ) {}

    public function findById(CategoryId $id): ICategory
    {
        $main_category = new MainCategory();

        if ($main_category->getId()->getValue() === $id->getValue()) {
            return $main_category;
        }

        return $this->mapper->findById($id);
    }

    public function searchByName(Owner $owner, string $name): ?ICategory
    {
        return $this->mapper->searchByName($owner, $name);
    }

    public function createCategory(ICategory $category): ICategory
    {
        if ($category->getCategoryType() === FlashcardCategoryType::GENERAL) {
            throw new CannotCreateCategoryException();
        }

        $category_id = $this->mapper->create($category);

        return $this->findById($category_id);
    }

    /** @return ICategory[] */
    public function getByOwner(Owner $owner, int $page, int $per_page): array
    {
        return $this->mapper->getByOwner($owner, $page, $per_page);
    }

    public function removeCategory(ICategory $category): void
    {
        $this->mapper->remove($category->getId());
    }
}
