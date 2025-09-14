<?php

declare(strict_types=1);

namespace Flashcard\Application\Command;

use Shared\Exercise\IFlashcardExerciseFacade;
use Flashcard\Application\Services\IFlashcardSelector;
use Flashcard\Domain\Services\SessionFlashcardsService;
use Flashcard\Application\Services\FlashcardSummaryFactory;
use Flashcard\Application\Repository\INextSessionFlashcardsRepository;

class AddSessionFlashcardsHandler
{
    public function __construct(
        private INextSessionFlashcardsRepository $next_session_flashcards_repository,
        private readonly IFlashcardSelector $selector,
        private readonly SessionFlashcardsService $service,
        private IFlashcardExerciseFacade $facade,
        private FlashcardSummaryFactory $flashcard_summary_factory,
    ) {}

    public function handle(AddSessionFlashcards $command, int $display_limit = 1): void
    {
        $next_session_flashcards = $this->next_session_flashcards_repository->find($command->getSessionId());

        if ($next_session_flashcards->getUnratedCount() >= $display_limit) {
            return;
        }

        $flashcards = $this->selector->select($next_session_flashcards, $command->getLimit(), $command->getFront(), $command->getBack());

        $exercise_type = $next_session_flashcards->resolveExerciseByRating($flashcards[0]->getLastUserRating());

        if ($exercise_type) {
            $summary_factory = $this->flashcard_summary_factory->make($exercise_type);

            $flashcard_summaries = $summary_factory->make($next_session_flashcards, $flashcards[0]);

            $next_session_flashcards->addFlashcardsFromSummaries($flashcard_summaries);

            $exercise_entries = $this->facade->buildExercise($flashcard_summaries, $command->getUserId(), $exercise_type);

            $next_session_flashcards->associateExerciseEntries($exercise_entries, $exercise_type);
        } else {
            $next_session_flashcards = $this->service->add($next_session_flashcards, $flashcards);
        }

        $this->next_session_flashcards_repository->save($next_session_flashcards);
    }
}
