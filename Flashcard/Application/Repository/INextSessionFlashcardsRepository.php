<?php

declare(strict_types=1);

namespace Flashcard\Application\Repository;

use Flashcard\Domain\Models\NextSessionFlashcards;
use Flashcard\Domain\ValueObjects\SessionId;

interface INextSessionFlashcardsRepository
{
    public function find(SessionId $id): NextSessionFlashcards;
    public function save(NextSessionFlashcards $next_session_flashcards): void;
}