<?php

declare(strict_types=1);

namespace Flashcard\Application\Command;

use Flashcard\Domain\Models\Owner;
use Flashcard\Domain\ValueObjects\SessionId;

class RateFlashcardsCommand
{
    public function __construct(
        private Owner $owner,
        private SessionId $session_id,
        private array $ratings
    ) {}

    public function getOwner(): Owner
    {
        return $this->owner;
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
