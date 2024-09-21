<?php

declare(strict_types=1);

namespace Flashcard\Application\Query;

use Flashcard\Domain\ValueObjects\SessionId;
use Flashcard\Application\Repository\ISessionFlashcardReadRepository;

class GetNextSessionFlashcardsHandler
{
    public function __construct(
        private ISessionFlashcardReadRepository $repository,
    ) {}

    public function handle(SessionId $session_id, int $limit): array
    {
        return $this->repository->findUnratedById($session_id, $limit);
    }
}