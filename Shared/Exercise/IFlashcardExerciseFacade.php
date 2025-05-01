<?php

namespace Shared\Exercise;

use Shared\Enum\ExerciseType;
use Shared\Flashcard\ISessionFlashcardSummary;
use Shared\Utils\ValueObjects\UserId;

interface IFlashcardExerciseFacade
{
    public function getRequiredExerciseCount(ExerciseType $type): int;

    /** @param ISessionFlashcardSummary[] $session_flashcard_summaries */
    public function makeExercise(array $session_flashcard_summaries, UserId $user_id, ExerciseType $type): IExerciseSummary;

    public function getUnscrambleWordExercise(int $exercise_id): IUnscrambleWordExerciseRead;
}