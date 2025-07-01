<?php

namespace Shared\Exercise\Exercises;

use Shared\Utils\ValueObjects\ExerciseId;

interface IWordMatchExerciseRead
{
    public function getExerciseId(): ExerciseId;
    public function isStory(): bool;

    /** @return IWordMatchExerciseReadEntry[] */
    public function getEntries(): array;
}