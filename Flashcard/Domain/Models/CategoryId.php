<?php

namespace Flashcard\Domain\Models;

final readonly class CategoryId
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
}