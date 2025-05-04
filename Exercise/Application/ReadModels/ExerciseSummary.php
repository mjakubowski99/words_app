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
        private ExerciseType $exercise_type,
        private int $session_flashcard_id,
    ) {}

    public function getId(): ExerciseId
    {
        return $this->exercise_id;
    }

    public function getExerciseType(): ExerciseType
    {
        return $this->exercise_type;
    }

    public function getSessionFlashcardId(): int
    {
        return $this->session_flashcard_id;
    }
}
