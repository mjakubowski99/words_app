<?php

declare(strict_types=1);

namespace Flashcard\Domain\Repositories;

use Shared\Utils\ValueObjects\UserId;
use Flashcard\Domain\Models\CategoryId;
use Flashcard\Domain\Models\FlashcardCategory;

interface IFlashcardCategoryRepository
{
    public function findById(CategoryId $id): FlashcardCategory;

    public function findMain(): FlashcardCategory;

    /** @return FlashcardCategory[] */
    public function getByUser(UserId $user_id, int $page, int $per_page): array;

    public function createCategory(FlashcardCategory $category): CategoryId;
}
