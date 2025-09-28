<?php

declare(strict_types=1);

namespace Flashcard\Application\Exercise;

use Flashcard\Domain\Models\Flashcard;
use Flashcard\Domain\Models\NextSessionFlashcards;
use Flashcard\Application\DTO\SessionFlashcardSummaries;

interface IExerciseFlashcardFactory
{
    public function make(NextSessionFlashcards $next_session_flashcards, Flashcard $base_flashcard): SessionFlashcardSummaries;
}
