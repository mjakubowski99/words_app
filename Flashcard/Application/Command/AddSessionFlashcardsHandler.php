<?php

declare(strict_types=1);

namespace Flashcard\Application\Command;

use Flashcard\Domain\Models\SessionId;
use Flashcard\Domain\Repositories\IFlashcardRepository;
use Flashcard\Domain\Repositories\ISessionRepository;
use Flashcard\Domain\Services\IFlashcardSelector;
use Flashcard\Domain\Services\SessionFlashcardService;

class AddSessionFlashcardsHandler
{
    public function __construct(
        private ISessionRepository $session_repository,
        private SessionFlashcardService $session_flashcard_service,
        private IFlashcardRepository $flashcard_repository,
        private IFlashcardSelector $selector,
    ) {}

    public function handle(AddSessionFlashcards $command): void
    {
        $count_to_generate = $this->calculateCountToGenerate($command->getSessionId(), $command->getLimit());

        $session = $this->session_repository->find($command->getSessionId());

        $flashcards = $this->selector->select(
            $session->getUserId(),
            $session->getFlashcardCategory()->getId(),
            $count_to_generate,
        );

        $this->flashcard_repository->addFlashcardsToSession($session->getId(), $flashcards);

        $this->session_flashcard_service->addFlashcards($session, $count_to_generate);
    }

    private function calculateCountToGenerate(SessionId $session_id, int $limit): int
    {
        $not_rated_flashcards_count = $this->session_repository->getNotRatedFlashcardsCount($session_id);

        if ($not_rated_flashcards_count < $limit) {
            return $limit - $not_rated_flashcards_count;
        }

        return 0;
    }
}