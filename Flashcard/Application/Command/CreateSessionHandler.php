<?php

declare(strict_types=1);

namespace Flashcard\Application\Command;

use Shared\Enum\SessionStatus;
use Flashcard\Domain\Models\Session;
use Flashcard\Application\DTO\CreateSessionResultDTO;
use Flashcard\Application\Services\IFlashcardSelector;
use Flashcard\Application\Repository\ISessionRepository;
use Flashcard\Application\Repository\IFlashcardDeckRepository;

class CreateSessionHandler
{
    public function __construct(
        private readonly ISessionRepository $repository,
        private readonly IFlashcardDeckRepository $deck_repository,
        private readonly IFlashcardSelector $selector,
    ) {}

    public function handle(CreateSession $command): CreateSessionResultDTO
    {
        $deck = $command->hasDeckId()
            ? $this->deck_repository->findById($command->getDeckId())
            : null;

        $this->repository->setAllOwnerSessionsStatus($command->getUserId(), SessionStatus::FINISHED);

        $session = Session::newSession(
            $command->getUserId(),
            $command->getCardsPerSession(),
            $command->getDevice(),
            $deck,
        );

        $session_id = $this->repository->create($session);

        $this->selector->resetRepetitionsInSession($command->getUserId());

        return new CreateSessionResultDTO(true, null, $session_id);
    }
}
