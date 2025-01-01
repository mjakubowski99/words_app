<?php

declare(strict_types=1);

namespace Flashcard\Application\Command;

use Shared\Utils\ValueObjects\UserId;
use Flashcard\Domain\ValueObjects\SessionId;

class RateFlashcards
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
