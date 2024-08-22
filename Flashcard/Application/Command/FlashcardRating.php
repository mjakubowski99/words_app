<?php

namespace Flashcard\Application\Command;

use Flashcard\Domain\Models\Rating;
use Flashcard\Domain\Models\SessionFlashcardId;

class FlashcardRating
{
    public function getSessionFlashcardId(): SessionFlashcardId
    {
        return new SessionFlashcardId();
    }

    public function getRating(): Rating
    {
        return Rating::GOOD;
    }
}