<?php

declare(strict_types=1);

namespace Flashcard\Application\DTO;

use Flashcard\Domain\ValueObjects\FlashcardDeckId;

class GenerateFlashcardsResult
{
    public function __construct(
        private readonly FlashcardDeckId $id,
        private int $generated_count,
        private readonly bool $merged_to_existing_deck
    ) {}

    public function getDeckId(): FlashcardDeckId
    {
        return $this->id;
    }

    public function getGeneratedCount(): int
    {
        return $this->generated_count;
    }

    public function getMergedToExistingDeck(): bool
    {
        return $this->merged_to_existing_deck;
    }
}
