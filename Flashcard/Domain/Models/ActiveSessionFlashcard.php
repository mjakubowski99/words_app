<?php

declare(strict_types=1);

namespace Flashcard\Domain\Models;

use Shared\Enum\SessionStatus;
use Shared\Utils\ValueObjects\UserId;
use Flashcard\Domain\ValueObjects\SessionId;
use Flashcard\Domain\ValueObjects\FlashcardId;
use Flashcard\Domain\ValueObjects\SessionFlashcardId;

class ActiveSessionFlashcard
{
    public function __construct(
        private readonly SessionId $session_id,
        private readonly SessionStatus $status,
        private readonly UserId $user_id,
        private readonly int $max_count,
        private readonly SessionFlashcardId $session_flashcard_id,
        private readonly FlashcardId $flashcard_id,
        private readonly int $rated_count,
        private ?Rating $rating,
        private readonly bool $has_deck
    ) {}

    public function getSessionStatus(): SessionStatus
    {
        return $this->status;
    }

    public function getUserId(): UserId
    {
        return $this->user_id;
    }

    public function rated(): bool
    {
        return $this->rating !== null;
    }

    public function rate(Rating $rating): void
    {
        $this->rating = $rating;
    }

    public function getSessionId(): SessionId
    {
        return $this->session_id;
    }

    public function getMaxCount(): int
    {
        return $this->max_count;
    }

    public function getSessionFlashcardId(): SessionFlashcardId
    {
        return $this->session_flashcard_id;
    }

    public function getFlashcardId(): FlashcardId
    {
        return $this->flashcard_id;
    }

    public function getRatedCount(): int
    {
        return $this->rated_count;
    }

    public function getRating(): ?Rating
    {
        return $this->rating;
    }

    public function hasDeck(): bool
    {
        return $this->has_deck;
    }
}
