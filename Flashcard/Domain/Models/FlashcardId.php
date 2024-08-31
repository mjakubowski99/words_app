<?php

namespace Flashcard\Domain\Models;

class FlashcardId
{
    private string $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function equals(object $flashcard_id): bool
    {
        return ($flashcard_id instanceof FlashcardId)
            && $flashcard_id->getValue() === $this->value;
    }
}