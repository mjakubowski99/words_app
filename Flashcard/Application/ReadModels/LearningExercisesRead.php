<?php

declare(strict_types=1);

namespace Flashcard\Application\ReadModels;

class LearningExercisesRead
{
    public function __construct(
        private SessionFlashcardsRead $session_flashcards_read,
        private array $exercise_summaries,
    ) {}

    public function getSessionFlashcards(): SessionFlashcardsRead
    {
        return $this->session_flashcards_read;
    }

    public function getExerciseSummaries(): array
    {
        return $this->exercise_summaries;
    }
}
