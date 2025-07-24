<?php

namespace Flashcard\Application\Services\ExerciseFlashcardFactory;

use Flashcard\Application\DTO\SessionFlashcardSummaries;
use Flashcard\Domain\Models\Flashcard;
use Flashcard\Domain\Models\NextSessionFlashcards;

interface IExerciseFlashcardFactory
{
    public function make(NextSessionFlashcards $next_session_flashcards, Flashcard $base_flashcard): SessionFlashcardSummaries;
}