<?php

declare(strict_types=1);

namespace Flashcard\Domain\Exceptions;

class FlashcardPollOverLoadedException extends \UnexpectedValueException
{
    public function __construct(
        private readonly int $expected_max_size,
        private readonly int $current_size
    ) {
        parent::__construct('Flashcard poll overloaded');
    }

    public function getExpectedMaxSize(): int
    {
        return $this->expected_max_size;
    }

    public function getCurrentSize(): int
    {
        return $this->current_size;
    }
}
