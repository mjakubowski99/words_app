<?php

declare(strict_types=1);

namespace Shared\Exercise\Exercises;

use Shared\Utils\ValueObjects\ExerciseEntryId;

interface IExerciseReadFacade
{
    public function getExerciseScoreSum(array $exercise_entry_ids): float;

    public function getUnscrambleWordExercise(ExerciseEntryId $id): IUnscrambleWordExerciseRead;

    public function getWordMatchExercise(ExerciseEntryId $id): IWordMatchExerciseRead;
}
