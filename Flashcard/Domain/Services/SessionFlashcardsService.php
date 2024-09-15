<?php

namespace Flashcard\Domain\Services;

use Flashcard\Domain\Models\NextSessionFlashcards;

class SessionFlashcardsService
{
    public function add(NextSessionFlashcards $session_flashcards, array $flashcards): NextSessionFlashcards
    {
        foreach ($flashcards as $flashcard) {
            if (!$session_flashcards->canAddNext()) {
                break;
            }
            $session_flashcards->addNext($flashcard);
        }

        return $session_flashcards;
    }
}