<?php

declare(strict_types=1);

namespace Flashcard\Domain\Models;

use Shared\Enum\SessionStatus;
use Shared\Utils\ValueObjects\UserId;

class Session
{
    private SessionId $id;

    public function __construct(
        private readonly SessionStatus $status,
        private readonly UserId $user_id,
        private readonly int $cards_per_session,
        private readonly string $device,
        private readonly FlashcardCategory $flashcard_category,
    ) {}

    public function setId(SessionId $id): void
    {
        $this->id = $id;
    }

    public function getId(): SessionId
    {
        return $this->id;
    }

    public function getFlashcardCategory(): FlashcardCategory
    {
        return $this->flashcard_category;
    }


    public function getStatus(): SessionStatus
    {
        return $this->status;
    }

    public function getUserId(): UserId
    {
        return $this->user_id;
    }

    public function getCardsPerSession(): int
    {
        return $this->cards_per_session;
    }

    public function getDevice(): string
    {
        return $this->device;
    }

    public function isFinished(): bool
    {
        return $this->status === SessionStatus::FINISHED;
    }
}
