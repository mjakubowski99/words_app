<?php

declare(strict_types=1);

namespace Flashcard\Domain\Models;

use Shared\Enum\SessionStatus;
use Shared\Utils\ValueObjects\UserId;
use Shared\Exceptions\ForbiddenException;
use Flashcard\Domain\ValueObjects\SessionId;

class Session
{
    private SessionId $id;

    public function __construct(
        private SessionStatus $status,
        private readonly UserId $user_id,
        private readonly int $cards_per_session,
        private readonly string $device,
        private readonly ?Deck $deck,
    ) {
        $this->validate();
    }

    public static function newSession(
        UserId $user_id,
        int $cards_per_session,
        string $device,
        ?Deck $deck
    ): self {
        return new Session(
            SessionStatus::STARTED,
            $user_id,
            $cards_per_session,
            $device,
            $deck
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

    public function hasFlashcardDeck(): bool
    {
        return $this->deck !== null;
    }

    public function getDeck(): Deck
    {
        return $this->deck;
    }

    public function getStatus(): SessionStatus
    {
        return $this->status;
    }

    public function setStatus(SessionStatus $status): void
    {
        $this->status = $status;
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

    private function validate(): void
    {
        if (!$this->hasFlashcardDeck() || !$this->deck->hasOwner()) {
            return;
        }

        $is_owner = $this->deck->getOwner()->equals(Owner::fromUser($this->user_id));

        if (!$is_owner) {
            throw new ForbiddenException('User is not authorized to create this deck');
        }
    }
}
