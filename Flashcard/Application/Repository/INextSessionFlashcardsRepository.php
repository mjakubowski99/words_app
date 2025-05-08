<?php

declare(strict_types=1);

namespace Flashcard\Application\Repository;

use Flashcard\Domain\ValueObjects\SessionId;
use Flashcard\Domain\Models\NextSessionFlashcards;
use Flashcard\Domain\Models\NextSessionFlashcardResult;

interface INextSessionFlashcardsRepository
{
    public function find(SessionId $id): NextSessionFlashcards;

    /** @return NextSessionFlashcardResult[] */
    public function save(NextSessionFlashcards $next_session_flashcards): void;
}
