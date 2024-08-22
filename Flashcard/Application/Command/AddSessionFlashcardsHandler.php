<?php

declare(strict_types=1);

namespace Flashcard\Application\Command;

use Flashcard\Domain\Repositories\IFlashcardRepository;
use Flashcard\Domain\Repositories\ISessionRepository;

class AddSessionFlashcardsHandler
{
    public function __construct(
        private ISessionRepository $session_repository,
        private IFlashcardRepository $flashcard_repository,
    ) {}

    public function handle(AddSessionFlashcards $command): void
    {
        $not_rated_flashcards = $this->session_repository->getNotRatedFlashcards($command->getSessionId());

        $session = $this->session_repository->find($command->getSessionId());

        if (count($not_rated_flashcards) >= $command->getLimit()) {
            return;
        }

        $count_to_generate = $command->getLimit() - count($not_rated_flashcards);

        $flashcards = $this->flashcard_repository->getFlashcardsWithLowestRepetitionInterval(
            $session->getUserId(),
            $session->getFlashcardCategory()->getId(),
            $count_to_generate
        );

        $this->flashcard_repository->addFlashcardsToSession($session->getId(), $flashcards);
    }
}