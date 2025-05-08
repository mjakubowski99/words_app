<?php

declare(strict_types=1);

namespace Flashcard\Application\ReadModels;

use Shared\Exercise\IExerciseSummary;
use Flashcard\Domain\ValueObjects\SessionId;

class SessionFlashcardsRead
{
    /**
     * @property SessionFlashcardRead[] $session_flashcards
     */
    public function __construct(
        private SessionId $id,
        private readonly int $progress,
        private readonly int $cards_per_session,
        private readonly bool $is_finished,
        private array $session_flashcards,
        private array $exercise_entry_ids,
    ) {}

    public function getSessionId(): SessionId
    {
        return $this->id;
    }

    public function getProgress(): int
    {
        return $this->progress;
    }

    public function getCardsPerSession(): int
    {
        return $this->cards_per_session;
    }

    public function getIsFinished(): bool
    {
        return $this->is_finished;
    }

    /** @return SessionFlashcardRead[] */
    public function getSessionFlashcards(): array
    {
        return $this->session_flashcards;
    }

    public function getExerciseEntryIds(): array
    {
        return $this->exercise_entry_ids;
    }
}
