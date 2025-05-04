<?php

declare(strict_types=1);

namespace Exercise\Application\DTO;

use Flashcard\Domain\Models\Rating;
use Shared\Flashcard\ISessionFlashcardRating;

class SessionFlashcardRating implements ISessionFlashcardRating
{
    public function __construct(
        private int $session_flashcard_id,
        private Rating $rating,
    ) {}

    public function getSessionFlashcardId(): int
    {
        return $this->session_flashcard_id;
    }

    public function getRating(): Rating
    {
        return $this->rating;
    }
}
