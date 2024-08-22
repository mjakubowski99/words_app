<?php

declare(strict_types=1);

namespace Shared\Utils\ValueObjects;
class Language
{
    public const PL = 'pl';
    public const EN = 'en';

    public const AVAILABLE = [
        self::PL,
        self::EN,
    ];

    private string $value;

    public function __construct(string $value)
    {
        $this->validate($value);
        $this->value = $value;
    }

    public static function from(string $value): self
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
        if (!in_array($value, self::AVAILABLE, true)) {
            throw new \Exception("{$value} is not available language.");
        }
    }
}