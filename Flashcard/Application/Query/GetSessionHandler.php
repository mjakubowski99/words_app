<?php

declare(strict_types=1);

namespace Flashcard\Application\Query;

use Flashcard\Domain\Models\SessionId;
use Flashcard\Application\DTO\SessionDetailsDTO;
use Flashcard\Domain\Repositories\ISessionRepository;
use Flashcard\Domain\Repositories\ISessionFlashcardRepository;

class GetSessionHandler
{
    public function __construct(
        private readonly ISessionRepository $repository,
        private readonly ISessionFlashcardRepository $session_flashcard_repository,
    ) {}

    public function handle(SessionId $id): SessionDetailsDTO
    {
        $session = $this->repository->find($id);

        return new SessionDetailsDTO(
            $session->getId(),
            $session->getStatus(),
            $this->session_flashcard_repository->getRatedSessionFlashcardsCount($id),
            $session->getCardsPerSession(),
            $session->isFinished()
        );
    }
}
