<?php

declare(strict_types=1);

namespace Shared\Utils\ValueObjects;

abstract class IntegerId
{
    private const NO_ID_VALUE = 0;

    protected int $value;

    final public function __construct(int $value)
    {
        $this->value = $value;
    }

    public function isEmpty(): bool
    {
        return $this->value === self::NO_ID_VALUE;
    }

    public function getNoIdValue(): int
    {
        return self::NO_ID_VALUE;
    }

    public static function noId(): static
    {
        return new static(self::NO_ID_VALUE);
    }

    public function getValue(): int
    {
        if ($this->value === self::NO_ID_VALUE) {
            throw new \UnexpectedValueException('Cannot retrieve no id value');
        }

        return $this->value;
    }

    public function __toString(): string
    {
        if ($this->value === self::NO_ID_VALUE) {
            throw new \UnexpectedValueException('Cannot retrieve no id value');
        }

        return (string) $this->value;
    }

    final public function equals(object $id): bool
    {
        return (get_class($this) === get_class($id))
            && $id->getValue() === $this->value;
    }
}
