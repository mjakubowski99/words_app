<?php

declare(strict_types=1);

namespace Flashcard\Application\Query;

use Flashcard\Application\ReadModels\SessionFlashcardsRead;
use Flashcard\Application\Repository\ISessionFlashcardReadRepository;
use Flashcard\Domain\ValueObjects\SessionId;

class GetNextSessionFlashcardsHandler
{
    public function __construct(
        private ISessionFlashcardReadRepository $repository,
    ) {}

    public function handle(SessionId $session_id, int $limit): SessionFlashcardsRead
    {
        return $this->repository->findUnratedById($session_id, $limit);
    }
}
