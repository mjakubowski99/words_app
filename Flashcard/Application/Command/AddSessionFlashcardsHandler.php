<?php

declare(strict_types=1);

namespace Flashcard\Application\Command;

use Flashcard\Application\DTO\SessionFlashcardSummary;
use Flashcard\Application\Repository\INextSessionFlashcardsRepository;
use Flashcard\Application\Services\IFlashcardSelector;
use Flashcard\Domain\Models\Rating;
use Flashcard\Domain\Services\SessionFlashcardsService;
use Flashcard\Domain\ValueObjects\FlashcardId;
use Shared\Exercise\IFlashcardExerciseFacade;

class AddSessionFlashcardsHandler
{
    public function __construct(
        private INextSessionFlashcardsRepository $next_session_flashcards_repository,
        private readonly IFlashcardSelector $selector,
        private readonly SessionFlashcardsService $service,
        private IFlashcardExerciseFacade $facade,
    ) {}

    public function handle(AddSessionFlashcards $command, int $display_limit = 1): void
    {
        $next_session_flashcards = $this->next_session_flashcards_repository->find($command->getSessionId());

        if ($next_session_flashcards->getUnratedCount() >= $display_limit) {
            return;
        }

        $flashcards = $this->selector->select($next_session_flashcards, $command->getLimit());

        $exercise_type = $next_session_flashcards->resolveNextExerciseType();

        if ($next_session_flashcards->isMixedSessionType() && $flashcards[0]->getLastUserRating() && $flashcards[0]->getLastUserRating()->value < Rating::GOOD->value) {
            $exercise_type = null;
        }

        if ($exercise_type) {
            $required_flashcards_count_for_exercise = 1;

            $additional_flashcards = $this->selector->select(
                $next_session_flashcards,
                $required_flashcards_count_for_exercise - 1,
                [$flashcards[0]->getId()]
            );

            $next_session_flashcards = $this->service->add($next_session_flashcards, [$flashcards[0]]);

            foreach ($additional_flashcards as $additional_flashcard) {
                $next_session_flashcards->addNextAdditional($additional_flashcard);
            }

            $all_flashcards = array_merge([$flashcards[0]], $additional_flashcards);

            $exercise_entries = $this->facade->makeExercise(
                $this->buildFlashcardSummaryObjects($all_flashcards),
                $command->getUserId(),
                $exercise_type
            );

            foreach ($exercise_entries as $entry) {
                $next_session_flashcards->associateExercise(
                    new FlashcardId($entry->getFlashcardId()),
                    $entry->getExerciseEntryId()
                );
            }

            $this->next_session_flashcards_repository->save($next_session_flashcards);
        } else {
            $next_session_flashcards = $this->service->add($next_session_flashcards, $flashcards);
        }

        $this->next_session_flashcards_repository->save($next_session_flashcards);
    }

    private function buildFlashcardSummaryObjects(array $flashcards): array
    {
        $flashcard_summaries = [];

        foreach ($flashcards as $flashcard) {
            $flashcard_summaries[] = new SessionFlashcardSummary(
                $flashcard->getId()->getValue(),
                $flashcard->getFrontWord(),
                $flashcard->getBackWord(),
                $flashcard->getFrontContext(),
                $flashcard->getBackContext(),
                $flashcard->getFrontLang(),
                $flashcard->getBackLang(),
                $flashcard->getEmoji()->toUnicode(),
            );
        }

        return $flashcard_summaries;
    }
}
