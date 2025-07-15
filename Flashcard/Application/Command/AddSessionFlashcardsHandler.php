<?php

declare(strict_types=1);

namespace Flashcard\Application\Command;

use Flashcard\Application\Repository\INextSessionFlashcardsRepository;
use Flashcard\Application\Services\FlashcardSummaryFactory;
use Flashcard\Application\Services\IFlashcardSelector;
use Flashcard\Domain\Models\Flashcard;
use Flashcard\Domain\Models\NextSessionFlashcards;
use Flashcard\Domain\Models\Rating;
use Flashcard\Domain\Services\SessionFlashcardsService;
use Shared\Enum\ExerciseType;
use Shared\Exercise\IFlashcardExerciseFacade;

class AddSessionFlashcardsHandler
{
    public function __construct(
        private INextSessionFlashcardsRepository  $next_session_flashcards_repository,
        private readonly IFlashcardSelector       $selector,
        private readonly SessionFlashcardsService $service,
        private IFlashcardExerciseFacade          $facade,
        private FlashcardSummaryFactory           $flashcard_story_factory,
    ) {}

    public function handle(AddSessionFlashcards $command, int $display_limit = 1): void
    {
        $next_session_flashcards = $this->next_session_flashcards_repository->find($command->getSessionId());

        if ($next_session_flashcards->getUnratedCount() >= $display_limit) {
            return;
        }

        $flashcards = $this->selector->select($next_session_flashcards, $command->getLimit());

        $exercise_type = $this->resolveExerciseType($next_session_flashcards, $flashcards[0]);

        if ($exercise_type) {
            $flashcard_summaries = $this->flashcard_story_factory->make($next_session_flashcards, $exercise_type, $flashcards[0]);

            foreach ($flashcard_summaries->getSummaries() as $summary) {
                if (!$next_session_flashcards->canAddNext()) {
                    return;
                }

                if ($summary->getIsAdditional()) {
                    $next_session_flashcards->addNextAdditional($summary->getFlashcard());
                } else {
                    $next_session_flashcards->addNext($summary->getFlashcard());
                }
            }

            $exercise_entries = $this->facade->buildExercise($flashcard_summaries, $command->getUserId(), $exercise_type);

            $next_session_flashcards->associateExercises($exercise_entries, $exercise_type);
        } else {
            $next_session_flashcards = $this->service->add($next_session_flashcards, $flashcards);
        }

        $this->next_session_flashcards_repository->save($next_session_flashcards);
    }

    private function resolveExerciseType(NextSessionFlashcards $next_session_flashcards, Flashcard $base_flashcard): ?ExerciseType
    {
        $exercise_type = $next_session_flashcards->resolveNextExerciseType();

        if (
            $next_session_flashcards->isMixedSessionType()
            && $base_flashcard->getLastUserRating()
            && $base_flashcard->getLastUserRating()->value < Rating::GOOD->value
        ) {
            return null;
        }

        return $exercise_type;
    }
}
