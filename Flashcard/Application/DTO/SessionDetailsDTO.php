<?php

declare(strict_types=1);

namespace Flashcard\Application\DTO;

use Shared\Enum\SessionStatus;
use Flashcard\Domain\Models\SessionId;

class SessionDetailsDTO
{
    public function __construct(
        private readonly SessionId $id,
        private readonly SessionStatus $status,
        private readonly int $progress,
        private readonly int $cards_per_session,
        private readonly bool $is_finished,
    ) {}

    public function getId(): SessionId
    {
        return $this->id;
    }

    public function getStatus(): SessionStatus
    {
        return $this->status;
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
