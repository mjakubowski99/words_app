<?php

declare(strict_types=1);

namespace Flashcard\Application\DTO;

use Flashcard\Domain\ValueObjects\CategoryId;

class GenerateFlashcardsResult
{
    public function __construct(
        private readonly CategoryId $id,
        private readonly bool $merged_to_existing_category
    ) {}

    public function getCategoryId(): CategoryId
    {
        return $this->id;
    }

    public function getMergedToExistingCategory(): bool
    {
        return $this->merged_to_existing_category;
    }
}
