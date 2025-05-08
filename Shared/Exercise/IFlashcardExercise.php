<?php

declare(strict_types=1);

namespace Shared\Exercise;

interface IFlashcardExercise
{
    public function getFlashcardId(): int;
    public function getExerciseEntryId(): int;
}
