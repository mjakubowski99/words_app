<?php

declare(strict_types=1);

namespace Flashcard\Domain\Models;

use Flashcard\Domain\ValueObjects\FlashcardId;

class NextSessionFlashcard
{
    private ?int $exercise_entry_id = null;

    public function __construct(
        private readonly FlashcardId $id,
    ) {}

    public function getFlashcardId(): FlashcardId
    {
        return $this->id;
    }

    public function getExerciseEntryId(): int
    {
        return $this->exercise_entry_id;
    }

    public function hasExercise(): bool
    {
        return $this->exercise_entry_id !== null;
    }

    public function setEntryId(int $id): void
    {
        $this->exercise_entry_id = $id;
    }
}
