<?php

declare(strict_types=1);

namespace Flashcard\Domain\Models;

final readonly class CategoryId
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
}
