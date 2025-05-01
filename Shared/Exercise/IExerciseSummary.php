<?php

namespace Shared\Exercise;

use Shared\Enum\ExerciseType;
use Shared\Utils\ValueObjects\ExerciseId;

interface IExerciseSummary
{
    public function getId(): ExerciseId;
    public function getExerciseType(): ExerciseType;
}