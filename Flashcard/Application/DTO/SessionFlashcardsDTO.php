<?php

declare(strict_types=1);

namespace Flashcard\Application\DTO;

use Flashcard\Domain\Models\DetailedSessionFlashcard;

class SessionFlashcardsDTO
{
    public function __construct(private readonly array $flashcards) {}

    /** @return SessionFlashcardDTO[] */
    public function getFlashcards(): array
    {
        return array_map(function (DetailedSessionFlashcard $flashcard) {
            return new SessionFlashcardDTO(
                $flashcard->getId(),
                $flashcard->hasRating() ? $flashcard->getRating() : null,
                $flashcard->getFlashcard()->getWord(),
                $flashcard->getFlashcard()->getWordLang(),
                $flashcard->getFlashcard()->getTranslation(),
                $flashcard->getFlashcard()->getTranslationLang(),
                $flashcard->getFlashcard()->getContext(),
                $flashcard->getFlashcard()->getContextTranslation(),
            );
        }, $this->flashcards);
    }
}
