<?php

declare(strict_types=1);

namespace Shared\Utils\ValueObjects;

class UserId
{
    public function __construct(private string $value) {}

    public function getValue(): string
    {
        return $this->value;
    }
}