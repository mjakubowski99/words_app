<?php

declare(strict_types=1);

namespace Flashcard\Domain\Exceptions;

class RateableSessionFlashcardNotFound extends \Exception
{
    private string $identifier;

    public function __construct(string $message, string $identifier, int $code = 0, ?\Throwable $previous = null)
    {
        $this->identifier = $identifier;
        parent::__construct($message, $code, $previous);
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }
}
