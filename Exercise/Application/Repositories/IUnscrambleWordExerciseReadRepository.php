<?php

declare(strict_types=1);

namespace Exercise\Application\Repositories;

use Shared\Exercise\ExerciseTypes\IUnscrambleWordExerciseRead;
use Shared\Utils\ValueObjects\ExerciseId;

interface IUnscrambleWordExerciseReadRepository
{
    public function find(ExerciseId $id): IUnscrambleWordExerciseRead;
}
