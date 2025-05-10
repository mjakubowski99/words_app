<?php

namespace Shared\Exercise\Exercises;

use Shared\Utils\ValueObjects\ExerciseEntryId;

interface IExerciseReadFacade
{
    public function getUnscrambleWordExercise(ExerciseEntryId $id): IUnscrambleWordExerciseRead;
}