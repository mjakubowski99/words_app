<?php

namespace Exercise\Application\Repositories;

use Shared\Exercise\Exercises\IWordMatchExerciseRead;
use Shared\Utils\ValueObjects\ExerciseEntryId;
use Shared\Utils\ValueObjects\ExerciseId;

interface IWordMatchExerciseReadRepository
{
    public function findByEntryId(ExerciseEntryId $id): IWordMatchExerciseRead;
}