<?php

namespace Flashcard\Application\Query;

use Shared\Exercise\IFlashcardExerciseFacade;
use Shared\Exercise\IUnscrambleWordExerciseRead;

class GetNextExerciseHandler
{
    public function __construct(
        private IFlashcardExerciseFacade $facade,
    ) {}

    public function getUnscrambleWordExercise(int $exercise_id): IUnscrambleWordExerciseRead
    {
        return $this->facade->getUnscrambleWordExercise($exercise_id);
    }
}