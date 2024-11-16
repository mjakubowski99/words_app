<?php

declare(strict_types=1);

namespace Flashcard\Application\DTO;

use Flashcard\Domain\ValueObjects\FlashcardDeckId;

class MainFlashcardCategoryDTO
{
    public function __construct(
        private readonly FlashcardDeckId $id,
        private readonly string $name,
    ) {}

    public function getId(): FlashcardDeckId
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
