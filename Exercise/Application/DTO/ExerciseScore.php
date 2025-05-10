<?php

declare(strict_types=1);

namespace Exercise\Application\DTO;

use Shared\Exercise\IExerciseScore;
use Shared\Utils\ValueObjects\ExerciseEntryId;

class ExerciseScore implements IExerciseScore
{
    public function __construct(
        private ExerciseEntryId $exercise_entry_id,
        private float $score,
    ) {}

    public function getExerciseEntryId(): ExerciseEntryId
    {
        return $this->exercise_entry_id;
    }

    public function getScore(): float
    {
        return $this->score;
    }
}
