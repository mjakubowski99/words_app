<?php

declare(strict_types=1);

namespace Exercise\Application\Repositories;

use Shared\Utils\ValueObjects\ExerciseEntryId;
use Shared\Exercise\Exercises\IUnscrambleWordExerciseRead;

interface IUnscrambleWordExerciseReadRepository
{
    public function findByEntryId(ExerciseEntryId $id): IUnscrambleWordExerciseRead;
}
