<?php

declare(strict_types=1);

namespace Exercise\Application\Repositories\ExerciseRead;

use Shared\Exercise\Exercises\IUnscrambleWordExerciseRead;
use Shared\Utils\ValueObjects\ExerciseEntryId;

interface IUnscrambleWordExerciseReadRepository
{
    public function findByEntryId(ExerciseEntryId $id): IUnscrambleWordExerciseRead;
}
