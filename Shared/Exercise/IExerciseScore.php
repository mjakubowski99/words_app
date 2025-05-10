<?php

declare(strict_types=1);

namespace Shared\Exercise;

use Shared\Utils\ValueObjects\ExerciseEntryId;

interface IExerciseScore
{
    public function getExerciseEntryId(): ExerciseEntryId;

    public function getScore(): float;
}
