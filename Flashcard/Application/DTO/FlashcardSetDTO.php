<?php

namespace Flashcard\Application\DTO;

use Flashcard\Domain\Models\Flashcard;

class FlashcardSetDTO
{
    public function __construct(private readonly array $flashcards) {}

    /** @return FlashcardDTO[] */
    public function getFlashcards(): array
    {
        return array_map(function (Flashcard $flashcard) {
            return new FlashcardDTO(
                $flashcard->getId(),
                $flashcard->getWord(),
                $flashcard->getWordLang(),
                $flashcard->getTranslation(),
                $flashcard->getTranslationLang(),
                $flashcard->getContext(),
                $flashcard->getContextTranslation(),
            );
        }, $this->flashcards);
    }
}