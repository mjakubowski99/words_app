<?php

declare(strict_types=1);

namespace Flashcard\Application\Command;

use Flashcard\Domain\Repositories\ISessionRepository;
use Flashcard\Domain\Services\SessionFlashcardsService;

class AddSessionFlashcardsHandler
{
    public function __construct(
        private readonly ISessionRepository $repository,
        private readonly SessionFlashcardsService $service,
    ) {}

    public function handle(AddSessionFlashcards $command): void
    {
        $session = $this->repository->find($command->getSessionId());

        $this->service->add($session, $command->getLimit());
    }
}
