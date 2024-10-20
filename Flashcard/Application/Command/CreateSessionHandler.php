<?php

declare(strict_types=1);

namespace Flashcard\Application\Command;

use Shared\Enum\SessionStatus;
use Flashcard\Domain\Models\Session;
use Flashcard\Application\DTO\CreateSessionResultDTO;
use Flashcard\Application\Repository\ISessionRepository;
use Flashcard\Application\Repository\IFlashcardCategoryRepository;

class CreateSessionHandler
{
    public function __construct(
        private readonly ISessionRepository $repository,
        private readonly IFlashcardCategoryRepository $category_repository,
    ) {}

    public function handle(CreateSession $command): CreateSessionResultDTO
    {
        $category = $command->hasCategoryId()
            ? $this->category_repository->findById($command->getCategoryId()) :
            null;

        $this->repository->setAllOwnerSessionsStatus($command->getOwner(), SessionStatus::FINISHED);

        $session = Session::newSession(
            $command->getOwner(),
            $command->getCardsPerSession(),
            $command->getDevice(),
            $category,
        );

        $session_id = $this->repository->create($session);

        return new CreateSessionResultDTO(true, null, $session_id);
    }
}
