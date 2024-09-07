<?php

declare(strict_types=1);

namespace Flashcard\Application\Command;

use Flashcard\Domain\Models\Flashcard;
use Flashcard\Domain\Models\SessionId;
use Flashcard\Domain\Models\SessionFlashcard;
use Flashcard\Domain\Models\SessionFlashcards;
use Flashcard\Domain\Services\IFlashcardSelector;
use Flashcard\Domain\Repositories\ISessionRepository;
use Flashcard\Domain\Repositories\ISessionFlashcardRepository;

class AddSessionFlashcardsHandler
{
    public function __construct(
        private ISessionRepository $session_repository,
        private ISessionFlashcardRepository $session_flashcard_repository,
        private IFlashcardSelector $selector,
    ) {}

    public function handle(AddSessionFlashcards $command): void
    {
        $generated_count = $this->session_flashcard_repository->getTotalSessionFlashcardsCount($command->getSessionId());

        $session = $this->session_repository->find($command->getSessionId());

        if ($generated_count >= $session->getCardsPerSession()) {
            return;
        }

        $count_to_generate = $this->calculateCountToGenerate($command->getSessionId(), $command->getLimit());

        if ($count_to_generate === 0) {
            return;
        }

        $session = $this->session_repository->find($command->getSessionId());

        $flashcards = $this->selector->select(
            $session->getUserId(),
            $session->getFlashcardCategory(),
            $count_to_generate,
        );

        $session_flashcards = new SessionFlashcards($session, array_map(function (Flashcard $flashcard) {
            return new SessionFlashcard($flashcard->getId(), null);
        }, $flashcards));

        $this->session_flashcard_repository->createMany($session_flashcards);
    }

    private function calculateCountToGenerate(SessionId $session_id, int $limit): int
    {
        $not_rated_flashcards_count = $this->session_flashcard_repository->getTotalSessionFlashcardsCount($session_id);

        if ($not_rated_flashcards_count < $limit) {
            return $limit - $not_rated_flashcards_count;
        }

        return 0;
    }
}
