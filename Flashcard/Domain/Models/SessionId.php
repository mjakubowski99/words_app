<?php

declare(strict_types=1);

namespace Flashcard\Domain\Models;

class SessionId
{
    private string $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public static function fromInt(int $value): self
    {
        return new SessionId((string) $value);
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
