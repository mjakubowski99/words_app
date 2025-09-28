<?php

declare(strict_types=1);

namespace Flashcard\Application\Exercise;

use Flashcard\Domain\Models\Flashcard;
use Flashcard\Domain\Models\NextSessionFlashcards;
use Flashcard\Application\DTO\SessionFlashcardSummaries;

class UnscrambleWordExerciseFlashcardFactory implements IExerciseFlashcardFactory
{
    public function make(NextSessionFlashcards $next_session_flashcards, Flashcard $base_flashcard): SessionFlashcardSummaries
    {
        return SessionFlashcardSummaries::fromFlashcards([$base_flashcard], $base_flashcard);
    }
}
