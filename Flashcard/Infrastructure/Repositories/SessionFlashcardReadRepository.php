<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Repositories;

use Flashcard\Domain\ValueObjects\SessionId;
use Flashcard\Application\ReadModels\SessionFlashcardsRead;
use Flashcard\Infrastructure\Mappers\SessionFlashcardReadMapper;
use Flashcard\Application\Repository\ISessionFlashcardReadRepository;

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
