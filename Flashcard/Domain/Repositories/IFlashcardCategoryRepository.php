<?php

declare(strict_types=1);

namespace Flashcard\Domain\Repositories;

use Flashcard\Domain\Contracts\ICategory;
use Flashcard\Domain\Models\Owner;
use Shared\Utils\ValueObjects\UserId;
use Flashcard\Domain\Models\CategoryId;
use Flashcard\Domain\Models\Category;

interface IFlashcardCategoryRepository
{
    public function findById(CategoryId $id): ICategory;

    /** @return Category[] */
    public function getByOwner(Owner $owner, int $page, int $per_page): array;

    public function createCategory(ICategory $category): ICategory;
}
