<?php

declare(strict_types=1);

namespace Flashcard\Application\Query;

use Flashcard\Application\DTO\MainFlashcardCategoryDTO;
use Flashcard\Domain\Models\MainCategory;

class GetMainCategory
{
    public function __construct() {}

    public function handle(): MainFlashcardCategoryDTO
    {
        $category = new MainCategory();

        return new MainFlashcardCategoryDTO(
            $category->getId(),
            $category->getName()
        );
    }
}
