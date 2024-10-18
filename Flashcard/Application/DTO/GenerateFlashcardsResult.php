<?php

declare(strict_types=1);

namespace Flashcard\Application\DTO;

use Flashcard\Domain\ValueObjects\CategoryId;

class GenerateFlashcardsResult
{
    public function __construct(
        private readonly CategoryId $id,
        private int $generated_count,
        private readonly bool $merged_to_existing_category
    ) {}

    public function getCategoryId(): CategoryId
    {
        return $this->id;
    }

    public function getGeneratedCount(): int
    {
        return $this->generated_count;
    }

    public function getMergedToExistingCategory(): bool
    {
        return $this->merged_to_existing_category;
    }
}
