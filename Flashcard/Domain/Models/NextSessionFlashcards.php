<?php

declare(strict_types=1);

namespace Flashcard\Domain\Models;

use Flashcard\Domain\ValueObjects\SessionId;
use Flashcard\Domain\Exceptions\InvalidNextSessionFlashcards;
use Flashcard\Domain\Exceptions\TooManySessionFlashcardsException;

class NextSessionFlashcards
{
    public const UNRATED_LIMIT = 5;

    private array $next_session_flashcards = [];

    public function __construct(
        private SessionId $session_id,
        private Owner $owner,
        private ?Category $category,
        private int $current_session_flashcards_count,
        private int $unrated_count,
        private int $max_flashcards_count,
    ) {
        if (!$this->isValid()) {
            throw new InvalidNextSessionFlashcards(
                "Cannot generate next session flashcards for session: {$this->session_id->getValue()}"
            );
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

    public function getUnratedCount(): int
    {
        return $this->unrated_count;
    }

    public function getCurrentSessionFlashcardsCount(): int
    {
        return $this->current_session_flashcards_count;
    }

    public function hasCategory(): bool
    {
        return $this->category !== null;
    }

    public function getCategory(): Category
    {
        return $this->category;
    }

    public function isValid(): bool
    {
        if ($this->unrated_count > self::UNRATED_LIMIT) {
            return false;
        }

        return $this->current_session_flashcards_count <= $this->max_flashcards_count;
    }

    public function canAddNext(): bool
    {
        if ($this->unrated_count + 1 > self::UNRATED_LIMIT) {
            return false;
        }

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
        ++$this->unrated_count;
    }

    public function getNextFlashcards(): array
    {
        return $this->next_session_flashcards;
    }
}
