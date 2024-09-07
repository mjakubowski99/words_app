<?php

declare(strict_types=1);

namespace Flashcard\Domain\Models;

class FlashcardId
{
    private int $value;

    public function __construct(int $value)
    {
        $this->value = $value;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function equals(object $flashcard_id): bool
    {
        return ($flashcard_id instanceof FlashcardId)
            && $flashcard_id->getValue() === $this->value;
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}
