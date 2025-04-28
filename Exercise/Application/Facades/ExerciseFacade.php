<?php

namespace Exercise\Application\Facades;

use Shared\Exercise\IUnscrambleWordExercise;
use Shared\Flashcard\ISessionFlashcardSummary;

class ExerciseFacade
{
    /** @param ISessionFlashcardSummary[] $session_flashcards_summary */
    public function makeUnscrambleWordExercise(array $session_flashcards_summary): IUnscrambleWordExercise
    {
        // make exercise bro
    }
}