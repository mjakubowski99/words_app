<?php

declare(strict_types=1);

namespace Flashcard\Application\Repository;

use Flashcard\Domain\ValueObjects\SessionId;

interface ISessionFlashcardRepository
{
    public function getLatestSessionFlashcardIds(SessionId $session_id, int $limit): array;
}