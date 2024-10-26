<?php

declare(strict_types=1);

namespace Flashcard\Application\DTO;

use Flashcard\Domain\Models\Category;

class ResolvedCategory
{
    public function __construct(
        private bool $is_existing_category,
        private Category $category
    ) {}

    public function isExistingCategory(): bool
    {
        return $this->is_existing_category;
    }

    public function getCategory(): Category
    {
        return $this->category;
    }
}
