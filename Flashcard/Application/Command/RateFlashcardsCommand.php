<?php

namespace Flashcard\Application\Command;

use Flashcard\Domain\Models\SessionId;

class RateFlashcardsCommand
{
    public function __construct(
        private SessionId $session_id,
        private array $ratings
    ) {}

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