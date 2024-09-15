<?php

declare(strict_types=1);

namespace Flashcard\Application\Query;

use Flashcard\Application\DTO\UserFlashcardCategoryDTO;
use Flashcard\Application\Repository\IFlashcardCategoryRepository;
use Flashcard\Domain\Models\Category;
use Flashcard\Domain\Models\Owner;

class GetUserCategories
{
    public function __construct(private IFlashcardCategoryRepository $repository) {}

    /** @return UserFlashcardCategoryDTO[] */
    public function handle(Owner $owner, int $page, int $per_page): array
    {
        return array_map(function (Category $category) {
            return new UserFlashcardCategoryDTO($category->getId(), $category->getName());
        }, $this->repository->getByOwner($owner, $page, $per_page));
    }
}
