<?php

declare(strict_types=1);

namespace Flashcard\Domain\Repositories;

use Flashcard\Domain\Models\CategoryId;
use Flashcard\Domain\Models\FlashcardCategory;

interface IFlashcardCategoryRepository
{
    public function findById(CategoryId $id): FlashcardCategory;

    public function findMain(): FlashcardCategory;

    public function createCategory(FlashcardCategory $category): CategoryId;
}