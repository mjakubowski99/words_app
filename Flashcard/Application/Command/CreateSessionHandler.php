<?php

declare(strict_types=1);

namespace Flashcard\Application\Command;

use Flashcard\Domain\Services\SessionService;
use Flashcard\Application\DTO\CreateSessionResultDTO;
use Flashcard\Domain\Repositories\ISessionRepository;

class CreateSessionHandler
{
    public function __construct(
        private readonly ISessionRepository $repository,
        private readonly SessionService $service,
    ) {}

    public function handle(CreateSession $command): CreateSessionResultDTO
    {
        $session = $this->service->newSession(
            $command->getOwner(),
            $command->getCategoryId(),
            $command->getCardsPerSession(),
            $command->getDevice()
        );

        $session_id = $this->repository->create($session);

        return new CreateSessionResultDTO(true, null, $session_id);
    }
}
