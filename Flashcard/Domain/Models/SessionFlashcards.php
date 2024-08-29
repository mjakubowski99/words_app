<?php

namespace Flashcard\Domain\Models;

class SessionFlashcards
{
    public function __construct(private array $session_flashcards) {}

    public function all(): array
    {
        return $this->session_flashcards;
    }

    public function findById(SessionFlashcardId $id): SessionFlashcard
    {
        $results = array_filter($this->session_flashcards, fn(SessionFlashcard $s) => $s->getId() === $id);

        if (count($results) === 0) {
            throw new \Exception();
        }

        return $results[0];
    }
}