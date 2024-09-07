<?php

declare(strict_types=1);

namespace Flashcard\Application\Command;

use Flashcard\Domain\Models\Rating;
use Flashcard\Domain\Models\SessionFlashcardId;

class FlashcardRating
{
    public function __construct(private SessionFlashcardId $id, private Rating $rating) {}

    public function getSessionFlashcardId(): SessionFlashcardId
    {
        return $this->id;
    }

    public function getRating(): Rating
    {
        return $this->rating;
    }
}
