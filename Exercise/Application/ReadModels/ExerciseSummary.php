<?php

declare(strict_types=1);

namespace Exercise\Application\ReadModels;

use Shared\Enum\ExerciseType;
use Shared\Exercise\IExerciseSummary;
use Shared\Utils\ValueObjects\ExerciseId;

class ExerciseSummary implements IExerciseSummary
{
    public function __construct(
        private ExerciseId $exercise_id,
        private bool $is_finished,
        private ExerciseType $exercise_type,
    ) {}

    public function getId(): ExerciseId
    {
        return $this->exercise_id;
    }

    public function isFinished(): bool
    {
        return $this->is_finished;
    }

    public function getExerciseType(): ExerciseType
    {
        return $this->exercise_type;
    }
}
