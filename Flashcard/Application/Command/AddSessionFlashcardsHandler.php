<?php

declare(strict_types=1);

namespace Flashcard\Application\Command;

use Flashcard\Application\Services\IFlashcardSelector;
use Flashcard\Domain\Services\SessionFlashcardsService;
use Flashcard\Application\Repository\INextSessionFlashcardsRepository;

class AddSessionFlashcardsHandler
{
    public function __construct(
        private INextSessionFlashcardsRepository $next_session_flashcards_repository,
        private readonly IFlashcardSelector $selector,
        private readonly SessionFlashcardsService $service,
    ) {}

    public function handle(AddSessionFlashcards $command, int $display_limit = 1): void
    {
        $next_session_flashcards = $this->next_session_flashcards_repository->find($command->getSessionId());

        if ($next_session_flashcards->getUnratedCount() >= $display_limit) {
            return;
        }

        $flashcards = $this->selector->select($next_session_flashcards, $command->getLimit());

        $next_session_flashcards = $this->service->add($next_session_flashcards, $flashcards);

        $this->next_session_flashcards_repository->save($next_session_flashcards);
    }
}
