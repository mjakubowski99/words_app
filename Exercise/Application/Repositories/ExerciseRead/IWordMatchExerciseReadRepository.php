<?php

declare(strict_types=1);

namespace Exercise\Application\Repositories\ExerciseRead;

use Shared\Exercise\Exercises\IWordMatchExerciseRead;
use Shared\Utils\ValueObjects\ExerciseEntryId;

interface IWordMatchExerciseReadRepository
{
    public function findByEntryId(ExerciseEntryId $id): IWordMatchExerciseRead;
}
