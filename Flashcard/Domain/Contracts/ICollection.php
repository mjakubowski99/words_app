<?php

namespace Flashcard\Domain\Contracts;

interface ICollection
{
    public function all(): array;

    public function isEmpty(): bool;
}