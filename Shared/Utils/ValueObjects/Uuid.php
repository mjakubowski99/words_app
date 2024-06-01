<?php

declare(strict_types=1);

namespace Shared\Utils\ValueObjects;

final class Uuid
{
    private readonly string $value;

    private function __construct(string $value)
    {
        $this->validate($value);
        $this->value = $value;
    }

    public static function make(): self
    {
        $uuid = \Ramsey\Uuid\Uuid::uuid4();

        return new self((string) $uuid);
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    private function validate(string $value): void
    {
        $uuid = \Ramsey\Uuid\Uuid::fromString($value);
    }
}
