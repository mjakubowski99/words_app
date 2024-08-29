<?php

namespace Flashcard\Domain\Models;

final readonly class CategoryId
{
    private string $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public static function fromInt(int $value): self
    {
        return new CategoryId((string) $value);
    }

    public function getValue(): string
    {
        return $this->value;
    }
}