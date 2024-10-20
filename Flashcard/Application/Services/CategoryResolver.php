<?php

declare(strict_types=1);

namespace Flashcard\Application\Services;

use Flashcard\Domain\Models\Owner;
use Flashcard\Domain\Models\Category;
use Flashcard\Application\DTO\ResolvedCategory;
use Flashcard\Application\Repository\IFlashcardCategoryRepository;

class CategoryResolver
{
    public function __construct(
        private IFlashcardCategoryRepository $repository
    ) {}

    public function resolve(Owner $owner, string $name): ResolvedCategory
    {
        $existing_category = $this->repository->searchByName($owner, $name);

        if ($existing_category) {
            return new ResolvedCategory(true, $existing_category);
        }

        $category = new Category(
            $owner,
            mb_strtolower($name),
            $name
        );
        $category = $this->repository->createCategory($category);

        return new ResolvedCategory(false, $category);
    }
}
