<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Repositories;

use Shared\Utils\ValueObjects\UserId;
use Flashcard\Domain\Models\CategoryId;
use Flashcard\Domain\Models\FlashcardCategory;
use Flashcard\Infrastructure\Mappers\FlashcardCategoryMapper;
use Flashcard\Domain\Repositories\IFlashcardCategoryRepository;

class FlashcardCategoryRepository implements IFlashcardCategoryRepository
{
    public function __construct(
        private readonly FlashcardCategoryMapper $mapper
    ) {}

    public function findById(CategoryId $id): FlashcardCategory
    {
        return $this->mapper->findById($id);
    }

    /** @return FlashcardCategory[] */
    public function getByUser(UserId $user_id, int $page, int $per_page): array
    {
        return $this->mapper->getByUser($user_id, $page, $per_page);
    }

    public function findMain(): FlashcardCategory
    {
        return $this->mapper->findByTag(FlashcardCategory::MAIN);
    }

    public function createCategory(FlashcardCategory $category): CategoryId
    {
        return $this->mapper->create($category);
    }
}
