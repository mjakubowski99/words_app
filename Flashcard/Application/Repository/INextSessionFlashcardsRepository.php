<?php

declare(strict_types=1);

namespace Flashcard\Application\Repository;

use Flashcard\Domain\ValueObjects\SessionFlashcardId;
use Flashcard\Domain\ValueObjects\SessionId;
use Flashcard\Domain\Models\NextSessionFlashcards;

interface INextSessionFlashcardsRepository
{
    public function find(SessionId $id): NextSessionFlashcards;

    public function save(NextSessionFlashcards $next_session_flashcards): void;
}
