<?php

declare(strict_types=1);

namespace Flashcard\Application\Services;

use Flashcard\Domain\Models\Flashcard;
use Flashcard\Domain\Models\NextSessionFlashcards;

interface IFlashcardSelector
{
    /** @return Flashcard[] */
    public function select(NextSessionFlashcards $next_session_flashcards, int $limit): array;
}
