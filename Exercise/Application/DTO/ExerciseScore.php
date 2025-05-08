<?php

declare(strict_types=1);

namespace Exercise\Application\DTO;

use Shared\Flashcard\IExerciseScore;

class ExerciseScore implements IExerciseScore
{
    public function __construct(
        private int $exercise_entry_id,
        private float $score,
    ) {}

    public function getExerciseEntryId(): int
    {
        return $this->exercise_entry_id;
    }

    public function getScore(): float
    {
        return $this->score;
    }
}
