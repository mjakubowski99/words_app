<?php

namespace Shared\Exercise\ExerciseTypes;

use Shared\Enum\ExerciseType;
use Shared\Exercise\IFlashcardExercise;
use Shared\Flashcard\ISessionFlashcardSummary;
use Shared\Utils\ValueObjects\ExerciseId;
use Shared\Utils\ValueObjects\UserId;

interface IExerciseTypeFacade
{
    /**
     * @param ISessionFlashcardSummary[] $session_flashcard_summaries
     * @return IFlashcardExercise[]
     */
    public function buildExercise(array $session_flashcard_summaries, UserId $user_id, ExerciseType $type): array;

    public function getUnscrambleWordExercise(ExerciseId $id): IUnscrambleWordExerciseRead;
}