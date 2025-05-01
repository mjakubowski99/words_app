<?php

namespace Exercise\Application\Facades;

use Exercise\Application\Services\FlashcardExerciseFactory;
use Shared\Enum\ExerciseType;
use Shared\Exercise\IExerciseSummary;
use Shared\Flashcard\ISessionFlashcardSummary;
use Shared\Utils\ValueObjects\UserId;

class ExerciseFacade
{
    public function __construct(
        private FlashcardExerciseFactory $exercise_factory,
    ) {}

    /** @param ISessionFlashcardSummary[] $session_flashcards_summary */
    public function makeExercise(array $session_flashcards_summary, UserId $user_id, ExerciseType $type): IExerciseSummary
    {
        return $this->exercise_factory->makeExercise($session_flashcards_summary, $user_id, $type);
    }
}