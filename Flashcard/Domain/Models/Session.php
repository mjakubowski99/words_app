<?php

declare(strict_types=1);

namespace Flashcard\Domain\Models;

use Flashcard\Domain\Contracts\ICategory;
use Shared\Enum\SessionStatus;

class Session
{
    private SessionId $id;

    public function __construct(
        private SessionStatus     $status,
        private readonly Owner $owner,
        private readonly int      $cards_per_session,
        private readonly string   $device,
        private readonly ICategory $flashcard_category,
    ) {}

    public function init(SessionId $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): SessionId
    {
        return $this->id;
    }

    public function getFlashcardCategory(): ICategory
    {
        return $this->flashcard_category;
    }

    public function getStatus(): SessionStatus
    {
        return $this->status;
    }

    public function setStatus(SessionStatus $status): void
    {
        $this->status = $status;
    }

    public function getOwner(): Owner
    {
        return $this->owner;
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
