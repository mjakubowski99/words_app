<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Repositories\Postgres;

use Flashcard\Domain\ValueObjects\SessionId;
use Flashcard\Application\ReadModels\SessionFlashcardsRead;
use Flashcard\Application\Repository\ISessionFlashcardReadRepository;
use Flashcard\Infrastructure\Mappers\Postgres\SessionFlashcardReadMapper;

class SessionFlashcardReadRepository implements ISessionFlashcardReadRepository
{
    public function __construct(
        private readonly SessionFlashcardReadMapper $mapper,
    ) {}

    public function findUnratedById(SessionId $session_id, int $limit): SessionFlashcardsRead
    {
        return $this->mapper->findUnratedById($session_id, $limit);
    }
}
