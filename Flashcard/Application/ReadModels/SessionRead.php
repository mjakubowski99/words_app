<?php

namespace Flashcard\Application\ReadModels;

use Flashcard\Domain\ValueObjects\SessionId;
use Shared\Enum\SessionStatus;

class SessionRead
{
    public function __construct(
        private SessionId $id,
        private readonly int $progress,
        private readonly int $cards_per_session,
        private readonly bool $is_finished,
    ) {}

    public function getId(): SessionId
    {
        return $this->id;
    }

    public function getProgress(): int
    {
        return $this->progress;
    }

    public function getCardsPerSession(): int
    {
        return $this->cards_per_session;
    }

    public function isFinished(): bool
    {
        return $this->is_finished;
    }
}