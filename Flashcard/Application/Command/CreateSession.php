<?php

declare(strict_types=1);

namespace Flashcard\Application\Command;

use Shared\Enum\SessionType;
use Shared\Utils\ValueObjects\UserId;
use Flashcard\Domain\ValueObjects\FlashcardDeckId;

final readonly class CreateSession
{
    public function __construct(
        private UserId $user_id,
        private int $cards_per_session,
        private string $device,
        private ?FlashcardDeckId $deck_id,
        private SessionType $type,
    ) {}

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

    public function hasDeckId(): bool
    {
        return $this->deck_id !== null;
    }

    public function getDeckId(): FlashcardDeckId
    {
        return $this->deck_id;
    }

    public function getType(): SessionType
    {
        return $this->type;
    }
}
