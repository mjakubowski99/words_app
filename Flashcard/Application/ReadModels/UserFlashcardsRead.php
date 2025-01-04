<?php

declare(strict_types=1);

namespace Flashcard\Application\ReadModels;

use Shared\Utils\ValueObjects\UserId;

class UserFlashcardsRead
{
    public function __construct(
        private UserId $user_id,
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

    public function getUserId(): UserId
    {
        return $this->user_id;
    }
}
