<?php

declare(strict_types=1);

namespace Exercise\Application\Repositories;

use Shared\Utils\ValueObjects\ExerciseId;
use Shared\Exercise\IUnscrambleWordExerciseRead;

interface IUnscrambleWordExerciseReadRepository
{
    public function find(ExerciseId $id): IUnscrambleWordExerciseRead;
}
