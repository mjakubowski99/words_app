<?php

declare(strict_types=1);

namespace Shared\Exercise;

use Shared\Utils\ValueObjects\ExerciseEntryId;

interface IFlashcardExercise
{
    public function getFlashcardId(): int;
    public function getExerciseEntryId(): ExerciseEntryId;
}
