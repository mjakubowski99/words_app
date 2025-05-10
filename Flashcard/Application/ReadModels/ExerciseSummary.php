<?php

namespace Flashcard\Application\ReadModels;

use Shared\Enum\ExerciseType;
use Shared\Utils\ValueObjects\ExerciseEntryId;

class ExerciseSummary
{
    public function __construct(
        private ExerciseEntryId $id,
        private ExerciseType $type,
    ) {}

    public function getExerciseEntryId(): ExerciseEntryId
    {
        return $this->id;
    }

    public function getExerciseType(): ExerciseType
    {
        return $this->type;
    }
}