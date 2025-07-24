<?php

declare(strict_types=1);

namespace Shared\Exercise\Exercises;

use Shared\Utils\ValueObjects\ExerciseId;

interface IWordMatchExerciseRead
{
    public function getExerciseId(): ExerciseId;

    public function isStory(): bool;

    /** @return IWordMatchExerciseReadEntry[] */
    public function getEntries(): array;
    /** @return string[] */
    public function getAnswerOptions(): array;
}
