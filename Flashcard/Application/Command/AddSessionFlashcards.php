<?php

declare(strict_types=1);

namespace Flashcard\Application\Command;

use Shared\Utils\ValueObjects\UserId;
use Flashcard\Domain\ValueObjects\SessionId;

class AddSessionFlashcards
{
    public function __construct(
        private readonly SessionId $session_id,
        private readonly UserId $user_id,
        private readonly int $limit
    ) {}

    public function getSessionId(): SessionId
    {
        return $this->session_id;
    }

    public function getUserId(): UserId
    {
        return $this->user_id;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }
}
