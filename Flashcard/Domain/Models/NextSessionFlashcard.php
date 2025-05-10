<?php

declare(strict_types=1);

namespace Flashcard\Domain\Models;

use Flashcard\Domain\ValueObjects\FlashcardId;
use Shared\Enum\ExerciseType;
use Shared\Utils\ValueObjects\ExerciseEntryId;

class NextSessionFlashcard
{
    private ?ExerciseEntryId $exercise_entry_id = null;
    private ?ExerciseType $type = null;

    public function __construct(
        private readonly FlashcardId $id,
    ) {}

    public function getFlashcardId(): FlashcardId
    {
        return $this->id;
    }

    public function getExerciseType(): ?ExerciseType
    {
        return $this->type;
    }

    public function getExerciseEntryId(): ExerciseEntryId
    {
        return $this->exercise_entry_id;
    }

    public function hasExercise(): bool
    {
        return $this->exercise_entry_id !== null;
    }

    public function setExercise(ExerciseEntryId $id, ExerciseType $type): void
    {
        $this->exercise_entry_id = $id;
        $this->type = $type;
    }
}
