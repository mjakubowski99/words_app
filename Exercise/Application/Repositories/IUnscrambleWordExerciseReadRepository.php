<?php

namespace Exercise\Application\Repositories;

use Shared\Exercise\IUnscrambleWordExerciseRead;
use Shared\Utils\ValueObjects\ExerciseId;

interface IUnscrambleWordExerciseReadRepository
{
    public function find(ExerciseId $id): IUnscrambleWordExerciseRead;
}