<?php

declare(strict_types=1);

namespace Flashcard\Domain\Exceptions;

use Shared\Exceptions\BadRequestException;

class SessionFlashcardAlreadyRatedException extends BadRequestException
{
    private string $identifier;

    public function __construct(string $message, string $identifier, ?\Throwable $previous = null)
    {
        $this->identifier = $identifier;
        parent::__construct($message, $previous);
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }
}
