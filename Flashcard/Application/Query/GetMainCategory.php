<?php

declare(strict_types=1);

namespace Flashcard\Application\Query;

use Flashcard\Domain\Models\MainCategory;
use Flashcard\Application\DTO\MainFlashcardCategoryDTO;

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
