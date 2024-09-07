<?php

declare(strict_types=1);

namespace Flashcard\Application\DTO;

use Flashcard\Domain\Models\CategoryId;

class UserFlashcardCategoryDTO
{
    public function __construct(
        private readonly CategoryId $id,
        private readonly string $name,
    ) {}

    public function getId(): CategoryId
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
