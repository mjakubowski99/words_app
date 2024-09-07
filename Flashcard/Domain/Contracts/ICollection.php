<?php

declare(strict_types=1);

namespace Flashcard\Domain\Contracts;

interface ICollection
{
    public function all(): array;

    public function isEmpty(): bool;
}
