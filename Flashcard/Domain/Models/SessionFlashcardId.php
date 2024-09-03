<?php

namespace Flashcard\Domain\Models;

class SessionFlashcardId
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

    public function equals(object $id): bool
    {
        return ($id instanceof SessionFlashcardId)
            && $id->getValue() === $this->getValue();
    }
}