<?php

declare(strict_types=1);

namespace Flashcard\Domain\Models;

use Flashcard\Domain\ValueObjects\FlashcardId;
use Flashcard\Domain\ValueObjects\SessionFlashcardId;

class ActiveSessionFlashcard
{
    public function __construct(
        private readonly SessionFlashcardId $session_flashcard_id,
        private readonly FlashcardId $flashcard_id,
        private ?Rating $rating,
        private ?int $exercise_entry_id,
        private readonly bool $is_additional,
    ) {}

    public function rated(): bool
    {
        return $this->rating !== null;
    }

    public function rate(Rating $rating): void
    {
        $this->rating = $rating;
    }

    public function getSessionFlashcardId(): SessionFlashcardId
    {
        return $this->session_flashcard_id;
    }

    public function getFlashcardId(): FlashcardId
    {
        return $this->flashcard_id;
    }

    public function getRating(): ?Rating
    {
        return $this->rating;
    }

    public function hasExercise(): bool
    {
        return $this->exercise_entry_id !== null;
    }

    public function getExerciseEntryId(): int
    {
        return $this->exercise_entry_id;
    }

    public function isAdditional(): bool
    {
        return $this->is_additional;
    }
}
