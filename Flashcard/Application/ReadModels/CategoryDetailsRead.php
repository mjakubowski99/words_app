<?php

namespace Flashcard\Application\ReadModels;

use Flashcard\Domain\ValueObjects\CategoryId;

class CategoryDetailsRead
{
    public function __construct(private CategoryId $id, private string $name, private array $flashcards) {}

    public function getId(): CategoryId
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /** @return FlashcardRead[] */
    public function getFlashcards(): array
    {
        return $this->flashcards;
    }
}