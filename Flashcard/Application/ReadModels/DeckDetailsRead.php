<?php

declare(strict_types=1);

namespace Flashcard\Application\ReadModels;

use Shared\Enum\FlashcardOwnerType;
use Flashcard\Domain\ValueObjects\FlashcardDeckId;

class DeckDetailsRead
{
    public function __construct(
        private FlashcardDeckId $id,
        private string $name,
        private array $flashcards,
        private int $page,
        private int $per_page,
        private int $count,
        private FlashcardOwnerType $owner_type,
    ) {}

    public function getId(): FlashcardDeckId
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

    public function getPage(): int
    {
        return $this->page;
    }

    public function getPerPage(): int
    {
        return $this->per_page;
    }

    public function getFlashcardsCount(): int
    {
        return $this->count;
    }

    public function getOwnerType(): FlashcardOwnerType
    {
        return $this->owner_type;
    }
}
