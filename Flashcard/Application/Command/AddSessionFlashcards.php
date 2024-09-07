<?php

declare(strict_types=1);

namespace Flashcard\Application\Command;

use Flashcard\Domain\Models\SessionId;

class AddSessionFlashcards
{
    public function __construct(private readonly SessionId $session_id, private readonly int $limit) {}

    public function getSessionId(): SessionId
    {
        return $this->session_id;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }
}
