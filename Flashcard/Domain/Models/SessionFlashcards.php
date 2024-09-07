<?php

declare(strict_types=1);

namespace Flashcard\Domain\Models;

use Flashcard\Domain\Contracts\ICollection;

class SessionFlashcards implements ICollection
{
    public function __construct(
        private Session $session,
        private array $session_flashcards,
    ) {}

    public function all(): array
    {
        return $this->session_flashcards;
    }

    public function isEmpty(): bool
    {
        return count($this->session_flashcards) === 0;
    }

    public function getSession(): Session
    {
        return $this->session;
    }

    public function rate(SessionFlashcardId $id, Rating $rating): void
    {
        $key = $this->findKeyById($id);

        $this->session_flashcards[$key]->rate($rating);
    }

    private function findKeyById(SessionFlashcardId $id): int
    {
        foreach ($this->session_flashcards as $key => $session_flashcard) {
            if ($session_flashcard->getId()->equals($id)) {
                return $key;
            }
        }

        throw new \Exception();
    }

    /** @return FlashcardId[] */
    public function pluckFlashcardIds(): array
    {
        return array_map(fn (SessionFlashcard $flashcard) => $flashcard->getFlashcardId(), $this->session_flashcards);
    }
}
