<?php

namespace Shared\Exercise;

use Shared\Flashcard\ISessionFlashcardSummary;

interface IExerciseFacade
{
    public function makeUnscrambleWordExercise(ISessionFlashcardSummary $summary): void;
}