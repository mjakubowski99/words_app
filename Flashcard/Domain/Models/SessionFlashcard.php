<?php

namespace Flashcard\Domain\Models;

class SessionFlashcard
{
    public function __construct(
        private SessionFlashcardId $id,
        private Flashcard $flashcard,
        private ?Rating $rating,
    ) {}

    public function getId(): SessionFlashcardId
    {
        return $this->id;
    }

    public function getFlashcard(): Flashcard
    {
        return $this->flashcard;
    }

    public function rated(): bool
    {
        return $this->rating !== null;
    }

    public function getRating(): Rating
    {
        return $this->rating;
    }

    public function rate(Rating $rating): void
    {
        if ($this->rated()) {
            throw new \Exception("Flashcard already rated");
        }
        $this->rating = $rating;
    }
}