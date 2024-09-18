<?php

declare(strict_types=1);

namespace Shared\Utils\ValueObjects;

class UserId implements \Stringable
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

    public function equals(object $user_id): bool
    {
        return ($user_id instanceof UserId)
            && $user_id->getValue() === $this->getValue();
    }

    public function __toString(): string
    {
        return $this->value->getValue();
    }
}
