<?php

declare(strict_types=1);

namespace Flashcard\Domain\Models;

class DetailedSessionFlashcard
{
    public function __construct(
        private SessionFlashcardId $id,
        private ?Rating $rating,
        private Flashcard $flashcard,
    ) {}

    public function getId(): SessionFlashcardId
    {
        return $this->id;
    }

    public function hasRating(): bool
    {
        return $this->rating !== null;
    }

    public function getRating(): Rating
    {
        return $this->rating;
    }

    public function getFlashcard(): Flashcard
    {
        return $this->flashcard;
    }
}
