<?php

declare(strict_types=1);

namespace Flashcard\Domain\Models;

use Flashcard\Domain\Contracts\ICategory;
use Flashcard\Domain\ValueObjects\SessionId;
use Flashcard\Domain\Exceptions\TooManySessionFlashcardsException;

class NextSessionFlashcards
{
    private array $next_session_flashcards = [];

    public function __construct(
        private SessionId $session_id,
        private Owner $owner,
        private ICategory $category,
        private int $current_session_flashcards_count,
        private int $max_flashcards_count,
    ) {
        if ($this->current_session_flashcards_count > $this->max_flashcards_count) {
            throw new TooManySessionFlashcardsException();
        }
    }

    public function getSessionId(): SessionId
    {
        return $this->session_id;
    }

    public function getOwner(): Owner
    {
        return $this->owner;
    }

    public function getCategory(): ICategory
    {
        return $this->category;
    }

    public function canAddNext(): bool
    {
        return $this->current_session_flashcards_count + 1 <= $this->max_flashcards_count;
    }

    public function addNext(Flashcard $flashcard): void
    {
        if (!$this->canAddNext()) {
            throw new TooManySessionFlashcardsException();
        }

        $this->next_session_flashcards[] = new NextSessionFlashcard(
            $flashcard->getId()
        );
        ++$this->current_session_flashcards_count;
    }

    public function getNextFlashcards(): array
    {
        return $this->next_session_flashcards;
    }
}
