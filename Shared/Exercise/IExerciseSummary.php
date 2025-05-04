<?php

declare(strict_types=1);

namespace Shared\Exercise;

use Shared\Enum\ExerciseType;
use Shared\Utils\ValueObjects\ExerciseId;

interface IExerciseSummary
{
    public function getId(): ExerciseId;

    public function getExerciseType(): ExerciseType;

    public function getSessionFlashcardId(): int;
}
