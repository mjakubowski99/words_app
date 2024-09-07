<?php

declare(strict_types=1);

namespace Flashcard\Application\Query;

use Flashcard\Domain\Models\SessionId;
use Flashcard\Application\DTO\SessionFlashcardsDTO;
use Flashcard\Domain\Repositories\ISessionFlashcardRepository;

class GetNextSessionFlashcardsHandler
{
    public function __construct(
        private ISessionFlashcardRepository $repository,
    ) {}

    public function handle(SessionId $session_id, int $limit): SessionFlashcardsDTO
    {
        return new SessionFlashcardsDTO(
            $this->repository->getNotRatedDetailedSessionFlashcards($session_id, $limit)
        );
    }
}
