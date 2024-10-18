<?php

declare(strict_types=1);

namespace Flashcard\Application\DTO;

use Flashcard\Domain\Models\Category;
use Flashcard\Domain\Contracts\ICategory;

class ResolvedCategory
{
    public function __construct(
        private bool $is_existing_category,
        private ICategory $category
    ) {}

    public function isExistingCategory(): bool
    {
        return $this->is_existing_category;
    }

    public function getCategory(): Category
    {
        if (!$this->category instanceof Category) {
            throw new \Exception('Category should be normal category');
        }

        return $this->category;
    }
}
