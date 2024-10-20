<?php

declare(strict_types=1);

namespace Flashcard\Domain\Models;

use Shared\Enum\SessionStatus;
use Shared\Exceptions\ForbiddenException;
use Flashcard\Domain\ValueObjects\SessionId;

class Session
{
    private SessionId $id;

    public function __construct(
        private SessionStatus $status,
        private readonly Owner $owner,
        private readonly int $cards_per_session,
        private readonly string $device,
        private readonly ?Category $flashcard_category,
    ) {
        $this->validate();
    }

    public static function newSession(
        Owner $owner,
        int $cards_per_session,
        string $device,
        ?Category $category
    ): self {
        return new Session(
            SessionStatus::STARTED,
            $owner,
            $cards_per_session,
            $device,
            $category
        );
    }

    public function init(SessionId $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): SessionId
    {
        return $this->id;
    }

    public function hasFlashcardCategory(): bool
    {
        return $this->flashcard_category !== null;
    }

    public function getFlashcardCategory(): Category
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

    private function validate(): void
    {
        if (!$this->hasFlashcardCategory() || !$this->flashcard_category->hasOwner()) {
            return;
        }

        $is_owner = $this->flashcard_category->getOwner()->equals($this->owner);

        if (!$is_owner) {
            throw new ForbiddenException('User is not authorized to create this category');
        }
    }
}
