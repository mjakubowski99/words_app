<?php

declare(strict_types=1);

namespace Exercise\Application\Repositories\ExerciseRead;

use Shared\Utils\ValueObjects\ExerciseEntryId;
use Shared\Exercise\Exercises\IWordMatchExerciseRead;

interface IWordMatchExerciseReadRepository
{
    public function findByEntryId(ExerciseEntryId $id): IWordMatchExerciseRead;
}
