<?php

declare(strict_types=1);

namespace Flashcard\Application\Repository;

use Flashcard\Domain\ValueObjects\SessionId;
use Flashcard\Application\ReadModels\SessionFlashcardsRead;

interface ISessionFlashcardReadRepository
{
    public function findUnratedById(SessionId $session_id, int $limit): SessionFlashcardsRead;
}
