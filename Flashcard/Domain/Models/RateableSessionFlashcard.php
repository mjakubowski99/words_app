<?php

declare(strict_types=1);

namespace Flashcard\Domain\Models;

use Flashcard\Domain\ValueObjects\FlashcardId;
use Flashcard\Domain\ValueObjects\SessionFlashcardId;
use Flashcard\Domain\Exceptions\SessionFlashcardAlreadyRatedException;

class RateableSessionFlashcard
{
    private ?Rating $rating = null;

    public function __construct(
        private readonly SessionFlashcardId $id,
        private readonly FlashcardId $flashcard_id,
    ) {}

    public function getId(): SessionFlashcardId
    {
        return $this->id;
    }

    public function getFlashcardId(): FlashcardId
    {
        return $this->flashcard_id;
    }

    public function rated(): bool
    {
        return $this->rating !== null;
    }

    public function getRating(): Rating
    {
        return $this->rating;
    }

    /** @throws SessionFlashcardAlreadyRatedException */
    public function rate(Rating $rating): void
    {
        if ($this->rated()) {
            throw new SessionFlashcardAlreadyRatedException("Already rated", (string) $this->id->getValue());
        }
        $this->rating = $rating;
    }
}
