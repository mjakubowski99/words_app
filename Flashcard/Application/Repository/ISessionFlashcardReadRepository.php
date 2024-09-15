<?php

namespace Flashcard\Application\Repository;

use Flashcard\Application\ReadModels\SessionFlashcardRead;
use Flashcard\Domain\ValueObjects\SessionId;

interface ISessionFlashcardReadRepository
{
    /** @return SessionFlashcardRead[] */
    public function findUnratedById(SessionId $session_id, int $limit): array;
}
