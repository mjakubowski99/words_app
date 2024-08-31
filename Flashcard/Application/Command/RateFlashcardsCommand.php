<?php

namespace Flashcard\Application\Command;

use Flashcard\Domain\Models\SessionId;
use Shared\Utils\ValueObjects\UserId;

class RateFlashcardsCommand
{
    public function __construct(
        private UserId $user_id,
        private SessionId $session_id,
        private array $ratings
    ) {}

    public function getUserId(): UserId
    {
        return $this->user_id;
    }

    public function getSessionId(): SessionId
    {
        return $this->session_id;
    }

    /**
     * @return FlashcardRating[]
     */
    public function getRatings(): array
    {
        return $this->ratings;
    }
}