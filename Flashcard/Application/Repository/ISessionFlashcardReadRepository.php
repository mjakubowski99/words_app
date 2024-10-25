<?php

declare(strict_types=1);

namespace Flashcard\Application\Repository;

use Flashcard\Application\ReadModels\SessionFlashcardsRead;
use Flashcard\Domain\ValueObjects\SessionId;

interface ISessionFlashcardReadRepository
{
    public function findUnratedById(SessionId $session_id, int $limit): SessionFlashcardsRead;
}
