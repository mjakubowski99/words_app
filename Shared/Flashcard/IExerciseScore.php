<?php

declare(strict_types=1);

namespace Shared\Flashcard;

interface IExerciseScore
{
    public function getExerciseEntryId(): int;

    public function getScore(): float;
}
