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

    /** @param IExerciseSummary[] $exercise_summaries */
    public function removeFlashcardsBelongingToExercises(array $exercise_summaries): void
    {
        $exercise_flashcards = array_map(fn (IExerciseSummary $exercise) => $exercise->getSessionFlashcardId(), $exercise_summaries);

        $this->session_flashcards = array_filter(
            $this->getSessionFlashcards(),
            fn (SessionFlashcardRead $flashcard) => !in_array($flashcard->getId()->getValue(), $exercise_flashcards, true)
        );
    }
}
