<?php

namespace Flashcard\Domain\Models;

use Flashcard\Domain\ValueObjects\FlashcardId;

class NextSessionFlashcard
{
    public function __construct(
        private readonly FlashcardId $id,
    ) {}

    public function getFlashcardId(): FlashcardId
    {
        return $this->id;
    }
}