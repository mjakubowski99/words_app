<?php

declare(strict_types=1);

namespace Shared\Models;

class Emoji implements \Stringable
{
    public function __construct(
        private string $emoji
    ) {}

    public static function fromUnicode(string $emoji): self
    {
        return new self(json_decode($emoji));
    }

    public function toUnicode(): string
    {
        return json_encode($this->emoji);
    }

    public function __toString(): string
    {
        return $this->emoji;
    }
}
