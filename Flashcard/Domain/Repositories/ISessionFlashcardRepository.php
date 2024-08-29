<?php

declare(strict_types=1);

namespace Flashcard\Domain\Repositories;

use Flashcard\Domain\Models\Flashcard;
use Flashcard\Domain\Models\SessionId;

interface ISessionFlashcardRepository
{
    /** @return Flashcard[] */
    public function getNotRatedSessionFlashcards(SessionId $session_id, int $limit): array;

    /** @param Flashcard[] $flashcards */
    public function addFlashcardsToSession(SessionId $session_id, array $flashcards): void;
}
