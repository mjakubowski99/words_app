<?php

declare(strict_types=1);

namespace Shared\Utils\ValueObjects;

class UserId
{
    private Uuid $value;

    public function __construct(string $value)
    {
        $this->value = Uuid::fromString($value);
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public function getValue(): string
    {
        return $this->value->getValue();
    }
}