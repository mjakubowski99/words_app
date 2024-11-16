<?php

declare(strict_types=1);

namespace Flashcard\Application\Command;

use Shared\Enum\SessionStatus;
use Flashcard\Domain\Models\Session;
use Flashcard\Application\DTO\CreateSessionResultDTO;
use Flashcard\Application\Repository\ISessionRepository;
use Flashcard\Application\Repository\IFlashcardDeckRepository;

class CreateSessionHandler
{
    public function __construct(
        private readonly ISessionRepository $repository,
        private readonly IFlashcardDeckRepository $deck_repository,
    ) {}

    public function handle(CreateSession $command): CreateSessionResultDTO
    {
        $deck = $command->hasDeckId()
            ? $this->deck_repository->findById($command->getDeckId()) :
            null;

        $this->repository->setAllOwnerSessionsStatus($command->getOwner(), SessionStatus::FINISHED);

        $session = Session::newSession(
            $command->getOwner(),
            $command->getCardsPerSession(),
            $command->getDevice(),
            $deck,
        );

        $session_id = $this->repository->create($session);

        return new CreateSessionResultDTO(true, null, $session_id);
    }
}
