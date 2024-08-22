<?php

declare(strict_types=1);

namespace Flashcard\Application\Command;

use Flashcard\Application\DTO\CreateSessionResultDTO;
use Flashcard\Domain\Models\Session;
use Flashcard\Domain\Repositories\IFlashcardCategoryRepository;
use Flashcard\Domain\Repositories\ISessionRepository;
use Shared\Enum\SessionStatus;

class CreateSessionHandler
{
    public function __construct(
        private readonly ISessionRepository $repository,
        private readonly IFlashcardCategoryRepository $category_repository,
    ) {}

    public function handle(CreateSession $command): CreateSessionResultDTO
    {
        $user_id = $command->getOwnerUser()->getId();
        $category_id = $command->getCategoryId();

        $category = $this->category_repository->findById($category_id);

        if ($this->repository->existsActiveByCategory($user_id, $category_id)) {
            return new CreateSessionResultDTO(false, 'Active session already exists', null);
        }

        $session = new Session(
            SessionStatus::STARTED,
            $user_id,
            $command->getCardsPerSession(),
            $command->getDevice(),
            $category
        );

        $session_id = $this->repository->create($session);

        return new CreateSessionResultDTO(true, null, $session_id->getValue());
    }
}
