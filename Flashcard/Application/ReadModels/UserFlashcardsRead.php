<?php

declare(strict_types=1);

namespace Flashcard\Application\ReadModels;

use Flashcard\Domain\Models\Owner;

class UserFlashcardsRead
{
    public function __construct(
        private Owner $owner,
        private array $flashcards,
        private int $page,
        private int $per_page,
        private int $count,
    ) {}

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

    public function getCount(): int
    {
        return $this->count;
    }

    public function getOwner(): Owner
    {
        return $this->owner;
    }
}
